@extends('layouts.app-unified')

@section('title','طلاب المعلم - لوحة تحكم المعلم')

@section('styles')
<style>
:root { --sidebar-w: 260px; --topbar-h: 70px; }
.sidebar { width: var(--sidebar-w); }
.main { margin-right: calc(var(--sidebar-w) + 22px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
body {
  background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 18%),
              radial-gradient(circle at bottom right, rgba(255,214,122,0.06), transparent 20%),
              linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 42%, var(--theme-page-bg) 100%);
  color: var(--text-primary);
}

.content {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.page-title {
  font-size: 32px;
  font-weight: 900;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.page-subtitle {
  font-size: 14px;
  color: var(--text-secondary);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 18px;
  margin-bottom: 24px;
}

.card {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,214,122,0.12);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
  transition: var(--transition);
  backdrop-filter: blur(18px);
  overflow: hidden;
  position: relative;
}

.stat-card {
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200px;
  height: 200px;
  background: var(--gold);
  border-radius: 50%;
  opacity: 0.03;
  pointer-events: none;
}

.stat-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-hover);
}

.stat-icon {
  width: 54px;
  height: 54px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  margin-bottom: 16px;
  position: relative;
  z-index: 1;
  transition: var(--transition);
}

.stat-icon.blue {
  background: rgba(198,166,117,0.1);
  color: #C6A675;
  box-shadow: 0 4px 12px rgba(198,166,117,0.15);
}

.stat-icon.green {
  background: rgba(52,199,89,0.1);
  color: #34C759;
  box-shadow: 0 4px 12px rgba(52,199,89,0.15);
}

.stat-icon.gold-i {
  background: var(--gold-light);
  color: var(--gold);
  box-shadow: 0 4px 12px rgba(196,150,58,0.2);
}

.stat-lbl {
  font-size: 13px;
  color: var(--text-secondary);
  font-weight: 600;
  margin-bottom: 8px;
  position: relative;
  z-index: 1;
}

.stat-val {
  font-size: 32px;
  font-weight: 900;
  line-height: 1;
  position: relative;
  z-index: 1;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  -webkit-background-clip: text;
  color: transparent;
}

.filter-bar {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.filter-select,
.btn {
  padding: 12px 16px;
  border: 1px solid rgba(255,214,122,0.12);
  border-radius: var(--radius-md);
  font-family: 'Tajawal', sans-serif;
  font-size: 14px;
  color: var(--text-primary);
  background: rgba(255,255,255,0.06);
  cursor: pointer;
  transition: var(--transition);
}

.filter-select {
  min-width: 180px;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  color-scheme: dark;
}

.filter-select option {
  background: var(--theme-surface);
  color: var(--text-primary);
}

.filter-bar {
  position: relative;
  z-index: 30;
}

.filter-select:hover,
.filter-select:focus,
.btn:hover {
  border-color: rgba(255,214,122,0.22);
  box-shadow: 0 0 0 3px rgba(255,214,122,0.12);
  outline: none;
}
.filter-select { outline: none; }

.btn-primary {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: #fff;
  border-color: transparent;
}

.students-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 18px;
}

.student-card {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,214,122,0.12);
  border-radius: var(--radius-lg);
  padding: 20px;
  cursor: pointer;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.student-card::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -35%;
  width: 220px;
  height: 220px;
  background: rgba(255,214,122,0.16);
  border-radius: 50%;
  opacity: 0;
  transition: var(--transition);
  pointer-events: none;
  filter: blur(16px);
}

.student-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 42px rgba(255,214,122,0.16);
  border-color: rgba(255,214,122,0.28);
}

.student-card:hover::before {
  opacity: 0.22;
}

.student-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.student-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 900;
  flex-shrink: 0;
  position: relative;
  overflow: hidden;
}

.student-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.student-avatar .avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1));
  color: white;
  font-weight: 900;
  font-size: 18px;
}

.student-info h3 {
  font-size: 14px;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 2px;
}

.student-status {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 600;
}

