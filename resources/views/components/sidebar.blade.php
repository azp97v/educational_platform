<!--
    ═════════════════════════════════════════════════════════════════════════
    SIDEBAR COMPONENT - شريط التنقل الجانبي
    ═════════════════════════════════════════════════════════════════════════

    المسؤوليات:
    ✓ عرض قائمة التنقل حسب دور المستخدم
    ✓ التبديل بين الأدوار والأقسام
    ✓ عرض معلومات المستخدم
    ✓ زر تسجيل الخروج
-->

<aside class="sidebar" id="sidebar">
    <!-- شعار المنصة -->
    <div class="sidebar-header">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="ri-graduation-cap-2-line"></i>
            </div>
            <h2 class="logo-text">إجلال</h2>
            <p class="logo-subtitle">منصة تعليمية ذكية</p>
        </div>
    </div>

    <!-- قائمة التنقل -->
    <nav class="sidebar-nav">
        @if(auth()->user()->role === 'student')
            <!-- Student Navigation -->
            <a href="{{ route('student.index') }}" class="nav-link @if(request()->is('student/dashboard')) active @endif">
                <i class="ri-home-4-line"></i>
                <span>الرئيسية</span>
            </a>

            <a href="{{ route('student.academy') }}" class="nav-link @if(request()->is('student/academy*')) active @endif">
                <i class="ri-book-open-line"></i>
                <span>المسارات</span>
            </a>

            <a href="{{ route('gamification.leaderboard') }}" class="nav-link @if(request()->is('gamification/leaderboard')) active @endif">
                <i class="ri-bar-chart-2-line"></i>
                <span>لوحة الألعاب</span>
            </a>

            <a href="{{ route('gamification.achievements') }}" class="nav-link @if(request()->is('gamification/achievements')) active @endif">
                <i class="ri-medal-line"></i>
                <span>الإنجازات</span>
            </a>

            <a href="{{ route('certificate.index') }}" class="nav-link @if(request()->is('certificates*')) active @endif">
                <i class="ri-award-line"></i>
                <span>الشهادات</span>
            </a>

            <a href="{{ route('student.inquiries.index') }}" class="nav-link @if(request()->is('student/my-inquiries*')) active @endif">
                <i class="ri-question-line"></i>
                <span>الاستفسارات</span>
            </a>

        @elseif(auth()->user()->role === 'teacher')
            <!-- Teacher Navigation -->
            <a href="{{ route('teacher.index') }}" class="nav-link @if(request()->is('teacher')) active @endif">
                <i class="ri-home-4-line"></i>
                <span>الرئيسية</span>
            </a>

            <a href="{{ route('teacher.index') }}" class="nav-link @if(request()->is('teacher') && !request()->is('teacher/analytics')) active @endif">
                <i class="ri-book-open-line"></i>
                <span>المسارات</span>
            </a>

            <a href="{{ route('teacher.exams') }}" class="nav-link @if(request()->is('teacher/exams')) active @endif">
                <i class="ri-survey-line"></i>
                <span>الامتحانات</span>
            </a>

            <a href="{{ route('teacher.questions.manage') }}" class="nav-link @if(request()->is('teacher/questions-manage')) active @endif">
                <i class="ri-question-line"></i>
                <span>الأسئلة</span>
            </a>

            <a href="{{ route('teacher.analytics') }}" class="nav-link @if(request()->is('teacher/analytics')) active @endif">
                <i class="ri-bar-chart-2-line"></i>
                <span>التحليلات</span>
            </a>

            <a href="{{ route('teacher.messaging') }}" class="nav-link @if(request()->is('teacher/messaging')) active @endif">
                <i class="ri-message-2-line"></i>
                <span>المراسلة</span>
            </a>

        @elseif(auth()->user()->role === 'admin')
            <!-- Admin Navigation -->
            <a href="{{ route('admin.index') }}" class="nav-link @if(request()->is('admin')) active @endif">
                <i class="ri-home-4-line"></i>
                <span>لوحة التحكم</span>
            </a>

            <a href="{{ route('admin.index') }}" class="nav-link @if(request()->is('admin/users')) active @endif">
                <i class="ri-team-line"></i>
                <span>المستخدمون</span>
            </a>

            <a href="{{ route('admin.create') }}" class="nav-link @if(request()->is('admin/create')) active @endif">
                <i class="ri-user-add-line"></i>
                <span>إضافة مستخدم</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="nav-link @if(request()->is('admin/settings')) active @endif">
                <i class="ri-settings-3-line"></i>
                <span>الإعدادات</span>
            </a>
        @endif
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <!-- Theme Toggle -->
        <button class="nav-link nav-toggle-theme" id="sidebarThemeToggle" title="تبديل المظهر">
            <i class="ri-moon-line theme-icon"></i>
            <span>المظهر</span>
        </button>

        <!-- Logout -->
        <form method="POST" action="
            @if(auth()->user()->role === 'teacher')
                {{ route('teacher.logout') }}
            @else
                {{ route('logout') }}
            @endif
        " class="logout-form">
            @csrf
            <button type="submit" class="nav-link nav-logout" title="تسجيل الخروج الآمن">
                <i class="ri-logout-box-line"></i>
                <span>تسجيل خروج</span>
            </button>
        </form>
    </div>
