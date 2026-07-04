<?php

namespace App\Services;

use App\Models\User;
use App\Models\Streak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StreakService
{
    /**
     * تحديث أو إنشاء سجل الأيام المتواصلة للمستخدم
     * يتم استدعاؤه عند كل نشاط
     */
    public function updateStreak(User $user, int $pointsToAdd = 0): Streak
    {
        // firstOrCreate يتجنب سباق الإنشاء عند الطلبات المتزامنة لنفس المستخدم.
        $streak = $user->streak ?? Streak::firstOrCreate(
            ['user_id' => $user->id],
            [
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_points' => 0,
                'last_accessed_at' => now(),
            ]
        );

        $lastAccess = $streak->last_accessed_at ? Carbon::parse($streak->last_accessed_at) : null;
        $today = Carbon::today();
        $lastAccessDate = $lastAccess ? $lastAccess->toDateString() : null;
        $todayDate = $today->toDateString();

        // إذا كان آخر وصول اليوم، لا نزيد الـ streak (تم التفاعل مرة واحدة فقط يومياً)
        if ($lastAccessDate === $todayDate) {
            // نقوم فقط بإضافة النقاط
            $streak->increment('total_points', $pointsToAdd);
            return $streak;
        }

        // إذا كان آخر وصول في الأمس، نزيد الـ streak
        $yesterday = Carbon::yesterday()->toDateString();
        if ($lastAccessDate === $yesterday) {
            $newStreak = $streak->current_streak + 1;
            $streak->update([
                'current_streak' => $newStreak,
                'longest_streak' => max($newStreak, $streak->longest_streak),
                'total_points' => $streak->total_points + $pointsToAdd,
                'last_accessed_at' => now(),
            ]);
            return $streak;
        }

        // إذا كان آخر وصول أكثر من يوم، نأخذ في الاعتبار قطع الـ streak
        // وننجح في الـ streak الجديد
        $streak->update([
            'current_streak' => 1,
            'total_points' => $streak->total_points + $pointsToAdd,
            'last_accessed_at' => now(),
        ]);

        return $streak;
    }

    /**
     * الحصول على الـ streak الحالي للمستخدم
     */
    public function getCurrentStreak(User $user): int
    {
        $streak = $user->streak;
        
        if (!$streak) {
            return 0;
        }

        $lastAccess = $streak->last_accessed_at ? Carbon::parse($streak->last_accessed_at) : null;
        if (!$lastAccess) {
            return 0;
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // إذا كان آخر وصول اليوم أو الأمس، نرجع الـ current_streak
        if ($lastAccess->toDateString() === $today->toDateString() || 
            $lastAccess->toDateString() === $yesterday->toDateString()) {
            return $streak->current_streak;
        }

        // إذا كان آخر وصول أكثر من يوم، الـ streak منقطع
        return 0;
    }

    /**
     * الحصول على إجمالي النقاط للمستخدم
     */
    public function getTotalPoints(User $user): int
    {
        return $user->points ?? 0;
    }

    /**
     * إضافة نقاط للمستخدم وتحديث الـ streak
     */
    public function addPoints(User $user, int $points, string $reason = 'activity'): array
    {
        // تغليف تحديث النقاط + الـ streak + لوحة المتصدرين في معاملة واحدة حتى لا
        // يحدث أن تُزاد النقاط بينما يفشل تحديث الـ streak أو اللوحة (أو العكس).
        $streak = DB::transaction(function () use ($user, $points) {
            // تحديث النقاط في جدول users
            $user->increment('points', $points);

            // تحديث الـ streak
            $streak = $this->updateStreak($user, $points);

            // تحديث سجل لوحة المتصدرين بشكل ذري
            $this->syncLeaderboard($user, $points);

            return $streak;
        });

        return [
            'user_points' => $user->fresh()->points,
            'current_streak' => $this->getCurrentStreak($user->fresh()),
            'total_earned' => $streak->total_points,
            'reason' => $reason,
        ];
    }

    /**
     * تحديث سجل لوحة المتصدرين للمستخدم بطريقة آمنة ضد التزامن.
     * نضمن أولاً وجود السجل (updateOrInsert)، ثم نزيد النقاط عبر عملية
     * UPDATE ذرية واحدة (total_points = total_points + ?) حتى لا تضيع أي نقاط
     * حتى لو قدّم 100 طالب اختباراتهم في نفس اللحظة.
     */
    protected function syncLeaderboard(User $user, int $points): void
    {
        DB::table('leaderboards')->updateOrInsert(
            ['user_id' => $user->id],
            ['updated_at' => now()]
        );

        DB::table('leaderboards')
            ->where('user_id', $user->id)
            ->update([
                'total_points' => DB::raw('total_points + ' . (int) $points),
                'last_activity_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * إعادة تعيين الـ streak اليومي (يُستدعى تلقائياً عند منتصف الليل)
     */
    public function checkAndResetDailyStreaks(): void
    {
        $streaks = Streak::all();

        foreach ($streaks as $streak) {
            if (!$streak->last_accessed_at) {
                continue;
            }

            $lastAccess = Carbon::parse($streak->last_accessed_at);
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            // إذا لم يكن هناك نشاط منذ أكثر من يوم
            if ($lastAccess->toDateString() !== $today->toDateString() && 
                $lastAccess->toDateString() !== $yesterday->toDateString()) {
                // قطع الـ streak
                $streak->update(['current_streak' => 0]);
            }
        }
    }

    /**
     * الحصول على بيانات الـ streak الكاملة
     */
    public function getStreakData(User $user): array
    {
        $streak = $user->streak ?? Streak::where('user_id', $user->id)->first();

        if (!$streak) {
            return [
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_points' => 0,
                'last_accessed_at' => null,
            ];
        }

        return [
            'current_streak' => $this->getCurrentStreak($user),
            'longest_streak' => $streak->longest_streak ?? 0,
            'total_points' => $streak->total_points ?? 0,
            'last_accessed_at' => $streak->last_accessed_at,
        ];
    }
}
