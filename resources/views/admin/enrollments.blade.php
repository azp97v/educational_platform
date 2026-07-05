@extends('layouts.admin')
@section('title', 'طلبات الالتحاق')
@section('page_title', 'إدارة طلبات الالتحاق')
@section('page_subtitle', 'مراجعة وقبول أو رفض طلبات الطلاب للانضمام إلى المسارات')

@section('content')

@if(session('success'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#dc262622;border:1px solid #f8717166;color:#f87171;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✗ {{ session('error') }}</div>
@endif

<style>
.enroll-tabs { display:flex; gap:6px; margin-bottom:18px; flex-wrap:wrap; }
.enroll-tab { padding:7px 18px; border-radius:8px; font-size:13px; font-weight:600; border:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.55); text-decoration:none; background:transparent; transition:.2s; }
.enroll-tab:hover { border-color:rgba(108,99,255,.4); color:#a78bfa; }
.enroll-tab.active { background:rgba(108,99,255,.18); border-color:#6c63ff; color:#a78bfa; }
.reject-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; align-items:center; justify-content:center; }
.reject-modal.open { display:flex; }
.reject-box { background:#1a2035; border:1px solid rgba(255,255,255,.1); border-radius:14px; padding:28px; width:90%; max-width:440px; }
.reject-box h3 { margin:0 0 16px; font-size:16px; }
.reject-box textarea { width:100%; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:8px; padding:10px 14px; font-size:13px; resize:vertical; min-height:80px; }
.reject-box .actions { display:flex; gap:8px; margin-top:14px; justify-content:flex-end; }
</style>

{{-- Filter tabs --}}
<div class="enroll-tabs">
    <a href="{{ route('admin.enrollments', ['filter'=>'pending']) }}" class="enroll-tab {{ $filter==='pending'?'active':'' }}">
        معلّق @if($pendingCount > 0)<span style="background:#ef4444;color:#fff;font-size:10px;padding:1px 7px;border-radius:20px;font-weight:700;margin-right:6px;">{{ $pendingCount }}</span>@endif
    </a>
    <a href="{{ route('admin.enrollments', ['filter'=>'approved']) }}" class="enroll-tab {{ $filter==='approved'?'active':'' }}">مقبول</a>
    <a href="{{ route('admin.enrollments', ['filter'=>'rejected']) }}" class="enroll-tab {{ $filter==='rejected'?'active':'' }}">مرفوض</a>
    <a href="{{ route('admin.enrollments', ['filter'=>'all']) }}" class="enroll-tab {{ $filter==='all'?'active':'' }}">الكل</a>
</div>

<section class="admin-card">
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الطالب</th>
                    <th>المسار</th>
                    <th>المعلم</th>
                    <th>تاريخ الطلب</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($enrollments as $en)
                <tr>
                    <td style="opacity:.4;font-size:12px;">{{ $en->id }}</td>
                    <td>
                        @if($en->student)
                            <div style="font-size:13px;font-weight:600;">{{ $en->student->name }}</div>
                            <div style="font-size:11px;opacity:.5;">{{ $en->student->email }}</div>
                        @else
                            <span style="opacity:.4;">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $en->course?->name ?? '—' }}</td>
                    <td style="font-size:12px;opacity:.7;">{{ $en->course?->instructor?->name ?? '—' }}</td>
                    <td style="font-size:12px;opacity:.6;">{{ $en->created_at?->format('Y-m-d') }}</td>
                    <td>
                        @if($en->status === 'approved')
                            <span style="color:#4ade80;font-size:11px;font-weight:700;">مقبول</span>
                        @elseif($en->status === 'rejected')
                            <span style="color:#f87171;font-size:11px;font-weight:700;">مرفوض</span>
                            @if($en->rejection_reason)
                                <div style="font-size:10px;opacity:.5;margin-top:2px;" title="{{ $en->rejection_reason }}">{{ Str::limit($en->rejection_reason, 25) }}</div>
                            @endif
                        @else
                            <span style="color:#fbbf24;font-size:11px;font-weight:700;">معلّق</span>
                        @endif
                    </td>
                    <td>
                        @if($en->status === 'pending')
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('admin.enrollments.approve', $en) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn" style="font-size:11px;padding:4px 12px;background:rgba(34,197,94,.15);color:#4ade80;">قبول</button>
                                </form>
                                <button type="button" class="admin-btn secondary" style="font-size:11px;padding:4px 12px;background:rgba(239,68,68,.12);color:#f87171;"
                                    onclick="openReject({{ $en->id }})">رفض</button>
                            </div>
                        @else
                            <span style="opacity:.35;font-size:12px;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;opacity:.4;">لا يوجد طلبات في هذه الفئة.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;">{{ $enrollments->links() }}</div>
</section>

{{-- Reject modals --}}
@foreach($enrollments as $en)
    @if($en->status === 'pending')
    <div class="reject-modal" id="reject-modal-{{ $en->id }}">
        <div class="reject-box">
            <h3>رفض طلب الالتحاق</h3>
            <p style="font-size:13px;opacity:.65;margin-bottom:14px;">
                طالب: <strong>{{ $en->student?->name ?? '—' }}</strong><br>
                مسار: <strong>{{ $en->course?->name ?? '—' }}</strong>
            </p>
            <form method="POST" action="{{ route('admin.enrollments.reject', $en) }}">
                @csrf
                <label style="font-size:12px;opacity:.7;display:block;margin-bottom:6px;">سبب الرفض (اختياري)</label>
                <textarea name="reason" placeholder="اكتب سبب الرفض..."></textarea>
                <div class="actions">
                    <button type="button" class="admin-btn secondary" onclick="closeReject({{ $en->id }})">إلغاء</button>
                    <button type="submit" class="admin-btn" style="background:rgba(239,68,68,.15);color:#f87171;">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

<script>
function openReject(id) { document.getElementById('reject-modal-'+id).classList.add('open'); }
function closeReject(id) { document.getElementById('reject-modal-'+id).classList.remove('open'); }
// close on backdrop click
document.querySelectorAll('.reject-modal').forEach(m => {
    m.addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); });
});
</script>
@endsection
