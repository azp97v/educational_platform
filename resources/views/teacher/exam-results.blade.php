<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نتائج الاختبار | {{ $exam->name }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

  <style>
    * {
      --gold: #C4963A;
      --dark-gold: #A07A28;
      --success: #06a77d;
      --danger: #D32F2F;
      --warning: #F57C00;
      --card-bg: var(--theme-surface);
      --card-hover: rgba(196, 150, 58, 0.08);
      --border-color: rgba(196, 150, 58, 0.25);
      --light-gray: var(--text-secondary);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: var(--theme-page-bg);
      color: var(--text-primary);
      min-height: 100vh;
      transition: background 0.3s, color 0.3s;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 3rem;
      padding-bottom: 2rem;
      border-bottom: 2px solid var(--border-color);
    }

    .header h1 {
      font-family: 'Josefin Slab', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--gold);
      margin: 0;
    }

    .back-btn {
      padding: 0.7rem 1.5rem;
      background: transparent;
      border: 1px solid var(--border-color);
      color: var(--gold);
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .back-btn:hover {
      background: rgba(196, 150, 58, 0.1);
      border-color: var(--gold);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    @media (max-width: 600px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }

    .stat-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 1.5rem;
      backdrop-filter: blur(5px);
    }

    .stat-label {
      font-size: 0.85rem;
      color: var(--light-gray);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.7rem;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--gold);
      font-family: 'Josefin Slab', serif;
    }

    .stat-desc {
      font-size: 0.85rem;
      color: var(--light-gray);
      margin-top: 0.3rem;
    }

    .table-container {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      overflow: hidden;
      backdrop-filter: blur(5px);
      margin-bottom: 2rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: rgba(196, 150, 58, 0.1);
      border-bottom: 2px solid var(--border-color);
    }

    th {
      padding: 1.2rem;
      text-align: right;
      font-weight: 600;
      color: var(--gold);
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }

    td {
      padding: 1.2rem;
      border-bottom: 1px solid var(--border-color);
      color: var(--light-gray);
    }

    tbody tr:hover {
      background: rgba(196, 150, 58, 0.05);
    }

    .badge {
      display: inline-block;
      padding: 0.4rem 0.8rem;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .badge-success {
      background: rgba(6, 168, 125, 0.2);
      color: var(--success);
    }

    .badge-danger {
      background: rgba(211, 47, 47, 0.2);
      color: var(--danger);
    }

    .badge-warning {
      background: rgba(245, 127, 0, 0.2);
      color: var(--warning);
    }

    .score-bar {
      display: inline-block;
      background: rgba(196, 150, 58, 0.2);
      border: 1px solid var(--gold);
      border-radius: 4px;
      height: 24px;
      width: 150px;
      overflow: hidden;
      position: relative;
    }

    .score-fill {
      background: linear-gradient(90deg, var(--gold) 0%, var(--dark-gold) 100%);
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      color: white;
      font-weight: 700;
    }

    .student-row {
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .student-row.expanded {
      background: rgba(196, 150, 58, 0.1);
    }

    .attempt-details {
      display: none;
      padding: 1.5rem;
      background: rgba(0, 0, 0, 0.3);
    }

    .attempt-details.show {
      display: block;
    }

    .attempt-item {
      padding: 0.8rem;
      background: rgba(196, 150, 58, 0.05);
      border-left: 3px solid var(--gold);
      margin-bottom: 0.8rem;
      border-radius: 4px;
    }

    .attempt-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .attempt-info {
      font-size: 0.9rem;
      color: var(--light-gray);
    }

    .empty-state {
      text-align: center;
      padding: 3rem 2rem;
      color: var(--light-gray);
    }

    .empty-icon {
      font-size: 80px;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    @media (max-width: 768px) {
      table {
        font-size: 0.9rem;
      }

      th, td {
        padding: 0.8rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Header -->
  <div class="header">
    <div>
      <h1><i class="ri-bar-chart-line"></i> نتائج الاختبار</h1>
      <p style="color: var(--light-gray); margin-top: 0.5rem;">{{ $exam->name }}</p>
    </div>
    <a href="{{ route('teacher.exams') }}" class="back-btn">
      <i class="ri-arrow-right-line"></i> العودة
    </a>
  </div>

  <!-- Statistics -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label"><i class="ri-user-line"></i> عدد الطلاب</div>
      <div class="stat-value">{{ $uniqueStudents }}</div>
      <div class="stat-desc">طالب أخذ الاختبار</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-repeat-line"></i> إجمالي المحاولات</div>
      <div class="stat-value">{{ $totalAttempts }}</div>
      <div class="stat-desc">محاولة</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-checkbox-circle-line"></i> النجاح</div>
      <div class="stat-value">{{ $passedAttempts }}</div>
      <div class="stat-desc">محاولة نجحت ({{ $passRate }}%)</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-star-line"></i> المتوسط</div>
      <div class="stat-value">{{ round($avgPercentage, 1) }}%</div>
      <div class="stat-desc">{{ round($avgScore) }} نقطة</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-trophy-line"></i> الأعلى درجة</div>
      <div class="stat-value">{{ $highestScore }}</div>
      <div class="stat-desc">نقطة</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-arrow-down-line"></i> الأدنى درجة</div>
      <div class="stat-value">{{ $lowestScore }}</div>
      <div class="stat-desc">نقطة</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-close-circle-line"></i> الرسوب</div>
      <div class="stat-value">{{ $failedAttempts }}</div>
      <div class="stat-desc">محاولة لم تنجح</div>
    </div>

    <div class="stat-card">
      <div class="stat-label"><i class="ri-time-line"></i> متوسط المدة</div>
      <div class="stat-value">{{ $avgDuration > 0 ? round($avgDuration / 60) : '-' }}</div>
      <div class="stat-desc">دقيقة</div>
    </div>
  </div>

  @if($totalAttempts > 0)
  <div class="table-container" style="padding: 1.5rem; margin-bottom: 2rem;">
    <h3 style="color: var(--gold); margin-bottom: 1.2rem; font-size: 1.1rem;">
      <i class="ri-bar-chart-2-line"></i> توزيع الدرجات
    </h3>
    <div style="display: flex; flex-direction: column; gap: 0.8rem;">
      @php
        $distMax = max(max($scoreDistribution), 1);
      @endphp
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="width: 80px; font-size: 0.85rem; color: var(--light-gray); text-align: left;">ممتاز (85%+)</span>
        <div style="flex: 1; background: rgba(6, 168, 125, 0.15); border-radius: 6px; height: 28px; overflow: hidden;">
          <div style="width: {{ ($scoreDistribution['excellent'] / $distMax) * 100 }}%; height: 100%; background: var(--success); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: white; font-weight: 700; min-width: {{ $scoreDistribution['excellent'] > 0 ? '30px' : '0' }};">
            {{ $scoreDistribution['excellent'] > 0 ? $scoreDistribution['excellent'] : '' }}
          </div>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="width: 80px; font-size: 0.85rem; color: var(--light-gray); text-align: left;">جيد (65-84%)</span>
        <div style="flex: 1; background: rgba(196, 150, 58, 0.15); border-radius: 6px; height: 28px; overflow: hidden;">
          <div style="width: {{ ($scoreDistribution['good'] / $distMax) * 100 }}%; height: 100%; background: var(--gold); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: white; font-weight: 700; min-width: {{ $scoreDistribution['good'] > 0 ? '30px' : '0' }};">
            {{ $scoreDistribution['good'] > 0 ? $scoreDistribution['good'] : '' }}
          </div>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="width: 80px; font-size: 0.85rem; color: var(--light-gray); text-align: left;">مقبول (50-64%)</span>
        <div style="flex: 1; background: rgba(245, 127, 0, 0.15); border-radius: 6px; height: 28px; overflow: hidden;">
          <div style="width: {{ ($scoreDistribution['average'] / $distMax) * 100 }}%; height: 100%; background: var(--warning); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: white; font-weight: 700; min-width: {{ $scoreDistribution['average'] > 0 ? '30px' : '0' }};">
            {{ $scoreDistribution['average'] > 0 ? $scoreDistribution['average'] : '' }}
          </div>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="width: 80px; font-size: 0.85rem; color: var(--light-gray); text-align: left;">ضعيف (&lt;50%)</span>
        <div style="flex: 1; background: rgba(211, 47, 47, 0.15); border-radius: 6px; height: 28px; overflow: hidden;">
          <div style="width: {{ ($scoreDistribution['poor'] / $distMax) * 100 }}%; height: 100%; background: var(--danger); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: white; font-weight: 700; min-width: {{ $scoreDistribution['poor'] > 0 ? '30px' : '0' }};">
            {{ $scoreDistribution['poor'] > 0 ? $scoreDistribution['poor'] : '' }}
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Results Table -->
  @if($uniqueStudents > 0)
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>اسم الطالب</th>
            <th>البريد الإلكتروني</th>
            <th>أفضل درجة</th>
            <th>النسبة المئوية</th>
            <th>المحاولات</th>
            <th>الحالة</th>
            <th>التفاصيل</th>
          </tr>
        </thead>
        <tbody>
          @foreach($studentStats as $studentId => $stats)
            <tr class="student-row" data-student-id="{{ $studentId }}">
              <td>
                <strong>{{ $stats['name'] }}</strong>
              </td>
              <td>{{ $stats['email'] }}</td>
              <td>
                <div class="score-bar">
                  <div class="score-fill" style="width: {{ ($stats['best_score'] / max($highestScore, 1) * 100) }}%">
                    {{ $stats['best_score'] }}
                  </div>
                </div>
              </td>
              <td>{{ number_format($stats['best_percentage'], 1) }}%</td>
              <td>{{ count($stats['attempts']) }}</td>
              <td>
                @if($stats['passed'])
                  <span class="badge badge-success">نجح</span>
                @elseif($stats['best_percentage'] >= 50)
                  <span class="badge badge-warning">قريب</span>
                @else
                  <span class="badge badge-danger">لم ينجح</span>
                @endif
              </td>
              <td style="text-align: center; cursor: pointer;">
                <i class="ri-expand-alt-line"></i>
              </td>
            </tr>
            <tr>
              <td colspan="7">
                <div class="attempt-details" id="details-{{ $studentId }}">
                  <strong style="color: var(--gold); display: block; margin-bottom: 1rem;">محاولات الاختبار:</strong>
                  @foreach($stats['attempts'] as $attempt)
                    <div class="attempt-item">
                      <div class="attempt-header">
                        <span>
                          <strong>المحاولة #{{ $attempt->attempt_number }}</strong>
                          @if($attempt->passed)
                            <span class="badge badge-success" style="margin-right: 0.5rem;">نجح</span>
                          @else
                            <span class="badge badge-danger" style="margin-right: 0.5rem;">لم ينجح</span>
                          @endif
                        </span>
                        <span style="color: var(--gold);">{{ $attempt->score }} نقطة ({{ number_format($attempt->percentage, 1) }}%)</span>
                      </div>
                      <div class="attempt-info">
                        <div>✓ إجابات صحيحة: {{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</div>
                        <div>⏰ قدم في: {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d/m/Y H:i') : '—' }}</div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="empty-state">
      <div class="empty-icon">ًں“­</div>
      <h3>لا توجد محاولات بعد</h3>
      <p>لم يقدم أي طالب هذا الاختبار حتى الآن</p>
    </div>
  @endif
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('tbody').addEventListener('click', function (e) {
      const row = e.target.closest('.student-row');
      if (!row) return;
      const studentId = row.dataset.studentId;
      if (!studentId) return;
      const details = document.getElementById('details-' + studentId);
      if (!details) return;
      details.classList.toggle('show');
      row.classList.toggle('expanded');
    });
  });
</script>

    @include('components.account-theme-foot')
</body>
</html>







