@extends('layouts.admin')

@section('title', 'لوحة الإدارة')
@section('page_title', 'لوحة القيادة التنفيذية')
@section('page_subtitle', 'مراقبة لحظية واتخاذ قرار سريع على مستوى المنصة بالكامل')

@section('content')

@php
    $total = max(1, (int) $totalUsers);
    $studentsPct = round(($students / $total) * 100);
    $teachersPct = round(($teachers / $total) * 100);
    $activePct   = round(($activeUsers / $total) * 100);
@endphp

<style>
.x-search-bar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom:18px; }
.x-search-bar input, .x-search-bar select { background:var(--bg-secondary,#1e2535); border:1px solid rgba(255,255,255,.1); color:var(--text-primary,#fff); border-radius:8px; padding:8px 14px; font-size:13px; outline:none; }
.x-search-bar input { flex:1; min-width:160px; }
.x-search-bar input:focus, .x-search-bar select:focus { border-color:#C6A675; }
.x-bulk-bar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; padding:10px 14px; background:rgba(198,166,117,.08); border-radius:8px; margin-bottom:14px; font-size:13px; }
.x-bulk-bar select { background:var(--bg-secondary,#1e2535); border:1px solid rgba(255,255,255,.1); color:var(--text-primary,#fff); border-radius:6px; padding:6px 12px; }
.x-check { accent-color:#C6A675; width:15px; height:15px; cursor:pointer; }
.x-role-label { display:inline-block; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; }
.x-role-admin   { background:rgba(198,166,117,.18); color:#C6A675; }
.x-role-teacher { background:#065f4622; color:#34d399; }
.x-role-student { background:#1d4ed822; color:#60a5fa; }
.x-status-active   { background:#16a34a22; color:#4ade80; font-size:10px; padding:2px 8px; border-radius:20px; font-weight:700; }
.x-status-blocked  { background:#dc262622; color:#f87171; font-size:10px; padding:2px 8px; border-radius:20px; font-weight:700; }
.x-status-inactive { background:#d9770622; color:#fb923c; font-size:10px; padding:2px 8px; border-radius:20px; font-weight:700; }
#bulk-bar { display:none; }
</style>

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#dc262622;border:1px solid #f8717166;color:#f87171;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✗ {{ session('error') }}</div>
@endif

<section class="admin-grid">
    <article class="metric"><div class="k">إجمالي المستخدمين</div><div class="v">{{ $totalUsers }}</div></article>
    <article class="metric"><div class="k">المشرفون</div><div class="v">{{ $admins }}</div></article>
    <article class="metric"><div class="k">المعلمون</div><div class="v">{{ $teachers }}</div></article>
    <article class="metric"><div class="k">الطلاب</div><div class="v">{{ $students }}</div></article>
    <article class="metric"><div class="k">المسارات</div><div class="v">{{ $totalCourses }}</div></article>
    <article class="metric"><div class="k">الدروس</div><div class="v">{{ $totalLessons }}</div></article>
    <article class="metric"><div class="k">الشهادات</div><div class="v">{{ $totalCertificates }}</div></article>
    <article class="metric" style="cursor:pointer;" onclick="location.href='{{ route('admin.enrollments') }}'">
        <div class="k">طلبات معلقة</div>
        <div class="v" style="{{ $pendingEnrollments > 0 ? 'color:#f87171' : '' }}">{{ $pendingEnrollments }}</div>
    </article>
</section>

<section class="admin-form-grid">
    <article class="admin-card">
        <h2>مؤشرات الأداء الحية</h2>
        <div class="admin-form" style="gap:14px;">
            <div>
                <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>نسبة الطلاب</span><span>{{ $studentsPct }}%</span></div>
                <div class="admin-progress"><span style="width:{{ $studentsPct }}%"></span></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>نسبة المعلمين</span><span>{{ $teachersPct }}%</span></div>
                <div class="admin-progress"><span style="width:{{ $teachersPct }}%"></span></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>معدل النشاط</span><span>{{ $activePct }}%</span></div>
                <div class="admin-progress"><span style="width:{{ $activePct }}%"></span></div>
            </div>
        </div>
    </article>
    <article class="admin-card">
        <h2>الإجراءات السريعة</h2>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.create') }}" class="admin-btn"><i class="ri-user-add-line"></i> إنشاء مستخدم</a>
            <a href="{{ route('admin.analytics') }}" class="admin-btn secondary"><i class="ri-line-chart-line"></i> التحليلات</a>
            <a href="{{ route('admin.enrollments') }}" class="admin-btn secondary"><i class="ri-user-received-line"></i> الطلبات</a>
            <a href="{{ route('admin.failed-jobs') }}" class="admin-btn secondary"><i class="ri-error-warning-line"></i> الأعمال الفاشلة</a>
        </div>
    </article>
</section>

{{-- ═══════════════════════════════════════════════════════
     AXIS 1: Enhanced User Table
══════════════════════════════════════════════════════════ --}}
<section class="admin-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
        <h2 style="margin:0;">جميع الحسابات</h2>
        <a href="{{ route('admin.users.export', request()->only(['search','role','status'])) }}" class="admin-btn secondary" style="font-size:12px;">
            <i class="ri-download-line"></i> تصدير CSV
        </a>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.users') }}" class="x-search-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد أو اسم المستخدم...">
        <select name="role">
            <option value="">كل الأدوار</option>
            <option value="admin"   {{ request('role')=='admin'   ? 'selected':'' }}>مشرف</option>
            <option value="teacher" {{ request('role')=='teacher' ? 'selected':'' }}>معلم</option>
            <option value="student" {{ request('role')=='student' ? 'selected':'' }}>طالب</option>
        </select>
        <select name="status">
            <option value="">كل الحالات</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>نشط</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>غير نشط</option>
            <option value="blocked"  {{ request('status')=='blocked'  ? 'selected':'' }}>محظور</option>
        </select>
        <button type="submit" class="admin-btn" style="white-space:nowrap;"><i class="ri-search-line"></i> بحث</button>
        @if(request()->hasAny(['search','role','status']))
            <a href="{{ route('admin.users') }}" class="admin-btn secondary" style="white-space:nowrap;">مسح</a>
        @endif
    </form>

    {{-- Bulk Action Bar --}}
    <form method="POST" action="{{ route('admin.users.bulk') }}" id="bulk-form">
        @csrf
        <div id="bulk-bar" class="x-bulk-bar">
            <span id="bulk-count">0</span> محدد
            <select name="action">
                <option value="">اختر عملية...</option>
                <option value="block">حظر</option>
                <option value="unblock">إلغاء الحظر</option>
                <option value="delete">حذف</option>
            </select>
            <button type="submit" class="admin-btn" onclick="return confirmBulk(this)">تطبيق</button>
        </div>

        <div style="overflow:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:38px;"><input type="checkbox" class="x-check" id="check-all" title="تحديد الكل"></th>
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $user->id }}" class="x-check row-check"></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($user->avatar_url)
                                    <img src="{{ asset('storage/' . $user->avatar_url) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                                @else
                                    <div style="width:28px;height:28px;border-radius:50%;background:rgba(198,166,117,.25);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#C6A675;">{{ mb_substr($user->name,0,1) }}</div>
                                @endif
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td style="font-size:12px;opacity:.8;">{{ $user->email }}</td>
                        <td>
                            <span class="x-role-label x-role-{{ $user->role }}">
                                {{ $user->role === 'admin' ? 'مشرف' : ($user->role === 'teacher' ? 'معلم' : 'طالب') }}
                            </span>
                        </td>
                        <td>
                            <span class="x-status-{{ $user->status ?? 'active' }}">
                                {{ $user->status === 'blocked' ? 'محظور' : ($user->status === 'inactive' ? 'غير نشط' : 'نشط') }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                <a href="{{ route('admin.show', $user) }}" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;">عرض</a>
                                <a href="{{ route('admin.edit', $user) }}" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;">تعديل</a>
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="display:inline;" onsubmit="return confirm('إعادة تعيين كلمة المرور لـ {{ addslashes($user->name) }}؟')">
                                    @csrf
                                    <button type="submit" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;background:rgba(249,115,22,.12);color:#fb923c;">مرور</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:30px;opacity:.5;">لا يوجد مستخدمون مطابقون.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div style="margin-top:12px;">{{ $users->onEachSide(1)->links() }}</div>
</section>

<script>
(function(){
    const checkAll = document.getElementById('check-all');
    const bulkBar  = document.getElementById('bulk-bar');
    const bulkCount = document.getElementById('bulk-count');
    const rows     = document.querySelectorAll('.row-check');

    function updateBulk() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        bulkBar.style.display = checked > 0 ? 'flex' : 'none';
        bulkCount.textContent = checked;
    }

    checkAll.addEventListener('change', function(){
        rows.forEach(r => r.checked = this.checked);
        updateBulk();
    });
    rows.forEach(r => r.addEventListener('change', updateBulk));
})();

function confirmBulk(btn) {
    const action = document.querySelector('#bulk-form select[name=action]').value;
    const count  = document.querySelectorAll('.row-check:checked').length;
    if (!action) { alert('اختر عملية أولاً.'); return false; }
    if (count === 0) { alert('حدد مستخدماً واحداً على الأقل.'); return false; }
    const labels = { block:'حظر', unblock:'إلغاء حظر', delete:'حذف' };
    return confirm(`هل أنت متأكد من ${labels[action]} ${count} مستخدم/مستخدمين؟`);
}
</script>
@endsection
