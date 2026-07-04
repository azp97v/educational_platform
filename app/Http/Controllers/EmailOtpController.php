<?php

namespace App\Http\Controllers;

use App\Models\EmailOtp;
use App\Models\User;
use App\Mail\SendOtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailOtpController extends Controller
{
    /**
     * Display OTP verification page
     */
    public function showVerify()
    {
        $email = session('otp_email');

        if (!$email) {
            Log::warning('OTP verify page accessed without otp_email in session');
            return redirect()->route('register')->with('error', 'انتهت صلاحية جلستك. يرجى التسجيل مرة أخرى');
        }

        // Check if OTP record still exists
        $emailOtp = EmailOtp::where('email', $email)->first();
        if (!$emailOtp) {
            Log::warning('OTP record not found for email: ' . $email);
            session()->forget(['otp_email', 'registration.pending', 'last_otp']);
            return redirect()->route('register')->with('error', 'انتهت صلاحية رمز التحقق. يرجى التسجيل مرة أخرى');
        }

        // Normalize email for display consistency
        $email = strtolower(trim($email));

        // Pass the actual server time of OTP expiration to JavaScript
        return view('auth.otp-verify', [
            'email' => $email,
            'expiresAt' => $emailOtp->expires_at,
            'lastSentAt' => $emailOtp->last_sent_at,
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
    {
        // Normalize email first before validation
        $request->merge([
            'email' => strtolower(trim($request->email ?? ''))
        ]);
        
        $request->validate([
            'otp' => 'required|string|size:6',
            'email' => 'required|email|exists:email_otps,email',
        ], [
            'otp.required' => 'يرجى إدخال رمز التحقق',
            'otp.size' => 'رمز التحقق يجب أن يكون 6 أحرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.exists' => 'لايوجد طلب تحقق لهذا البريد',
        ]);

        $emailOtp = EmailOtp::byEmail($request->email);

        // Check if record exists
        if (!$emailOtp) {
            return back()
                ->withInput()
                ->with('error', 'انتهت صلاحية رمز التحقق، يرجى التسجيل مرة أخرى');
        }

        // Check if expired
        if ($emailOtp->isExpired()) {
            return back()
                ->withInput()
                ->with('error', 'انتهت صلاحية رمز التحقق، يرجى طلب رمز جديد');
        }

        if ($emailOtp->attempts >= 5) {
            return back()
                ->withInput()
                ->with('error', 'تجاوزت الحد الأقصى من المحاولات (5)، يرجى طلب رمز جديد');
        }

        // Verify OTP using secure comparison
        if (!hash_equals($emailOtp->otp, strtoupper($request->otp))) {
            $emailOtp->increment('attempts');

            $remaining = 5 - $emailOtp->attempts;

            return back()
                ->withInput()
                ->with('error', "رمز التحقق غير صحيح ({$remaining} محاولات متبقية)");
        }

        // OTP verified successfully
        // CREATE USER NOW (only after OTP verification succeeds)
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // User does not exist - create it now
            $registrationData = session('registration.pending', []);
            $name = $registrationData['name'] ?? 'User';
            $username = $registrationData['username'] ?? null;
            $hashedPassword = $registrationData['password'] ?? null;

            if (!$hashedPassword) {
                return back()->with('error', 'انتهت صلاحية جلستك. يرجى التسجيل مرة أخرى');
            }

            // Find teacher
            $teacher = User::where('email', 'teacher@iglal.com')->first();
            $teacherId = $teacher ? $teacher->id : null;

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => strtolower(trim($request->email)),
                'password' => $hashedPassword,
                'role' => 'student',
                'teacher_id' => $teacherId,
                'email_verified_at' => now(),
            ]);

            Log::info('User created after OTP verification:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ]);

            // Clear registration session data
            session()->forget(['otp_email', 'registration.pending', 'last_otp']);
        } else {
            // User exists but not verified yet - mark as verified
            $user->update([
                'email_verified_at' => now(),
            ]);

            Log::info('User marked as verified after OTP:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ]);
        }

        // Delete OTP record
        $emailOtp->delete();

        // Log user in
        auth()->login($user);
        \Illuminate\Support\Facades\Cache::put('user-is-online-' . $user->id, true, now()->addSeconds(3));
        \Illuminate\Support\Facades\Cache::put('last-activity-' . $user->id, now(), now()->addDays(7));

        return redirect()
            ->route('otp-success')
            ->with('success', 'تم التحقق من حسابك بنجاح!');
    }

    /**
     * Resend OTP code (rate limited: 3 attempts per 1 minute)
     */
    public function resend(Request $request)
    {
        try {
            // Normalize email first before validation  
            $request->merge([
                'email' => strtolower(trim($request->email ?? ''))
            ]);
            
            // Validate email format only (not existence yet)
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
            ]);

            // Email is already normalized via request merge
            $email = $request->email;

            // Check if email exists in email_otps table
            $emailOtp = EmailOtp::where('email', $email)->first();

            if (!$emailOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'لايوجد طلب تحقق لهذا البريد',
                ], 404);
            }

            // Check if expired (if OTP expired, delete and return error)
            if ($emailOtp->isExpired()) {
                $emailOtp->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت صلاحية طلب التحقق، يرجى التسجيل مرة أخرى',
                ], 404);
            }

            // Check resend cooldown (3-minute wait)
            if (!$emailOtp->canResend()) {
                $waitTime = $emailOtp->getResendWaitTime();

                return response()->json([
                    'success' => false,
                    'message' => "يرجى الانتظار {$waitTime} ثانية قبل إعادة الطلب",
                    'wait_time' => $waitTime,
                ], 429);
            }

            // Generate new OTP
            $newOtp = strtoupper(Str::random(6));
            $userName = User::where('email', $email)->first()?->name ?? 'المستخدم';

            // Try to send email
            try {
                Log::info('Attempting to send OTP to: ' . $email);
                
                Mail::to($email)->send(new SendOtpCode(
                    $userName,
                    $newOtp,
                    10
                ));

                Log::info('OTP sent successfully to: ' . $email);

                // Update OTP record ONLY if email was sent successfully
                $emailOtp->update([
                    'otp' => $newOtp,
                    'attempts' => 0,
                    'expires_at' => now()->addMinutes(10),
                    'last_sent_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني',
                    'expires_in_minutes' => 10,
                    'expires_at' => $emailOtp->expires_at->toIso8601String(),
                ]);
            } catch (\Exception $mailException) {
                Log::error('Mail send failed: ' . $mailException->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ في إرسال البريد. يرجى التحقق من البريد الإلكتروني والمحاولة لاحقاً',
                    'error' => config('app.debug') ? $mailException->getMessage() : null,
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['email'][0] ?? 'خطأ في التحقق',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Resend OTP error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً',
            ], 500);
        }
    }

    /**
     * Success page after OTP verification
     */
    public function showSuccess()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('auth.otp-success');
    }
}
