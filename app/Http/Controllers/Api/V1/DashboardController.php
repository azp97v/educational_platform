<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('enrollments.course');

        $courses = $user->enrollments->map(fn ($e) => [
            'id'       => $e->course->id,
            'title'    => $e->course->title,
            'progress' => $e->progress ?? 0,
        ]);

        return response()->json([
            'user'   => ['id' => $user->id, 'name' => $user->name, 'role' => $user->role],
            'stats'  => [
                'active_courses' => $user->enrollments->where('progress', '<', 100)->count(),
                'points'         => $user->points ?? 0,
                'certificates'   => $user->certificates()->count(),
                'streak'         => $user->streak_days ?? 0,
            ],
            'courses' => $courses,
        ]);
    }
}
