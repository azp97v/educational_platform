@extends('layouts.admin')
@section('title', 'مركز المراقبة الحية')
@section('page_title', 'مركز المراقبة الحية')
@section('page_subtitle', 'متابعة تشغيلية لحظية — يتحدث كل 60 ثانية')
@section('content')

{{-- Auto-refresh indicator --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
    <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;animation:pulse-live 1.4s ease-in-out infinite;"></span>
    <span style="font-size:12px;color:var(--text-muted);">مباشر — آخر تحديث: <span id="last-refresh">الآن</span></span>
    <span style="margin-right:auto;font-size:12px;color:var(--text-muted);">التحديث التالي بعد <span id="countdown" style="font-weight:700;color:#6c63ff;">60</span> ثانية</span>
</div>

{{-- KPI Row --}}
<section class="admin-grid" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;">
    <article class="metric">
        <div class="k"><i class="ri-user-line" style="color:#6c63ff;margin-left:4px;"></i>مستخدمون نشطون</div>
        <div class="v" style="color:#6c63ff;">{{ $ops['online_users'] }}</div>
    </article>
    <article class="metric">
        <div class="k"><i class="ri-user-received-line" style="color:#f59e0b;margin-left:4px;"></i>طلبات معلّقة</div>
        <div class="v" style="color:#f59e0b;">{{ $ops['pending_enrollments'] }}</div>
    </article>
    <article class="metric">
        <div class="k"><i class="ri-message-3-line" style="color:#ec4899;margin-left:4px;"></i>رسائل غير مقروءة</div>
        <div class="v" style="color:#ec4899;">{{ $ops['unread_messages'] }}</div>
    </article>
    <article class="metric">
        <div class="k"><i class="ri-user-add-line" style="color:#10b981;margin-left:4px;"></i>مستخدمون جدد (24س)</div>
        <div class="v" style="color:#10b981;">{{ $ops['new_users_24h'] }}</div>
    </article>
</section>

{{-- Alerts --}}
<section class="admin-card" style="margin-top:14px;padding:16px 20px;">
    <h2 style="margin:0 0 12px;font-size:14px;"><i class="ri-alarm-warning-line" style="color:#f59e0b;margin-left:6px;"></i>التنبيهات التشغيلية</h2>
    @if($alerts->isEmpty())
        <div style="display:flex;align-items:center;gap:8px;color:#10b981;font-size:13px;padding:8px 0;">
            <i class="ri-shield-check-line" style="font-size:18px;"></i>
            لا توجد تنبيهات حرجة — المنصة تعمل بشكل طبيعي.
        </div>
    @else
        <div style="display:grid;gap:8px;">
            @foreach($alerts as $alert)
                @php
                    $colors = ['warning'=>['bg'=>'rgba(245,158,11,0.1)','border'=>'rgba(245,158,11,0.3)','text'=>'#f59e0b','icon'=>'ri-error-warning-line'],
                               'error'  =>['bg'=>'rgba(239,68,68,0.1)', 'border'=>'rgba(239,68,68,0.3)', 'text'=>'#ef4444','icon'=>'ri-close-circle-line'],
                               'info'   =>['bg'=>'rgba(108,99,255,0.1)','border'=>'rgba(108,99,255,0.3)','text'=>'#6c63ff','icon'=>'ri-information-line']];
                    $c = $colors[$alert['level']] ?? $colors['info'];
                @endphp
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }};font-size:13px;color:{{ $c['text'] }};">
                    <i class="{{ $c['icon'] }}" style="font-size:16px;flex-shrink:0;"></i>
                    {{ $alert['msg'] }}
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- Two-column tables --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;">

    {{-- Recent Users --}}
    <section class="admin-card" style="padding:0;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;">
            <h2 style="margin:0;font-size:14px;"><i class="ri-user-add-line" style="color:#10b981;margin-left:6px;"></i>آخر المسجّلين</h2>
            <a href="{{ route('admin.index') }}" style="font-size:11px;color:#6c63ff;text-decoration:none;">عرض الكل</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--surface-2);">
                        <th style="padding:8px 14px;text-align:right;font-size:11px;color:var(--text-muted);font-weight:600;">الاسم</th>
                        <th style="padding:8px 14px;text-align:center;font-size:11px;color:var(--text-muted);font-weight:600;">الدور</th>
                        <th style="padding:8px 14px;text-align:center;font-size:11px;color:var(--text-muted);font-weight:600;">منذ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:8px 14px;">
                            <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $u->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $u->email }}</div>
                        </td>
                        <td style="padding:8px 14px;text-align:center;">
                            @php $rc=['student'=>['#6c63ff','الطالب'],'teacher'=>['#10b981','المعلم'],'admin'=>['#f59e0b','المشرف']][$u->role] ?? ['#9ca3af',$u->role] @endphp
                            <span style="font-size:11px;padding:2px 8px;border-radius:12px;background:{{ $rc[0] }}22;color:{{ $rc[0] }};font-weight:700;">{{ $rc[1] }}</span>
                        </td>
                        <td style="padding:8px 14px;text-align:center;font-size:11px;color:var(--text-muted);">{{ $u->created_at?->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:16px;text-align:center;color:var(--text-muted);font-size:13px;">لا يوجد مستخدمون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Pending Enrollments --}}
    <section class="admin-card" style="padding:0;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;">
            <h2 style="margin:0;font-size:14px;"><i class="ri-user-received-line" style="color:#f59e0b;margin-left:6px;"></i>طلبات الالتحاق المعلّقة</h2>
            <a href="{{ route('admin.enrollments') }}" style="font-size:11px;color:#6c63ff;text-decoration:none;">إدارة الكل</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--surface-2);">
                        <th style="padding:8px 14px;text-align:right;font-size:11px;color:var(--text-muted);font-weight:600;">الطالب</th>
                        <th style="padding:8px 14px;text-align:right;font-size:11px;color:var(--text-muted);font-weight:600;">المسار</th>
                        <th style="padding:8px 14px;text-align:center;font-size:11px;color:var(--text-muted);font-weight:600;">منذ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingEnrollments as $en)
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:8px 14px;font-size:13px;color:var(--text);">{{ $en->student?->name ?? '—' }}</td>
                        <td style="padding:8px 14px;font-size:12px;color:var(--text-muted);">{{ $en->course?->name ?? '—' }}</td>
                        <td style="padding:8px 14px;text-align:center;font-size:11px;color:var(--text-muted);">{{ $en->created_at?->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:16px;text-align:center;color:#10b981;font-size:13px;"><i class="ri-check-line"></i> لا توجد طلبات معلّقة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
@keyframes pulse-live {
    0%,100% { opacity:1; transform:scale(1); }
    50% { opacity:.4; transform:scale(1.3); }
}
</style>
<script>
(function() {
    let secs = 60;
    const cd = document.getElementById('countdown');
    const lr = document.getElementById('last-refresh');
    setInterval(() => {
        secs--;
        if (cd) cd.textContent = secs;
        if (secs <= 0) { window.location.reload(); }
    }, 1000);
    if (lr) lr.textContent = new Date().toLocaleTimeString('ar-SA');
})();
</script>
@endsection
