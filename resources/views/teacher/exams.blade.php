@extends('layouts.app-unified')

@section('title','الاختبارات - لوحة تحكم المعلم')

@section('styles')
<style>
  body {
    background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 22%),
                linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%);
    color: var(--text-primary);
  }

  .content {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
  }

  .page-header {
    margin-bottom: 24px;
    animation: fadeUp 0.5s ease;
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

  .btn {
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #fff;
    border: none;
    border-radius: var(--radius-md);
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn:hover { background: var(--gold-dark); transform: translateY(-2px); }

  .exams-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
  }

  .exam-card {
    background: var(--card-bg);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-lg);
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    animation: fadeUp 0.5s ease;
    cursor: pointer;
    backdrop-filter: blur(16px);
  }

  .exam-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: var(--gold);
    border-radius: 50%;
    opacity: 0;
    transition: var(--transition);
    pointer-events: none;
  }

  .exam-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(196,150,58,0.2);
    border-color: var(--gold);
  }

  .exam-card:hover::before { opacity: 0.08; }

  .exam-header {
    display: flex;
    align-items: start;
    justify-content: space-between;
    margin-bottom: 12px;
  }

  .exam-icon {
    width: 44px;
    height: 44px;
    background: var(--gold-light);
    color: var(--gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: var(--transition);
  }

  .exam-card:hover .exam-icon { transform: scale(1.1) rotate(15deg); }

  .exam-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
  }

  .exam-status.active { background: rgba(52,199,89,0.15); color: #34C759; }
  .exam-status.upcoming { background: rgba(198,166,117,0.15); color: #C6A675; }
  .exam-status.closed { background: rgba(153,153,153,0.15); color: #666; }
  .exam-status.expired { background: rgba(231,76,60,0.18); color: #e74c3c; }

  .exam-card.exam-expired { opacity: 0.75; border-color: rgba(231,76,60,0.2); }
  .exam-card.exam-expired:hover { border-color: rgba(231,76,60,0.4); box-shadow: 0 12px 32px rgba(231,76,60,0.12); }
  .exam-card.exam-expired:hover::before { opacity: 0; }
  .exam-card.exam-expired .action-btn:disabled { opacity: 0.4; cursor: not-allowed; background: transparent; color: var(--text-muted); border-color: rgba(255,255,255,0.04); }

  .exam-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 4px;
    margin-top: 8px;
  }

  .exam-course {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 12px;
  }

  .exam-stats {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  .stat-item { flex: 1; text-align: center; }

  .stat-item-val {
    font-size: 18px;
    font-weight: 900;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  }

  .stat-item-lbl {
    font-size: 10px;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
  }

  .exam-actions {
    display: flex;
    gap: 8px;
  }

  .action-btn {
    flex: 1;
    padding: 8px 12px;
    background: var(--gold-light);
    border: 1px solid var(--gold);
    color: var(--gold);
    border-radius: var(--radius-md);
    font-family: 'Tajawal', sans-serif;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none;
  }

  .action-btn:hover {
    background: var(--gold);
    color: #fff;
    transform: scale(1.05);
  }

  .empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
  }

  .empty-state h3 {
    margin-bottom: 10px;
    font-size: 22px;
  }

  .empty-state p {
    margin-bottom: 20px;
    font-size: 14px;
  }

  @media (max-width: 1024px) {
    .main { margin-right: 72px !important; }
  }

  @media (max-width: 768px) {
    .main { margin-right: 0 !important; }
    .content { padding: 16px; }
    .exams-container { grid-template-columns: 1fr; }
    .search-wrap { width: 100%; }
  }

  @media (max-width: 480px) {
    .content { padding: 10px; }
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
@endsection

@section('content')
  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
    <div class="page-header" style="margin-bottom: 0;">
      <h1 class="page-title">الاختبارات</h1>
      <p class="page-subtitle">إنشاء وإدارة الاختبارات والأسئلة</p>
    </div>
    <a href="{{ route('teacher.exam.new') }}" class="btn"><i class="ri-add-line"></i> اختبار جديد</a>
  </div>

  <div class="exams-container">
    @forelse($exams as $exam)
      <div class="exam-card {{ $exam->isExpired() ? 'exam-expired' : '' }}" data-href="{{ route('teacher.exam.results', $exam->id) }}" style="cursor: pointer;">
        <div class="exam-header">
          <div class="exam-icon"><i class="ri-file-text-line"></i></div>
          @if($exam->isExpired())
            <div class="exam-status expired">
              <i class="ri-timer-line"></i> منتهي
            </div>
          @else
            <div class="exam-status {{ $exam->is_published ? 'active' : 'closed' }}">
              <i class="ri-check-line"></i> {{ $exam->is_published ? 'منشور' : 'مخفي' }}
            </div>
          @endif
        </div>
        <h3 class="exam-title">{{ $exam->name }}</h3>
        <p class="exam-course">المساق: {{ $exam->lesson->course->name ?? 'غير محدد' }}</p>
        @if($exam->isExpired())
          <p style="color: #e74c3c; font-size: 0.8rem; margin-bottom: 0.5rem;">
            <i class="ri-calendar-close-line"></i> انتهى في: {{ $exam->expires_at->format('Y/m/d') }}
          </p>
        @endif
        <div class="exam-stats">
          <div class="stat-item">
            <div class="stat-item-val">{{ $exam->questions->count() }}</div>
            <div class="stat-item-lbl">أسئلة</div>
          </div>
          <div class="stat-item">
            <div class="stat-item-val">{{ $exam->passing_score ?? '0' }}</div>
            <div class="stat-item-lbl">الحد الأدنى</div>
          </div>
          <div class="stat-item">
            <div class="stat-item-val">{{ $exam->attempts_allowed ?? '3' }}</div>
            <div class="stat-item-lbl">المحاولات</div>
          </div>
        </div>
        <div class="exam-actions">
          <a href="{{ route('teacher.exam.questions', $exam->id) }}" class="action-btn exam-inner-btn"><i class="ri-questionnaire-line"></i> أسئلة</a>
          <a href="{{ route('teacher.exam.edit', $exam->id) }}" class="action-btn exam-inner-btn"><i class="ri-edit-line"></i> تعديل</a>
          <form action="{{ route('teacher.exam.toggle-publish', $exam->id) }}" method="POST" style="flex:1;" class="exam-inner-form">
            @csrf
            <button type="submit" class="action-btn" style="width:100%;" {{ $exam->isExpired() ? 'disabled' : '' }}>
              <i class="ri-broadcast-line"></i> {{ $exam->is_published ? 'إخفاء' : 'نشر' }}
            </button>
          </form>
          <button type="button" class="action-btn exam-delete-btn" data-exam-id="{{ $exam->id }}"><i class="ri-delete-bin-line"></i></button>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <i class="ri-inbox-archive-line" style="font-size: 64px; margin-bottom: 20px; display: block; opacity: 0.5;"></i>
        <h3>لا توجد اختبارات حتى الآن</h3>
        <p>ابدأ بإنشاء اختبار جديد لدروسك</p>
        <a href="{{ route('teacher.exam.new') }}" class="btn" style="display: inline-flex; text-decoration: none;">
          <i class="ri-add-line"></i> إنشاء اختبار جديد
        </a>
      </div>
    @endforelse
  </div>
@endsection

@section('scripts')
<script>
  function toggleDark() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';

    html.setAttribute('data-theme', newTheme);
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    const btn = document.getElementById('darkBtn');
    if (btn) {
      btn.innerHTML = newTheme === 'dark' ? '<i class="ri-sun-line"></i>' : '<i class="ri-moon-line"></i>';
    }
  }

  document.querySelectorAll('.exam-card[data-href]').forEach(function(card) {
    card.addEventListener('click', function(e) {
      if (e.target.closest('.exam-inner-btn') || e.target.closest('.exam-inner-form') || e.target.closest('.exam-delete-btn')) {
        return;
      }
      window.location.href = this.dataset.href;
    });
  });

  document.querySelectorAll('.exam-delete-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const examId = this.dataset.examId;
      if (confirm('هل أنت متأكد من رغبتك في حذف هذا الاختبار؟ هذا الإجراء لا يمكن التراجع عنه.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/teacher/exams/' + examId;
        form.innerHTML = '@csrf <input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
      }
    });
  });

  const theme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', theme);
  if (theme === 'dark') {
    const btn = document.getElementById('darkBtn');
    if (btn) {
      btn.innerHTML = '<i class="ri-sun-line"></i>';
    }
  }
</script>


@endsection
