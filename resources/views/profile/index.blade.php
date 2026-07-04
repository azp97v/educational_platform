<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding: 0;
            color: var(--text-primary);
        }

        body.pref-large-text {
            font-size: 115%;
        }

        body.pref-reduce-motion *,
        body.pref-reduce-motion *::before,
        body.pref-reduce-motion *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }

        /* Topbar Styles */
        .topbar {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: linear-gradient(135deg, rgba(10, 20, 44, 0.92), rgba(8, 16, 36, 0.96));
            border-bottom: 1px solid rgba(198, 117, 46, 0.22);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-wrap {
            flex: 1;
            max-width: 450px;
            position: relative;
        }

        .search-wrap input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(198,117,46,0.25);
            border-radius: 20px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }

        .search-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 2px var(--gold-light);
        }

        .search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.03);
            color: #d9deea;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .icon-btn:hover {
            color: var(--gold);
            background: var(--gold-light);
        }

        .g-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--bg);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            color: var(--text-primary);
            transition: all 0.6s ease;
        }

        .g-xp i {
            color: var(--gold);
            font-size: 14px;
        }

        .g-streak {
            transition: all 0.6s ease;
        }

        .g-streak i {
            color: #FF9500;
            font-size: 14px;
            animation: floatFlame 3s ease-in-out infinite;
            transition: all 0.6s ease;
        }

        .g-streak.active i {
            animation: floatFlame 3s ease-in-out infinite, glowPulse 2.5s ease-in-out infinite;
            filter: drop-shadow(0 0 12px rgba(255,149,0,0.6));
        }

        .g-streak.inactive i {
            color: #999;
            animation: none;
            filter: drop-shadow(0 0 4px rgba(153,153,153,0.3));
            opacity: 0.7;
        }

        @keyframes floatFlame {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes glowPulse {
            0%, 100% {
                filter: drop-shadow(0 0 12px rgba(255,149,0,0.6));
            }
            50% {
                filter: drop-shadow(0 0 18px rgba(255,149,0,0.9)) drop-shadow(0 0 25px rgba(255,149,0,0.4));
            }
        }

        .u-av {
            width: 40px;
            height: 40px;
            background: var(--gold);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
        }

        .u-av-img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid var(--gold);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card-bg);
            color: var(--gold);
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 30px;
            transition: var(--transition);
            border: 1px solid var(--gold-light);
            box-shadow: var(--shadow);
        }

        .back-button:hover {
            background: var(--card-bg);
            box-shadow: var(--shadow-hover);
            transform: translateX(-5px);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(52, 199, 89, 0.1), rgba(52, 199, 89, 0.05));
            border-color: rgba(52, 199, 89, 0.2);
            color: var(--success);
        }

        .profile-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .profile-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gold-light);
            animation: fadeIn 0.5s ease;
        }

        .settings-panel {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gold-light);
            animation: fadeIn 0.5s ease 0.1s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 70px;
            overflow: hidden;
            border: 6px solid var(--gold);
            box-shadow: 0 20px 40px rgba(198, 117, 46, 0.3);
            position: relative;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-status {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 15px 0;
        }

        .profile-role {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gold-light);
            color: var(--gold);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid var(--gold-light);
            margin-bottom: 15px;
        }

        .profile-email {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .profile-email i {
            margin-left: 6px;
            color: var(--gold);
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--gold);
        }

        .info-section-title i {
            font-size: 20px;
            color: var(--gold);
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: var(--gold-light);
            border-radius: 12px;
            margin-bottom: 10px;
            border-left: 4px solid var(--gold);
            transition: var(--transition);
        }

        .info-item:hover {
            background: var(--gold-light);
            transform: translateX(5px);
        }

        .info-label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
            text-align: left;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 25px 20px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(198, 117, 46, 0.2);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(198, 117, 46, 0.3);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 13px;
            opacity: 0.95;
            font-weight: 500;
        }

        .settings-panel {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gold-light);
            animation: fadeIn 0.5s ease 0.1s both;
        }

        .settings-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .settings-title i {
            font-size: 24px;
            color: var(--gold);
        }

        .settings-group {
            margin-bottom: 25px;
        }

        .settings-group-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 3px solid var(--gold);
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--gold-light);
            border-radius: 12px;
            margin-bottom: 10px;
            transition: var(--transition);
            border: 1px solid var(--gold-light);
        }

        .setting-item:hover {
            background: var(--gold-light);
            border-color: var(--gold);
        }

        .setting-label {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .setting-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        .setting-desc {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 28px;
            background: var(--text-muted);
            border-radius: 14px;
            cursor: pointer;
            transition: var(--transition);
        }

        .toggle-switch.active {
            background: var(--success);
        }

        .toggle-switch::before {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            top: 2px;
            right: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .toggle-switch.active::before {
            right: 24px;
        }

        .section-divider {
            background: var(--gold-light);
            height: 1px;
            margin: 25px 0;
            border: none;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            box-shadow: 0 10px 25px rgba(198, 117, 46, 0.3);
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(198, 117, 46, 0.4);
        }

        .btn-secondary {
            background: var(--gold-light);
            color: var(--gold);
            border: 2px solid var(--gold);
            flex: 1;
        }

        .btn-secondary:hover {
            background: var(--gold);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: white;
            flex: 1;
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 107, 107, 0.4);
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(52, 199, 89, 0.1), rgba(52, 199, 89, 0.05));
            color: var(--success);
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(52, 199, 89, 0.2);
            margin-top: 15px;
        }

        .membership-info {
            background: var(--gold-light);
            padding: 15px;
            border-radius: 12px;
            margin-top: 20px;
            border: 1px solid var(--gold-light);
        }

        .membership-info p {
            margin: 8px 0;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .membership-info strong {
            color: var(--gold);
        }

        @media (max-width: 1024px) {
            .profile-wrapper {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .profile-card,
            .settings-panel {
                padding: 25px;
            }

            .avatar {
                width: 140px;
                height: 140px;
                font-size: 50px;
            }

            .profile-name {
                font-size: 22px;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }

        /* ===== Unified Theme Override ===== */
        body {
            background: var(--theme-page-bg) !important;
            color: var(--theme-text) !important;
        }

        .topbar {
            background: linear-gradient(90deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%) !important;
            border-bottom: 1px solid var(--theme-border) !important;
            box-shadow: var(--theme-shadow-soft) !important;
        }

        .icon-btn {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            border: 1px solid var(--theme-border) !important;
            background: var(--theme-surface-2) !important;
            color: var(--theme-gold) !important;
        }

        .search-wrap input {
            background: var(--theme-surface) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-text) !important;
        }

        .search-wrap input:focus {
            background: var(--theme-surface-2) !important;
            border-color: var(--theme-gold) !important;
        }

        .g-badge {
            background: var(--theme-surface-2) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-gold) !important;
        }

        .profile-card,
        .settings-panel,
        .back-button {
            background: var(--theme-surface) !important;
            border: 1px solid var(--theme-border) !important;
        }

        .setting-item,
        .info-item,
        .membership-info {
            background: var(--theme-surface-2) !important;
            border-color: var(--theme-border) !important;
        }

        .settings-group-title,
        .info-section-title {
            border-color: var(--theme-gold) !important;
        }

        .setting-name,
        .profile-name,
        .info-value,
        .settings-title,
        .info-section-title,
        .info-content h4 {
            color: var(--theme-text) !important;
        }

        .setting-desc,
        .profile-email,
        .info-label,
        .membership-info p,
        .info-content p {
            color: var(--theme-text-soft) !important;
        }

        @media (max-width: 480px) {
            .profile-card, .settings-panel { padding: 16px; }
            .profile-name { font-size: 18px; }
        }
    </style>
</head>
<body>
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.index') : (auth()->user()->role === 'teacher' ? route('teacher.dashboard') : route('student.index')) }}" class="icon-btn" title="الرجوع" style="text-decoration: none;">
                <i class="ri-arrow-right-line"></i>
            </a>
            <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
                <i class="ri-moon-line" id="darkIcon"></i>
            </button>
            <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
                <i class="ri-notification-3-line"></i>
            </button>
            @include('components.notification-bell')
            @if(auth()->user()->role === 'student')
                <div class="g-badge g-xp">
                    <span id="userXP">{{ auth()->user()->points ?? 0 }}</span>
                    <i class="ri-flashlight-fill"></i>
                </div>
            @endif
        </div>

        <div class="search-wrap">
            <input type="text" placeholder="بحث...">
            <i class="ri-search-line search-icon"></i>
        </div>

        <div class="topbar-right">
            @if(auth()->user()->avatar_url)
                <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" alt="{{ auth()->user()->name }}" class="u-av-img">
            @else
                <div class="u-av">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            @endif
        </div>
    </header>

    <div class="container">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.index') : (Auth::user()->role === 'teacher' ? route('teacher.dashboard') : route('student.index')) }}" class="back-button">
            <i class="ri-arrow-right-line"></i> العودة
        </a>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="ri-check-circle-line" style="font-size: 16px;"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-wrapper">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar-container">
                        <div class="avatar">
                            @if($user->avatar_url)
                                <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="صورة شخصية">
                            @else
                                {{ mb_substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="avatar-status">
                            <i class="ri-check-line"></i>
                        </div>
                    </div>

                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-role">
                        @if($user->role === 'teacher')
                            <i class="ri-user-teacher-line"></i> معلم
                        @elseif($user->role === 'student')
                            <i class="ri-graduation-cap-line"></i> طالب
                        @else
                            <i class="ri-shield-admin-line"></i> مسؤول
                        @endif
                    </div>
                    <div class="profile-email">
                        <i class="ri-mail-line"></i> {{ $user->email }}
                    </div>

                    <div class="security-badge">
                        <i class="ri-shield-check-line"></i> حساب موثق
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-section-title">
                        <i class="ri-information-line"></i> معلومات الحساب
                    </h3>
                    <div class="info-item">
                        <span class="info-label">الاسم الكامل</span>
                        <span class="info-value">{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">البريد الإلكتروني</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    @if($user->phone)
                        <div class="info-item">
                            <span class="info-label">رقم الهاتف</span>
                            <span class="info-value">{{ $user->phone }}</span>
                        </div>
                    @endif
                    @if($user->bio)
                        <div class="info-item">
                            <span class="info-label">النبذة</span>
                            <span class="info-value">{{ $user->bio }}</span>
                        </div>
                    @endif
                </div>

                @if($user->role === 'student')
                    <div class="info-section">
                        <h3 class="info-section-title">
                            <i class="ri-award-line"></i> الإحصائيات
                        </h3>
                        <div class="stat-grid">
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->enrolledCourses()->count() ?? 0 }}</div>
                                <div class="stat-label">مسارات مسجل</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->points ?? 0 }}</div>
                                <div class="stat-label">نقاط</div>
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'teacher')
                    <div class="info-section">
                        <h3 class="info-section-title">
                            <i class="ri-award-line"></i> الإحصائيات
                        </h3>
                        <div class="stat-grid">
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->courses()->count() ?? 0 }}</div>
                                <div class="stat-label">مسارات</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->courses()->with('lessons')->get()->sum(function($c) { return $c->lessons->count(); }) ?? 0 }}</div>
                                <div class="stat-label">دروس</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="membership-info">
                    <p><strong>آخر تحديث:</strong> {{ $user->updated_at->format('Y-m-d H:i') }}</p>
                    <p><strong>تاريخ الانضمام:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
                </div>
            </div>

            <!-- Settings Panel -->
            <div class="settings-panel">
                <div class="settings-title">
                    <i class="ri-settings-3-line"></i> الإعدادات والتخصيص
                </div>

                <div class="settings-group">
                    <div class="settings-group-title">تفضيلات العرض</div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-sun-line" style="margin-left: 8px;"></i>الوضع الليلي</span>
                            <span class="setting-desc">تفعيل الوضع الليلي عند الدخول</span>
                        </div>
                        <div class="toggle-switch" data-setting="darkMode"></div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-text-size" style="margin-left: 8px;"></i>التكبير</span>
                            <span class="setting-desc">زيادة حجم الخط بنسبة 15%</span>
                        </div>
                        <div class="toggle-switch" data-setting="largeText"></div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-animation" style="margin-left: 8px;"></i>تقليل الحركة</span>
                            <span class="setting-desc">تقليل التأثيرات والحركات</span>
                        </div>
                        <div class="toggle-switch" data-setting="reduceMotion"></div>
                    </div>
                </div>

                <hr class="section-divider">

                <div class="settings-group">
                    <div class="settings-group-title">إشعارات</div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-mail-check-line" style="margin-left: 8px;"></i>تنبيهات البريد</span>
                            <span class="setting-desc">استقبل تنبيهات الرسائل المهمة</span>
                        </div>
                        <div class="toggle-switch active" data-setting="emailNotif"></div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-chat-smile-line" style="margin-left: 8px;"></i>تنبيهات الطلب</span>
                            <span class="setting-desc">إخطارات بشأن الدروس والمهام</span>
                        </div>
                        <div class="toggle-switch active" data-setting="classNotif"></div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-heart-pulse-line" style="margin-left: 8px;"></i>تنبيهات الإنجازات</span>
                            <span class="setting-desc">احصل على إشعارات الإنجازات الجديدة</span>
                        </div>
                        <div class="toggle-switch active" data-setting="achievementNotif"></div>
                    </div>
                </div>

                <hr class="section-divider">

                <div class="settings-group">
                    <div class="settings-group-title">الخصوصية والأمان</div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-eye-off-line" style="margin-left: 8px;"></i>الملف الشخصي خاص</span>
                            <span class="setting-desc">اجعل ملفك الشخصي مرئيًا فقط لك</span>
                        </div>
                        <div class="toggle-switch" data-setting="privateProfile"></div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-name"><i class="ri-shield-lock-line" style="margin-left: 8px;"></i>المصادقة الثنائية</span>
                            <span class="setting-desc">حماية إضافية لحسابك</span>
                        </div>
                        <div class="toggle-switch" data-setting="twoFactor"></div>
                    </div>
                </div>

                <hr class="section-divider">

                <div class="button-group">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="ri-edit-line"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('profile.change-password') }}" class="btn btn-secondary">
                        <i class="ri-key-line"></i> تغيير كلمة المرور
                    </a>
                    <button type="button" class="btn btn-danger" id="deleteAccountBtn">
                        <i class="ri-delete-bin-line"></i> حذف الحساب
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const PROFILE_SETTINGS = [
            'darkMode',
            'largeText',
            'reduceMotion',
            'emailNotif',
            'classNotif',
            'achievementNotif',
            'privateProfile',
            'twoFactor'
        ];

        document.addEventListener('DOMContentLoaded', function() {
            let savedTheme = localStorage.getItem('theme');
            if (!savedTheme) {
                savedTheme = '{{ auth()->user()->role === "teacher" ? "dark" : "light" }}';
                localStorage.setItem('theme', savedTheme);
            }

            loadSettings();
            applyAccessibilitySettings();
            updateDarkModeIcon();
            updateStreakFlameState();

            document.getElementById('darkBtn')?.addEventListener('click', function() {
                if (typeof toggleDarkMode === 'function') toggleDarkMode();
            });

            document.getElementById('deleteAccountBtn')?.addEventListener('click', function() {
                if (typeof confirmDelete === 'function') confirmDelete();
            });

            document.querySelector('.settings-panel')?.addEventListener('click', function(e) {
                var toggle = e.target.closest('.toggle-switch');
                if (toggle && toggle.hasAttribute('data-setting')) {
                    toggleSetting(toggle, toggle.getAttribute('data-setting'));
                }
            });
        });

        function toggleDarkMode() {
            if (typeof window.toggleThemeUniversal === 'function') {
                window.toggleThemeUniversal();
            } else {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
                localStorage.setItem('theme', isDark ? 'light' : 'dark');
            }
            localStorage.setItem('setting_darkMode', String(document.documentElement.getAttribute('data-theme') === 'dark'));
            updateDarkModeIcon();
            updateDarkModeToggle();
        }

        function updateDarkModeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const darkIcon = document.getElementById('darkIcon');
            if (darkIcon) {
                darkIcon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
            }
        }

        function updateDarkModeToggle() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const darkModeToggle = document.querySelector('.settings-group .setting-item:nth-child(1) .toggle-switch');
            if (darkModeToggle) {
                darkModeToggle.classList.toggle('active', isDark);
            }
        }

        function toggleSetting(element, setting) {
            element.classList.toggle('active');
            const isActive = element.classList.contains('active');
            localStorage.setItem(`setting_${setting}`, isActive);

            if (setting === 'darkMode') {
                const currentIsDark = document.documentElement.getAttribute('data-theme') === 'dark';
                if (isActive !== currentIsDark) {
                    toggleDarkMode();
                }
                return;
            }

            applyAccessibilitySettings();
        }

        function loadSettings() {
            const darkModeToggle = document.querySelector('.settings-group .setting-item:nth-child(1) .toggle-switch');
            const savedTheme = (localStorage.getItem('app-theme') || localStorage.getItem('theme') || 'light');

            if (darkModeToggle) {
                darkModeToggle.classList.toggle('active', savedTheme === 'dark');
            }

            PROFILE_SETTINGS.filter(setting => setting !== 'darkMode').forEach(setting => {
                const value = localStorage.getItem(`setting_${setting}`) === 'true';
                const toggle = document.querySelector(`[data-setting="${setting}"]`);
                if (toggle) {
                    toggle.classList.toggle('active', value);
                }
            });
        }

        function applyAccessibilitySettings() {
            const largeTextEnabled = localStorage.getItem('setting_largeText') === 'true';
            const reduceMotionEnabled = localStorage.getItem('setting_reduceMotion') === 'true';
            document.body.classList.toggle('pref-large-text', largeTextEnabled);
            document.body.classList.toggle('pref-reduce-motion', reduceMotionEnabled);
        }

        function confirmDelete() {
            if (confirm('هل أنت متأكد من رغبتك في حذف حسابك بشكل نهائي؟ هذا الإجراء لا يمكن التراجع عنه.')) {
                window.location.href = '{{ route('profile.delete-account') }}';
            }
        }

        function updateStreakFlameState() {
            const streakBadge = document.querySelector('.g-streak');
            const streakSpan = document.querySelector('.g-streak span');

            if (streakBadge && streakSpan) {
                const currentStreak = parseInt(streakSpan.textContent) || 0;
                streakBadge.classList.toggle('active', currentStreak > 0);
                streakBadge.classList.toggle('inactive', currentStreak <= 0);
            }
        }
    </script>
    @include('components.account-theme-foot')
</body>
</html>





