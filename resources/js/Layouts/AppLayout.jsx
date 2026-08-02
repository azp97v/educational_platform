import { useState, useEffect, useCallback } from 'react';
import { Link, usePage, router } from '@inertiajs/react';

const STUDENT_NAV = [
    { icon: 'ri-home-4-line',      label: 'sidebar.home',        href: '/student/dashboard' },
    { icon: 'ri-book-open-line',   label: 'sidebar.courses',     href: '/student/academy' },
    { icon: 'ri-file-list-line',   label: 'sidebar.exams',       href: '/student/exams' },
    { icon: 'ri-rewind-line',      label: 'sidebar.smart_rewind',href: '/student/smart-rewind' },
    { icon: 'ri-question-line',    label: 'sidebar.inquiries',   href: '/student/my-inquiries' },
    { icon: 'ri-bar-chart-2-line', label: 'sidebar.my_stats',    href: '/gamification/leaderboard' },
    { icon: 'ri-message-3-line',   label: 'sidebar.messages',    href: '/messaging' },
];

const TEACHER_NAV = [
    { icon: 'ri-home-4-line',      label: 'sidebar.home',              href: '/teacher' },
    { icon: 'ri-book-2-line',      label: 'sidebar.courses',           href: '/teacher/courses' },
    { icon: 'ri-price-tag-3-line', label: 'sidebar.categories',        href: '/teacher/categories' },
    { icon: 'ri-user-add-line',    label: 'sidebar.enrollment_requests',href: '/teacher/enrollment-requests' },
    { icon: 'ri-file-list-line',   label: 'sidebar.exams',             href: '/teacher/exams' },
    { icon: 'ri-bar-chart-2-line', label: 'sidebar.progress',          href: '/teacher/analytics' },
    { icon: 'ri-team-line',        label: 'sidebar.my_students',       href: '/teacher/students' },
    { icon: 'ri-award-line',       label: 'sidebar.certificates',      href: '/teacher/certificates/students' },
    { icon: 'ri-message-3-line',   label: 'sidebar.messages',          href: '/messaging' },
];

const ADMIN_NAV = [
    { icon: 'ri-home-4-line',      label: 'sidebar.home',      href: '/admin' },
    { icon: 'ri-team-line',        label: 'sidebar.users',     href: '/admin/users' },
    { icon: 'ri-book-2-line',      label: 'sidebar.courses',   href: '/admin/courses' },
    { icon: 'ri-message-3-line',   label: 'sidebar.messages',  href: '/messaging' },
    { icon: 'ri-settings-3-line',  label: 'sidebar.settings',  href: '/admin/settings' },
];

const NAV_BY_ROLE = { student: STUDENT_NAV, teacher: TEACHER_NAV, admin: ADMIN_NAV };

const TRANS = {
    ar: {
        'sidebar.home': 'الرئيسية', 'sidebar.courses': 'المسارات', 'sidebar.exams': 'الاختبارات',
        'sidebar.smart_rewind': 'إعادة ذكية', 'sidebar.inquiries': 'استفساراتي',
        'sidebar.my_stats': 'إحصائياتي', 'sidebar.messages': 'الرسائل',
        'sidebar.categories': 'التصنيفات', 'sidebar.enrollment_requests': 'طلبات الالتحاق',
        'sidebar.progress': 'التقدم والأداء', 'sidebar.my_students': 'طلابي',
        'sidebar.certificates': 'الشهادات', 'sidebar.users': 'المستخدمون',
        'sidebar.settings': 'الإعدادات', 'sidebar.platform_name': 'إجلال',
        'sidebar.platform_subtitle': 'المنصة التعليمية',
        'auth.logout': 'تسجيل الخروج', 'theme.dark': 'داكن', 'theme.light': 'فاتح',
    },
    en: {
        'sidebar.home': 'Home', 'sidebar.courses': 'Courses', 'sidebar.exams': 'Exams',
        'sidebar.smart_rewind': 'Smart Rewind', 'sidebar.inquiries': 'My Inquiries',
        'sidebar.my_stats': 'My Stats', 'sidebar.messages': 'Messages',
        'sidebar.categories': 'Categories', 'sidebar.enrollment_requests': 'Enrollment Requests',
        'sidebar.progress': 'Analytics', 'sidebar.my_students': 'My Students',
        'sidebar.certificates': 'Certificates', 'sidebar.users': 'Users',
        'sidebar.settings': 'Settings', 'sidebar.platform_name': 'Eglal',
        'sidebar.platform_subtitle': 'Educational Platform',
        'auth.logout': 'Logout', 'theme.dark': 'Dark', 'theme.light': 'Light',
    },
};

