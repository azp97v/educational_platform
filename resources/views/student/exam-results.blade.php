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
      --light: #f6f3ee;
      --dark: #1a1a1a;
      --light-gray: #999999;
      --success: #06a77d;
      --danger: #D32F2F;
      --bg-dark: #0a0e27;
      --bg-darker: #05071a;
      --card-bg: rgba(255, 255, 255, 0.04);
      --card-hover: rgba(255, 255, 255, 0.08);
      --border-color: rgba(196, 150, 58, 0.25);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: linear-gradient(135deg, var(--bg-dark) 0%, #16213e 100%);
      color: white;
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    .result-container {
      max-width: 900px;
      margin: 3rem auto;
      padding: 2rem;
      animation: slideUp 0.7s ease-out;
    }

    .result-header {
      text-align: center;
      margin-bottom: 3rem;
    }

    .result-icon {
      font-size: 100px;
      margin-bottom: 1rem;
      animation: pulse 2s infinite;
    }

    .result-title {
      font-family: 'Josefin Slab', serif;
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: var(--gold);
    }

    .result-subtitle {
      font-size: 1.1rem;
      color: var(--light-gray);
    }

    .result-stats {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    @media (max-width: 600px) {
      .result-stats {
        grid-template-columns: 1fr;
      }
    }

    .stat-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 15px;
      padding: 2rem;
      text-align: center;
      backdrop-filter: blur(5px);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      background: var(--card-hover);
      transform: translateY(-5px);
    }

    .stat-label {
      font-size: 0.9rem;
      color: var(--light-gray);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 0.8rem;
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--gold);
      font-family: 'Josefin Slab', serif;
    }

    .stat-unit {
      font-size: 0.9rem;
      color: var(--light-gray);
      margin-top: 0.3rem;
    }

    .progress-ring {
      width: 150px;
      height: 150px;
      margin: 0 auto;
      position: relative;
    }

    .progress-ring-circle {
      transition: stroke-dashoffset 0.5s ease;
      transform: rotate(-90deg);
      transform-origin: 50% 50%;
      stroke: var(--gold);
      fill: none;
      stroke-width: 8;
      stroke-dasharray: 471.2;
    }

    .progress-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      color: var(--gold);
    }

    .result-details {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 3rem;
      backdrop-filter: blur(5px);
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.2rem 0;
      border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      font-weight: 600;
      color: var(--light-gray);
    }

    .detail-value {
      font-size: 1.1rem;
      color: var(--light);
    }

    .badge {
      display: inline-block;
      padding: 0.5rem 1.2rem;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .badge-success {
      background: rgba(6, 168, 125, 0.2);
      color: var(--success);
      border: 1px solid var(--success);
    }

    .badge-danger {
      background: rgba(211, 47, 47, 0.2);
      color: var(--danger);
      border: 1px solid var(--danger);
    }

    .action-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 1rem 2rem;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.7rem;
      border: none;
      text-decoration: none;
    }

    .btn-primary {
      background: linear-gradient(90deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(196, 150, 58, 0.35);
    }

    .btn-secondary {
      background: transparent;
      border: 2px solid var(--gold);
      color: var(--gold);
    }

    .btn-secondary:hover {
      background: rgba(196, 150, 58, 0.1);
    }

    .light-mode {
      --bg-dark: #f6f3ee;
      --bg-darker: #ede9e0;
      --dark: #1a1a1a;
      --card-bg: rgba(0, 0, 0, 0.02);
      --card-hover: rgba(0, 0, 0, 0.05);
      --border-color: rgba(0, 0, 0, 0.1);
      --light-gray: #666666;
    }

    html.light-mode body {
      background: linear-gradient(135deg, #f6f3ee 0%, #ede9e0 100%);
      color: #1a1a1a;
    }

    html.light-mode .stat-value,
    html.light-mode .result-title,
    html.light-mode .progress-text {
      color: var(--gold);
    }

    @media (max-width: 768px) {
      .result-container { padding: 1rem; margin: 1rem auto; }
      .result-title { font-size: 1.8rem; }
      .result-icon { font-size: 70px; }
    }
    @media (max-width: 480px) {
      .result-title { font-size: 1.4rem; }
      .stat-card { padding: 1rem; }
      .stat-value { font-size: 1.8rem; }
    }
  </style>
</head>
<body>

<div class="result-container">
  <!-- Header -->
  <div class="result-header">
    <div class="result-icon">
      @if($attempt->passed)
        ✨
      @else
        ⚠️
      @endif
    </div>
    
    <h1 class="result-title">
      @if($attempt->passed)
        أحسنت! لقد نجحت 🎉
      @else
        لم تنجح في هذه المرة 😔
      @endif
    </h1>
    
    <p class="result-subtitle">
      {{$exam->name}}
      @if($attempt->percentage >= 90)
        <span class="badge badge-success" style="margin-right: 1rem;">ممتاز!</span>
      @elseif($attempt->percentage >= 70)
        <span class="badge badge-success" style="margin-right: 1rem;">جيد</span>
      @else
        <span class="badge badge-danger" style="margin-right: 1rem;">حاول مرة أخرى</span>
      @endif
    </p>
  </div>

  <!-- Stats -->
  <div class="result-stats">
    <!-- Score Card with Progress Ring -->
    <div class="stat-card">
      <div class="stat-label">النتيجة</div>
      <div class="progress-ring">
        <svg width="150" height="150" viewBox="0 0 150 150">
          <circle cx="75" cy="75" r="75" fill="none" stroke="var(--border-color)" stroke-width="8"/>
          <circle class="progress-ring-circle" cx="75" cy="75" r="75" style="stroke-dashoffset: {{ 471.2 - (471.2 * $attempt->percentage / 100) }};"/>
        </svg>
        <div class="progress-text">{{ number_format($attempt->percentage, 1) }}%</div>
      </div>
    </div>

    <!-- Correct Answers -->
    <div class="stat-card">
      <div class="stat-label"><i class="ri-check-double-line"></i> الإجابات الصحيحة</div>
      <div class="stat-value">{{ $attempt->correct_answers }}</div>
      <div class="stat-unit">من {{ $attempt->total_questions }} أسئلة</div>
    </div>

    <!-- Score -->
    <div class="stat-card">
      <div class="stat-label"><i class="ri-star-line"></i> النقاط المكتسبة</div>
      <div class="stat-value">{{ $attempt->score }}</div>
      <div class="stat-unit">نقطة</div>
    </div>

    <!-- Attempt Number -->
    <div class="stat-card">
      <div class="stat-label"><i class="ri-repeat-line"></i> محاولة رقم</div>
      <div class="stat-value">{{ $attempt->attempt_number }}</div>
      <div class="stat-unit">@if($exam->attempts_allowed)من {{ $exam->attempts_allowed }}@endif</div>
    </div>
  </div>

  <!-- Details -->
  <div class="result-details">
    <div class="detail-row">
      <span class="detail-label">📊 نسبتك المئوية:</span>
      <span class="detail-value">{{ number_format($attempt->percentage, 2) }}%</span>
    </div>
    
    <div class="detail-row">
      <span class="detail-label">✅ الحد الأدنى للنجاح:</span>
      <span class="detail-value">{{ $exam->passing_score ?? 70 }}%</span>
    </div>
    
    <div class="detail-row">
      <span class="detail-label">⏰ وقت الانتهاء:</span>
      <span class="detail-value">{{ $attempt->submitted_at->format('d/m/Y H:i') }}</span>
    </div>
    
    @if($attempt->passed)
      <div class="detail-row">
        <span class="detail-label">🎓 الحالة:</span>
        <span class="badge badge-success">نجحت بتفوق!</span>
      </div>
    @else
      <div class="detail-row">
        <span class="detail-label">❌ الحالة:</span>
        <span class="badge badge-danger">لم تنجح</span>
      </div>
    @endif

    @if($exam->attempts_allowed && $attempt->attempt_number < $exam->attempts_allowed && !$attempt->passed)
      <div class="detail-row">
        <span class="detail-label">📝 محاولات متبقية:</span>
        <span class="detail-value" style="color: var(--gold);">{{ $exam->attempts_allowed - $attempt->attempt_number }}</span>
      </div>
    @endif
  </div>

  <!-- Action Buttons -->
  <div class="action-buttons">
    @if(!$attempt->passed && $exam->attempts_allowed && $attempt->attempt_number < $exam->attempts_allowed)
      <a href="{{ route('student.exam.show', $exam->id) }}" class="btn btn-primary">
        <i class="ri-restart-line"></i> حاول مرة أخرى
      </a>
    @endif
    
    <a href="{{ route('student.exams') }}" class="btn btn-secondary">
      <i class="ri-arrow-left-line"></i> العودة للاختبارات
    </a>

    <a href="{{ route('student.index') }}" class="btn btn-secondary">
      <i class="ri-home-line"></i> الرئيسية
    </a>
  </div>

  <!-- Feedback Section -->
  @if($exam->instructions)
    <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 15px; padding: 2rem; margin-top: 3rem; backdrop-filter: blur(5px);">
      <h3 style="color: var(--gold); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.7rem;">
        <i class="ri-information-line"></i> ملاحظات المعلم
      </h3>
      <p style="color: var(--light-gray); line-height: 1.8;">
        {{ $exam->instructions }}
      </p>
    </div>
  @endif
</div>

<script>
  // Theme toggle
  const savedTheme = localStorage.getItem('examTheme');
  if (savedTheme === 'light') {
    document.documentElement.classList.add('light-mode');
  }
</script>

    @include('components.account-theme-foot')
</body>
</html>



