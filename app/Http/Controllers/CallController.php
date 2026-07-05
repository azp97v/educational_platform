<?php

namespace App\Http\Controllers;

use App\Events\CallAnswered;
use App\Events\CallEnded;
use App\Events\CallInitiated;
use App\Events\CallRejected;
use App\Events\CallRinging;
use App\Events\GroupParticipantJoined;
use App\Events\GroupParticipantLeft;
use App\Events\IceCandidateSent;
use App\Events\PeerAnswerSent;
use App\Events\PeerOfferSent;
use App\Http\Controllers\Traits\MessagingPrivacyTrait;
use App\Models\Call;
use App\Models\CallParticipant;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    use MessagingPrivacyTrait;

    const MAX_GROUP_PARTICIPANTS = 5;

    /**
     * إنشاء مكالمة: فردية (recipient_id واحد) أو جماعية (participant_ids عدة).
     * لا يُبَث عرض (offer) هنا — العرض يُرسل لكل مدعو عبر offer() بعد إنشاء المكالمة.
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => ['nullable', 'integer', 'exists:users,id'],
            'participant_ids' => ['nullable', 'array', 'min:1', 'max:' . self::MAX_GROUP_PARTICIPANTS],
            'participant_ids.*' => ['integer', 'exists:users,id'],
            'type' => ['required', 'in:voice,video'],
        ]);

        $participantIds = $data['participant_ids'] ?? ($data['recipient_id'] ? [$data['recipient_id']] : []);
        $participantIds = array_values(array_unique($participantIds));

        abort_if(empty($participantIds), 422, 'يجب تحديد شخص واحد على الأقل للاتصال به');

        /** @var User $caller */
        $caller = Auth::user();
        abort_if(in_array((int) $caller->id, array_map('intval', $participantIds), true), 422, 'لا يمكن الاتصال بنفسك');

        $isGroup = count($participantIds) > 1;
        $recipients = User::whereIn('id', $participantIds)->get();
        foreach ($recipients as $recipient) {
            abort_unless($this->userCanMessage($caller, $recipient), 403, 'لا يمكنك الاتصال بـ ' . $recipient->name);

            // Reject if recipient is already in an active call
            $busy = CallParticipant::where('user_id', $recipient->id)
                ->whereIn('status', ['joined', 'ringing'])
                ->whereHas('call', fn ($q) => $q->whereIn('status', ['ringing', 'accepted']))
                ->exists();
            if ($busy) {
                return response()->json(['success' => false, 'busy' => true, 'error' => $recipient->name . ' في مكالمة أخرى حالياً'], 200);
            }
        }

        $call = Call::create([
            'caller_id' => $caller->id,
            'recipient_id' => $isGroup ? null : $participantIds[0],
            'type' => $data['type'],
            'status' => 'ringing',
            'is_group' => $isGroup,
            'started_at' => now(),
        ]);

        // المتصل نفسه يُعتبر "منضمّاً" منذ البداية.
        CallParticipant::create(['call_id' => $call->id, 'user_id' => $caller->id, 'status' => 'joined', 'joined_at' => now()]);
        foreach ($participantIds as $id) {
            CallParticipant::create(['call_id' => $call->id, 'user_id' => $id, 'status' => 'ringing']);
        }

        return response()->json(['success' => true, 'call_id' => $call->id, 'is_group' => $isGroup]);
    }

    /**
     * إضافة شخص جديد إلى مكالمة جارية فعلاً (ترقية لمكالمة جماعية إن كانت فردية).
     */
    public function invite(Request $request, Call $call)
    {
        $this->authorizeParticipant($call);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $newUserId = (int) $data['user_id'];
        $alreadyParticipant = $call->participants()->where('user_id', $newUserId)->exists();
        abort_if($alreadyParticipant, 422, 'هذا الشخص مضاف بالفعل لهذه المكالمة');

        $currentCount = $call->participants()->whereIn('status', ['joined', 'ringing'])->count();
        abort_if($currentCount >= self::MAX_GROUP_PARTICIPANTS, 422, 'تم الوصول للحد الأقصى لعدد المشاركين');

        $newUser = User::findOrFail($newUserId);
        abort_unless($this->userCanMessage(Auth::user(), $newUser), 403, 'لا يمكنك إضافة هذا المستخدم');

        if (!$call->is_group) {
            $call->update(['is_group' => true]);
        }

        CallParticipant::create(['call_id' => $call->id, 'user_id' => $newUserId, 'status' => 'ringing']);

        return response()->json(['success' => true]);
    }

    /**
     * إرسال عرض WebRTC (offer) لمستخدم محدد — يُستخدم عند بدء/دعوة طرف لأول مرة (تظهر له شاشة "مكالمة واردة").
     */
    public function offer(Request $request, Call $call)
    {
        $this->authorizeParticipant($call);

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'offer' => ['required', 'array'],
        ]);

        broadcast(new CallInitiated($call, $data['offer'], (int) $data['to_user_id']));

        return response()->json(['success' => true]);
    }

    /**
     * عرض WebRTC بين مشاركين موجودين فعلاً (يُستخدم عند انضمام مشارك جديد لمكالمة جماعية جارية).
     */
    public function peerOffer(Request $request, Call $call)
    {
        $userId = $this->authorizeParticipant($call);

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'offer' => ['required', 'array'],
        ]);

        broadcast(new PeerOfferSent($call, $userId, (int) $data['to_user_id'], $data['offer']));

        return response()->json(['success' => true]);
    }

    public function peerAnswer(Request $request, Call $call)
    {
        $userId = $this->authorizeParticipant($call);

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'answer' => ['required', 'array'],
        ]);

        broadcast(new PeerAnswerSent($call, $userId, (int) $data['to_user_id'], $data['answer']));

        return response()->json(['success' => true]);
    }

    public function answer(Request $request, Call $call)
    {
        $userId = $this->authorizeInvited($call);

        $data = $request->validate(['answer' => ['required', 'array']]);

        $participant = $call->participants()->where('user_id', $userId)->first();
        $participant?->update(['status' => 'joined', 'joined_at' => now()]);

        if (!$call->is_group) {
            $call->update(['status' => 'accepted', 'answered_at' => now()]);
        } elseif (!$call->answered_at) {
            $call->update(['answered_at' => now()]);
        }

        broadcast(new CallAnswered($call, $data['answer'], $userId, $call->caller_id));

        return response()->json(['success' => true]);
    }

    /**
     * انضمام فعلي لمكالمة جماعية جارية: يُعلم كل المشاركين الحاليين حتى ينشئ كل واحد
     * منهم اتصالاً مستقلاً (mesh) موجّهاً للمنضم الجديد.
     */
    public function join(Call $call)
    {
        $userId = $this->authorizeInvited($call);

        $existingIds = $call->participants()
            ->where('status', 'joined')
            ->where('user_id', '!=', $userId)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $participant = $call->participants()->where('user_id', $userId)->first();
        $participant?->update(['status' => 'joined', 'joined_at' => now()]);

        if (!empty($existingIds)) {
            broadcast(new GroupParticipantJoined($call, Auth::user(), $existingIds));
        }

        return response()->json(['success' => true, 'existing_participant_ids' => $existingIds]);
    }

    /**
     * يستدعيها جهاز المستلم فوراً عند استقبال إشعار المكالمة فعلياً (أي أنه متصل بالموقع ووصلته المكالمة)،
     * فيتحول نص حالة المتصل من "جاري الاتصال" إلى "يرن" بدلاً من افتراض ذلك دون تأكيد فعلي.
     */
    public function ring(Call $call)
    {
        $userId = $this->authorizeInvited($call);

        broadcast(new CallRinging($call, $userId));

        return response()->json(['success' => true]);
    }

    public function reject(Call $call)
    {
        $userId = $this->authorizeInvited($call);

        $participant = $call->participants()->where('user_id', $userId)->first();
        $participant?->update(['status' => 'rejected', 'left_at' => now()]);

        if (!$call->is_group) {
            $call->update(['status' => 'rejected', 'ended_at' => now()]);
        }

        broadcast(new CallRejected($call));

        return response()->json(['success' => true]);
    }

    /**
     * مغادرة المكالمة: للمكالمات الفردية تنهي المكالمة بالكامل، وللجماعية تُخرج
     * هذا المشارك فقط بينما يستمر الباقون.
     */
    public function end(Call $call)
    {
        $userId = $this->authorizeParticipant($call);

        $participant = $call->participants()->where('user_id', $userId)->first();

        if ($call->is_group) {
            $wasRinging = $participant && $participant->status === 'ringing';
            $participant?->update(['status' => $wasRinging ? 'missed' : 'left', 'left_at' => now()]);

            $remainingIds = $call->participants()
                ->where('status', 'joined')
                ->where('user_id', '!=', $userId)
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            if (!empty($remainingIds)) {
                broadcast(new GroupParticipantLeft($call, $userId, $remainingIds));
            } else {
                $duration = $call->answered_at ? (int) abs(now()->diffInSeconds($call->answered_at)) : 0;
                $call->update(['status' => 'ended', 'ended_at' => now(), 'duration_seconds' => $duration]);
                broadcast(new CallEnded($call));
            }

            return response()->json(['success' => true, 'status' => $participant?->status]);
        }

        if ($call->status === 'ringing') {
            $call->update(['status' => 'missed', 'ended_at' => now()]);
            $this->notifyMissedCall($call);
        } elseif ($call->status === 'accepted') {
            $duration = $call->answered_at ? (int) abs(now()->diffInSeconds($call->answered_at)) : 0;
            $call->update(['status' => 'ended', 'ended_at' => now(), 'duration_seconds' => $duration]);
        }

        broadcast(new CallEnded($call));

        return response()->json(['success' => true, 'status' => $call->status, 'duration_seconds' => $call->duration_seconds]);
    }

    public function iceCandidate(Request $request, Call $call)
    {
        $userId = $this->authorizeParticipant($call);

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'candidate' => ['required', 'array'],
        ]);

        broadcast(new IceCandidateSent($call, $userId, (int) $data['to_user_id'], $data['candidate']));

        return response()->json(['success' => true]);
    }

    /**
     * يسمح فقط لمن لديه سجل مشارك فعلي (منضمّ أو مدعو) في هذه المكالمة.
     */
    private function authorizeParticipant(Call $call): int
    {
        $userId = (int) Auth::id();
        $isParticipant = $call->participants()->where('user_id', $userId)->exists();
        abort_unless($isParticipant, 403);

        return $userId;
    }

    /**
     * يسمح فقط لمن هو مدعو فعلاً (له سجل CallParticipant)، يُستخدم لعمليات الرد/الرفض/الانضمام.
     */
    private function authorizeInvited(Call $call): int
    {
        return $this->authorizeParticipant($call);
    }

    private function notifyMissedCall(Call $call): void
    {
        $recipient = $call->recipient;
        if (!$recipient || !($recipient->notify_call ?? true)) {
            return;
        }

        $caller = $call->caller;
        $recipient->notify(new AppNotification(
            'مكالمة فائتة',
            "لديك مكالمة " . ($call->type === 'video' ? 'مرئية' : 'صوتية') . " فائتة من {$caller->name}.",
            $recipient->role === 'teacher' ? route('teacher.messaging') : route('student.messaging'),
            'call',
            'ri-phone-line'
        ));
    }
}
