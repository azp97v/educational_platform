{{-- 
═══════════════════════════════════════════════════════════════════════════════
SIDEBAR COMPONENT - شريط التنقل الجانبي الموحد
═══════════════════════════════════════════════════════════════════════════════

المسؤوليات:
✓ عرض قائمة التنقل الموحدة لجميع الأدوار (student, teacher, admin)
✓ التبديل بين المظهر (الفاتح والمستخدم)
✓ عرض معلومات المستخدم الحالي
✓ خيار تسجيل الخروج الآمن
✓ التوافق الكامل مع الأجهزة المحمولة
✓ Animations و Effects موحدة

الخصائص والميزات:
- نفس الـ CSS والـ HTML لجميع الصفحات
- نفس الأيقونات والألوان والتنسيقجموعات البيانات
- نفس السلوك والتفاعلات
- نفس الـ Theme Toggle و Animations
--}}

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<aside class="sidebar" id="sidebar">
    {{-- شعار/لوجو المنصة --}}
    <div class="sidebar-logo">
        <div class="logo-icon">
            @if(file_exists(public_path('images/logo/logo.png')))
                <img src="{{ asset('images/logo/logo.png?v=' . time()) }}" alt="إجلال" loading="lazy" />
            @else
                <i class="ri-book-read-fill"></i>
            @endif
        </div>
        <div class="logo-name">إجلال</div>
        <div class="logo-sub">المنصة التعليمية</div>
    </div>
    
    {{-- قائمة التنقل حسب دور المستخدم --}}
    <nav class="sidebar-nav">
        @if(auth()->user()->role === 'student')
            {{-- قائمة الطالب --}}
            <a href="{{ route('student.index') }}" 
               class="nav-btn {{ request()->routeIs('student.index') ? 'active' : '' }}">
                <i class="ri-home-4-line"></i>
                <span>الرئيسية</span>
            </a>
            
            <a href="{{ route('student.academy') }}" 
               class="nav-btn {{ request()->routeIs('student.academy') ? 'active' : '' }}">
                <i class="ri-book-open-line"></i>
                <span>المسارات</span>
            </a>
            
            <a href="{{ route('student.exams') }}" 
               class="nav-btn {{ request()->routeIs('student.exams') ? 'active' : '' }}">
                <i class="ri-file-list-line"></i>
                <span>الاختبارات</span>
            </a>
            
            <a href="{{ route('student.inquiries.index') }}" 
               class="nav-btn {{ request()->routeIs('student.inquiries.index') ? 'active' : '' }}">
                <i class="ri-question-line"></i>
                <span>الاستفسارات</span>
            </a>
            
            <a href="{{ route('gamification.leaderboard') }}" 
               class="nav-btn {{ request()->routeIs('gamification.*') ? 'active' : '' }}">
                <i class="ri-bar-chart-2-line"></i>
                <span>إحصائياتي</span>
            </a>
            
        @elseif(auth()->user()->role === 'teacher')
            <a href="{{ route('teacher.dashboard') }}" class="nav-btn {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" id="nb-home">
                <i class="ri-home-4-line"></i><span>الرئيسية</span>
            </a>
            <a href="{{ route('teacher.courses') }}" class="nav-btn {{ request()->routeIs('teacher.courses') ? 'active' : '' }}" id="nb-courses">
                <i class="ri-book-2-line"></i><span>المسارات</span>
            </a>
            <a href="{{ route('teacher.enrollment.requests') }}" class="nav-btn {{ request()->routeIs('teacher.enrollment.requests') ? 'active' : '' }}" id="nb-enrollment">
                <i class="ri-user-add-line"></i><span>طلبات الالتحاق</span>
            </a>
            <a href="{{ route('teacher.exams') }}" class="nav-btn {{ request()->routeIs('teacher.exams') ? 'active' : '' }}" id="nb-exams">
                <i class="ri-file-list-line"></i><span>الاختبارات</span>
            </a>
            <a href="{{ route('teacher.analytics') }}" class="nav-btn {{ request()->routeIs('teacher.analytics') ? 'active' : '' }}" id="nb-analytics">
                <i class="ri-bar-chart-2-line"></i><span>نسبة الإنجاز</span>
            </a>
            <a href="{{ route('teacher.students') }}" class="nav-btn {{ request()->routeIs('teacher.students') ? 'active' : '' }}" id="nb-students">
                <i class="ri-team-line"></i><span>طلابي</span>
            </a>
            <a href="{{ route('teacher.certificates.students') }}" class="nav-btn {{ request()->routeIs('teacher.certificates.*') ? 'active' : '' }}" id="nb-certificates">
                <i class="ri-award-line"></i><span>الشهادات</span>
            </a>
            <a href="{{ route('teacher.questions.manage') }}" class="nav-btn {{ request()->routeIs('teacher.questions.manage') ? 'active' : '' }}" id="nb-inquiries">
                <i class="ri-chat-3-line"></i><span>الأسئلة والاستفسارات</span>
            </a>
            <a href="{{ route('teacher.messaging') }}" class="nav-btn {{ request()->routeIs('teacher.messaging') ? 'active' : '' }}" id="nb-messaging">
                <i class="ri-message-2-line"></i><span>المراسلة</span>
            </a>
        @elseif(auth()->user()->role === 'admin')
            {{-- قائمة الإدارة --}}
            <a href="{{ route('admin.index') }}" 
               class="nav-btn {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <i class="ri-home-4-line"></i>
                <span>لوحة التحكم</span>
            </a>
            
            <a href="{{ route('admin.index') }}" 
               class="nav-btn {{ request()->routeIs('admin.index') && request()->segment(3) === 'users' ? 'active' : '' }}">
                <i class="ri-team-line"></i>
                <span>المستخدمون</span>
            </a>
            
            <a href="{{ route('admin.create') }}" 
               class="nav-btn {{ request()->routeIs('admin.create') ? 'active' : '' }}">
                <i class="ri-user-add-line"></i>
                <span>إضافة مستخدم</span>
            </a>
            
        @endif
    </nav>
    
    {{-- Footer مع أزرار التحكم --}}
    <div class="sidebar-footer">
        @if(auth()->user()->role === 'teacher')
            <form action="{{ route('teacher.logout') }}" method="POST" style="width: 100%;">
                @csrf
                <button type="submit" class="nav-btn logout" style="width: 100%; margin: 0; border: none;">
                    <i class="ri-logout-box-r-line"></i><span>خروج</span>
                </button>
            </form>
        @else
            {{-- زر تبديل المظهر --}}
            <button class="nav-btn nav-toggle-theme" 
                    id="sidebarThemeToggle"
                    title="تبديل بين الوضع الفاتح والغامق">
                <i class="ri-moon-line theme-icon"></i>
                <span>المظهر</span>
            </button>
            
            {{-- زر تسجيل الخروج --}}
            <form method="POST" 
                  action="{{ route('logout') }}" 
                  class="logout-form">
                @csrf
                <button type="submit" class="nav-btn nav-logout" title="تسجيل الخروج الآمن">
                    <i class="ri-logout-box-line"></i>
                    <span>خروج</span>
                </button>
            </form>
        @endif
    </div>
