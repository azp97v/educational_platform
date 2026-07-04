<header class="admin-topbar">
    <div class="topbar-left">
        <a href="{{ route('profile.show') }}" class="icon-btn" title="الملف الشخصي"><i class="ri-user-line"></i></a>
        <button class="icon-btn" type="button" data-theme-toggle title="الوضع الليلي"><i class="ri-moon-line"></i></button>
        <a href="{{ route('notifications.index') }}" class="icon-btn" title="التنبيهات">
            <i class="ri-notification-3-line"></i>
            @php($adminUnread = auth()->user()->unreadNotifications()->count())
            @if($adminUnread > 0)
                <span class="admin-badge">{{ $adminUnread > 99 ? '99+' : $adminUnread }}</span>
            @endif
        </a>

        <div class="g-badge"><i class="ri-group-line"></i><span>{{ $adminStatsMini['totalUsers'] ?? 0 }} مستخدم</span></div>
        <div class="g-badge"><i class="ri-user-star-line"></i><span>{{ $adminStatsMini['activeUsers'] ?? 0 }} نشط</span></div>
    </div>

    <div class="topbar-right">
        <div class="topbar-title">
            <h1>@yield('page_title', 'لوحة الإدارة')</h1>
            <p>@yield('page_subtitle', 'تحكم شامل في المنصة')</p>
        </div>

        <label class="search-wrap" aria-label="بحث الإدارة">
            <i class="ri-search-line search-icon"></i>
            <input type="text" placeholder="ابحث في الإدارة...">
        </label>

        <div class="g-badge" id="admin-live-time"><i class="ri-time-line"></i><span>--:--</span></div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('[data-theme-toggle]')?.addEventListener('click', function() {
        if (typeof toggleTheme === 'function') toggleTheme();
    });
});
</script>
