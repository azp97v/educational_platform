<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * عرض صفحة تحرير الملف الشخصي
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_]+$/', 'unique:users,username,' . $user->id],
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // فحص cooldown تغيير اسم المستخدم
        $newUsername = $validated['username'] ?? null;
        if ($newUsername && $newUsername !== $user->username) {
            if ($user->username && $user->username_changed_at) {
                $nextAllowed = $user->username_changed_at->copy()->addDays(30);
                if (now()->lt($nextAllowed)) {
                    $daysLeft = (int) ceil(now()->diffInRealSeconds($nextAllowed) / 86400);
                    return back()->withErrors(['username' => "يمكنك تغيير اسم المستخدم بعد {$daysLeft} يوماً من آخر تغيير."]);
                }
            }
            $validated['username_changed_at'] = now();
        }

        // معالجة الصورة الشخصية
        if ($request->hasFile('avatar_url')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->avatar_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_url);
            }

            $avatar = $request->file('avatar_url')->store('avatars', 'public');
            $validated['avatar_url'] = $avatar;
        } elseif ($request->has('remove_avatar') && $user->avatar_url) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_url);
            }
            $validated['avatar_url'] = null;
        }

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function checkUsername(Request $request)
    {
        $username = trim($request->query('username', ''));
        $user = Auth::user();

        if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
            return response()->json(['available' => false, 'reason' => 'يجب أن يكون 3 أحرف على الأقل، وحروف/أرقام/شرطة سفلية فقط']);
        }

        if ($username !== $user->username && $user->username && $user->username_changed_at) {
            $nextAllowed = $user->username_changed_at->copy()->addDays(30);
            if (now()->lt($nextAllowed)) {
                $daysLeft = (int) ceil(now()->diffInRealSeconds($nextAllowed) / 86400);
                return response()->json(['available' => false, 'reason' => "لا يمكن التغيير الآن — متبقٍ {$daysLeft} يوم"]);
            }
        }

        $exists = User::where('username', $username)->where('id', '!=', $user->id)->exists();
        return response()->json(['available' => !$exists, 'reason' => $exists ? 'هذا الاسم مستخدم بالفعل' : null]);
    }

    /**
     * عرض صفحة تغيير كلمة المرور
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم على الأقل',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين',
        ]);

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('profile.show')->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * عرض صفحة حذف الحساب
     */
    public function showDeleteAccount()
    {
        return view('profile.delete-account');
    }

    /**
     * حذف الحساب
     */
    public function deleteAccount(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required',
            'confirmation' => 'required|in:تأكيد حذف حسابي,تاكيد حذف حسابي'
        ]);

        // التحقق من كلمة المرور
        if (!Hash::check($validated['password'], Auth::user()->password)) {
            return back()->withErrors(['password' => 'كلمة المرور غير صحيحة']);
        }

        $user = Auth::user();

        // حذف الصورة الشخصية إذا كانت موجودة
        if ($user->avatar_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar_url)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_url);
        }

        // تسجيل الخروج وحذف الحساب
        \Illuminate\Support\Facades\Cache::forget('user-is-online-' . $user->id);
        \Illuminate\Support\Facades\Cache::put('last-activity-' . $user->id, now(), now()->addDays(7));
        \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم حذف حسابك بنجاح');
    }
}
