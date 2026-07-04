@extends('layouts.app-unified')

@section('title','نسبة الإنجاز - لوحة تحكم المعلم')

@section('styles')
<style>
  :root {
    --gold: #C4963A;
    --gold-dark: #A07A28;
    --gold-light: rgba(196,150,58,0.14);
    --sidebar-w: 260px;
    --topbar-h: 70px;
    --primary-dark: #121212;
    --card-bg: var(--theme-surface);
    --text-primary: #F4F4F7;
    --text-secondary: #AEAEB2;
    --text-muted: #A0A0B0;
    --border: rgba(255,255,255,0.08);
    --shadow: 0 18px 50px rgba(0,0,0,0.35);
    --shadow-hover: 0 18px 60px rgba(0,0,0,0.45);
    --radius-lg: 16px;
    --radius-md: 12px;
    --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
  }

  body {
    background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 22%),
                linear-gradient(180deg, #071018 0%, #08141f 40%, #09131f 100%);
    color: var(--text-primary);
  }

  .sidebar { width: var(--sidebar-w); }
  .main { margin-right: calc(var(--sidebar-w) + 22px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

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

  .date-range {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .date-input {
    padding: 10px 14px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-family: 'Tajawal', sans-serif;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
  }

  .date-input:hover,
  .date-input:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px var(--gold-light);
  }

  .stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
  }

  .stat-small {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 14px;
    text-align: center;
    transition: var(--transition);
    animation: fadeUp 0.5s ease;
  }

  .stat-small:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
  }

  .stat-label {
    font-size: 11px;
    color: var(--text-secondary);
    font-weight: 600;
    margin-bottom: 4px;
  }

  .stat-number {
    font-size: 18px;
    font-weight: 900;
    background: linear-gradient(135deg, #C4963A, #A07A28);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 20px;
  }

  .chart-wrapper {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px;
    box-shadow: var(--shadow);
    animation: fadeUp 0.5s ease 0.1s both;
    margin-bottom: 24px;
  }

  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 18px;
    margin-top: 20px;
  }

  .metric-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    animation: fadeUp 0.5s ease;
  }

  .metric-card::before {
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

  .metric-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 24px rgba(196,150,58,0.15);
    border-color: var(--gold);
  }

  .metric-card:hover::before { opacity: 0.05; }

  .metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    background: var(--gold-light);
    color: var(--gold);
    transition: var(--transition);
  }

  .metric-card:hover .metric-icon { transform: scale(1.1) rotate(10deg); }

  .metric-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 4px;
  }

  .metric-value {
    font-size: 24px;
    font-weight: 900;
    background: linear-gradient(135deg, #C4963A, #A07A28);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
  }

  .metric-change {
    font-size: 12px;
    margin-top: 8px;
  }

  .metric-change.up { color: #34C759; }
  .metric-change.down { color: #FF3B30; }

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

  .btn:hover {
    background: var(--gold-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(196,150,58,0.4);
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 1024px) {
    .main { margin-right: 72px !important; }
  }

  @media (max-width: 768px) {
    .main { margin-right: 0 !important; }
    .content { padding: 20px; }
    .date-range { flex-direction: column; align-items: stretch; }
    .chart-container { height: 260px; }
  }

  @media (max-width: 480px) {
    .content { padding: 12px; }
    .chart-container { height: 200px; }
  }
</style>
@endsection

@section('content')
  <div class="page-header">
    <h1 class="page-title">التحليلات والإحصائيات</h1>
    <p class="page-subtitle">تحليل شامل لأداء طلابك والمحتوى التعليمي</p>
  </div>

  <div class="date-range">
    <input type="date" class="date-input" value="2024-01-01">
    <span style="color: var(--text-muted);">إلى</span>
    <input type="date" class="date-input" value="2024-12-31">
    <button class="btn"><i class="ri-refresh-line"></i> تحديث</button>
  </div>

  <div class="stats-row">
    <div class="stat-small">
      <div class="stat-label">إجمالي الحضور</div>
      <div class="stat-number">4,234</div>
    </div>
    <div class="stat-small">
      <div class="stat-label">متوسط الدرجات</div>
      <div class="stat-number">82%</div>
    </div>
    <div class="stat-small">
      <div class="stat-label">الطلاب النشطين</div>
      <div class="stat-number">378</div>
    </div>
    <div class="stat-small">
      <div class="stat-label">معدل الإنجاز</div>
      <div class="stat-number">76%</div>
    </div>
  </div>

  <div class="chart-wrapper">
    <h3 style="margin-bottom: 16px; font-size: 16px; font-weight: 800;">أداء الطلاب الأسبوعية</h3>
    <div class="chart-container">
      <canvas id="performanceChart"></canvas>
    </div>
  </div>

  <div class="chart-wrapper">
    <h3 style="margin-bottom: 16px; font-size: 16px; font-weight: 800;">توزيع الدرجات</h3>
    <div class="chart-container" style="height: 250px;">
      <canvas id="gradesChart"></canvas>
    </div>
  </div>

  <div class="metrics-grid">
    <div class="metric-card">
      <div class="metric-icon"><i class="ri-user-check-line"></i></div>
      <div class="metric-title">الطلاب المتقدمون</div>
      <div class="metric-value">125</div>
      <div class="metric-change up"><i class="ri-arrow-up-line"></i> +12% هذا الشهر</div>
    </div>

    <div class="metric-card">
      <div class="metric-icon" style="background: rgba(255,59,48,0.1); color: #FF3B30;"><i class="ri-alert-line"></i></div>
      <div class="metric-title">الطلاب بحاجة متابعة</div>
      <div class="metric-value">34</div>
      <div class="metric-change down"><i class="ri-arrow-down-line"></i> -8% هذا الشهر</div>
    </div>

    <div class="metric-card">
      <div class="metric-icon" style="background: rgba(52,199,89,0.1); color: #34C759;"><i class="ri-trophy-line"></i></div>
      <div class="metric-title">حاملو الشهادات</div>
      <div class="metric-value">89</div>
      <div class="metric-change up"><i class="ri-arrow-up-line"></i> +5 هذا الشهر</div>
    </div>

    <div class="metric-card">
      <div class="metric-icon" style="background: rgba(198,166,117,0.1); color: #C6A675;"><i class="ri-book-open-line"></i></div>
      <div class="metric-title">الدروس المكتملة</div>
      <div class="metric-value">342</div>
      <div class="metric-change up"><i class="ri-arrow-up-line"></i> +23% هذا الشهر</div>
    </div>
  </div>
@endsection

@section('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
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

  const perfCtx = document.getElementById('performanceChart').getContext('2d');
  new Chart(perfCtx, {
    type: 'line',
    data: {
      labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
      datasets: [{
        label: 'متوسط الأداء',
        data: [72, 74, 79, 76, 82, 85, 88],
        borderColor: '#C4963A',
        backgroundColor: 'rgba(196,150,58,0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointBackgroundColor: '#C4963A',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { min: 60, max: 100 } }
    }
  });

  const gradesCtx = document.getElementById('gradesChart').getContext('2d');
  new Chart(gradesCtx, {
    type: 'doughnut',
    data: {
      labels: ['ممتاز (90-100)', 'جيد جداً (80-89)', 'جيد (70-79)', 'مقبول (60-69)'],
      datasets: [{
        data: [125, 189, 112, 56],
        backgroundColor: ['#C4963A', '#A07A28', '#FF9500', '#FF3B30'],
        borderColor: 'var(--card-bg)',
        borderWidth: 2
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
</script>


@endsection
