<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Console\Command;

class SeedGamification extends Command
{
    protected $signature = 'gamification:seed';
    protected $description = 'Seed achievements and leaderboards';

    public function handle()
    {
        $achievements = [
            ['name' => 'بداية التعلم', 'description' => 'ابدأ رحلتك التعليمية في إجلال', 'badge_icon' => '🎓', 'type' => 'points', 'requirement' => 10, 'reward_points' => 25],
            ['name' => 'جامع نقاط', 'description' => 'اجمع 100 نقطة', 'badge_icon' => '💎', 'type' => 'points', 'requirement' => 100, 'reward_points' => 50],
            ['name' => 'الطالب المجتهد', 'description' => 'أكمل 5 اختبارات بنجاح', 'badge_icon' => '📚', 'type' => 'exams_passed', 'requirement' => 5, 'reward_points' => 75],
            ['name' => 'المتعلم المستمر', 'description' => 'تعلم 7 أيام متتالية', 'badge_icon' => '🔥', 'type' => 'consecutive_days', 'requirement' => 7, 'reward_points' => 100],
            ['name' => 'خبير Smart Rewind', 'description' => 'أتقن 3 مهارات باستخدام Smart Rewind', 'badge_icon' => '⚡', 'type' => 'smart_rewind_mastered', 'requirement' => 3, 'reward_points' => 60],
        ];

        foreach ($achievements as $data) {
            Achievement::firstOrCreate(['name' => $data['name']], $data);
            $this->info("Created achievement: {$data['name']}");
        }

        $students = User::where('role', 'student')->get();
        foreach ($students as $student) {
            Leaderboard::firstOrCreate(
                ['user_id' => $student->id],
                ['total_points' => $student->points ?? 0, 'rank' => 999]
            );
            $this->info("Created leaderboard for: {$student->name}");
        }

        $this->info('✅ Gamification data seeded successfully!');
    }
}
