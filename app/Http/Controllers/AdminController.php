<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function safeTableCount(string $table): int
    {
        return Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
    }

    private function safeConditionalCount(string $table, callable $queryBuilder): int
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        return (int) $queryBuilder(DB::table($table))->count();
    }

    private function baseStats(): array
    {
        return [
            'totalUsers' => User::count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'activeUsers' => $this->safeConditionalCount('users', fn ($q) => $q->where('status', 'active')),
            'totalCourses' => Course::count(),
            'totalLessons' => Lesson::count(),
            'totalExams' => $this->safeTableCount('exams'),
            'totalMessages' => $this->safeTableCount('messages'),
            'totalInquiries' => $this->safeTableCount('student_inquiries'),
            'totalCertificates' => $this->safeTableCount('certificates'),
            'totalRewinds' => $this->safeTableCount('smart_rewinds'),
            'pendingEnrollments' => $this->safeConditionalCount('course_enrollments', fn ($q) => $q->where('status', 'pending')),
            'openTickets' => $this->safeConditionalCount('support_tickets', fn ($q) => $q->whereIn('status', ['open', 'pending'])),
            'failedJobs' => $this->safeTableCount('failed_jobs'),
        ];
    }

    private function adminNavGroups(): array
    {
        return [
            ['group' => 'admin_profile', 'items' => [
                ['key' => 'overview', 'label' => 'overview', 'icon' => 'ri-dashboard-line'],
                ['key' => 'courses', 'label' => 'courses', 'icon' => 'ri-book-open-line'],
                ['key' => 'achievements', 'label' => 'achievements', 'icon' => 'ri-medal-line'],
                ['key' => 'settings', 'label' => 'settings', 'icon' => 'ri-settings-3-line'],
                ['key' => 'activity_log', 'label' => 'activity_log', 'icon' => 'ri-time-line'],
            ]],
        ];
    }

    private function renderAdmin(string $view, array $data = [])
    {
        return view($view, array_merge($data, [
            'adminNavGroups' => $this->adminNavGroups(),
            'adminStatsMini' => $this->baseStats(),
        ]));
    }

    public function index()
    {
        $stats = $this->baseStats();
        $users = User::query()->orderByDesc('id')->paginate(30);

        return $this->renderAdmin('admin.dashboard', array_merge($stats, ['users' => $users]));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);

        return $this->renderAdmin('admin.create-user', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_.]+$/i',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,teacher,student',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = null; // اجعلها null لتفعيل نظام OTP عند الدخول الأول

        if ($validated['role'] === 'student') {
            $validated['teacher_id'] = $validated['teacher_id'] ?? null;
        } else {
            $validated['teacher_id'] = null;
        }

        User::create($validated);

        return redirect()->route('admin.index')->with('success', 'User created successfully.');
    }

    public function checkUsername(Request $request)
    {
        $username = trim($request->query('username', ''));
        if ($username === '' || strlen($username) < 3) {
            return response()->json(['available' => false, 'reason' => 'Too short']);
        }

        $exists = User::where('username', $username)->exists();
        return response()->json(['available' => !$exists]);
    }

    public function show(User $user)
    {
        return $this->renderAdmin('admin.show-user', compact('user'));
    }

    public function edit(User $user)
    {
        $teachers = User::where('role', 'teacher')->where('id', '!=', $user->id)->orderBy('name')->get(['id', 'name']);

        return $this->renderAdmin('admin.edit-user', compact('user', 'teachers'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,teacher,student',
            'status' => 'required|in:active,inactive,blocked',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $validated['teacher_id'] = $validated['role'] === 'student' ? ($validated['teacher_id'] ?? null) : null;
        $user->update($validated);

        return redirect()->route('admin.index')->with('success', 'Updated successfully.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.index')->with('error', 'You cannot delete your own account.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->whereKeyNot($user->id)->count() === 0) {
            return redirect()->route('admin.index')->with('error', 'At least one admin account must remain.');
        }

        $user->delete();

        return redirect()->route('admin.index')->with('success', 'Deleted successfully.');
    }

    public function analytics()
    {
        $stats = $this->baseStats();
        $kpis = [
            'total_users' => $stats['totalUsers'],
            'total_messages' => $stats['totalMessages'],
            'total_exams' => $stats['totalExams'],
            'total_inquiries' => $stats['totalInquiries'],
            'pending_enrollments' => $stats['pendingEnrollments'],
        ];
        $roleDistribution = [
            'admins' => $stats['admins'],
            'teachers' => $stats['teachers'],
            'students' => $stats['students'],
        ];
        $latestUsers = User::query()->latest()->limit(8)->get();

        return $this->renderAdmin('admin.analytics', compact('roleDistribution', 'latestUsers', 'kpis') + $stats);
    }

    public function activity()
    {
        $stats = $this->baseStats();
        $recentUsers = User::query()->latest()->limit(20)->get();

        return $this->renderAdmin('admin.activity', compact('recentUsers') + $stats);
    }

    public function settings()
    {
        $stats = $this->baseStats();
        $settings = \App\Models\PlatformSetting::all()->keyBy('key');

        return $this->renderAdmin('admin.settings', compact('settings') + $stats);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'timezone' => 'required|string|max:50',
            'locale' => 'required|in:ar,en',
            'registration_enabled' => 'boolean',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'max_login_attempts' => 'required|integer|min:1|max:20',
            'smart_rewind_enabled' => 'boolean',
            'certificates_enabled' => 'boolean',
            'gamification_enabled' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\PlatformSetting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'تم حفظ الإعدادات بنجاح.');
    }

    public function finance()
    {
        $stats = $this->baseStats();
        $projectedRevenue = ($stats['students'] * 89) + ($stats['teachers'] * 149);
        $projectedCosts = max(1200, (int) round($projectedRevenue * 0.34));
        $projectedProfit = max(0, $projectedRevenue - $projectedCosts);

        return $this->renderAdmin('admin.finance', compact('projectedRevenue', 'projectedCosts', 'projectedProfit') + $stats);
    }

    public function rbac()
    {
        $stats = $this->baseStats();
        $matrix = [
            'students' => ['courses.view', 'progress.view', 'exams.submit'],
            'teachers' => ['courses.manage', 'lessons.manage', 'exams.manage'],
            'admins' => ['users.manage', 'reports.export', 'settings.manage'],
        ];

        return $this->renderAdmin('admin.rbac', compact('matrix') + $stats);
    }

    public function liveops()
    {
        $stats = $this->baseStats();
        $ops = [
            'online_users' => $stats['activeUsers'],
            'pending_enrollments' => $stats['pendingEnrollments'],
            'unread_messages' => $this->safeConditionalCount('messages', fn ($q) => $q->whereNull('read_at')),
            'new_users_24h' => User::where('created_at', '>=', now()->subDay())->count(),
        ];

        $alerts = collect([
            $ops['pending_enrollments'] > 25 ? 'High pending enrollment requests.' : null,
            $ops['unread_messages'] > 150 ? 'Unread messages are accumulating.' : null,
            $ops['new_users_24h'] > 30 ? 'High new user growth in 24h.' : null,
        ])->filter()->values();

        return $this->renderAdmin('admin.liveops', compact('ops', 'alerts') + $stats);
    }

    public function console(string $page)
    {
        $allowed = ['overview', 'courses', 'achievements', 'settings', 'activity_log'];

        if (!in_array($page, $allowed, true)) {
            abort(404);
        }

        $stats = $this->baseStats();
        $courses = Course::query()->latest()->limit(8)->get()->map(function ($c) {
            $enrollments = Schema::hasTable('course_enrollments')
                ? DB::table('course_enrollments')->where('course_id', $c->id)
                : null;
            $total = $enrollments ? (int) $enrollments->count() : 0;
            $completed = $enrollments ? (int) DB::table('course_enrollments')->where('course_id', $c->id)->where('status', 'completed')->count() : 0;
            $progress = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
            return [
                'name' => $c->name,
                'status' => $c->status ?? 'published',
                'progress' => $progress,
                'score' => $progress,
            ];
        })->values()->all();

        $achievements = Schema::hasTable('achievements')
            ? DB::table('achievements')->orderByDesc('id')->limit(8)->get()->map(fn ($a) => [
                'name' => $a->name ?? 'Achievement',
                'date' => optional($a->created_at)->format('Y-m-d') ?: now()->format('Y-m-d'),
            ])->all()
            : [];

        $activity = User::query()->latest()->limit(8)->get()->map(fn ($u) => [
            'title' => 'تحديث حساب المستخدم: ' . $u->name,
            'time' => optional($u->updated_at)->diffForHumans() ?? 'الآن',
        ])->all();

        $rows = User::latest()->limit(8)->get()->map(fn ($u) => [$u->name, $u->email, $u->role, $u->status ?? 'active', optional($u->created_at)->format('Y-m-d')])->all();

        return $this->renderAdmin('admin.console-page', [
            'page' => $page,
            'cards' => [
                ['label' => 'total_users', 'value' => $stats['totalUsers']],
                ['label' => 'teachers', 'value' => $stats['teachers']],
                ['label' => 'students', 'value' => $stats['students']],
                ['label' => 'courses', 'value' => $stats['totalCourses']],
                ['label' => 'exams', 'value' => $stats['totalExams']],
                ['label' => 'certificates', 'value' => $stats['totalCertificates']],
                ['label' => 'inquiries', 'value' => $stats['totalInquiries']],
                ['label' => 'pending_enrollments', 'value' => $stats['pendingEnrollments']],
            ],
            'sectionRows' => $rows,
            'coursesData' => $courses,
            'achievementsData' => $achievements,
            'activityData' => $activity,
        ] + $stats);
    }
}
