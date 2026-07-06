<?php

namespace App\Jobs;

use App\Models\PdfGeneration;
use App\Models\Certificates\CertificateStudent;
use App\Models\Certificates\CustomTemplate;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** الحد الأقصى لمحاولات التنفيذ */
    public int $tries = 1;

    /** مهلة التنفيذ بالثواني */
    public int $timeout = 120;

    public function __construct(
        private readonly int    $pdfGenerationId,
    ) {}

    public function handle(): void
    {
        // تجاوز حدود PCRE لتجنب فشل mPDF مع الصور الضخمة
        ini_set('pcre.backtrack_limit', '50000000');
        ini_set('pcre.recursion_limit', '50000000');

        /** @var PdfGeneration $gen */
        $gen = PdfGeneration::find($this->pdfGenerationId);
        if (!$gen) {
            Log::warning('[PDF-JOB] PdfGeneration not found: ' . $this->pdfGenerationId);
            return;
        }

        // ضمان وجود مجلد التخزين المؤقت
        $pdfTempDir  = storage_path('app/pdf_temp');
        $pdfStoreDir = storage_path('app/pdf_store');
        foreach ([$pdfTempDir, $pdfStoreDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $gen->update(['status' => 'processing']);

        try {
            if ($gen->type === 'preset') {
                $this->generatePreset($gen, $pdfTempDir, $pdfStoreDir);
            } elseif ($gen->type === 'custom') {
                $this->generateCustom($gen, $pdfTempDir, $pdfStoreDir);
            } elseif ($gen->type === 'academic') {
                $this->generateAcademic($gen, $pdfTempDir, $pdfStoreDir);
            } else {
                throw new \RuntimeException('Unknown PDF type: ' . $gen->type);
            }
        } catch (\Throwable $e) {
            Log::error('[PDF-JOB] Failed: ' . $e->getMessage(), [
                'pdf_generation_id' => $gen->id,
                'type'              => $gen->type,
                'file'              => $e->getFile(),
                'line'              => $e->getLine(),
            ]);
            $gen->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Preset Templates (qw1.jpeg ... qw9.jpeg)
    // ─────────────────────────────────────────────────────────────

    private function generatePreset(PdfGeneration $gen, string $pdfTempDir, string $pdfStoreDir): void
    {
        $student = CertificateStudent::find($gen->student_id);
        if (!$student) {
            throw new \RuntimeException('CertificateStudent not found: ' . $gen->student_id);
        }
        $teacher     = \App\Models\User::find($gen->user_id);
        $teacherName = $teacher?->name ?? 'المعهد';
        $templateNum = $gen->template_num;
        $imageName   = 'qw' . $templateNum . '.jpeg';
        $imagePath   = public_path('image/' . $imageName);

        Log::info('[PDF-JOB] generatePreset: template=' . $templateNum . ' student=' . $gen->student_id);

        // mPDF يقرأ الملف مباشرة بالمسار — أسرع وأقل ذاكرة من base64
        $backgroundImageForMpdf = file_exists($imagePath) ? $imagePath : '';

        $html = view('teacher.certificates.pdf.download', [
            'student'         => $student,
            'teacherName'     => $teacherName,
            'backgroundImage' => $backgroundImageForMpdf,
        ])->render();

        Log::info('[PDF-JOB] HTML rendered: ' . strlen($html) . ' bytes');

        $mpdf = $this->buildMpdf($pdfTempDir);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        $fileName = 'certificate_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $student->name) . '_' . time() . '.pdf';
        $filePath = $pdfStoreDir . DIRECTORY_SEPARATOR . $fileName;

        $mpdf->Output($filePath, 'F'); // حفظ في ملف

        $gen->update([
            'status'    => 'done',
            'file_path' => $filePath,
            'file_name' => "certificate_{$student->name}.pdf",
            'expires_at'=> now()->addHours(2),
        ]);

        Log::info('[PDF-JOB] Preset PDF saved: ' . $filePath);
    }

    // ─────────────────────────────────────────────────────────────
    // Custom Templates
    // ─────────────────────────────────────────────────────────────

    private function generateCustom(PdfGeneration $gen, string $pdfTempDir, string $pdfStoreDir): void
    {
        $student  = CertificateStudent::findOrFail($gen->student_id);
        $template = CustomTemplate::findOrFail($gen->template_id);

        Log::info('[PDF-JOB] generateCustom: template_id=' . $gen->template_id . ' student_id=' . $gen->student_id);

        // تحميل صورة الخلفية بمسارها المباشر وليس base64 (لمنع انهيار mPDF وتوليد 48 صفحة نصية!)
        $pdfBgImage = null;
        if ($template->background_type === 'image' && $template->background_image) {
            $bgPath = storage_path('app/public/' . $template->background_image);
            if (file_exists($bgPath)) {
                $pdfBgImage = str_replace('\\', '/', $bgPath);
            }
        }

        // تحميل صورة الشعار بمسارها المباشر
        $logoBase64 = null;
        if ($template->show_logo && $template->logo_image) {
            $logoPath = storage_path('app/public/' . $template->logo_image);
            if (file_exists($logoPath)) {
                $logoBase64 = str_replace('\\', '/', $logoPath);
            }
        }
        if (!$logoBase64) {
            // الشعار الافتراضي
            $defaultLogo = public_path('image/logono.png');
            if (file_exists($defaultLogo)) {
                $logoBase64 = str_replace('\\', '/', $defaultLogo);
            }
        }

        $html = view('teacher.certificates.custom-preview', [
            'student'    => $student,
            'template'   => $template,
            'preview'    => false,
            'forPdf'     => true,
            'pdfBgImage' => $pdfBgImage,
            'logoBase64' => $logoBase64,
        ])->render();

        Log::info('[PDF-JOB] Custom HTML rendered: ' . strlen($html) . ' bytes');

        $mpdf = $this->buildMpdf($pdfTempDir);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        $fileName = 'custom_certificate_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $student->name) . '_' . time() . '.pdf';
        $filePath = $pdfStoreDir . DIRECTORY_SEPARATOR . $fileName;

        $mpdf->Output($filePath, 'F');

        $gen->update([
            'status'    => 'done',
            'file_path' => $filePath,
            'file_name' => "custom_certificate_{$student->name}.pdf",
            'expires_at'=> now()->addHours(2),
        ]);

        Log::info('[PDF-JOB] Custom PDF saved: ' . $filePath);
    }

    // ─────────────────────────────────────────────────────────────
    // Academic Certificates (from certificates table)
    // ─────────────────────────────────────────────────────────────

    private function generateAcademic(PdfGeneration $gen, string $pdfTempDir, string $pdfStoreDir): void
    {
        // template_id يحمل certificate->id في حالة academic
        $certificate = Certificate::with('user', 'course')->findOrFail($gen->template_id);

        Log::info('[PDF-JOB] generateAcademic: cert_id=' . $certificate->id);

        $html = view('certificates.pdf', compact('certificate'))->render();
        Log::info('[PDF-JOB] Academic HTML rendered: ' . strlen($html) . ' bytes');

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4-L',
            'default_font'  => 'dejavusans',
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir'       => $pdfTempDir,
            'curlTimeout'   => 3,
        ]);

        $mpdf->setAutoTopMargin    = false;
        $mpdf->useSubstitutions    = false;
        $mpdf->ignore_invalid_utf8 = true;

        $mpdf->WriteHTML($html);

        $certNum  = $certificate->certificate_number ?? $certificate->id;
        $fileName = 'certificate_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $certNum) . '_' . time() . '.pdf';
        $filePath = $pdfStoreDir . DIRECTORY_SEPARATOR . $fileName;

        $mpdf->Output($filePath, 'F');

        $gen->update([
            'status'    => 'done',
            'file_path' => $filePath,
            'file_name' => 'certificate-' . $certNum . '.pdf',
            'expires_at'=> now()->addHours(2),
        ]);

        Log::info('[PDF-JOB] Academic PDF saved: ' . $filePath);
    }

    // ─────────────────────────────────────────────────────────────
    // mPDF Builder — إعدادات مشتركة وآمنة
    // ─────────────────────────────────────────────────────────────


    private function buildMpdf(string $pdfTempDir): Mpdf
    {
        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4-L',
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir'       => $pdfTempDir,
            // منع mPDF من محاولة تنزيل أي شيء من الإنترنت
            'curlTimeout'   => 3,
            'curlFollowLocation' => false,
        ]);

        // تعطيل الميزات التي تحتاج شبكة أو تُثقل الذاكرة
        $mpdf->setAutoTopMargin        = false;
        $mpdf->useSubstitutions        = false;
        $mpdf->simpleTables            = false;
        $mpdf->packTableData           = true;
        $mpdf->ignore_invalid_utf8     = true;

        return $mpdf;
    }

    /** عند فشل الـ Job يُسجّل الخطأ */
    public function failed(\Throwable $exception): void
    {
        Log::error('[PDF-JOB] Job failed permanently: ' . $exception->getMessage());
        PdfGeneration::where('id', $this->pdfGenerationId)
            ->where('status', '!=', 'done')
            ->update([
                'status'        => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
    }
}
