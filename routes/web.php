<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SmartRewindController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\StudentInquiryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\PdfController;

if (!function_exists('guessMessagingMimeType')) {
    function guessMessagingMimeType(string $path, ?string $fallback = null): string
    {
        if (!empty($fallback)) {
            return $fallback;
        }

        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'application/octet-stream',
            'bmp' => 'image/bmp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'mkv' => 'video/x-matroska',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'm4a' => 'audio/mp4',
            'ogg' => 'audio/ogg',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}

if (!function_exists('isSafeInlineMessagingMimeType')) {
    function isSafeInlineMessagingMimeType(string $mimeType): bool
    {
        $mimeType = strtolower(trim($mimeType));

        return $mimeType === 'application/pdf'
            || str_starts_with($mimeType, 'audio/')
            || str_starts_with($mimeType, 'video/')
            || (str_starts_with($mimeType, 'image/') && $mimeType !== 'image/svg+xml');
    }
}

// DEBUG: Test messaging interface (local only)
if (app()->environment('local')) {
    Route::get('/test-messaging', function () {
        $student = \App\Models\User::where('role', 'student')->first();
        if ($student) {
            Auth::login($student);
        }

        $controller = new \App\Http\Controllers\MessagingController();
        $request = new \Illuminate\Http\Request();
        return $controller->index($request);
    })->name('test.messaging');

    Route::get('/test-messaging-teacher', function () {
        $teacher = \App\Models\User::where('role', 'teacher')->first();
        if ($teacher) {
            Auth::login($teacher);
        }

        $controller = new \App\Http\Controllers\MessagingController();
        $request = new \Illuminate\Http\Request();
        return $controller->index($request);
    })->name('test.messaging.teacher');
}

Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'teacher' => redirect()->route('teacher.dashboard'),
            'student'  => redirect()->route('student.index'),
            'admin'    => redirect()->route('admin.index'),
            default    => redirect()->route('landing'),
        };
    }

    return view('landing-new');
})->name('home');

Route::get('/u/{user}', [\App\Http\Controllers\MessagingController::class, 'publicProfileCard'])->middleware('auth')->name('profile.card');

Route::get('/landing', function () {
    return view('landing-new');
})->name('landing');

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'admin' => redirect()->route('admin.index'),
        'teacher' => redirect()->route('teacher.dashboard'),
        'student' => redirect()->route('student.index'),
        default => redirect()->route('landing'),
    };
})->name('dashboard');

Route::get('/features-guide', function () {
    return view('features-guide');
})->name('features-guide');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::get('/login-new', function () { return redirect()->route('login'); })->name('login.new')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::get('/register-new', function () { return redirect()->route('register'); })->name('register.new')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,5');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Secure File Access Route (for message attachments)
Route::middleware('auth')->get('/storage/message-attachment/{path}', function (\Illuminate\Http\Request $request, $path) {
    $fullPath = ltrim(str_replace('\\', '/', (string) $path), '/');
    $basename = basename($fullPath);
    $publicCandidates = [
        $fullPath,
        'message_attachments/' . $basename,
    ];
    $privateCandidates = [
        'messaging/attachments/' . $basename,
    ];

    $resolvedDisk = null;
    $resolvedPath = null;
    $mimeType = null;

    foreach ($publicCandidates as $candidate) {
        if (Storage::disk('public')->exists($candidate)) {
            $resolvedDisk = 'public';
            $resolvedPath = $candidate;
            $mimeType = Storage::disk('public')->mimeType($candidate);
            break;
        }
    }

    if ($resolvedPath === null) {
        foreach ($privateCandidates as $candidate) {
            if (Storage::disk('local')->exists($candidate)) {
                $resolvedDisk = 'local';
                $resolvedPath = $candidate;
                $mimeType = Storage::disk('local')->mimeType($candidate);
                break;
            }
        }
    }

    if ($resolvedPath === null || $resolvedDisk === null) {
        abort(404, 'File not found');
    }

    $contentType = guessMessagingMimeType($resolvedPath, $mimeType);
    $disposition = isSafeInlineMessagingMimeType($contentType) ? 'inline' : 'attachment';
    $filename = str_replace(['\\', '"'], ['_', ''], basename($path));

    $headers = [
        'Content-Type' => $contentType,
        'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control' => 'private, max-age=3600, must-revalidate',
    ];

    return response()->file(Storage::disk($resolvedDisk)->path($resolvedPath), $headers);
})->where('path', '.*')->name('storage.message-attachment');

Route::middleware('auth')->get('/storage/message-audio/{path}', function (\Illuminate\Http\Request $request, $path) {
    $fullPath = ltrim(str_replace('\\', '/', (string) $path), '/');
    $basename = basename($fullPath);
    $publicCandidates = [
        $fullPath,
        'message_audio/' . $basename,
    ];
    $privateCandidates = [
        'messaging/audio/' . $basename,
    ];

    $resolvedDisk = null;
    $resolvedPath = null;
    $mimeType = null;

    foreach ($publicCandidates as $candidate) {
        if (Storage::disk('public')->exists($candidate)) {
            $resolvedDisk = 'public';
            $resolvedPath = $candidate;
            $mimeType = Storage::disk('public')->mimeType($candidate);
            break;
        }
    }

    if ($resolvedPath === null) {
        foreach ($privateCandidates as $candidate) {
            if (Storage::disk('local')->exists($candidate)) {
                $resolvedDisk = 'local';
                $resolvedPath = $candidate;
                $mimeType = Storage::disk('local')->mimeType($candidate);
                break;
            }
        }
    }

    if ($resolvedPath === null || $resolvedDisk === null) {
        abort(404, 'File not found');
    }

    $contentType = guessMessagingMimeType($resolvedPath, $mimeType ?: 'audio/webm');

    $headers = [
        'Content-Type' => $contentType,
        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control' => 'private, max-age=3600, must-revalidate',
    ];

    return response()->file(Storage::disk($resolvedDisk)->path($resolvedPath), $headers);
})->where('path', '.*')->name('storage.message-audio');

// Health Check (unauthenticated)
Route::get('/health', App\Http\Controllers\HealthController::class)->name('health');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,60');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,60');

// Email Verification Routes (New Registration Flow)
Route::post('/verification/send', [\App\Http\Controllers\EmailVerificationController::class, 'sendVerificationCode'])->name('verification.send')->middleware('throttle:6,10');
Route::get('/auth/verify-email', [\App\Http\Controllers\EmailVerificationController::class, 'showVerificationPage'])->name('auth.verify-email');
Route::post('/verification/verify', [\App\Http\Controllers\EmailVerificationController::class, 'verifyCode'])->name('verification.verify')->middleware('throttle:10,1');
Route::post('/verification/resend', [\App\Http\Controllers\EmailVerificationController::class, 'resendCode'])->name('verification.resend')->middleware('throttle:3,1');
Route::get('/welcome', [\App\Http\Controllers\EmailVerificationController::class, 'showWelcomePage'])->name('welcome');

