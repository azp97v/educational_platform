@extends('layouts.admin')
@section('title', 'الإدارة المالية')
@section('page_title', 'الإدارة المالية')
@section('page_subtitle', 'مؤشرات عائد وتكلفة وربحية تقديرية للمنصة')
@section('content')

{{-- KPI Row --}}
<section class="admin-grid" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;">
    <article class="metric">
        <div class="k">الإيراد التقديري</div>
        <div class="v" style="color:#10b981;">{{ number_format($projectedRevenue) }} <small style="font-size:13px;">ر.س</small></div>
    </article>
    <article class="metric">
        <div class="k">التكاليف التقديرية</div>
        <div class="v" style="color:#f59e0b;">{{ number_format($projectedCosts) }} <small style="font-size:13px;">ر.س</small></div>
    </article>
    <article class="metric">
        <div class="k">صافي الربح التقديري</div>
        <div class="v" style="color:#6c63ff;">{{ number_format($projectedProfit) }} <small style="font-size:13px;">ر.س</small></div>
    </article>
    <article class="metric">
        <div class="k">هامش الربح</div>
        <div class="v" style="color:#ec4899;">
            {{ $projectedRevenue > 0 ? number_format(($projectedProfit / $projectedRevenue) * 100, 1) : 0 }}<small style="font-size:13px;">%</small>
        </div>
    </article>
    <article class="metric">
        <div class="k">متوسط قيمة الطالب</div>
        <div class="v">89 <small style="font-size:13px;">ر.س</small></div>
    </article>
    <article class="metric">
        <div class="k">متوسط قيمة المعلم</div>
        <div class="v">149 <small style="font-size:13px;">ر.س</small></div>
    </article>
</section>

{{-- Charts Row --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-top:16px;">

    {{-- Monthly Revenue Chart --}}
    <section class="admin-card" style="padding:20px;">
        <h2 style="margin:0 0 16px;font-size:15px;">نمو الإيرادات الشهرية (12 شهراً)</h2>
        <canvas id="revenueChart" height="90"></canvas>
    </section>

    {{-- Revenue Breakdown Doughnut --}}
    <section class="admin-card" style="padding:20px;">
        <h2 style="margin:0 0 16px;font-size:15px;">توزيع الإيرادات</h2>
        <canvas id="breakdownChart" height="160"></canvas>
        <div style="margin-top:12px;display:flex;flex-direction:column;gap:6px;">
            @foreach($revenueBreakdown as $item)
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-muted);">
                    <span style="width:10px;height:10px;border-radius:50%;background:{{ $item['color'] }};flex-shrink:0;"></span>
                    <span>{{ $item['label'] }}</span>
                    <span style="margin-right:auto;font-weight:700;color:var(--text);">{{ number_format($item['value']) }}</span>
                </div>
            @endforeach
        </div>
    </section>
</div>

{{-- Operational Indicators --}}
<section class="admin-card" style="margin-top:16px;padding:20px;">
    <h2 style="margin:0 0 16px;font-size:15px;">مؤشرات تشغيلية</h2>
    <div class="admin-form-grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));">
        <div><label>المسارات</label><input value="{{ $totalCourses }}" disabled></div>
        <div><label>الدروس</label><input value="{{ $totalLessons }}" disabled></div>
        <div><label>الاختبارات</label><input value="{{ $totalExams }}" disabled></div>
        <div><label>الشهادات الصادرة</label><input value="{{ $totalCertificates }}" disabled></div>
        <div><label>إجمالي الطلاب</label><input value="{{ $students }}" disabled></div>
        <div><label>إجمالي المعلمين</label><input value="{{ $teachers }}" disabled></div>
        <div><label>تكلفة / مسار</label><input value="{{ $totalCourses > 0 ? number_format($projectedCosts / $totalCourses, 0) : 0 }} ر.س" disabled></div>
        <div><label>تكلفة / درس</label><input value="{{ $totalLessons > 0 ? number_format($projectedCosts / $totalLessons, 0) : 0 }} ر.س" disabled></div>
    </div>
</section>

<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
(function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const labelColor = isDark ? '#9ca3af' : '#6b7280';

    const monthlyData = @json($monthlyData);
    const labels   = monthlyData.map(d => d.label);
    const revenues = monthlyData.map(d => d.revenue);
    const users    = monthlyData.map(d => d.users);

    // Revenue Bar Chart
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'الإيراد التقديري (ر.س)',
                    data: revenues,
                    backgroundColor: 'rgba(108,99,255,0.75)',
                    borderColor: '#6c63ff',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'عدد المسجّلين',
                    data: users,
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y2',
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { labels: { color: labelColor, font: { family: 'inherit' } } } },
            scales: {
                x: { ticks: { color: labelColor }, grid: { color: gridColor } },
                y: {
                    position: 'right',
                    ticks: { color: '#6c63ff', callback: v => v.toLocaleString() + ' ر.س' },
                    grid: { color: gridColor }
                },
                y2: {
                    position: 'left',
                    ticks: { color: '#10b981' },
                    grid: { display: false }
                }
            }
        }
    });

    // Breakdown Doughnut
    const bd = @json($revenueBreakdown);
    new Chart(document.getElementById('breakdownChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: bd.map(d => d.label),
            datasets: [{ data: bd.map(d => d.value), backgroundColor: bd.map(d => d.color), borderWidth: 0 }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: { legend: { display: false } }
        }
    });
})();
</script>
@endsection
