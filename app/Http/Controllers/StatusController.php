<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationReceived;
use App\Http\Controllers\Traits\MessagingPrivacyTrait;
use App\Models\Message;
use App\Models\User;
use App\Notifications\AppNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StatusController extends Controller
{
    use MessagingPrivacyTrait;

    public function getStatuses(Request $request)
    {
        $user = Auth::user();
        $now = now();

        $query = DB::table('user_statuses as s')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->where('s.expires_at', '>', $now)
            ->where(function ($q) use ($user) {
                $q->where('s.user_id', $user->id)
                    ->orWhere('s.privacy_type', 'all');
            })
            ->select(
                's.id',
                's.user_id',
                'u.name as user_name',
                'u.avatar_url as user_avatar',
                's.type',
                's.content_url',
                's.audio_url',
                's.text_content',
                's.text_color',
                's.text_objects',
                's.text_pos_x',
                's.text_pos_y',
                's.text_rotate',
                's.text_bg_style',
                's.font_style',
                's.font_size',
                's.bg_color',
                's.filter_style',
                's.media_pos_x',
                's.media_pos_y',
                's.media_scale',
                's.media_rotate',
                's.duration_hours',
                's.expires_at',
                's.created_at',
                's.views_count'
            )
            ->orderByDesc('s.created_at')
            ->get();

        $userReactions = DB::table('status_reactions')
            ->whereIn('status_id', $query->pluck('id'))
            ->where('user_id', $user->id)
            ->pluck('emoji', 'status_id');

        $statuses = $query->map(function ($s) use ($user, $userReactions) {
            return [
                'id' => (int) $s->id,
                'userId' => (int) $s->user_id,
                'user_id' => (int) $s->user_id,
                'userName' => $s->user_name,
                'user_name' => $s->user_name,
                'userAvatar' => $s->user_avatar ? asset('storage/' . ltrim((string) $s->user_avatar, '/')) : null,
                'user_avatar' => $s->user_avatar ? asset('storage/' . ltrim((string) $s->user_avatar, '/')) : null,
                'type' => $s->type,
                'contentUrl' => $this->getSecureAttachmentUrl($s->content_url),
                'audioUrl' => $this->getSecureAttachmentUrl($s->audio_url),
                'textContent' => $s->text_content,
                'textColor' => $s->text_color ?: '#ffffff',
                'text_layers' => $s->text_objects,
                'textPosX' => (int) ($s->text_pos_x ?? 50),
                'textPosY' => (int) ($s->text_pos_y ?? 50),
                'textRotate' => (int) ($s->text_rotate ?? 0),
                'textBgStyle' => $s->text_bg_style ?: 'none',
                'fontStyle' => $s->font_style ?: 'Tajawal',
                'fontSize' => (int) ($s->font_size ?: 24),
                'bgColor' => $s->bg_color ?: 'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)',
                'filterStyle' => $s->filter_style,
                'mediaPosX' => isset($s->media_pos_x) ? (float) $s->media_pos_x : 50,
                'mediaPosY' => isset($s->media_pos_y) ? (float) $s->media_pos_y : 50,
                'mediaScale' => isset($s->media_scale) ? (float) $s->media_scale : 1,
                'mediaRotate' => isset($s->media_rotate) ? (float) $s->media_rotate : 0,
                'durationHours' => (int) ($s->duration_hours ?: 24),
                'expiresAt' => Carbon::parse($s->expires_at)->toISOString(),
                'createdAt' => Carbon::parse($s->created_at)->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                'viewsCount' => (int) ($s->views_count ?: 0),
                'mediaDurationSec' => isset($s->media_duration_sec) ? (float) $s->media_duration_sec : null,
                'is_mine' => (int) $s->user_id === (int) $user->id,
                'myReaction' => $userReactions->get((int) $s->id) ?: null,
            ];
        })->values();

        $myStatuses = $statuses->filter(fn ($s) => (int) $s['user_id'] === (int) $user->id)->values();

        $contactGroups = $statuses
            ->filter(fn ($s) => (int) $s['user_id'] !== (int) $user->id)
            ->groupBy('user_id')
            ->map(function ($items, $contactUserId) {
                $items = collect($items)->values();
                $first = $items->first();
                $statusIds = $items->pluck('id')->map(fn ($v) => (int) $v)->values();
                $viewedCount = DB::table('status_viewers')
                    ->whereIn('status_id', $statusIds)
                    ->where('viewer_id', Auth::id())
                    ->distinct('status_id')
                    ->count('status_id');
                $allViewed = $statusIds->count() > 0 && $viewedCount >= $statusIds->count();
                return [
                    'user_id' => (int) $contactUserId,
                    'user_name' => $first['user_name'] ?? 'User',
                    'user_avatar' => $first['user_avatar'] ?? null,
                    'all_viewed' => $allViewed,
                    'statuses' => $items->values()->all(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'my_statuses' => $myStatuses->all(),
                'contact_statuses' => $contactGroups->all(),
            ],
        ]);
    }

    public function createStatus(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'type' => ['required', 'in:text,image,video'],
            'text_content' => ['nullable', 'string', 'max:2000'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'font_style' => ['nullable', 'string', 'max:60'],
            'font_size' => ['nullable', 'integer', 'min:12', 'max:96'],
            'text_pos_x' => ['nullable', 'integer', 'min:0', 'max:100'],
            'text_pos_y' => ['nullable', 'integer', 'min:0', 'max:100'],
            'text_rotate' => ['nullable', 'integer', 'min:-360', 'max:360'],
            'text_bg_style' => ['nullable', 'string', 'in:none,translucent,solid'],
            'text_objects' => ['nullable', 'string', 'max:20000'],
            'bg_color' => ['nullable', 'string', 'max:255'],
            'filter_style' => ['nullable', 'string', 'max:40'],
            'duration_hours' => ['nullable', 'integer', 'in:24,72,168'],
            'media_duration_sec' => ['nullable', 'numeric', 'min:1', 'max:3600'],
            'media_pos_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'media_pos_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'media_scale' => ['nullable', 'numeric', 'min:0.1', 'max:10'],
            'media_rotate' => ['nullable', 'numeric', 'min:-360', 'max:360'],
            'privacy_type' => ['nullable', 'in:all,contacts,selected,except'],
            'media' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,webm,mkv', 'extensions:jpg,jpeg,png,webp,gif,mp4,mov,webm,mkv'],
            'audio' => ['nullable', 'file', 'max:5120', 'mimes:mp3,wav,m4a,ogg,webm', 'extensions:mp3,wav,m4a,ogg,webm'],
        ]);

        if (($data['type'] ?? 'text') !== 'text' && !$request->hasFile('media')) {
            return response()->json(['success' => false, 'message' => 'يرجى إرفاق وسائط للحالة.'], 422);
        }

        if (($data['type'] ?? 'text') === 'text' && empty(trim((string) ($data['text_content'] ?? '')))) {
            return response()->json(['success' => false, 'message' => 'لا يمكن نشر حالة نصية فارغة.'], 422);
        }

        $contentPath = null;
        $audioPath = null;
        if ($request->hasFile('media')) {
            $contentPath = $request->file('media')->store('statuses/media', 'public');
        }
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('statuses/audio', 'public');
        }

        $textObjectsJson = null;
        if (!empty($data['text_objects'])) {
            $decoded = json_decode($data['text_objects'], true);
            if (is_array($decoded)) {
                $textObjectsJson = json_encode($decoded);
            }
        }

        $duration = (int) ($data['duration_hours'] ?? 24);
        $insertData = [
            'user_id' => $user->id,
            'type' => $data['type'],
            'content_url' => $contentPath,
            'audio_url' => $audioPath,
            'text_content' => $data['text_content'] ?? null,
            'text_color' => $data['text_color'] ?? '#ffffff',
            'font_style' => $data['font_style'] ?? 'Tajawal',
            'font_size' => (int) ($data['font_size'] ?? 24),
            'text_pos_x' => (int) ($data['text_pos_x'] ?? 50),
            'text_pos_y' => (int) ($data['text_pos_y'] ?? 50),
            'text_rotate' => (int) ($data['text_rotate'] ?? 0),
            'text_bg_style' => $data['text_bg_style'] ?? 'none',
            'text_objects' => $textObjectsJson,
            'bg_color' => isset($data['bg_color']) ? substr($data['bg_color'], 0, 255) : 'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)',
            'filter_style' => $data['filter_style'] ?? null,
            'duration_hours' => $duration,
            'expires_at' => now()->addHours($duration),
            'privacy_type' => $data['privacy_type'] ?? 'all',
            'privacy_user_ids' => null,
            'views_count' => 0,
            'allow_reply' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_statuses', 'media_duration_sec')) {
            $insertData['media_duration_sec'] = isset($data['media_duration_sec']) ? (float) $data['media_duration_sec'] : null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_statuses', 'media_pos_x')) {
            $insertData['media_pos_x'] = isset($data['media_pos_x']) ? (float) $data['media_pos_x'] : null;
            $insertData['media_pos_y'] = isset($data['media_pos_y']) ? (float) $data['media_pos_y'] : null;
            $insertData['media_scale'] = isset($data['media_scale']) ? (float) $data['media_scale'] : 1;
            $insertData['media_rotate'] = isset($data['media_rotate']) ? (float) $data['media_rotate'] : 0;
        }

        $createdId = DB::table('user_statuses')->insertGetId($insertData);

        return response()->json(['success' => true, 'id' => $createdId, 'message' => 'تم نشر الحالة بنجاح']);
    }

    public function viewStatus(Request $request, $status)
    {
        $user = Auth::user();
        $statusRow = DB::table('user_statuses')->where('id', (int) $status)->first();
        if (!$statusRow) {
            return response()->json(['success' => false, 'message' => 'الحالة غير موجودة'], 404);
        }

        if ((int) $statusRow->user_id !== (int) $user->id) {
            $isNewViewer = !DB::table('status_viewers')
                ->where('status_id', (int) $status)
                ->where('viewer_id', $user->id)
                ->exists();

            DB::table('status_viewers')->updateOrInsert(
                ['status_id' => (int) $status, 'viewer_id' => $user->id],
                ['viewed_at' => now()]
            );

            if ($isNewViewer) {
                DB::table('user_statuses')->where('id', (int) $status)->increment('views_count');
            }
        }

        return response()->json(['success' => true]);
    }

    public function getStatusViewers(Request $request, $status)
    {
        $user = Auth::user();
        $statusRow = DB::table('user_statuses')->where('id', (int) $status)->first();
        if (!$statusRow) {
            return response()->json(['success' => false, 'message' => 'الحالة غير موجودة'], 404);
        }
        if ((int) $statusRow->user_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $durationHours = (int) ($statusRow->duration_hours ?? 24);
        $viewers = DB::table('status_viewers as sv')
            ->join('users as u', 'u.id', '=', 'sv.viewer_id')
            ->leftJoin('status_reactions as sr', function ($join) use ($status) {
                $join->on('sr.user_id', '=', 'sv.viewer_id')
                     ->where('sr.status_id', '=', (int) $status);
            })
            ->where('sv.status_id', (int) $status)
            ->where('sv.viewer_id', '!=', $user->id)
            ->orderByDesc('sv.viewed_at')
            ->select('u.id', 'u.name', 'u.avatar_url', 'sv.viewed_at', 'sr.emoji as reaction_emoji')
            ->get()
            ->map(function ($v) use ($durationHours) {
                $dt = Carbon::parse($v->viewed_at)->copy()->setTimezone('Asia/Riyadh');
                $now = now('Asia/Riyadh');
                $viewedAtText = $durationHours <= 24
                    ? $dt->format('g:i A')
                    : ($dt->year === $now->year ? $dt->format('M j') : $dt->format('M Y'));
                return [
                    'id' => (int) $v->id,
                    'name' => $v->name,
                    'avatarUrl' => $v->avatar_url ? asset('storage/' . ltrim((string) $v->avatar_url, '/')) : null,
                    'viewedAt' => $dt->format('Y-m-d H:i:s'),
                    'viewedAtText' => $viewedAtText,
                    'liked' => !empty($v->reaction_emoji),
                ];
            })
            ->values();

        return response()->json(['success' => true, 'data' => $viewers]);
    }

    public function deleteStatus(Request $request, $status)
    {
        $user = Auth::user();
        $row = DB::table('user_statuses')->where('id', (int) $status)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'الحالة غير موجودة'], 404);
        }
        if ((int) $row->user_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        if (!empty($row->content_url) && Storage::disk('public')->exists($row->content_url)) {
            Storage::disk('public')->delete($row->content_url);
        }
        if (!empty($row->audio_url) && Storage::disk('public')->exists($row->audio_url)) {
            Storage::disk('public')->delete($row->audio_url);
        }
        DB::table('status_viewers')->where('status_id', (int) $status)->delete();
        DB::table('user_statuses')->where('id', (int) $status)->delete();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $status)
    {
        $user = Auth::user();
        $row = DB::table('user_statuses')->where('id', (int) $status)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'الحالة غير موجودة'], 404);
        }
        if ((int) $row->user_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $data = $request->validate([
            'type' => ['required', 'in:text,image,video'],
            'text_content' => ['nullable', 'string', 'max:2000'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'font_style' => ['nullable', 'string', 'max:60'],
            'font_size' => ['nullable', 'integer', 'min:12', 'max:96'],
            'text_pos_x' => ['nullable', 'integer', 'min:0', 'max:100'],
            'text_pos_y' => ['nullable', 'integer', 'min:0', 'max:100'],
            'text_rotate' => ['nullable', 'integer', 'min:-360', 'max:360'],
            'text_bg_style' => ['nullable', 'string', 'in:none,translucent,solid'],
            'text_objects' => ['nullable', 'string', 'max:20000'],
            'bg_color' => ['nullable', 'string', 'max:255'],
            'filter_style' => ['nullable', 'string', 'max:40'],
            'duration_hours' => ['nullable', 'integer', 'in:24,72,168'],
            'media_duration_sec' => ['nullable', 'numeric', 'min:1', 'max:3600'],
            'media_pos_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'media_pos_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'media_scale' => ['nullable', 'numeric', 'min:0.1', 'max:10'],
            'media_rotate' => ['nullable', 'numeric', 'min:-360', 'max:360'],
            'privacy_type' => ['nullable', 'in:all,contacts,selected,except'],
            'media' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,webm,mkv', 'extensions:jpg,jpeg,png,webp,gif,mp4,mov,webm,mkv'],
            'audio' => ['nullable', 'file', 'max:5120', 'mimes:mp3,wav,m4a,ogg,webm', 'extensions:mp3,wav,m4a,ogg,webm'],
        ]);

        if (($data['type'] ?? 'text') === 'text' && empty(trim((string) ($data['text_content'] ?? '')))) {
            return response()->json(['success' => false, 'message' => 'لا يمكن نشر حالة نصية فارغة.'], 422);
        }

        $contentPath = $row->content_url;
        $audioPath = $row->audio_url;
        if ($request->hasFile('media')) {
            if (!empty($contentPath) && Storage::disk('public')->exists($contentPath)) {
                Storage::disk('public')->delete($contentPath);
            }
            $contentPath = $request->file('media')->store('statuses/media', 'public');
        } elseif (($data['type'] ?? 'text') === 'text') {
            if (!empty($contentPath) && Storage::disk('public')->exists($contentPath)) {
                Storage::disk('public')->delete($contentPath);
            }
            $contentPath = null;
        } elseif (empty($contentPath)) {
            return response()->json(['success' => false, 'message' => 'يرجى إرفاق وسائط للحالة.'], 422);
        }

        if ($request->hasFile('audio')) {
            if (!empty($audioPath) && Storage::disk('public')->exists($audioPath)) {
                Storage::disk('public')->delete($audioPath);
            }
            $audioPath = $request->file('audio')->store('statuses/audio', 'public');
        }

        $textObjectsJsonUpd = null;
        if (!empty($data['text_objects'])) {
            $decoded = json_decode($data['text_objects'], true);
            if (is_array($decoded)) {
                $textObjectsJsonUpd = json_encode($decoded);
            }
        }

        $duration = (int) ($data['duration_hours'] ?? ($row->duration_hours ?? 24));
        $updateData = [
            'type' => $data['type'],
            'content_url' => $contentPath,
            'audio_url' => $audioPath,
            'text_content' => $data['text_content'] ?? null,
            'text_color' => $data['text_color'] ?? '#ffffff',
            'font_style' => $data['font_style'] ?? 'Tajawal',
            'font_size' => (int) ($data['font_size'] ?? 24),
            'text_pos_x' => (int) ($data['text_pos_x'] ?? 50),
            'text_pos_y' => (int) ($data['text_pos_y'] ?? 50),
            'text_rotate' => (int) ($data['text_rotate'] ?? 0),
            'text_bg_style' => $data['text_bg_style'] ?? 'none',
            'text_objects' => $textObjectsJsonUpd,
            'bg_color' => $data['bg_color'] ?? 'var(--panel-2)',
            'filter_style' => $data['filter_style'] ?? null,
            'duration_hours' => $duration,
            'expires_at' => now()->addHours($duration),
            'privacy_type' => $data['privacy_type'] ?? 'all',
            'updated_at' => now(),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_statuses', 'media_duration_sec')) {
            $updateData['media_duration_sec'] = isset($data['media_duration_sec']) ? (float) $data['media_duration_sec'] : null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_statuses', 'media_pos_x')) {
            $updateData['media_pos_x'] = isset($data['media_pos_x']) ? (float) $data['media_pos_x'] : null;
            $updateData['media_pos_y'] = isset($data['media_pos_y']) ? (float) $data['media_pos_y'] : null;
            $updateData['media_scale'] = isset($data['media_scale']) ? (float) $data['media_scale'] : 1;
            $updateData['media_rotate'] = isset($data['media_rotate']) ? (float) $data['media_rotate'] : 0;
        }

        DB::table('user_statuses')->where('id', (int) $status)->update($updateData);

        return response()->json(['success' => true, 'id' => (int) $status, 'message' => 'تم تحديث الحالة بنجاح']);
    }

    public function replyToStatus(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'status_id' => ['required', 'integer', 'exists:user_statuses,id'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $statusRow = DB::table('user_statuses')->where('id', (int) $data['status_id'])->first();
        $owner = User::findOrFail((int) $statusRow->user_id);
        if ((int) $owner->id === (int) $user->id || !$this->canMessage($user, $owner)) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $content = $this->sanitizeContent($data['content']);
        if (!$content) {
            return response()->json(['success' => false, 'message' => 'الرد فارغ'], 422);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $owner->id,
            'content' => '[[status:' . (int) $statusRow->id . ']] ' . $content,
        ]);

        DB::table('status_replies')->insert([
            'status_id' => (int) $statusRow->id,
            'user_id' => $user->id,
            'message_id' => $message->id,
            'content' => $content,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($owner->notify_message ?? true) {
            $messagingRoute = $owner->role === 'teacher' ? route('teacher.messaging') : route('student.messaging');
            $owner->notify(new AppNotification(
                'رد على حالتك',
                "{$user->name} رد على حالتك: {$content}",
                $messagingRoute,
                'message',
                'ri-chat-quote-line'
            ));
            try { event(new NewNotificationReceived($owner->id, 'message')); } catch (\Throwable) {}
        }

        return response()->json(['success' => true, 'data' => $this->messagePayload($message->fresh(['sender']), $user->id)]);
    }

    public function reactToStatus(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'status_id' => ['required', 'integer', 'exists:user_statuses,id'],
            'emoji' => ['required', 'string', 'max:16'],
        ]);

        $statusRow = DB::table('user_statuses')->where('id', (int) $data['status_id'])->first();
        $owner = User::findOrFail((int) $statusRow->user_id);
        if ((int) $owner->id === (int) $user->id || !$this->canMessage($user, $owner)) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $emoji = trim((string) $data['emoji']);
        $existing = DB::table('status_reactions')
            ->where('status_id', (int) $statusRow->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->emoji === $emoji) {
            DB::table('status_reactions')
                ->where('status_id', (int) $statusRow->id)
                ->where('user_id', $user->id)
                ->delete();
            $liked = false;
        } else {
            DB::table('status_reactions')->updateOrInsert(
                ['status_id' => (int) $statusRow->id, 'user_id' => $user->id],
                ['emoji' => $emoji, 'updated_at' => now(), 'created_at' => now()]
            );
            $liked = true;
            if ($owner->notify_message ?? true) {
                $messagingRoute = $owner->role === 'teacher' ? route('teacher.messaging') : route('student.messaging');
                $owner->notify(new AppNotification(
                    'تفاعل على حالتك',
                    "{$user->name} تفاعل مع حالتك بـ {$emoji}",
                    $messagingRoute,
                    'message',
                    'ri-heart-line'
                ));
                try { event(new NewNotificationReceived($owner->id, 'message')); } catch (\Throwable) {}
            }
        }

        return response()->json(['success' => true, 'data' => ['status_id' => (int) $statusRow->id, 'emoji' => $emoji, 'liked' => $liked]]);
    }

    protected function getSecureAttachmentUrl(?string $attachmentPath): ?string
    {
        if (!$attachmentPath) {
            return null;
        }

        if (preg_match('#^https?://#i', trim($attachmentPath))) {
            return trim($attachmentPath);
        }

        $path = str_replace('\\', '/', trim($attachmentPath));
        $path = ltrim($path, '/');
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'messaging/audio/')) {
            $path = 'message_audio/' . basename($path);
        } elseif (str_starts_with($path, 'messaging/attachments/')) {
            $path = 'message_attachments/' . basename($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        if (!str_contains($path, '/')) {
            foreach (['message_audio', 'message_attachments', 'statuses/media', 'statuses/audio'] as $dir) {
                $candidate = $dir . '/' . $path;
                if (Storage::disk('public')->exists($candidate)) {
                    return asset('storage/' . $candidate);
                }
            }
        }

        if (Storage::disk('local')->exists('messaging/attachments/' . basename($path))) {
            return route('storage.message-attachment', ['path' => 'message_attachments/' . basename($path)]);
        }
        if (Storage::disk('local')->exists('messaging/audio/' . basename($path))) {
            return route('storage.message-audio', ['path' => 'message_audio/' . basename($path)]);
        }

        return asset('storage/' . $path);
    }

    protected function messagePayload(Message $message, int $viewerId, array $reactions = [], array $pinned = []): array
    {
        $message->loadMissing('sender', 'replyTo.sender');

        $reply = null;
        if ($message->reply_to && $message->replyTo) {
            $reply = [
                'id' => $message->replyTo->id,
                'content' => $message->replyTo->content,
                'senderName' => $message->replyTo->sender?->name,
                'attachmentName' => $message->replyTo->attachment_name,
            ];
        }

        return [
            'id' => $message->id,
            'senderId' => $message->sender_id,
            'recipientId' => $message->recipient_id,
            'senderName' => $message->sender?->name,
            'senderAvatar' => $message->sender?->avatar_url ? asset('storage/' . ltrim($message->sender->avatar_url, '/')) : null,
            'content' => $message->content,
            'attachmentUrl' => $this->getSecureAttachmentUrl($message->attachment_path),
            'attachmentName' => $message->attachment_name,
            'attachmentMime' => $message->attachment_type,
            'attachmentKind' => $message->attachment_kind,
            'isEdited' => (bool) $message->is_edited,
            'createdAt' => $message->created_at?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
            'readAt' => $message->read_at,
            'replyTo' => $reply,
            'reactions' => $reactions[$message->id] ?? [],
            'isPinned' => (bool) ($pinned[$message->id] ?? false),
            'forwardedFromMessageId' => $message->forwarded_from_message_id,
            'audioPosition' => (float) ($message->audio_position ?? 0),
            'isSensitive' => (bool) $message->is_sensitive,
        ];
    }
}