// OTP Routes
Route::get('/otp/verify', [App\Http\Controllers\EmailOtpController::class, 'showVerify'])->name('otp.verify');
Route::post('/otp/verify', [App\Http\Controllers\EmailOtpController::class, 'verify'])->name('otp.verify.submit')->middleware('throttle:5,1');
Route::post('/otp/resend', [App\Http\Controllers\EmailOtpController::class, 'resend'])->name('otp.resend')->middleware('throttle:3,1');
Route::get('/otp-success', [App\Http\Controllers\EmailOtpController::class, 'showSuccess'])->name('otp-success');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/console/{page}', [AdminController::class, 'console'])->name('console');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/finance', [AdminController::class, 'finance'])->name('finance');
    Route::get('/rbac', [AdminController::class, 'rbac'])->name('rbac');
    Route::get('/liveops', [AdminController::class, 'liveops'])->name('liveops');
    Route::get('/activity', [AdminController::class, 'activity'])->name('activity');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    // User management — specific routes MUST come before /{user} wildcard
    Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');
    Route::post('/users/bulk', [AdminController::class, 'bulkAction'])->name('users.bulk');
    Route::get('/users', [AdminController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminController::class, 'create'])->name('create');
    Route::post('/users', [AdminController::class, 'store'])->name('store');
    Route::get('/users/{user}', [AdminController::class, 'show'])->name('show');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('destroy');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('users.reset-password');
    Route::get('/check-username', [AdminController::class, 'checkUsername'])->name('check-username');

    // Enrollment management
    Route::get('/enrollments', [AdminController::class, 'enrollments'])->name('enrollments');
    Route::post('/enrollments/{enrollment}/approve', [AdminController::class, 'approveEnrollment'])->name('enrollments.approve');
    Route::post('/enrollments/{enrollment}/reject', [AdminController::class, 'rejectEnrollment'])->name('enrollments.reject');

    // Failed jobs management
    Route::get('/failed-jobs', [AdminController::class, 'failedJobs'])->name('failed-jobs');
    Route::post('/failed-jobs/{uuid}/retry', [AdminController::class, 'retryFailedJob'])->name('failed-jobs.retry');
    Route::delete('/failed-jobs/{uuid}', [AdminController::class, 'deleteFailedJob'])->name('failed-jobs.delete');
    Route::delete('/failed-jobs', [AdminController::class, 'deleteAllFailedJobs'])->name('failed-jobs.delete-all');

    // Announcements (Axis 5)
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
    Route::get('/announcements/create', [AdminController::class, 'createAnnouncement'])->name('announcements.create');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AdminController::class, 'editAnnouncement'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AdminController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');
});

