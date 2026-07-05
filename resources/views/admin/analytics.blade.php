@extends('layouts.admin')
@section('title', 'التحليلات')
@section('page_title', 'مركز التحليلات')
@section('page_subtitle', 'قراءة تشغيلية وتعليمية دقيقة')

@php
    $total       = max(1, (int) $kpis['total_users']);
    $adminsPct   = round(($roleDistribution['admins']   / $total) * 100);
    $teachersPct = round(($roleDistribution['teachers'] / $total) * 100);
    $studentsPct = round(($roleDistribution['students'] / $total) * 100);
@endphp

@section('content')

<style>
.chart-wrap { position:relative; width:100%; }
.chart-wrap canvas { width:100% !important; }
.kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:14px; margin-bottom:20px; }
.kpi-card { background:rgba(198,166,117,.08); border-radius:10px; padding:16px; text-align:center; border:1px solid rgba(198,166,117,.18); }
.kpi-card .kv { font-size:26px; font-weight:900; color:#C6A675; }
.kpi-card .kk { font-size:11px; opacity:.6; margin-top:4px; }
.growth-pill { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; padding:2px 10px; border-radius:20px; }
.growth-up   { background:#16a34a22; color:#4ade80; }
.growth-down { background:#dc262622; color:#f87171; }
</style>

{{-- KPI Row --}}
<div class="kpi-row">
    <div class="kpi-card"><div class="kv">{{ $kpis['total_users'] }}</div><div class="kk">إجمالي المستخدمين</div></div>
    <div class="kpi-card"><div class="kv">{{ $thisMonth }}</div><div class="kk">تسجيلات هذا الشهر</div></div>
    <div class="kpi-card">
        <div class="kv">
            <span class="growth-pill {{ $growth >= 0 ? 'growth-up' : 'growth-down' }}">
                {{ $growth >= 0 ? '▲' : '▼' }} {{ abs($growth) }}%
            </span>
        </div>
        <div class="kk">نمو مقارنة بالشهر الماضي</div>
    </div>
    <div class="kpi-card"><div class="kv">{{ $kpis['total_messages'] }}</div><div class="kk">الرسائل</div></div>
    <div class="kpi-card"><div class="kv">{{ $kpis['total_exams'] }}</div><div class="kk">الاختبارات</div></div>
    <div class="kpi-card"><div class="kv">{{ $kpis['pending_enrollments'] }}</div><div class="kk">طلبات معلقة</div></div>
</div>

<section class="admin-form-grid">
    {{-- Line chart: daily registrations --}}
    <article class="admin-card" style="grid-column:1/-1;">
        <h2>التسجيلات اليومية — آخر 30 يوم</h2>
        <div class="chart-wrap" style="height:240px;">
            <canvas id="regChart"></canvas>
        </div>
    </article>
</section>

<section class="admin-form-grid">
    {{-- Pie chart: role distribution --}}
    <article class="admin-card">
        <h2>توزيع الأدوار</h2>
        <div class="chart-wrap" style="height:220px;display:flex;align-items:center;justify-content:center;">
            <canvas id="roleChart" style="max-width:220px;max-height:220px;"></canvas>
        </div>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:14px;font-size:12px;">
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#C6A675;margin-left:5px;"></span>مشرفون ({{ $roleDistribution['admins'] }})</span>
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#6B4422;margin-left:5px;"></span>معلمون ({{ $roleDistribution['teachers'] }})</span>
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#F0DDB8;margin-left:5px;"></span>طلاب ({{ $roleDistribution['students'] }})</span>
        </div>
    </article>

    {{-- Top teachers --}}
    <article class="admin-card">
        <h2>أكثر المعلمين طلاباً</h2>
        @forelse($topTeachers as $t)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06);">
                <div style="font-size:13px;">{{ $t->name }}</div>
                <div style="font-size:12px;font-weight:700;color:#C6A675;">{{ $t->student_count }} طالب</div>
            </div>
        @empty
            <div style="opacity:.4;font-size:13px;text-align:center;padding:20px;">لا توجد بيانات.</div>
        @endforelse
    </article>
</section>

<section class="admin-card">
    <h2>التوزيع النسبي للأدوار</h2>
    <div class="admin-form-grid">
        <div>
            <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>المشرفون</span><span>{{ $adminsPct }}%</span></div>
            <div class="admin-progress"><span style="width:{{ $adminsPct }}%"></span></div>
        </div>
        <div>
            <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>المعلمون</span><span>{{ $teachersPct }}%</span></div>
            <div class="admin-progress"><span style="width:{{ $teachersPct }}%"></span></div>
        </div>
        <div>
            <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>الطلاب</span><span>{{ $studentsPct }}%</span></div>
            <div class="admin-progress"><span style="width:{{ $studentsPct }}%"></span></div>
        </div>
    </div>
</section>

<section class="admin-card">
    <h2>أحدث المستخدمين</h2>
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead><tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>آخر انضمام</th></tr></thead>
            <tbody>
            @forelse($latestUsers as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td style="font-size:12px;opacity:.7;">{{ $u->email }}</td>
                    <td>{{ $u->role }}</td>
                    <td>{{ $u->created_at?->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;opacity:.4;">لا توجد بيانات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
(function(){
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(198,166,117,0.08)' : 'rgba(141,114,82,0.10)';
    const textColor  = isDark ? 'rgba(198,166,117,0.70)' : 'rgba(107,80,64,0.80)';
    const accent     = '#C6A675';

    // Daily registrations line chart
    const regCtx = document.getElementById('regChart');
    if (regCtx) {
        new Chart(regCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'تسجيلات جديدة',
                    data: {!! json_encode($chartCounts) !!},
                    borderColor: accent,
                    backgroundColor: 'rgba(198,166,117,0.13)',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: accent,
                    fill: true,
                    tension: 0.35,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks:{ color:textColor, maxRotation:45, font:{size:10} }, grid:{ color:gridColor } },
                    y: { ticks:{ color:textColor, font:{size:10}, stepSize:1 }, grid:{ color:gridColor }, beginAtZero:true },
                }
            }
        });
    }

    // Role pie chart
    const roleCtx = document.getElementById('roleChart');
    if (roleCtx) {
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: ['مشرفون', 'معلمون', 'طلاب'],
                datasets: [{
                    data: [{{ $roleDistribution['admins'] }}, {{ $roleDistribution['teachers'] }}, {{ $roleDistribution['students'] }}],
                    backgroundColor: ['#C6A675','#6B4422','#F0DDB8'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1a1410' : '#f4f0ea',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                cutout: '68%',
                plugins: { legend: { display: false } }
            }
        });
    }
})();
</script>
@endsection
