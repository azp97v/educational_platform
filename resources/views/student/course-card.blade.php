<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>بطاقة المسار | {{ $course->name }}</title>
  <meta name="description" content="بطاقة المسار للطالب في منصة إجلال">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Cairo', sans-serif;
    }
    html, body {
      min-height: 100%;
      background: radial-gradient(circle at top left, rgba(196,150,58,0.08), transparent 30%),
                  radial-gradient(circle at bottom right, rgba(6,167,125,0.06), transparent 25%),
                  linear-gradient(135deg, var(--theme-page-bg) 0%, var(--theme-surface) 55%, var(--theme-page-bg) 100%);
      color: var(--text-primary);
    }
    body {
      padding: 16px;
    }
    .page-shell {
      max-width: 1140px;
      margin: 0 auto;
      display: grid;
      gap: 18px;
    }
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 18px 22px;
      border-radius: 22px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.12);
      box-shadow: 0 24px 60px rgba(0,0,0,0.18);
      backdrop-filter: blur(14px);
    }
    .page-header h1 {
      font-size: 24px;
      font-weight: 800;
      letter-spacing: 0.02em;
    }
    .page-header p {
      color: var(--text-secondary);
      font-size: 14px;
      margin-top: 6px;
    }
    .course-card {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 26px;
      padding: 28px;
      box-shadow: 0 28px 80px rgba(0,0,0,0.22);
      overflow: hidden;
      animation: fadeInUp 0.5s ease forwards;
    }
    .course-card::before {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      background: linear-gradient(180deg, rgba(255,255,255,0.08), transparent 45%);
      transform: skewY(-2deg);
      opacity: 0.7;
    }
    .course-card-inner {
      position: relative;
      z-index: 1;
      display: grid;
      gap: 20px;
    }
    .topbar-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
    .topbar-actions .icon-btn {
      width: 46px;
      height: 46px;
      border-radius: 16px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.14);
      color: var(--text-primary);
      display: grid;
      place-items: center;
      cursor: pointer;
      transition: transform 0.2s ease, background 0.2s ease;
    }
    .topbar-actions .icon-btn:hover {
      transform: translateY(-1px);
      background: rgba(255,255,255,0.12);
    }
    .topbar-actions .icon-btn i {
      font-size: 20px;
    }
    .course-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 14px;
      background: rgba(196,150,58,0.12);
      color: var(--text-primary);
      font-size: 13px;
      font-weight: 600;
    }
    .course-title {
      font-size: 32px;
      font-weight: 800;
      line-height: 1.1;
    }
    .course-meta {
      display: grid;
      grid-template-columns: repeat(3, minmax(140px, 1fr));
      gap: 14px;
    }
    .course-meta-item {
      padding: 16px;
      border-radius: 18px;
      background: rgba(255,255,255,0.02);
      border: 1px solid rgba(255,255,255,0.08);
      color: var(--text-secondary);
      font-size: 14px;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .course-meta-item strong {
      font-size: 18px;
      color: var(--text-primary);
    }
    .course-description {
      color: var(--text-secondary);
      line-height: 1.85;
      font-size: 15px;
      padding: 18px 0;
      border-top: 1px solid rgba(255,255,255,0.08);
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .action-block {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      align-items: center;
    }
    .btn-primary,
    .btn-secondary {
      padding: 14px 22px;
      border-radius: 14px;
      border: none;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .btn-primary {
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: var(--theme-page-bg);
      box-shadow: 0 18px 40px rgba(196,150,58,0.28);
    }
    .btn-secondary {
      background: rgba(255,255,255,0.06);
      color: var(--text-primary);
      border: 1px solid rgba(255,255,255,0.12);
    }
    .btn-primary:hover,
    .btn-secondary:hover {
      transform: translateY(-1px);
    }
    .message-card {
      padding: 18px 22px;
      border-radius: 18px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.12);
      color: var(--text-secondary);
      display: grid;
      gap: 10px;
    }
    .message-card strong {
      color: var(--text-primary);
    }
    .actions-row {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: center;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(18px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 860px) {
      .course-meta {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="page-shell">
    <section class="page-header">
      <div>
        <span class="course-badge"><i class="ri-notification-line"></i> إشعار المسار الجديد</span>
        <h1>بطاقة المسار</h1>
        <p>هذه الصفحة تعرض تفاصيل المسار الذي وصلك عبر الإشعار، وتوفر زر الانضمام أو فتح المسار مباشرة.</p>
      </div>
      <div class="topbar-actions">
        <button class="icon-btn" title="التنبيهات"><i class="ri-notification-3-line"></i></button>
      </div>
      @include('components.notification-bell')
    </section>

    <section class="course-card">
      <div class="course-card-inner">
        <div>
          <div class="course-badge">{{ $course->status === 'published' ? 'منشور' : 'غير منشور' }}</div>
          <h2 class="course-title">{{ $course->name }}</h2>
          <p class="course-description">{{ $course->description ?: 'هذا المسار جديد ولم يتم إضافة وصف تفصيلي بعد، لكنه جاهز للاكتشاف والالتحاق.' }}</p>
        </div>

        <div class="course-meta">
          <div class="course-meta-item">
            <span>المعلم</span>
            <strong>{{ $course->instructor?->name ?? 'غير متوفر' }}</strong>
          </div>
          <div class="course-meta-item">
            <span>عدد الدروس</span>
            <strong>{{ $lessonCount }}</strong>
          </div>
          <div class="course-meta-item">
            <span>مدة المسار</span>
            <strong>{{ $course->duration ? $course->duration . ' دقيقة' : 'غير محددة' }}</strong>
          </div>
        </div>

        <div class="message-card">
          @if($approved)
            <strong>أنت بالفعل ملتحق بهذا المسار.</strong>
            <span>يمكنك فتح المسار والبدء في دروسك الآن.</span>
          @elseif($pending)
            <strong>طلب الالتحاق قيد المراجعة.</strong>
            <span>سيتم إشعارك عندما يوافق المعلم على طلبك.</span>
          @else
            <strong>لم تنضم إلى هذا المسار بعد.</strong>
            <span>اضغط زر الانضمام لتقديم طلب الالتحاق وسيتم إرساله للمعلم.</span>
          @endif
        </div>

        <div class="actions-row">
          @if($approved)
            <a href="{{ route('student.course.show', $course) }}" class="btn-primary">
              <i class="ri-arrow-right-line"></i>
              عرض المسار
            </a>
          @elseif($pending)
            <button class="btn-secondary" disabled>
              <i class="ri-time-line"></i>
              في انتظار الموافقة
            </button>
          @else
            <form action="{{ route('student.request-enrollment', $course) }}" method="POST" style="margin:0;">
              @csrf
              <button type="submit" class="btn-primary">
                <i class="ri-user-add-line"></i>
                طلب الانضمام الآن
              </button>
            </form>
            <a href="{{ route('student.academy') }}" class="btn-secondary">
              <i class="ri-book-open-line"></i>
              زيارة الأكاديمية
            </a>
          @endif
        </div>
      </div>
    </section>
  </div>
    @include('components.account-theme-foot')
</body>
</html>