// Student Routes - Dashboard with Real Data
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // صفحات رئيسية
    Route::get('/dashboard', [StudentController::class, 'index'])->name('index');  // الرئيسية
    Route::get('/academy', [StudentController::class, 'academy'])->name('academy');  // الأكاديمية والمسارات
    Route::get('/exams', [StudentController::class, 'exams'])->name('exams');  // الاختبارات
    Route::get('/competition', [StudentController::class, 'competition'])->name('competition');  // المتنافسين
    Route::get('/achievements', [StudentController::class, 'achievementsPage'])->name('achievements');  // الإنجازات
    Route::get('/certificates/{student}/custom/{template}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customShow'])->name('certificates.custom.show');
    Route::get('/certificates/{student}/custom/{template}/download', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customDownload'])->name('certificates.custom.download');

    // الصفحات التفاعلية
    Route::post('/request-enrollment/{course}', [StudentController::class, 'requestEnrollment'])->name('request-enrollment');
    Route::get('/course/{course}', [StudentController::class, 'show'])->name('course.show');  // تفاصيل المسار
    Route::get('/course/{course}/card', [StudentController::class, 'courseCard'])->name('course.card');  // صفحة بطاقة المسار عند الإشعار
    Route::get('/lesson/{lesson}', [StudentController::class, 'watchLesson'])->name('lesson.show');  // مشاهدة الدرس
    Route::get('/lesson/{lesson}/media/{type}', [StudentController::class, 'streamLessonMedia'])->whereIn('type', ['audio', 'video'])->name('lesson.media');
    Route::get('/exam/{exam}', [StudentController::class, 'showExam'])->name('exam.show');  // العمل مع الاختبار
    Route::post('/exam/{exam}/submit', [StudentController::class, 'submitExam'])->name('exam.submit');  // تقديم الاختبار
    Route::get('/exam/{exam}/results', [StudentController::class, 'examResults'])->name('exam.results');  // نتائج الاختبار
    Route::post('/lesson/{lesson}/progress', [StudentController::class, 'updateProgress'])->name('progress.update');
    Route::get('/lesson/{lesson}/resources/download', [StudentController::class, 'downloadLessonResources'])->name('lesson.resources.download');

    // Lesson Ratings & Notes
    Route::post('/lesson/{lesson}/rating', [\App\Http\Controllers\LessonInteractionController::class, 'saveRating'])->name('lesson.rating.save');
    Route::get('/lesson/{lesson}/rating', [\App\Http\Controllers\LessonInteractionController::class, 'getRating'])->name('lesson.rating.get');
    Route::get('/lesson/{lesson}/notes', [\App\Http\Controllers\LessonInteractionController::class, 'getNotes'])->name('lesson.notes.get');
    Route::post('/lesson/{lesson}/notes', [\App\Http\Controllers\LessonInteractionController::class, 'addNote'])->name('lesson.notes.add');
    Route::delete('/lesson/{lesson}/notes/{note}', [\App\Http\Controllers\LessonInteractionController::class, 'deleteNote'])->name('lesson.notes.delete');
    Route::put('/lesson/{lesson}/notes/{note}', [\App\Http\Controllers\LessonInteractionController::class, 'updateNote'])->name('lesson.notes.update');

    // Inquiry Routes
    Route::post('/inquiry/store', [StudentInquiryController::class, 'store'])->name('inquiry.store');  // إرسال سؤال
    Route::get('/my-inquiries', [StudentInquiryController::class, 'studentIndex'])->name('inquiries.index');  // أسئلتي

    // Messaging Routes (with auth)
    Route::get('/messaging', [MessagingController::class, 'index'])->name('messaging');
    Route::get('/messaging/qr/{user}', [MessagingController::class, 'qrCode'])->name('messaging.qr');
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/calls', [CallController::class, 'initiate'])->name('calls.initiate');
        Route::post('/calls/{call}/offer', [CallController::class, 'offer'])->name('calls.offer');
        Route::post('/calls/{call}/invite', [CallController::class, 'invite'])->name('calls.invite');
        Route::post('/calls/{call}/peer-offer', [CallController::class, 'peerOffer'])->name('calls.peer-offer');
        Route::post('/calls/{call}/peer-answer', [CallController::class, 'peerAnswer'])->name('calls.peer-answer');
        Route::post('/calls/{call}/join', [CallController::class, 'join'])->name('calls.join');
        Route::post('/calls/{call}/answer', [CallController::class, 'answer'])->name('calls.answer');
        Route::post('/calls/{call}/reject', [CallController::class, 'reject'])->name('calls.reject');
        Route::post('/calls/{call}/ring', [CallController::class, 'ring'])->name('calls.ring');
        Route::post('/calls/{call}/end', [CallController::class, 'end'])->name('calls.end');
        Route::post('/calls/{call}/ice-candidate', [CallController::class, 'iceCandidate'])->name('calls.ice-candidate');
        Route::post('/calls/{call}/hms-token', [CallController::class, 'hmsToken'])->name('calls.hms-token');
        Route::get('/calls/pending', [CallController::class, 'pending'])->name('calls.pending');
    });
    Route::post('/messaging/send', [MessagingController::class, 'send'])->middleware('throttle:30,1')->name('messaging.send');
    Route::get('/messaging/refresh', [MessagingController::class, 'refresh'])->name('messaging.refresh');
    Route::get('/messaging/load', [MessagingController::class, 'loadMessages'])->name('messaging.load');
    Route::post('/messaging/audio', [MessagingController::class, 'uploadAudio'])->middleware('throttle:20,1')->name('messaging.audio');
    Route::post('/messaging/file', [MessagingController::class, 'uploadFile'])->middleware('throttle:20,1')->name('messaging.file');
    Route::get('/messaging/search', [MessagingController::class, 'searchMessages'])->middleware('throttle:20,1')->name('messaging.search');
    Route::get('/messaging/users/search', [MessagingController::class, 'searchUsers'])->middleware('throttle:30,1')->name('messaging.users.search');
    Route::post('/messaging/read', [MessagingController::class, 'markAsRead'])->name('messaging.read');
    Route::post('/messaging/read-single', [MessagingController::class, 'markSingleMessageAsRead'])->name('messaging.read-single');
    Route::get('/messaging/delta', [MessagingController::class, 'delta'])->name('messaging.delta');
    Route::get('/messaging/stream', [MessagingController::class, 'stream'])->name('messaging.stream');
    Route::post('/messaging/group', [MessagingController::class, 'createGroup'])->name('messaging.group');
    Route::get('/messaging/groups', [\App\Http\Controllers\GroupMessageController::class, 'myGroups'])->name('messaging.groups');
    Route::get('/messaging/group/{groupId}/messages', [\App\Http\Controllers\GroupMessageController::class, 'load'])->name('messaging.group.load');
    Route::post('/messaging/group/{groupId}/messages', [\App\Http\Controllers\GroupMessageController::class, 'send'])->middleware('throttle:30,1')->name('messaging.group.send');
    Route::get('/messaging/group/{groupId}/delta', [\App\Http\Controllers\GroupMessageController::class, 'delta'])->name('messaging.group.delta');
    Route::get('/messaging/group/{groupId}/info', [\App\Http\Controllers\GroupMessageController::class, 'info'])->name('messaging.group.info');
    Route::post('/messaging/group/{groupId}/members', [\App\Http\Controllers\GroupMessageController::class, 'addMember'])->name('messaging.group.members.add');
    Route::delete('/messaging/group/{groupId}/members/{userId}', [\App\Http\Controllers\GroupMessageController::class, 'removeMember'])->name('messaging.group.members.remove');
    Route::put('/messaging/group/{groupId}/settings', [\App\Http\Controllers\GroupMessageController::class, 'updateSettings'])->name('messaging.group.settings');
    Route::post('/messaging/group/{groupId}/members/{userId}/role', [\App\Http\Controllers\GroupMessageController::class, 'changeRole'])->name('messaging.group.members.role');
    Route::delete('/messaging/group/{groupId}', [\App\Http\Controllers\GroupMessageController::class, 'deleteGroup'])->name('messaging.group.delete');
    Route::post('/messaging/group/{groupId}/invite', [\App\Http\Controllers\GroupMessageController::class, 'generateInviteLink'])->name('messaging.group.invite.generate');
    Route::delete('/messaging/group/{groupId}/invite', [\App\Http\Controllers\GroupMessageController::class, 'revokeInviteLink'])->name('messaging.group.invite.revoke');
    Route::post('/messaging/group/{groupId}/permissions', [\App\Http\Controllers\GroupMessageController::class, 'updatePermissions'])->name('messaging.group.permissions');
    Route::post('/messaging/group/{groupId}/file', [\App\Http\Controllers\GroupMessageController::class, 'uploadFile'])->middleware('throttle:20,1')->name('messaging.group.file');
    Route::put('/messages/{message}', [MessagingController::class, 'update'])->name('messages.update');
    Route::post('/messages/{message}/audio-position', [MessagingController::class, 'saveAudioPosition'])->name('messaging.audio-position');
    Route::delete('/messages/{message}', [MessagingController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messaging/fix-attachments', [MessagingController::class, 'fixOldAttachments'])->name('messaging.fix-attachments');
    Route::post('/messaging/reaction', [MessagingController::class, 'toggleReaction'])->name('messaging.reaction');
    // Status (Stories) Routes
    Route::get('/messaging/statuses', [\App\Http\Controllers\StatusController::class, 'getStatuses'])->name('messaging.statuses');
    Route::post('/messaging/status', [\App\Http\Controllers\StatusController::class, 'createStatus'])->name('messaging.status.create');
    Route::post('/messaging/status/reply', [\App\Http\Controllers\StatusController::class, 'replyToStatus'])->name('messaging.status.reply');
    Route::post('/messaging/status/reaction', [\App\Http\Controllers\StatusController::class, 'reactToStatus'])->name('messaging.status.reaction');
    Route::post('/messaging/status/{status}/view', [\App\Http\Controllers\StatusController::class, 'viewStatus'])->name('messaging.status.view');
    Route::post('/messaging/status/{status}/update', [\App\Http\Controllers\StatusController::class, 'updateStatus'])->name('messaging.status.update');
    Route::get('/messaging/status/{status}/viewers', [\App\Http\Controllers\StatusController::class, 'getStatusViewers'])->name('messaging.status.viewers');
    Route::delete('/messaging/status/{status}', [\App\Http\Controllers\StatusController::class, 'deleteStatus'])->name('messaging.status.delete');
    Route::post('/messaging/pin', [MessagingController::class, 'pinMessage'])->name('messaging.pin');
    Route::post('/messaging/forward', [MessagingController::class, 'forwardMessage'])->name('messaging.forward');
    Route::get('/messaging/settings', [\App\Http\Controllers\MessagingSettingsController::class, 'getMessagingSettings'])->name('messaging.settings.get');
    Route::post('/messaging/settings', [\App\Http\Controllers\MessagingSettingsController::class, 'saveMessagingSettings'])->name('messaging.settings.save');
    Route::post('/messaging/account', [\App\Http\Controllers\MessagingSettingsController::class, 'updateAccountInfo'])->name('messaging.account.update');
    Route::get('/messaging/account/check-username', [\App\Http\Controllers\MessagingSettingsController::class, 'checkUsernameAvailability'])->name('messaging.account.check-username');
    Route::get('/messaging/account/check-phone', [\App\Http\Controllers\MessagingSettingsController::class, 'checkPhoneAvailability'])->name('messaging.account.check-phone');
    Route::get('/messaging/blocked', [\App\Http\Controllers\MessagingSettingsController::class, 'listBlockedUsers'])->name('messaging.blocked.list');
    Route::post('/messaging/blocked', [\App\Http\Controllers\MessagingSettingsController::class, 'blockUser'])->name('messaging.blocked.add');
    Route::delete('/messaging/blocked/{userId}', [\App\Http\Controllers\MessagingSettingsController::class, 'unblockUser'])->name('messaging.blocked.remove');
    Route::get('/messaging/sessions', [\App\Http\Controllers\MessagingSettingsController::class, 'listActiveSessions'])->name('messaging.sessions.list');
    Route::delete('/messaging/sessions/{sessionId}', [\App\Http\Controllers\MessagingSettingsController::class, 'terminateSession'])->name('messaging.sessions.terminate');
    Route::post('/messaging/2fa/request', [\App\Http\Controllers\MessagingSettingsController::class, 'request2FACode'])->middleware('throttle:5,1')->name('messaging.2fa.request');
    Route::post('/messaging/2fa/confirm', [\App\Http\Controllers\MessagingSettingsController::class, 'confirm2FA'])->name('messaging.2fa.confirm');
    Route::post('/messaging/2fa/disable', [\App\Http\Controllers\MessagingSettingsController::class, 'disable2FA'])->name('messaging.2fa.disable');
    Route::get('/messaging/folders', [\App\Http\Controllers\MessagingSettingsController::class, 'listFolders'])->name('messaging.folders.list');
    Route::post('/messaging/folders', [\App\Http\Controllers\MessagingSettingsController::class, 'saveFolder'])->name('messaging.folders.save');
    Route::delete('/messaging/folders/{folderId}', [\App\Http\Controllers\MessagingSettingsController::class, 'deleteFolder'])->name('messaging.folders.delete');
    Route::post('/messaging/locale', [MessagingController::class, 'updateLocale'])->name('messaging.locale.update');
    Route::get('/messaging/frequent-contacts', [MessagingController::class, 'getFrequentContacts'])->name('messaging.frequent-contacts');
    Route::get('/messaging/export', [MessagingController::class, 'exportMyData'])->name('messaging.export');
    Route::post('/messaging/typing', [MessagingController::class, 'typingPing'])->name('messaging.typing');
    // Wallpaper Routes
    Route::post('/messaging/wallpaper', [MessagingController::class, 'setWallpaper'])->name('messaging.wallpaper.set');
    Route::get('/messaging/wallpaper', [MessagingController::class, 'getWallpaper'])->name('messaging.wallpaper.get');

    // Saved Messages Routes
    Route::get('/messaging/saved', [MessagingController::class, 'getSavedMessages'])->name('messaging.saved.list');
    Route::get('/messaging/saved-ids', [MessagingController::class, 'getSavedMessageIds'])->name('messaging.saved.ids');
    Route::post('/messaging/save/{messageId}', [MessagingController::class, 'saveMessage'])->name('messaging.save');
    Route::delete('/messaging/save/{messageId}', [MessagingController::class, 'unsaveMessage'])->name('messaging.unsave');

    // Stickers & GIFs
    Route::get('/messaging/stickers', [\App\Http\Controllers\StickerController::class, 'index'])->name('messaging.stickers.index');
    Route::post('/messaging/stickers', [\App\Http\Controllers\StickerController::class, 'store'])->middleware('throttle:20,1')->name('messaging.stickers.store');
    Route::post('/messaging/stickers/{sticker}/favorite', [\App\Http\Controllers\StickerController::class, 'toggleFavorite'])->name('messaging.stickers.favorite');
    Route::post('/messaging/stickers/{sticker}/used', [\App\Http\Controllers\StickerController::class, 'markUsed'])->name('messaging.stickers.used');
    Route::delete('/messaging/stickers/{sticker}', [\App\Http\Controllers\StickerController::class, 'destroy'])->name('messaging.stickers.destroy');
    Route::get('/messaging/gifs/search', [\App\Http\Controllers\GifController::class, 'search'])->name('messaging.gifs.search');

    // E2E Encryption Key Management
    Route::post('/messaging/encryption/register-key', [MessagingController::class, 'registerEncryptionKey'])->name('messaging.encryption.register');
    Route::get('/messaging/encryption/public-key/{user}', [MessagingController::class, 'getEncryptionPublicKey'])->name('messaging.encryption.public-key');
});

