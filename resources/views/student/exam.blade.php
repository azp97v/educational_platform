<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $exam->title }} | اختبار تقييمي</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <style>
    * {
      --gold: #C4963A;
      --dark-gold: #A07A28;
      --light: #f6f3ee;
      --dark: #1a1a1a;
      --gray: #666666;
      --light-gray: #999999;
      --success: #06a77d;
      --danger: #D32F2F;
      --bg-dark: #050505;
      --bg-darker: #0F0F10;
      --card-bg: rgba(255, 255, 255, 0.04);
      --card-hover: rgba(255, 255, 255, 0.08);
      --border-color: rgba(196, 150, 58, 0.25);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: linear-gradient(135deg, var(--bg-dark) 0%, #191A1C 100%);
      color: white;
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    /* ANIMATIONS */
    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    textarea:focus {
      background: rgba(196, 150, 58, 0.1) !important;
      border-color: var(--gold) !important;
      outline: none;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes glow {
      0%, 100% { box-shadow: 0 0 10px rgba(196, 150, 58, 0.2); }
      50% { box-shadow: 0 0 20px rgba(196, 150, 58, 0.4); }
    }

    /* MAIN CONTAINER */
    .exam-container {
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 0;
      height: 100vh;
    }

    /* LEFT: QUESTIONS SECTION */
    .questions-section {
      display: flex;
      flex-direction: column;
      padding: 3rem 2.5rem;
      overflow-y: auto;
      height: 100%;
      animation: slideInLeft 0.7s ease-out;
    }

    /* HEADER */
    .exam-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1.5px solid var(--border-color);
    }

    .back-btn {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      color: var(--gold);
      text-decoration: none;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      transform: translateX(5px);
      opacity: 0.8;
    }

    .control-btn {
      background: none;
      border: 1px solid var(--border-color);
      color: var(--light-gray);
      width: 48px;
      height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.2rem;
      transition: all 0.3s ease;
    }

    .control-btn:hover {
      color: var(--gold);
      border-color: var(--gold);
      background: rgba(196, 150, 58, 0.1);
      transform: scale(1.05);
    }

    /* QUESTION CARD */
    .question-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      animation: slideUp 0.5s ease-out;
      backdrop-filter: blur(5px);
    }

    .question-card:hover {
      background: var(--card-hover);
      border-color: rgba(196, 150, 58, 0.4);
    }

    .question-number {
      font-size: 0.85rem;
      color: var(--gold);
      font-weight: 600;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .question-text {
      font-size: 1.15rem;
      font-weight: 700;
      margin-bottom: 2rem;
      color: var(--light);
      line-height: 1.6;
    }

    .options-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .option-label {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1.2rem;
      background: rgba(255, 255, 255, 0.02);
      border: 1.5px solid var(--border-color);
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .option-label:hover {
      background: rgba(196, 150, 58, 0.1);
      border-color: var(--gold);
      padding-right: 1.5rem;
    }

    .option-label.selected {
      background: rgba(196, 150, 58, 0.15);
      border-color: var(--gold);
      box-shadow: 0 0 15px rgba(196, 150, 58, 0.2);
    }

    .option-radio {
      width: 20px;
      height: 20px;
      border: 2px solid var(--border-color);
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }

    .option-label input[type="radio"] {
      display: none;
    }

    .option-label input[type="radio"]:checked + .option-radio {
      background: var(--gold);
      border-color: var(--gold);
      box-shadow: 0 0 10px var(--gold);
    }

    .option-label input[type="radio"]:checked + .option-radio::after {
      content: '✓';
      color: var(--dark);
      font-weight: 700;
    }

    .option-text {
      flex: 1;
      font-size: 0.95rem;
      font-weight: 500;
    }

    /* SUBMIT SECTION */
    .submit-section {
      display: flex;
      gap: 1.5rem;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 1px solid var(--border-color);
    }

    .btn-primary, .btn-secondary {
      flex: 1;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.35s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.7rem;
      border: none;
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
      border: 1.5px solid var(--border-color);
      color: white;
    }

    .btn-secondary:hover {
      border-color: var(--gold);
      background: rgba(196, 150, 58, 0.1);
    }

    /* RIGHT: SIDEBAR PANEL */
    .exam-sidebar {
      grid-column: 2;
      padding: 3rem 2.5rem;
      overflow-y: auto;
      height: 100vh;
      display: flex;
      flex-direction: column;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.3) 100%);
      animation: slideInRight 0.7s ease-out;
      backdrop-filter: blur(5px);
      border-left: 1px solid var(--border-color);
    }

    .info-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      animation: fadeIn 0.6s ease-out;
      backdrop-filter: blur(5px);
    }

    .info-card:hover {
      background: var(--card-hover);
    }

    .info-title {
      font-size: 0.9rem;
      color: var(--light-gray);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .info-value {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--gold);
      font-family: 'Josefin Slab', serif;
    }

    .info-subtitle {
      font-size: 0.85rem;
      color: var(--light-gray);
      margin-top: 0.5rem;
    }

    .timer-card {
      background: linear-gradient(135deg, rgba(211, 47, 47, 0.15) 0%, rgba(198, 40, 40, 0.1) 100%);
      border: 1px solid rgba(211, 47, 47, 0.3);
    }

    .timer-value {
      color: #ff6b6b;
    }

    .progress-text {
      font-size: 0.9rem;
      color: var(--light-gray);
      margin-top: 1rem;
      text-align: center;
    }

    /* EMPTY STATE */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 4rem 2rem;
      text-align: center;
    }

    .empty-icon {
      font-size: 80px;
      margin-bottom: 1rem;
      opacity: 0.5;
      animation: glow 2.5s ease-in-out infinite;
    }

    .empty-text {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .empty-desc {
      font-size: 0.95rem;
      color: var(--light-gray);
    }

    /* SCROLLBAR STYLING */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: rgba(196, 150, 58, 0.05);
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(196, 150, 58, 0.3);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(196, 150, 58, 0.6);
    }

    /* LIGHT MODE */
    html.light-mode {
      --bg-dark: #f6f3ee;
      --bg-darker: #ede9e0;
      --dark: #1a1a1a;
      --text-color: #1a1a1a;
      --card-bg: rgba(255, 255, 255, 0.7);
      --card-hover: rgba(255, 255, 255, 0.9);
      --border-color: rgba(196, 150, 58, 0.25);
      --light-gray: #555555;
      --light: #1a1a1a;
    }

    html.light-mode body {
      background: linear-gradient(135deg, #f6f3ee 0%, #ede9e0 100%);
      color: #1a1a1a;
    }

    html.light-mode .question-text,
    html.light-mode .option-text,
    html.light-mode .question-number {
      color: #1a1a1a;
    }

    html.light-mode .question-number {
      color: var(--gold);
    }

    html.light-mode .option-label {
      background: rgba(255, 255, 255, 0.6);
      border-color: rgba(0, 0, 0, 0.12);
    }

    html.light-mode .option-label:hover {
      background: rgba(196, 150, 58, 0.1);
      border-color: var(--gold);
    }

    html.light-mode .option-label.selected {
      background: rgba(196, 150, 58, 0.12);
      border-color: var(--gold);
    }

    html.light-mode .option-radio {
      border-color: rgba(0, 0, 0, 0.2);
    }

    html.light-mode .question-card {
      background: rgba(255, 255, 255, 0.6);
      border-color: rgba(196, 150, 58, 0.2);
    }

    html.light-mode .question-card:hover {
      background: rgba(255, 255, 255, 0.85);
    }

    html.light-mode .exam-sidebar {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0.3) 100%);
      border-left-color: rgba(196, 150, 58, 0.2);
    }

    html.light-mode .info-card {
      background: rgba(255, 255, 255, 0.6);
      border-color: rgba(196, 150, 58, 0.2);
    }

    html.light-mode .info-card:hover {
      background: rgba(255, 255, 255, 0.85);
    }

    html.light-mode .info-title {
      color: #555555;
    }

    html.light-mode .info-subtitle {
      color: #666666;
    }

    html.light-mode .back-btn {
      color: var(--gold);
    }

    html.light-mode .control-btn {
      border-color: rgba(0, 0, 0, 0.12);
      color: #555555;
    }

    html.light-mode .btn-secondary {
      border-color: rgba(0, 0, 0, 0.15);
      color: #1a1a1a;
    }

    html.light-mode textarea {
      background: rgba(255, 255, 255, 0.6);
      border-color: rgba(0, 0, 0, 0.12);
      color: #1a1a1a;
    }

    html.light-mode .exam-header {
      border-bottom-color: rgba(196, 150, 58, 0.2);
    }

    html.light-mode .submit-section {
      border-top-color: rgba(196, 150, 58, 0.2);
    }

    html.light-mode ::-webkit-scrollbar-track {
      background: rgba(196, 150, 58, 0.08);
    }

    html.light-mode ::-webkit-scrollbar-thumb {
      background: rgba(196, 150, 58, 0.35);
    }

    html[data-theme="light"] body {
      background: linear-gradient(135deg, #f6f3ee 0%, #ede9e0 100%);
      color: #222B3D;
    }

    html[data-theme="light"] .question-card {
      background: rgba(255, 255, 255, 0.8);
      border-color: rgba(196, 150, 58, 0.2);
    }

    html[data-theme="light"] .question-card:hover {
      background: rgba(255, 255, 255, 0.95);
    }

    html[data-theme="light"] .exam-sidebar {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0.5) 100%);
      border-left-color: rgba(196, 150, 58, 0.2);
    }

    html[data-theme="light"] .info-card {
      background: rgba(255, 255, 255, 0.7);
      border-color: rgba(196, 150, 58, 0.2);
    }

    html[data-theme="light"] .info-card:hover {
      background: rgba(255, 255, 255, 0.9);
    }

    html[data-theme="light"] .question-text,
    html[data-theme="light"] .option-text,
    html[data-theme="light"] .info-title {
      color: #222B3D;
    }

    html[data-theme="light"] .option-label {
      background: rgba(255, 255, 255, 0.6);
      border-color: rgba(0, 0, 0, 0.12);
      color: #222B3D;
    }

    html[data-theme="light"] .option-label:hover {
      background: rgba(196, 150, 58, 0.1);
      border-color: var(--gold);
    }

    html[data-theme="light"] .option-label.selected {
      background: rgba(196, 150, 58, 0.12);
      border-color: var(--gold);
    }

    html[data-theme="light"] .option-radio {
      border-color: rgba(0, 0, 0, 0.2);
    }

    html[data-theme="light"] .info-subtitle,
    html[data-theme="light"] .light-gray,
    html[data-theme="light"] .back-btn {
      color: #5E6675;
    }

    html[data-theme="light"] .control-btn {
      border-color: rgba(0, 0, 0, 0.12);
      color: #5E6675;
    }

    html[data-theme="light"] .btn-secondary {
      border-color: rgba(0, 0, 0, 0.15);
      color: #222B3D;
    }

    html[data-theme="light"] textarea {
      background: rgba(255, 255, 255, 0.6);
      border-color: rgba(0, 0, 0, 0.12);
      color: #222B3D;
    }

    html[data-theme="light"] .exam-header {
      border-bottom-color: rgba(196, 150, 58, 0.2);
    }

    html[data-theme="light"] .submit-section {
      border-top-color: rgba(196, 150, 58, 0.2);
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
      .exam-container {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto;
      }

      .questions-section {
        padding: 2rem;
      }

      .exam-sidebar {
        grid-column: 1;
        height: auto;
        max-height: 400px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        border-left: none;
        border-top: 1px solid var(--border-color);
      }

      .info-card {
        margin-bottom: 0;
      }
    }

    @media (max-width: 768px) {
      .exam-container {
        grid-template-columns: 1fr;
      }

      .questions-section {
        padding: 1.5rem 1rem;
      }

      .exam-sidebar {
        grid-template-columns: 1fr 1fr;
        padding: 1.5rem 1rem;
      }

      .question-card {
        padding: 1.5rem;
      }

      .submit-section {
        flex-direction: column;
      }
    }

    @media (max-width: 480px) {
      .questions-section { padding: 1rem; }
      .exam-sidebar { grid-template-columns: repeat(3, 1fr); padding: 1rem; }
      .question-card { padding: 1rem; }
      .exam-header { padding: 0.8rem 1rem; }
    }
  </style>
