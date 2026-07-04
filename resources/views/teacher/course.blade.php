<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->name ?? 'إدارة مسار' }} - معلم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root { --sidebar-w: 300px; --topbar-h: 70px; }
*{margin:0;padding:0;box-sizing:border-box}body{font-family:Tajawal,sans-serif;background:radial-gradient(circle at top left, rgba(255,214,122,0.16), transparent 20%),linear-gradient(180deg,var(--theme-page-bg) 0%,var(--theme-surface) 45%,var(--theme-page-bg) 100%);color:var(--text-primary);min-height:100vh;transition:background 0.3s,color 0.3s;position:relative;overflow-x:hidden}body::before{content:'';position: fixed;top:16px;left:16px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(255,214,122,0.22),transparent 55%);filter:blur(72px);pointer-events:none;z-index:1}.app{display:flex;min-height:100vh}.sidebar {
            width: var(--sidebar-w);
            position: fixed;
            top: 24px;
            right: 18px;
            bottom: 24px;
            background: var(--sidebar-bg, var(--theme-surface));
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
            box-shadow: 0 0 16px rgba(196,150,58,0.2);
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
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 0;
            height: 0;
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
            color: #F9F9FB;
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
            background: rgba(255,59,48,0.08);
            border-color: rgba(255,59,48,0.18);
        }

        .sidebar-footer {
            margin-top: auto;
        }

        .sidebar-footer form {
            width: 100%;
        }

        .main {
            margin-right: calc(var(--sidebar-w) + 18px);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

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

        .topbar-left { display: flex; align-items: center; gap: 14px; }

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
            background: linear-gradient(135deg, transparent 0%, rgba(196,150,58,0.1) 100%);
            opacity: 0;
            transition: var(--transition);
        }

        .user-profile-btn:hover {
            border-color: rgba(255,214,122,0.6);
            box-shadow: 0 14px 34px rgba(0,0,0,0.2);
            transform: translateY(-2px);
            background: rgba(255,255,255,0.14);
        }

        .user-profile-btn:hover::before { opacity: 1; }

        .user-profile-btn .u-info { text-align: right; position: relative; z-index: 1; }

        .user-profile-btn .u-name {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-primary);
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: var(--transition);
        }

        .user-profile-btn:hover .u-name { text-shadow: 0 0 8px rgba(196,150,58,0.3); }

        .user-profile-btn .u-role { font-size: 10px; color: var(--text-muted); font-weight: 600; }

        .user-profile-btn .u-av {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1));
            color: #111;
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
            overflow: hidden;
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

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .search-wrap { width: 400px; position: relative; }

        .search-wrap input {
            width: 100%;
            padding: 12px 45px 12px 16px;
            background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.02));
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 40px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .search-wrap input::placeholder {
            color: var(--text-muted);
            font-weight: 500;
        }

        .search-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-light);
            
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

        .search-wrap input:focus ~ .search-icon {
            color: var(--gold);
        }

        .content { padding: 0 32px 32px; flex: 1; }.page-header{margin-bottom:28px}.page-header h1{font-size:32px;font-weight:700;background:linear-gradient(135deg,var(--gold) 0%,var(--gold-dark) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}.breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-muted);margin-bottom:12px}.breadcrumb a{color:var(--gold);text-decoration:none;transition:var(--transition)}.breadcrumb a:hover{color:var(--gold-dark)}.course-info{background:linear-gradient(135deg,rgba(255,255,255,0.04),rgba(255,255,255,0.02));border:1px solid rgba(255,255,255,0.08);box-shadow:0 24px 80px rgba(255,214,122,0.08);border-radius:var(--radius-lg);padding:28px;margin-bottom:32px;transition:var(--transition);backdrop-filter:blur(18px)}.course-info:hover{border-color:rgba(255,214,122,0.24);box-shadow:0 26px 90px rgba(255,214,122,0.16)}.course-header{display:grid;grid-template-columns:1fr auto;gap:20px;align-items:start;margin-bottom:20px}.course-info h2{font-size:28px;font-weight:700;margin-bottom:8px;color:var(--text-primary)}.course-info p{font-size:14px;color:var(--text-secondary);line-height:1.6}.course-actions{display:flex;gap:12px;flex-wrap:wrap}.btn{padding:12px 22px;border:1px solid rgba(255,214,122,0.18);border-radius:16px;font-weight:700;font-size:14px;cursor:pointer;transition:var(--transition);display:flex;align-items:center;justify-content:center;gap:10px;font-family:Tajawal,sans-serif;background:rgba(255,214,122,0.10);color:var(--gold)}.btn i{font-size:18px}.btn-primary{background:rgba(255,214,122,0.14);color:var(--gold);box-shadow:0 16px 34px rgba(255,214,122,0.14);border-color:rgba(255,214,122,0.28)}.btn-primary:hover{transform:translateY(-2px);background:rgba(255,214,122,0.2);box-shadow:0 20px 44px rgba(255,214,122,0.18)}.course-meta{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.08)}.meta-item{display:flex;align-items:center;gap:12px;font-size:14px}.meta-item i{color:var(--gold);font-size:20px;width:24px;text-align:center}.meta-item strong{display:block;color:var(--text-primary);font-weight:700}.meta-item span{display:block;color:var(--text-secondary);font-size:12px}.tabs{display:flex;gap:16px;margin-bottom:24px;border-bottom:2px solid rgba(255,255,255,0.08)}.tab{padding:16px 24px;border:none;background:transparent;color:var(--text-secondary);font-weight:600;cursor:pointer;transition:var(--transition);font-size:14px;font-family:Tajawal,sans-serif;display:flex;align-items:center;gap:8px;border-bottom:3px solid transparent}.tab:hover{color:var(--text-primary)}.tab.active{color:var(--gold);border-bottom-color:var(--gold)}.tab i{font-size:18px}.content-card{background:var(--card-bg, var(--theme-surface));border-radius:var(--radius-lg);padding:28px;box-shadow:0 18px 48px rgba(0,0,0,0.28);border:1px solid rgba(255,255,255,0.08);margin-bottom:24px;transition:var(--transition);backdrop-filter:blur(18px)}.content-card:hover{box-shadow:0 26px 70px rgba(255,214,122,0.16);border-color:rgba(255,214,122,0.18)}.content-card h3{font-size:18px;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:12px;color:var(--text-primary)}.content-card h3 i{color:var(--gold);font-size:22px}.item-list{list-style:none;display:grid;gap:16px}.item-list li{display:flex;align-items:center;justify-content:space-between;padding:18px;background:linear-gradient(135deg,rgba(255,255,255,0.04),rgba(255,255,255,0.02));border:1px solid rgba(255,255,255,0.08);border-radius:16px;transition:var(--transition);box-shadow:0 12px 28px rgba(0,0,0,0.18)}.item-list li:hover{background:linear-gradient(135deg,rgba(255,255,255,0.08),rgba(255,255,255,0.02));border-color:rgba(255,214,122,0.18);box-shadow:0 18px 40px rgba(255,214,122,0.12);transform:translateY(-2px)}.item-info{flex:1}.item-info h4{font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text-primary)}.item-info p{font-size:13px;color:var(--text-secondary)}.item-actions{display:flex;gap:8px;align-items:center}.action-btn{width:36px;height:36px;border:none;border-radius:12px;background:rgba(255,214,122,0.12);color:var(--gold);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:var(--transition);font-size:18px;text-decoration:none}.action-btn:hover{background:var(--gold);color:#111;transform:translateY(-2px);box-shadow:0 8px 24px rgba(196,150,58,0.22)}.action-btn.danger{background:rgba(255,59,48,0.16);color:var(--danger)}.action-btn.danger:hover{background:var(--danger);color:#fff}.question-section-header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px;flex-wrap:wrap}.question-section-header p{margin:0;color:var(--text-secondary);font-size:14px;max-width:760px}.question-list{display:grid;gap:18px}.question-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:22px;box-shadow:0 14px 48px rgba(0,0,0,0.18);transition:var(--transition)}.question-card:hover{transform:translateY(-2px);box-shadow:0 20px 62px rgba(0,0,0,0.22)}.question-header{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;flex-wrap:wrap;margin-bottom:14px}.question-header h4{font-size:16px;font-weight:700;color:var(--text-primary);margin:0;line-height:1.3}.question-meta{display:flex;flex-wrap:wrap;gap:10px;color:var(--text-secondary);font-size:13px}.question-tag{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:rgba(255,214,122,0.1);color:var(--gold);border-radius:999px;font-size:12px;font-weight:700}.answers-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:8px}.answer-pill{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:14px;background:rgba(255,255,255,0.04);color:var(--text-secondary);font-size:13px}.answer-pill.correct{background:rgba(52,199,89,0.12);border:1px solid rgba(52,199,89,0.3);color:var(--success)}.no-answers{margin:0;color:var(--text-secondary);font-size:14px}.empty-state{text-align:center;padding:48px 24px;color:var(--text-muted)}.empty-state i{font-size:64px;color:var(--gold);margin-bottom:16px;opacity:0.3}.empty-state h4{font-size:18px;font-weight:700;margin-bottom:8px;color:var(--text-primary)}.empty-state p{font-size:14px;margin-bottom:20px}.btn-add{display:inline-flex;align-items:center;gap:10px;padding:14px 24px;background:rgba(255,214,122,0.14);color:var(--gold);border:1px solid rgba(255,214,122,0.28);border-radius:16px;text-decoration:none;font-weight:700;transition:var(--transition)}.btn-add:hover{transform:translateY(-2px);box-shadow:0 14px 32px rgba(255,214,122,0.18)}::-webkit-scrollbar{width:8px}::-webkit-scrollbar-track{background:var(--bg)}::-webkit-scrollbar-thumb{background:var(--gold);border-radius:4px}.modal-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;backdrop-filter:blur(4px)}.modal-overlay.active{display:flex}.delete-modal{background:var(--card-bg);border-radius:20px;padding:40px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:slideUp 0.3s ease;border:1px solid rgba(196,150,58,0.1)}@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}.delete-modal-icon{width:80px;height:80px;background:linear-gradient(135deg,rgba(255,59,48,0.15),rgba(255,59,48,0.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;color:var(--danger);font-size:40px}.delete-modal h3{font-size:22px;font-weight:700;color:var(--text-primary);text-align:center;margin-bottom:12px}.delete-modal p{font-size:14px;color:var(--text-secondary);text-align:center;line-height:1.6;margin-bottom:32px}.delete-modal-actions{display:flex;gap:12px;flex-direction:row-reverse}.delete-modal-actions button{flex:1;padding:12px 20px;border:none;border-radius:12px;font-weight:600;font-size:14px;cursor:pointer;font-family:Tajawal,sans-serif;transition:var(--transition)}.btn-delete-confirm{background:linear-gradient(135deg,var(--danger),#FF5A52);color:#fff;box-shadow:0 4px 12px rgba(255,59,48,0.3)}.btn-delete-confirm:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(255,59,48,0.4)}.btn-delete-cancel{background:var(--gold-light);color:var(--gold);border:2px solid var(--gold)}.btn-delete-cancel:hover{background:var(--gold);color:#fff}


        /* ═══ Responsive ═══ */
        .hamburger-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            color: var(--text-primary);
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .hamburger-btn:hover { background: rgba(255,214,122,0.1); border-color: rgba(255,214,122,0.3); }

        @media (max-width: 1024px) {
            .main { margin-right: 72px !important; }
            .topbar { padding: 0 20px; }
            .content { padding: 0 20px 20px; }
            .search-wrap { width: 260px; }
            .user-profile-btn { min-width: 160px; }
            .course-meta { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 768px) {
            .hamburger-btn { display: flex; }
            .main { margin-right: 0 !important; }
            .topbar { padding: 0 12px; height: 56px; }
            .search-wrap { display: none; }
            .user-profile-btn { min-width: auto; }
            .u-name, .u-role { display: none; }
            .content { padding: 0 12px 12px; }
            .course-meta { grid-template-columns: repeat(2, 1fr); }
            .course-header { grid-template-columns: 1fr; }
            .tabs { flex-wrap: wrap; gap: 6px; border-bottom: none; }
            .tab { padding: 10px 14px; font-size: 13px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); border-bottom: none; }
            .tab.active { background: rgba(255,214,122,0.12); border-color: rgba(255,214,122,0.25); }
            .content-card { padding: 18px; }
        }

        @media (max-width: 480px) {
            .content { padding: 0 8px 8px; }
            .course-meta { grid-template-columns: 1fr 1fr; }
            .topbar { padding: 0 8px; height: 52px; }
            .icon-btn { width: 36px; height: 36px; font-size: 17px; }
            .hamburger-btn { width: 36px; height: 36px; font-size: 18px; }
            .item-list li { flex-direction: column; align-items: flex-start; gap: 12px; }
            .item-actions { align-self: flex-end; }
        }
    </style>
</head>
<body>
    @include('components.alerts')
    <div class="app">
        @include('components.sidebar-unified')
        <main class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="hamburger-btn" id="sidebarToggle" title="فتح القائمة">
                        <i class="ri-menu-line"></i>
                    </button>
                    <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
                        <i class="ri-sun-line" id="theme-icon"></i>
                    </button>
                    <button class="icon-btn" id="notificationBtn" title="الإشعارات"><i class="ri-notification-3-line"></i></button>
                    <a href="{{ route('profile.show') }}" class="user-profile-btn" title="عرض الملف الشخصي">
                        <div class="u-info">
                            <div class="u-name">{{ Auth::user()->name }}</div>
                            <div class="u-role">معلم</div>
                        </div>
                        <div class="u-av">{{ mb_substr(Auth::user()->name, 0, 1) }}</div>
                    </a>
                </div>
                <div class="topbar-right">
                    <div class="search-wrap">
                        <input type="text" placeholder="بحث...">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
            </header>
            <div class="content">
                <div class="breadcrumb">
                    <a href="{{ route('teacher.courses') }}">المسارات</a>
                    <span>/</span>
                    <span>{{ $course->name ?? 'إدارة مسار' }}</span>
                </div>
                <div class="page-header">
                    <h1>{{ $course->name }}</h1>
                </div>
                <div class="course-info">
                    <div class="course-header">
                        <div>
                            <h2>{{ $course->name }}</h2>
                            <p>{{ $course->description ?? 'لا يوجد وصف' }}</p>
                        </div>
                        <div class="course-actions">
                            <button class="btn btn-primary" id="showLessonFormBtn"><i class="ri-add-line"></i> درس جديد</button>
                            <a href="{{ route('teacher.exam.new') }}" class="btn btn-primary" style="text-decoration:none;"><i class="ri-add-line"></i> اختبار جديد</a>
                        </div>
                    </div>
                    <div class="course-meta">
                        <div class="meta-item"><i class="ri-book-open-line"></i><div><strong>{{ $lessons->count() ?? 0 }}</strong> <span>درس</span></div></div>
                        <div class="meta-item"><i class="ri-file-list-line"></i><div><strong>{{ $exams->count() ?? 0 }}</strong> <span>اختبار</span></div></div>
                        <div class="meta-item"><i class="ri-question-answer-line"></i><div><strong>{{ $studentQuestions->count() ?? 0 }}</strong> <span>سؤال</span></div></div>
                        <div class="meta-item"><i class="ri-sticky-note-line"></i><div><strong>{{ $courseNotes->count() ?? 0 }}</strong> <span>ملاحظة</span></div></div>
                        <div class="meta-item"><i class="ri-time-line"></i><div><strong>{{ $course->duration ?? '-' }}</strong> <span>ساعة</span></div></div>
                        <div class="meta-item"><i class="ri-users-line"></i><div><strong>{{ $course->max_students ?? 'بدون حد' }}</strong> <span>الحد الأقصى</span></div></div>
                    </div>
                </div>
                <div class="tabs" id="tabsContainer">
                    <button class="tab active" data-tab="lessons"><i class="ri-book-2-line"></i> الدروس</button>
                    <button class="tab" data-tab="exams"><i class="ri-file-list-line"></i> الاختبارات</button>
                    <button class="tab" data-tab="questions"><i class="ri-question-answer-line"></i> إدارة الأسئلة</button>
                    <button class="tab" data-tab="notes"><i class="ri-sticky-note-line"></i> الملاحظات</button>
                </div>
                <div id="lessons-tab" class="content-card" style="display: block;">
                    <h3><i class="ri-book-open-line"></i> الدروس المتاحة</h3>
                    @if($lessons && $lessons->count() > 0)
                        <ul class="item-list" id="lessonList">
                            @foreach($lessons as $lesson)
                                <li data-href="{{ route('teacher.lesson.edit', $lesson->id) }}" style="cursor:pointer;">
                                    <div class="item-info">
                                        <h4>{{ $lesson->name }}</h4>
                                        <p>{{ Str::limit($lesson->description, 80) }}</p>
                                    </div>
                                    <div class="item-actions" >
                                        <a href="{{ route('teacher.lesson.edit', $lesson->id) }}" class="action-btn"><i class="ri-edit-line"></i></a>
                                        <button class="action-btn danger" data-id="{{ $lesson->id }}" data-type="lesson" data-name="{{ $lesson->name }}" data-url="{{ route('teacher.deleteLesson', $lesson->id) }}"><i class="ri-delete-bin-line"></i></button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state">
                            <i class="ri-book-open-line"></i>
                            <h4>لا توجد دروس بعد</h4>
                            <p>ابدأ بإضافة درس</p>
                            <a href="{{ route('teacher.lesson.create') }}?course={{ $course->id }}" class="btn-add"><i class="ri-add-line"></i> إضافة درس</a>
                        </div>
                    @endif
                </div>
                <div id="questions-tab" class="content-card" style="display: none;">
                    <div class="question-section-header">
                        <h3><i class="ri-question-answer-line"></i> إدارة الأسئلة</h3>
                        <p>عرض جميع أسئلة الطلاب التي تم طرحها على دروس هذا المسار.</p>
                    </div>
                    @if($studentQuestions && $studentQuestions->count() > 0)
                        <div class="question-list">
                            @foreach($studentQuestions as $question)
                                <div class="question-card">
                                    <div class="question-header">
                                        <div>
                                            <h4>{{ Str::limit($question->question_text, 120) }}</h4>
                                            <div class="question-meta">
                                                <span class="question-tag">
                                                    {{ $question->status === 'pending' ? 'قيد الانتظار' : 'مُجاب عليه' }}
                                                </span>
                                                <span>{{ $question->lesson->name ?? 'درس غير معروف' }}</span>
                                                <span>{{ $question->student->name ?? 'طالب غير معروف' }}</span>
                                            </div>
                                        </div>
                                        <div class="item-actions" >
                                            <a href="{{ route('teacher.questions.manage') }}"
                                               class="action-btn"
                                               title="الانتقال إلى صفحة إدارة الأسئلة">
                                                <i class="ri-arrow-right-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="answers-row">
                                        <span class="answer-pill {{ $question->status === 'answered' ? 'correct' : '' }}">
                                            <i class="ri-time-line"></i> {{ $question->created_at->format('d/m/Y') }}
                                        </span>
                                        @if($question->status === 'answered')
                                            <span class="answer-pill correct">
                                                <i class="ri-checkbox-circle-line"></i> تمت الإجابة
                                            </span>
                                        @endif
                                    </div>
                                    @if($question->status === 'answered' && $question->answer_text)
                                        <div class="question-meta" style="margin-top: 16px; gap: 10px;">
                                            <strong style="color: var(--gold); display: block; margin-bottom: 8px;">الإجابة:</strong>
                                            <p class="no-answers">{{ Str::limit($question->answer_text, 180) }}</p>
                                        </div>
                                    @else
                                        <p class="no-answers">السؤال لم يتم الرد عليه بعد.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ri-question-answer-line"></i>
                            <h4>لا توجد أسئلة طلابية على هذا المسار</h4>
                            <p>عندما يطرح الطالب سؤالاً داخل درس من هذا المسار سيظهر هنا.</p>
                        </div>
                    @endif
                </div>
                <div id="notes-tab" class="content-card" style="display: none;">
                    <div class="question-section-header">
                        <h3><i class="ri-sticky-note-line"></i> الملاحظات</h3>
                        <p>عرض الملاحظات المتعلقة بهذا المسار من خلال صفحة إدارة الملاحظات.</p>
                    </div>
                    @if($courseNotes && $courseNotes->count() > 0)
                        <div class="question-list">
                            @foreach($courseNotes as $note)
                                <div class="question-card">
                                    <div class="question-header">
                                        <div>
                                            <h4>{{ Str::limit($note->question_text, 120) }}</h4>
                                            <div class="question-meta">
                                                <span class="question-tag">
                                                    {{ $note->status === 'pending' ? 'قيد الانتظار' : 'مُجاب عليه' }}
                                                </span>
                                                <span>{{ $note->lesson->name ?? 'درس غير معروف' }}</span>
                                                <span>{{ $note->student->name ?? 'طالب غير معروف' }}</span>
                                            </div>
                                        </div>
                                        <div class="item-actions" >
                                            <a href="{{ route('teacher.questions.manage') }}" class="action-btn" title="الانتقال إلى صفحة إدارة الأسئلة"><i class="ri-arrow-right-line"></i></a>
                                        </div>
                                    </div>
                                    <div class="answers-row">
                                        <span class="answer-pill {{ $note->status === 'answered' ? 'correct' : '' }}">
                                            <i class="ri-time-line"></i> {{ $note->created_at->format('d/m/Y') }}
                                        </span>
                                        @if($note->status === 'answered')
                                            <span class="answer-pill correct">
                                                <i class="ri-checkbox-circle-line"></i> تمت الإجابة
                                            </span>
                                        @endif
                                    </div>
                                    @if($note->status === 'answered' && $note->answer_text)
                                        <div class="question-meta" style="margin-top: 16px; gap: 10px;">
                                            <strong style="color: var(--gold); display: block; margin-bottom: 8px;">الإجابة:</strong>
                                            <p class="no-answers">{{ Str::limit($note->answer_text, 180) }}</p>
                                        </div>
                                    @else
                                        <p class="no-answers">لم يتم الرد على هذه الملاحظة بعد.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ri-sticky-note-line"></i>
                            <h4>لا توجد ملاحظات على هذا المسار</h4>
                            <p>عندما يضيف الطالب ملاحظات على دروس هذا المسار ستظهر هنا.</p>
                            <a href="{{ route('teacher.questions.manage') }}" class="btn-add"><i class="ri-arrow-right-line"></i> إدارة الملاحظات</a>
                        </div>
                    @endif
                </div>
                <div id="exams-tab" class="content-card" style="display: none;">
                    <h3><i class="ri-file-list-line"></i> الاختبارات المتاحة</h3>
                    @if($exams && $exams->count() > 0)
                        <ul class="item-list" id="examList">
                            @foreach($exams as $exam)
                                <li data-href="{{ route('teacher.exam.edit', $exam->id) }}" style="cursor:pointer;">
                                    <div class="item-info">
                                        <h4>{{ $exam->name }}</h4>
                                        <p>{{ $exam->questions->count() }} سؤال</p>
                                    </div>
                                    <div class="item-actions" >
                                        <a href="{{ route('teacher.exam.edit', $exam->id) }}" class="action-btn"><i class="ri-edit-line"></i></a>
                                        <button class="action-btn danger" data-id="{{ $exam->id }}" data-type="exam" data-name="{{ $exam->name }}" data-url="{{ route('teacher.exam.delete', $exam->id) }}"><i class="ri-delete-bin-line"></i></button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state">
                            <i class="ri-file-list-line"></i>
                            <h4>لا توجد اختبارات بعد</h4>
                            <p>أضف اختبارات لتختبر طلابك</p>
                            <a href="{{ route('teacher.exam.create') }}?course={{ $course->id }}" class="btn-add"><i class="ri-add-line"></i> إنشاء اختبار</a>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
    <div id="deleteModal" class="modal-overlay">
        <div class="delete-modal">
            <div class="delete-modal-icon">
                <i class="ri-delete-bin-line"></i>
            </div>
            <h3 id="modalTitle">حذف العنصر</h3>
            <p id="modalMessage">هل أنت متأكد من رغبتك في حذف هذا العنصر؟ لا يمكن التراجع عن هذه العملية.</p>
            <div class="delete-modal-actions">
                <form id="deleteForm" method="POST" style="flex:1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-confirm" style="width:100%;">حذف نهائياً</button>
                </form>
                <button type="button" class="btn-delete-cancel" id="closeDeleteModalBtn">إلغاء</button>
            </div>
        </div>
    </div>
    <script>        function toggleDarkMode(){const html=document.documentElement,isDark='dark'===html.getAttribute('data-theme');html.setAttribute('data-theme',isDark?'light':'dark'),localStorage.setItem('theme',isDark?'light':'dark'),updateThemeIcon()}function updateThemeIcon(){const isDark='dark'===document.documentElement.getAttribute('data-theme');document.getElementById('theme-icon').className=isDark?'ri-moon-line':'ri-sun-line'}function switchTab(tab){document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active')),document.querySelector(`.tab[data-tab="${tab}"]`).classList.add('active'),document.getElementById('lessons-tab').style.display='lessons'===tab?'block':'none',document.getElementById('exams-tab').style.display='exams'===tab?'block':'none',document.getElementById('questions-tab').style.display='questions'===tab?'block':'none',document.getElementById('notes-tab').style.display='notes'===tab?'block':'none'}function showLessonForm(){window.location.href='{{ route('teacher.lesson.create') }}?course={{ $course->id }}'}function openDeleteModal(itemId,type,name,deleteUrl){const modal=document.getElementById('deleteModal'),form=document.getElementById('deleteForm'),title=document.getElementById('modalTitle'),message=document.getElementById('modalMessage');form.action=deleteUrl;let typeLabel='الاختبار';if('lesson'===type)typeLabel='الدرس';else if('question'===type)typeLabel='السؤال';title.textContent=`حذف ${typeLabel}`;message.textContent=`هل أنت متأكد من رغبتك في حذف "${name}"؟ لا يمكن التراجع عن هذه العملية.`;modal.classList.add('active')}function closeDeleteModal(){document.getElementById('deleteModal').classList.remove('active')}function handleItemListClick(e){const deleteBtn=e.target.closest('.action-btn.danger');if(deleteBtn){e.preventDefault();const{id,type,name,url}=deleteBtn.dataset;if(id&&type&&url){openDeleteModal(id,type,name||'',url)}return}if(e.target.closest('.item-actions'))return;const li=e.target.closest('li[data-href]');if(li){window.location.href=li.dataset.href}}document.addEventListener('DOMContentLoaded',function(){const theme=localStorage.getItem('theme')||'light';document.documentElement.setAttribute('data-theme',theme);updateThemeIcon();document.getElementById('darkBtn')?.addEventListener('click',toggleDarkMode);document.getElementById('showLessonFormBtn')?.addEventListener('click',showLessonForm);document.getElementById('tabsContainer')?.addEventListener('click',function(e){const tabBtn=e.target.closest('[data-tab]');if(tabBtn)switchTab(tabBtn.dataset.tab)});document.getElementById('lessonList')?.addEventListener('click',handleItemListClick);document.getElementById('examList')?.addEventListener('click',handleItemListClick);document.getElementById('closeDeleteModalBtn')?.addEventListener('click',closeDeleteModal);document.getElementById('deleteModal')?.addEventListener('click',function(e){e.target===this&&closeDeleteModal()})})</script>
@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>













