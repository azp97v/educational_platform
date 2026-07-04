<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\PdfGeneration;
use App\Jobs\GenerateCertificatePdfJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class CertificateController extends Controller
{
    /**
     * عرض جميع شهادات المستخدم الحالي
     */
    public function index()
    {
        $certificates = Certificate::getUserCertificates(Auth::id());

        return view('certificates.index', compact('certificates'));
    }

    /**
     * عرض تفاصيل شهادة واحدة
     */
    public function show(Certificate $certificate)
    {
        // التحقق من أن المستخدم الحالي هو مالك الشهادة أو admin
        if ($certificate->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return redirect()->route('certificate.index')->with('error', 'غير مصرح');
        }

        return view('certificates.show', compact('certificate'));
    }

    /**
     * إصدار شهادة للمستخدم
     */
    public function issue(Request $request)
    {
        // هذه الدالة يجب أن تكون محمية للمسؤولين فقط
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $certificate = Certificate::issueCertificate(
            $request->user_id,
            $request->course_id,
            $request->score
        );

        return response()->json([
            'message' => 'Certificate issued successfully',
            'certificate' => $certificate
        ]);
    }

    /**
     * التحقق من شهادة
     */
    public function verify(Request $request)
    {
        $request->validate(['certificate_number' => 'required']);

        $certificate = Certificate::verifyCertificate($request->certificate_number);

        if (!$certificate) {
            return view('certificates.verify-failed');
        }

        return view('certificates.verify-success', compact('certificate'));
    }

    /**
     * تحميل PDF للشهادة
     */
    public function downloadPDF(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // إطلاق Job في الخلفية بدلاً من التوليد المتزامن الذي يُجمّد السيرفر
        $token = bin2hex(random_bytes(24));

        // نخزّن جميع البيانات الضرورية في certificate_id عبر template_id
        $gen = PdfGeneration::create([
            'token'       => $token,
            'user_id'     => Auth::id(),
            'type'        => 'academic',
            'template_id' => $certificate->id,  // نستخدم template_id لحفظ ID الشهادة
            'status'      => 'pending',
            'expires_at'  => now()->addHours(3),
        ]);

        // نستخدم نفس الجوب لكن بنوع 'academic' لتوليد شهادة أكاديمية
        GenerateCertificatePdfJob::dispatch($gen->id);

        return redirect()->route('pdf.wait', ['token' => $token]);
    }

    public function viewPDF(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // view و download نفس المنطق — كلاهما عبر Queue
        $token = bin2hex(random_bytes(24));

        $gen = PdfGeneration::create([
            'token'       => $token,
            'user_id'     => Auth::id(),
            'type'        => 'academic',
            'template_id' => $certificate->id,
            'status'      => 'pending',
            'expires_at'  => now()->addHours(3),
        ]);

        GenerateCertificatePdfJob::dispatch($gen->id);

        return redirect()->route('pdf.wait', ['token' => $token]);
    }

    /**
     * API: الحصول على شهادات المستخدم
     */
    public function getUserCertificates(User $user)
    {
        if ($user->id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $certificates = Certificate::getUserCertificates($user->id);

        return response()->json(['certificates' => $certificates]);
    }

    /**
     * API: التحقق من شهادة
     */
    public function verifyCertificateAPI(Request $request)
    {
        $request->validate(['certificate_number' => 'required']);

        $certificate = Certificate::verifyCertificate($request->certificate_number);

        if (!$certificate) {
            return response()->json([
                'verified' => false,
                'message' => 'Certificate not found or invalid'
            ]);
        }

        return response()->json([
            'verified' => true,
            'certificate' => [
                'number' => $certificate->certificate_number,
                'user_name' => $certificate->user->name,
                'course_name' => $certificate->course->name,
                'score' => $certificate->score,
                'issued_at' => $certificate->issued_at->format('d/m/Y'),
                'is_expired' => $certificate->isExpired()
            ]
        ]);
    }
}