.student-stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 12px;
}

.student-stat-lbl {
  color: var(--text-secondary);
  font-weight: 600;
}

.student-stat-val {
  color: var(--text-primary);
  font-weight: 700;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: rgba(0,0,0,0.05);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 12px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--gold), var(--gold-dark));
  border-radius: 3px;
  transition: var(--transition);
}

.student-streak-section {
  background: linear-gradient(135deg, rgba(196,150,58,0.08), rgba(196,150,58,0.02));
  border: 1px solid rgba(196,150,58,0.15);
  border-radius: var(--radius-md);
  padding: 12px;
  margin-bottom: 12px;
}

.student-streak-row {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-top: 8px;
}

.streak-badge {
  flex: 1;
  background: rgba(255,255,255,0.06);
  border: 2px solid var(--gold);
  border-radius: var(--radius-md);
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: var(--transition);
}

.streak-badge:hover {
  box-shadow: 0 4px 12px rgba(196,150,58,0.25);
  transform: translateY(-2px);
}

.streak-icon {
  color: var(--gold);
  display: block;
  margin-bottom: 4px;
  transition: var(--transition);
  animation: floatFlame 3s ease-in-out infinite;
}

.streak-text {
  font-size: 10px;
  color: var(--text-secondary);
  font-weight: 600;
  display: block;
  margin-bottom: 2px;
}

.streak-count {
  font-size: 16px;
  font-weight: 900;
  color: var(--gold);
}

.streak-best {
  flex: 0 0 auto;
  background: rgba(196,150,58,0.1);
  border-radius: var(--radius-md);
  padding: 6px 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 2px;
}

.best-label {
  font-size: 9px;
  color: var(--text-secondary);
  font-weight: 600;
}

.best-count {
  font-size: 14px;
  font-weight: 800;
  color: var(--gold);
}

.student-actions {
  display: flex;
  gap: 6px;
}

.action-btn {
  flex: 1;
  padding: 10px 14px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,214,122,0.16);
  color: var(--gold);
  border-radius: var(--radius-md);
  font-family: 'Tajawal', sans-serif;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
}

.action-btn:hover {
  background: var(--gold);
  color: #fff;
  transform: scale(1.03);
}

.no-results {
  grid-column: 1 / -1;
  padding: 48px;
  text-align: center;
  color: var(--text-secondary);
}

@keyframes floatFlame {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
}

::-webkit-scrollbar {
  display: none;
}

