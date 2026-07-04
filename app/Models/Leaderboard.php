<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leaderboard extends Model
{
    protected $fillable = [
        'user_id',
        'total_points',
        'exams_passed',
        'consecutive_days',
        'smart_rewinds_mastered',
        'rank',
        'last_activity_at'
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديث النقاط
     */
    public function addPoints(int $points): void
    {
        $this->increment('total_points', $points);
        $this->touch('last_activity_at');
        $this->updateRank();
    }

    /**
     * تحديث عدد الاختبارات المنجزة
     */
    public function incrementExamsPassed(): void
    {
        $this->increment('exams_passed');
        $this->touch('last_activity_at');
    }

    /**
     * تحديث أيام متتالية
     */
    public function updateConsecutiveDays(): void
    {
        $lastActivity = $this->last_activity_at;
        
        if (!$lastActivity || $lastActivity->diffInDays(now()) == 1) {
            $this->increment('consecutive_days');
        } elseif ($lastActivity->diffInDays(now()) > 1) {
            $this->update(['consecutive_days' => 1]);
        }
        
        $this->touch('last_activity_at');
    }

    /**
     * تحديث عدد Smart Rewinds المتقنة
     */
    public function incrementSmartRewindsMastered(): void
    {
        $this->increment('smart_rewinds_mastered');
        $this->touch('last_activity_at');
    }

    /**
     * تحديث الترتيب
     */
    public static function updateRank(): void
    {
        // تغليف إعادة الترتيب الكاملة في معاملة واحدة حتى لا يرى أي قارئ
        // ترتيباً نصف محدّث، ولتسلسل الاستدعاءات المتزامنة.
        \DB::transaction(function () {
            $leaderboards = self::orderBy('total_points', 'desc')
                ->lockForUpdate()
                ->get(['id']);

            foreach ($leaderboards as $index => $leaderboard) {
                self::where('id', $leaderboard->id)->update(['rank' => $index + 1]);
            }
        });
    }

    /**
     * الحصول على أعلى 10 لاعبين
     */
    public static function getTopPlayers(int $limit = 10)
    {
        return self::with('user')
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على ترتيب المستخدم بين أصدقائه
     */
    public static function getUserRank(int $userId, int $limit = 5)
    {
        $user = self::where('user_id', $userId)->first();
        if (!$user) {
            return [];
        }

        $proximity = $limit / 2;
        $rankStart = max(1, $user->rank - $proximity);
        
        return self::with('user')
            ->whereBetween('rank', [$rankStart, $rankStart + $limit - 1])
            ->orderBy('rank')
            ->get();
    }

    /**
     * الحصول على إحصائيات المستخدم
     */
    public static function getUserStats(int $userId): array
    {
        $leaderboard = self::where('user_id', $userId)->first();
        
        if (!$leaderboard) {
            return [
                'total_points' => 0,
                'rank' => 'N/A',
                'exams_passed' => 0,
                'consecutive_days' => 0,
                'smart_rewinds_mastered' => 0,
            ];
        }

        return [
            'total_points' => $leaderboard->total_points,
            'rank' => $leaderboard->rank,
            'exams_passed' => $leaderboard->exams_passed,
            'consecutive_days' => $leaderboard->consecutive_days,
            'smart_rewinds_mastered' => $leaderboard->smart_rewinds_mastered,
            'last_activity_at' => $leaderboard->last_activity_at?->diffForHumans(),
        ];
    }
}
