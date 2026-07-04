<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'qr_code',
        'score',
        'issued_at',
        'expires_at',
        'pdf_url',
        'is_verified'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع الدورة
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * إنشاء شهادة جديدة
     */
    public static function issueCertificate(int $userId, int $courseId, float $score): self
    {
        // إنشاء رقم فريد للشهادة
        $certificateNumber = 'CERT-' . date('Ymd') . '-' . strtoupper(uniqid());
        
        $certificate = self::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'certificate_number' => $certificateNumber,
            'score' => $score,
            'is_verified' => true,
            'issued_at' => now(),
            'expires_at' => now()->addYears(2)
        ]);

        // إنشاء QR Code
        $certificate->generateQRCode();

        return $certificate;
    }

    /**
     * توليد رمز QR
     */
    public function generateQRCode(): void
    {
        try {
            $verificationUrl = route('certificate.verify', ['token' => $this->certificate_number]);
            
            $qrCode = new QrCode($verificationUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $dir = storage_path('app/public/qr_codes');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = $this->certificate_number . '.png';
            $fullPath = $dir . '/' . $filename;
            file_put_contents($fullPath, $result->getString());
            
            $this->update(['qr_code' => 'qr_codes/' . $filename]);
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
        }
    }

    /**
     * التحقق من صحة الشهادة
     */
    public static function verifyCertificate(string $certificateNumber): ?self
    {
        return self::where('certificate_number', $certificateNumber)
            ->where('is_verified', true)
            ->first();
    }

    /**
     * الحصول على جميع شهادات المستخدم
     */
    public static function getUserCertificates(int $userId)
    {
        return self::where('user_id', $userId)
            ->with('course')
            ->orderBy('issued_at', 'desc')
            ->get();
    }

    /**
     * فحص انتهاء الشهادة
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * تحديث حالة التحقق
     */
    public function invalidate(): void
    {
        $this->update(['is_verified' => false]);
    }
}
