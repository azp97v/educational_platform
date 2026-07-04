<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المتنافسين | جمعية إجلال</title>
  <meta name="description" content="منصة إجلال التعليمية - قائمة المتنافسين والترتيب">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * {
      --gold: var(--theme-gold);
      --gold-light: rgba(198, 166, 117, 0.38);
      --gold-dark: var(--theme-gold-dark);
      --primary: var(--theme-page-bg);
      --secondary: var(--theme-surface);
      --accent: var(--theme-success);
      --danger: var(--theme-danger);
      --warning: var(--theme-pending);
      --text-primary: var(--theme-text);
      --text-secondary: var(--theme-text-soft);
      --text-tertiary: var(--theme-muted);
      --border-strong: var(--theme-border-strong);
      --border-light: var(--theme-border-light);
      --surface-1: var(--theme-surface);
      --surface-2: var(--theme-surface-2);
      --surface-3: rgba(198, 166, 117, 0.1);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: var(--theme-page-bg);
      color: var(--theme-text);
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(circle at 8% 10%, rgba(198, 166, 117, 0.24) 0%, transparent 34%),
        radial-gradient(circle at 82% 76%, rgba(198, 166, 117, 0.14) 0%, transparent 38%);
      pointer-events: none;
      z-index: 0;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 0 15px rgba(196, 150, 58, 0.2); }
      50% { box-shadow: 0 0 30px rgba(196, 150, 58, 0.4); }
    }

    @keyframes bounceIn {
      0% { opacity: 0; transform: scale(0.3); }
      50% { opacity: 1; transform: scale(1.05); }
      70% { transform: scale(0.9); }
      100% { opacity: 1; transform: scale(1); }
    }

    .container {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .topbar,
    .content {
      position: relative;
      z-index: 1;
    }

    /* ===== TOPBAR ===== */
    .topbar {
      background: linear-gradient(90deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--theme-border);
      padding: 1.2rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 2rem;
      animation: slideUp 0.6s ease-out;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }

    .back-btn {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      padding: 1rem 1.5rem;
      background: var(--theme-gold-soft);
      border: 1px solid var(--theme-border-strong);
      border-radius: 16px;
      color: var(--gold);
      text-decoration: none;
      font-weight: 700;
      font-size: 0.95rem;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(196, 150, 58, 0.2);
    }

    .back-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(196, 150, 58, 0.1), transparent);
      transition: left 0.5s ease;
    }

    .back-btn:hover {
      background: rgba(198, 166, 117, 0.22);
      border-color: var(--gold);
      color: var(--theme-gold);
      transform: translateX(-5px) scale(1.02);
      box-shadow: 0 8px 25px rgba(196, 150, 58, 0.3);
    }

    .back-btn:hover::before {
      left: 100%;
    }

    .back-btn i {
      font-size: 1.3rem;
      transition: transform 0.3s ease;
    }

    .back-btn:hover i {
      transform: translateX(-3px);
    }

    .icon-btn {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      border: 1px solid var(--theme-border);
      background: var(--theme-surface-2);
      color: var(--gold);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      transition: all 0.3s ease;
    }

    .icon-btn:hover {
      background: var(--theme-gold-soft);
      border-color: var(--gold);
      transform: translateY(-3px);
    }

    /* ===== NOTIFICATION STYLES ===== */
    .notification-wrapper {
      position: relative;
    }

    .notification-btn {
      position: relative;
    }

    .notification-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: var(--danger);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      font-weight: 700;
      border: 2px solid var(--primary);
    }

    .notification-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      width: 380px;
      max-height: 500px;
      background: linear-gradient(135deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      border: 1px solid var(--theme-border);
      border-radius: 16px;
      backdrop-filter: blur(20px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      margin-top: 1rem;
    }

    .notification-dropdown.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid var(--theme-border);
    }

    .dropdown-header h4 {
      color: var(--gold);
      font-size: 1.1rem;
      font-weight: 700;
      margin: 0;
    }

    .btn-close {
      background: none;
      border: none;
      color: var(--text-secondary);
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .btn-close:hover {
      background: var(--surface-2);
      color: var(--text-primary);
    }

    .notification-list {
      max-height: 350px;
      overflow-y: auto;
    }

    .notification-item {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--theme-border);
      text-decoration: none;
      color: var(--text-primary);
      transition: all 0.3s ease;
    }

    .notification-item:hover {
      background: var(--surface-2);
    }

    .notification-item.unread {
      background: rgba(198, 166, 117, 0.14);
      border-right: 3px solid var(--gold);
    }

    .notification-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--surface-2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .notification-details {
      flex: 1;
      min-width: 0;
    }

    .notification-title {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
    }

    .notification-text {
      font-size: 0.8rem;
      color: var(--text-secondary);
      margin-bottom: 0.25rem;
      line-height: 1.4;
    }

    .notification-time {
      font-size: 0.75rem;
      color: var(--text-tertiary);
    }

    .dropdown-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid var(--border-light);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem 1.5rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.9rem;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
      border: none;
    }

    .btn-secondary {
      background: var(--surface-2);
      color: var(--text-primary);
      border: 1.5px solid var(--border-light);
    }

    .btn-secondary:hover {
      background: var(--surface-3);
      border-color: var(--gold);
      color: var(--gold);
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.8rem;
    }

    .w-100 {
      width: 100%;
    }

    .empty-message {
      text-align: center;
      padding: 2rem;
      color: var(--text-secondary);
      font-style: italic;
    }

    /* ===== CONTENT AREA ===== */
    .content {
      flex: 1;
      padding: 2rem;
      padding-bottom: 3rem;
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      align-items: start;
    }

    @media (max-width: 1024px) {
      .content {
        grid-template-columns: 1fr;
        gap: 2rem;
      }
    }

    /* ===== CUSTOM SCROLLBAR ===== */
    .content::-webkit-scrollbar {
      width: 12px;
    }

    .content::-webkit-scrollbar-track {
      background: linear-gradient(180deg, rgba(10, 14, 39, 0.3) 0%, rgba(22, 33, 62, 0.3) 100%);
      border-radius: 10px;
      margin: 5px;
    }

    .content::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, var(--gold) 0%, var(--gold-dark) 50%, var(--gold) 100%);
      border-radius: 10px;
      border: 2px solid rgba(10, 14, 39, 0.5);
      transition: all 0.3s ease;
      box-shadow: inset 0 0 5px rgba(196, 150, 58, 0.3);
    }

    .content::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, var(--gold-light) 0%, var(--gold) 50%, var(--gold-light) 100%);
      box-shadow: 0 0 15px rgba(196, 150, 58, 0.6), inset 0 0 8px rgba(196, 150, 58, 0.4);
      transform: scale(1.1);
    }

    .content::-webkit-scrollbar-corner {
      background: transparent;
    }

    /* ===== PAGE HEADER ===== */
    .page-header {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 3rem;
      animation: fadeInUp 0.6s ease-out;
    }

    .page-icon {
      width: 80px;
      height: 80px;
      border-radius: 20px;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.2) 0%, rgba(196, 150, 58, 0.1) 100%);
      border: 2px solid var(--gold);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 2.5rem;
      animation: pulse-glow 2s infinite;
    }

    .page-title-group h1 {
      font-size: 2.5rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .page-subtitle {
      font-size: 1.1rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    /* ===== LEADERBOARD CARD ===== */
    .leaderboard-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.05) 100%);
      border: 1.5px solid var(--border-light);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 3rem;
      animation: fadeInUp 0.7s ease-out;
    }

    .leaderboard-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
    }

    .leaderboard-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--gold);
      margin: 0;
    }

    .leaderboard-list {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .leaderboard-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: var(--surface-1);
      border: 1px solid var(--border-light);
      border-radius: 12px;
      transition: all 0.3s ease;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.6s ease-out forwards;
    }

    .leaderboard-item:hover {
      background: var(--surface-2);
      border-color: var(--gold);
      transform: translateX(-5px);
    }

    .rank {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .rank-1 {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
    }

    .rank-2 {
      background: linear-gradient(135deg, #c0c0c0 0%, #a0a0a0 100%);
      color: white;
    }

    .rank-3 {
      background: linear-gradient(135deg, #CD7F32 0%, #a0522d 100%);
      color: white;
    }

    .rank-other {
      background: var(--surface-2);
      color: var(--text-secondary);
      border: 1px solid var(--border-light);
    }

    .student-info {
      flex: 1;
    }

    .student-name {
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
    }

    .student-points {
      color: var(--gold);
      font-weight: 700;
      font-size: 0.9rem;
    }

    /* ===== PODIUM CARD WRAPPER ===== */
    .podium-card-wrapper {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.1) 0%, rgba(196, 150, 58, 0.05) 100%);
      border: 1.5px solid var(--border-light);
      border-radius: 20px;
      padding: 2rem;
      animation: fadeInUp 0.8s ease-out;
      height: fit-content;
    }

    .podium-title {
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .podium-quote {
      font-size: 1rem;
      color: var(--text-secondary);
      font-style: italic;
      margin-bottom: 2rem;
      font-weight: 500;
    }

    .podium-stands {
      display: flex;
      justify-content: center;
      align-items: flex-end;
      gap: 4rem;
      margin-bottom: 2rem;
    }

    .podium-stand {
      display: flex;
      flex-direction: column;
      align-items: center;
      animation: bounceIn 0.8s ease-out;
    }

    .podium-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.8rem;
      color: white;
      margin-bottom: 1rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 3px solid;
      position: relative;
    }

    .podium-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .podium-avatar.first {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-color: var(--gold-light);
      width: 120px;
      height: 120px;
      font-size: 2rem;
    }

    .podium-avatar.first::before {
      content: '👑';
      position: absolute;
      top: -15px;
      font-size: 2rem;
    }

    .podium-avatar.second {
      background: linear-gradient(135deg, #c0c0c0 0%, #a0a0a0 100%);
      border-color: #e0e0e0;
    }

    .podium-avatar.third {
      background: linear-gradient(135deg, #CD7F32 0%, #a0522d 100%);
      border-color: #daa520;
    }

    .podium-card {
      background: var(--surface-2);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 1.5rem;
      min-width: 120px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .podium-rank {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
    }

    .podium-rank.first {
      color: var(--gold);
    }

    .podium-rank.second {
      color: #c0c0c0;
    }

    .podium-rank.third {
      color: #CD7F32;
    }

    .podium-label {
      font-size: 0.9rem;
      color: var(--text-secondary);
      font-weight: 600;
    }

    .podium-label.first {
      color: var(--gold);
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--text-secondary);
    }

    .empty-icon {
      font-size: 4rem;
      color: var(--gold);
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .empty-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .empty-text {
      font-size: 1rem;
      color: var(--text-tertiary);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .content {
        grid-template-columns: 1fr;
        gap: 2rem;
      }

      .page-header {
        grid-column: 1;
      }
    }

    @media (max-width: 768px) {
      .topbar {
        padding: 1rem;
      }

      .page-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
      }

      .page-title-group h1 {
        font-size: 2rem;
      }

      .podium-stands {
        flex-direction: column;
        gap: 2rem;
      }

      .podium-stand {
        flex-direction: row;
        gap: 1rem;
      }

      .podium-avatar {
        width: 80px;
        height: 80px;
        font-size: 1.5rem;
      }

      .podium-avatar.first {
        width: 100px;
        height: 100px;
        font-size: 1.8rem;
      }

      .leaderboard-card {
        padding: 1rem;
      }

      .podium-card-wrapper {
        padding: 1.5rem;
      }

      .content {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
<div class="container">

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="topbar-left">
      <a href="{{ route('student.index') }}" class="back-btn">
        <i class="ri-arrow-right-line"></i>
        <span>العودة للرئيسية</span>
      </a>
    </div>
  </header>

  <!-- CONTENT -->
  <div class="content">
    <!-- Page Header -->
    <div class="page-header" style="grid-column: 1 / -1;">
      <div class="page-icon">
        <i class="ri-trophy-fill"></i>
      </div>
      <div class="page-title-group">
        <h1>المتنافسين</h1>
        <p class="page-subtitle">تابع ترتيبك بين الطلاب واستمر في التقدم</p>
      </div>
    </div>

    <!-- Leaderboard Card -->
    <div class="leaderboard-card">
      <div class="leaderboard-header">
        <h2 class="leaderboard-title">قائمة المتنافسين</h2>
      </div>

      <div class="leaderboard-list">
        @forelse($topStudents as $index => $student)
          <div class="leaderboard-item">
            <div class="rank rank-{{ $index < 3 ? ($index + 1) : 'other' }}">
              {{ $index + 1 }}
            </div>
            <div class="student-info">
              <div class="student-name">{{ $student->name }}</div>
              <div class="student-points">{{ $student->points }} XP</div>
            </div>
          </div>
        @empty
          <div class="empty-state">
            <div class="empty-icon">
              <i class="ri-trophy-line"></i>
            </div>
            <h3 class="empty-title">لا توجد بيانات متاحة</h3>
            <p class="empty-text">ابدأ في الدراسة واكسب النقاط لتظهر في القائمة</p>
          </div>
        @endforelse
      </div>
    </div>

    <!-- Podium Card -->
    <div class="podium-card-wrapper">
      @if($top3Students->count() > 0)
        <div class="podium-section">
          <h2 class="podium-title">نخبة جمعية إجلال</h2>
          <p class="podium-quote">"وفي ذلك فليتنافس المتنافسون"</p>

          <div class="podium-stands">
            <!-- 2nd Place -->
            @if($top3Students->count() >= 2)
              <div class="podium-stand" style="animation-delay: 0.2s;">
                <div class="podium-avatar second">
                  @if(!empty($top3Students->get(1)->avatar_url))
                    <img src="{{ asset('storage/' . $top3Students->get(1)->avatar_url) }}" alt="{{ $top3Students->get(1)->name }}">
                  @else
                    {{ mb_substr($top3Students->get(1)->name, 0, 2) }}
                  @endif
                </div>
                <div class="podium-card">
                  <div class="podium-rank second">2</div>
                  <div class="podium-label second">فضية 🥈</div>
                </div>
              </div>
            @endif

            <!-- 1st Place -->
            @if($top3Students->count() >= 1)
              <div class="podium-stand" style="animation-delay: 0s;">
                <div class="podium-avatar first">
                  @if(!empty($top3Students->first()->avatar_url))
                    <img src="{{ asset('storage/' . $top3Students->first()->avatar_url) }}" alt="{{ $top3Students->first()->name }}">
                  @else
                    {{ mb_substr($top3Students->first()->name, 0, 2) }}
                  @endif
                </div>
                <div class="podium-card">
                  <div class="podium-rank first">1</div>
                  <div class="podium-label first">بطل إجلال 👑</div>
                </div>
              </div>
            @endif

            <!-- 3rd Place -->
            @if($top3Students->count() >= 3)
              <div class="podium-stand" style="animation-delay: 0.4s;">
                <div class="podium-avatar third">
                  @if(!empty($top3Students->get(2)->avatar_url))
                    <img src="{{ asset('storage/' . $top3Students->get(2)->avatar_url) }}" alt="{{ $top3Students->get(2)->name }}">
                  @else
                    {{ mb_substr($top3Students->get(2)->name, 0, 2) }}
                  @endif
                </div>
                <div class="podium-card">
                  <div class="podium-rank third">3</div>
                  <div class="podium-label third">برونزية 🥉</div>
                </div>
              </div>
            @endif
          </div>
        </div>
      @else
        <div class="empty-state">
          <div class="empty-icon">
            <i class="ri-medal-line"></i>
          </div>
          <h3 class="empty-title">لا توجد بيانات متاحة</h3>
          <p class="empty-text">ابدأ في الدراسة واكسب النقاط لتظهر في المنصة</p>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  // Page animations
  document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animations to leaderboard items
    const leaderboardItems = document.querySelectorAll('.leaderboard-item');
    leaderboardItems.forEach((item, index) => {
      item.style.animationDelay = `${index * 0.1}s`;
      item.style.animation = 'fadeInUp 0.6s ease-out forwards';
      item.style.opacity = '0';
      item.style.transform = 'translateY(20px)';
    });

    // Trigger animations after a short delay
    setTimeout(() => {
      leaderboardItems.forEach(item => {
        item.style.opacity = '1';
        item.style.transform = 'translateY(0)';
      });
    }, 100);
  });
</script>
    @include('components.account-theme-foot')
</body>
</html>



