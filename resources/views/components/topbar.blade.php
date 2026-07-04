<!--
    ═════════════════════════════════════════════════════════════════════════
    TOPBAR COMPONENT - شريط القمة
    ═════════════════════════════════════════════════════════════════════════

    المسؤوليات:
    ✓ عرض البحث
    ✓ عرض الإشعارات
    ✓ عرض معلومات المستخدم
    ✓ عرض شريط الحالة والإحصائيات
-->

<header class="topbar" id="topbar">
    @php
        $notificationItems = auth()->user()->notifications()->latest()->take(6)->get();
        $unreadCount = auth()->user()->unreadNotifications()->count();
    @endphp

    @if(auth()->user()->role === 'teacher')
        <div class="topbar-left">
            <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
                <i class="ri-moon-line" id="darkIcon"></i>
            </button>
            <div class="notification-wrapper">
                <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
                    <i class="ri-notification-3-line"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: {{ $unreadCount ? 'flex' : 'none' }}">{{ $unreadCount }}</span>
                </button>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="dropdown-header">
                        <h4>الإشعارات</h4>
                        <button class="btn-close" id="closeNotifications">×</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        @if($notificationItems->count())
                            @foreach($notificationItems as $notification)
                                @php $data = $notification->data; @endphp
                                <a href="{{ route('notifications.goto', $notification->id) }}" class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                    <div class="notification-icon"><i class="{{ !empty($data['icon']) ? $data['icon'] : 'ri-notification-3-line' }}"></i></div>
                                    <div class="notification-details">
                                        <div class="notification-title">{{ $data['title'] ?? 'إشعار جديد' }}</div>
                                        <div class="notification-text">{{ $data['message'] ?? '' }}</div>
                                        <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="empty-message">لا توجد إشعارات جديدة</p>
                        @endif
                    </div>
                    <div class="dropdown-footer">
                        <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm w-100">عرض الكل</a>
                    </div>
                </div>
            </div>
            <a href="{{ route('profile.show') }}" class="user-profile-btn" title="عرض الملف الشخصي">
                    <div class="u-info">
                        <div class="u-name">{{ auth()->user()->name }}</div>
                        <div class="u-role">معلم</div>
                    </div>
                    <div class="u-av">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                </a>
        </div>
        <div class="topbar-right">
            <div class="search-wrap">
                <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
                <i class="ri-search-line search-icon"></i>
            </div>
        </div>
    @else
        <!-- يسار -->
        <div class="topbar-left">
            <!-- Menu Toggle (Mobile) -->
            <button class="btn btn-icon sidebar-toggle" id="sidebarToggle">
                <i class="ri-menu-line"></i>
            </button>
        </div>

        <!-- وسط -->
        <div class="topbar-center">
            <!-- Search Bar -->
            <div class="search-wrapper">
                <i class="ri-search-line search-icon"></i>
                <input
                    type="text"
                    class="search-input"
                    placeholder="ابحث هنا..."
                    id="searchInput"
                >
            </div>
        </div>

        <!-- يمين -->
        <div class="topbar-right">
            <!-- User Stats (if student) -->
            @if(auth()->user()->role === 'student')
                <div class="stats-group">
                    <div class="stat-item">
                        <i class="ri-star-line"></i>
                        <span id="userXP">{{ auth()->user()->points ?? 0 }} نقطة</span>
                    </div>
                    <div class="stat-item">
                        <i class="ri-flame-line"></i>
                        <span id="userStreak">
                            @php
                                $streakService = new \App\Services\StreakService();
                                $currentStreak = $streakService->getCurrentStreak(auth()->user());
                            @endphp
                            {{ $currentStreak }} يوم
                        </span>
                    </div>
                </div>
            @endif

            <!-- Notifications -->
            <div class="notification-wrapper">
                <button class="btn btn-icon icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
                    <i class="ri-notification-3-line"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: {{ $unreadCount ? 'flex' : 'none' }}">{{ $unreadCount }}</span>
                </button>

                <!-- Notification Dropdown -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="dropdown-header">
                        <h4>الإشعارات</h4>
                        <button class="btn-close" id="closeNotifications">×</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        @if($notificationItems->count())
                            @foreach($notificationItems as $notification)
                                @php $data = $notification->data; @endphp
                                <a href="{{ route('notifications.goto', $notification->id) }}" class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                    <div class="notification-icon"><i class="{{ !empty($data['icon']) ? $data['icon'] : 'ri-notification-3-line' }}"></i></div>
                                    <div class="notification-details">
                                        <div class="notification-title">{{ $data['title'] ?? 'إشعار جديد' }}</div>
                                        <div class="notification-text">{{ $data['message'] ?? '' }}</div>
                                        <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="empty-message">لا توجد إشعارات جديدة</p>
                        @endif
                    </div>
                    <div class="dropdown-footer">
                        <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm w-100">عرض الكل</a>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="user-profile">
                <!-- User Avatar -->
                <button class="btn-user-avatar" id="userProfileBtn">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" alt="{{ auth()->user()->name }}" class="avatar-image">
                    @else
                        <div class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                    @endif
                </button>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="dropdown-header">
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">
                                @switch(auth()->user()->role)
                                    @case('admin')
                                        مسؤول النظام
                                        @break
                                    @case('teacher')
                                        معلم
                                        @break
                                    @case('student')
                                        طالب
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-body">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="ri-user-line"></i>
                            <span>الملف الشخصي</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    </div>

                    <div class="dropdown-footer">
                        <form method="POST" action="
                            @if(auth()->user()->role === 'teacher')
                                {{ route('teacher.logout') }}
                            @else
                                {{ route('logout') }}
                            @endif
                        " style="width: 100%;">
                            @csrf
                            <button type="submit" class="dropdown-item danger w-100" title="تسجيل الخروج الآمن">
                                <i class="ri-logout-box-line"></i>
                                <span>تسجيل خروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</header>

