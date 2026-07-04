<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'badge_icon',
        'type',
        'requirement',
        'reward_points'
    ];

    /**
     * العلاقة مع المستخدمين الذين حصلوا على الإنجاز
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('achieved_at')
            ->withTimestamps();
    }

    /**
     * الحصول على جميع الإنجازات من نوع معين
     */
    public static function byType(string $type)
    {
        return self::where('type', $type)->get();
    }

    /**
     * فحص ما إذا حقق المستخدم هذا الإنجاز
     */
    public function checkAndAward(User $user): bool
    {
        // إذا كان المستخدم لديه الإنجاز بالفعل
        if ($user->achievements()->where('achievement_id', $this->id)->exists()) {
            return false;
        }

        try {
            $achieved = match ($this->type) {
                'points' => $user->points >= $this->requirement,
                'exams_passed' => ($user->leaderboard?->exams_passed ?? 0) >= $this->requirement,
                'smart_rewind_mastered' => ($user->leaderboard?->smart_rewinds_mastered ?? 0) >= $this->requirement,
                'consecutive_days' => ($user->leaderboard?->consecutive_days ?? 0) >= $this->requirement,
                default => false
            };

            if ($achieved) {
                $this->awardToUser($user);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * منح الإنجاز للمستخدم
     */
    public function awardToUser(User $user): void
    {
        if (!$user->achievements()->where('achievement_id', $this->id)->exists()) {
            $user->achievements()->attach($this->id);
            $user->increment('points', $this->reward_points);
        }
    }
}