function useT() {
    const { locale } = usePage().props;
    const dict = TRANS[locale] ?? TRANS.ar;
    return (key) => dict[key] ?? key;
}

function NavLink({ item, current }) {
    const t = useT();
    const isActive = current.startsWith(item.href);
    return (
        <a
            href={item.href}
            className={`nav-btn${isActive ? ' active' : ''}`}
            onClick={(e) => { e.preventDefault(); router.visit(item.href); }}
        >
            <i className={item.icon} />
            <span>{t(item.label)}</span>
        </a>
    );
}

function Sidebar({ open, onClose }) {
    const { auth, locale } = usePage().props;
    const t = useT();
    const user = auth?.user;
    const role = user?.role ?? 'student';
    const nav = NAV_BY_ROLE[role] ?? STUDENT_NAV;
    const current = window.location.pathname;

    const [theme, setTheme] = useState(() => localStorage.getItem('app-theme') || 'light');

    const toggleTheme = useCallback(() => {
        setTheme(prev => {
            const next = prev === 'light' ? 'dark' : 'light';
            localStorage.setItem('app-theme', next);
            document.documentElement.setAttribute('data-theme', next);
            return next;
        });
    }, []);

    const logout = useCallback((e) => {
        e.preventDefault();
        router.post('/logout');
    }, []);

    return (
        <>
            {open && <div className="sidebar-backdrop" onClick={onClose} />}
            <aside className={`sidebar${open ? ' open' : ''}`} id="sidebar">
                <div className="sidebar-logo">
                    <div className="logo-icon">
                        <i className="ri-book-read-fill" />
                    </div>
                    <div className="logo-name">{t('sidebar.platform_name')}</div>
                    <div className="logo-sub">{t('sidebar.platform_subtitle')}</div>
                </div>

                <nav className="sidebar-nav">
                    {nav.map(item => (
                        <NavLink key={item.href} item={item} current={current} />
                    ))}
                </nav>

                <div className="sidebar-footer">
                    <div className="user-info">
                        <div className="user-avatar">
                            {user?.avatar
                                ? <img src={user.avatar} alt={user.name} />
                                : <i className="ri-user-line" />
                            }
                        </div>
                        <div className="user-meta">
                            <span className="user-name">{user?.name}</span>
                            <span className="user-role">{user?.email}</span>
                        </div>
                    </div>

                    <button className="nav-btn theme-toggle" onClick={toggleTheme}>
                        <i className={theme === 'dark' ? 'ri-sun-line' : 'ri-moon-line'} />
                        <span>{theme === 'dark' ? t('theme.light') : t('theme.dark')}</span>
                    </button>

                    <button className="nav-btn logout-btn" onClick={logout}>
                        <i className="ri-logout-box-r-line" />
                        <span>{t('auth.logout')}</span>
                    </button>
                </div>
            </aside>
        </>
    );
}

export default function AppLayout({ children, title }) {
    const { locale, flash } = usePage().props;
    const isRtl = locale === 'ar';
    const [sidebarOpen, setSidebarOpen] = useState(false);

    // Sync theme on mount
    useEffect(() => {
        const theme = localStorage.getItem('app-theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    }, []);

    // Close sidebar on route change
    useEffect(() => {
        setSidebarOpen(false);
    }, [window.location.pathname]);

    return (
        <div className="app" dir={isRtl ? 'rtl' : 'ltr'}>
            <Sidebar open={sidebarOpen} onClose={() => setSidebarOpen(false)} />

            <div className="content-wrapper">
                {/* Topbar */}
                <header className="topbar">
                    <button
                        className="hamburger-btn"
                        onClick={() => setSidebarOpen(prev => !prev)}
                        aria-label="Toggle menu"
                    >
                        <i className="ri-menu-line" />
                    </button>
                    {title && <h1 className="page-title">{title}</h1>}
                </header>

                {/* Flash messages */}
                {flash?.success && (
                    <div className="flash flash-success">
                        <i className="ri-checkbox-circle-line" />
                        <span>{flash.success}</span>
                    </div>
                )}
                {flash?.error && (
                    <div className="flash flash-error">
                        <i className="ri-error-warning-line" />
                        <span>{flash.error}</span>
                    </div>
                )}

                <main className="main-content">
                    {children}
                </main>
            </div>
        </div>
    );
}
