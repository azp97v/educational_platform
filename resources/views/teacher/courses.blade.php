@extends('layouts.app-unified')

@section('title','المسارات التعليمية - لوحة تحكم المعلم')

@section('styles')
<style>
  :root { --sidebar-w: 260px; --topbar-h: 70px; }
  .sidebar { width: var(--sidebar-w); }
  .main { margin-right: calc(var(--sidebar-w) + 22px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
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

  .section-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }

  .section-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-primary);
  }

  .btn-primary {
    padding: 10px 20px;
    background: var(--gold);
    color: #fff;
    border: none;
    border-radius: var(--radius-md);
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(196,150,58,0.3);
    text-decoration: none;
  }

  .btn-primary:hover {
    background: var(--gold-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(196,150,58,0.4);
  }

  .grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }

  .item-card {
    padding: 24px;
    border: 1px solid rgba(255,255,255,0.08);
    position: relative;
    cursor: pointer;
    transition: var(--transition);
    overflow: hidden;
    background: rgba(255,255,255,0.06);
    border-radius: var(--radius-xl);
    backdrop-filter: blur(16px);
  }

  .item-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(196,150,58,0.1) 0%, transparent 70%);
    border-radius: 50%;
    opacity: 0;
    transition: var(--transition);
  }

  .item-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
    border-color: rgba(255,214,122,0.16);
  }

  .item-card:hover::before {
    opacity: 1;
  }

  .item-icon-box {
    width: 48px;
    height: 48px;
    border: 2px solid rgba(255,214,122,0.18);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: var(--gold);
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
    transition: var(--transition);
    box-shadow: 0 0 12px rgba(196,150,58,0.12);
  }

  .item-card:hover .item-icon-box {
    border-color: var(--gold);
    transform: rotate(10deg) scale(1.1);
    box-shadow: 0 0 20px rgba(196,150,58,0.25);
  }

  .item-title {
    font-size: 16px;
    font-weight: 800;
    margin-bottom: 6px;
    position: relative;
    z-index: 1;
  }

  .item-sub {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
  }

  .item-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px dashed rgba(255,255,255,0.12);
    font-size: 13px;
    font-weight: 700;
    position: relative;
    z-index: 1;
  }

  .item-stat { color: var(--gold); }
  .item-stat-alt { color: var(--text-secondary); }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
  }

  .empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  .empty-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .empty-text {
    color: var(--text-secondary);
    margin-bottom: 20px;
  }

  @media (max-width: 1024px) {
    .main { margin-right: 72px !important; }
    .grid-container { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 768px) {
    .main { margin-right: 0 !important; }
    .grid-container { grid-template-columns: 1fr; }
    .search-wrap { width: 100%; }
    .section-hdr { padding: 0; flex-direction: column; align-items: flex-start; }
  }

  @media (max-width: 480px) {
    .grid-container { grid-template-columns: 1fr; }
  }

  .delete-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }

  .delete-modal-overlay.active {
    display: flex;
    animation: fadeIn 0.3s ease;
  }

  .delete-modal-content {
    background: var(--card-bg);
    border-radius: 24px;
    padding: 48px;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
    border: 1px solid rgba(255,214,122,0.12);
    text-align: center;
  }

  .delete-modal-icon {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, rgba(255, 59, 48, 0.15), rgba(255, 59, 48, 0.05));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: #FF3B30;
    font-size: 44px;
  }

  .delete-modal-content h3 {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
  }

  .delete-modal-content p {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 32px;
  }

  .delete-modal-actions {
    display: flex;
    gap: 12px;
    flex-direction: row-reverse;
  }

  .delete-modal-btn {
    flex: 1;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    font-family: 'Tajawal', sans-serif;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .delete-modal-confirm {
    background: linear-gradient(135deg, #FF3B30, #FF5A52);
    color: #fff;
    box-shadow: 0 4px 12px rgba(255, 59, 48, 0.3);
  }

  .delete-modal-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 59, 48, 0.4);
  }

  .delete-modal-cancel {
    background: var(--gold-light);
    color: var(--gold);
    border: 2px solid var(--gold);
  }

  .delete-modal-cancel:hover {
    background: var(--gold);
    color: #fff;
    transform: translateY(-2px);
  }

  @keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .course-edit-btn:hover { background: var(--gold) !important; color: white !important; }
  .course-delete-btn:hover { background: #FF3B30 !important; color: white !important; }
</style>
@endsection

@section('content')
  <div class="section-hdr">
    <div class="section-title">المسارات التعليمية</div>
    <a href="{{ route('teacher.create') }}" class="btn-primary" style="text-decoration: none;">
      <i class="ri-add-line"></i> مسار جديد
    </a>
  </div>

  @if(count($myCourses) > 0)
    <div class="grid-container">
      @foreach($myCourses as $course)
        <div class="card item-card" style="position: relative;">
          <div style="position: absolute; top: 12px; left: 12px; display: flex; gap: 6px; z-index: 10;">
<a href="{{ route('teacher.edit', $course->id) }}" class="course-edit-btn" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: var(--gold-light); color: var(--gold); border-radius: 8px; font-size: 16px; transition: var(--transition); text-decoration: none;"><i class="ri-edit-line"></i></a>
<button data-course-id="{{ $course->id }}" data-course-name="{{ addslashes($course->name) }}" class="course-delete-btn" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: rgba(255,59,48,0.12); color: #FF3B30; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; transition: var(--transition);"><i class="ri-delete-bin-line"></i></button>
          </div>
          <a href="{{ route('teacher.show', $course->id) }}" style="text-decoration: none; color: inherit; display: block;">
            <div class="item-icon-box"><i class="ri-book-open-line"></i></div>
            <div class="item-title">{{ $course->name }}</div>
            <div class="item-sub">{{ Str::limit($course->description ?? '', 50) }}</div>
            <div class="item-foot">
              <span class="item-stat-alt">{{ count($course->lessons ?? []) }} درس</span>
              <span class="item-stat">{{ count($course->students ?? []) }} طالب</span>
            </div>
          </a>
        </div>
        <form id="delete-form-{{ $course->id }}" action="{{ route('teacher.delete', $course->id) }}" method="POST" style="display: none;">
          @csrf
          @method('DELETE')
        </form>
      @endforeach
    </div>
  @else
    <div class="card empty-state">
      <div class="empty-icon">📚</div>
      <div class="empty-title">لا توجد مسارات</div>
      <div class="empty-text">ابدأ الآن وأنشئ مسارك التعليمي الأول</div>
      <a href="{{ route('teacher.create') }}" class="btn-primary">
        <i class="ri-add-line"></i> إنشاء مسار جديد
      </a>
    </div>
  @endif

  <div id="deleteModal" class="delete-modal-overlay">
    <div class="delete-modal-content">
      <div class="delete-modal-icon">
        <i class="ri-delete-bin-6-line"></i>
      </div>
      <h3>حذف المسار</h3>
      <p>هل أنت متأكد من حذف المسار <strong id="modalCourseName"></strong>؟ هذا الإجراء لا يمكن التراجع عنه.</p>
      <div class="delete-modal-actions">
<button class="delete-modal-btn delete-modal-confirm" id="confirmDeleteBtn">
  <i class="ri-delete-bin-line"></i> حذف نهائي
</button>
<button class="delete-modal-btn delete-modal-cancel" id="cancelDeleteBtn">
  <i class="ri-close-line"></i> إلغاء
</button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  let currentDeleteCourseId = null;

  function openDeleteModal(courseId, courseName) {
    currentDeleteCourseId = courseId;
    document.getElementById('modalCourseName').textContent = courseName;
    document.getElementById('deleteModal').classList.add('active');
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    currentDeleteCourseId = null;
  }

  function confirmDeleteModal() {
    if (currentDeleteCourseId) {
      document.getElementById('delete-form-' + currentDeleteCourseId).submit();
    }
  }

  document.querySelectorAll('.course-delete-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      openDeleteModal(this.dataset.courseId, this.dataset.courseName);
    });
  });

  document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDeleteModal);
  document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);

  document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
  });

  let dark = false;
  function toggleDark() {
    dark = !dark;
    const theme = dark ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    document.body.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    const icon = document.getElementById('darkIcon');
    if (icon) icon.className = dark ? 'ri-sun-line' : 'ri-moon-line';
  }

  (function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    dark = savedTheme === 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    const icon = document.getElementById('darkIcon');
    if (icon) icon.className = dark ? 'ri-sun-line' : 'ri-moon-line';
  })();
</script>


@endsection
