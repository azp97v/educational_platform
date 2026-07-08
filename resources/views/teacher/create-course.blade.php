<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>إنشاء مسار جديد - معلم | إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('app-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
    })();
  </script>
  <style>
    :root { --sidebar-w: 300px; --topbar-h: 70px; }
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Tajawal', sans-serif;
      min-height: 100vh;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 20%),
                  radial-gradient(circle at bottom right, rgba(255,214,122,0.08), transparent 18%),
                  linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%);
      color: var(--text-primary);
      transition: background 0.3s, color 0.3s;
      overflow-x: hidden;
      overflow-y: auto;
      position: relative;
    }

    body::before {
      content: '';
      position: fixed;
      top: 18px;
      left: 18px;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      background: rgba(255,214,122,0.16);
      filter: blur(84px);
      pointer-events: none;
      z-index: 0;
    }

    .app {
      display: flex;
      min-height: 100vh;
      position: relative;
      z-index: 2;
    }

    .sidebar {
      width: var(--sidebar-w);
      background: var(--sidebar-bg);
      position: fixed;
      right: 18px;
      top: 24px;
      bottom: 24px;
      display: flex;
      flex-direction: column;
      z-index: 200;
      box-shadow: -16px 28px 70px rgba(0,0,0,0.28);
      border-left: 1px solid rgba(255,214,122,0.16);
      border-top-left-radius: 32px;
      border-bottom-left-radius: 32px;
      backdrop-filter: blur(24px);
      padding: 28px 22px;
      gap: 20px;
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
      transition: var(--transition);
      box-shadow: 0 0 16px rgba(196,150,58,0.18);
      animation: float 3s ease-in-out infinite;
      overflow: hidden;
    }

    .logo-icon img { width: 100%; height: 100%; object-fit: contain; border-radius: 30px; display: block; }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-8px); }
    }

    .logo-name { font-size: 18px; font-weight: 800; color: var(--gold); position: relative; z-index: 1; }
    .logo-sub { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-top: 4px; position: relative; z-index: 1; letter-spacing: 0.02em; }

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
      display: none;
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
      cursor: pointer;
      overflow: hidden;
    }

    .nav-btn i { font-size: 20px; color: rgba(255,214,122,0.92); flex-shrink: 0; }
    .nav-btn span { color: inherit; flex: 1; }
    .nav-btn:hover { background: rgba(255,214,122,0.08); color: #F9F9FB; border-color: rgba(255,214,122,0.18); }
    .nav-btn.active { background: rgba(255,214,122,0.18); color: #fff; border-color: rgba(255,214,122,0.32); box-shadow: 0 20px 40px rgba(255,214,122,0.14); backdrop-filter: blur(18px); }
    .nav-btn.logout { color: #FF6C63; background: rgba(255,59,48,0.08); border-color: rgba(255,59,48,0.18); }

    .sidebar-footer { margin-top: auto; }
    .sidebar-footer form { width: 100%; }

    .main {
      margin-right: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding-bottom: 32px;
      overflow-y: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      height: var(--topbar-h);
      background: transparent;
      backdrop-filter: blur(18px);
      border-bottom: 1px solid transparent;
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

    .topbar-left, .topbar-right { display: flex; align-items: center; gap: 14px; }
    .search-wrap { width: min(100%, 420px); position: relative; }
    .search-wrap input {
      width: 100%;
      padding: 12px 45px 12px 16px;
      background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.02));
      border: 1px solid rgba(255,255,255,0.10);
      border-radius: 40px;
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      color: var(--text-primary);
      outline: none;
      transition: var(--transition);
    }

    .search-wrap input::placeholder { color: var(--text-muted); }
    .search-wrap input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(255,214,122,0.14); }
    .search-icon { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px; pointer-events: none; }

    .notification-btn { position: relative; }
    .icon-btn { width: 42px; height: 42px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; background: var(--card-bg); color: var(--text-secondary); font-size: 19px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); box-shadow: 0 12px 34px rgba(0,0,0,0.10); overflow: hidden; }
    .icon-btn::before { content: ''; position: absolute; width: 0; height: 0; background: var(--gold); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%); transition: var(--transition); z-index: -1; opacity: 0.12; }
    .icon-btn:hover::before { width: 100%; height: 100%; }
    .icon-btn:hover { color: var(--gold); border-color: var(--gold); transform: scale(1.05); }

    .user-profile-btn { display: inline-flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 999px; box-shadow: 0 10px 26px rgba(0,0,0,0.12); cursor: pointer; transition: var(--transition); overflow: hidden; min-width: 200px; text-decoration: none; color: inherit; position: relative; }
    .user-profile-btn::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, transparent 0%, rgba(196,150,58,0.1) 100%); opacity: 0; transition: var(--transition); }
    .user-profile-btn:hover { border-color: rgba(255,214,122,0.5); box-shadow: 0 18px 42px rgba(0,0,0,0.16); transform: translateY(-1px); background: rgba(255,255,255,0.14); }
    .user-profile-btn:hover::before { opacity: 1; }
    .user-profile-btn .u-info { text-align: right; z-index: 1; }
    .user-profile-btn .u-name { font-size: 12px; font-weight: 800; color: var(--text-primary); background: linear-gradient(135deg, var(--gold), var(--gold-dark)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .user-profile-btn .u-role { font-size: 10px; color: var(--text-muted); font-weight: 600; }
    .user-profile-btn .u-av { width: 30px; height: 30px; background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1)); color: #111; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 900; box-shadow: 0 4px 10px rgba(255,214,122,0.24); }
    .user-profile-btn:hover .u-av { transform: scale(1.15) rotate(-5deg); box-shadow: 0 6px 16px rgba(196,150,58,0.35); }

    .content { padding: 32px; flex: 1; }
    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-size: 32px; font-weight: 700; margin-bottom: 6px; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .page-header p { color: var(--text-secondary); font-size: 14px; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .page-header p i { color: var(--gold); }
    .breadcrumb { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 13px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }

    .form-container { display: grid; grid-template-columns: 1.45fr 0.95fr; gap: 24px; align-items: start; }
    .form-card, .preview-card { background: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow); border: 1px solid rgba(255,255,255,0.08); overflow: hidden; }
    .form-card { padding: 32px; }
    .preview-card {
      padding: 28px;
      position: relative;
      overflow: hidden;
    }
    .preview-card::before {
      content: '';
      position: absolute;
      top: -20px;
      left: -20px;
      width: 180px;
      height: 180px;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.28), transparent 55%);
      filter: blur(24px);
      pointer-events: none;
      z-index: 1;
    }
    .preview-card > * { position: relative; z-index: 2; }
    .form-card h2, .preview-card h3 { font-size: 20px; margin-bottom: 20px; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
    .form-card h2 i, .preview-card h3 i { color: var(--gold); font-size: 24px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 10px; font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .required { color: var(--danger); margin-left: 6px; }
    .form-group input, .form-group textarea, .form-group select {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 16px;
      background: rgba(255,255,255,0.04);
      color: var(--text-primary);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      transition: var(--transition);
      outline: none;
    }
    .form-group textarea { min-height: 150px; resize: vertical; }
    .form-group select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none;
      padding-right: 16px;
    }
    .form-group select::-ms-expand { display: none; }
    .form-group input[type="number"]::-webkit-outer-spin-button,
    .form-group input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    .form-group input[type="number"] {
      -moz-appearance: textfield;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(255,214,122,0.14); transform: translateY(-1px); }
    .form-group option {
      background: rgba(10, 18, 32, 0.96);
      color: var(--text-primary);
    }
    .form-group option[value=""] { color: var(--text-muted); }
    .form-hint { margin-top: 10px; color: var(--text-muted); font-size: 13px; }
    .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .form-actions { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; margin-top: 28px; }
    .btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 22px; border-radius: 16px; font-weight: 700; text-decoration: none; cursor: pointer; transition: var(--transition); border: 1px solid transparent; }
    .btn-primary { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #111; border-color: transparent; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 14px 32px rgba(196,150,58,0.28); }
    .btn-secondary { background: rgba(255,255,255,0.06); color: var(--text-primary); border-color: rgba(255,255,255,0.14); }
    .btn-secondary:hover { background: rgba(255,255,255,0.12); }

    .preview-card .course-preview { display: grid; gap: 18px; }
    .course-preview-image { width: 100%; min-height: 180px; border-radius: 24px; background: linear-gradient(180deg, rgba(255,214,122,0.16), rgba(255,214,122,0.04)); display: grid; place-items: center; color: var(--gold); font-size: 48px; }
    .preview-card h4 { margin: 0; font-size: 22px; font-weight: 800; }
    .preview-card p { color: var(--text-secondary); line-height: 1.75; }
    .preview-info { display: flex; justify-content: space-between; gap: 10px; padding: 14px 0; border-top: 1px solid rgba(255,255,255,0.08); }
    .preview-info label { color: var(--text-muted); font-size: 13px; }
    .preview-info strong { color: var(--text-primary); }
    .preview-card > div:last-child { background: rgba(255,214,122,0.08); border-radius: 16px; padding: 16px; margin-top: 18px; }

    @media (max-width: 1200px) {
      .main { margin-right: calc(var(--sidebar-w) - 40px); }
      .form-container { grid-template-columns: 1fr; }
    }
    @media (max-width: 992px) {
      .sidebar { width: 260px; }
      .main { margin-right: 260px; }
    }
    @media (max-width: 768px) {
      .app { flex-direction: column; }
      .sidebar { position: relative; width: 100%; right: auto; top: auto; bottom: auto; border-left: none; border-radius: 0; box-shadow: none; border: none; margin-bottom: 18px; }
      .main { margin-right: 0; }
      .topbar { padding: 0 20px; }
      .content { padding: 20px; }
      .form-card, .preview-card { padding: 24px; }
      .form-row { grid-template-columns: 1fr; }
      .search-wrap { width: 100%; }
    }
    @media (max-width: 480px) {
      .topbar { flex-wrap: wrap; gap: 12px; padding: 16px; }
      .content { padding: 16px; }
      .search-wrap input { padding: 12px 46px 12px 16px; }
      .form-actions { flex-direction: column; }
      .btn { width: 100%; }
    }

    html, body, .sidebar, .sidebar-nav, .main {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    html::-webkit-scrollbar,
    body::-webkit-scrollbar,
    .sidebar::-webkit-scrollbar,
    .sidebar-nav::-webkit-scrollbar,
    .main::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }
  </style>
</head>
<body>
@include('components.alerts')
<div class="app">
  <!-- Sidebar -->
  <aside class="sidebar">
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
      <a href="{{ route('teacher.dashboard') }}" class="nav-btn" id="nb-home">
        <i class="ri-home-4-line"></i><span>الرئيسية</span>
      </a>
      <a href="{{ route('teacher.courses') }}" class="nav-btn active" id="nb-courses">
        <i class="ri-book-2-line"></i><span>المسارات</span>
      </a>
      <a href="{{ route('teacher.enrollment.requests') }}" class="nav-btn" id="nb-enrollment">
        <i class="ri-user-add-line"></i><span>طلبات الالتحاق</span>
      </a>
      <a href="{{ route('teacher.exams') }}" class="nav-btn" id="nb-exams">
        <i class="ri-file-list-line"></i><span>الاختبارات</span>
      </a>
      <a href="{{ route('teacher.analytics') }}" class="nav-btn" id="nb-analytics">
        <i class="ri-bar-chart-2-line"></i><span>نسبة الإنجاز</span>
      </a>
      <a href="{{ route('teacher.students') }}" class="nav-btn" id="nb-students">
        <i class="ri-team-line"></i><span>طلابي</span>
      </a>
      <a href="{{ route('teacher.questions.manage') }}" class="nav-btn" id="nb-inquiries">
        <i class="ri-chat-3-line"></i><span>الأسئلة والاستفسارات</span>
      </a>
      <a href="{{ route('teacher.messaging') }}" class="nav-btn" id="nb-messaging">
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
        <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
          <i class="ri-moon-line" id="darkIcon"></i>
        </button>
        <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
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
          <input type="text" placeholder="ابحث هنا...">
          <i class="ri-search-line search-icon"></i>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
            @if ($errors->any())
                <div style="background: #FF3B30; color: white; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;">
                    <ul style="list-style: none;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Header -->
            <div class="page-header">
                <h1>إنشاء مسار جديد</h1>
                <p><i class="ri-lightbulb-flash-line"></i> أنشئ مسارك التعليمي الأول وابدأ رحلة التعليم الرائعة</p>
                <div class="breadcrumb">
                    <a href="{{ route('teacher.dashboard') }}">الرئيسية</a>
                    <span>/</span>
                    <span>إنشاء مسار جديد</span>
                </div>
            </div>

            <!-- Form Section -->
            <div class="form-container">
                <!-- Form Card -->
                <div class="form-card">
                    <h2><i class="ri-edit-box-line"></i> بيانات المسار</h2>

                    <form action="{{ route('teacher.store') }}" method="POST">
                        @csrf

                        <!-- Course Name -->
                        <div class="form-group">
                            <label>
                                <span class="required">*</span>
                                اسم المسار
                            </label>
                            <input
                                type="text"
                                name="name"
                                placeholder="أدخل اسم المسار التعليمي"
                                value="{{ old('name') }}"
                                required
                                
                            >
                            <div class="form-hint">اختر اسماً جذاباً ووصفياً</div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>
                                <span class="required">*</span>
                                الوصف
                            </label>
                            <textarea
                                name="description"
                                placeholder="اكتب وصفاً شاملاً للمسار التعليمي..."
                                required
                                
                            >{{ old('description') }}</textarea>
                            <div class="form-hint">اشرح محتوى المسار والأهداف بشكل واضح</div>
                        </div>

                        <!-- Row: Category & Level -->
                        <div class="form-row">
                            <!-- Category -->
                            <div class="form-group">
                                <label>الفئة</label>
                                <select name="category">
                                    <option value="">اختر فئة...</option>
                                    <optgroup label="الدراسات الإسلامية">
                                        <option value="quran" {{ old('category')=='quran'?'selected':'' }}>القرآن الكريم والتجويد</option>
                                        <option value="fiqh" {{ old('category')=='fiqh'?'selected':'' }}>الفقه والشريعة</option>
                                        <option value="hadith" {{ old('category')=='hadith'?'selected':'' }}>الحديث والسيرة</option>
                                        <option value="aqeedah" {{ old('category')=='aqeedah'?'selected':'' }}>العقيدة والتوحيد</option>
                                    </optgroup>
                                    <optgroup label="اللغة والأدب">
                                        <option value="arabic" {{ old('category')=='arabic'?'selected':'' }}>اللغة العربية</option>
                                        <option value="language" {{ old('category')=='language'?'selected':'' }}>اللغات الأجنبية</option>
                                        <option value="literature" {{ old('category')=='literature'?'selected':'' }}>الأدب والنصوص</option>
                                    </optgroup>
                                    <optgroup label="العلوم والتقنية">
                                        <option value="math" {{ old('category')=='math'?'selected':'' }}>الرياضيات</option>
                                        <option value="science" {{ old('category')=='science'?'selected':'' }}>العلوم الطبيعية</option>
                                        <option value="programming" {{ old('category')=='programming'?'selected':'' }}>البرمجة والتقنية</option>
                                    </optgroup>
                                    <optgroup label="العلوم الإنسانية">
                                        <option value="history" {{ old('category')=='history'?'selected':'' }}>التاريخ والجغرافيا</option>
                                        <option value="social" {{ old('category')=='social'?'selected':'' }}>الدراسات الاجتماعية</option>
                                        <option value="education" {{ old('category')=='education'?'selected':'' }}>التربية وعلم النفس</option>
                                    </optgroup>
                                    <optgroup label="المهارات العملية">
                                        <option value="business" {{ old('category')=='business'?'selected':'' }}>الأعمال والإدارة</option>
                                        <option value="design" {{ old('category')=='design'?'selected':'' }}>التصميم والفنون</option>
                                        <option value="health" {{ old('category')=='health'?'selected':'' }}>الصحة واللياقة</option>
                                    </optgroup>
                                    <option value="other" {{ old('category')=='other'?'selected':'' }}>أخرى</option>
                                </select>
                            </div>

                            <!-- Level -->
                            <div class="form-group">
                                <label>المستوى</label>
                                <select name="level">
                                    <option value="">اختر مستوى...</option>
                                    <option value="beginner" {{ old('level')=='beginner'?'selected':'' }}>مبتدئ</option>
                                    <option value="intermediate" {{ old('level')=='intermediate'?'selected':'' }}>متوسط</option>
                                    <option value="advanced" {{ old('level')=='advanced'?'selected':'' }}>متقدم</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row: Duration + Unit -->
                        <div class="form-row">
                            <div class="form-group" style="flex:2;">
                                <label>المدة الكلية للمسار <span style="font-size:11px;color:var(--text-muted);">(الدروس ستُخصم منها)</span></label>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <input
                                        type="number"
                                        name="duration"
                                        id="courseDuration"
                                        placeholder="مثلاً: 3"
                                        min="1"
                                        value="{{ old('duration') }}"
                                        style="flex:1;"
                                    >
                                    <select name="duration_unit" id="courseDurationUnit" style="width:130px;">
                                        <option value="hours" {{ old('duration_unit','hours')=='hours'?'selected':'' }}>ساعات</option>
                                        <option value="days" {{ old('duration_unit')=='days'?'selected':'' }}>أيام</option>
                                        <option value="months" {{ old('duration_unit')=='months'?'selected':'' }}>شهور</option>
                                    </select>
                                </div>
                                <div class="form-hint">الفصل الدراسي = 4 شهور مثلاً | الدورة القصيرة = ساعات أو أيام</div>
                            </div>

                            <!-- Max Students -->
                            <div class="form-group" style="flex:1;">
                                <label>الحد الأقصى للطلاب</label>
                                <input
                                    type="number"
                                    name="max_students"
                                    placeholder="∞ بلا حد"
                                    min="1"
                                    value="{{ old('max_students') }}"
                                >
                                <div class="form-hint">يُطبَّق عند القبول</div>
                            </div>
                        </div>

                        <!-- Row: Start & End Dates (optional calendar) -->
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="ri-calendar-check-line" style="color:var(--gold-dark);margin-left:4px;"></i> تاريخ بدء المسار</label>
                                <input type="date" name="start_date" value="{{ old('start_date') }}" style="direction:ltr;text-align:right;">
                                <div class="form-hint">اختياري — للمسارات ذات الجدول الزمني المحدد</div>
                            </div>
                            <div class="form-group">
                                <label><i class="ri-calendar-close-line" style="color:var(--danger);margin-left:4px;"></i> تاريخ انتهاء المسار</label>
                                <input type="date" name="end_date" value="{{ old('end_date') }}" style="direction:ltr;text-align:right;">
                                <div class="form-hint">اختياري — يُستخدم كمرجع للطلاب</div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-check-line"></i> إنشاء المسار
                            </button>
                            <a href="{{ route('teacher.courses') }}" class="btn btn-secondary">
                                <i class="ri-close-line"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Preview Card -->
                <div class="preview-card">
                    <h3><i class="ri-eye-line"></i> معاينة المسار</h3>

                    <div class="course-preview" id="preview">
                        <div class="course-preview-image">
                            <i class="ri-book-2-line"></i>
                        </div>
                        <h4>اسم المسار</h4>
                        <p>أضف معلومات المسار سيتم عرض معاينة حية هنا</p>
                        <div class="preview-info">
                            <label>الفئة:</label>
                            <strong>-</strong>
                        </div>
                        <div class="preview-info">
                            <label>المستوى:</label>
                            <strong>-</strong>
                        </div>
                        <div class="preview-info">
                            <label>المدة:</label>
                            <strong>-</strong>
                        </div>
                    </div>

                    <div style="background: var(--gold-light); border-radius: 8px; padding: 12px; margin-top: 16px;">
                        <p style="font-size: 12px; color: var(--gold-dark); display: flex; align-items: center; gap: 8px;">
                            <i class="ri-information-line"></i>
                            سيتم إضافة الدروس والاختبارات بعد إنشاء المسار
                        </p>
                    </div>
                </div>
            </div>
    </section>
  </main>
</div>

<script>
  // Dark Mode Toggle
  const html = document.documentElement;
  const darkIcon = document.getElementById('darkIcon');

  function updateTheme() {
    const currentTheme = html.getAttribute('data-theme');
    const isDark = currentTheme === 'dark';
    if (darkIcon) {
      darkIcon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
    }
  }

  function toggleDark() {
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('app-theme', newTheme);
    updateTheme();
  }

  document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('app-theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateTheme();
    // Attach event listeners for preview update
    const previewInputs = document.querySelectorAll('input[name="name"], textarea[name="description"], input[name="duration"]');
    previewInputs.forEach(function(el) { el.addEventListener('input', updatePreview); });
    const previewSelects = document.querySelectorAll('select[name="category"], select[name="level"]');
    previewSelects.forEach(function(el) { el.addEventListener('change', updatePreview); });
    const darkBtn = document.getElementById('darkBtn');
    if (darkBtn) { darkBtn.addEventListener('click', toggleDark); }
  });

  // Preview Update
  function updatePreview() {
    const name = document.querySelector('input[name="name"]').value || 'اسم المسار';
    const description = document.querySelector('textarea[name="description"]').value || 'أضف وصفاً للمسار';
    const category = document.querySelector('select[name="category"]').value || '-';
    const level = document.querySelector('select[name="level"]').value || '-';
    const duration = document.querySelector('input[name="duration"]').value || '-';

    const categoryNames = {
      programming: 'البرمجة',
      design: 'التصميم',
      business: 'الأعمال',
      language: 'اللغات',
      science: 'العلوم',
      other: 'أخرى'
    };

    const levelNames = {
      beginner: 'مبتدئ',
      intermediate: 'متوسط',
      advanced: 'متقدم'
    };

    const preview = document.getElementById('preview');
    preview.innerHTML = `
      <div class="course-preview-image">
        <i class="ri-book-2-line"></i>
      </div>
      <h4>${name}</h4>
      <p>${description.substring(0, 60)}...</p>
      <div class="preview-info">
        <label>الفئة:</label>
        <strong>${categoryNames[category] || '-'}</strong>
      </div>
      <div class="preview-info">
        <label>المستوى:</label>
        <strong>${levelNames[level] || '-'}</strong>
      </div>
      <div class="preview-info">
        <label>المدة:</label>
        <strong>${duration} ساعات</strong>
      </div>
    `;
  }
</script>
@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>



