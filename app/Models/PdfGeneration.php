<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfGeneration extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'type',
        'student_id',
        'template_id',
        'template_num',
        'status',
        'file_path',
        'file_name',
        'error_message',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /** هل الملف جاهز للتنزيل؟ */
    public function isDone(): bool
    {
        return $this->status === 'done'
            && $this->file_path
            && file_exists($this->file_path);
    }

    /** هل انتهت صلاحية الجلسة؟ */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /** نسبة اكتمال مبسّطة للـ frontend */
    public function progressPercent(): int
    {
        return match ($this->status) {
            'pending'    => 10,
            'processing' => 60,
            'done'       => 100,
            'failed'     => 0,
            default      => 0,
        };
    }
}