</aside>

<style>
    /* ═════════════════════════════════════════════════════════════════════════ */
    /* SIDEBAR STYLES - أنماط شريط التنقل الموحد */
    /* ═════════════════════════════════════════════════════════════════════════ */
    
    :root {
        --sidebar-width: 240px;
        --gold: #C6A675;
        --gold-dark: #997722;
        --gold-light: rgba(198,166,117,0.14);
        --bg: #F4F6F8;
        --card-bg: #FFFFFF;
        --text-primary: #222B3D;
        --text-secondary: #5E6675;
        --text-muted: #7D8797;
        --danger: #D64545;
        --border: #E5E5EA;
        --shadow: 0 4px 24px rgba(0,0,0,0.04);
        --shadow-hover: 0 8px 32px rgba(0,0,0,0.08);
        --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        --radius-lg: 16px;
        --radius-md: 12px;
    }

    [data-theme="dark"] {
        --bg: #050505;
        --card-bg: #0F0F10;
        --text-primary: #F2F2F7;
        --text-secondary: #7D8797;
        --text-muted: #636366;
        --shadow: 0 4px 24px rgba(0,0,0,0.4);
    }

    /* ─── الـ Sidebar الرئيسي ─── */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--card-bg);
        position: fixed;
        right: 0;
        top: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        z-index: 200;
        box-shadow: -2px 0 12px rgba(0,0,0,0.03);
        border-left: 1px solid rgba(0,0,0,0.04);
        background: linear-gradient(180deg, var(--card-bg) 0%, rgba(196,150,58,0.02) 100%);
        transition: var(--transition);
        overflow-y: auto;
        overflow-x: hidden;
    }

    @if(auth()->check() && auth()->user()->role === 'teacher')
        :root {
            --sidebar-w: 300px;
            --sidebar-width: var(--sidebar-w);
            --sidebar-offset: 18px;
            --sidebar-bg: var(--theme-surface);
            --card-bg: var(--theme-surface);
            --text-primary: var(--text-primary);
            --text-secondary: var(--text-secondary);
            --text-muted: var(--text-muted);
            --shadow: 0 18px 50px rgba(0,0,0,0.35);
            --shadow-hover: 0 18px 60px rgba(0,0,0,0.45);
            --gold-light: rgba(196,150,58,0.14);
        }

        .sidebar {
            width: var(--sidebar-w);
            position: fixed;
            top: 24px;
            right: 18px;
            bottom: 24px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(24px);
            border-left: 1px solid rgba(255,214,122,0.16);
            border-top-left-radius: 32px;
            border-bottom-left-radius: 32px;
            display: flex;
            flex-direction: column;
            padding: 28px 22px;
            gap: 20px;
            z-index: 10;
            box-shadow: -16px 28px 70px rgba(0,0,0,0.28);
        }

        .sidebar-logo {
            padding: 30px 20px 24px;
            text-align: center;
            margin-bottom: 10px;
            position: relative;
            overflow: hidden;
        }

        .sidebar-logo::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .logo-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 12px;
            color: var(--gold);
            font-size: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gold-light);
            border-radius: 14px;
            position: relative;
            transition: var(--transition);
            box-shadow: 0 0 16px rgba(198,166,117,0.24);
            animation: float 3s ease-in-out infinite;
            overflow: hidden;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 30px;
            display: block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .logo-name {
            font-size: 19px;
            font-weight: 800;
            color: var(--gold);
            position: relative;
            z-index: 1;
        }

        .logo-sub {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav {
            flex: 1;
            display: grid;
            gap: 14px;
            overflow-y: auto;
            padding: 0;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            width: 100%;
            padding: 16px 22px;
            min-height: 62px;
            background: rgba(255,255,255,0.06);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--text-muted);
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            backdrop-filter: blur(14px);
        }

        .nav-btn i {
            font-size: 20px;
            color: rgba(255,214,122,0.92);
            flex-shrink: 0;
        }

        .nav-btn span {
            color: inherit;
            flex: 1;
            text-align: right;
        }

        .nav-btn:hover {
            background: rgba(255,214,122,0.08);
            color: var(--text-primary, #F9F9FB);
            border-color: rgba(255,214,122,0.18);
        }

        .nav-btn.active {
            background: rgba(255,214,122,0.18);
            color: #FFFFFF;
            border-color: rgba(255,214,122,0.32);
            box-shadow: 0 20px 40px rgba(255,214,122,0.14);
            backdrop-filter: blur(18px);
        }

        .nav-btn.logout {
            color: #FF6C63;
            background: rgba(214,69,69,0.12);
            border-color: rgba(255,59,48,0.18);
        }

        .sidebar-footer {
            margin-top: auto;
        }

        .sidebar-footer form {
            width: 100%;
        }
    @endif

    /* ─── شعار المنصة ─── */

    .sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(198,166,117,0.24);
        border-radius: 2px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(196,150,58,0.4);
    }

    @if(auth()->check() && auth()->user()->role !== 'teacher')
        /* ─── أزرار التنقل ─── */
        .nav-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            border: none;
            border-radius: var(--radius-lg);
            background: transparent;
            color: var(--text-secondary);
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: right;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            outline: none;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: var(--gold-light);
            transition: var(--transition);
            z-index: -1;
        }

        .nav-btn:hover::before {
            width: 100%;
        }

        .nav-btn i {
            font-size: 20px;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .nav-btn:hover {
            background: var(--gold-light);
            color: var(--text-primary);
        }

        .nav-btn:hover i {
            transform: scale(1.1);
        }

        {{-- الحالة النشطة --}}
        .nav-btn.active {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
            box-shadow: 0 6px 16px rgba(196,150,58,0.25);
            position: relative;
        }

        .nav-btn.active::before {
            width: 0;
        }

        .nav-btn.active i {
            color: #fff;
            transform: scale(1.15) rotate(-5deg);
        }

        {{-- زر تسجيل الخروج --}}
        .nav-logout {
            color: var(--danger);
            font-weight: 700;
        }

        .nav-logout:hover {
            background: rgba(214,69,69,0.14);
            color: var(--danger);
        }

        .nav-logout i {
            color: var(--danger);
        }

        {{-- زر تبديل المظهر --}}
        .nav-toggle-theme {
            margin-bottom: 8px;
        }

        /* ─── Footer ─── */
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .logout-form {
            width: 100%;
            margin: 0;
        }
    @endif

    /* ═════════════════════════════════════════════════════════════════════════ */
    /* RESPONSIVE DESIGN - التصميم الاستجابي */
    /* ═════════════════════════════════════════════════════════════════════════ */

    /* Sidebar backdrop overlay — visual only, pointer-events:none so touch goes through to sidebar */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        pointer-events: none;
    }
    .sidebar-backdrop.active {
        display: block;
    }

    @media (max-width: 1024px) {
        .sidebar {
            width: 72px;
            --sidebar-width: 72px;
            padding: 20px 8px;
        }

        .sidebar-logo {
            padding: 10px 4px 16px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            margin: 0 auto 6px;
        }

        .logo-icon img {
            max-height: 38px;
        }

        .nav-btn span,
        .logo-name,
        .logo-sub {
            display: none;
        }

        .nav-btn {
            justify-content: center;
            padding: 12px;
            gap: 0;
        }

        .nav-btn i {
            font-size: 1.4rem;
        }

        .sidebar-footer {
            padding-top: 10px;
            margin-top: 10px;
        }
    }

    .hamburger-btn {
        display: none;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 12px;
        background: rgba(255,255,255,0.06);
        color: var(--text-primary);
        font-size: 20px;
        cursor: pointer;
        transition: all 0.3s;
        flex-shrink: 0;
    }
    .hamburger-btn:hover { background: rgba(255,214,122,0.1); border-color: rgba(255,214,122,0.3); }

    /* iOS touch: remove 300ms delay on all nav buttons */
    .nav-btn {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        cursor: pointer;
    }
    .hamburger-btn {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    @media (max-width: 768px) {
        .hamburger-btn { display: flex; }

        .sidebar {
            position: fixed;
            transform: translateX(110%);
            visibility: hidden;
            pointer-events: none;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s;
            width: 260px;
            max-width: 80vw;
            right: 0;
            top: 0;
            bottom: 0;
            height: 100vh;
            z-index: 9999;
            border-left: none;
            border-radius: 0;
            padding: 24px 16px;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .nav-btn,
        .nav-btn.active {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .sidebar.sidebar-open {
            transform: translateX(0);
            visibility: visible;
            pointer-events: auto;
        }

        /* Restore full sidebar content when open on mobile */
        .sidebar.sidebar-open .nav-btn span,
        .sidebar.sidebar-open .logo-name,
        .sidebar.sidebar-open .logo-sub {
            display: block;
        }

        .sidebar.sidebar-open .nav-btn {
            justify-content: flex-start;
            padding: 12px 16px;
            gap: 12px;
        }

        .sidebar.sidebar-open .logo-icon {
            width: 64px;
            height: 64px;
        }

        .sidebar.sidebar-open .logo-icon img {
            max-height: 56px;
        }
    }

    /* إخفاء الـ Scrollbar في جميع المتصفحات */
    .sidebar,
    .sidebar-nav {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .sidebar::-webkit-scrollbar,
    .sidebar-nav::-webkit-scrollbar {
        width: 0;
        height: 0;
        background: transparent;
    }
</style>

<script>
    function getSidebarScrollKey() {
        return 'app-sidebar-scroll-top';
    }

    function restoreSidebarPosition(sidebar) {
        if (!sidebar) return;
        const saved = localStorage.getItem(getSidebarScrollKey());
        if (saved !== null) {
            sidebar.scrollTop = parseInt(saved, 10) || 0;
        }
        const activeItem = sidebar.querySelector('.nav-btn.active');
        if (activeItem) {
            activeItem.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        }
    }

    function bindSidebarScrollPersistence(sidebar) {
        if (!sidebar) return;
        sidebar.addEventListener('scroll', function() {
            localStorage.setItem(getSidebarScrollKey(), String(sidebar.scrollTop));
        });

        sidebar.querySelectorAll('.sidebar-nav .nav-btn').forEach(function(link) {
            link.addEventListener('click', function() {
                localStorage.setItem(getSidebarScrollKey(), String(sidebar.scrollTop));
            });
        });
    }

    function runPageSearch(query) {
        const normalized = query.trim().toLowerCase();
        const content = document.querySelector('.content');
        if (!content) {
            return;
        }

        const selectors = [
            '.card',
            '.item-card',
            '.exam-card',
            '.student-card',
            '.message-card',
            '.conversation-card',
            '.inquiry-card',
            '.list-item',
            '.course-card'
        ].join(',');

        const nodes = Array.from(content.querySelectorAll(selectors));

        if (normalized === '') {
            nodes.forEach(function(node) {
                node.style.display = '';
            });
            return;
        }

        let found = false;
        nodes.forEach(function(node) {
            const text = node.textContent.toLowerCase();
            const match = text.includes(normalized);
            node.style.display = match ? '' : 'none';
            if (match) {
                found = true;
            }
        });

        if (!found) {
            const headings = Array.from(content.querySelectorAll('h1, h2, h3, h4, h5, h6'));
            const matchedHeading = headings.find(function(heading) {
                return heading.textContent.toLowerCase().includes(normalized);
            });
            if (matchedHeading) {
                matchedHeading.classList.add('search-highlight');
                matchedHeading.scrollIntoView({ behavior: 'smooth', block: 'start' });
                window.setTimeout(function() {
                    matchedHeading.classList.remove('search-highlight');
                }, 1400);
                found = true;
            }
        }

        if (!found) {
            const anchors = Array.from(content.querySelectorAll('[id]'));
            const matchedAnchor = anchors.find(function(el) {
                return el.id.toLowerCase().includes(normalized);
            });
            if (matchedAnchor) {
                matchedAnchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    function bindTopbarSearch() {
        const searchInput = document.querySelector('.topbar .search-wrap input, .topbar .search-wrapper input');
        if (!searchInput) {
            return;
        }

        let timeoutId;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = window.setTimeout(function() {
                runPageSearch(searchInput.value);
            }, 200);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                runPageSearch(searchInput.value);
            }
        });
    }

    /**
     * تبديل المظهر (الفاتح والغامق)
     * Toggle Theme Function
     */
    function toggleTheme() {
        const html = document.documentElement;
        const isDark = html.getAttribute('data-theme') === 'dark';
        const newTheme = isDark ? 'light' : 'dark';
        
        // تطبيق التغيير
        html.setAttribute('data-theme', newTheme);
        
        // حفظ في localStorage
        localStorage.setItem('app-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        sessionStorage.setItem('app-theme', newTheme);
        sessionStorage.setItem('theme', newTheme);
        if (document.body) {
            document.body.setAttribute('data-theme', newTheme);
            document.body.classList.toggle('dark-mode', newTheme === 'dark');
        }
        
        // تحديث أيقونة المظهر
        updateThemeIcon();
    }

    /**
     * تحديث أيقونة المظهر
     */
    function updateThemeIcon() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const icon = document.querySelector('.theme-icon');
        if (icon) {
            icon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
        }
    }

    /**
     * تحميل المظهر المحفوظ عند فتح الصفحة
     */
    document.addEventListener('DOMContentLoaded', function() {
        updateThemeIcon();
        
        // حفظ واستعادة موقع الشريط الجانبي
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            restoreSidebarPosition(sidebar);
            bindSidebarScrollPersistence(sidebar);
        }

        // تبديل الـ Sidebar على الأجهزة المحمولة
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            if (sidebar) sidebar.classList.add('sidebar-open');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('sidebar-open');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('sidebar-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        // Close sidebar when tapping outside (backdrop has pointer-events:none so we use document)
        document.addEventListener('click', function(e) {
            if (!sidebar || !sidebar.classList.contains('sidebar-open')) return;
            var toggle = document.getElementById('sidebarToggle');
            if (!sidebar.contains(e.target) && (!toggle || !toggle.contains(e.target))) {
                closeSidebar();
            }
        }, true);

        // إغلاق الـ Sidebar عند الضغط على ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // Make openSidebar/closeSidebar globally available
        window.openSidebar = openSidebar;
        window.closeSidebar = closeSidebar;

        bindTopbarSearch();

        document.getElementById('sidebarThemeToggle')?.addEventListener('click', function() {
            if (typeof toggleTheme === 'function') toggleTheme();
        });
    });
</script>