// Gamification Routes (All Authenticated Users)
Route::middleware('auth')->prefix('gamification')->name('gamification.')->group(function () {
    Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/achievements', [GamificationController::class, 'achievements'])->name('achievements');
    Route::get('/achievements/{achievement}', [GamificationController::class, 'achievementDetail'])->name('achievement.detail');
    Route::get('/dashboard', [GamificationController::class, 'dashboard'])->name('dashboard');
    Route::get('/user/{user}/stats', [GamificationController::class, 'userStats'])->name('user.stats');
    Route::get('/compare/{user1}/{user2}', [GamificationController::class, 'compare'])->name('compare');

    // API Endpoints
    Route::get('/api/stats', [GamificationController::class, 'getCurrentUserStats'])->name('api.stats');
    Route::get('/api/achievements', [GamificationController::class, 'getAvailableAchievements'])->name('api.achievements');
    Route::post('/api/update-leaderboard', [GamificationController::class, 'updateLeaderboard'])->middleware('role:admin')->name('api.update');
});
// Certificate Routes (All Users)
Route::middleware('auth')->prefix('certificates')->name('certificate.')->group(function () {
    Route::get('/', [CertificateController::class, 'index'])->name('index');
    Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
    Route::get('/{certificate}/pdf', [CertificateController::class, 'viewPDF'])->name('pdf');
    Route::get('/{certificate}/download', [CertificateController::class, 'downloadPDF'])->name('download');

    // Admin Only
    Route::post('/issue', [CertificateController::class, 'issue'])->middleware('role:admin')->name('issue');
});

