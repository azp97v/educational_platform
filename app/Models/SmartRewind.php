<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartRewind extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'exam_id',
        'video_timestamp',
        'explanation',
        'watch_count',
        'status'
    ];

    protected $casts = [
        'video_timestamp' => 'integer',
        'watch_count' => 'integer',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع السؤال
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * علاقة مع الاختبار
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * تحديث الحالة إلى "watched"
     */
    public function markAsWatched(): void
    {
        $this->update([
            'status' => 'watched',
            'watch_count' => $this->watch_count + 1
        ]);
    }

    /**
     * تحديث الحالة إلى "mastered"
     */
    public function markAsMastered(): void
    {
        $this->update(['status' => 'mastered']);
    }

    /**
     * الحصول على الأسئلة المتبقية في نفس الاختبار
     */
    public function getRelatedQuestions()
    {
        return Question::where('exam_id', $this->exam_id)
            ->where('id', '!=', $this->question_id)
            ->orderBy('order')
            ->get();
    }

    /**
     * إنشاء Smart Rewind تلقائياً عند الإجابة الخاطئة
     */
    public static function createFromWrongAnswer(
        int $userId,
        int $questionId,
        int $examId,
        int $videoTimestamp,
        string $explanation
    ): self {
        return self::create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'exam_id' => $examId,
            'video_timestamp' => $videoTimestamp,
            'explanation' => $explanation,
            'status' => 'pending'
        ]);
    }

    /**
     * الحصول على ملخص Smart Rewinds للطالب
     */
    public static function getUserSummary(int $userId)
    {
        return self::where('user_id', $userId)
            ->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->get();
    }
}
