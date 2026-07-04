<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الأكاديمية | جمعية إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * {
      --gold: var(--theme-gold);
      --gold-light: rgba(198, 166, 117, 0.38);
      --gold-dark: var(--theme-gold-dark);
      --primary: var(--theme-page-bg);
      --secondary: var(--theme-surface);
      --accent: var(--theme-success);
      --danger: var(--theme-danger);
      --warning: var(--theme-pending);
      --text-primary: var(--theme-text);
      --text-secondary: var(--theme-text-soft);
      --text-tertiary: var(--theme-muted);
      --border-strong: var(--theme-border-strong);
      --border-light: var(--theme-border-light);
      --surface-1: var(--theme-surface);
      --surface-2: var(--theme-surface-2);
      --surface-3: rgba(198, 166, 117, 0.1);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: var(--theme-page-bg);
      color: var(--theme-text);
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(circle at 8% 10%, rgba(198, 166, 117, 0.24) 0%, transparent 34%),
        radial-gradient(circle at 82% 76%, rgba(198, 166, 117, 0.14) 0%, transparent 38%);
      pointer-events: none;
      z-index: 0;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes scaleIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    /* ===== HEADER ===== */
    .header-bar {
      background: linear-gradient(90deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--theme-border);
      padding: 1.2rem 3rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
      animation: fadeInDown 0.6s ease-out;
    }

    .back-navigation {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      color: var(--gold);
      text-decoration: none;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      padding: 0.8rem 1.2rem;
      border-radius: 10px;
    }

    .back-navigation:hover {
      background: var(--theme-gold-soft);
      gap: 1.2rem;
      color: var(--theme-gold);
    }

    .header-actions {
      display: flex;
      gap: 1rem;
    }

    .icon-btn {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      border: 1px solid var(--theme-border);
      background: var(--theme-surface-2);
      color: var(--gold);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      transition: all 0.3s ease;
    }

    .icon-btn:hover {
      background: var(--theme-gold-soft);
      border-color: var(--gold);
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(196, 150, 58, 0.2);
    }

    /* ===== HERO SECTION ===== */
    .hero-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
      padding: 4rem 3rem;
      max-width: 1600px;
      margin: 0 auto;
      animation: fadeInUp 0.7s ease-out 0.1s both;
    }

    .hero-content h1 {
      font-family: 'Playfair Display', serif;
      font-size: 3.5rem;
      font-weight: 900;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-content p {
      font-size: 1.15rem;
      color: var(--text-secondary);
      line-height: 1.8;
      margin-bottom: 2rem;
      max-width: 550px;
    }

    .hero-badges {
      display: flex;
      gap: 1rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.15) 0%, rgba(196, 150, 58, 0.05) 100%);
      border: 1.5px solid var(--border-light);
      padding: 0.75rem 1.3rem;
      border-radius: 10px;
      font-size: 0.95rem;
      color: var(--gold-light);
      font-weight: 600;
    }

    .badge i {
      font-size: 1.1rem;
      color: var(--gold);
    }

    .hero-visual {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 400px;
    }

    .visual-icon {
      font-size: 200px;
      color: var(--gold);
      opacity: 0.9;
      animation: scaleIn 0.8s ease-out;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
      padding: 3rem;
      max-width: 1600px;
      margin: 0 auto;
    }

    .section-header {
      margin-bottom: 3rem;
      padding-bottom: 1.5rem;
      border-bottom: 2px solid var(--border-light);
      animation: fadeInUp 0.7s ease-out 0.2s both;
    }

    .section-title {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      color: var(--text-primary);
    }

    .section-title i {
      font-size: 2.2rem;
      color: var(--gold);
    }

    .section-subtitle {
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .status-badge {
      display: inline-block;
      padding: 0.4rem 0.8rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 1rem;
    }

    .status-active {
      background: rgba(6, 167, 125, 0.15);
      color: var(--accent);
      border: 1px solid rgba(6, 167, 125, 0.3);
    }

    .status-pending {
      background: rgba(255, 159, 64, 0.15);
      color: var(--warning);
      border: 1px solid rgba(255, 159, 64, 0.3);
    }

    .status-rejected {
      background: rgba(211, 47, 47, 0.15);
      color: var(--danger);
      border: 1px solid rgba(211, 47, 47, 0.3);
    }

    /* ===== COURSES GRID ===== */
    .courses-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .course-card {
      background: var(--surface-1);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      animation: fadeInUp 0.6s ease-out both;
    }

    .course-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(196, 150, 58, 0.1), transparent);
      transition: all 0.6s;
    }

    .course-card.clickable {
      cursor: pointer;
    }

    .status-panel {
      margin-bottom: 2.5rem;
    }

    .status-card {
      background: var(--theme-surface);
      border: 1px solid var(--theme-border);
      border-radius: 24px;
      padding: 1.6rem;
      backdrop-filter: blur(18px);
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
      animation: fadeInUp 0.7s ease-out both;
    }

    .status-card .section-header {
      margin-bottom: 1rem;
      border-bottom: none;
      padding-bottom: 0;
    }

    .status-list {
      display: grid;
      gap: 12px;
      margin-top: 1rem;
    }

    .status-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      padding: 1rem 1.2rem;
      border-radius: 16px;
      background: var(--theme-surface-2);
      border: 1px solid var(--theme-border);
      color: var(--text-secondary);
      transition: all 0.2s ease;
    }

    .status-row:hover {
      background: var(--theme-gold-soft);
      border-color: rgba(196, 150, 58, 0.18);
    }

    .course-label {
      font-weight: 600;
      color: var(--text-primary);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      min-width: 0;
    }

    .status-row .status-badge {
      margin: 0;
    }

    .course-card:hover {
      border-color: var(--gold);
      background: var(--surface-3);
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.25);
    }

    .course-card:hover::before {
      right: 100%;
    }

    .course-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .course-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.2) 0%, rgba(196, 150, 58, 0.1) 100%);
      border: 2px solid var(--border-light);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 2rem;
      flex-shrink: 0;
    }

    .course-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.8rem;
      line-height: 1.4;
      flex-grow: 1;
    }

    .course-info {
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .info-item i {
      color: var(--gold);
      width: 18px;
      font-size: 1.1rem;
    }

    .progress-section {
      margin-bottom: 1.5rem;
    }

    .progress-label {
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
      color: var(--text-secondary);
      margin-bottom: 0.6rem;
      font-weight: 600;
    }

    .progress-bar {
      width: 100%;
      height: 8px;
      background: var(--theme-soft);
      border-radius: 4px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-radius: 4px;
      transition: width 0.5s ease;
    }

    .course-action {
      width: 100%;
      padding: 0.9rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      margin-top: auto;
      text-decoration: none;
    }

    .course-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(196, 150, 58, 0.3);
    }

    .course-action:disabled {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.3) 0%, rgba(139, 111, 45, 0.3) 100%);
      cursor: not-allowed;
      opacity: 0.6;
    }

    .course-action.secondary {
      background: var(--surface-2);
      color: var(--gold);
      border: 1.5px solid var(--border-light);
    }

    .course-action.danger {
      background: linear-gradient(135deg, rgba(211, 47, 47, 0.2) 0%, rgba(211, 47, 47, 0.15) 100%);
      color: var(--danger);
      border: 1.5px solid rgba(211, 47, 47, 0.3);
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      grid-column: 1 / -1;
      background: var(--surface-2);
      border: 2px dashed var(--border-light);
      border-radius: 16px;
      padding: 4rem 2rem;
      text-align: center;
    }

    .empty-icon {
      font-size: 5rem;
      color: var(--gold);
      opacity: 0.4;
      margin-bottom: 1rem;
    }

    .empty-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.8rem;
    }

    .empty-desc {
      color: var(--text-secondary);
      font-size: 1rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .hero-section {
        grid-template-columns: 1fr;
        gap: 2rem;
      }

      .courses-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      }
    }

    @media (max-width: 768px) {
      .header-bar {
        padding: 1rem 1.5rem;
        flex-direction: column;
        gap: 1rem;
      }

      .hero-section {
        padding: 2rem 1.5rem;
      }

      .hero-content h1 {
        font-size: 2.2rem;
      }

      .visual-icon {
        font-size: 150px;
      }

      .main-content {
        padding: 1.5rem;
      }

      .courses-grid {
        grid-template-columns: 1fr;
      }

      .section-title {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .header-bar { padding: 0.8rem 1rem; flex-direction: row; justify-content: space-between; }
      .hero-content h1 { font-size: 1.8rem; }
      .hero-section { padding: 1.5rem 1rem; }
      .main-content { padding: 1rem; }
      .section-title { font-size: 1.3rem; }
      .courses-grid { grid-template-columns: 1fr; }
    }

    a { text-decoration: none; color: inherit; }

    .header-bar,
    .hero-section,
    .main-content {
      position: relative;
      z-index: 1;
    }
  </style>
</head>
<body>

<!-- HEADER -->
<div class="header-bar">
  <a href="{{ route('student.index') }}" class="back-navigation">
    <i class="ri-arrow-right-line"></i>
    العودة للوحة التحكم
  </a>
  <div class="header-actions">
    <button class="icon-btn" id="academyThemeToggle" title="الوضع الليلي">
      <i class="ri-moon-line"></i>
    </button>
    <button class="icon-btn" title="التنبيهات">
      <i class="ri-notification-3-line"></i>
    </button>
  </div>
</div>

<!-- HERO SECTION -->
<div class="hero-section">
  <div class="hero-content">
    <h1>الأكاديمية المتقدمة</h1>
    <p>استكشف مسارات تعليمية شاملة ومتنوعة، واختر ما يناسب أهدافك العلمية. تعلم من أفضل المعلمين والخبراء في المجال.</p>

    <div class="hero-badges">
      <div class="badge">
        <i class="ri-book-line"></i>
        {{ $enrolledCourses->count() }} مسارات نشطة
      </div>
      <div class="badge">
        <i class="ri-graduation-cap-line"></i>
        {{ $availableCourses->count() }} متاح للتسجيل
      </div>
      <div class="badge">
        <i class="ri-users-line"></i>
        {{ $enrolledCourses->sum(fn($c) => $c->studentCount()) ?? 0 }} طالب
      </div>
    </div>
  </div>

  <div class="hero-visual">
    <i class="visual-icon ri-graduation-cap-fill"></i>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
  <!-- مساراتي النشطة -->
  @if($enrolledCourses->count() > 0)
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <i class="ri-play-circle-line"></i>
          مساراتي النشطة
        </h2>
        <p class="section-subtitle">المسارات التي أنت مسجل فيها حالياً - استمر في التعلم وأكمل دراستك</p>
      </div>
    </div>

    <div class="courses-grid">
      @foreach($enrolledCourses as $course)
        <div class="course-card clickable" data-href="{{ route('student.course.show', $course->id) }}">
          <span class="status-badge status-active">
            <i class="ri-check-circle-line"></i> نشط
          </span>

          <div class="course-header">
            <div>
              <h3 class="course-title">{{ $course->name }}</h3>
            </div>
            <div class="course-icon">
              <i class="ri-book-open-line"></i>
            </div>
          </div>

          <div class="course-info">
            <div class="info-item">
              <i class="ri-video-line"></i>
              <span>{{ $courseDetails[$course->id]['total'] ?? 0 }} دروس</span>
            </div>
            <div class="info-item">
              <i class="ri-users-line"></i>
              <span>{{ $course->studentCount() ?? 0 }} طالب مسجل</span>
            </div>
            <div class="info-item">
              <i class="ri-bar-chart-line"></i>
              <span>{{ $courseDetails[$course->id]['completed'] ?? 0 }} درس مكتمل</span>
            </div>
          </div>

          <div class="progress-section">
            <div class="progress-label">
              <span>التقدم</span>
              <span style="color: var(--gold);">{{ $courseProgress[$course->id] ?? 0 }}%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: {{ $courseProgress[$course->id] ?? 0 }}%"></div>
            </div>
          </div>

          <a href="{{ route('student.course.show', $course->id) }}" class="course-action">
            <i class="ri-play-circle-line"></i>
            استمر في الدراسة
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <!-- مساراتي المعلقة -->
  @if($pendingCourses->count() > 0)
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <i class="ri-hourglass-line"></i>
          طلبات في الانتظار
        </h2>
        <p class="section-subtitle">طلبات التحاقك المعلقة - في انتظار موافقة المعلم</p>
      </div>
    </div>

    <div class="courses-grid">
      @foreach($pendingCourses as $course)
        <div class="course-card clickable" data-href="{{ route('student.course.show', $course->id) }}">
          <span class="status-badge status-pending">
            <i class="ri-hourglass-line"></i> معلق
          </span>

          <div class="course-header">
            <div>
              <h3 class="course-title">{{ $course->name }}</h3>
            </div>
            <div class="course-icon">
              <i class="ri-time-line"></i>
            </div>
          </div>

          <div class="course-info">
            <div class="info-item">
              <i class="ri-video-line"></i>
              <span>{{ $course->lessons()->count() ?? 0 }} دروس</span>
            </div>
            <div class="info-item">
              <i class="ri-users-line"></i>
              <span>{{ $course->studentCount() ?? 0 }} طالب مسجل</span>
            </div>
            <div class="info-item">
              <i class="ri-information-line"></i>
              <span>في انتظار موافقة المعلم</span>
            </div>
          </div>

          <div class="progress-section">
            <div class="progress-label">
              <span>الحالة</span>
              <span style="color: var(--warning);">قيد المراجعة</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 0%; background: var(--warning);"></div>
            </div>
          </div>

          <button class="course-action" disabled>
            <i class="ri-hourglass-line"></i>
            قيد الانتظار
          </button>
        </div>
      @endforeach
    </div>
  @endif

  <!-- مساراتي المرفوضة -->
  @if($rejectedCourses->count() > 0)
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <i class="ri-close-circle-line"></i>
          طلبات مرفوضة
        </h2>
        <p class="section-subtitle">طلبات التحاق لم توافق عليها - يمكنك إعادة الطلب</p>
      </div>
    </div>

    <div class="courses-grid">
      @foreach($rejectedCourses as $course)
        <div class="course-card clickable" data-href="{{ route('student.course.show', $course->id) }}">
          <span class="status-badge status-rejected">
            <i class="ri-close-line"></i> مرفوض
          </span>

          <div class="course-header">
            <div>
              <h3 class="course-title">{{ $course->name }}</h3>
            </div>
            <div class="course-icon">
              <i class="ri-close-line"></i>
            </div>
          </div>

          <div class="course-info">
            <div class="info-item">
              <i class="ri-video-line"></i>
              <span>{{ $course->lessons()->count() ?? 0 }} دروس</span>
            </div>
            <div class="info-item">
              <i class="ri-users-line"></i>
              <span>{{ $course->studentCount() ?? 0 }} طالب مسجل</span>
            </div>
            <div class="info-item">
              <i class="ri-alert-line"></i>
              <span>تم رفض طلبك</span>
            </div>
          </div>

          <div class="progress-section">
            <div class="progress-label">
              <span>الحالة</span>
              <span style="color: var(--danger);">مرفوض</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 0%; background: var(--danger);"></div>
            </div>
          </div>

          <form action="{{ route('student.request-enrollment', $course->id) }}" method="POST" style="width: 100%; display: flex;">
            @csrf
            <button type="submit" class="course-action danger" style="flex: 1;">
              <i class="ri-refresh-line"></i>
              إعادة الطلب
            </button>
          </form>
        </div>
      @endforeach
    </div>
  @endif

  @php
    $allCourses = collect([])
      ->concat($enrolledCourses)
      ->concat($pendingCourses)
      ->concat($rejectedCourses)
      ->concat($availableCourses)
      ->unique('id');
  @endphp

  <!-- المسارات المتاحة للتسجيل -->
  <div class="section-header">
    <div>
      <h2 class="section-title">
        <i class="ri-unlock-line"></i>
        مسارات متاحة للتسجيل
      </h2>
      <p class="section-subtitle">مسارات متاحة وحالة كل المسارات الحالية في هذا القسم.</p>
    </div>
  </div>

  @if($availableCourses->count() > 0)
    <div class="courses-grid">
      @foreach($availableCourses as $course)
        <div class="course-card clickable" data-href="{{ route('student.course.show', $course->id) }}">
          <span class="status-badge status-active">
            <i class="ri-star-line"></i> متاح
          </span>

          <div class="course-header">
            <div>
              <h3 class="course-title">{{ $course->name }}</h3>
            </div>
            <div class="course-icon">
              <i class="ri-book-2-line"></i>
            </div>
          </div>

          <div class="course-info">
            <div class="info-item">
              <i class="ri-video-line"></i>
              <span>{{ $course->lessons()->count() ?? 0 }} دروس</span>
            </div>
            <div class="info-item">
              <i class="ri-users-line"></i>
              <span>{{ $course->studentCount() ?? 0 }} طالب مسجل</span>
            </div>
            <div class="info-item">
              <i class="ri-book-mark-line"></i>
              <span>مستوى {{ $course->level ?? 'متقدم' }}</span>
            </div>
          </div>

          <div class="progress-section">
            <div class="progress-label">
              <span>متاح للتسجيل</span>
              <span style="color: var(--accent);">مفتوح</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 100%; background: var(--accent);"></div>
            </div>
          </div>

          <form action="{{ route('student.request-enrollment', $course->id) }}" method="POST" style="width: 100%; display: flex;">
            @csrf
            <button type="submit" class="course-action">
              <i class="ri-user-add-line"></i>
              سجل الآن
            </button>
          </form>
        </div>
      @endforeach
    </div>
  @else
    <div class="courses-grid">
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-inbox-archive-line"></i></div>
        <h3 class="empty-title">جميع المسارات مسجل فيها!</h3>
        <p class="empty-desc">أنت مسجل بالفعل في جميع المسارات المتاحة. استمر في دراستك وحقق أحلامك!</p>
      </div>
    </div>
  @endif

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('academyThemeToggle').addEventListener('click', function () {
      if (typeof window.toggleThemeUniversal === 'function') {
        window.toggleThemeUniversal();
      }
    });

    document.querySelectorAll('.course-card.clickable').forEach(function (card) {
      card.addEventListener('click', function (event) {
        if (event.target.closest('a, button, input, textarea, select, form')) {
          return;
        }
        const href = card.dataset.href;
        if (href) {
          window.location.href = href;
        }
      });
    });
  });
</script>

@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>

