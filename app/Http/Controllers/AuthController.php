<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\SendOtpCode;
use App\Http\Controllers\EmailVerificationController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();

        // If user doesn't exist, show error
        if (!$user) {
            return back()->withErrors(['email' => 'بريد إلكتروني غير مسجل']);
        }

        // Check if email is verified (admin is exempt)
        if ($user->email_verified_at === null && $user->role !== 'admin') {
            // Generate OTP
            $otp = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
            $expires = \Carbon\Carbon::now()->addMinutes(10);
            
            \App\Models\EmailOtp::updateOrCreate(
                ['email' => $user->email],
                ['otp' => $otp, 'expires_at' => $expires, 'attempts' => 0]
            );
            
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SendOtpCode($user->name, $otp));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Login OTP send failed: ' . $e->getMessage());
                return back()->withErrors(['email' => 'تعذر إرسال رمز التحقق. يرجى المحاولة لاحقاً.']);
            }
            
            session(['otp_email' => $user->email]);
            return redirect()->route('otp.verify')->with('success', 'يرجى تأكيد حسابك. تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
        }

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            Cache::put('user-is-online-' . $user->id, true, now()->addSeconds(3));
            Cache::put('last-activity-' . $user->id, now(), now()->addDays(7));
            if ($user->role === 'admin') {
                return redirect('/admin/users');
            } elseif ($user->role === 'teacher') {
                return redirect('/teacher');
            } else {
                return redirect('/student/dashboard');
            }
        }

        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_.]+$/i',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $otp = Str::upper(Str::random(6));
        $expires = Carbon::now()->addMinutes(10);

        EmailOtp::where('email', $request->email)->delete();

        EmailOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => $expires,
            'attempts' => 0,
        ]);

        $mailFailed = false;
        try {
            Mail::to($request->email)->send(new SendOtpCode($request->name, $otp));
        } catch (\Exception $e) {
            Log::error('Registration OTP send failed: ' . $e->getMessage());
            $mailFailed = true;
        }

        session(['registration.pending' => [
            'name' => $request->name,
            'username' => $request->username ?? null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]]);

        session(['otp_email' => $request->email]);

        if (config('app.debug')) {
            session(['last_otp' => $otp]);
        }

        $redirect = redirect()->route('otp.verify');

        if ($mailFailed) {
            $redirect->with('error', 'تعذر إرسال رسالة التحقق إلى بريدك الإلكتروني. تحقق من صحة البريد ثم اطلب إعادة الإرسال.');
        }

        return $redirect;
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        if ($userId) {
            Cache::forget('user-is-online-' . $userId);
            Cache::put('last-activity-' . $userId, now(), now()->addDays(7));
            DB::table('sessions')->where('user_id', $userId)->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /**
     * Teacher Logout Function
     * تسجيل خروج خاص بالمعلمين فقط
     */
    public function teacherLogout(Request $request)
    {
        // احفظ المعرف قبل تسجيل الخروج
        $teacherId = Auth::id();
        if ($teacherId) {
            Cache::forget('user-is-online-' . $teacherId);
            Cache::put('last-activity-' . $teacherId, now(), now()->addDays(7));
            DB::table('sessions')->where('user_id', $teacherId)->delete();
        }

        // تسجيل الخروج
        Auth::logout();

        // إبطال الجلسة الحالية
        $request->session()->invalidate();

        // إعادة توليد CSRF token
        $request->session()->regenerateToken();

        // تسجيل نشاط الخروج
        Log::info('Teacher logged out', [
            'teacher_id' => $teacherId ?? 'unknown',
            'timestamp' => now()
        ]);

        // إعادة توجيه مع رسالة نجاح
        return redirect('/login')->with('success', 'تم تسجيل خروجك بنجاح');
    }

    /**
     * Show the Forgot Password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send Password Reset Link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني يجب أن يكون صحيحاً',
            'email.exists' => 'قد لا يكون هذا البريد الإلكتروني مسجلاً في نظامنا',
        ]);

        // Generate a token
        $token = Str::random(60);
        $email = $request->email;

        // Store reset token with expiration (60 minutes)
        session([
            'password_reset_token' => $token,
            'password_reset_email' => $email,
            'password_reset_expires' => now()->addMinutes(60),
        ]);

        try {
            // Send reset link email
            Mail::send('emails.password-reset', [
                'token' => $token,
                'email' => $email,
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('رابط إعادة تعيين كلمة المرور | جمعية إجلال');
            });

            Log::info('Password reset link sent to: ' . $email);

            return back()->with('status', 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني. الرابط صالح لمدة 60 دقيقة');
        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'فشل إرسال رابط إعادة التعيين. حاول مرة أخرى لاحقاً']);
        }
    }

    /**
     * Show the Reset Password form
     */
    public function showResetPassword($token)
    {
        // Check if token is valid
        $storedToken = session('password_reset_token');
        $storedEmail = session('password_reset_email');
        $expiresAt = session('password_reset_expires');

        if (!$storedToken || $storedToken !== $token || !$expiresAt || now()->isAfter($expiresAt)) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'رابط إعادة التعيين غير صحيح أو انتهت صلاحيته']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $storedEmail,
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        // Validate inputs
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',      // At least one uppercase
                'regex:/[a-z]/',      // At least one lowercase
                'regex:/[0-9]/',      // At least one number
                'regex:/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\\\|`~]/', // At least one special character
            ],
            'token' => 'required',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.exists' => 'قد لا يكون هذا البريد الإلكتروني مسجلاً في نظامنا',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'كلمات المرور غير متطابقة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز خاصة',
        ]);

        // Verify token
        $storedToken = session('password_reset_token');
        $storedEmail = session('password_reset_email');
        $expiresAt = session('password_reset_expires');

        if (!$storedToken || $storedToken !== $request->token || !$expiresAt || now()->isAfter($expiresAt) || $storedEmail !== $request->email) {
            return back()->withErrors(['token' => 'رابط إعادة التعيين غير صحيح أو انتهت صلاحيته']);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'المستخدم غير موجود']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Log::info('Password reset successfully for user: ' . $user->email);

        // Clear reset session data
        session()->forget(['password_reset_token', 'password_reset_email', 'password_reset_expires']);

        // Return success message
        return redirect()->route('login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول برابطك الجديد');
    }
}


