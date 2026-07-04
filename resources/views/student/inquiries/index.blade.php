<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>أسئلتي | جمعية إجلال</title>
  <meta name="description" content="منصة إجلال التعليمية - صفحة الاستفسارات للطالب">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * {
      --gold: #C4963A;
      --gold-light: #E8D4A8;
      --gold-dark: #8B6F2D;
      --primary: #0a0e27;
      --secondary: #16213e;
      --accent: #06a77d;
      --danger: #D32F2F;
      --warning: #FF9F40;
      --text-primary: #FFFFFF;
      --text-secondary: #B0B0B0;
      --text-tertiary: #808080;
      --border-strong: rgba(196, 150, 58, 0.4);
      --border-light: rgba(196, 150, 58, 0.15);
      --surface-1: rgba(255, 255, 255, 0.02);
      --surface-2: rgba(255, 255, 255, 0.05);
      --surface-3: rgba(255, 255, 255, 0.08);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: linear-gradient(135deg, #0a0e27 0%, #16213e 50%, #1a2750 100%);
      color: var(--text-primary);
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background:
        radial-gradient(circle at 20% 50%, rgba(196, 150, 58, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(6, 167, 125, 0.03) 0%, transparent 50%);
      pointer-events: none;
      z-index: -1;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 0 15px rgba(196, 150, 58, 0.2); }
      50% { box-shadow: 0 0 30px rgba(196, 150, 58, 0.4); }
    }

    .app {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 0;
      min-height: 100vh;
    }

    .sidebar {
      background: linear-gradient(180deg, rgba(10, 14, 39, 0.98) 0%, rgba(22, 33, 62, 0.95) 100%);
      border-right: 1.5px solid var(--border-light);
      padding: 2rem 1.5rem;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      animation: fadeInLeft 0.6s ease-out;
      direction: ltr;
      scrollbar-gutter: stable;
    }

    .sidebar-logo {
      text-align: center;
      margin-bottom: 2rem;
      padding: 1rem 0 1.5rem 0;
      border-bottom: 1px solid var(--border-light);
    }

    .logo-icon {
      width: 100%;
      height: auto;
      margin: 0 auto 1.5rem auto;
      padding: 0.5rem 0;
      border-radius: 0;
      overflow: visible;
      background: transparent;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo-icon img {
      width: auto;
      height: auto;
      max-width: 90%;
      max-height: 140px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .logo-icon i {
      font-size: 2.5rem;
      color: var(--gold);
    }

    .logo-name {
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 0.2rem;
    }

    .logo-sub {
      font-size: 0.85rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
      margin-bottom: auto;
    }

    .nav-btn {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.2rem;
      background: transparent;
      color: var(--text-secondary);
      border: 1.5px solid transparent;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .nav-btn:hover {
      background: var(--surface-2);
      color: var(--gold-light);
    }

    .nav-btn.active {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.2) 0%, rgba(196, 150, 58, 0.1) 100%);
      border-color: var(--gold);
      color: var(--gold);
    }

    .nav-btn i {
      font-size: 1.3rem;
    }

    .sidebar-footer {
      border-top: 1px solid var(--border-light);
      padding-top: 1.5rem;
      margin-top: 2rem;
    }

    .logout {
      width: 100%;
      background: linear-gradient(135deg, rgba(211, 47, 47, 0.15) 0%, rgba(211, 47, 47, 0.08) 100%);
      border: 1.5px solid rgba(211, 47, 47, 0.3);
      color: #FF6B6B;
    }

    .logout:hover {
      background: rgba(211, 47, 47, 0.2);
      border-color: #FF6B6B;
    }

    .main {
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .topbar {
      background: linear-gradient(90deg, rgba(10, 14, 39, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%);
      backdrop-filter: blur(20px);
      border-bottom: 1.5px solid var(--border-light);
      padding: 1.2rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 2rem;
      animation: slideUp 0.6s ease-out;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }

    .icon-btn {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      border: 1.5px solid var(--border-light);
      background: var(--surface-1);
      color: var(--gold);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      transition: all 0.3s ease;
    }

    .icon-btn:hover {
      background: var(--surface-3);
      border-color: var(--gold);
      transform: translateY(-3px);
    }

    .g-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.15) 0%, rgba(196, 150, 58, 0.05) 100%);
      border: 1.5px solid var(--border-light);
      padding: 0.75rem 1.2rem;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--gold);
    }

    .g-badge i {
      font-size: 1.2rem;
    }

    .search-wrap {
      flex: 1;
      max-width: 400px;
      position: relative;
    }

    .search-wrap input {
      width: 100%;
      padding: 0.85rem 3.2rem 0.85rem 1rem;
      background: var(--surface-2);
      border: 1.5px solid var(--border-light);
      border-radius: 12px;
      color: var(--text-primary);
      font-family: 'Cairo', sans-serif;
      font-size: 0.95rem;
      transition: all 0.3s;
    }

    .search-wrap input::placeholder {
      color: var(--text-tertiary);
    }

    .search-wrap input:focus {
      outline: none;
      border-color: var(--gold);
      background: var(--surface-3);
    }

    .search-icon {
      position: absolute;
      right: 1.2rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-secondary);
      font-size: 1rem;
    }

    .content {
      flex: 1;
      overflow-y: auto;
      padding: 2rem;
      padding-bottom: 3rem;
    }

    .hero-card {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 24px;
      align-items: center;
      padding: 32px;
      border-radius: 28px;
      background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
      border: 1px solid rgba(255,255,255,0.1);
      animation: fadeInUp 0.6s ease-out;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.85rem 1.2rem;
      border-radius: 999px;
      background: rgba(196,150,58,0.12);
      color: var(--gold);
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .hero-card h1 {
      margin: 0;
      font-size: 2.5rem;
      line-height: 1.05;
      color: var(--text-primary);
    }

    .hero-description {
      margin: 1rem 0 0;
      max-width: 720px;
      color: var(--text-secondary);
      line-height: 1.8;
      font-size: 1rem;
    }

    .page-section {
      display: grid;
      gap: 24px;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      margin-bottom: 0;
      animation: fadeInUp 0.6s ease-out;
    }

    .page-header h1 {
      font-size: 32px;
      margin: 0;
    }

    .page-description {
      color: var(--text-secondary);
      margin-top: 10px;
      max-width: 64rem;
    }

    .page-actions {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.95rem 1.4rem;
      border-radius: 14px;
      font-weight: 700;
      color: var(--text-primary);
      text-decoration: none;
      transition: all 0.25s ease;
      border: 1px solid transparent;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(6px);
    }

    .btn:hover {
      transform: translateY(-1px);
      background: rgba(255,255,255,0.15);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      border-color: rgba(255,255,255,0.15);
      color: #111;
    }

    .btn-primary:hover {
      box-shadow: 0 12px 28px rgba(255,198,94,0.22);
    }

    .btn-secondary {
      background: rgba(255,255,255,0.08);
      color: var(--text-primary);
      border-color: rgba(255,255,255,0.12);
    }

    .btn-secondary:hover {
      background: rgba(255,255,255,0.14);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 18px;
      margin-top: 0;
    }

    .stat-box {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1.6rem;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.1);
      background: linear-gradient(135deg, rgba(196,150,58,0.1), rgba(196,150,58,0.04));
    }

    .stat-icon {
      width: 64px;
      height: 64px;
      border-radius: 18px;
      display: grid;
      place-items: center;
      color: var(--gold);
      font-size: 1.5rem;
      background: linear-gradient(135deg, rgba(196,150,58,0.2), rgba(196,150,58,0.12));
    }

    .stat-content h3 {
      margin: 0;
      font-size: 2.2rem;
      color: var(--gold);
    }

    .stat-content p {
      margin: 0;
      color: var(--text-secondary);
      font-size: 0.95rem;
    }

    .inquiries-grid {
      display: grid;
      gap: 20px;
    }

    .inquiry-card {
      display: grid;
      gap: 18px;
      padding: 24px;
      border-radius: 24px;
      border: 1px solid rgba(255,255,255,0.08);
      background: rgba(255,255,255,0.04);
      animation: fadeInUp 0.6s ease-out both;
    }

    .inquiry-card-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .inquiry-card-top h3 {
      margin: 0;
      font-size: 1.3rem;
      color: var(--text-primary);
    }

    .inquiry-meta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 10px;
      color: var(--text-secondary);
      font-size: 0.95rem;
    }

    .inquiry-meta-row span {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.8rem 1rem;
      border-radius: 999px;
      font-weight: 700;
      white-space: nowrap;
      font-size: 0.88rem;
    }

    .status-badge.pending {
      background: rgba(255,159,64,0.14);
      color: #FFB347;
    }

    .status-badge.answered {
      background: rgba(52,199,89,0.14);
      color: #7BE495;
    }

    .inquiry-body {
      margin: 0;
      color: var(--text-secondary);
      line-height: 1.8;
      font-size: 1rem;
    }

    .inquiry-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      color: var(--text-secondary);
      font-size: 0.95rem;
    }

    .inquiry-details span {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
    }

    .answer-card {
      padding: 20px;
      border-radius: 20px;
      background: rgba(196,150,58,0.08);
      border: 1px solid rgba(196,150,58,0.15);
    }

    .answer-title {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 12px;
      color: var(--gold);
      font-weight: 700;
    }

    .empty-state-card {
      display: grid;
      gap: 18px;
      justify-items: center;
      padding: 48px 32px;
      border-radius: 24px;
      border: 1px dashed rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.02);
      color: var(--text-secondary);
      text-align: center;
    }

    .empty-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 96px;
      height: 96px;
      border-radius: 50%;
      background: rgba(196,150,58,0.12);
      color: var(--gold);
      font-size: 2.4rem;
    }

    .empty-state-card h2 {
      margin: 0;
      color: var(--text-primary);
      font-size: 1.8rem;
    }

    .empty-state-card p {
      margin: 0;
      line-height: 1.8;
      max-width: 660px;
    }

    .pagination-wrapper {
      display: flex;
      justify-content: center;
      margin-top: 16px;
    }

    @media (max-width: 1024px) {
      .app {
        grid-template-columns: 1fr;
      }

      .sidebar {
        display: none;
      }
    }

    @media (max-width: 900px) {
      .hero-card {
        grid-template-columns: 1fr;
        padding: 28px;
      }
    }

    @media (max-width: 768px) {
      .content {
        padding: 1.5rem;
      }

      .hero-actions .btn {
        width: 100%;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<div class="app">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">
        @if(file_exists(public_path('images/logo/logo.png')))
          <img src="{{ asset('images/logo/logo.png?v=' . time()) }}" alt="جمعية إجلال" loading="lazy" />
        @else
          <i class="ri-book-mark-fill"></i>
        @endif
      </div>
      <div class="logo-name">جمعية إجلال</div>
      <div class="logo-sub">بمكة المكرمة</div>
    </div>

    <nav class="sidebar-nav">
      <a href="{{ route('student.index') }}" class="nav-btn">
        <i class="ri-layout-grid-line"></i>
        <span>لوحة التحكم</span>
      </a>
      <a href="{{ route('student.academy') }}" class="nav-btn">
        <i class="ri-graduation-cap-line"></i>
        <span>الأكاديمية</span>
      </a>
      <a href="{{ route('student.exams') }}" class="nav-btn">
        <i class="ri-survey-line"></i>
        <span>الاختبارات</span>
      </a>
      <a href="{{ route('student.competition') }}" class="nav-btn">
        <i class="ri-trophy-line"></i>
        <span>المتنافسين</span>
      </a>
      <a href="{{ route('student.inquiries.index') }}" class="nav-btn active">
        <i class="ri-question-answer-line"></i>
        <span>أسئلتي</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-btn logout">
          <i class="ri-logout-box-r-line"></i>
          <span>خروج</span>
        </button>
      </form>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <a href="{{ route('profile.show') }}" class="icon-btn" title="الملف الشخصي">
          <i class="ri-user-line"></i>
        </a>
        <button class="icon-btn" id="inqThemeToggle" title="الوضع الليلي">
          <i class="ri-moon-line"></i>
        </button>
        <button class="icon-btn" title="التنبيهات">
          <i class="ri-notification-3-line"></i>
        </button>
        <div class="g-badge">
          <i class="ri-fire-fill"></i>
          <span>{{ Auth::user()->points ?? 0 }} نقطة</span>
        </div>
      </div>
      <div class="search-wrap">
        <i class="ri-search-line search-icon"></i>
        <input type="text" placeholder="ابحث عن دروسك أو مساراتك...">
      </div>
    </header>

    <div class="content">
      @php
        $inquiryItems = method_exists($inquiries, 'items') ? collect($inquiries->items()) : collect($inquiries);
        $totalCount = $inquiryItems->count();
        $pendingCount = $inquiryItems->where('status', 'pending')->count();
        $answeredCount = $inquiryItems->where('status', 'answered')->count();
      @endphp

      <div class="page-section">
        <div class="page-header">
          <div>
            <h1>أسئلتي</h1>
            <p class="page-description">تابع حالة استفساراتك واطلع على ردود المعلم ضمن واجهة متناسقة مع لوحة الطالب.</p>
          </div>
          <div class="page-actions">
            <a href="{{ route('student.index') }}" class="btn btn-secondary">
              <i class="ri-arrow-right-line"></i>
              العودة إلى اللوحة الرئيسية
            </a>
          </div>
        </div>

        <div class="hero-card">
          <div>
            <div class="hero-badge"><i class="ri-question-answer-line"></i> صفحة الاستفسارات</div>
            <h1>أسئلتي</h1>
            <p class="hero-description">استعرض سجل أسئلتك، راجع حالة المتابعة، واطلع على الردود من المعلم بسهولة.</p>
          </div>
          <div class="hero-actions">
            <a href="{{ route('student.academy') }}" class="btn btn-primary">
              <i class="ri-graduation-cap-line"></i>
              العودة إلى الأكاديمية
            </a>
          </div>
        </div>

        <div class="stats-grid">
          <div class="stat-box">
            <div class="stat-icon"><i class="ri-question-answer-line"></i></div>
            <div class="stat-content">
              <h3>{{ $totalCount }}</h3>
              <p>عدد الاستفسارات</p>
            </div>
          </div>
          <div class="stat-box">
            <div class="stat-icon"><i class="ri-time-line"></i></div>
            <div class="stat-content">
              <h3>{{ $pendingCount }}</h3>
              <p>قيد المراجعة</p>
            </div>
          </div>
          <div class="stat-box">
            <div class="stat-icon"><i class="ri-checkbox-circle-line"></i></div>
            <div class="stat-content">
              <h3>{{ $answeredCount }}</h3>
              <p>تم الرد عليها</p>
            </div>
          </div>
        </div>

        @if($inquiryItems->isEmpty())
          <div class="empty-state-card">
            <div class="empty-icon"><i class="ri-question-answer-line"></i></div>
            <h2>لم ترسل أي استفسار بعد</h2>
            <p>ابدأ رحلة التعلم الآن وأرسل أول سؤال إلى معلمك من داخل أحد الدروس.</p>
          </div>
        @else
          <div class="inquiries-grid">
            @foreach($inquiryItems as $inquiry)
              <article class="inquiry-card">
                <div class="inquiry-card-top">
                  <div>
                    <h3>{{ $inquiry->lesson?->title ?? 'درس غير معروف' }}</h3>
                    <div class="inquiry-meta-row">
                      <span><i class="ri-calendar-line"></i> {{ $inquiry->created_at?->format('d/m/Y H:i') ?? '' }}</span>
                      <span><i class="ri-book-open-line"></i> {{ $inquiry->course?->name ?? 'الدورة غير محددة' }}</span>
                    </div>
                  </div>
                  <span class="status-badge {{ $inquiry->status === 'pending' ? 'pending' : 'answered' }}">
                    {{ $inquiry->status === 'pending' ? 'قيد المراجعة' : 'تم الرد' }}
                  </span>
                </div>
                <div class="inquiry-body">{{ $inquiry->question_text }}</div>
                <div class="inquiry-details">
                  <span><i class="ri-user-line"></i> {{ $inquiry->teacher?->name ?? 'المعلم غير متوفر' }}</span>
                </div>
                @if($inquiry->status === 'answered' && !empty($inquiry->answer_text))
                  <div class="answer-card">
                    <div class="answer-title"><i class="ri-chat-3-line"></i> رد المعلم</div>
                    <p>{{ $inquiry->answer_text }}</p>
                  </div>
                @endif
              </article>
            @endforeach
          </div>

          <div class="pagination-wrapper">{{ $inquiries->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('inqThemeToggle').addEventListener('click', function () {
      document.documentElement.style.filter = 'invert(1)';
      setTimeout(function () { document.documentElement.style.filter = ''; }, 2000);
    });
  });
</script>

@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>



