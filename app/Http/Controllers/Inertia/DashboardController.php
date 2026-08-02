<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function student(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $cacheKey = "student_dashboard_{$user->id}";
        $data = Cache::remember($cacheKey, 60, function () use ($user) {
            $enrolledCourses = $user->enrolledCourses()
                ->where('courses.status', 'published')
                ->whereNull('courses.deleted_at')
                ->withCount(['lessons' => fn ($q) => $q->withoutGlobalScopes()])
                ->get()
                ->map(fn ($c) => [
                    'id'            => $c->id,
                    'title'         => $c->name,
                    'lessons_count' => $c->lessons_count,
                    'progress'      => 0,
                ]);

            $availableCourses = Course::where('status', 'published')
                ->whereNull('deleted_at')
                ->whereNotIn('id', $enrolledCourses->pluck('id'))
                ->withCount(['lessons' => fn ($q) => $q->withoutGlobalScopes()])
                ->latest()
                ->limit(6)
                ->get()
                ->map(fn ($c) => [
                    'id'            => $c->id,
                    'title'         => $c->name,
                    'lessons_count' => $c->lessons_count,
                ]);

            $streak = $user->streak?->current_streak ?? 0;
            $points = $user->points ?? 0;

            $completedLessons = DB::table('user_progress')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->count();

            $certificates = DB::table('certificates')
                ->where('user_id', $user->id)
                ->count();

            $totalMinutes = 0;

            return compact('enrolledCourses', 'availableCourses', 'streak', 'points', 'completedLessons', 'certificates', 'totalMinutes');
        });

        return Inertia::render('Dashboard/Student', [
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar_url,
            ],
            'stats' => [
                'active_courses'    => $data['enrolledCourses']->count(),
                'points'            => $data['points'],
                'streak'            => $data['streak'],
                'certificates'      => $data['certificates'],
                'completed_lessons' => $data['completedLessons'],
                'total_minutes'     => $data['totalMinutes'],
            ],
            'enrolledCourses'  => $data['enrolledCourses'],
            'availableCourses' => $data['availableCourses'],
            'recentActivity'   => [],
        ]);
    }

    public function teacher(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $cacheKey = "teacher_dashboard_{$user->id}";
        $data = Cache::remember($cacheKey, 60, function () use ($user) {
            $courses = Course::where('instructor_id', $user->id)
                ->whereNull('deleted_at')
                ->withCount([
                    'lessons'                      => fn ($q) => $q->withoutGlobalScopes(),
                    'enrolledStudents as students_count',
                ])
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn ($c) => [
                    'id'             => $c->id,
                    'title'          => $c->name,
                    'status'         => $c->status,
                    'lessons_count'  => $c->lessons_count,
                    'students_count' => $c->students_count,
                ]);

            $totalStudents = DB::table('course_enrollments')
                ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->where('courses.instructor_id', $user->id)
                ->where('course_enrollments.status', 'approved')
                ->count();

            $pendingRequests = DB::table('course_enrollments')
                ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->join('users', 'course_enrollments.user_id', '=', 'users.id')
                ->where('courses.instructor_id', $user->id)
                ->where('course_enrollments.status', 'pending')
                ->select('course_enrollments.id', 'users.name as student_name', 'courses.name as course_title')
                ->limit(10)
                ->get()
                ->toArray();

            $totalExams = DB::table('exams')
                ->join('lessons', 'exams.lesson_id', '=', 'lessons.id')
                ->join('courses', 'lessons.course_id', '=', 'courses.id')
                ->where('courses.instructor_id', $user->id)
                ->count();

            $certificates = DB::table('certificates')
                ->join('courses', 'certificates.course_id', '=', 'courses.id')
                ->where('courses.instructor_id', $user->id)
                ->count();

            $avgCompletion = 0;

            return compact('courses', 'totalStudents', 'pendingRequests', 'totalExams', 'certificates', 'avgCompletion');
        });

        return Inertia::render('Dashboard/Teacher', [
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar_url,
            ],
            'stats' => [
                'total_courses'      => $data['courses']->count(),
                'total_students'     => $data['totalStudents'],
                'pending_requests'   => count($data['pendingRequests']),
                'total_exams'        => $data['totalExams'],
                'certificates_issued'=> $data['certificates'],
                'avg_completion'     => $data['avgCompletion'],
            ],
            'recentCourses'    => $data['courses'],
            'pendingRequests'  => $data['pendingRequests'],
            'recentActivity'   => [],
        ]);
    }

    public function admin(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $stats = Cache::remember('admin_dashboard_stats', 120, function () {
            return [
                'total_users'   => User::count(),
                'students'      => User::where('role', 'student')->count(),
                'teachers'      => User::where('role', 'teacher')->count(),
                'total_courses' => Course::whereNull('deleted_at')->count(),
                'total_exams'   => DB::table('exams')->count(),
                'certificates'  => DB::table('certificates')->count(),
                'db_ok'         => true,
                'redis_ok'      => true,
                'horizon_ok'    => true,
                'storage_ok'    => true,
            ];
        });

        $recentUsers = User::latest()->limit(8)->get()->map(fn ($u) => [
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
            'role'  => $u->role,
        ]);

        $recentCourses = Course::whereNull('deleted_at')
            ->with('instructor:id,name')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($c) => [
                'id'              => $c->id,
                'title'           => $c->name,
                'status'          => $c->status,
                'instructor_name' => $c->instructor?->name,
            ]);

        return Inertia::render('Dashboard/Admin', [
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar_url,
            ],
            'stats'         => $stats,
            'recentUsers'   => $recentUsers,
            'recentCourses' => $recentCourses,
        ]);
    }
}