</head>
<body>

<div class="exam-container">
  <!-- LEFT: QUESTIONS SECTION -->
  <div class="questions-section">
    <!-- Header -->
    <div class="exam-header">
      <a href="{{ route('student.exams') }}" class="back-btn">
        <i class="ri-arrow-right-line"></i> العودة للاختبارات
      </a>
      <div style="display: flex; gap: 1rem;">
        <button class="control-btn" title="تبديل الثيم" id="themeToggle"><i class="ri-moon-line"></i></button>
      </div>
    </div>

    <!-- Exam Title -->
    <div style="margin-bottom: 3rem;">
      <h1 style="font-family: 'Josefin Slab', serif; font-size: 2rem; font-weight: 700; color: var(--light); letter-spacing: 0.5px; margin-bottom: 0.5rem;">
        {{ $exam->title }}
      </h1>
      <p style="color: var(--light-gray); font-size: 0.95rem; line-height: 1.6;">
        {{ $exam->description ?? 'اختبار تقييمي شامل' }}
      </p>
    </div>

    <!-- Questions Form -->
    <form method="POST" action="{{ route('student.exam.submit', $exam->id) }}" id="examForm">
      @csrf

      @forelse($questions as $index => $question)
        <div class="question-card">
          <div class="question-number">
            <i class="ri-survey-line" style="margin-left: 0.5rem;"></i>السؤال {{ $index + 1 }} من {{ $questions->count() }}
          </div>

          <div class="question-text">
            {{ $question->question_text ?? $question->question }}
          </div>

          @if($question->question_type === 'short_answer')
            <!-- حقل الإجابة القصيرة -->
            <div style="margin-top: 2rem;">
              <textarea 
                name="answers[{{ $question->id }}]" 
                placeholder="اكتب إجابتك هنا بعناية..."
                required
                rows="4"
                style="
                  width: 100%;
                  padding: 1rem 1.5rem;
                  background: rgba(255, 255, 255, 0.02);
                  border: 1.5px solid var(--border-color);
                  border-radius: 10px;
                  color: inherit;
                  font-family: 'Cairo', sans-serif;
                  font-size: 0.95rem;
                  transition: all 0.3s ease;
                  resize: vertical;
                "

              ></textarea>
              <div style="color: var(--light-gray); font-size: 0.8rem; margin-top: 0.7rem;">
                <i class="ri-information-line"></i> اكتب إجابتك بعناية - ستُقيّم إجابتك من قبل المعلم
              </div>
            </div>
          @else
            <!-- خيارات متعددة -->
            <div class="options-group">
              @php
                // الخيارات تأتي من علاقة answers - مجموعة Eloquent
                $answersList = $question->answers ?? collect([]);
                
                // إذا كانت فارغة، حاول JSON
                if ($answersList->isEmpty() && !empty($question->answers_json)) {
                  $decoded = json_decode($question->answers_json, true);
                  if (is_array($decoded)) {
                    $answersList = collect($decoded);
                  }
                }
              @endphp

              @forelse($answersList as $answerIndex => $answer)
                @php
                  // التعامل مع Answer model
                  if (is_object($answer)) {
                    $answerText = $answer->answer_text ?? '';
                    $answerId = $answer->id ?? $answerIndex;
                  } else {
                    // نص مباشر (في حالة JSON)
                    $answerText = $answer ?? '';
                    $answerId = $answerIndex;
                  }
                @endphp
                
                @if(!empty(trim($answerText)))
                  <label class="option-label">
                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answerId }}" required>
                    <div class="option-radio"></div>
                    <span class="option-text">{{ $answerText }}</span>
                  </label>
                @endif
              @empty
                <div style="color: var(--light-gray); font-size: 0.9rem; padding: 1rem; text-align: center;">
                  ⚠️ لا توجد خيارات متاحة لهذا السؤال
                </div>
              @endforelse
            </div>
          @endif
        </div>
      @empty
        <div class="empty-state">
          <div class="empty-icon">📋</div>
          <div class="empty-text">لا توجد أسئلة</div>
          <div class="empty-desc">لم يتم إضافة أسئلة لهذا الاختبار بعد</div>
        </div>
      @endforelse

      @if($questions->count() > 0)
        <div class="submit-section">
          <button type="submit" class="btn-primary">
            <i class="ri-check-double-line"></i> إرسال الإجابات
          </button>
          <a href="{{ route('student.exams') }}" class="btn-secondary" style="text-decoration: none;">
            <i class="ri-close-line"></i> إلغاء
          </a>
        </div>
      @endif
    </form>
  </div>

  <!-- RIGHT: SIDEBAR PANEL -->
  <div class="exam-sidebar">
    <!-- Exam Info Card -->
    <div class="info-card timer-card">
      <div class="info-title"><i class="ri-time-line"></i> المدة المتاحة</div>
      <div class="timer-value" id="timerDisplay">
        <span id="timerMinutes">{{ $exam->duration ?? 30 }}</span>:<span id="timerSeconds">00</span>
      </div>
      <div class="info-subtitle">دقيقة</div>
    </div>

    <!-- Questions Count -->
    <div class="info-card">
      <div class="info-title"><i class="ri-survey-line"></i> عدد الأسئلة</div>
      <div class="info-value">{{ $questions->count() ?? 0 }}</div>
      <div class="info-subtitle">سؤال تقييمي</div>
    </div>

    <!-- Progress -->
    <div class="info-card">
      <div class="info-title"><i class="ri-checkbox-circle-line"></i> تقدمك</div>
      <div class="progress-text">
        <span id="answeredCount">0</span> من <span id="totalCount">{{ $questions->count() }}</span> سؤال
      </div>
      <div style="width: 100%; height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 3px; margin-top: 1rem; overflow: hidden;">
        <div id="progressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, var(--gold) 0%, var(--dark-gold) 100%); transition: width 0.3s ease; border-radius: 3px;"></div>
      </div>
    </div>
  </div>
