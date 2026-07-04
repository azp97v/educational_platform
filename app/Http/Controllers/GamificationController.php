<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Leaderboard;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    /**
     * عرض لوحة المتصدرين العامة
     */
    public function leaderboard()
    {
        $topPlayers = Leaderboard::getTopPlayers(20);
        $currentUserRank = null;

        if (Auth::check()) {
            $currentUserRank = Leaderboard::getUserRank(Auth::id(), 7);
        }

        return view('gamification.leaderboard', compact('topPlayers', 'currentUserRank'));
    }

    /**
     * عرض إحصائيات المستخدم
     */
    public function userStats(User $user)
    {
        $stats = Leaderboard::getUserStats($user->id);
        $achievements = $user->achievements()->withPivot('achieved_at')->get();
        
        return view('gamification.user-stats', compact('user', 'stats', 'achievements'));
    }

    /**
     * عرض جميع الإنجازات
     */
    public function achievements()
    {
        $allAchievements = Achievement::all();
        $userAchievements = [];
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $userAchievements = $user->achievements()->pluck('achievement_id')->toArray();
        }

        return view('gamification.achievements', compact('allAchievements', 'userAchievements'));
    }

    /**
     * عرض تفاصيل إنجاز واحد
     */
    public function achievementDetail(Achievement $achievement)
    {
        $userHasAchievement = false;
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $userHasAchievement = $user->achievements()->where('achievement_id', $achievement->id)->exists();
        }

        $usersWithAchievement = $achievement->users()
            ->with('leaderboard')
            ->orderBy('user_achievements.achieved_at', 'desc')
            ->limit(10)
            ->get();

        return view('gamification.achievement-detail', compact(
            'achievement',
            'userHasAchievement',
            'usersWithAchievement'
        ));
    }

    /**
     * إحصائيات شخصية (Dashboard)
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $stats = Leaderboard::getUserStats($user->id);
        $achievements = $user->achievements()->get();
        $recentAchievements = $user->achievements()
            ->orderBy('user_achievements.achieved_at', 'desc')
            ->limit(5)
            ->get();

        return view('gamification.dashboard', compact(
            'user',
            'stats',
            'achievements',
            'recentAchievements'
        ));
    }

    /**
     * مقارنة بين صديقين
     */
    public function compare(User $user1, User $user2)
    {
        $stats1 = Leaderboard::getUserStats($user1->id);
        $stats2 = Leaderboard::getUserStats($user2->id);

        return view('gamification.compare', compact('user1', 'user2', 'stats1', 'stats2'));
    }

    /**
     * API: الحصول على إحصائيات المستخدم الحالي
     */
    public function getCurrentUserStats()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = Leaderboard::getUserStats(Auth::id());
        
        return response()->json($stats);
    }

    /**
     * API: الحصول على الإنجازات المتاحة
     */
    public function getAvailableAchievements()
    {
        $achievements = Achievement::all();

        return response()->json([
            'count' => $achievements->count(),
            'achievements' => $achievements
        ]);
    }

    /**
     * API: تحديث ترتيب لوحة المتصدرين
     */
    public function updateLeaderboard()
    {
        Leaderboard::updateRank();

        return response()->json([
            'message' => 'Leaderboard updated successfully',
            'timestamp' => now()
        ]);
    }
}