// Group invite link — authenticated users join via shared link
Route::middleware('auth')->get('/g/{token}', [\App\Http\Controllers\GroupMessageController::class, 'joinViaLink'])->name('group.join');

// Public Certificate Verification
Route::get('/verify-certificate', [CertificateController::class, 'verify'])->name('certificate.verify');
Route::post('/verify-certificate', [CertificateController::class, 'verify'])->name('certificate.verify.check');

// ─── PDF Queue System Routes ─────────────────────────────────────────────────
// صفحة الانتظار أثناء توليد PDF (تحتاج تسجيل دخول)
Route::middleware('auth')->group(function () {
    Route::get('/pdf/wait',     [PdfController::class, 'waitPage'])    ->name('pdf.wait');
    Route::get('/pdf/status',   [PdfController::class, 'statusCheck']) ->name('pdf.status');
    Route::get('/pdf/download', [PdfController::class, 'download'])    ->name('pdf.download');
});

// DEBUG: Local diagnostics only
if (app()->environment('local')) {
    Route::get('/test-teacher-debug', function () {
        $user = Auth::user();
        return response()->json([
            'logged_in' => Auth::check(),
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_role' => $user?->role,
            'user_email' => $user?->email,
        ])->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    });

    Route::get('/__codex_probe', function () {
        return response()->json([
            'probe' => 'copy2',
            'base_path' => base_path(),
            'time' => now()->toDateTimeString(),
        ]);
    });
}

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {
    Route::match(['get', 'post'], '/logout', [TeacherController::class, 'teacherLogout'])->name('teacher.logout');
    Route::get('/', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard.full');
    Route::get('/courses', [TeacherController::class, 'courses'])->name('teacher.courses');
    Route::get('/index', [TeacherController::class, 'index'])->name('teacher.index');
    Route::get('/create', [TeacherController::class, 'createCourse'])->name('teacher.create');
    Route::post('/courses', [TeacherController::class, 'storeCourse'])->name('teacher.store');
    Route::get('/courses/{course}', [TeacherController::class, 'show'])->name('teacher.show');
    Route::get('/courses/{course}/edit', [TeacherController::class, 'editCourse'])->name('teacher.edit');
    Route::put('/courses/{course}', [TeacherController::class, 'updateCourse'])->name('teacher.update');
    Route::delete('/courses/{course}', [TeacherController::class, 'deleteCourse'])->name('teacher.delete');

    // Category Management Routes
    Route::get('/categories', [TeacherController::class, 'categoriesIndex'])->name('teacher.categories');
    Route::post('/categories', [TeacherController::class, 'categoryStore'])->name('teacher.categories.store');
    Route::put('/categories/{category}', [TeacherController::class, 'categoryUpdate'])->name('teacher.categories.update');
    Route::delete('/categories/{category}', [TeacherController::class, 'categoryDestroy'])->name('teacher.categories.destroy');

    // Lessons Routes - Form Pages
    Route::get('/lessons/create', [TeacherController::class, 'showLessonForm'])->name('teacher.lesson.create');
    Route::get('/lessons/{lesson}/edit', [TeacherController::class, 'editLesson'])->name('teacher.lesson.edit');
    Route::post('/courses/{course}/lessons', [TeacherController::class, 'addLesson'])->name('teacher.addLesson');
    Route::put('/lessons/{lesson}', [TeacherController::class, 'updateLesson'])->name('teacher.updateLesson');
    Route::delete('/lessons/{lesson}', [TeacherController::class, 'deleteLesson'])->name('teacher.deleteLesson');

    // API Routes for lessons
    Route::post('/api/lessons/upload-file', [TeacherController::class, 'uploadLessonFile'])->name('teacher.api.upload-lesson-file');
    Route::post('/api/youtube-duration', [TeacherController::class, 'getYouTubeDuration'])->name('teacher.api.youtube-duration');
    Route::get('/lessons/{lesson}/student-notes', [\App\Http\Controllers\LessonInteractionController::class, 'teacherNotes'])->name('teacher.lesson.student-notes');

    // Exam Routes - Form Pages
    Route::get('/exams/new', [TeacherController::class, 'showCreateExamPage'])->name('teacher.exam.new');
    Route::get('/exams/create', [TeacherController::class, 'showExamForm'])->name('teacher.exam.create');
    Route::get('/exams/{exam}/edit', [TeacherController::class, 'editExam'])->name('teacher.exam.edit');
    Route::get('/exams/{exam}/questions', [TeacherController::class, 'showExamQuestions'])->name('teacher.exam.questions');
    Route::post('/lessons/{lesson}/exams', [TeacherController::class, 'createExam'])->name('teacher.createExam');
    Route::put('/exams/{exam}', [TeacherController::class, 'updateExam'])->name('teacher.exam.update');
    Route::post('/exams/{exam}/toggle-publish', [TeacherController::class, 'toggleExamPublish'])->name('teacher.exam.toggle-publish');
    Route::delete('/exams/{exam}', [TeacherController::class, 'deleteExam'])->name('teacher.exam.delete');

    // Questions & Answers Routes
    Route::post('/exams/{exam}/questions', [TeacherController::class, 'addQuestion'])->name('teacher.addQuestion');
    Route::put('/questions/{question}', [TeacherController::class, 'updateQuestion'])->name('teacher.updateQuestion');
    Route::delete('/questions/{question}', [TeacherController::class, 'deleteQuestion'])->name('teacher.deleteQuestion');
    Route::post('/questions/{question}/answers', [TeacherController::class, 'addAnswer'])->name('teacher.addAnswer');
    Route::put('/answers/{answer}', [TeacherController::class, 'updateAnswer'])->name('teacher.updateAnswer');
    Route::delete('/answers/{answer}', [TeacherController::class, 'deleteAnswer'])->name('teacher.deleteAnswer');

    // Management Pages
    Route::get('/questions-manage', [TeacherController::class, 'showQuestionsPage'])->name('teacher.questions.manage');
    Route::post('/questions/{question}/answer', [TeacherController::class, 'answerStudentQuestion'])->name('teacher.questions.answer');
    Route::delete('/questions/{question}', [TeacherController::class, 'deleteStudentQuestion'])->name('teacher.questions.delete');
    Route::delete('/questions/answered/bulk-clear', [TeacherController::class, 'clearAnsweredQuestions'])->name('teacher.questions.clear-answered');
    Route::delete('/inquiries/{inquiry}', [TeacherController::class, 'deleteInquiry'])->name('teacher.inquiries.delete');
    Route::delete('/inquiries/answered/bulk-clear', [TeacherController::class, 'clearAnsweredInquiries'])->name('teacher.inquiries.clear-answered');

    // Analytics & Reports
    Route::get('/analytics', [TeacherController::class, 'analytics'])->name('teacher.analytics');
    Route::get('/students', [TeacherController::class, 'students'])->name('teacher.students');
    Route::get('/students/{student}', [TeacherController::class, 'showStudentProfile'])->name('teacher.student.profile')->where('student', '[0-9]+');
    Route::get('/questions', fn() => redirect()->route('teacher.questions.manage'))->name('teacher.questions');
    Route::get('/exams', [TeacherController::class, 'exams'])->name('teacher.exams');
    Route::get('/exams/{exam}/results', [TeacherController::class, 'examResults'])->name('teacher.exam.results');
    // Legacy inquiry routes — redirect to the unified questions page
    Route::get('/inquiries', fn() => redirect()->route('teacher.questions.manage'))->name('teacher.inquiries');
    Route::get('/inquiries/analytics', fn() => redirect()->route('teacher.questions.manage'))->name('teacher.inquiries.analytics');
    Route::post('/inquiries/{inquiry}/answer', [TeacherController::class, 'answerInquiry'])->name('teacher.inquiries.answer');

    // Messaging Routes
    Route::get('/messaging', [MessagingController::class, 'index'])->name('teacher.messaging');
    Route::get('/messaging/qr/{user}', [MessagingController::class, 'qrCode'])->name('teacher.messaging.qr');
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/calls', [CallController::class, 'initiate'])->name('teacher.calls.initiate');
        Route::post('/calls/{call}/offer', [CallController::class, 'offer'])->name('teacher.calls.offer');
        Route::post('/calls/{call}/invite', [CallController::class, 'invite'])->name('teacher.calls.invite');
        Route::post('/calls/{call}/peer-offer', [CallController::class, 'peerOffer'])->name('teacher.calls.peer-offer');
        Route::post('/calls/{call}/peer-answer', [CallController::class, 'peerAnswer'])->name('teacher.calls.peer-answer');
        Route::post('/calls/{call}/join', [CallController::class, 'join'])->name('teacher.calls.join');
        Route::post('/calls/{call}/answer', [CallController::class, 'answer'])->name('teacher.calls.answer');
        Route::post('/calls/{call}/reject', [CallController::class, 'reject'])->name('teacher.calls.reject');
        Route::post('/calls/{call}/ring', [CallController::class, 'ring'])->name('teacher.calls.ring');
        Route::post('/calls/{call}/end', [CallController::class, 'end'])->name('teacher.calls.end');
        Route::post('/calls/{call}/ice-candidate', [CallController::class, 'iceCandidate'])->name('teacher.calls.ice-candidate');
        Route::post('/calls/{call}/hms-token', [CallController::class, 'hmsToken'])->name('teacher.calls.hms-token');
        Route::get('/calls/pending', [CallController::class, 'pending'])->name('teacher.calls.pending');
    });
    Route::post('/messaging/send', [MessagingController::class, 'send'])->middleware('throttle:30,1')->name('teacher.messaging.send');
    Route::get('/messaging/refresh', [MessagingController::class, 'refresh'])->name('teacher.messaging.refresh');
    Route::get('/messaging/load', [MessagingController::class, 'loadMessages'])->name('teacher.messaging.load');
    Route::post('/messaging/audio', [MessagingController::class, 'uploadAudio'])->middleware('throttle:20,1')->name('teacher.messaging.audio');
    Route::post('/messaging/file', [MessagingController::class, 'uploadFile'])->middleware('throttle:20,1')->name('teacher.messaging.file');
    Route::get('/messaging/search', [MessagingController::class, 'searchMessages'])->middleware('throttle:20,1')->name('teacher.messaging.search');
    Route::get('/messaging/users/search', [MessagingController::class, 'searchUsers'])->middleware('throttle:30,1')->name('teacher.messaging.users.search');
    Route::post('/messaging/read', [MessagingController::class, 'markAsRead'])->name('teacher.messaging.read');
    Route::post('/messaging/read-single', [MessagingController::class, 'markSingleMessageAsRead'])->name('teacher.messaging.read-single');
    Route::get('/messaging/delta', [MessagingController::class, 'delta'])->name('teacher.messaging.delta');
    Route::get('/messaging/stream', [MessagingController::class, 'stream'])->name('teacher.messaging.stream');
    Route::post('/messaging/group', [MessagingController::class, 'createGroup'])->name('teacher.messaging.group');
    Route::get('/messaging/groups', [\App\Http\Controllers\GroupMessageController::class, 'myGroups'])->name('teacher.messaging.groups');
    Route::get('/messaging/group/{groupId}/messages', [\App\Http\Controllers\GroupMessageController::class, 'load'])->name('teacher.messaging.group.load');
    Route::post('/messaging/group/{groupId}/messages', [\App\Http\Controllers\GroupMessageController::class, 'send'])->middleware('throttle:30,1')->name('teacher.messaging.group.send');
    Route::get('/messaging/group/{groupId}/delta', [\App\Http\Controllers\GroupMessageController::class, 'delta'])->name('teacher.messaging.group.delta');
    Route::get('/messaging/group/{groupId}/info', [\App\Http\Controllers\GroupMessageController::class, 'info'])->name('teacher.messaging.group.info');
    Route::post('/messaging/group/{groupId}/members', [\App\Http\Controllers\GroupMessageController::class, 'addMember'])->name('teacher.messaging.group.members.add');
    Route::delete('/messaging/group/{groupId}/members/{userId}', [\App\Http\Controllers\GroupMessageController::class, 'removeMember'])->name('teacher.messaging.group.members.remove');
    Route::put('/messaging/group/{groupId}/settings', [\App\Http\Controllers\GroupMessageController::class, 'updateSettings'])->name('teacher.messaging.group.settings');
    Route::post('/messaging/group/{groupId}/members/{userId}/role', [\App\Http\Controllers\GroupMessageController::class, 'changeRole'])->name('teacher.messaging.group.members.role');
    Route::delete('/messaging/group/{groupId}', [\App\Http\Controllers\GroupMessageController::class, 'deleteGroup'])->name('teacher.messaging.group.delete');
    Route::post('/messaging/group/{groupId}/invite', [\App\Http\Controllers\GroupMessageController::class, 'generateInviteLink'])->name('teacher.messaging.group.invite.generate');
    Route::delete('/messaging/group/{groupId}/invite', [\App\Http\Controllers\GroupMessageController::class, 'revokeInviteLink'])->name('teacher.messaging.group.invite.revoke');
    Route::post('/messaging/group/{groupId}/permissions', [\App\Http\Controllers\GroupMessageController::class, 'updatePermissions'])->name('teacher.messaging.group.permissions');
    Route::post('/messaging/group/{groupId}/file', [\App\Http\Controllers\GroupMessageController::class, 'uploadFile'])->middleware('throttle:20,1')->name('teacher.messaging.group.file');
    Route::put('/messages/{message}', [MessagingController::class, 'update'])->name('teacher.messages.update');
    Route::post('/messages/{message}/audio-position', [MessagingController::class, 'saveAudioPosition'])->name('teacher.messaging.audio-position');
    Route::delete('/messages/{message}', [MessagingController::class, 'destroy'])->name('teacher.messages.destroy');
    Route::post('/messaging/fix-attachments', [MessagingController::class, 'fixOldAttachments'])->name('teacher.messaging.fix-attachments');
    Route::post('/messaging/reaction', [MessagingController::class, 'toggleReaction'])->name('teacher.messaging.reaction');
    // Status (Stories) Routes
    Route::get('/messaging/statuses', [\App\Http\Controllers\StatusController::class, 'getStatuses'])->name('teacher.messaging.statuses');
    Route::post('/messaging/status', [\App\Http\Controllers\StatusController::class, 'createStatus'])->name('teacher.messaging.status.create');
    Route::post('/messaging/status/reply', [\App\Http\Controllers\StatusController::class, 'replyToStatus'])->name('teacher.messaging.status.reply');
    Route::post('/messaging/status/reaction', [\App\Http\Controllers\StatusController::class, 'reactToStatus'])->name('teacher.messaging.status.reaction');
    Route::post('/messaging/status/{status}/view', [\App\Http\Controllers\StatusController::class, 'viewStatus'])->name('teacher.messaging.status.view');
    Route::post('/messaging/status/{status}/update', [\App\Http\Controllers\StatusController::class, 'updateStatus'])->name('teacher.messaging.status.update');
    Route::get('/messaging/status/{status}/viewers', [\App\Http\Controllers\StatusController::class, 'getStatusViewers'])->name('teacher.messaging.status.viewers');
    Route::delete('/messaging/status/{status}', [\App\Http\Controllers\StatusController::class, 'deleteStatus'])->name('teacher.messaging.status.delete');
    Route::post('/messaging/pin', [MessagingController::class, 'pinMessage'])->name('teacher.messaging.pin');
    Route::post('/messaging/forward', [MessagingController::class, 'forwardMessage'])->name('teacher.messaging.forward');
    Route::get('/messaging/settings', [\App\Http\Controllers\MessagingSettingsController::class, 'getMessagingSettings'])->name('teacher.messaging.settings.get');
    Route::post('/messaging/settings', [\App\Http\Controllers\MessagingSettingsController::class, 'saveMessagingSettings'])->name('teacher.messaging.settings.save');
    Route::post('/messaging/account', [\App\Http\Controllers\MessagingSettingsController::class, 'updateAccountInfo'])->name('teacher.messaging.account.update');
    Route::get('/messaging/account/check-username', [\App\Http\Controllers\MessagingSettingsController::class, 'checkUsernameAvailability'])->name('teacher.messaging.account.check-username');
    Route::get('/messaging/account/check-phone', [\App\Http\Controllers\MessagingSettingsController::class, 'checkPhoneAvailability'])->name('teacher.messaging.account.check-phone');
    Route::get('/messaging/blocked', [\App\Http\Controllers\MessagingSettingsController::class, 'listBlockedUsers'])->name('teacher.messaging.blocked.list');
    Route::post('/messaging/blocked', [\App\Http\Controllers\MessagingSettingsController::class, 'blockUser'])->name('teacher.messaging.blocked.add');
    Route::delete('/messaging/blocked/{userId}', [\App\Http\Controllers\MessagingSettingsController::class, 'unblockUser'])->name('teacher.messaging.blocked.remove');
    Route::get('/messaging/sessions', [\App\Http\Controllers\MessagingSettingsController::class, 'listActiveSessions'])->name('teacher.messaging.sessions.list');
    Route::delete('/messaging/sessions/{sessionId}', [\App\Http\Controllers\MessagingSettingsController::class, 'terminateSession'])->name('teacher.messaging.sessions.terminate');
    Route::post('/messaging/2fa/request', [\App\Http\Controllers\MessagingSettingsController::class, 'request2FACode'])->middleware('throttle:5,1')->name('teacher.messaging.2fa.request');
    Route::post('/messaging/2fa/confirm', [\App\Http\Controllers\MessagingSettingsController::class, 'confirm2FA'])->name('teacher.messaging.2fa.confirm');
    Route::post('/messaging/2fa/disable', [\App\Http\Controllers\MessagingSettingsController::class, 'disable2FA'])->name('teacher.messaging.2fa.disable');
    Route::get('/messaging/folders', [\App\Http\Controllers\MessagingSettingsController::class, 'listFolders'])->name('teacher.messaging.folders.list');
    Route::post('/messaging/folders', [\App\Http\Controllers\MessagingSettingsController::class, 'saveFolder'])->name('teacher.messaging.folders.save');
    Route::delete('/messaging/folders/{folderId}', [\App\Http\Controllers\MessagingSettingsController::class, 'deleteFolder'])->name('teacher.messaging.folders.delete');
    Route::post('/messaging/locale', [MessagingController::class, 'updateLocale'])->name('teacher.messaging.locale.update');
    Route::get('/messaging/frequent-contacts', [MessagingController::class, 'getFrequentContacts'])->name('teacher.messaging.frequent-contacts');
    Route::get('/messaging/export', [MessagingController::class, 'exportMyData'])->name('teacher.messaging.export');
    Route::post('/messaging/typing', [MessagingController::class, 'typingPing'])->name('teacher.messaging.typing');
    // Wallpaper Routes
    Route::post('/messaging/wallpaper', [MessagingController::class, 'setWallpaper'])->name('teacher.messaging.wallpaper.set');
    Route::get('/messaging/wallpaper', [MessagingController::class, 'getWallpaper'])->name('teacher.messaging.wallpaper.get');

    // Saved Messages Routes
    Route::get('/messaging/saved', [MessagingController::class, 'getSavedMessages'])->name('teacher.messaging.saved.list');
    Route::get('/messaging/saved-ids', [MessagingController::class, 'getSavedMessageIds'])->name('teacher.messaging.saved.ids');
    Route::post('/messaging/save/{messageId}', [MessagingController::class, 'saveMessage'])->name('teacher.messaging.save');
    Route::delete('/messaging/save/{messageId}', [MessagingController::class, 'unsaveMessage'])->name('teacher.messaging.unsave');

    // Stickers & GIFs
    Route::get('/messaging/stickers', [\App\Http\Controllers\StickerController::class, 'index'])->name('teacher.messaging.stickers.index');
    Route::post('/messaging/stickers', [\App\Http\Controllers\StickerController::class, 'store'])->middleware('throttle:20,1')->name('teacher.messaging.stickers.store');
    Route::post('/messaging/stickers/{sticker}/favorite', [\App\Http\Controllers\StickerController::class, 'toggleFavorite'])->name('teacher.messaging.stickers.favorite');
    Route::post('/messaging/stickers/{sticker}/used', [\App\Http\Controllers\StickerController::class, 'markUsed'])->name('teacher.messaging.stickers.used');
    Route::delete('/messaging/stickers/{sticker}', [\App\Http\Controllers\StickerController::class, 'destroy'])->name('teacher.messaging.stickers.destroy');
    Route::get('/messaging/gifs/search', [\App\Http\Controllers\GifController::class, 'search'])->name('teacher.messaging.gifs.search');

    // E2E Encryption Key Management
    Route::post('/messaging/encryption/register-key', [MessagingController::class, 'registerEncryptionKey'])->name('teacher.messaging.encryption.register');
    Route::get('/messaging/encryption/public-key/{user}', [MessagingController::class, 'getEncryptionPublicKey'])->name('teacher.messaging.encryption.public-key');

    // Enrollment Management Routes
    Route::get('/enrollment-requests', [TeacherController::class, 'enrollmentRequests'])->name('teacher.enrollment.requests');
    Route::post('/enroll/{enrollment}/approve', [TeacherController::class, 'approveEnrollment'])->name('teacher.enroll.approve');
    Route::post('/enroll/{enrollment}/reject', [TeacherController::class, 'rejectEnrollment'])->name('teacher.enroll.reject');
    Route::delete('/enroll/{enrollment}', [TeacherController::class, 'removeEnrolledStudent'])->name('teacher.enroll.remove');

    // Certificate Designer Routes
    Route::prefix('certificates')->name('teacher.certificates.')->group(function () {
        // Student CRUD
        Route::get('/templates', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'myTemplates'])->name('my-templates');
        Route::get('/students', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'students'])->name('students');
        Route::get('/students/create', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'createStudent'])->name('students.create');
        Route::post('/students', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'storeStudent'])->name('students.store');
        Route::get('/students/{student}/edit', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'editStudent'])->name('students.edit');
        Route::put('/students/{student}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'updateStudent'])->name('students.update');
        Route::delete('/students/{student}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'destroyStudent'])->name('students.destroy');

        // Student Profile/Stats
        Route::get('/students/{student}/profile', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'studentProfile'])->name('student.profile');

        // Template Gallery & Presets
        Route::get('/students/{student}/gallery', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'gallery'])->name('gallery');
        Route::get('/preview/{templateNum}/{student}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'preview'])->name('preview');
        Route::get('/download/{templateNum}/{student}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'download'])->name('download');

        // Custom Template CRUD
        Route::get('/students/{student}/custom/create', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customCreate'])->name('custom.create');
        Route::post('/students/{student}/custom', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customStore'])->name('custom.store');
        Route::get('/students/{student}/custom/{template}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customShow'])->name('custom.show');
        Route::get('/students/{student}/custom/{template}/edit', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customEdit'])->name('custom.edit');
        Route::put('/students/{student}/custom/{template}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customUpdate'])->name('custom.update');
        Route::delete('/students/{student}/custom/{template}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customDestroy'])->name('custom.destroy');
        Route::get('/students/{student}/custom/{template}/download', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customDownload'])->name('custom.download');
        Route::post('/students/{student}/custom/{template}/issue', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'customIssue'])->name('custom.issue');

        // Upload Template
        Route::get('/students/{student}/upload', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'uploadView'])->name('custom.upload.view');
        Route::post('/students/{student}/upload', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'uploadTemplate'])->name('custom.upload');

        // Username Availability
        Route::get('/students/check-username/{username}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'checkUsername'])->name('students.check-username');

        // Email
        Route::post('/students/{student}/send-email/{templateNum}', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'sendEmail'])->name('email');
        Route::post('/students/{student}/custom/{template}/send-email', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'sendCustomEmail'])->name('custom.email');

        // Bulk Email
        Route::post('/bulk-send-email', [\App\Http\Controllers\Teacher\CertificateDesignerController::class, 'bulkSendEmail'])->name('bulk.email');
    });
});

// Default Routes
Route::middleware('auth')->get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect('/admin/users');
    } elseif ($user->role === 'teacher') {
        return redirect('/teacher');
    } else {
        return redirect('/student/dashboard');
    }
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::get('/notifications/{notification}/go', [NotificationController::class, 'redirect'])->name('notifications.goto');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/check-username', [\App\Http\Controllers\ProfileController::class, 'checkUsername'])->name('profile.check-username');
    Route::get('/profile/change-password', [\App\Http\Controllers\ProfileController::class, 'editPassword'])->name('profile.change-password');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/delete', function () {
        return view('profile.delete-account');
    })->name('profile.delete-account');
    Route::delete('/profile/delete', [\App\Http\Controllers\ProfileController::class, 'deleteAccount'])->name('profile.delete-account-confirm');

    Route::post('/activity/ping', function () {
        if (auth()->check()) {
            Cache::put('user-is-online-' . auth()->id(), true, now()->addSeconds(35));
            Cache::put('last-activity-' . auth()->id(), now(), now()->addDays(7));
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    })->middleware('throttle:10,1')->name('activity.ping');
});
