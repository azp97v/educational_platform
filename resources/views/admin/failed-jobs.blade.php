@extends('layouts.admin')
@section('title', 'الأعمال الفاشلة')
@section('page_title', 'إدارة الأعمال الفاشلة')
@section('page_subtitle', 'فحص وإعادة تشغيل أو حذف المهام التي فشل تنفيذها')

@section('content')

@if(session('success'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#dc262622;border:1px solid #f8717166;color:#f87171;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✗ {{ session('error') }}</div>
@endif

<style>
.exc-cell { font-size:11px; font-family:monospace; color:#fb923c; background:rgba(249,115,22,.06); border-radius:4px; padding:2px 6px; max-width:320px; word-break:break-word; display:inline-block; }
</style>

@if($jobs->total() > 0)
<div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
    <form method="POST" action="{{ route('admin.failed-jobs.delete-all') }}" onsubmit="return confirm('حذف جميع الأعمال الفاشلة نهائياً؟')">
        @csrf @method('DELETE')
        <button type="submit" class="admin-btn secondary" style="background:rgba(239,68,68,.1);color:#f87171;">
            <i class="ri-delete-bin-line"></i> حذف الكل ({{ $jobs->total() }})
        </button>
    </form>
</div>
@endif

<section class="admin-card">
    @if($jobs->total() === 0)
        <div style="text-align:center;padding:50px;opacity:.45;">
            <div style="font-size:48px;margin-bottom:12px;">✓</div>
            <div style="font-size:16px;font-weight:600;">لا توجد أعمال فاشلة</div>
            <div style="font-size:13px;margin-top:6px;">جميع المهام تعمل بشكل طبيعي.</div>
        </div>
    @else
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>UUID</th>
                    <th>الطابور</th>
                    <th>رسالة الخطأ</th>
                    <th>وقت الفشل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            @foreach($jobs as $job)
                @php
                    $payload   = json_decode($job->payload, true);
                    $jobClass  = $payload['displayName'] ?? ($payload['job'] ?? '—');
                    $excLine   = Str::before(Str::after($job->exception ?? '', "\n"), "\n");
                    $excShort  = Str::limit(trim($excLine ?: $job->exception), 80);
                @endphp
                <tr>
                    <td style="font-size:10px;font-family:monospace;opacity:.5;">{{ Str::limit($job->uuid, 12) }}</td>
                    <td>
                        <div style="font-size:12px;font-weight:600;">{{ $job->queue }}</div>
                        <div style="font-size:10px;opacity:.5;font-family:monospace;">{{ Str::afterLast($jobClass, '\\') }}</div>
                    </td>
                    <td>
                        <span class="exc-cell" title="{{ $job->exception }}">{{ $excShort ?: '—' }}</span>
                    </td>
                    <td style="font-size:12px;opacity:.6;">
                        {{ \Carbon\Carbon::parse($job->failed_at)->format('Y-m-d H:i') }}<br>
                        <span style="font-size:10px;opacity:.5;">{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->uuid) }}">
                                @csrf
                                <button type="submit" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;background:rgba(34,197,94,.1);color:#4ade80;" title="إعادة المحاولة">
                                    <i class="ri-restart-line"></i> إعادة
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.failed-jobs.delete', $job->uuid) }}" onsubmit="return confirm('حذف هذه العملية؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;background:rgba(239,68,68,.1);color:#f87171;" title="حذف">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                            <button type="button" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;"
                                onclick="document.getElementById('exc-{{ $job->id }}').classList.toggle('hidden')">
                                <i class="ri-code-line"></i>
                            </button>
                        </div>
                        <pre id="exc-{{ $job->id }}" class="hidden" style="font-size:10px;margin-top:8px;background:rgba(0,0,0,.3);padding:10px;border-radius:6px;white-space:pre-wrap;word-break:break-all;max-height:200px;overflow:auto;display:none;">{{ $job->exception }}</pre>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;">{{ $jobs->links() }}</div>
    @endif
</section>

<script>
document.querySelectorAll('pre[id^="exc-"]').forEach(el => {
    el.style.display = 'none';
});
document.querySelectorAll('button[onclick*="exc-"]').forEach(btn => {
    btn.addEventListener('click', function(){
        const id = this.getAttribute('onclick').match(/'(exc-[^']+)'/)[1];
        const pre = document.getElementById(id);
        pre.style.display = pre.style.display === 'none' ? 'block' : 'none';
    });
    btn.removeAttribute('onclick');
});
</script>
@endsection