</div>

<script>
  let isDarkMode = (document.documentElement.getAttribute('data-theme') !== 'light');

  // Track answered questions
  function updateProgress() {
    let answered = 0;
    const totalQuestions = parseInt(document.getElementById('totalCount').innerText);
    
    // عد الخيارات المختارة (الأسئلة المتعددة الخيارات)
    const selectedRadios = document.querySelectorAll('.option-label input[type="radio"]:checked').length;
    answered += selectedRadios;
    
    // عد حقول النص المملوءة (الأسئلة القصيرة)
    const filledTextareas = Array.from(document.querySelectorAll('textarea[name^="answers"]')).filter(ta => ta.value.trim().length > 0).length;
    answered += filledTextareas;
    
    document.getElementById('answeredCount').innerText = answered;
    document.getElementById('progressBar').style.width = (answered / totalQuestions * 100) + '%';
  }

  // التحقق من أن جميع الأسئلة قد تم الإجابة عليها
  function validateAllAnswered() {
    const totalQuestions = parseInt(document.getElementById('totalCount').innerText);
    
    // عد الخيارات المختارة (الأسئلة المتعددة الخيارات)
    const selectedRadios = document.querySelectorAll('.option-label input[type="radio"]:checked').length;
    
    // عد حقول النص المملوءة (الأسئلة القصيرة)
    const filledTextareas = Array.from(document.querySelectorAll('textarea[name^="answers"]')).filter(ta => ta.value.trim().length > 0).length;
    
    const answered = selectedRadios + filledTextareas;
    
    if (answered !== totalQuestions) {
      alert(`⚠️ يجب الإجابة على جميع الأسئلة\n\nالأسئلة المجابة: ${answered} من ${totalQuestions}\n\n⏳ تأكد من ملء جميع الإجابات!`);
      return false;
    }
    return true;
  }

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    loadThemePreference();

    // Theme toggle
    document.getElementById('themeToggle').addEventListener('click', toggleDark);

    // Textarea change tracking for progress
    document.querySelectorAll('textarea[name^="answers"]').forEach(ta => {
      ta.addEventListener('change', updateProgress);
      ta.addEventListener('input', updateProgress);
    });

    // Radio button interaction
    document.querySelectorAll('.option-label input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', () => {
        const siblings = document.querySelectorAll(`[name="${radio.name}"]`);
        siblings.forEach(r => r.closest('.option-label').classList.remove('selected'));
        radio.closest('.option-label').classList.add('selected');
        updateProgress();
      });
    });

    // Prevent form submission if not all questions are answered
    let timerExpired = false;

    document.getElementById('examForm').addEventListener('submit', function(e) {
      if (!timerExpired && !validateAllAnswered()) {
        e.preventDefault();
      }
    });

    // Timer initialization with localStorage persistence
    const examId = "{{ $exam->id }}";
    const duration = parseInt("{{ $exam->duration ?? 30 }}") * 60;
    let startTime = localStorage.getItem('exam_' + examId + '_start_time');
    let remaining;

    if (!startTime) {
      startTime = Math.floor(Date.now() / 1000);
      localStorage.setItem('exam_' + examId + '_start_time', startTime);
      remaining = duration;
    } else {
      const elapsed = Math.floor(Date.now() / 1000) - parseInt(startTime);
      remaining = Math.max(0, duration - elapsed);
    }

    const timerMinutes = document.getElementById('timerMinutes');
    const timerSeconds = document.getElementById('timerSeconds');
    const timerCard = document.querySelector('.timer-card');

    function updateTimerDisplay() {
      const minutes = Math.floor(remaining / 60);
      const seconds = remaining % 60;
      timerMinutes.innerText = minutes;
      timerSeconds.innerText = (seconds < 10 ? '0' : '') + seconds;
    }

    updateTimerDisplay();

    const timerInterval = setInterval(function() {
      if (remaining > 0) {
        remaining--;
        updateTimerDisplay();

        if (remaining <= 300 && timerCard) {
          timerCard.style.animation = 'glow 1s ease-in-out infinite';
        }

        if (remaining === 0) {
          clearInterval(timerInterval);
          timerExpired = true;
          alert('انتهى الوقت! سيتم إرسال إجاباتك الآن.');
          document.getElementById('examForm').submit();
        }
      }
    }, 1000);

    updateProgress();
  });

  function toggleDark() {
    isDarkMode = !isDarkMode;
    const html = document.documentElement;

    if (isDarkMode) {
      html.classList.remove('light-mode');
      html.setAttribute('data-theme', 'dark');
      document.getElementById('themeToggle').querySelector('i').className = 'ri-moon-line';
    } else {
      html.classList.add('light-mode');
      html.setAttribute('data-theme', 'light');
      document.getElementById('themeToggle').querySelector('i').className = 'ri-sun-line';
    }

    localStorage.setItem('examTheme', isDarkMode ? 'dark' : 'light');
    localStorage.setItem('app-theme', isDarkMode ? 'dark' : 'light');
    localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
  }

  function loadThemePreference() {
    const saved = localStorage.getItem('app-theme') || localStorage.getItem('examTheme');
    if (saved === 'light') {
      isDarkMode = false;
      document.documentElement.classList.add('light-mode');
      document.documentElement.setAttribute('data-theme', 'light');
      document.getElementById('themeToggle').querySelector('i').className = 'ri-sun-line';
    } else if (saved === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  }
</script>

    @include('components.account-theme-foot')
</body>
</html>



