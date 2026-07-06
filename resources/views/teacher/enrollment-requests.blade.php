@extends('layouts.app-unified')

@section('title','طلبات الالتحاق - لوحة تحكم المعلم')

@section('styles')
<style>
  :root {
    --gold: #C4963A;
    --gold-dark: #A07A28;
    --gold-light: rgba(196,150,58,0.14);
    --sidebar-w: 260px;
    --topbar-h: 70px;
    --bg: #F4F5F7;
    --card-bg: #FFFFFF;
    --text-primary: #1C1C1E;
    --text-secondary: #6C6C70;
    --text-muted: #AEAEB2;
    --danger: #FF3B30;
    --success: #34C759;
    --border: #E5E5EA;
    --radius-lg: 16px;
    --radius-md: 12px;
    --shadow: 0 4px 24px rgba(0,0,0,0.04);
    --transition: all 0.3s ease;
  }

  [data-theme="dark"] {
    --bg: #121212;
    --card-bg: #1E1E1E;
    --text-primary: #F2F2F7;
    --text-secondary: #AEAEB2;
    --text-muted: #636366;
    --border: #2A2F3A;
  }

  .sidebar { width: var(--sidebar-w); }
  .main { margin-right: calc(var(--sidebar-w) + 22px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
  body { background: var(--bg); color: var(--text-primary); }

  .content { padding: 0 32px 32px; }

  .page-header { margin-bottom: 28px; }
  .page-header h1 {
    font-size: 32px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 12px;
  }

  .tabs {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    border-bottom: 2px solid var(--border);
    flex-wrap: wrap;
  }

  .tab {
    padding: 12px 16px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
    font-family: 'Tajawal', sans-serif;
    border-bottom: 3px solid transparent;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .tab.active {
    color: var(--gold);
    border-bottom-color: var(--gold);
  }

  .content-card {
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    padding: 28px;
    box-shadow: var(--shadow);
    border: 1px solid rgba(196,150,58,0.08);
    margin-bottom: 24px;
  }

  .content-card h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .enrollment-table {
    width: 100%;
    border-collapse: collapse;
  }

  .enrollment-table thead {
    background: var(--gold-light);
    border-bottom: 2px solid rgba(196,150,58,0.2);
  }

  .enrollment-table th,
  .enrollment-table td {
    padding: 16px;
    text-align: right;
    font-size: 13px;
  }

  .enrollment-table th {
    font-weight: 700;
    color: var(--gold);
  }

  .enrollment-table td {
    border-bottom: 1px solid var(--border);
    color: var(--text-primary);
  }

  .enrollment-table tbody tr:hover {
    background: rgba(196,150,58,0.02);
  }

  .student-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 16px;
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
    font-size: 16px;
  }

  .student-info h4 {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
  }

  .student-info p {
    font-size: 12px;
    color: var(--text-secondary);
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
  }

  .badge-pending { background: rgba(255,193,7,0.12); color: #F59E0B; }
  .badge-approved { background: rgba(52,199,89,0.12); color: var(--success); }
  .badge-rejected { background: rgba(255,59,48,0.12); color: var(--danger); }

  .actions {
    display: flex;
    gap: 8px;
  }

  .action-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    font-size: 16px;
    text-decoration: none;
  }

  .btn-approve { background: rgba(52,199,89,0.12); color: var(--success); }
  .btn-approve:hover { background: var(--success); color: #fff; transform: translateY(-2px); }

  .btn-reject { background: rgba(255,59,48,0.12); color: var(--danger); }
  .btn-reject:hover { background: var(--danger); color: #fff; transform: translateY(-2px); }

  .btn-remove { background: rgba(255,59,48,0.12); color: var(--danger); }
  .btn-remove:hover { background: var(--danger); color: #fff; }

  .empty-state {
    text-align: center;
    padding: 60px 24px;
    color: var(--text-muted);
  }

  .empty-state i {
    font-size: 64px;
    color: var(--gold);
    margin-bottom: 16px;
    opacity: 0.3;
  }

  .empty-state h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text-primary);
  }

  .empty-state p {
    font-size: 14px;
  }

  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
  }

  .modal-overlay.active { display: flex; }

  .rejection-modal {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 40px;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    border: 1px solid rgba(196,150,58,0.1);
  }

  .modal-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
  }

  .modal-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    color: var(--text-primary);
    background: var(--bg);
    resize: vertical;
    min-height: 100px;
    margin-bottom: 20px;
  }

  .modal-actions {
    display: flex;
    gap: 12px;
    flex-direction: row-reverse;
  }

  .btn-submit {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Tajawal', sans-serif;
  }

  .btn-confirm { background: linear-gradient(135deg, var(--danger), #FF5A52); color: #fff; }
  .btn-cancel { background: var(--gold-light); color: var(--gold); border: 2px solid var(--gold); }

  @media (max-width: 1024px) {
    .main { margin-right: 72px !important; }
  }

  @media (max-width: 768px) {
    .main { margin-right: 0 !important; }
    .content { padding: 16px; }
    .tabs { flex-direction: column; gap: 4px; }
    .enrollment-table th, .enrollment-table td { padding: 10px; font-size: 13px; }
    .page-header h1 { font-size: 24px; }
    .table-wrap { overflow-x: auto; }
  }

  @media (max-width: 480px) {
    .content { padding: 10px; }
    .enrollment-table th, .enrollment-table td { padding: 8px; font-size: 12px; }
  }
</style>
@endsection

@section('content')
  <div class="page-header">
    <h1>إدارة طلبات الالتحاق</h1>
  </div>

  <div class="tabs" id="enrollmentTabs">
    <button class="tab active" data-tab="pending">
      <i class="ri-time-line"></i>
      <span>قيد الانتظار</span>
      <span style="background:rgba(255,193,7,0.2);border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#F59E0B;font-weight:700;">{{ $pending->count() }}</span>
    </button>
    <button class="tab" data-tab="approved">
      <i class="ri-check-line"></i>
      <span>المقبولون</span>
      <span style="background:rgba(52,199,89,0.2);border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--success);font-weight:700;">{{ $approved->count() }}</span>
    </button>
    <button class="tab" data-tab="rejected">
      <i class="ri-close-line"></i>
      <span>المرفوضون</span>
      <span style="background:rgba(255,59,48,0.2);border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--danger);font-weight:700;">{{ $rejected->count() }}</span>
    </button>
  </div>

  <div id="pending-tab" class="content-card">
    <h3><i class="ri-time-line"></i> طلبات الالتحاق المعلقة</h3>
    @if($pending->count() > 0)
      <table class="enrollment-table">
        <thead>
          <tr>
            <th>الطالب</th>
            <th>المسار</th>
            <th>تاريخ التقديم</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pending as $enrollment)
            <tr>
              <td>
                <div class="student-cell">
                  <div class="student-avatar">
                    @if($enrollment->student->avatar_url)
                      <img src="{{ asset('storage/' . $enrollment->student->avatar_url) }}" loading="lazy" alt="{{ $enrollment->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($enrollment->student->name, 0, 1) }}</div>
                    @endif
                  </div>
                  <div class="student-info">
                    <h4>{{ $enrollment->student->name }}</h4>
                    <p>{{ $enrollment->student->email }}</p>
                  </div>
                </div>
              </td>
              <td>{{ $enrollment->course->name }}</td>
              <td>{{ $enrollment->created_at->format('Y-m-d H:i') }}</td>
              <td>
                <div class="actions">
                  <form action="{{ route('teacher.enroll.approve', $enrollment->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="action-btn btn-approve" title="قبول">
                      <i class="ri-check-line"></i>
                    </button>
                  </form>
                  <button class="action-btn btn-reject" data-enrollment-id="{{ $enrollment->id }}" data-student-name="{{ $enrollment->student->name }}" title="رفض">
                    <i class="ri-close-line"></i>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="empty-state">
        <i class="ri-inbox-line"></i>
        <h3>لا توجد طلبات معلقة</h3>
        <p>جميع الطلبات تم البت فيها</p>
      </div>
    @endif
  </div>

  <div id="approved-tab" class="content-card" style="display:none;">
    <h3><i class="ri-check-line"></i> الطلاب المقبولون</h3>
    @if($approved->count() > 0)
      <table class="enrollment-table">
        <thead>
          <tr>
            <th>الطالب</th>
            <th>المسار</th>
            <th>تاريخ القبول</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          @foreach($approved as $enrollment)
            <tr>
              <td>
                <div class="student-cell">
                  <div class="student-avatar">
                    @if($enrollment->student->avatar_url)
                      <img src="{{ asset('storage/' . $enrollment->student->avatar_url) }}" loading="lazy" alt="{{ $enrollment->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($enrollment->student->name, 0, 1) }}</div>
                    @endif
                  </div>
                  <div class="student-info">
                    <h4>{{ $enrollment->student->name }}</h4>
                    <p>{{ $enrollment->student->email }}</p>
                  </div>
                </div>
              </td>
              <td>{{ $enrollment->course->name }}</td>
              <td>{{ $enrollment->enrolled_at->format('Y-m-d H:i') ?? '-' }}</td>
              <td>
                <div class="actions">
                  <form id="removeForm-{{ $enrollment->id }}" action="{{ route('teacher.enroll.remove', $enrollment->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="action-btn btn-remove" title="إزالة" data-enrollment-id="{{ $enrollment->id }}" data-student-name="{{ addslashes($enrollment->student->name) }}">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="empty-state">
        <i class="ri-inbox-line"></i>
        <h3>لا يوجد طلاب مقبولون</h3>
        <p>لم تقبل أي طلبات حتى الآن</p>
      </div>
    @endif
  </div>

  <div id="rejected-tab" class="content-card" style="display:none;">
    <h3><i class="ri-close-line"></i> الطلبات المرفوضة</h3>
    @if($rejected->count() > 0)
      <table class="enrollment-table">
        <thead>
          <tr>
            <th>الطالب</th>
            <th>المسار</th>
            <th>تاريخ الرفض</th>
            <th>السبب</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rejected as $enrollment)
            <tr>
              <td>
                <div class="student-cell">
                  <div class="student-avatar">
                    @if($enrollment->student->avatar_url)
                      <img src="{{ asset('storage/' . $enrollment->student->avatar_url) }}" loading="lazy" alt="{{ $enrollment->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($enrollment->student->name, 0, 1) }}</div>
                    @endif
                  </div>
                  <div class="student-info">
                    <h4>{{ $enrollment->student->name }}</h4>
                    <p>{{ $enrollment->student->email }}</p>
                  </div>
                </div>
              </td>
              <td>{{ $enrollment->course->name }}</td>
              <td>{{ $enrollment->updated_at->format('Y-m-d H:i') ?? '-' }}</td>
              <td>
                @if($enrollment->rejection_reason)
                  <div class="rejection-reason">{{ $enrollment->rejection_reason }}</div>
                @else
                  <span style="color:var(--text-muted);">-</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="empty-state">
        <i class="ri-inbox-line"></i>
        <h3>لم يتم رفض أي طلبات</h3>
        <p>جميع الطلبات المرفوضة ستظهر هنا</p>
      </div>
    @endif
  </div>

  <div id="rejectionModal" class="modal-overlay">
    <div class="rejection-modal">
      <h3 class="modal-title">رفض طلب الالتحاق</h3>
      <p style="color:var(--text-secondary);font-size:14px;margin-bottom:16px;">إضافة سبب (اختياري)</p>
      <form id="rejectForm" method="POST">
        @csrf
        <textarea name="rejection_reason" class="modal-textarea" placeholder="اشرح سبب رفض الطلب..."></textarea>
        <div class="modal-actions">
          <button type="submit" class="btn-submit btn-confirm">رفض</button>
          <button type="button" class="btn-submit btn-cancel" id="closeRejectModalBtn">إلغاء</button>
        </div>
      </form>
    </div>
  </div>

  <div id="removeModal" class="modal-overlay">
    <div class="rejection-modal" style="text-align:center;">
      <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,59,48,0.12);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 20px;">
        <i class="ri-delete-bin-line" style="color:#FF3B30;"></i>
      </div>
      <h3 class="modal-title" style="text-align:center;">إزالة الطالب</h3>
      <p id="removeModalText" style="color:var(--text-secondary);font-size:14px;margin-bottom:24px;text-align:center;line-height:1.6;">هل تريد حقاً إزالة هذا الطالب من المسار؟<br>لا يمكن التراجع عن هذا الإجراء.</p>
      <div class="modal-actions">
        <button type="button" class="btn-submit btn-confirm" id="removeConfirmBtn">إزالة</button>
        <button type="button" class="btn-submit btn-cancel" id="closeRemoveModalBtn">إلغاء</button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  const rejectUrlBase = "{{ url('/teacher/enroll') }}";

  function toggleDark() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';

    html.setAttribute('data-theme', newTheme);
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('app-theme', newTheme);

    const icon = document.getElementById('darkIcon');
    if (icon) {
      icon.className = newTheme === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
    }
  }

  const savedTheme = localStorage.getItem('app-theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
  const darkIcon = document.getElementById('darkIcon');
  if (darkIcon) {
    darkIcon.className = savedTheme === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
  }

  function switchTab(name, btn) {
    document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.getElementById('pending-tab').style.display = (name === 'pending') ? 'block' : 'none';
    document.getElementById('approved-tab').style.display = (name === 'approved') ? 'block' : 'none';
    document.getElementById('rejected-tab').style.display = (name === 'rejected') ? 'block' : 'none';
  }

  function openRejectModal(enrollmentId, studentName) {
    const form = document.getElementById('rejectForm');
    form.action = `${rejectUrlBase}/${enrollmentId}/reject`;
    document.getElementById('rejectionModal').classList.add('active');
  }

  function closeRejectModal() {
    document.getElementById('rejectionModal').classList.remove('active');
  }

  document.getElementById('enrollmentTabs').addEventListener('click', function(e) {
    const btn = e.target.closest('.tab');
    if (btn && btn.dataset.tab) {
      switchTab(btn.dataset.tab, btn);
    }
  });

  document.querySelectorAll('.action-btn.btn-reject[data-enrollment-id]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      openRejectModal(this.dataset.enrollmentId, this.dataset.studentName);
    });
  });

  document.getElementById('closeRejectModalBtn').addEventListener('click', closeRejectModal);

  document.getElementById('rejectionModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
  });

  let _removeFormId = null;
  function openRemoveModal(enrollmentId, studentName) {
    _removeFormId = 'removeForm-' + enrollmentId;
    document.getElementById('removeModalText').innerHTML =
      'هل تريد حقاً إزالة <strong>' + studentName + '</strong> من المسار؟<br>لا يمكن التراجع عن هذا الإجراء.';
    document.getElementById('removeModal').classList.add('active');
  }

  function closeRemoveModal() {
    document.getElementById('removeModal').classList.remove('active');
    _removeFormId = null;
  }

  document.querySelectorAll('.action-btn.btn-remove[data-enrollment-id]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      openRemoveModal(this.dataset.enrollmentId, this.dataset.studentName);
    });
  });

  document.getElementById('closeRemoveModalBtn').addEventListener('click', closeRemoveModal);

  document.getElementById('removeConfirmBtn').addEventListener('click', function() {
    if (_removeFormId) {
      document.getElementById(_removeFormId).submit();
    }
  });

  document.getElementById('removeModal').addEventListener('click', function(e) {
    if (e.target === this) closeRemoveModal();
  });
</script>


@endsection
