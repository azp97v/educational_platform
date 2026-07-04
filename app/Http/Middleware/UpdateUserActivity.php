<?php

namespace App\Http\Middleware;

use App\Services\StreakService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Cache::put('user-is-online-' . Auth::id(), true, now()->addSeconds(3));
            Cache::put('last-activity-' . Auth::id(), now(), now()->addDays(7));
        }

        if (Auth::check() && Auth::user()->role === 'student') {
            $streakService = new StreakService();
            $streakService->updateStreak(Auth::user(), 0);
        }

        return $next($request);
    }
}
