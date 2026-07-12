<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الاستفسارات - لوحة المعلم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Load Theme ASAP -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    
</script>

    <style>
        :root {
            /* Colors */
            --gold: #C4963A;
            --gold-dark: #A07A28;
            --gold-light: rgba(255,214,122,0.15);
            --bg: #08101A;
            --bg-secondary: rgba(255,255,255,0.06);
            --sidebar-bg: rgba(15,18,28,0.96);
            --card-bg: rgba(255,255,255,0.06);
            --text-primary: #F4F4F7;
            --text-secondary: #B8B9C5;
            --text-muted: #7F8395;
            --text-tertiary: #868AA3;
            --success: #34C759;
            --danger: #FF3B30;
            --danger-light: rgba(255,59,48,0.12);
            --border: rgba(255,214,122,0.12);
            --sidebar-w: 260px;
            --topbar-h: 70px;
            --radius-lg: 22px;
            --radius-md: 14px;
            --shadow: 0 18px 50px rgba(0,0,0,0.35);
            --shadow-md: 0 10px 30px rgba(0,0,0,0.22);
            --shadow-hover: 0 20px 60px rgba(0,0,0,0.45);
            --transition: all 0.28s cubic-bezier(0.4,0,0.2,1);
            --transition-base: all 0.28s ease;
            --color-gold: #FFD66D;
            --color-gold-dark: #C4963A;
        }

        [data-theme="dark"] {
            --bg: #121212;
            --sidebar-bg: #1E1E1E;
            --card-bg: #1E1E1E;
            --text-primary: #F2F2F7;
            --text-secondary: #AEAEB2;
            --text-muted: #636366;
            --border: rgba(255,255,255,0.04);
            --shadow: 0 4px 24px rgba(0,0,0,0.4);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: radial-gradient(circle at top left, rgba(255,214,122,0.20), transparent 20%),
                        radial-gradient(circle at bottom right, rgba(255,214,122,0.08), transparent 18%),
                        linear-gradient(180deg, #071018 0%, #09131f 45%, #071018 100%);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        .app { display: flex; min-height: 100vh; position: relative; }
        .app::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 24px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,214,122,0.18);
            filter: blur(72px);
            pointer-events: none;
            z-index: 0;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(180deg, rgba(15,18,28,0.98) 0%, rgba(16,21,38,0.96) 100%);
            position: fixed;
            right: 18px; top: 18px;
            bottom: 18px;
            display: flex;
            flex-direction: column;
            z-index: 200;
            box-shadow: -14px 24px 64px rgba(0,0,0,0.32);
            border-left: 1px solid rgba(255,214,122,0.14);
            border-top-left-radius: 36px;
            border-bottom-left-radius: 36px;
            backdrop-filter: blur(18px);
            padding-top: 20px;
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
            width: 48px;
            height: 48px;
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
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .logo-name { font-size: 19px; font-weight: 800; color: var(--gold); position: relative; z-index: 1; }
        .logo-sub { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-top: 4px; position: relative; z-index: 1; }

        .sidebar-nav {
            flex: 1;
            padding: 0 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
        }

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

        .nav-btn:hover::before { width: 100%; }
        .nav-btn i { font-size: 20px; width: 22px; text-align: center; flex-shrink: 0; }
        .nav-btn:hover { background: var(--gold-light); color: var(--text-primary); }
        .nav-btn.active {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
            box-shadow: 0 6px 16px rgba(196,150,58,0.25);
        }
        .nav-btn.logout {
            color: #FF6C63;
            background: rgba(255,59,48,0.08);
            border: 1px solid rgba(255,59,48,0.18);
            font-weight: 700;
        }
        .nav-btn.logout:hover {
            background: rgba(255,59,48,0.16);
        }

        .sidebar-footer { padding: 20px; }

        /* MAIN */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-right: var(--sidebar-w);
        }

        /* TOPBAR */
        .topbar {
            height: 70px;
            background: rgba(12,16,28,0.92);
            border-bottom: 1px solid rgba(255,214,122,0.12);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: sticky;
            top: 18px;
            z-index: 100;
            backdrop-filter: blur(16px);
            box-shadow: inset 0 -1px 0 rgba(255,214,122,0.05);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-wrap {
            position: relative;
            width: 400px;
        }

        .search-wrap input {
            width: 100%;
            padding: 12px 45px 12px 16px;
            background: linear-gradient(135deg, var(--card-bg), rgba(196, 150, 58, 0.02));
            border: 1px solid var(--border);
            border-radius: 40px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .search-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-light), var(--shadow);
        }

        .search-wrap input::placeholder { color: var(--text-secondary); }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 18px;
            pointer-events: none;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 18px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .icon-btn::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 100%;
            background: rgba(196, 150, 58, 0.15);
            transition: var(--transition);
            z-index: -1;
        }

        .icon-btn:hover::before { width: 100%; }
        .icon-btn:hover { color: var(--gold); }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            background: transparent;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Tajawal', sans-serif;
            border-radius: var(--radius-md);
            position: relative;
            overflow: hidden;
        }

        .user-profile-btn::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 100%;
            background: rgba(196, 150, 58, 0.1);
            transition: var(--transition);
            z-index: -1;
        }

        .user-profile-btn:hover::before { width: 100%; }

        .u-av {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            border-radius: 50%;
            font-weight: 700;
        }

        .u-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .u-role { font-size: 11px; color: var(--text-secondary); }

        .content { padding: 32px; flex: 1; overflow-y: auto; }

        .page-header { margin-bottom: 30px; }
        .page-title { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--text-secondary); }

        /* INQUIRY CARDS */
        .inquiries-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .inquiry-card {
            background: rgba(255,255,255,0.08);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid rgba(255,214,122,0.14);
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(18px);
            z-index: 0;
        }

        .inquiry-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,214,122,0.14);
            filter: blur(28px);
            opacity: 0.7;
            pointer-events: none;
            transition: var(--transition);
            z-index: -1;
        }

        .inquiry-card:hover {
            box-shadow: 0 26px 74px rgba(0,0,0,0.34);
            transform: translateY(-4px);
            border-color: rgba(255,214,122,0.22);
        }

        .inquiry-card:hover::before {
            transform: translateX(-8px) scale(1.03);
            opacity: 1;
        }

        .inquiry-card.answered::before {
            background: rgba(52,199,89,0.14);
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .inquiry-icon-wrapper {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,214,122,0.14);
            border-radius: 16px;
            color: var(--color-gold);
            font-size: 22px;
            flex-shrink: 0;
        }

        .inquiry-card.answered .inquiry-icon-wrapper {
            background: rgba(52,199,89,0.14);
            color: var(--success);
        }

        .inquiry-info {
            flex: 1;
        }

        .inquiry-student {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .inquiry-date {
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .inquiry-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .inquiry-status.pending {
            background: rgba(255, 159, 64, 0.15);
            color: #FF9F40;
        }

        .inquiry-status.answered {
            background: rgba(52, 199, 89, 0.15);
            color: var(--success);
        }

        .inquiry-course {
            padding: 14px 16px;
            background: rgba(255,214,122,0.10);
            border-radius: var(--radius-md);
            font-size: 13px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
            border-right: 3px solid var(--gold);
            backdrop-filter: blur(12px);
        }

        .inquiry-text {
            padding: 14px 16px;
            background: rgba(255,255,255,0.08);
            border-radius: var(--radius-md);
            border-right: 3px solid rgba(255,214,122,0.22);
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-primary);
            backdrop-filter: blur(12px);
        }

        .inquiry-answer {
            padding: 14px 16px;
            background: rgba(52,199,89,0.12);
            border-radius: var(--radius-md);
            border-right: 3px solid var(--success);
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-primary);
            backdrop-filter: blur(12px);
        }

        .inquiry-answer-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .inquiry-actions {
            display: flex;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,214,122,0.10);
            margin-top: 2px;
        }

        .inquiry-action-btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            background: linear-gradient(135deg, rgba(255,214,122,0.95), rgba(196,150,58,0.95));
            color: white;
            border-radius: 20px;
            font-family: 'Tajawal', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 14px 32px rgba(255,214,122,0.16);
        }

        .inquiry-action-btn:hover {
            box-shadow: 0 18px 38px rgba(255,214,122,0.22);
            transform: translateY(-2px);
        }

        .inquiry-action-btn.delete {
            background: rgba(255,59,48,0.95);
            border-color: transparent;
            color: white;
        }

        .inquiry-action-btn.delete:hover {
            box-shadow: 0 14px 32px rgba(255,59,48,0.25);
        }

        .success-message i { font-size: 18px; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i { font-size: 64px; color: var(--text-muted); margin-bottom: 15px; opacity: 0.5; }
        .empty-state h3 { font-size: 18px; font-weight: 600; color: var(--text-primary); margin-bottom: 8px; }
        .empty-state p { font-size: 13px; color: var(--text-secondary); }

        .stat-card {
            padding: 18px 22px;
            border-radius: var(--radius-lg);
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,214,122,0.12);
            box-shadow: 0 16px 38px rgba(0,0,0,0.18);
            backdrop-filter: blur(12px);
        }

        .inquiries-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .inquiry-card {
            background: rgba(255,255,255,0.05);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.24);
            transition: var(--transition);
            border: 1px solid rgba(255,214,122,0.12);
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(16px);
        }

        .inquiry-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,214,122,0.14);
            filter: blur(24px);
            opacity: 0.8;
            pointer-events: none;
            transition: var(--transition-base);
            z-index: 0;
        }

        .inquiry-card:hover {
            box-shadow: 0 24px 70px rgba(0,0,0,0.32);
            transform: translateY(-4px);
            border-color: rgba(255,214,122,0.22);
        }

        .inquiry-card:hover::before {
            transform: translateX(-8px) scale(1.05);
            opacity: 1;
        }

        .inquiry-card.answered::before {
            background: rgba(52,199,89,0.14);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active { display: flex; }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .modal-close:hover { color: var(--text-primary); }

        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: var(--text-primary); font-size: 14px; }

        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg);
            resize: vertical;
            min-height: 150px;
            transition: var(--transition);
        }

        textarea:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-light);
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Tajawal', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
        }

        .btn-primary:hover {
            background: var(--gold-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--gold-light);
            color: var(--gold);
            border: 1px solid var(--gold);
        }

        .btn-secondary:hover {
            background: var(--gold);
            color: white;
        }

        @media (max-width: 1024px) {
            .main { margin-right: 72px !important; }
        }

        @media (max-width: 768px) {
            .main { margin-right: 0 !important; }
            .inquiries-container { grid-template-columns: 1fr; }
            .inquiry-actions { flex-direction: column; }
            .inquiry-action-btn { width: 100%; }
            .search-wrap { width: 100%; min-width: 200px; }
        }

        @media (max-width: 480px) {
            .content { padding: 0 10px 10px !important; }
        }

        /* ===== HIDE SCROLLBAR ===== */
        ::-webkit-scrollbar {
          display: none;
        }

        .sidebar,
        .main,
        .content,
        .messages-list {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }
    </style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon"><i class="ri-book-read-fill"></i></div>
      <div class="logo-name">إجلال</div>
      <div class="logo-sub">المنصة التعليمية</div>
    </div>

    <nav class="sidebar-nav">
      <button class="nav-btn" data-href="{{ route('teacher.index') }}">
        <i class="ri-home-4-line"></i><span>الرئيسية</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.index') }}">
        <i class="ri-book-2-line"></i><span>المسارات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.categories') }}">
        <i class="ri-price-tag-3-line"></i><span>الفئات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.exams') }}">
        <i class="ri-file-list-line"></i><span>الاختبارات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.analytics') }}">
        <i class="ri-bar-chart-2-line"></i><span>نسبة الإنجاز</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.students') }}">
        <i class="ri-team-line"></i><span>طلابي</span>
      </button>
      <button class="nav-btn active" data-href="{{ route('teacher.questions.manage') }}">
        <i class="ri-chat-3-line"></i><span>الأسئلة والاستفسارات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.messaging') }}">
        <i class="ri-message-2-line"></i><span>المراسلة</span>
      </button>
    </nav>

    <div class="sidebar-footer">
      <form action="{{ route('teacher.logout') }}" method="POST" style="width: 100%;">
        @csrf
        <button type="submit" class="nav-btn logout" style="width: 100%; margin: 0;">
          <i class="ri-logout-box-r-line"></i><span>خروج</span>
        </button>
      </form>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
          <i class="ri-moon-line" id="darkIcon"></i>
        </button>
        <button class="icon-btn" id="notificationBtn" title="الإشعارات"><i class="ri-notification-3-line"></i></button>
        <button class="user-profile-btn">
          <div style="text-align: right;">
            <div class="u-name">{{ Auth::user()->name ?? 'المعلم' }}</div>
            <div class="u-role">معلم</div>
          </div>
          <div class="u-av">{{ mb_substr(Auth::user()->name ?? 'م', 0, 1) }}</div>
        </button>
      </div>
      <div class="topbar-right">
        <div class="search-wrap">
          <input type="text" placeholder="ابحث عن استفسار...">
          <i class="ri-search-line search-icon"></i>
        </div>
      </div>
    </header>

    <div class="content">
      <div class="page-header">
        <h1 class="page-title">الاستفسارات</h1>
        <p class="page-subtitle">الرد على استفسارات وأسئلة الطلاب</p>
      </div>

      @if(session('success'))
        <div class="success-message">
            <i class="ri-check-line"></i> {{ session('success') }}
        </div>
      @endif

      <!-- Statistics -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(255,159,64,0.1), transparent); border-right: 3px solid #FF9F40;">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">قيد الانتظار</div>
            <div style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, #FF9F40, #FF7A30); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $pendingInquiries->total() }}</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(52,199,89,0.1), transparent); border-right: 3px solid var(--success);">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">تمت الإجابة</div>
            <div style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, var(--success), #2FA562); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $answeredInquiries->count() }}</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(196,150,58,0.1), transparent); border-right: 3px solid var(--gold);">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">المجموع</div>
            <div style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $pendingInquiries->total() + $answeredInquiries->count() }}</div>
        </div>
      </div>

      <!-- Pending -->
      @if($pendingInquiries->count() > 0)
        <div style="margin-bottom: 40px;">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <i class="ri-time-line" style="font-size: 20px; color: #FF9F40;"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">الاستفسارات المعلقة</h2>
            <span style="background: rgba(255,159,64,0.15); color: #FF9F40; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $pendingInquiries->total() }}</span>
          </div>
          <div class="inquiries-container">
            @foreach($pendingInquiries as $inquiry)
              <div class="inquiry-card">
                <div class="inquiry-header">
                  <div class="inquiry-icon-wrapper"><i class="ri-chat-question-line"></i></div>
                  <div style="flex: 1;">
                    <div class="inquiry-student">{{ $inquiry->student->name }}</div>
                    <div class="inquiry-date"><i class="ri-calendar-line"></i> {{ $inquiry->created_at->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="inquiry-status pending"><i class="ri-time-line"></i> قيد الانتظار</span>
                </div>
                <div class="inquiry-course">
                  <i class="ri-book-line"></i> <strong>{{ $inquiry->lesson->name }}</strong>
                </div>
                <div class="inquiry-text">{{ Str::limit($inquiry->question_text, 150) }}</div>
                <div class="inquiry-actions">
                  <button data-inquiry-id="{{ $inquiry->id }}" class="inquiry-action-btn answer-btn">
                    <i class="ri-reply-all-line"></i> الرد على الاستفسار
                  </button>
                  <form action="{{ route('teacher.inquiries.destroy', $inquiry) }}" method="POST" style="display: flex; flex: 1;" class="delete-inquiry-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inquiry-action-btn delete" style="flex: 1;">
                      <i class="ri-delete-bin-line"></i> حذف
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
          <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $pendingInquiries->links() }}
          </div>
        </div>
      @else
        <div class="empty-state" style="margin-bottom: 40px;">
          <i class="ri-inbox-line"></i>
          <h3>لا توجد استفسارات معلقة</h3>
          <p>جميع الاستفسارات تمت الإجابة عليها</p>
        </div>
      @endif

      <!-- Answered -->
      @if($answeredInquiries->count() > 0)
        <div>
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <i class="ri-checkbox-circle-line" style="font-size: 20px; color: var(--success);"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">الاستفسارات المجابة</h2>
            <span style="background: rgba(52,199,89,0.15); color: var(--success); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $answeredInquiries->count() }}</span>
          </div>
          <div class="inquiries-container">
            @foreach($answeredInquiries as $inquiry)
              <div class="inquiry-card answered">
                <div class="inquiry-header">
                  <div class="inquiry-icon-wrapper"><i class="ri-chat-check-line"></i></div>
                  <div style="flex: 1;">
                    <div class="inquiry-student">{{ $inquiry->student->name }}</div>
                    <div class="inquiry-date"><i class="ri-calendar-line"></i> {{ $inquiry->created_at->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="inquiry-status answered"><i class="ri-check-double-line"></i> مجاب عليه</span>
                </div>
                <div class="inquiry-course">
                  <i class="ri-book-line"></i> <strong>{{ $inquiry->lesson->name }}</strong>
                </div>
                <div class="inquiry-text">{{ Str::limit($inquiry->question_text, 150) }}</div>
                <div class="inquiry-answer">
                  <div class="inquiry-answer-label"><i class="ri-check-line"></i> الإجابة</div>
                  {{ Str::limit($inquiry->answer_text, 200) }}
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="empty-state">
          <i class="ri-checkbox-circle-line"></i>
          <h3>لم تجب على أي استفسارات بعد</h3>
          <p>ابدأ بالرد على الاستفسارات المعلقة</p>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Modal -->
<div id="answerModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      الرد على السؤال
      <button class="modal-close" id="closeAnswerModalBtn">×</button>
    </div>
    <form id="answerForm" method="POST">
      @csrf
      <div class="form-group">
        <label>الإجابة</label>
        <textarea name="answer_text" placeholder="اكتب إجابتك هنا..." required></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeAnswerModalBtn2">إلغاء</button>
        <button type="submit" class="btn btn-primary">إرسال الإجابة</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Dark Mode
  function toggleDark() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';

    html.setAttribute('data-theme', newTheme);
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    const icon = document.getElementById('darkIcon');
    if (icon) {
      icon.className = newTheme === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
    }
  }

  // Load saved theme
  (function() {
    const theme = localStorage.getItem('theme') || localStorage.getItem('app-theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
    if (theme === 'dark') {
      const icon = document.getElementById('darkIcon');
      if (icon) {
        icon.className = 'ri-sun-line';
      }
    }
  })();

  // Modal
  function openAnswerModal(inquiryId) {
    document.getElementById('answerForm').action = `/teacher/inquiries/${inquiryId}/answer`;
    document.getElementById('answerModal').classList.add('active');
  }

  function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
  }

  document.getElementById('answerModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('active');
    }
  });

  // Init theme icon
  window.addEventListener('load', function() {
    const theme = document.documentElement.getAttribute('data-theme');
    const icon = document.getElementById('darkIcon');
    if (theme === 'dark') {
      icon.className = 'ri-sun-line';
    }
  });


  // CSP-compliant event listeners (replacing inline handlers)
  document.addEventListener('DOMContentLoaded', function() {
    // 1-7. Nav button delegation
    document.querySelector('.sidebar-nav').addEventListener('click', function(e) {
      var btn = e.target.closest('.nav-btn');
      if (btn && btn.dataset.href) {
        location.href = btn.dataset.href;
      }
    });
    // 8. Answer button delegation
    document.addEventListener('click', function(e) {
      var btn = e.target.closest('.answer-btn');
      if (btn && btn.dataset.inquiryId) {
        openAnswerModal(btn.dataset.inquiryId);
      }
    });
    // 9. Delete form confirmation
    document.querySelectorAll('.delete-inquiry-form').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        if (!confirm('\u0647\u0644 \u062A\u0631\u064A\u062F \u062D\u0630\u0641 \u0647\u0630\u0627 \u0627\u0644\u0627\u0633\u062A\u0641\u0633\u0627\u0631\u061F')) {
          e.preventDefault();
        }
      });
    });
    // 10-11. Modal close buttons
    document.getElementById('closeAnswerModalBtn').addEventListener('click', function() {
      closeModal('answerModal');
    });
    document.getElementById('closeAnswerModalBtn2').addEventListener('click', function() {
      closeModal('answerModal');
    });
    // 12. Dark mode toggle
    document.getElementById('darkBtn').addEventListener('click', function() {
      toggleDark();
    });
  });
</script>
@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>






