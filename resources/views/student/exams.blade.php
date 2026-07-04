<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الاختبارات | جمعية إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * {
      --gold: var(--theme-gold);
      --gold-light: var(--theme-gold);
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
      font-family: 'Cairo', sans-serif;
      font-size: 3.35rem;
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

    .status-available {
      background: rgba(6, 167, 125, 0.15);
      color: var(--accent);
      border: 1px solid rgba(6, 167, 125, 0.3);
    }

    .status-in-progress {
      background: rgba(255, 159, 64, 0.15);
      color: var(--warning);
      border: 1px solid rgba(255, 159, 64, 0.3);
    }

    .status-completed {
      background: color-mix(in srgb, var(--theme-success) 16%, transparent);
      color: var(--theme-success);
      border: 1px solid color-mix(in srgb, var(--theme-success) 32%, transparent);
    }

    /* ===== EXAMS GRID ===== */
    .exams-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .exam-card {
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

    .exam-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(196, 150, 58, 0.1), transparent);
      transition: all 0.6s;
    }

    .exam-card:hover {
      border-color: var(--gold);
      background: var(--surface-3);
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.25);
    }

    .exam-card:hover::before {
      right: 100%;
    }

    .exam-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .exam-icon {
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

    .exam-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.8rem;
      line-height: 1.4;
      flex-grow: 1;
    }

    .exam-desc {
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      line-height: 1.6;
    }

    .exam-meta {
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .meta-item i {
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

    .exam-action {
      width: 100%;
      padding: 0.9rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: var(--theme-on-gold, #fff);
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

    .exam-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(196, 150, 58, 0.3);
    }

    .exam-action:disabled {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.3) 0%, rgba(139, 111, 45, 0.3) 100%);
      cursor: not-allowed;
      opacity: 0.6;
    }

    .exam-action.secondary {
      background: var(--surface-2);
      color: var(--gold);
      border: 1.5px solid var(--border-light);
    }

    .exam-action.view-results {
      background: color-mix(in srgb, var(--theme-success) 14%, var(--surface-2));
      color: var(--theme-success);
      border: 1.5px solid color-mix(in srgb, var(--theme-success) 32%, transparent);
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

      .exams-grid {
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

      .exams-grid {
        grid-template-columns: 1fr;
      }

      .section-title {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .header-bar { padding: 0.8rem 1rem; }
      .hero-content h1 { font-size: 1.8rem; }
      .hero-section { padding: 1.5rem 1rem; }
      .main-content { padding: 1rem; }
      .section-title { font-size: 1.3rem; }
    }

    a { text-decoration: none; color: inherit; }

    .header-bar,
    .hero-section,
    .main-content {
      position: relative;
      z-index: 1;
    }

    html[data-theme="light"] body::before {
      background:
        radial-gradient(circle at 8% 10%, rgba(198, 166, 117, 0.08) 0%, transparent 34%),
        radial-gradient(circle at 82% 76%, rgba(198, 166, 117, 0.05) 0%, transparent 38%);
    }

    html[data-theme="light"] .exam-card {
      background: #ffffff;
      border-color: var(--theme-border);
      box-shadow: 0 4px 20px rgba(34, 43, 61, 0.08);
    }

    html[data-theme="light"] .exam-card:hover {
      background: #fafafa;
      box-shadow: 0 12px 40px rgba(196, 150, 58, 0.15);
    }

    html[data-theme="light"] .header-bar {
      background: #ffffff;
      border-bottom-color: var(--theme-border);
    }

    html[data-theme="light"] .badge {
      background: rgba(198, 166, 117, 0.08);
      border-color: var(--theme-border);
      color: var(--theme-gold-dark);
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
    <button class="icon-btn" id="examsThemeToggle" title="الوضع الليلي">
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
    <h1>مركز الاختبارات</h1>
    <p>اختبر معلوماتك وقياس مستواك الدراسي. اختبارات شاملة تغطي جميع المواضيع والمستويات لتطوير مهاراتك.</p>

    <div class="hero-badges">
      <div class="badge">
        <i class="ri-file-list-line"></i>
        {{ count($allExams) }} اختبار إجمالي
      </div>
      <div class="badge">
        <i class="ri-questionnaire-line"></i>
        {{ array_sum(array_map(fn($e) => $e['questions_count'] ?? 0, $allExams)) }} أسئلة
      </div>
      <div class="badge">
        <i class="ri-timer-line"></i>
        {{ array_sum(array_map(fn($e) => $e['duration'] ?? 0, $allExams)) }} دقيقة إجمالية
      </div>
    </div>
  </div>

  <div class="hero-visual">
    <i class="visual-icon ri-survey-fill"></i>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
  <!-- الاختبارات المتاحة -->
  @if(count($availableExams) > 0)
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <i class="ri-lock-unlock-line"></i>
          اختبارات متاحة للحل
        </h2>
        <p class="section-subtitle">اختبارات جديدة جاهزة لك - ابدأ الحل الآن وحقق أفضل النتائج</p>
      </div>
    </div>

    <div class="exams-grid">
      @foreach($availableExams as $exam)
        <div class="exam-card">
          <span class="status-badge status-available">
            <i class="ri-check-circle-line"></i> متاح
          </span>

          <div class="exam-header">
            <div>
              <h3 class="exam-title">{{ $exam['name'] }}</h3>
            </div>
            <div class="exam-icon">
              <i class="ri-file-list-3-line"></i>
            </div>
          </div>

          <p class="exam-desc">{{ $exam['description'] ?? 'اختبار شامل في المادة' }}</p>

          <div class="exam-meta">
            <div class="meta-item">
              <i class="ri-timer-line"></i>
              <span>{{ $exam['duration'] ?? 60 }} دقائق</span>
            </div>
            <div class="meta-item">
              <i class="ri-questionnaire-line"></i>
              <span>{{ $exam['questions_count'] ?? 0 }} أسئلة</span>
            </div>
            <div class="meta-item">
              <i class="ri-award-line"></i>
              <span>اختبار من المستوى {{ $exam['level'] ?? 'متقدم' }}</span>
            </div>
          </div>

          <a href="{{ route('student.exam.show', $exam['id']) }}" class="exam-action">
            <i class="ri-play-circle-line"></i>
            ابدأ الاختبار الآن
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <!-- الاختبارات قيد الحل -->
  @if(count($inProgressExams) > 0)
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <i class="ri-hourglass-line"></i>
          اختبارات قيد الحل
        </h2>
        <p class="section-subtitle">استكمل اختباراتك التي بدأت حلها مسبقاً</p>
      </div>
    </div>

    <div class="exams-grid">
      @foreach($inProgressExams as $exam)
        <div class="exam-card">
          <span class="status-badge status-in-progress">
            <i class="ri-hourglass-line"></i> قيد الحل
          </span>

          <div class="exam-header">
            <div>
              <h3 class="exam-title">{{ $exam['name'] }}</h3>
            </div>
            <div class="exam-icon">
              <i class="ri-time-line"></i>
            </div>
          </div>

          <p class="exam-desc">{{ $exam['description'] ?? 'اختبار شامل في المادة' }}</p>

          <div class="exam-meta">
            <div class="meta-item">
              <i class="ri-timer-line"></i>
              <span>{{ $exam['duration'] ?? 60 }} دقائق</span>
            </div>
            <div class="meta-item">
              <i class="ri-questionnaire-line"></i>
              <span>{{ $exam['questions_count'] ?? 0 }} أسئلة</span>
            </div>
            <div class="meta-item">
              <i class="ri-progress-1-line"></i>
              <span>{{ $exam['progress'] ?? 0 }}% مكتمل</span>
            </div>
          </div>

          <div class="progress-section">
            <div class="progress-label">
              <span>التقدم</span>
              <span style="color: var(--warning);">{{ $exam['progress'] ?? 0 }}%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: {{ $exam['progress'] ?? 0 }}%; background: var(--warning);"></div>
            </div>
          </div>

          <a href="{{ route('student.exam.show', $exam['id']) }}" class="exam-action secondary">
            <i class="ri-play-circle-line"></i>
            استكمل الحل
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <!-- الاختبارات المكتملة -->
  @if(count($completedExams) > 0)
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <i class="ri-check-double-line"></i>
          اختبارات مكتملة
        </h2>
        <p class="section-subtitle">نتائجك السابقة - استعرض الإجابات والتفاصيل</p>
      </div>
    </div>

    <div class="exams-grid">
      @foreach($completedExams as $exam)
        <div class="exam-card">
          <span class="status-badge status-completed">
            <i class="ri-check-line"></i> مكتمل
          </span>

          <div class="exam-header">
            <div>
              <h3 class="exam-title">{{ $exam['name'] }}</h3>
            </div>
            <div class="exam-icon">
              <i class="ri-checkbox-circle-line"></i>
            </div>
          </div>

          <p class="exam-desc">{{ $exam['description'] ?? 'اختبار شامل في المادة' }}</p>

          <div class="exam-meta">
            <div class="meta-item">
              <i class="ri-timer-line"></i>
              <span>{{ $exam['duration'] ?? 60 }} دقائق</span>
            </div>
            <div class="meta-item">
              <i class="ri-questionnaire-line"></i>
              <span>{{ $exam['questions_count'] ?? 0 }} أسئلة</span>
            </div>
            <div class="meta-item">
              <i class="ri-medal-fill"></i>
              <span>علامتك: {{ $exam['score'] ?? '-' }}/100</span>
            </div>
          </div>

          <div class="progress-section">
            <div class="progress-label">
              <span>النتيجة</span>
              <span style="color: var(--theme-success);">{{ $exam['score'] ?? '-' }}%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: {{ $exam['score'] ?? 0 }}%; background: var(--theme-success);"></div>
            </div>
          </div>

          <a href="{{ route('student.exam.show', $exam['id']) }}" class="exam-action view-results">
            <i class="ri-eye-line"></i>
            عرض النتائج
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <!-- حالة فارغة -->
  @if(count($availableExams) === 0 && count($inProgressExams) === 0 && count($completedExams) === 0)
    <div class="exams-grid">
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-inbox-archive-line"></i></div>
        <h3 class="empty-title">لا توجد اختبارات حالياً</h3>
        <p class="empty-desc">سيتم إتاحة اختبارات جديدة قريباً. تابع صفحتك للحصول على آخر التحديثات!</p>
      </div>
    </div>
  @endif
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('examsThemeToggle').addEventListener('click', function () {
      if (typeof window.toggleThemeUniversal === 'function') {
        window.toggleThemeUniversal();
      }
    });
  });
</script>

@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>



