@extends('layouts.app-unified')

@section('title', 'الملف الشخصي للطالب - لوحة المعلم')

@section('styles')
<style>
  .student-profile-page {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    gap: 1.2rem;
  }

  html, body {
    background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 18%),
                radial-gradient(circle at bottom right, rgba(255,214,122,0.06), transparent 20%),
                linear-gradient(180deg, #070A14 0%, #0B101A 42%, #070B13 100%) !important;
  }

  .content {
    background: transparent !important;
  }

  .profile-hero {
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    padding: 1.5rem;
    background:
      radial-gradient(circle at 90% 10%, rgba(196,150,58,0.24), transparent 32%),
      linear-gradient(135deg, #0f1f47 0%, #151a2f 52%, #111522 100%);
    border: 1px solid rgba(255,255,255,0.12);
    box-shadow: 0 20px 54px rgba(0,0,0,0.28);
  }

  .profile-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      repeating-linear-gradient(30deg, rgba(255,255,255,0.08) 0 1px, transparent 1px 34px),
      repeating-linear-gradient(-30deg, rgba(255,255,255,0.08) 0 1px, transparent 1px 34px),
      repeating-linear-gradient(90deg, rgba(255,255,255,0.04) 0 1px, transparent 1px 34px);
    opacity: 0.35;
    pointer-events: none;
  }

  .profile-hero::after {
    content: '';
    position: absolute;
    width: 240px;
    height: 240px;
    left: -70px;
    top: -70px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(196,150,58,0.14), transparent 65%);
    filter: blur(16px);
    pointer-events: none;
  }

  .hero-row {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
    align-items: center;
  }

  .hero-name {
    color: #fff;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.45rem;
  }

  .hero-meta {
    color: rgba(255,255,255,0.8);
    font-size: 1rem;
    display: flex;
    gap: 0.8rem;
    flex-wrap: wrap;
  }

  .hero-badges {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.65rem;
    flex-wrap: wrap;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.22rem 0.68rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.18);
    font-size: 0.84rem;
    font-weight: 800;
    color: #fff;
  }

  .hero-badge.active {
    background: rgba(52,199,89,0.2);
    border-color: rgba(52,199,89,0.55);
    color: #85f5a2;
  }

  .hero-badge.offline {
    background: rgba(142,152,182,0.2);
    border-color: rgba(142,152,182,0.55);
    color: #cdd5ea;
  }

  .hero-badge.role {
    background: rgba(198,166,117,0.2);
    border-color: rgba(198,166,117,0.5);
    color: #C6A675;
  }

  .hero-presence {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: rgba(255,255,255,0.82);
    font-size: 0.92rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .hero-presence .dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #3fd283;
  }

  .hero-presence .dot.offline {
    background: #8e98b6;
  }

  .hero-actions {
    display: flex;
    gap: 0.65rem;
    margin-top: 1rem;
    flex-wrap: wrap;
  }

  .hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    background: rgba(255,255,255,0.08);
  }

  .hero-btn.primary {
    color: #2d2307;
    background: linear-gradient(135deg, #f2ce7b, #c4963a);
    border-color: rgba(242,206,123,0.7);
  }

  .hero-avatar {
    width: 110px;
    height: 110px;
    border-radius: 18px;
    background: linear-gradient(135deg, #d2a633, #b18523);
    border: 4px solid #0f1220;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 2.6rem;
    font-weight: 900;
    overflow: hidden;
    box-shadow: 0 14px 26px rgba(0,0,0,0.28);
  }

  .hero-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.9rem;
  }

  .stat-card {
    background: var(--card-bg);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    padding: 1rem;
    box-shadow: var(--shadow);
  }

  .stat-label {
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 700;
    margin-bottom: 0.45rem;
  }

  .stat-value {
    color: var(--text-primary);
    font-size: 1.55rem;
    font-weight: 900;
  }

  .card-panel {
    background: var(--card-bg);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.1rem;
    box-shadow: var(--shadow);
  }

  .panel-title {
    margin: 0 0 0.95rem;
    color: var(--text-primary);
    font-size: 1.05rem;
    font-weight: 800;
    display: flex;
    gap: 0.45rem;
    align-items: center;
  }

  .panel-title i {
    color: var(--gold);
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.75rem;
  }

  .info-item {
    padding: 0.78rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
  }

  .info-k {
    color: var(--text-muted);
    font-size: 0.74rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
  }

  .info-v {
    color: var(--text-primary);
    font-size: 0.9rem;
    font-weight: 700;
    word-break: break-word;
  }

  .tabs {
    display: flex;
    gap: 0.55rem;
    flex-wrap: wrap;
  }

  .tab-btn {
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    color: var(--text-secondary);
    border-radius: 10px;
    padding: 0.5rem 0.85rem;
    cursor: pointer;
    font-family: inherit;
    font-weight: 700;
  }

  .tab-btn.active {
    color: var(--gold);
    border-color: rgba(196,150,58,0.45);
    background: rgba(196,150,58,0.12);
  }

  .tab-pane { display: none; }
  .tab-pane.active { display: block; }

  .course-item,
  .activity-item {
    padding: 0.9rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 0.7rem;
  }

  .course-top,
  .activity-top {
    display: flex;
    justify-content: space-between;
    gap: 0.8rem;
    align-items: center;
    margin-bottom: 0.45rem;
    flex-wrap: wrap;
  }

  .course-title,
  .activity-title {
    color: var(--text-primary);
    font-weight: 800;
    font-size: 0.94rem;
  }

  .course-sub,
  .activity-time {
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 600;
  }

  .progress-track {
    width: 100%;
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
    background: rgba(255,255,255,0.08);
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #c4963a, #f2ce7b);
  }

  .empty-box {
    text-align: center;
    color: var(--text-secondary);
    padding: 1.4rem 0.6rem;
    font-weight: 700;
  }

  @media (max-width: 1024px) {
    .main { margin-right: 72px !important; }
  }
  @media (max-width: 900px) {
    .hero-row {
      grid-template-columns: 1fr;
    }
    .hero-avatar {
      width: 88px;
      height: 88px;
      font-size: 2rem;
    }
  }
  @media (max-width: 768px) {
    .main { margin-right: 0 !important; }
    .student-profile-page { grid-template-columns: 1fr; }
  }
  @media (max-width: 480px) {
    .profile-hero { padding: 1rem; }
    .hero-name { font-size: 1.1rem; }
  }
</style>
@endsection

@section('content')
@php
  $avatarSrc = null;
  if (!empty($student->avatar_url)) {
      if (\Illuminate\Support\Str::startsWith($student->avatar_url, ['http://', 'https://'])) {
          $avatarSrc = $student->avatar_url;
      } elseif (\Illuminate\Support\Str::startsWith($student->avatar_url, '/storage/')) {
          $avatarSrc = asset(ltrim($student->avatar_url, '/'));
      } else {
          $avatarSrc = asset('storage/' . ltrim($student->avatar_url, '/'));
      }
  }
@endphp

<div class="student-profile-page">
  <section class="profile-hero">
    <div class="hero-row">
      <div>
        <div class="hero-badges">
          <span class="hero-badge {{ $isOnline ? 'active' : 'offline' }}">{{ $isOnline ? 'نشط الآن' : 'غير متصل' }}</span>
          <span class="hero-badge role">طالب</span>
        </div>
        <div class="hero-presence">
          @if($isOnline)
            <span class="dot"></span>
            <span>متصل</span>
          @else
            <span class="dot offline"></span>
            <span>آخر ظهور {{ $lastSeenText }}</span>
          @endif
        </div>

        <h1 class="hero-name">{{ $student->name }}</h1>
        <div class="hero-meta">
          <span>{{ $student->email }}</span>
          <span>{{ $student->phone ?: 'رقم الهاتف غير مضاف' }}</span>
        </div>

        <div class="hero-actions">
          <a href="{{ route('teacher.students') }}" class="hero-btn primary">
            <i class="ri-arrow-right-line"></i> عودة للطلاب
          </a>
          <a href="{{ route('teacher.messaging') }}?student={{ $student->id }}" class="hero-btn">
            <i class="ri-message-2-line"></i> مراسلة
          </a>
        </div>
      </div>

      <div class="hero-avatar" aria-label="صورة الطالب">
        @if($avatarSrc)
          <img src="{{ $avatarSrc }}" alt="{{ $student->name }}">
        @else
          {{ mb_substr($student->name, 0, 1) }}
        @endif
      </div>
    </div>
  </section>

  <section class="stats-grid">
    <article class="stat-card">
      <div class="stat-label">الدورات المسجل بها</div>
      <div class="stat-value">{{ $enrolledCourses->count() }}</div>
    </article>
    <article class="stat-card">
      <div class="stat-label">الدروس المكتملة</div>
      <div class="stat-value">{{ $completedLessons }} / {{ $totalLessons }}</div>
    </article>
    <article class="stat-card">
      <div class="stat-label">نسبة الإكمال</div>
      <div class="stat-value">{{ $completionPercentage }}%</div>
    </article>
    <article class="stat-card">
      <div class="stat-label">متوسط التقدم</div>
      <div class="stat-value">{{ $averageScore }}%</div>
    </article>
    <article class="stat-card">
      <div class="stat-label">تقييمات مرتبطة بالدروس</div>
      <div class="stat-value">{{ $examAttempts->count() }}</div>
    </article>
    <article class="stat-card">
      <div class="stat-label">سلسلة النشاط</div>
      <div class="stat-value">{{ $streak }} يوم</div>
    </article>
  </section>

  <section class="card-panel">
    <div class="tabs">
      <button class="tab-btn active" data-tab="overview">نظرة عامة</button>
      <button class="tab-btn" data-tab="courses">الدورات</button>
      <button class="tab-btn" data-tab="activity">سجل النشاط</button>
    </div>
  </section>

  <section id="overview" class="card-panel tab-pane active">
    <h2 class="panel-title"><i class="ri-information-line"></i> معلومات الطالب</h2>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-k">الاسم الكامل</div>
        <div class="info-v">{{ $student->name }}</div>
      </div>
      <div class="info-item">
        <div class="info-k">البريد الإلكتروني</div>
        <div class="info-v">{{ $student->email }}</div>
      </div>
      <div class="info-item">
        <div class="info-k">رقم الهاتف</div>
        <div class="info-v">{{ $student->phone ?: 'غير مضاف' }}</div>
      </div>
      <div class="info-item">
        <div class="info-k">تاريخ التسجيل</div>
        <div class="info-v">{{ $registrationDate ? $registrationDate->translatedFormat('d F Y') : 'غير متوفر' }}</div>
      </div>
      <div class="info-item">
        <div class="info-k">آخر نشاط</div>
        <div class="info-v">{{ $lastActivity ? $lastActivity->diffForHumans() : 'لا يوجد نشاط بعد' }}</div>
      </div>
      <div class="info-item">
        <div class="info-k">الحالة</div>
        <div class="info-v">{{ $isOnline ? 'نشط الآن' : 'غير متصل' }}</div>
      </div>
    </div>
  </section>

  <section id="courses" class="card-panel tab-pane">
    <h2 class="panel-title"><i class="ri-book-open-line"></i> تقدم الطالب في الدورات</h2>
    @forelse($enrolledCourses as $course)
      @php
        $lessonsCount = $course->lessons->count();
        $completedInCourse = $course->lessons->filter(function ($lesson) {
          return optional($lesson->userProgress->first())->status === 'completed';
        })->count();
        $courseProgress = $lessonsCount > 0 ? round(($completedInCourse / $lessonsCount) * 100) : 0;
      @endphp
      <article class="course-item">
        <div class="course-top">
          <div class="course-title">{{ $course->name }}</div>
          <div class="course-sub">{{ $completedInCourse }} / {{ $lessonsCount }} درس</div>
        </div>
        <div class="progress-track">
          <div class="progress-fill" style="width: {{ $courseProgress }}%"></div>
        </div>
        <div class="course-sub" style="margin-top: 0.45rem;">نسبة الإكمال: {{ $courseProgress }}%</div>
      </article>
    @empty
      <div class="empty-box">لا توجد دورات مرتبطة بهذا الطالب ضمن دوراتك حاليًا.</div>
    @endforelse
  </section>

  <section id="activity" class="card-panel tab-pane">
    <h2 class="panel-title"><i class="ri-history-line"></i> آخر أنشطة الطالب</h2>
    @forelse($studentProgress->take(12) as $progress)
      <article class="activity-item">
        <div class="activity-top">
          <div class="activity-title">
            {{ $progress->lesson->name ?? 'درس' }}
            @if($progress->status === 'completed')
              - مكتمل
            @elseif($progress->status === 'in_progress')
              - قيد التنفيذ
            @else
              - {{ $progress->status }}
            @endif
          </div>
          <div class="activity-time">{{ $progress->updated_at?->diffForHumans() }}</div>
        </div>
        <div class="course-sub">
          {{ $progress->lesson->course->name ?? 'بدون دورة' }} | التقدم: {{ (int) $progress->progress_percentage }}%
        </div>
      </article>
    @empty
      <div class="empty-box">لا توجد أنشطة مسجلة لهذا الطالب بعد.</div>
    @endforelse
  </section>
</div>
@endsection

@section('scripts')
<script>
  function switchTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.tabs')?.addEventListener('click', function(e) {
      const btn = e.target.closest('.tab-btn');
      if (btn) {
        const tabId = btn.dataset.tab;
        if (tabId) switchTab(tabId);
      }
    });
  });
</script>
@endsection
