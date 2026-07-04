<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\MessagingPrivacyTrait;
use App\Mail\SendOtpCode;
use App\Models\BlockedContact;
use App\Models\EmailOtp;
use App\Models\MessageFolder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MessagingSettingsController extends Controller
{
    use MessagingPrivacyTrait;

    const USERNAME_COOLDOWN_DAYS = 30;

    public function getMessagingSettings(Request $request)
    {
        $settings = DB::table('user_messaging_settings')->where('user_id', Auth::id())->first();
        $payload = $this->settingsPayload($settings);
        $payload['account'] = $this->accountPayload(Auth::user());

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }

    public function saveMessagingSettings(Request $request)
    {
        $data = $request->validate([
            'privacy' => ['nullable', 'array'],
            'notifications' => ['nullable', 'array'],
            'media' => ['nullable', 'array'],
            'security' => ['nullable', 'array'],
            'chats' => ['nullable', 'array'],
        ]);

        $security = $data['security'] ?? null;
        if (is_array($security)) {
            unset($security['pin'], $security['pinConfirm']);
            $data['security'] = $security;
        }

        DB::table('user_messaging_settings')->updateOrInsert(
            ['user_id' => Auth::id()],
            [
                'privacy' => json_encode($data['privacy'] ?? [], JSON_UNESCAPED_UNICODE),
                'notifications' => json_encode($data['notifications'] ?? [], JSON_UNESCAPED_UNICODE),
                'media' => json_encode($data['media'] ?? [], JSON_UNESCAPED_UNICODE),
                'security' => json_encode($data['security'] ?? [], JSON_UNESCAPED_UNICODE),
                'chats' => json_encode($data['chats'] ?? [], JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $settings = DB::table('user_messaging_settings')->where('user_id', Auth::id())->first();
        return response()->json(['success' => true, 'data' => $this->settingsPayload($settings)]);
    }

    public function updateAccountInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'username' => ['nullable', 'string', 'min:3', 'max:32', 'regex:/^[A-Za-z0-9_]+$/', 'unique:users,username,' . $user->id],
            'phone' => ['nullable', 'string', 'min:6', 'max:20', 'unique:users,phone,' . $user->id],
            'bio' => ['nullable', 'string', 'max:120'],
            'birthday' => ['nullable', 'date', 'before:today'],
        ]);

        $newUsername = $data['username'] ?? null;
        if ($newUsername && $newUsername !== $user->username) {
            if ($user->username && $user->username_changed_at) {
                $nextAllowed = $user->username_changed_at->copy()->addDays(self::USERNAME_COOLDOWN_DAYS);
                if (now()->lt($nextAllowed)) {
                    $daysLeft = (int) ceil(now()->diffInRealSeconds($nextAllowed) / 86400);
                    return response()->json([
                        'success' => false,
                        'message' => "يمكنك تغيير اسم المستخدم بعد {$daysLeft} يوماً من آخر تغيير.",
                    ], 422);
                }
            }
            $data['username_changed_at'] = now();
        }

        $user->update($data);

        return response()->json(['success' => true, 'data' => $this->accountPayload($user->fresh())]);
    }

    public function checkUsernameAvailability(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:32'],
        ]);

        $username = $data['username'];
        $user = Auth::user();

        if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
            return response()->json(['success' => true, 'available' => false, 'reason' => 'يجب أن يكون 3 أحرف على الأقل، وحروف/أرقام/شرطة سفلية فقط.']);
        }

        $taken = User::where('username', $username)->where('id', '!=', $user->id)->exists();

        return response()->json(['success' => true, 'available' => !$taken, 'reason' => $taken ? 'هذا الاسم مستخدم بالفعل' : null]);
    }

    public function checkPhoneAvailability(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = $data['phone'];
        $user = Auth::user();

        if (mb_strlen($phone) < 6) {
            return response()->json(['success' => true, 'available' => false, 'reason' => 'رقم قصير جداً']);
        }

        $taken = User::where('phone', $phone)->where('id', '!=', $user->id)->exists();

        return response()->json(['success' => true, 'available' => !$taken, 'reason' => $taken ? 'هذا الرقم مستخدم من قبل مستخدم آخر' : null]);
    }

    public function listBlockedUsers(Request $request)
    {
        $blocked = BlockedContact::where('blocker_id', Auth::id())
            ->with('blocked:id,name,username,avatar_url,role')
            ->latest()
            ->get()
            ->map(fn ($row) => [
                'id' => $row->blocked->id,
                'name' => $row->blocked->name,
                'username' => $row->blocked->username,
                'avatar_url' => $row->blocked->avatar_url,
                'role' => $row->blocked->role,
            ]);

        return response()->json(['success' => true, 'data' => $blocked]);
    }

    public function blockUser(Request $request)
    {
        $data = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        if ((int) $data['user_id'] === (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'لا يمكنك حظر نفسك'], 422);
        }

        BlockedContact::firstOrCreate([
            'blocker_id' => Auth::id(),
            'blocked_id' => $data['user_id'],
        ]);

        return response()->json(['success' => true]);
    }

    public function unblockUser(Request $request, $userId)
    {
        BlockedContact::where('blocker_id', Auth::id())->where('blocked_id', $userId)->delete();

        return response()->json(['success' => true]);
    }

    public function listActiveSessions(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($row) use ($request) {
                return [
                    'id' => $row->id,
                    'is_current' => $row->id === $request->session()->getId(),
                    'ip_address' => $row->ip_address,
                    'user_agent' => $row->user_agent,
                    'last_activity' => Carbon::createFromTimestamp($row->last_activity)->toIso8601String(),
                ];
            });

        return response()->json(['success' => true, 'data' => $sessions]);
    }

    public function terminateSession(Request $request, $sessionId)
    {
        if ($sessionId === $request->session()->getId()) {
            return response()->json(['success' => false, 'message' => 'لا يمكن إنهاء الجلسة الحالية من هنا'], 422);
        }

        DB::table('sessions')->where('user_id', Auth::id())->where('id', $sessionId)->delete();

        return response()->json(['success' => true]);
    }

    public function request2FACode(Request $request)
    {
        $user = Auth::user();

        $otp = strtoupper(Str::random(6));

        EmailOtp::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp, 'attempts' => 0, 'expires_at' => now()->addMinutes(10), 'last_sent_at' => now()]
        );

        Mail::to($user->email)->send(new SendOtpCode($user->name, $otp, 10));

        return response()->json(['success' => true, 'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني']);
    }

    public function confirm2FA(Request $request)
    {
        $data = $request->validate(['otp' => ['required', 'string']]);
        $user = Auth::user();

        $record = EmailOtp::where('email', $user->email)->latest()->first();

        if (!$record || $record->isExpired() || !hash_equals($record->otp, strtoupper($data['otp']))) {
            return response()->json(['success' => false, 'message' => 'رمز التحقق غير صحيح أو منتهي'], 422);
        }

        $settings = DB::table('user_messaging_settings')->where('user_id', $user->id)->first();
        $payload = $this->settingsPayload($settings);
        $payload['security']['twoFaEnabled'] = true;

        DB::table('user_messaging_settings')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'security' => json_encode($payload['security'], JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $record->delete();

        return response()->json(['success' => true, 'data' => $payload['security']]);
    }

    public function disable2FA(Request $request)
    {
        $data = $request->validate(['password' => ['required', 'string']]);

        if (!Hash::check($data['password'], Auth::user()->password)) {
            return response()->json(['success' => false, 'message' => 'كلمة المرور غير صحيحة'], 422);
        }

        $settings = DB::table('user_messaging_settings')->where('user_id', Auth::id())->first();
        $payload = $this->settingsPayload($settings);
        $payload['security']['twoFaEnabled'] = false;

        DB::table('user_messaging_settings')->updateOrInsert(
            ['user_id' => Auth::id()],
            [
                'security' => json_encode($payload['security'], JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'data' => $payload['security']]);
    }

    public function listFolders(Request $request)
    {
        $folders = MessageFolder::where('user_id', Auth::id())->orderBy('position')->get();

        return response()->json(['success' => true, 'data' => $folders]);
    }

    public function saveFolder(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'max:60'],
            'include_ids' => ['nullable', 'array'],
            'exclude_ids' => ['nullable', 'array'],
        ]);

        $folder = MessageFolder::where('user_id', Auth::id())->where('id', $data['id'] ?? null)->first();

        if (!$folder) {
            $folder = new MessageFolder(['user_id' => Auth::id()]);
            $folder->position = MessageFolder::where('user_id', Auth::id())->count();
        }

        $folder->name = $data['name'];
        $folder->icon = $data['icon'] ?? 'ri-folder-3-line';
        $folder->include_ids = $data['include_ids'] ?? [];
        $folder->exclude_ids = $data['exclude_ids'] ?? [];
        $folder->save();

        return response()->json(['success' => true, 'data' => $folder]);
    }

    public function deleteFolder(Request $request, $folderId)
    {
        MessageFolder::where('user_id', Auth::id())->where('id', $folderId)->delete();

        return response()->json(['success' => true]);
    }

    protected function accountPayload(User $user): array
    {
        return [
            'name' => $user->name,
            'username' => $user->username,
            'username_changed_at' => $user->username_changed_at?->toIso8601String(),
            'bio' => $user->bio,
            'phone' => $user->phone,
            'birthday' => $user->birthday?->format('Y-m-d'),
            'avatar_url' => $user->avatar_url,
            'locale' => $user->locale ?? 'ar',
            'member_since' => $user->created_at?->format('Y'),
            'email_masked' => $this->maskEmail($user->email),
        ];
    }

    protected function maskEmail(?string $email): ?string
    {
        if (!$email || !str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = mb_substr($local, 0, 2);
        $masked = $visible . str_repeat('*', max(1, mb_strlen($local) - 2));

        return $masked . '@' . $domain;
    }
}
