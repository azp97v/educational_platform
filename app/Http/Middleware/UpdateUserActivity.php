<?php

namespace App\Http\Middleware;

use App\Services\StreakService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $uid = Auth::id();
            Cache::put('user-is-online-' . $uid, true, now()->addSeconds(3));
            Cache::put('last-activity-' . $uid, now(), now()->addDays(7));

            // Write to DB at most once every 5 minutes per user (avoids N writes per page)
            if (!Cache::has('activity-db-' . $uid)) {
                Cache::put('activity-db-' . $uid, true, now()->addMinutes(5));
                DB::table('users')->where('id', $uid)->update(['last_activity_at' => now()]);
            }
        }

        // Throttle streak updates: at most once per 10 minutes per student
        if (Auth::check() && Auth::user()->role === 'student') {
            $streakKey = 'streak-checked-' . Auth::id();
            if (!Cache::has($streakKey)) {
                Cache::put($streakKey, true, now()->addMinutes(10));
                $streakService = new StreakService();
                $streakService->updateStreak(Auth::user(), 0);
            }
        }

        return $next($request);
    }
}