@if(auth()->check())
<script>
  const activityPingRoute = '{{ route('activity.ping') }}';
  function sendActivityPing() {
    fetch(activityPingRoute, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      keepalive: true,
      body: JSON.stringify({ ping: true }),
    }).catch(() => {
      // تهميش أي خطأ في الخلفية، الهدف فقط الحفاظ على الجلسة نشطة
    });
  }

  sendActivityPing();
  setInterval(sendActivityPing, 60000);
</script>
@endif

<style>
    /**
     * Topbar Styles
     */
    .topbar {
        height: var(--topbar-height);
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--bg-tertiary);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 var(--space-2xl);
        gap: var(--space-xl);
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: var(--shadow-sm);
        overflow: visible;
    }

    .topbar-left,
    .topbar-right {
        display: flex;
        align-items: center;
        gap: var(--space-lg);
    }

    .topbar-center {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        max-width: 500px;
    }

    @if(auth()->check() && auth()->user()->role === 'teacher')
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--topbar-h);
            background: transparent;
            backdrop-filter: blur(18px);
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            animation: slideDown 0.5s ease;
            box-shadow: none;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-profile-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 999px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.16);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            min-width: 200px;
            text-decoration: none;
            color: inherit;
        }

        .user-profile-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(198,166,117,0.14) 100%);
            opacity: 0;
            transition: var(--transition);
        }

        .user-profile-btn:hover {
            border-color: rgba(255,214,122,0.6);
            box-shadow: 0 14px 34px rgba(0,0,0,0.2);
            transform: translateY(-2px);
            background: rgba(255,255,255,0.14);
        }

        .user-profile-btn:hover::before {
            opacity: 1;
        }

        .user-profile-btn .u-info {
            text-align: right;
            position: relative;
            z-index: 1;
        }

        .user-profile-btn .u-name {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-primary);
            background: linear-gradient(135deg, var(--gold, #C6A675), var(--gold-dark, #997722));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: var(--transition);
        }

        .user-profile-btn:hover .u-name {
            text-shadow: 0 0 8px var(--theme-gold-aura, rgba(196,150,58,0.3));
        }

        .user-profile-btn .u-role {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .user-profile-btn .u-av {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--gold-light, rgba(255,214,122,1)), var(--gold, #C6A675));
            color: var(--text-primary, #111);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 4px 10px rgba(255,214,122,0.24);
            transition: var(--transition);
            position: relative;
            z-index: 1;
        }

        .user-profile-btn:hover .u-av {
            transform: scale(1.15) rotate(-5deg);
            box-shadow: 0 6px 16px rgba(196,150,58,0.35);
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 50%;
            background: var(--card-bg);
            color: var(--text-secondary);
            font-size: 19px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            box-shadow: var(--shadow);
            position: relative;
            overflow: visible;
        }

        .icon-btn::before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            background: var(--gold);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: var(--transition);
            z-index: -1;
            opacity: 0.15;
        }

        .icon-btn:hover::before {
            width: 100%;
            height: 100%;
        }

        .icon-btn:hover {
            color: var(--gold);
            border-color: var(--gold);
            transform: scale(1.08);
            box-shadow: 0 0 16px rgba(196,150,58,0.3);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            justify-content: flex-end;
            min-width: 0;
        }

        .search-wrap {
            width: min(100%, 400px);
            min-width: 0;
            position: relative;
            display: block;
            overflow: hidden;
        }

        .search-wrap::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: radial-gradient(circle at top left, rgba(255,214,122,0.24), transparent 55%);
            opacity: 0.8;
            pointer-events: none;
            filter: blur(14px);
            transition: opacity 0.3s ease;
        }

        .search-wrap:hover::before {
            opacity: 1;
        }

        .search-wrap input {
            width: 100%;
            display: block;
            box-sizing: border-box;
            padding: 12px 48px 12px 16px;
            height: 44px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 40px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.08), 0 12px 30px rgba(0,0,0,0.18);
            transition: var(--transition);
            min-width: 0;
        }

        .search-wrap input::placeholder {
            color: var(--text-muted);
            font-weight: 500;
        }

        .search-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-light);
            transform: scale(1.02);
        }

        .search-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
            pointer-events: none;
            transition: var(--transition);
        }

        .topbar-center,
        .stats-group,
        .user-profile {
            display: none;
        }
        .notification-wrapper {
            display: block;
        }

        @media (max-width: 1024px) {
            .topbar {
                padding: 0 20px;
            }

            .search-wrap {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px 16px;
            }

            .topbar-left,
            .topbar-right {
                width: 100%;
                justify-content: space-between;
            }

            .search-wrap {
                order: 3;
                margin-top: 12px;
            }
        }
    @endif

    /* Search Box */
    .search-wrapper {
        position: relative;
        width: 100%;
    }

    .search-input {
        width: 100%;
        padding: var(--space-md) var(--space-lg) var(--space-md) var(--space-2xl);
        border: 1px solid var(--bg-tertiary);
        border-radius: var(--radius-lg);
        font-size: 13px;
        color: var(--text-primary);
        background-color: var(--bg-primary);
        transition: var(--transition-fast);
    }

    .search-input::placeholder {
        color: var(--text-tertiary);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--color-gold);
        box-shadow: 0 0 0 3px var(--color-gold-light);
    }

    .search-icon {
        position: absolute;
        right: var(--space-lg);
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-tertiary);
        font-size: 16px;
        pointer-events: none;
    }

    .topbar .search-wrap {
        width: min(100%, 400px);
        min-width: 0;
        position: relative;
        display: block;
    }

    .topbar .search-wrap input {
        padding-right: 48px;
        padding-left: 16px;
        height: 44px;
        box-sizing: border-box;
    }

    .topbar .search-wrap .search-icon {
        right: 16px;
        font-size: 18px;
    }

    /* Stats Group */
    .stats-group {
        display: flex;
        gap: var(--space-lg);
        padding-right: var(--space-lg);
        border-right: 1px solid var(--bg-tertiary);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .stat-item i {
        font-size: 16px;
        color: var(--color-gold);
    }

    /* Notifications */
    .notification-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        z-index: 1100;
        overflow: visible !important;
    }

    .notification-btn {
        position: relative;
        background: var(--theme-soft);
        color: var(--text-secondary);
        overflow: visible !important;
        z-index: 1210;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        left: auto;
        min-width: 24px;
        height: 24px;
        padding: 0 6px;
        border-radius: 50%;
        background: var(--danger, #D64545);
        color: #fff;
        display: flex !important;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        pointer-events: none;
        box-shadow: 0 3px 10px rgba(214, 69, 69, 0.45);
        z-index: 1200;
        border: 2px solid var(--theme-surface, rgba(18,22,34,0.96));
    }

    .notification-dropdown {
        position: fixed;
        top: auto;
        right: auto;
        width: 320px;
        max-width: calc(100vw - 24px);
        background: var(--theme-surface);
        border: 1px solid var(--theme-border);
        border-radius: 22px;
        box-shadow: 0 28px 80px rgba(0,0,0,0.18);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 99999;
        backdrop-filter: blur(18px);
    }

    [data-theme="dark"] .notification-dropdown {
        background: rgba(10, 18, 38, 0.96);
        border-color: rgba(255,255,255,0.12);
        box-shadow: 0 28px 80px rgba(0,0,0,0.32);
    }

    .notification-dropdown.active {
        display: flex;
    }

    .notification-dropdown .dropdown-header,
    .notification-dropdown .dropdown-footer {
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .notification-dropdown .dropdown-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: var(--theme-text);
    }

    .notification-dropdown .btn-close {
        background: var(--theme-soft);
        border: none;
        color: var(--theme-text);
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
        width: 36px;
        height: 36px;
        border-radius: 14px;
        transition: background 0.2s ease;
    }

    .notification-dropdown .btn-close:hover {
        background: var(--theme-soft-2);
    }

    .notification-dropdown .notification-list {
        max-height: 320px;
        overflow-y: auto;
        background: transparent;
        padding: 8px 0;
    }

    .notification-dropdown .notification-item {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        text-decoration: none;
        color: inherit;
        align-items: flex-start;
    }

    .notification-dropdown .notification-item:last-child {
        border-bottom: none;
    }

    .notification-dropdown .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background: rgba(255,214,122,0.12);
        display: grid;
        place-items: center;
        color: var(--color-gold);
        font-size: 18px;
        flex-shrink: 0;
    }

    .notification-dropdown .notification-details {
        min-width: 0;
        flex: 1;
    }

    .notification-dropdown .notification-title {
        margin-bottom: 4px;
        font-size: 13px;
        font-weight: 700;
        color: var(--theme-text);
    }

    .notification-dropdown .notification-text {
        font-size: 13px;
        color: var(--theme-text-soft);
        margin-bottom: 6px;
    }

    .notification-dropdown .notification-time {
        font-size: 11px;
        color: var(--theme-muted);
    }

    .notification-dropdown .empty-message {
        padding: 16px;
        text-align: center;
        color: var(--theme-text-soft);
        font-size: 13px;
    }

    .bell-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: min(340px, calc(100% - 48px));
        background: rgba(32, 35, 43, 0.96);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 16px 18px;
        box-shadow: 0 22px 48px rgba(0,0,0,0.32);
        opacity: 0;
        transform: translateY(20px);
        transition: transform 0.35s ease, opacity 0.35s ease;
        z-index: 9999;
        pointer-events: none;
    }

    .bell-toast.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .bell-toast strong {
        display: block;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .bell-toast p {
        margin: 0;
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    .dropdown-header {
        padding: var(--space-lg);
        border-bottom: 1px solid var(--bg-tertiary);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-header h4 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--text-tertiary);
    }

    .notification-list {
        flex: 1;
        padding: var(--space-lg) 0;
        min-height: 120px;
    }

    .notification-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: var(--space-md) var(--space-lg);
        border-bottom: 1px solid var(--bg-tertiary);
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .notification-item:hover {
        background-color: var(--bg-primary);
    }

    .notification-item.unread {
        background-color: rgba(255,214,122,0.08);
        border-color: rgba(255,214,122,0.22);
    }

    .notification-icon {
        min-width: 42px;
        min-height: 42px;
        display: grid;
        place-items: center;
        background: rgba(198,166,117,0.14);
        border-radius: 14px;
        color: var(--color-gold);
        font-size: 18px;
    }

    .notification-details {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .notification-text {
        color: var(--text-secondary);
        font-size: 13px;
        line-height: 1.4;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .notification-time {
        margin-top: 8px;
        color: var(--text-tertiary);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .empty-message {
        text-align: center;
        color: var(--text-tertiary);
        padding: var(--space-2xl);
        margin: 0;
        font-size: 13px;
    }

    .dropdown-footer {
        padding: var(--space-lg);
        border-top: 1px solid var(--bg-tertiary);
    }

    /* User Profile */
    .user-profile {
        position: relative;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        background-color: var(--color-gold);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
    }

    .avatar-image {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        object-fit: cover;
        cursor: pointer;
        border: 2px solid var(--color-gold);
    }

    .btn-user-avatar {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
    }

    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 240px;
        background-color: var(--bg-secondary);
        border: 1px solid var(--bg-tertiary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        margin-top: var(--space-md);
        display: none;
        z-index: 200;
        overflow: hidden;
    }

    .profile-dropdown.active {
        display: flex;
        flex-direction: column;
    }

    .profile-dropdown .dropdown-header {
        padding: var(--space-lg);
        background-color: var(--bg-primary);
    }

    .user-info {
        text-align: right;
    }

    .user-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--space-xs);
    }

    .user-role {
        font-size: 12px;
        color: var(--text-tertiary);
    }

    .dropdown-body {
        padding: var(--space-sm) 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: var(--space-md);
        padding: var(--space-md) var(--space-lg);
        color: var(--text-secondary);
        text-decoration: none;
        border: none;
        background: none;
        cursor: pointer;
        width: 100%;
        text-align: right;
        font-size: 14px;
        transition: var(--transition-fast);
    }

    .dropdown-item:hover {
        background-color: var(--bg-primary);
        color: var(--color-gold);
    }

    .dropdown-item.danger {
        color: var(--color-danger);
    }

    .dropdown-item.danger:hover {
        background-color: rgba(214, 69, 69, 0.14);
    }

    .dropdown-divider {
        height: 1px;
        background-color: var(--bg-tertiary);
        margin: var(--space-sm) 0;
    }

    /* Sidebar Toggle (Mobile) */
    .sidebar-toggle {
        display: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .topbar {
            padding: 0 var(--space-lg);
        }

        .search-wrapper {
            display: none;
        }

        .sidebar-toggle {
            display: flex;
        }
    }

    @media (max-width: 768px) {
        .topbar {
            height: 60px;
            padding: 0 var(--space-lg);
        }

        .stats-group {
            display: none;
        }

        .topbar-center {
            display: none;
        }
    }

    /* ===== HIDE SCROLLBAR ===== */
    ::-webkit-scrollbar {
        display: none;
    }

    .notification-dropdown {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    [data-theme="light"] .notification-dropdown,
    body[data-theme="light"] .notification-dropdown {
        background: #FFFFFF !important;
        border-color: #DFE5EC !important;
        box-shadow: 0 16px 34px rgba(34, 43, 61, 0.12) !important;
    }
    [data-theme="light"] .notification-dropdown .notification-item,
    body[data-theme="light"] .notification-dropdown .notification-item {
        border-bottom-color: #E7ECF2 !important;
    }
    [data-theme="light"] .notification-dropdown .notification-item.unread,
    body[data-theme="light"] .notification-dropdown .notification-item.unread {
        background: rgba(198, 166, 117, 0.08) !important;
        border-right: 3px solid #C6A675 !important;
    }
    [data-theme="light"] .notification-dropdown .notification-title,
    [data-theme="light"] .notification-dropdown .dropdown-header h4,
    [data-theme="light"] .notification-dropdown .btn-close,
    body[data-theme="light"] .notification-dropdown .notification-title,
    body[data-theme="light"] .notification-dropdown .dropdown-header h4,
    body[data-theme="light"] .notification-dropdown .btn-close {
        color: #222B3D !important;
    }
    [data-theme="light"] .notification-dropdown .notification-text,
    body[data-theme="light"] .notification-dropdown .notification-text {
        color: #5E6675 !important;
    }
    [data-theme="light"] .notification-dropdown .notification-time,
    body[data-theme="light"] .notification-dropdown .notification-time {
        color: #7D8797 !important;
    }
    [data-theme="light"] .notification-dropdown .btn-close,
    body[data-theme="light"] .notification-dropdown .btn-close {
        background: #E7ECF2 !important;
    }
    [data-theme="light"] .notification-dropdown .btn-close:hover,
    body[data-theme="light"] .notification-dropdown .btn-close:hover {
        background: #D6DDE6 !important;
    }
    [data-theme="light"] .bell-toast,
    body[data-theme="light"] .bell-toast {
        background: #FFFFFF !important;
        border-color: #DFE5EC !important;
        color: #222B3D !important;
    }
    [data-theme="light"] .bell-toast p,
    body[data-theme="light"] .bell-toast p {
        color: #5E6675 !important;
    }

    /* ═══════════════════════════════════════════════════════
       RESPONSIVE ADDITIONS - إضافات الاستجابة
    ═══════════════════════════════════════════════════════ */

    /* Teacher topbar responsive */
    @if(auth()->check() && auth()->user()->role === 'teacher')
    @media (max-width: 768px) {
        .topbar {
            padding: 0 12px !important;
            gap: 8px !important;
            height: 60px !important;
        }
        .topbar-left {
            gap: 8px !important;
        }
        .topbar-right {
            display: none !important;
        }
        .user-profile-btn {
            min-width: unset !important;
            padding: 8px 10px !important;
        }
        .user-profile-btn .u-info {
            display: none !important;
        }
        .search-wrap {
            display: none !important;
        }
        .icon-btn {
            width: 36px !important;
            height: 36px !important;
            font-size: 16px !important;
        }
    }
    @media (max-width: 480px) {
        .topbar-left { gap: 6px !important; }
        .icon-btn { width: 34px !important; height: 34px !important; font-size: 15px !important; }
    }
    @endif

    /* Non-teacher topbar additional responsive */
    @if(!auth()->check() || auth()->user()->role !== 'teacher')
    @media (max-width: 768px) {
        .topbar {
            padding: 0 12px;
            height: 58px;
        }
        .topbar-left {
            gap: 8px;
        }
        .search-wrap {
            display: none;
        }
        .icon-btn {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
    }
    @media (max-width: 480px) {
        .topbar { padding: 0 10px; height: 54px; }
        .icon-btn { width: 32px; height: 32px; font-size: 14px; }
    }
    @endif
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!($skipBellJs ?? false))
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const closeNotifications = document.getElementById('closeNotifications');
        @endif
        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const searchInput = document.getElementById('searchInput');
        const sidebarToggle = document.getElementById('sidebarToggle');

        @if(!($skipBellJs ?? false))
        // Toggle Notifications
        notificationBtn?.addEventListener('click', function(event) {
            event.stopPropagation();
            if (notificationDropdown) {
                notificationDropdown.classList.toggle('active');
                if (notificationDropdown.classList.contains('active')) {
                    const rect = notificationBtn.getBoundingClientRect();
                    notificationDropdown.style.top = (rect.bottom + 12) + 'px';
                    const isRtl = document.dir === 'rtl';
                    if (isRtl) {
                        notificationDropdown.style.left = Math.max(10, rect.left - 320 + rect.width) + 'px';
                        notificationDropdown.style.right = 'auto';
                    } else {
                        notificationDropdown.style.right = Math.max(10, window.innerWidth - rect.right) + 'px';
                        notificationDropdown.style.left = 'auto';
                    }
                }
            }
        });

        closeNotifications?.addEventListener('click', function(event) {
            event.stopPropagation();
            notificationDropdown?.classList.remove('active');
        });
        @endif

        // Toggle Profile Menu
        userProfileBtn?.addEventListener('click', function(event) {
            event.stopPropagation();
            profileDropdown?.classList.toggle('active');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            @if(!($skipBellJs ?? false))
            if (!event.target.closest('.notification-wrapper')) {
                notificationDropdown?.classList.remove('active');
            }
            @endif
            if (!event.target.closest('.user-profile')) {
                profileDropdown?.classList.remove('active');
            }
        });

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value;
                if (typeof window.runPageSearch === 'function') {
                    window.runPageSearch(query);
                }
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (typeof window.runPageSearch === 'function') {
                        window.runPageSearch(e.target.value);
                    }
                }
            });
        }

        // Sidebar toggle on mobile
        sidebarToggle?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('sidebar-open');
        });

        document.getElementById('darkBtn')?.addEventListener('click', function() {
            if (typeof toggleDark === 'function') toggleDark();
        });
    });

    @if(!($skipBellJs ?? false))
    // Live notification polling and bell toast
    const notificationFetchRoute = '{{ route('notifications.fetch') }}';
    let latestNotificationId = {{ $notificationItems->first()?->id ?? 0 }};

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    function renderNotificationItems(items) {
        const list = document.getElementById('notificationList');
        if (!list) return;

        if (!items.length) {
            list.innerHTML = '<p class="empty-message">لا توجد إشعارات جديدة</p>';
            return;
        }

        const iconMap = {
            'enrollment': 'ri-user-add-line',
            'inquiry': 'ri-question-line',
            'message': 'ri-message-2-line',
            'chat': 'ri-message-2-line',
            'question': 'ri-chat-3-line',
            'answer': 'ri-chat-3-line',
            'support': 'ri-headset-line',
            'announcement': 'ri-notification-3-line',
            'alert': 'ri-alert-line',
            'system': 'ri-notification-3-line',
            'course': 'ri-book-open-line',
            'lesson': 'ri-book-2-line',
            'exam': 'ri-survey-line',
            'achievement': 'ri-medal-line',
            'attendance': 'ri-checkbox-circle-line',
            'payment': 'ri-wallet-line',
            'general': 'ri-notification-3-line'
        };

        const fallbackEmoji = {
            'enrollment': '👥',
            'inquiry': '❓',
            'message': '💬',
            'chat': '💬',
            'question': '❓',
            'answer': '💬',
            'support': '🎧',
            'announcement': '📢',
            'alert': '⚠️',
            'system': '🔔',
            'course': '📖',
            'lesson': '📘',
            'exam': '📝',
            'achievement': '🏆',
            'attendance': '✅',
            'payment': '💰',
            'general': '📬'
        };

        list.innerHTML = items.map(item => {
            const iconClass = item.icon || iconMap[item.category] || iconMap['general'];
            const emoji = fallbackEmoji[item.category] || fallbackEmoji['general'];
            return `
                <a href="${item.url}" class="notification-item ${item.read_at ? '' : 'unread'}">
                    <div class="notification-icon"><i class="${iconClass}" data-fallback="${emoji}"></i></div>
                    <div class="notification-details">
                        <div class="notification-title">${escapeHtml(item.title)}</div>
                        <div class="notification-text">${escapeHtml(item.message)}</div>
                        <div class="notification-time">${escapeHtml(item.created_at)}</div>
                    </div>
                </a>
            `;
        }).join('');

        // Apply fallback to any icons that didn't load
        setTimeout(() => {
            const icons = list.querySelectorAll('.notification-icon i');
            icons.forEach(icon => {
                const rect = icon.getBoundingClientRect();
                if (rect.width === 0 || icon.textContent === '') {
                    const fallback = icon.getAttribute('data-fallback') || '📬';
                    icon.textContent = fallback;
                    icon.style.fontFamily = 'inherit';
                    icon.style.fontSize = '1.3rem';
                }
            });
        }, 100);
    }

    function showBellToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'bell-toast';
        toast.innerHTML = `
            <strong>${escapeHtml(notification.title)}</strong>
            <p>${escapeHtml(notification.message)}</p>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('visible'), 50);
        setTimeout(() => {
            toast.classList.remove('visible');
            setTimeout(() => toast.remove(), 400);
        }, 4200);
    }

    function refreshNotifications() {
        fetch(notificationFetchRoute, {
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    return;
                }

                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    badge.textContent = data.unread_count;
                    badge.style.display = data.unread_count ? 'flex' : 'none';
                }

                renderNotificationItems(data.notifications);

                if (data.latest_id && data.latest_id > latestNotificationId) {
                    const newNotification = data.notifications.find(item => item.id === data.latest_id);
                    if (newNotification) {
                        showBellToast(newNotification);
                    }
                    latestNotificationId = data.latest_id;
                }
            })
            .catch(() => {
                // If polling fails, we keep the UI stable.
            });
    }

    // Initial refresh when page loads
    refreshNotifications();

    // Refresh every 15 seconds
    setInterval(refreshNotifications, 15000);

    // Refresh when page becomes visible (user returns from another tab/page)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden === false) {
            refreshNotifications();
        }
    });

    // Refresh when page focus changes
    window.addEventListener('focus', function() {
        refreshNotifications();
    });

    // Also listen for navigation events (in case of navigation within SPA)
    window.addEventListener('popstate', function() {
        refreshNotifications();
    });

    // Reverb WebSocket: instant bell refresh when a notification arrives
    @auth
    if (typeof window.Echo !== 'undefined') {
        try {
            window.Echo.private('user.{{ auth()->id() }}')
                .listen('.notification.new', function() {
                    refreshNotifications();
                });
        } catch (e) {}
    }
    @endauth
    @endif

    function toggleDark() {
        const html = document.documentElement;
        const isDark = html.getAttribute('data-theme') === 'dark';
        const newTheme = isDark ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        localStorage.setItem('app-theme', newTheme);
        sessionStorage.setItem('theme', newTheme);
        sessionStorage.setItem('app-theme', newTheme);
        if (document.body) {
            document.body.setAttribute('data-theme', newTheme);
            document.body.classList.toggle('dark-mode', newTheme === 'dark');
        }
    }
</script>



