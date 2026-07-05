<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>{{ isset($exam) ? 'تعديل الاختبار' : 'إنشاء اختبار جديد' }} - معلم | إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root { --sidebar-w: 300px; --topbar-h: 70px; }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Tajawal', sans-serif;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.16), transparent 22%), linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%);
      color: var(--text-primary);
      min-height: 100vh;
      transition: background 0.3s, color 0.3s;
      position: relative;
      overflow-x: hidden;
    }
    body::before {
      content: '';
      position: fixed;
      top: 16px;
      left: 16px;
      width: 320px;
      height: 320px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,214,122,0.24), transparent 55%);
      filter: blur(72px);
      z-index: 0;
      pointer-events: none;
    }
    .app { display: flex; min-height: 100vh; }

    .sidebar {
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
      border-radius: 14px;
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
      position: relative;
    }
    .nav-btn i { font-size: 20px; color: rgba(255,214,122,0.92); flex-shrink: 0; }
    .nav-btn span { color: inherit; flex: 1; }
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

    .sidebar-footer { padding: 20px; }

    .main { margin-right: calc(var(--sidebar-w) + 18px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

    .topbar {
      height: var(--topbar-h);
      background: transparent;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      margin: 20px 0 32px;
      position: relative;
      animation: slideDown 0.5s ease;
    }
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .topbar-left { display: flex; align-items: center; gap: 14px; }
    .icon-btn { background: none; border: none; font-size: 20px; color: var(--text-secondary); cursor: pointer; transition: var(--transition); }
    .icon-btn:hover { color: var(--gold); transform: scale(1.1); }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
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
    .user-profile-btn:hover::before {
      opacity: 1;
    }
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
    .user-profile-btn:hover .u-name {
      text-shadow: 0 0 8px rgba(196,150,58,0.3);
    }
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
    .search-wrap {
      width: 430px;
      position: relative;
      display: block;
    }
    .search-wrap::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 40px;
      background: rgba(255,255,255,0.04);
      pointer-events: none;
      filter: blur(16px);
      opacity: 0.35;
    }
    .search-wrap input,
    .search-input {
      width: 100%;
      height: 52px;
      padding: 12px 48px 12px 18px;
      background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.02)) !important;
      border: 1px solid rgba(0,0,0,0.04) !important;
      border-radius: 40px !important;
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      color: var(--text-primary) !important;
      outline: none;
      box-shadow: 0 22px 40px rgba(0,0,0,0.22), inset 0 1px 2px rgba(255,255,255,0.06);
      transition: var(--transition);
      position: relative;
      z-index: 1;
    }
    .search-wrap input::placeholder {
      color: rgba(255,255,255,0.72);
      font-weight: 500;
    }
    .search-wrap input:focus {
      border-color: var(--gold) !important;
      box-shadow: 0 0 0 4px rgba(255,214,122,0.14) !important;
      transform: translateY(-1px);
    }
    .search-icon {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255,255,255,0.78);
      font-size: 18px;
      pointer-events: none;
      transition: var(--transition);
      z-index: 1;
    }
    .search-wrap input:focus ~ .search-icon {
      color: var(--gold);
      transform: translateY(-50%) scale(1.02);
    }
    .search-match {
      border-radius: 14px;
      background: rgba(196,150,58,0.14);
      box-shadow: inset 0 0 0 1px rgba(196,150,58,0.35);
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
    .icon-btn:hover {
      color: var(--gold);
      border-color: var(--gold);
      transform: scale(1.08);
      box-shadow: 0 0 16px rgba(196,150,58,0.3);
    }

    .content { padding: 0 32px 40px; flex: 1; }

    /* Form Styles */
    .page-header {
      margin-bottom: 28px;
      animation: slideInDown 0.5s ease;
    }
    .page-header h1 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 6px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .page-header p {
      color: var(--text-secondary);
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
    }
    .page-header p i { color: var(--gold); font-size: 16px; animation: pulse 2s ease-in-out infinite; }

    .form-container {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 26px;
      animation: slideInUp 0.5s ease 0.1s both;
      align-items: start;
    }

    .form-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(0,0,0,0.02);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      animation: fadeUp 0.6s ease both;
    }
    .form-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle at 100% 0%, rgba(196,150,58,0.08), transparent);
      pointer-events: none;
    }
    .form-card:hover {
      box-shadow: 0 12px 32px rgba(196,150,58,0.12);
      border-color: var(--gold);
    }
    .form-card h2 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      z-index: 2;
    }
    .form-card h2 i { color: var(--gold); font-size: 22px; }

    .form-group {
      margin-bottom: 24px;
      position: relative;
      z-index: 2;
      animation: fadeUp 0.6s ease backwards;
    }
    .form-group:nth-child(2) { animation-delay: 0.1s; }
    .form-group:nth-child(3) { animation-delay: 0.2s; }
    .form-group:nth-child(4) { animation-delay: 0.3s; }
    .form-group:nth-child(5) { animation-delay: 0.4s; }
    .form-group:last-child { margin-bottom: 0; }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text-primary);
      font-size: 14px;
    }
    .required { color: var(--danger); margin-right: 4px; }

    input[type="text"],
    input[type="number"],
    input[type="email"],
    input[type="datetime-local"],
    textarea,
    select {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid var(--border);
      border-radius: 8px;
      font-family: 'Tajawal', sans-serif;
      font-size: 13px;
      background: var(--bg);
      color: var(--text-primary);
      transition: var(--transition);
    }

    input[type="text"]::placeholder,
    input[type="number"]::placeholder,
    textarea::placeholder { color: var(--text-muted); }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="email"]:focus,
    input[type="datetime-local"]:focus,
    textarea:focus,
    select:focus {
      outline: none;
      border-color: var(--gold);
      background: var(--card-bg);
      box-shadow: 0 0 0 4px var(--gold-light), inset 0 0 0 1px var(--gold);
      transform: translateY(-2px);
    }

    textarea {
      resize: vertical;
      min-height: 100px;
      font-family: 'Tajawal', sans-serif;
    }

    .form-hint {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 4px;
      transition: var(--transition);
    }

    input[type="text"]:focus ~ .form-hint,
    textarea:focus ~ .form-hint { color: var(--gold); }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      animation: fadeUp 0.6s ease backwards;
    }

    .form-actions {
      display: flex;
      gap: 10px;
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
      animation: fadeUp 0.6s ease 0.8s both;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-family: 'Tajawal', sans-serif;
      position: relative;
      overflow: hidden;
      text-decoration: none;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255,255,255,0.3);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
      pointer-events: none;
    }

    .btn:active::before { width: 300px; height: 300px; }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      flex: 1;
      box-shadow: 0 4px 12px rgba(196,150,58,0.3);
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(196,150,58,0.4);
    }
    .btn-primary:active { transform: translateY(-1px); }

    .btn-secondary {
      background: var(--bg);
      color: var(--text-primary);
      border: 2px solid var(--border);
    }
    .btn-secondary:hover {
      background: var(--gold-light);
      border-color: var(--gold);
      color: var(--gold);
    }

    /* Animations */
    @keyframes slideInDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.8; }
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .form-container {
        grid-template-columns: 1fr;
      }
    }

    /* Hamburger button */
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

    /* Sidebar backdrop */
    .sidebar-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.55);
      z-index: 9998;
    }
    .sidebar-backdrop.active { display: block; }

    @media (max-width: 1024px) {
      .hamburger-btn { display: flex; }
      .sidebar {
        position: fixed !important;
        transform: translateX(110%) !important;
        visibility: hidden !important;
        pointer-events: none !important;
        width: var(--sidebar-w) !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 100vh !important;
        z-index: 9999 !important;
        border-radius: 0 !important;
        padding: 28px 18px !important;
        transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), visibility 0.3s !important;
      }
      .sidebar.sidebar-open {
        transform: translateX(0) !important;
        visibility: visible !important;
        pointer-events: auto !important;
      }
      .main { margin-right: 0 !important; }
    }

    @media (max-width: 768px) {
      .topbar { padding: 16px 20px; }
      .content { padding: 20px; }
      .form-card { padding: 20px; }
      .form-row { grid-template-columns: 1fr; }
      .page-header h1 { font-size: 28px; }
      .form-actions { flex-direction: column; }
      .btn { width: 100%; }
    }

    /* ===== HIDE SCROLLBAR ===== */
    ::-webkit-scrollbar {
      display: none;
    }

    .sidebar,
    .main {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
</head>
<body>
  @include('components.alerts')
  <div class="sidebar-backdrop" id="examSidebarBackdrop"></div>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="examSidebar">
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

      <nav class="sidebar-nav">
        <a href="{{ route('teacher.dashboard') }}" class="nav-btn">
          <i class="ri-home-4-line"></i><span>الرئيسية</span>
        </a>
        <a href="{{ route('teacher.courses') }}" class="nav-btn">
          <i class="ri-book-2-line"></i><span>المسارات</span>
        </a>
        <a href="{{ route('teacher.exams') }}" class="nav-btn active">
          <i class="ri-file-list-line"></i><span>الاختبارات</span>
        </a>
        <a href="{{ route('teacher.analytics') }}" class="nav-btn">
          <i class="ri-bar-chart-2-line"></i><span>نسبة الإنجاز</span>
        </a>
        <a href="{{ route('teacher.students') }}" class="nav-btn">
          <i class="ri-team-line"></i><span>طلابي</span>
        </a>
        <a href="{{ route('teacher.questions.manage') }}" class="nav-btn">
          <i class="ri-chat-3-line"></i><span>الأسئلة والاستفسارات</span>
        </a>
        <a href="{{ route('teacher.messaging') }}" class="nav-btn">
          <i class="ri-message-2-line"></i><span>المراسلة</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <form action="{{ route('teacher.logout') }}" method="POST" style="width: 100%;">
          @csrf
          <button type="submit" class="nav-btn logout" style="width: 100%; margin: 0; border: none;">
            <i class="ri-logout-box-r-line"></i><span>خروج</span>
          </button>
        </form>
      </div>
    </aside>

    <main class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="topbar-left">
          <button class="hamburger-btn" id="examHamburger" title="فتح القائمة">
            <i class="ri-menu-line"></i>
          </button>
          <button class="icon-btn" id="themeToggle" title="تبديل الثيم">
            <i id="theme-icon" class="ri-moon-line"></i>
          </button>
          <button class="icon-btn" id="notificationBtn" title="الإشعارات">
            <i class="ri-notification-3-line"></i>
          </button>
          <a href="{{ route('profile.show') }}" class="user-profile-btn" title="عرض الملف الشخصي">
            <div class="u-info">
              <div class="u-name">{{ Auth::user()->name }}</div>
              <div class="u-role">معلم</div>
            </div>
            <div class="u-av">{{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</div>
          </a>
        </div>
        <div class="topbar-right">
          <div class="search-wrap">
            <input type="text" class="search-input" id="searchInput" placeholder="بحث..." aria-label="بحث">
            <i class="ri-search-line search-icon"></i>
          </div>
        </div>
      </div>

      <div class="content">
        <div class="page-header">
          <h1>{{ isset($exam) ? 'تعديل الاختبار' : 'إنشاء اختبار جديد' }}</h1>
          <p><i class="ri-pencil-line"></i> {{ isset($exam) ? 'قم بتحديث تفاصيل الاختبار بسهولة' : 'أنشئ اختبار جديد وأضف الأسئلة والإجابات' }}</p>
        </div>

                <!-- Form -->
                <div class="form-card">
                  <h2><i class="ri-file-list-line"></i> إعدادات الاختبار</h2>

                  <form action="{{ isset($exam) ? route('teacher.exam.update', $exam->id) : route('teacher.createExam', $lesson->id ?? 0) }}" method="POST">
                    @csrf
                    @if(isset($exam))
                      @method('PUT')
                    @endif

                    <!-- Basic Information Section -->
                    <div class="form-group">
                      <label><span class="required">*</span> اسم الاختبار</label>
                      <input type="text" name="name" placeholder="أدخل اسم الاختبار" value="{{ isset($exam) ? ($exam->name ?? '') : old('name') }}" required>
                      <div class="form-hint">اختر اسماً واضحاً يصف محتوى الاختبار</div>
                    </div>

                    <div class="form-group">
                      <label>تعليمات الاختبار</label>
                      <textarea name="instructions" placeholder="أضف تعليمات للاختبار...">{{ isset($exam) ? ($exam->instructions ?? '') : old('instructions') }}</textarea>
                      <div class="form-hint">اترك فارغاً إذا لم تكن تريد إضافة تعليمات</div>
                    </div>

                    <!-- Exam Settings Section -->
                    <div class="form-row">
                      <div class="form-group">
                        <label><span class="required">*</span> الحد الأدنى للنجاح (%)</label>
                        <input type="number" name="passing_score" placeholder="60" min="0" max="100" value="{{ isset($exam) ? ($exam->passing_score ?? 60) : old('passing_score', 60) }}" required>
                      </div>

                      <div class="form-group">
                        <label><span class="required">*</span> عدد المحاولات المسموح</label>
                        <select name="attempts_allowed" required style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; font-family:inherit; font-size:14px; background:var(--card-bg); color:var(--text-primary);">
                          <option value="1" {{ (isset($exam) ? ($exam->attempts_allowed ?? 1) : old('attempts_allowed', 1)) == 1 ? 'selected' : '' }}>محاولة واحدة</option>
                          <option value="2" {{ (isset($exam) ? ($exam->attempts_allowed ?? 1) : old('attempts_allowed', 1)) == 2 ? 'selected' : '' }}>محاولتان</option>
                          <option value="3" {{ (isset($exam) ? ($exam->attempts_allowed ?? 1) : old('attempts_allowed', 1)) == 3 ? 'selected' : '' }}>3 محاولات</option>
                          <option value="5" {{ (isset($exam) ? ($exam->attempts_allowed ?? 1) : old('attempts_allowed', 1)) == 5 ? 'selected' : '' }}>5 محاولات</option>
                          <option value="10" {{ (isset($exam) ? ($exam->attempts_allowed ?? 1) : old('attempts_allowed', 1)) == 10 ? 'selected' : '' }}>10 محاولات</option>
                          <option value="0" {{ (isset($exam) ? ($exam->attempts_allowed ?? 1) : old('attempts_allowed', 1)) == 0 ? 'selected' : '' }}>غير محدود</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group">
                        <label>مدة الاختبار (بالدقائق)</label>
                        <input type="number" name="duration" placeholder="30" min="1" max="600" value="{{ isset($exam) ? ($exam->duration ?? 30) : old('duration', 30) }}">
                        <div class="form-hint">المدة الزمنية المتاحة للاختبار (افتراضي: 30 دقيقة)</div>
                      </div>

                      <div class="form-group">
                        <label>تاريخ انتهاء الاختبار</label>
                        <input type="datetime-local" name="expires_at" value="{{ isset($exam) && $exam->expires_at ? $exam->expires_at->format('Y-m-d\TH:i') : old('expires_at') }}">
                        <div class="form-hint">اتركه فارغاً لعدم تحديد انتهاء - سيختفي الاختبار تلقائياً بعد هذا التاريخ</div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', isset($exam) ? (int)$exam->is_published : 0) ? 'checked' : '' }}>
                        <span>نشر الاختبار للطلاب</span>
                      </label>
                      <div class="form-hint">عند إلغاء التفعيل لن يظهر الاختبار للطلاب حتى يتم نشره.</div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                      <button type="submit" class="btn btn-primary">
                        <i class="ri-check-line"></i> {{ isset($exam) ? 'حفظ التغييرات' : 'إنشاء الاختبار' }}
                      </button>
                      <a href="{{ route('teacher.exams') }}" class="btn btn-secondary">
                        <i class="ri-close-line"></i> إلغاء
                      </a>
                    </div>
                  </form>
                </div>

                <!-- Information Sidebar -->
                <div class="form-card" style="height: fit-content; animation-delay: 0.2s;">
                  <h2><i class="ri-lightbulb-flash-line"></i> معلومات مهمة</h2>

                  <div class="form-group">
                    <h3><i class="ri-information-line"></i> نصائح</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                      <li style="margin-bottom: 14px; display: flex; gap: 10px;">
                        <span style="color: var(--gold); font-weight: bold;">✓</span>
                        <span style="font-size: 13px; color: var(--text-secondary);">استخدم أسماء واضحة وموصوفة</span>
                      </li>
                      <li style="margin-bottom: 14px; display: flex; gap: 10px;">
                        <span style="color: var(--gold); font-weight: bold;">✓</span>
                        <span style="font-size: 13px; color: var(--text-secondary);">حدد وقتاً معقولاً للاختبار</span>
                      </li>
                      <li style="display: flex; gap: 10px;">
                        <span style="color: var(--gold); font-weight: bold;">✓</span>
                        <span style="font-size: 13px; color: var(--text-secondary);">أضف الأسئلة والإجابات بعد الإنشاء</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
      </div>
    </main>
  </div>

  <script>
    document.getElementById('themeToggle').addEventListener('click', toggleDarkMode);

    function toggleDarkMode() {
      const html = document.documentElement;
      const isDark = html.getAttribute('data-theme') === 'dark';
      const newTheme = isDark ? 'light' : 'dark';
      html.setAttribute('data-theme', newTheme);
      if (document.body) document.body.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateThemeIcon();
    }

    function updateThemeIcon() {
      const html = document.documentElement;
      const icon = document.getElementById('theme-icon');
      if (icon) {
        const isDark = html.getAttribute('data-theme') === 'dark';
        icon.className = isDark ? 'ri-moon-line' : 'ri-sun-line';
      }
    }

    function bindSearchInput() {
      const searchInput = document.getElementById('searchInput');
      if (!searchInput) return;

      searchInput.addEventListener('input', function() {
        runPageSearch(this.value);
      });

      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          runPageSearch(this.value);
        }
      });
    }

    function clearSearchHighlights() {
      document.querySelectorAll('.search-match').forEach(el => {
        el.classList.remove('search-match');
      });
    }

    function runPageSearch(query) {
      const content = document.querySelector('.content');
      if (!content) return;

      clearSearchHighlights();
      const normalized = query.trim().toLowerCase();
      if (!normalized) return;

      const selectors = 'h1, h2, h3, h4, p, label, span, div, li, small, option, .form-hint';
      const candidates = Array.from(content.querySelectorAll(selectors));
      let firstMatch = null;

      candidates.forEach(el => {
        const text = (el.textContent || '').toLowerCase();
        if (text.includes(normalized)) {
          el.classList.add('search-match');
          if (!firstMatch) {
            firstMatch = el;
          }
        }
      });

      if (firstMatch) {
        firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    window.addEventListener('load', function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', savedTheme);
      if (document.body) document.body.setAttribute('data-theme', savedTheme);
      updateThemeIcon();
      bindSearchInput();
    });
  </script>
  <script>
    (function() {
      const sidebar = document.getElementById('examSidebar');
      const hamburger = document.getElementById('examHamburger');
      const backdrop = document.getElementById('examSidebarBackdrop');
      function openSidebar() {
        sidebar && sidebar.classList.add('sidebar-open');
        backdrop && backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
      function closeSidebar() {
        sidebar && sidebar.classList.remove('sidebar-open');
        backdrop && backdrop.classList.remove('active');
        document.body.style.overflow = '';
      }
      hamburger && hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar && sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
      });
      backdrop && backdrop.addEventListener('click', closeSidebar);
      document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSidebar(); });
      sidebar && sidebar.querySelectorAll('.nav-btn').forEach(function(btn) {
        btn.addEventListener('click', closeSidebar);
      });
    })();
  </script>
@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>