.sidebar,
.main,
.content {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

@media (max-width: 1024px) {
  .main { margin-right: 72px !important; }
}

@media (max-width: 768px) {
  .main { margin-right: 0 !important; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .table-container { overflow-x: auto; }
}

@media (max-width: 480px) {
  .stats-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">الطلاب</h1>
    <p class="page-subtitle">إدارة وتتبع أداء طلابك</p>
  </div>
</div>

<div class="stats-grid">
  <div class="card stat-card">
    <div class="stat-icon blue">
      <i class="ri-group-fill"></i>
    </div>
    <div class="stat-lbl">إجمالي الطلاب</div>
    <div class="stat-val">{{ count($students) }}</div>
  </div>
  <div class="card stat-card">
    <div class="stat-icon green">
      <i class="ri-checkbox-circle-line"></i>
    </div>
    <div class="stat-lbl">متوسط الإنجاز</div>
    <div class="stat-val">{{ $students->isNotEmpty() ? round($students->avg('completion_percentage'), 0) : 0 }}%</div>
  </div>
  <div class="card stat-card">
    <div class="stat-icon gold-i">
      <i class="ri-fire-line"></i>
    </div>
    <div class="stat-lbl">بسلسلة متواصلة</div>
    <div class="stat-val">{{ $students->where('current_streak', '>', 0)->count() }}</div>
  </div>
  <div class="card stat-card">
    <div class="stat-icon" style="background: rgba(175,82,222,0.1); color: #AF52DE;">
      <i class="ri-award-line"></i>
    </div>
    <div class="stat-lbl">متوسط النقاط</div>
    <div class="stat-val">{{ $students->isNotEmpty() ? round($students->avg('student_points'), 0) : 0 }}</div>
  </div>
</div>

<div class="filter-bar">
  <select class="filter-select" id="courseFilter">
    <option value="">جميع المساقات</option>
    @foreach(($courses ?? collect()) as $course)
      <option value="{{ $course->id }}">{{ $course->name }}</option>
    @endforeach
  </select>
  <select class="filter-select" id="statusFilter">
    <option value="">جميع الحالات</option>
    <option value="active">نشط</option>
    <option value="inactive">غير نشط</option>
    <option value="pending">قيد الانتظار</option>
  </select>
  <button class="btn btn-primary">
    <i class="ri-download-2-line"></i> تصدير البيانات
  </button>
</div>

<div class="students-container" id="studentsGrid">
  @forelse($students as $student)
    <div class="card student-card" data-id="{{ $student->id }}" data-course="{{ implode(',', $student->course_ids ?? []) }}" data-status="{{ $student->is_online ? 'active' : 'inactive' }}" data-last-activity="{{ $student->last_activity_timestamp ?? '' }}">
      <div class="student-header">
        <div class="student-avatar">
          @if($student->avatar_url)
            <img src="{{ asset('storage/' . $student->avatar_url) }}" loading="lazy" alt="{{ $student->name }}">
          @else
            <div class="avatar-placeholder">{{ mb_substr($student->name, 0, 1) }}</div>
          @endif
        </div>
        <div class="student-info">
          <h3>{{ $student->name }}</h3>
          <div class="student-status">
            @if($student->is_online)
              🟢 نشط الآن
            @else
              🔴 غير نشط
            @endif
          </div>
        </div>
      </div>
      <div class="student-stat">
        <span class="student-stat-lbl">البريد الإلكتروني:</span>
        <span class="student-stat-val">{{ $student->email }}</span>
      </div>
      <div class="student-stat">
        <span class="student-stat-lbl">آخر نشاط:</span>
        <span class="student-stat-val last-seen-text" data-last-activity="{{ $student->last_activity_timestamp ?? '' }}">{{ $student->last_activity_readable ?? 'لم يسجل' }}</span>
      </div>
      <div class="student-stat">
        <span class="student-stat-lbl">مستوى الإنجاز:</span>
        <span class="student-stat-val">{{ $student->completion_percentage }}%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width: {{ $student->completion_percentage }}%;"></div>
      </div>
      <div class="student-streak-section">
        <div class="student-stat">
          <span class="student-stat-lbl">⚡ النقاط:</span>
          <span class="student-stat-val" style="color: var(--gold);">{{ $student->student_points }}</span>
        </div>
        <div class="student-streak-row">
          <div class="streak-badge {{ $student->current_streak > 0 ? 'active' : 'inactive' }}">
            <i class="ri-fire-fill streak-icon" style="font-size: 20px;"></i>
            <span class="streak-text">أيام متواصلة</span>
            <span class="streak-count">{{ $student->current_streak }}</span>
          </div>
          <div class="streak-best">
            <span class="best-label">أفضل سجل:</span>
            <span class="best-count">{{ $student->longest_streak }}</span>
          </div>
        </div>
      </div>
      <div class="student-actions">
        <button class="action-btn student-view-btn" data-student-id="{{ $student->id }}">عرض الملف</button>
        <button class="action-btn student-message-btn" data-student-id="{{ $student->id }}">رسالة</button>
      </div>
    </div>
  @empty
    <div class="no-results">
      <i class="ri-user-line" style="font-size: 40px; margin-bottom: 12px;"></i>
      <h4>لا توجد طلاب</h4>
      <p>لم يتم العثور على طلاب في مسارات المعلم</p>
    </div>
  @endforelse
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    updateStreakFlames();
    updateStudentActivityLabels();
    setInterval(updateStudentActivityLabels, 60 * 1000);

    document.getElementById('courseFilter').addEventListener('change', filterStudents);
    document.getElementById('statusFilter').addEventListener('change', filterStudents);

    document.querySelectorAll('.student-view-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        window.location.href = '/teacher/students/' + this.dataset.studentId;
      });
    });

    document.querySelectorAll('.student-message-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        window.location.href = '/teacher/messaging?contact=' + this.dataset.studentId;
      });
    });
  });

  function formatActivityTime(timestamp) {
    if (!timestamp) {
      return 'لم يسجل';
    }

    const activityTime = new Date(Number(timestamp) * 1000);
    const now = new Date();
    const diffSeconds = Math.floor((now - activityTime) / 1000);

    if (diffSeconds < 60) {
      return 'نشط الآن';
    }

    if (diffSeconds < 3600) {
      const minutes = Math.floor(diffSeconds / 60);
      return `آخر ظهور قبل ${minutes} دقيقة`;
    }

    if (diffSeconds <= 10800) {
      const hours = Math.floor(diffSeconds / 3600);
      return `آخر ظهور قبل ${hours} ساعة`;
    }

    const isToday = activityTime.toDateString() === now.toDateString();
    const yesterday = new Date(now);
    yesterday.setDate(now.getDate() - 1);
    const isYesterday = activityTime.toDateString() === yesterday.toDateString();

    const pad = (value) => String(value).padStart(2, '0');
    const timeString = `${pad(activityTime.getHours())}:${pad(activityTime.getMinutes())}`;

    if (isToday) {
      return `آخر ظهور اليوم في ${timeString}`;
    }

    if (isYesterday) {
      return `آخر ظهور أمس في ${timeString}`;
    }

    const daysAgo = Math.floor(diffSeconds / 86400);
    if (daysAgo < 7) {
      return `آخر ظهور منذ ${daysAgo} يوم`;
    }

    const dateString = `${pad(activityTime.getDate())}/${pad(activityTime.getMonth() + 1)}/${activityTime.getFullYear()}`;
    return `آخر ظهور في ${dateString} ${timeString}`;
  }

  function updateStreakFlames() {
    const streakBadges = document.querySelectorAll('.streak-badge');
    streakBadges.forEach(badge => {
      const streakCount = parseInt(badge.querySelector('.streak-count').textContent) || 0;
      if (streakCount > 0) {
        badge.classList.add('active');
        badge.classList.remove('inactive');
      } else {
        badge.classList.add('inactive');
        badge.classList.remove('active');
      }
    });
  }

  function updateStudentActivityLabels() {
    document.querySelectorAll('.student-card').forEach(card => {
      const lastActivity = card.dataset.lastActivity;
      const statusEl = card.querySelector('.student-status');
      const lastSeenEl = card.querySelector('.last-seen-text');
      const label = formatActivityTime(lastActivity);

      if (lastSeenEl) {
        lastSeenEl.textContent = label;
      }

      if (statusEl) {
        const diffSeconds = lastActivity ? Math.floor((new Date() - new Date(Number(lastActivity) * 1000)) / 1000) : Infinity;
        const isOnline = diffSeconds <= 300;
        card.dataset.status = isOnline ? 'active' : 'inactive';
        statusEl.textContent = isOnline ? '🟢 نشط الآن' : '🔴 غير نشط';
      }
    });
  }

  function filterStudents() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const courseFilter = document.getElementById('courseFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('.student-card');

    cards.forEach(card => {
      const name = card.querySelector('.student-info h3').textContent.toLowerCase();
      const courseRaw = card.getAttribute('data-course') || '';
      const studentCourses = courseRaw.split(',').map(v => v.trim()).filter(Boolean);
      const status = card.getAttribute('data-status');

      const matchSearch = name.includes(searchInput);
      const matchCourse = !courseFilter || studentCourses.includes(courseFilter);
      const matchStatus = !statusFilter || status === statusFilter;

      card.style.display = (matchSearch && matchCourse && matchStatus) ? '' : 'none';
    });
  }
</script>


@endsection
