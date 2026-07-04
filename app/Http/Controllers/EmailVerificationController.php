<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use App\Mail\SendVerificationCode;
use App\Mail\AccountConfirmationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    /**
     * Generate and send OTP code
     */
    public function sendVerificationCode($email)
    {
        // Generate 6-character alphanumeric code
        $code = strtoupper(Str::random(6));
        
        // Delete existing verification code for this email
        EmailVerification::where('email', $email)->delete();
        
        // Create new verification code (expires in 10 minutes)
        $verification = EmailVerification::create([
            'email' => $email,
            'code' => $code,
            'attempts' => 0,
            'last_attempt_at' => null,
            'expires_at' => now()->addMinutes(10),
        ]);
        
        // Send email with OTP code
        Mail::to($email)->send(new SendVerificationCode($code, $email));
        
        return [
            'success' => true,
            'message' => 'تم إرسال الرمز إلى بريدك الإلكتروني',
        ];
    }

    /**
     * Verify the OTP code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|digits:1',
        ]);

        $code = implode('', $request->input('otp'));
        $email = $request->input('email');

        // Find the verification record
        $verification = EmailVerification::where('email', $email)->first();

        if (!$verification) {
            return redirect(route('auth.verify-email'))->with('error', 'لم نجد رمز التحقق. يرجى طلب رمز جديد.');
        }

        // Check if code has expired
        if ($verification->isExpired()) {
            $verification->delete();
            return redirect(route('auth.verify-email'))->with('error', 'انتهت صلاحية الرمز. يرجى طلب رمز جديد.');
        }

        // Check if code matches using timing-safe comparison
        if (!hash_equals((string) $verification->code, (string) $code)) {
            $verification->increment('attempts');
            
            // Lock after 5 failed attempts
            if ($verification->attempts >= 5) {
                $verification->delete();
                return redirect(route('auth.verify-email'))->with('error', 'عدد محاولات غير صحيح. يرجى طلب رمز جديد.');
            }
            
            return redirect(route('auth.verify-email'))->with(
                'error', 
                "الرمز غير صحيح. محاولات متبقية: " . (5 - $verification->attempts)
            );
        }

        // Code is correct, find or create user
        $user = User::where('email', $email)->first();

        if (!$user) {
            // User doesn't exist yet (registration flow)
            // Store verified email in session for registration completion
            session(['verified_email' => $email]);
            $verification->delete();
            
            return redirect(route('register'))->with('success', 'تم التحقق من بريدك الإلكتروني بنجاح. أكمل التسجيل.');
        }

        // Mark email as verified
        $user->update(['email_verified_at' => now()]);
        $verification->delete();

        // Send confirmation email
        Mail::to($user->email)->send(new AccountConfirmationEmail($user));

        // Log the user in
        auth()->login($user);
        \Illuminate\Support\Facades\Cache::put('user-is-online-' . $user->id, true, now()->addSeconds(3));
        \Illuminate\Support\Facades\Cache::put('last-activity-' . $user->id, now(), now()->addDays(7));

        return redirect(route('welcome'))->with([
            'success' => 'تم تأكيد حسابك بنجاح! مرحباً بك في إجلال',
            'user' => $user,
        ]);
    }

    /**
     * Resend verification code
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $verification = EmailVerification::where('email', $email)->first();

        if (!$verification) {
            // If no verification record exists, create a new one
            return response()->json($this->sendVerificationCode($email));
        }

        // Check if cooldown period (3 minutes) has passed
        if (!$verification->canResendCode()) {
            $remaining = $verification->getRemainingTime();
            return response()->json([
                'success' => false,
                'message' => "يرجى الانتظار {$remaining} ثانية قبل إعادة الطلب",
                'remaining' => $remaining,
            ], 429);
        }

        // Generate new code
        $code = strtoupper(Str::random(6));
        $verification->update([
            'code' => $code,
            'attempts' => 0,
            'last_attempt_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send email
        Mail::to($email)->send(new SendVerificationCode($code, $email));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز جديد إلى بريدك الإلكتروني',
        ]);
    }

    /**
     * Show verification page
     */
    public function showVerificationPage(Request $request)
    {
        $email = $request->query('email') ?? session('pending_verification_email');

        if (!$email) {
            return redirect(route('register'));
        }

        return view('auth.verify-email', [
            'email' => $email,
        ]);
    }

    /**
     * Show welcome page after verification
     */
    public function showWelcomePage()
    {
        if (!auth()->check()) {
            return redirect(route('login'));
        }

        return view('auth.welcome', [
            'user' => auth()->user(),
        ]);
    }
}
