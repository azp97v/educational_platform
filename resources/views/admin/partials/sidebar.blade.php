<aside class="admin-sidebar">
    <div class="admin-brand">
        <div class="admin-logo-wrap">
            @if(file_exists(public_path('images/logo/logo.png')))
                <img src="{{ asset('images/logo/logo.png?v=' . time()) }}" alt="جمعية إجلال" class="admin-logo">
            @else
                <i class="ri-book-mark-fill"></i>
            @endif
        </div>
        <div class="admin-brand-name">جمعية إجلال</div>
        <div class="admin-brand-sub">لوحة إدارة المنصة</div>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.index') }}" class="nav-btn {{ request()->routeIs('admin.index') ? 'active' : '' }}"><i class="ri-layout-grid-line"></i><span>لوحة التحكم</span></a>
        <a href="{{ route('admin.analytics') }}" class="nav-btn {{ request()->routeIs('admin.analytics') ? 'active' : '' }}"><i class="ri-line-chart-line"></i><span>التحليلات</span></a>
        <a href="{{ route('admin.liveops') }}" class="nav-btn {{ request()->routeIs('admin.liveops') ? 'active' : '' }}"><i class="ri-pulse-line"></i><span>المراقبة الحية</span></a>
        <a href="{{ route('admin.activity') }}" class="nav-btn {{ request()->routeIs('admin.activity') ? 'active' : '' }}"><i class="ri-time-line"></i><span>سجل النشاط</span></a>
        <a href="{{ route('admin.finance') }}" class="nav-btn {{ request()->routeIs('admin.finance') ? 'active' : '' }}"><i class="ri-funds-line"></i><span>المالية</span></a>
        <a href="{{ route('admin.rbac') }}" class="nav-btn {{ request()->routeIs('admin.rbac') ? 'active' : '' }}"><i class="ri-lock-password-line"></i><span>الصلاحيات</span></a>
        <a href="{{ route('admin.settings') }}" class="nav-btn {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i class="ri-settings-3-line"></i><span>الإعدادات</span></a>
        <a href="{{ route('admin.create') }}" class="nav-btn {{ request()->routeIs('admin.create') ? 'active' : '' }}"><i class="ri-user-add-line"></i><span>إنشاء مستخدم</span></a>
    </nav>

    <div class="admin-sidebar-footer">
        <div class="admin-live-chip"><span></span>وضع المراقبة المباشر</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-btn logout"><i class="ri-logout-box-r-line"></i><span>تسجيل الخروج</span></button>
        </form>
    </div>
</aside>
