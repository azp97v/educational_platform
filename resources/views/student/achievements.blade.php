<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إنجازاتي | إدارة إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    :root {
      --primary: var(--theme-page-bg);
      --primary-dark: var(--theme-page-bg);
      --secondary: var(--theme-surface);
      --gold: var(--theme-gold);
      --gold-dark: var(--theme-gold-dark);
      --gold-light: rgba(198, 166, 117, 0.38);
      --success: var(--theme-success);
      --danger: var(--theme-danger);
      --surface-1: var(--theme-surface);
      --surface-2: var(--theme-surface-2);
      --surface-3: rgba(198, 166, 117, 0.1);
      --border-light: var(--theme-border-light);
      --text-primary: var(--theme-text);
      --text-secondary: var(--theme-text-soft);
      --text-tertiary: var(--theme-muted);
      --bg-gradient: radial-gradient(circle at 8% 10%, rgba(198, 166, 117, 0.24) 0%, transparent 34%),
                    radial-gradient(circle at 82% 76%, rgba(198, 166, 117, 0.14) 0%, transparent 38%);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Tajawal', sans-serif;
      background: var(--theme-page-bg);
      color: var(--text-primary);
      overflow-x: hidden;
      min-height: 100vh;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: var(--bg-gradient);
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

    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .container {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
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

    /* ===== CONTENT AREA ===== */
    .content {
      flex: 1;
      padding: 2rem;
      padding-bottom: 3rem;
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
    }

    /* ===== CUSTOM SCROLLBAR ===== */
    .content::-webkit-scrollbar {
      width: 12px;
    }

    .content::-webkit-scrollbar-track {
      background: linear-gradient(180deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      border-radius: 10px;
      margin: 5px;
    }

    .content::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, var(--gold) 0%, var(--gold-dark) 50%, var(--gold) 100%);
      border-radius: 10px;
      border: 2px solid var(--theme-border);
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

    /* ===== STREAK CARD ===== */
    .streak-card {
      background: linear-gradient(135deg, rgba(198, 166, 117, 0.16) 0%, rgba(198, 166, 117, 0.08) 100%);
      border: 1.5px solid var(--theme-border-strong);
      border-radius: 20px;
      padding: 3rem 2rem;
      margin-bottom: 3rem;
      text-align: center;
      animation: fadeInUp 0.7s ease-out;
      position: relative;
      overflow: hidden;
    }

    .streak-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(198, 166, 117, 0.1) 0%, rgba(198, 166, 117, 0.04) 100%);
      animation: shimmer 3s infinite;
    }

    .streak-emoji {
      font-size: 4rem;
      margin-bottom: 1rem;
      animation: bounceIn 0.8s ease-out;
    }

    .streak-number {
      font-size: 3rem;
      font-weight: 800;
      color: var(--gold);
      margin-bottom: 0.5rem;
      text-shadow: 0 0 20px rgba(198, 166, 117, 0.32);
    }

    .streak-label {
      font-size: 1.2rem;
      color: var(--text-secondary);
      font-weight: 600;
    }

    .streak-subtitle {
      font-size: 1rem;
      color: var(--text-tertiary);
      margin-top: 0.5rem;
    }

    /* ===== ACHIEVEMENTS & CERTIFICATES GRID ===== */
    .achievements-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      margin-bottom: 3rem;
    }

    @media (max-width: 1024px) {
      .achievements-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
      }
    }

    /* ===== CERTIFICATES SECTION ===== */
    .certificates-section {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.15) 0%, rgba(196, 150, 58, 0.08) 100%);
      border: 2px solid rgba(196, 150, 58, 0.4);
      border-radius: 25px;
      padding: 3rem 2.5rem;
      animation: fadeInUp 0.8s ease-out;
      box-shadow: 0 15px 40px rgba(196, 150, 58, 0.1);
      position: relative;
      overflow: hidden;
    }

    .certificates-section::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(196, 150, 58, 0.05) 0%, transparent 70%);
      animation: pulse-glow 4s infinite;
      pointer-events: none;
    }

    .section-header {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 2.5rem;
      animation: fadeInUp 0.7s ease-out;
    }

    .section-icon {
      width: 70px;
      height: 70px;
      border-radius: 20px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2rem;
      box-shadow: 0 8px 25px rgba(196, 150, 58, 0.3);
      animation: pulse-glow 3s infinite;
      transition: transform 0.3s ease;
    }

    .section-icon:hover {
      transform: scale(1.1);
    }

    .section-title {
      font-size: 2.2rem;
      font-weight: 900;
      color: var(--text-primary);
      margin: 0;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      background: linear-gradient(135deg, var(--text-primary) 0%, rgba(0, 0, 0, 0.8) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: fadeInUp 0.8s ease-out;
    }

    .section-count {
      color: var(--text-secondary);
      font-size: 1.1rem;
      font-weight: 600;
      margin-top: 0.5rem;
      background: linear-gradient(135deg, var(--text-secondary) 0%, rgba(0, 0, 0, 0.6) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: fadeInUp 0.9s ease-out;
    }

    .certificates-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 2rem;
    }

    .certificate-card {
      background: linear-gradient(135deg, var(--surface-1) 0%, rgba(196, 150, 58, 0.02) 100%);
      border: 2px solid rgba(196, 150, 58, 0.2);
      border-radius: 20px;
      padding: 2.5rem 2rem;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .certificate-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--gold) 0%, var(--gold-light) 50%, var(--gold) 100%);
      border-radius: 20px 20px 0 0;
    }

    .certificate-card::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(196, 150, 58, 0.03) 0%, transparent 70%);
      opacity: 0;
      transition: opacity 0.4s ease;
      pointer-events: none;
    }

    .certificate-card:hover {
      background: linear-gradient(135deg, var(--surface-2) 0%, rgba(196, 150, 58, 0.05) 100%);
      border-color: var(--gold);
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.25);
    }

    .certificate-card:hover::after {
      opacity: 1;
    }

    .certificate-emoji {
      font-size: 4rem;
      margin-bottom: 1.5rem;
      display: block;
      text-align: center;
      animation: bounceIn 0.6s ease-out;
    }

    .certificate-name {
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 1rem;
      font-size: 1.3rem;
      text-align: center;
      line-height: 1.4;
      background: linear-gradient(135deg, var(--text-primary) 0%, rgba(196, 150, 58, 0.8) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: fadeInUp 0.7s ease-out;
    }

    .certificate-date {
      color: var(--text-secondary);
      font-size: 1rem;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 500;
      background: linear-gradient(135deg, var(--text-secondary) 0%, rgba(0, 0, 0, 0.6) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: fadeInUp 0.8s ease-out;
    }

    .certificate-score {
      display: inline-block;
      background: linear-gradient(135deg, var(--success) 0%, #27AE60 100%);
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 25px;
      font-size: 1rem;
      font-weight: 700;
      margin-bottom: 2rem;
      box-shadow: 0 4px 15px rgba(6, 167, 125, 0.3);
      text-align: center;
      width: 100%;
      animation: fadeInUp 0.9s ease-out;
    }

    .certificate-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
    }

    .action-btn {
      flex: 1;
      padding: 1rem 1.5rem;
      border: 2px solid rgba(196, 150, 58, 0.3);
      border-radius: 15px;
      background: linear-gradient(135deg, var(--surface-2) 0%, rgba(196, 150, 58, 0.05) 100%);
      color: var(--text-primary);
      text-decoration: none;
      font-size: 1rem;
      font-weight: 700;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 1s ease-out;
    }

    .action-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(196, 150, 58, 0.1), transparent);
      transition: left 0.4s ease;
    }

    .action-btn:hover {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-color: var(--gold);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(196, 150, 58, 0.3);
    }

    .action-btn:hover::before {
      left: 100%;
    }

    .action-btn.primary {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      border-color: var(--gold);
      animation: fadeInUp 1.1s ease-out;
    }

    .action-btn.primary:hover {
      background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
    }

    /* ===== ACHIEVEMENTS SECTION ===== */
    .achievements-section {
      background: linear-gradient(135deg, rgba(255, 107, 107, 0.15) 0%, rgba(255, 107, 107, 0.08) 100%);
      border: 2px solid rgba(255, 107, 107, 0.4);
      border-radius: 25px;
      padding: 3rem 2.5rem;
      animation: fadeInUp 0.9s ease-out;
      box-shadow: 0 15px 40px rgba(255, 107, 107, 0.1);
      position: relative;
      overflow: hidden;
    }

    .achievements-section::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 107, 107, 0.05) 0%, transparent 70%);
      animation: pulse-glow 4s infinite;
      pointer-events: none;
    }

    .achievement-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
    }

    .achievement-card {
      background: linear-gradient(135deg, var(--surface-1) 0%, rgba(255, 107, 107, 0.02) 100%);
      border: 2px solid rgba(255, 107, 107, 0.2);
      border-radius: 20px;
      padding: 2.5rem 2rem;
      text-align: center;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      cursor: pointer;
    }

    .achievement-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--gold) 0%, var(--gold-light) 50%, var(--gold) 100%);
      border-radius: 20px 20px 0 0;
    }

    .achievement-card::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 107, 107, 0.03) 0%, transparent 70%);
      opacity: 0;
      transition: opacity 0.4s ease;
      pointer-events: none;
    }

    .achievement-card:hover {
      background: linear-gradient(135deg, var(--surface-2) 0%, rgba(255, 107, 107, 0.05) 100%);
      border-color: var(--gold);
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 20px 50px rgba(255, 107, 107, 0.25);
    }

    .achievement-card:hover::after {
      opacity: 1;
    }

    .achievement-emoji {
      font-size: 5rem;
      margin-bottom: 2rem;
      display: block;
      animation: bounceIn 0.6s ease-out;
      filter: drop-shadow(0 4px 8px rgba(255, 107, 107, 0.3));
    }

    .achievement-name {
      font-weight: 800;
      color: var(--text-primary);
      font-size: 1.4rem;
      line-height: 1.4;
      text-shadow: 0 0 10px rgba(255, 107, 107, 0.2);
      background: linear-gradient(135deg, var(--text-primary) 0%, rgba(255, 107, 107, 0.8) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: fadeInUp 0.8s ease-out;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--text-secondary);
      grid-column: 1 / -1;
      animation: fadeInUp 0.8s ease-out;
    }

    .empty-icon {
      font-size: 4rem;
      color: var(--gold);
      margin-bottom: 1rem;
      opacity: 0.5;
      animation: fadeInUp 0.6s ease-out;
    }

    .empty-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      animation: fadeInUp 0.7s ease-out;
    }

    .empty-text {
      font-size: 1rem;
      color: var(--text-tertiary);
      margin-bottom: 2rem;
      animation: fadeInUp 0.9s ease-out;
    }

    .empty-action {
      display: inline-block;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      padding: 1rem 2rem;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.3s ease;
      animation: fadeInUp 1s ease-out;
    }

    .empty-action:hover {
      background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(196, 150, 58, 0.3);
    }

    /* ===== RESPONSIVE ===== */
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

      .streak-card {
        padding: 2rem 1.5rem;
      }

      .streak-number {
        font-size: 2.5rem;
      }

      .certificates-section,
      .achievements-section {
        padding: 2rem 1.5rem;
      }

      .section-icon {
        width: 60px;
        height: 60px;
        font-size: 1.8rem;
      }

      .section-title {
        font-size: 1.8rem;
      }

      .certificates-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }

      .certificate-card {
        padding: 2rem 1.5rem;
      }

      .certificate-emoji {
        font-size: 3rem;
        margin-bottom: 1rem;
      }

      .certificate-name {
        font-size: 1.1rem;
      }

      .achievement-cards-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
      }

      .achievement-card {
        padding: 2rem 1.5rem;
      }

      .achievement-emoji {
        font-size: 4rem;
        margin-bottom: 1.5rem;
      }

      .achievement-name {
        font-size: 1.2rem;
      }
    }
    @media (max-width: 480px) {
      .container { padding: 0 8px; }
      .page-title-group h1 { font-size: 1.6rem; }
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
    <div class="page-header">
      <div class="page-icon">
        <i class="ri-trophy-fill"></i>
      </div>
      <div class="page-title-group">
        <h1>إنجازاتي</h1>
        <p class="page-subtitle">تابع تقدمك واستمتع بإنجازاتك التعليمية</p>
      </div>
    </div>

    <!-- Streak Card -->
    @if($myStreak)
      <div class="streak-card">
        <div class="streak-emoji">🔥</div>
        <div class="streak-number">{{ $myStreak->current_streak ?? 0 }}</div>
        <div class="streak-label">أيام متتالية</div>
        <div class="streak-subtitle">استمر في رحلتك التعليمية!</div>
      </div>
    @endif

    <!-- Achievements & Certificates Grid -->
    <div class="achievements-grid">
      <!-- Certificates Section (merged) -->
      <div class="certificates-section">
        <div class="section-header">
          <div class="section-icon">
            <i class="ri-award-fill"></i>
          </div>
          <div>
            <h2 class="section-title">شهاداتي</h2>
            <div class="section-count">{{ $myCertificates->count() + $myDesignedCertificates->sum(fn($s) => $s->issuedTemplates->count()) }} شهادة</div>
          </div>
        </div>

        @php
          $hasAny = $myCertificates->count() > 0 || $myDesignedCertificates->count() > 0;
        @endphp

        @if($hasAny)
          <div class="certificates-grid">
            @foreach($myCertificates as $cert)
              <div class="certificate-card">
                <div class="certificate-emoji">📜</div>
                <div class="certificate-name">{{ $cert->course_name ?? $cert->name }}</div>
                <div class="certificate-date">
                  تم الحصول عليها في {{ \Carbon\Carbon::parse($cert->created_at)->locale('ar')->format('d M Y') }}
                </div>
                @if(isset($cert->score))
                  <span class="certificate-score">{{ $cert->score }}%</span>
                @endif
                <div class="certificate-actions">
                  <button class="action-btn primary cert-download-btn" data-cert-id="{{ $cert->id }}">
                    <i class="ri-download-line"></i> تحميل
                  </button>
                  <button class="action-btn cert-share-btn" data-cert-id="{{ $cert->id }}">
                    <i class="ri-share-line"></i> مشاركة
                  </button>
                </div>
              </div>
            @endforeach

            @foreach($myDesignedCertificates as $designedCert)
              @foreach($designedCert->issuedTemplates as $template)
                <div class="certificate-card">
                  <div class="certificate-emoji"><i class="ri-award-fill" style="font-size:3.5rem;"></i></div>
                  <div class="certificate-name">{{ $template->title }} — {{ $designedCert->course }}</div>
                  <div class="certificate-date">
                    تم الإصدار في {{ \Carbon\Carbon::parse($template->created_at)->locale('ar')->format('d M Y') }}
                  </div>
                  <span class="certificate-score">{{ $designedCert->degree }}</span>
                  <div class="certificate-actions">
                    <a class="action-btn primary" href="{{ route('student.certificates.custom.show', [$designedCert, $template]) }}" target="_blank">
                      <i class="ri-eye-line"></i> معاينة
                    </a>
                    <a class="action-btn" href="{{ route('student.certificates.custom.download', [$designedCert, $template]) }}">
                      <i class="ri-download-line"></i> تحميل
                    </a>
                  </div>
                </div>
              @endforeach
            @endforeach
          </div>
        @else
          <div class="empty-state">
            <div class="empty-icon">
              <i class="ri-award-line"></i>
            </div>
            <h3 class="empty-title">لا توجد شهادات بعد</h3>
            <p class="empty-text">أكمل الدورات والاختبارات لتحصل على شهادات إجلال</p>
          </div>
        @endif
      </div>

      <!-- Achievements Section -->
      <div class="achievements-section">
        <div class="section-header">
          <div class="section-icon" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 100%);">
            <i class="ri-medal-2-fill"></i>
          </div>
          <div>
            <h2 class="section-title" style="color: #FF6B6B;">إنجازاتي</h2>
            <div class="section-count">{{ $myAchievements->count() }} إنجاز</div>
          </div>
        </div>

        @if($myAchievements->count() > 0)
          <div class="achievement-cards-grid">
            @foreach($myAchievements as $achievement)
              <div class="achievement-card">
                <div class="achievement-emoji">
                  @if(strpos($achievement->name, 'أول') !== false)
                    🥇
                  @elseif(strpos($achievement->name, 'القارئ') !== false)
                    📚
                  @elseif(strpos($achievement->name, 'العبقري') !== false)
                    🧠
                  @elseif(strpos($achievement->name, 'الفائز') !== false)
                    🏆
                  @else
                    ⭐
                  @endif
                </div>
                <div class="achievement-name">{{ $achievement->name }}</div>
              </div>
            @endforeach
          </div>
        @else
          <div class="empty-state">
            <div class="empty-icon">
              <i class="ri-medal-line"></i>
            </div>
            <h3 class="empty-title">لا توجد إنجازات بعد</h3>
            <p class="empty-text">ابدأ في الدراسة واكسب النقاط لتحصل على الإنجازات</p>
          </div>
        @endif
      </div>
    </div>

    <!-- Overall Empty State -->
    @if(!$myStreak && $myCertificates->count() == 0 && $myAchievements->count() == 0 && $myDesignedCertificates->sum(fn($s) => $s->issuedTemplates->count()) == 0)
      <div class="empty-state">
        <div class="empty-icon">
          <i class="ri-rocket-line"></i>
        </div>
        <h3 class="empty-title">ابدأ رحلتك اليوم</h3>
        <p class="empty-text">أكمل الدورات والامتحانات لكسب الإنجازات والشهادات</p>
        <a href="{{ route('student.academy') }}" class="empty-action">
          <i class="ri-graduation-cap-line"></i> اذهب إلى الأكاديمية
        </a>
      </div>
    @endif
  </div>
</div>

<script>
  // Page animations
  document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animations to cards
    const cards = document.querySelectorAll('.certificate-card, .achievement-card');
    cards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.1}s`;
      card.style.animation = 'fadeInUp 0.6s ease-out forwards';
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
    });

    // Trigger animations after a short delay
    setTimeout(() => {
      cards.forEach(card => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      });
    }, 100);

    // Certificate download buttons
    document.querySelectorAll('.cert-download-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var certId = this.getAttribute('data-cert-id');
        window.location.href = '{{ route("certificate.download", ["certificate" => "__CERT_ID__"]) }}'.replace('__CERT_ID__', certId);
      });
    });

    // Certificate share buttons
    document.querySelectorAll('.cert-share-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (navigator.share) {
          navigator.share({
            title: 'شهادة إجلال',
            text: 'حصلت على شهادة من جمعية إجلال!',
            url: window.location.href
          });
        } else {
          var url = window.location.href;
          navigator.clipboard.writeText(url).then(function () {
            alert('تم نسخ رابط الشهادة!');
          });
        }
      });
    });
  });
</script>
    @include('components.account-theme-foot')
</body>
</html>



