<?php

namespace App\Http\Controllers;

use App\Models\PdfGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    /**
     * صفحة الانتظار — تُعرض للمستخدم بعد طلب التنزيل
     */
    public function waitPage(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            abort(404);
        }

        $gen = PdfGeneration::where('token', $token)
            ->where('user_id', Auth::id())
            ->first();

        if (!$gen) {
            abort(404, 'طلب التنزيل غير موجود أو منتهي الصلاحية');
        }

        // إذا كان جاهزاً — تحويل فوري للتنزيل
        if ($gen->isDone()) {
            return redirect()->route('pdf.download', ['token' => $token]);
        }

        if ($gen->status === 'failed') {
            return view('pdf.wait', [
                'gen'    => $gen,
                'token'  => $token,
                'failed' => true,
            ]);
        }

        $referrer = url()->previous(route('student.index')); // رابط العودة

        return view('pdf.wait', [
            'gen'      => $gen,
            'token'    => $token,
            'failed'   => false,
            'referrer' => $referrer,
        ]);
    }

    /**
     * API للتحقق من حالة الـ Job — وإذا كان الوركر لا يعمل، يُنفّذ الـ Job مباشرة
     */
    public function statusCheck(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            return response()->json(['error' => 'missing token'], 400);
        }

        $gen = PdfGeneration::where('token', $token)
            ->where('user_id', Auth::id())
            ->first();

        if (!$gen) {
            return response()->json(['status' => 'not_found', 'progress' => 0], 404);
        }

        // إذا كان لا يزال pending لأكثر من 3 ثوان، نُنفّذه inline
        // (يحدث عندما لا يكون queue worker يعمل)
        if ($gen->status === 'pending' && $gen->created_at->diffInSeconds(now()) > 3) {
            Log::info('[PDF-CTRL] Inline processing for token=' . $token);
            try {
                (new \App\Jobs\GenerateCertificatePdfJob($gen->id))->handle();
                $gen->refresh(); // تحديث البيانات
            } catch (\Throwable $e) {
                Log::error('[PDF-CTRL] Inline process failed: ' . $e->getMessage());
                $gen->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                $gen->refresh();
            }
        }

        return response()->json([
            'status'       => $gen->status,
            'progress'     => $gen->progressPercent(),
            'download_url' => $gen->isDone()
                ? route('pdf.download', ['token' => $token])
                : null,
            'error'        => $gen->error_message,
        ]);
    }

    /**
     * تنزيل الملف الجاهز
     */
    public function download(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            abort(404);
        }

        $gen = PdfGeneration::where('token', $token)
            ->where('user_id', Auth::id())
            ->first();

        if (!$gen) {
            abort(404, 'طلب التنزيل غير موجود أو منتهي الصلاحية');
        }

        if (!$gen->isDone()) {
            // لم يجتهز بعد — نُعيد توجيه لصفحة الانتظار
            return redirect()->route('pdf.wait', ['token' => $token]);
        }

        $filePath = $gen->file_path;
        $fileName = $gen->file_name ?: 'certificate.pdf';

        Log::info('[PDF-CTRL] Download: token=' . $token . ' file=' . $filePath);

        return response()->download($filePath, $fileName, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ])->deleteFileAfterSend(false); // نحتفظ بالملف للتنزيل مرة أخرى
    }
}