</aside>

<style>
    /**
     * Sidebar Styles
     */
    .sidebar {
        width: var(--sidebar-width);
        background-color: var(--bg-secondary);
        border-right: 1px solid var(--bg-tertiary);
        position: fixed;
        right: 0;
        top: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        z-index: 100;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.05);
        padding: var(--space-lg) 0;
        overflow-y: auto;
        transition: var(--transition-base);
    }

    .sidebar-header {
        padding: var(--space-lg) var(--space-lg);
        border-bottom: 1px solid var(--bg-tertiary);
        margin-bottom: var(--space-xl);
        text-align: center;
    }

    .logo-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto var(--space-md);
        background-color: var(--color-gold-light);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: var(--color-gold);
    }

    .logo-text {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: var(--space-xs);
    }

    .logo-subtitle {
        font-size: 11px;
        color: var(--text-tertiary);
        font-weight: 500;
    }

    .sidebar-nav {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: var(--space-sm);
        padding: 0 var(--space-lg);
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: var(--space-md);
        padding: var(--space-md) var(--space-lg);
        border: none;
        border-radius: var(--radius-lg);
        background-color: transparent;
        color: var(--text-secondary);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        text-align: right;
        transition: var(--transition-fast);
        white-space: nowrap;
    }

    .nav-link i {
        font-size: 20px;
        width: 22px;
        text-align: center;
        flex-shrink: 0;
    }

    .nav-link:hover {
        background-color: var(--bg-tertiary);
        color: var(--text-primary);
    }

    .nav-link.active {
        background-color: var(--color-gold);
        color: #fff;
    }

    .nav-link.active i {
        color: #fff;
    }

    .sidebar-footer {
        padding: var(--space-lg);
        border-top: 1px solid var(--bg-tertiary);
    }

    .nav-logout {
        color: var(--color-danger);
        width: 100%;
        justify-content: flex-end;
    }

    .nav-logout:hover {
        background-color: rgba(255, 59, 48, 0.1);
    }

    .logout-form {
        width: 100%;
        margin: 0;
    }

    @media (max-width: 1024px) {
        .sidebar {
            width: 200px;
        }

        .nav-link span {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            width: 100%;
            max-width: 240px;
        }

        .sidebar-open {
            transform: translateX(0);
        }

        /* ===== HIDE SCROLLBAR ===== */
        ::-webkit-scrollbar {
            display: none;
        }

        .sidebar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('sidebarThemeToggle')?.addEventListener('click', function() {
        if (typeof ThemeManager !== 'undefined' && typeof ThemeManager.toggle === 'function') {
            ThemeManager.toggle();
        }
    });
});
</script>
