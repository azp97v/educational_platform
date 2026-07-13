<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        if (!Schema::hasTable($table)) return 0;
        return (int) $queryBuilder(DB::table($table))->count();
    }

    private function baseStats(): array
    {
        return Cache::remember('admin_base_stats', 300, function () {
            return [
            'totalUsers'         => User::count(),
            'teachers'           => User::where('role', 'teacher')->count(),
            'students'           => User::where('role', 'student')->count(),
            'admins'             => User::where('role', 'admin')->count(),
            'activeUsers'        => $this->safeConditionalCount('users', fn($q) => $q->where('last_activity_at', '>=', now()->subHour())),
            'totalCourses'       => Course::count(),
            'totalLessons'       => Lesson::count(),
            'totalExams'         => $this->safeTableCount('exams'),
            'totalMessages'      => $this->safeTableCount('messages'),
            'totalInquiries'     => $this->safeTableCount('student_inquiries'),
            'totalCertificates'  => $this->safeTableCount('certificates'),
            'totalRewinds'       => $this->safeTableCount('smart_rewinds'),
            'pendingEnrollments' => $this->safeConditionalCount('course_enrollments', fn($q) => $q->where('status', 'pending')),
            'openTickets'        => $this->safeConditionalCount('support_tickets', fn($q) => $q->whereIn('status', ['open', 'pending'])),
            'failedJobs'         => $this->safeTableCount('failed_jobs'),
            'activeAnnouncements' => $this->safeConditionalCount('announcements', fn($q) => $q->where('is_active', true)->where(fn($q2) => $q2->whereNull('expires_at')->orWhere('expires_at', '>', now()))),
            ]; // end inner array
        }); // end Cache::remember
        // liveopsAlerts uses now() so stays outside the cache
        $stats['liveopsAlerts'] = $this->calcLiveopsAlertCount();
        return $stats;
    }

    private function renderAdmin(string $view, array $data = [])
    {
        return view($view, array_merge($data, [
            'adminStatsMini' => $this->baseStats(),
        ]));
    }

    private function calcLiveopsAlertCount(): int
    {
        $count = 0;
        $pending = $this->safeConditionalCount('course_enrollments', fn($q) => $q->where('status', 'pending'));
        $unread  = $this->safeConditionalCount('messages', fn($q) => $q->whereNull('read_at'));
        $new24h  = Schema::hasTable('users') ? User::where('created_at', '>=', now()->subDay())->count() : 0;
        if ($pending > 25) $count++;
        if ($unread > 150) $count++;
        if ($new24h > 30)  $count++;
        return $count;
    }

    private function adminLog(string $action, ?string $targetType = null, ?int $targetId = null, ?string $details = null): void
    {
        try {
            AdminLog::create([
                'admin_id'    => Auth::id(),
                'action'      => $action,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'details'     => $details,
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Throwable) {
            // Never let logging crash a request
        }
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 1: Enhanced User Management (Dashboard)
    ═══════════════════════════════════════════════════════ */

    public function index(Request $request)
    {
        $stats = $this->baseStats();

        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderByDesc('id')->paginate(30)->withQueryString();

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
            'name'       => 'required|string|max:255',
            'username'   => 'nullable|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/i',
            'email'      => 'required|email|unique:users',
            'password'   => ['required', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'role'       => 'required|in:admin,teacher,student',
            'teacher_id' => 'nullable|exists:users,id',
        ], [
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم على الأقل',
        ]);

        $validated['password']          = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['teacher_id']        = $validated['role'] === 'student' ? ($validated['teacher_id'] ?? null) : null;

        $newUser = User::create($validated);
        $this->adminLog('user_created', 'User', $newUser->id, "Created {$newUser->role}: {$newUser->email}");

        return redirect()->route('admin.users')->with('success', 'تم إنشاء الحساب بنجاح.');
    }

    public function checkUsername(Request $request)
    {
        $username = trim($request->query('username', ''));
        if (strlen($username) < 3) {
            return response()->json(['available' => false, 'reason' => 'قصير جداً']);
        }
        $exists = User::where('username', $username)->exists();
        return response()->json(['available' => !$exists]);
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 2: Full User Profile
    ═══════════════════════════════════════════════════════ */

    public function show(User $user)
    {
        $enrollmentCount = Schema::hasTable('course_enrollments')
            ? DB::table('course_enrollments')->where('user_id', $user->id)->count() : 0;
        $approvedEnrollments = Schema::hasTable('course_enrollments')
            ? DB::table('course_enrollments')->where('user_id', $user->id)->where('status', 'approved')->count() : 0;
        $messageCount = Schema::hasTable('messages')
            ? DB::table('messages')->where('sender_id', $user->id)->count() : 0;
        $certCount = Schema::hasTable('certificates')
            ? DB::table('certificates')->where('user_id', $user->id)->count() : 0;
        $inquiryCount = Schema::hasTable('student_inquiries')
            ? DB::table('student_inquiries')->where('student_id', $user->id)->count() : 0;

        $enrollments = Schema::hasTable('course_enrollments')
            ? DB::table('course_enrollments')
                ->join('courses', 'courses.id', '=', 'course_enrollments.course_id')
                ->select('courses.name as course_name', 'course_enrollments.status', 'course_enrollments.enrolled_at')
                ->where('course_enrollments.user_id', $user->id)
                ->orderByDesc('course_enrollments.created_at')
                ->limit(10)
                ->get()
            : collect();

        return $this->renderAdmin('admin.show-user', compact(
            'user', 'enrollmentCount', 'approvedEnrollments',
            'messageCount', 'certCount', 'inquiryCount', 'enrollments'
        ));
    }

    public function edit(User $user)
    {
        $teachers = User::where('role', 'teacher')->where('id', '!=', $user->id)->orderBy('name')->get(['id', 'name']);
        return $this->renderAdmin('admin.edit-user', compact('user', 'teachers'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'role'       => 'required|in:admin,teacher,student',
            'status'     => 'required|in:active,inactive,blocked',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $validated['teacher_id'] = $validated['role'] === 'student' ? ($validated['teacher_id'] ?? null) : null;
        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'تم تحديث الحساب بنجاح.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users')->with('error', 'لا يمكنك حذف حسابك الخاص.');
        }
        if ($user->role === 'admin' && User::where('role', 'admin')->whereKeyNot($user->id)->count() === 0) {
            return redirect()->route('admin.users')->with('error', 'يجب أن يبقى مشرف واحد على الأقل.');
        }
        $this->adminLog('user_deleted', 'User', $user->id, "Deleted {$user->role}: {$user->email}");
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'تم حذف الحساب بنجاح.');
    }

    public function resetUserPassword(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'لا يمكنك إعادة تعيين كلمة مرور حسابك الخاص من هنا.');
        }
        $newPassword = Str::random(6) . rand(10, 99) . strtoupper(Str::random(2));
        $user->update(['password' => Hash::make($newPassword)]);
        $this->adminLog('password_reset', 'User', $user->id, "Reset password for: {$user->email}");

        return redirect()->back()->with('password_reset', "تمت إعادة التعيين. كلمة المرور الجديدة: {$newPassword}");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'action' => 'required|in:block,unblock,delete',
        ]);

        $ids = collect($request->ids)->reject(fn($id) => $id == Auth::id())->values();

        if ($ids->isEmpty()) {
            return redirect()->back()->with('error', 'لا يمكن تطبيق العملية على حسابك الخاص.');
        }

        if ($request->action === 'delete') {
            User::whereIn('id', $ids)
                ->where(fn($q) => $q->where('role', '!=', 'admin')->orWhereRaw('(SELECT COUNT(*) FROM users WHERE role = "admin") > 1'))
                ->delete();
            $this->adminLog('bulk_delete', 'User', null, "Deleted IDs: " . $ids->implode(','));
            return redirect()->route('admin.users')->with('success', "تم حذف " . $ids->count() . " حساب.");
        }

        $newStatus = $request->action === 'block' ? 'blocked' : 'active';
        User::whereIn('id', $ids)->update(['status' => $newStatus]);
        $this->adminLog("bulk_{$request->action}", 'User', null, "IDs: " . $ids->implode(','));
        $label = $request->action === 'block' ? 'حظر' : 'إلغاء حظر';
        return redirect()->route('admin.users')->with('success', "تم {$label} " . $ids->count() . " حساب.");
    }

    public function exportUsers(Request $request)
    {
        $query = User::query();
        if ($search = $request->get('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }
        if ($role = $request->get('role'))     $query->where('role', $role);
        if ($status = $request->get('status')) $query->where('status', $status);

        $users = $query->orderByDesc('id')->get(['id', 'name', 'username', 'email', 'role', 'status', 'created_at']);

        $csv = "\xEF\xBB\xBF"; // BOM for Arabic in Excel
        $csv .= "ID,الاسم,اسم المستخدم,البريد الإلكتروني,الدور,الحالة,تاريخ الإنشاء\n";
        foreach ($users as $u) {
            $csv .= implode(',', [
                $u->id,
                '"' . str_replace('"', '""', $u->name) . '"',
                $u->username ?? '',
                $u->email,
                $u->role,
                $u->status ?? 'active',
                $u->created_at?->format('Y-m-d H:i'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 3: Analytics with Charts
    ═══════════════════════════════════════════════════════ */

    public function analytics()
    {
        $stats = $this->baseStats();

        // Daily registrations — last 30 days
        $rawCounts = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartLabels = [];
        $chartCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('m/d');
            $chartCounts[] = $rawCounts[$date] ?? 0;
        }

        // Growth vs previous period
        $thisMonth  = User::where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth  = User::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $growth     = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : ($thisMonth > 0 ? 100 : 0);

        $total = max(1, (int) $stats['totalUsers']);
        $roleDistribution = [
            'admins'   => $stats['admins'],
            'teachers' => $stats['teachers'],
            'students' => $stats['students'],
        ];

        $latestUsers = User::latest()->limit(8)->get();

        // Top teachers by student count
        $topTeachers = User::where('role', 'teacher')
            ->withCount(['students as student_count' => fn($q) => $q->where('role', 'student')])
            ->orderByDesc('student_count')
            ->limit(5)
            ->get(['id', 'name', 'email']);

        $kpis = [
            'total_users'         => $stats['totalUsers'],
            'total_messages'      => $stats['totalMessages'],
            'total_exams'         => $stats['totalExams'],
            'total_inquiries'     => $stats['totalInquiries'],
            'pending_enrollments' => $stats['pendingEnrollments'],
        ];

        return $this->renderAdmin('admin.analytics', compact(
            'roleDistribution', 'latestUsers', 'kpis',
            'chartLabels', 'chartCounts', 'growth',
            'thisMonth', 'lastMonth', 'topTeachers'
        ) + $stats);
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 4: Enrollment Requests
    ═══════════════════════════════════════════════════════ */

    public function enrollments(Request $request)
    {
        $stats = $this->baseStats();
        $filter = $request->get('filter', 'pending');

        $query = CourseEnrollment::with(['student', 'course.instructor'])
            ->orderByDesc('created_at');

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $enrollments = $query->paginate(25)->withQueryString();
        $pendingCount = CourseEnrollment::pending()->count();

        return $this->renderAdmin('admin.enrollments', compact('enrollments', 'filter', 'pendingCount') + $stats);
    }

    public function approveEnrollment(CourseEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'approved', 'enrolled_at' => now()]);
        $this->adminLog('enrollment_approved', 'CourseEnrollment', $enrollment->id, "Student {$enrollment->user_id} → Course {$enrollment->course_id}");
        return redirect()->back()->with('success', 'تم قبول طلب الالتحاق بنجاح.');
    }

    public function rejectEnrollment(Request $request, CourseEnrollment $enrollment)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $enrollment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason ?? null,
        ]);
        $this->adminLog('enrollment_rejected', 'CourseEnrollment', $enrollment->id, "Student {$enrollment->user_id} → Course {$enrollment->course_id}");
        return redirect()->back()->with('success', 'تم رفض طلب الالتحاق.');
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 7: Failed Jobs Management
    ═══════════════════════════════════════════════════════ */

    public function failedJobs()
    {
        $stats = $this->baseStats();
        $jobs  = DB::table('failed_jobs')->orderByDesc('failed_at')->paginate(20);
        return $this->renderAdmin('admin.failed-jobs', compact('jobs') + $stats);
    }

    public function retryFailedJob($uuid)
    {
        try {
            Artisan::call('queue:retry', ['id' => [$uuid]]);
            return redirect()->back()->with('success', 'تمت إعادة المحاولة للعملية.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل في إعادة المحاولة: ' . $e->getMessage());
        }
    }

    public function deleteFailedJob($uuid)
    {
        DB::table('failed_jobs')->where('uuid', $uuid)->delete();
        return redirect()->back()->with('success', 'تم حذف العملية الفاشلة.');
    }

    public function deleteAllFailedJobs()
    {
        DB::table('failed_jobs')->delete();
        return redirect()->back()->with('success', 'تم حذف جميع العمليات الفاشلة.');
    }

    /* ═══════════════════════════════════════════════════════
       Existing Pages (unchanged)
    ═══════════════════════════════════════════════════════ */

    public function settings()
    {
        $stats    = $this->baseStats();
        $settings = PlatformSetting::all()->keyBy('key');
        return $this->renderAdmin('admin.settings', compact('settings') + $stats);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'platform_name'        => 'required|string|max:255',
            'timezone'             => 'required|string|max:50',
            'locale'               => 'required|in:ar,en',
            'registration_enabled' => 'boolean',
            'session_timeout'      => 'required|integer|min:5|max:1440',
            'max_login_attempts'   => 'required|integer|min:1|max:20',
            'smart_rewind_enabled' => 'boolean',
            'certificates_enabled' => 'boolean',
            'gamification_enabled' => 'boolean',
        ]);
        foreach ($validated as $key => $value) {
            PlatformSetting::set($key, $value);
        }
        $this->adminLog('settings_updated', null, null, 'Platform settings saved');
        return redirect()->route('admin.settings')->with('success', 'تم حفظ الإعدادات بنجاح.');
    }

    public function finance()
    {
        $stats            = $this->baseStats();
        $projectedRevenue = ($stats['students'] * 89) + ($stats['teachers'] * 149);
        $projectedCosts   = max(1200, (int) round($projectedRevenue * 0.34));
        $projectedProfit  = max(0, $projectedRevenue - $projectedCosts);

        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Schema::hasTable('users')
                ? User::whereYear('created_at', $month->year)
                      ->whereMonth('created_at', $month->month)
                      ->count()
                : 0;
            $monthlyData[] = [
                'label'   => $month->format('M y'),
                'users'   => $count,
                'revenue' => $count * 89,
            ];
        }

        $revenueBreakdown = [
            ['label' => 'اشتراكات الطلاب',   'value' => $stats['students'] * 89,  'color' => '#6c63ff'],
            ['label' => 'رسوم المعلمين',     'value' => $stats['teachers'] * 149, 'color' => '#10b981'],
            ['label' => 'التكاليف التشغيلية', 'value' => $projectedCosts,          'color' => '#f59e0b'],
        ];

        return $this->renderAdmin('admin.finance', compact(
            'projectedRevenue', 'projectedCosts', 'projectedProfit',
            'monthlyData', 'revenueBreakdown'
        ) + $stats);
    }

    public function rbac()
    {
        $stats = $this->baseStats();

        $permissions = [
            ['key' => 'dashboard.view',      'label' => 'عرض لوحة التحكم'],
            ['key' => 'users.manage',         'label' => 'إدارة المستخدمين'],
            ['key' => 'courses.view',         'label' => 'عرض المسارات'],
            ['key' => 'courses.manage',       'label' => 'إدارة المسارات'],
            ['key' => 'lessons.view',         'label' => 'عرض الدروس'],
            ['key' => 'lessons.manage',       'label' => 'إدارة الدروس'],
            ['key' => 'exams.view',           'label' => 'عرض الاختبارات'],
            ['key' => 'exams.submit',         'label' => 'إجابة الاختبارات'],
            ['key' => 'exams.manage',         'label' => 'إدارة الاختبارات'],
            ['key' => 'progress.view',        'label' => 'عرض التقدم'],
            ['key' => 'certificates.view',    'label' => 'عرض الشهادات'],
            ['key' => 'certificates.manage',  'label' => 'إصدار الشهادات'],
            ['key' => 'messages.send',        'label' => 'إرسال الرسائل'],
            ['key' => 'enrollments.apply',    'label' => 'طلب الالتحاق'],
            ['key' => 'enrollments.manage',   'label' => 'إدارة الالتحاق'],
            ['key' => 'announcements.view',   'label' => 'عرض الإعلانات'],
            ['key' => 'announcements.manage', 'label' => 'إدارة الإعلانات'],
            ['key' => 'reports.export',       'label' => 'تصدير التقارير'],
            ['key' => 'settings.manage',      'label' => 'إعدادات المنصة'],
            ['key' => 'logs.view',            'label' => 'سجل النشاط'],
        ];

        $roles = [
            'student' => ['label' => 'الطالب',  'color' => '#6c63ff', 'icon' => 'ri-user-3-line',
                'grants' => ['dashboard.view','courses.view','lessons.view','exams.view','exams.submit','progress.view','certificates.view','messages.send','enrollments.apply','announcements.view']],
            'teacher' => ['label' => 'المعلم',  'color' => '#10b981', 'icon' => 'ri-user-star-line',
                'grants' => ['dashboard.view','courses.view','courses.manage','lessons.view','lessons.manage','exams.view','exams.manage','progress.view','certificates.view','certificates.manage','messages.send','announcements.view']],
            'admin'   => ['label' => 'المشرف', 'color' => '#f59e0b', 'icon' => 'ri-shield-user-line',
                'grants' => array_column($permissions, 'key')],
        ];

        return $this->renderAdmin('admin.rbac', compact('permissions', 'roles') + $stats);
    }

    public function liveops()
    {
        $stats        = $this->baseStats();
        $unreadMsgs   = $this->safeConditionalCount('messages', fn($q) => $q->whereNull('read_at'));
        $new24h       = User::where('created_at', '>=', now()->subDay())->count();
        $ops          = [
            'online_users'        => $stats['activeUsers'],
            'pending_enrollments' => $stats['pendingEnrollments'],
            'unread_messages'     => $unreadMsgs,
            'new_users_24h'       => $new24h,
        ];
        $alerts = collect([
            $ops['pending_enrollments'] > 25 ? ['level' => 'warning', 'msg' => 'طلبات الالتحاق المعلقة مرتفعة — ' . $ops['pending_enrollments'] . ' طلب'] : null,
            $ops['unread_messages'] > 150    ? ['level' => 'error',   'msg' => 'الرسائل غير المقروءة تتراكم — ' . $ops['unread_messages'] . ' رسالة'] : null,
            $ops['new_users_24h'] > 30       ? ['level' => 'info',    'msg' => 'نمو كبير في التسجيلات — ' . $ops['new_users_24h'] . ' مستخدم جديد خلال 24 ساعة'] : null,
        ])->filter()->values();

        $recentUsers = Schema::hasTable('users')
            ? User::latest()->limit(10)->get(['id', 'name', 'email', 'role', 'created_at'])
            : collect();

        $pendingEnrollments = Schema::hasTable('course_enrollments')
            ? CourseEnrollment::with(['student', 'course'])
                ->pending()
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return $this->renderAdmin('admin.liveops', compact(
            'ops', 'alerts', 'recentUsers', 'pendingEnrollments'
        ) + $stats);
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 6: Real Activity Log
    ═══════════════════════════════════════════════════════ */

    public function activity()
    {
        $stats = $this->baseStats();
        $logs  = AdminLog::with('admin')->latest()->paginate(30);
        return $this->renderAdmin('admin.activity', compact('logs') + $stats);
    }

    /* ═══════════════════════════════════════════════════════
       AXIS 5: Announcements
    ═══════════════════════════════════════════════════════ */

    public function announcements()
    {
        $stats         = $this->baseStats();
        $announcements = Announcement::with('author')->latest()->paginate(20);
        return $this->renderAdmin('admin.announcements', compact('announcements') + $stats);
    }

    public function createAnnouncement()
    {
        return $this->renderAdmin('admin.announcement-form', ['announcement' => null] + $this->baseStats());
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'type'        => 'required|in:info,success,warning,danger',
            'target_role' => 'required|in:all,admin,teacher,student',
            'scope'       => 'required|in:dashboard,site_wide',
            'is_active'   => 'boolean',
            'expires_at'  => 'nullable|date|after:now',
        ]);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['created_by'] = Auth::id();

        $ann = Announcement::create($data);
        $this->adminLog('announcement_created', 'Announcement', $ann->id, $ann->title);

        return redirect()->route('admin.announcements')->with('success', 'تم نشر الإعلان بنجاح.');
    }

    public function editAnnouncement(Announcement $announcement)
    {
        return $this->renderAdmin('admin.announcement-form', compact('announcement') + $this->baseStats());
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'type'        => 'required|in:info,success,warning,danger',
            'target_role' => 'required|in:all,admin,teacher,student',
            'scope'       => 'required|in:dashboard,site_wide',
            'is_active'   => 'boolean',
            'expires_at'  => 'nullable|date',
        ]);
        $data['is_active'] = $request->boolean('is_active', false);
        $announcement->update($data);
        $this->adminLog('announcement_updated', 'Announcement', $announcement->id, $announcement->title);

        return redirect()->route('admin.announcements')->with('success', 'تم تحديث الإعلان بنجاح.');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $this->adminLog('announcement_deleted', 'Announcement', $announcement->id, $announcement->title);
        $announcement->delete();
        return redirect()->route('admin.announcements')->with('success', 'تم حذف الإعلان.');
    }

    public function console(string $page)
    {
        $allowed = ['overview', 'courses', 'achievements', 'settings', 'activity_log'];
        if (!in_array($page, $allowed, true)) abort(404);

        $stats  = $this->baseStats();
        $courses = Course::query()->latest()->limit(8)->get()->map(function ($c) {
            $e        = Schema::hasTable('course_enrollments') ? DB::table('course_enrollments')->where('course_id', $c->id) : null;
            $total    = $e ? (int) $e->count() : 0;
            $completed = $e ? (int) DB::table('course_enrollments')->where('course_id', $c->id)->where('status', 'completed')->count() : 0;
            $progress = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
            return ['name' => $c->name, 'status' => $c->status ?? 'published', 'progress' => $progress, 'score' => $progress];
        })->values()->all();

        $achievements = Schema::hasTable('achievements')
            ? DB::table('achievements')->orderByDesc('id')->limit(8)->get()->map(fn($a) => [
                'name' => $a->name ?? 'Achievement',
                'date' => optional($a->created_at)->format('Y-m-d') ?: now()->format('Y-m-d'),
            ])->all()
            : [];

        $activity = User::latest()->limit(8)->get()->map(fn($u) => [
            'title' => 'تحديث حساب المستخدم: ' . $u->name,
            'time'  => optional($u->updated_at)->diffForHumans() ?? 'الآن',
        ])->all();

        $rows = User::latest()->limit(8)->get()->map(fn($u) => [
            $u->name, $u->email, $u->role, $u->status ?? 'active', optional($u->created_at)->format('Y-m-d'),
        ])->all();

        return $this->renderAdmin('admin.console-page', [
            'page' => $page,
            'cards' => [
                ['label' => 'total_users',         'value' => $stats['totalUsers']],
                ['label' => 'teachers',             'value' => $stats['teachers']],
                ['label' => 'students',             'value' => $stats['students']],
                ['label' => 'courses',              'value' => $stats['totalCourses']],
                ['label' => 'exams',                'value' => $stats['totalExams']],
                ['label' => 'certificates',         'value' => $stats['totalCertificates']],
                ['label' => 'inquiries',            'value' => $stats['totalInquiries']],
                ['label' => 'pending_enrollments',  'value' => $stats['pendingEnrollments']],
            ],
            'sectionRows'     => $rows,
            'coursesData'     => $courses,
            'achievementsData' => $achievements,
            'activityData'    => $activity,
        ] + $stats);
    }
}
