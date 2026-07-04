<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $course->title }} | جمعية إجلال</title>
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
      position: relative;
    }

    /* ===== BACKGROUND EFFECTS ===== */
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

    /* ===== GLOBAL ANIMATIONS ===== */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInRight {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes scaleIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    @keyframes slideHorizontal {
      0%, 100% { transform: translateX(0); }
      50% { transform: translateX(5px); }
    }

    @keyframes shimmer {
      0% { background-position: -1000px 0; }
      100% { background-position: 1000px 0; }
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
      position: sticky;
      top: 0;
      z-index: 100;
      animation: fadeInDown 0.6s ease-out;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .search-wrap {
      flex: 1;
      max-width: 520px;
      position: relative;
    }

    .search-wrap input {
      width: 100%;
      padding: 0.85rem 3.2rem 0.85rem 1rem;
      background: var(--theme-surface);
      border: 1px solid var(--theme-border);
      border-radius: 12px;
      color: var(--theme-text);
      font-family: 'Cairo', sans-serif;
      font-size: 0.95rem;
      transition: all 0.3s;
    }

    .search-wrap input::placeholder {
      color: var(--theme-muted);
    }

    .search-wrap input:focus {
      outline: none;
      border-color: var(--theme-gold);
      background: var(--theme-surface-2);
    }

    .search-icon {
      position: absolute;
      right: 1.2rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--theme-text-soft);
      font-size: 1rem;
    }

    .topbar-actions {
      display: flex;
      gap: 1rem;
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
      font-size: 1.4rem;
      transition: all 0.3s ease;
    }

    .icon-btn:hover {
      background: var(--theme-gold-soft);
      border-color: var(--gold);
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(196, 150, 58, 0.2);
    }

    .g-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: var(--theme-surface-2);
      border: 1px solid var(--theme-border);
      padding: 0.75rem 1.2rem;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--theme-gold);
      text-decoration: none;
    }

    .g-badge i {
      font-size: 1.1rem;
    }

    /* ===== HERO SECTION ===== */
    .hero-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
      padding: 4rem 3rem;
      max-width: 1600px;
      margin: 0 auto;
      animation: fadeInUp 0.7s ease-out 0.1s both;
    }

    .hero-content {
      padding-right: 2rem;
    }

    .breadcrumb {
      display: inline-block;
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      font-weight: 500;
      padding: 0.6rem 1.2rem;
      background: var(--surface-2);
      border-radius: 8px;
      border-left: 3px solid var(--gold);
    }

    .breadcrumb i {
      color: var(--gold);
      margin: 0 0.5rem;
    }

    .hero-title {
      font-family: 'Playfair Display', serif;
      font-size: 3.5rem;
      font-weight: 900;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -0.5px;
    }

    .hero-description {
      font-size: 1.15rem;
      color: var(--text-secondary);
      line-height: 1.8;
      margin-bottom: 2.5rem;
      max-width: 550px;
      font-weight: 300;
    }

    .hero-badges {
      display: flex;
      gap: 1rem;
      margin-bottom: 2.5rem;
      flex-wrap: wrap;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: var(--theme-surface-2);
      border: 1px solid var(--theme-border);
      padding: 0.75rem 1.3rem;
      border-radius: 10px;
      font-size: 0.95rem;
      color: var(--theme-text-soft);
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .badge:hover {
      border-color: var(--gold);
      background: var(--theme-surface);
    }

    .badge i {
      font-size: 1.1rem;
      color: var(--gold);
    }

    .hero-buttons {
      display: flex;
      gap: 1.2rem;
      align-items: center;
    }

    .btn-primary {
      padding: 1.1rem 2.5rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.05rem;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      gap: 0.8rem;
      box-shadow: 0 10px 30px rgba(196, 150, 58, 0.25);
    }

    .btn-primary:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.4);
    }

    .btn-primary:active {
      transform: translateY(-1px);
    }

    .btn-secondary {
      padding: 1.1rem 2.5rem;
      background: var(--surface-2);
      color: var(--gold);
      border: 1.5px solid var(--border-light);
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.05rem;
      cursor: pointer;
      transition: all 0.4s;
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .btn-secondary:hover {
      border-color: var(--gold);
      background: var(--surface-3);
      transform: translateY(-4px);
    }

    .hero-visual {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 400px;
      padding: 2rem;
    }

    .visual-circle {
      position: absolute;
      border-radius: 50%;
      border: 2px solid var(--border-light);
      opacity: 0.8;
    }

    .circle-1 {
      width: 300px;
      height: 300px;
      animation: slideHorizontal 4s ease-in-out infinite;
    }

    .circle-2 {
      width: 200px;
      height: 200px;
      border-color: var(--border-strong);
      animation: slideHorizontal 5s ease-in-out infinite reverse;
    }

    .visual-icon {
      position: relative;
      z-index: 10;
      font-size: 180px;
      color: var(--gold);
      opacity: 0.9;
      animation: scaleIn 0.8s ease-out;
    }

    /* ===== MAIN CONTENT SECTION ===== */
    .main-content {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 3rem;
      padding: 3rem;
      max-width: 1600px;
      margin: 0 auto;
    }

    /* ===== SECTION HEADER ===== */
    .section-header {
      margin-bottom: 3rem;
      padding-bottom: 1.5rem;
      border-bottom: 2px solid var(--border-light);
      animation: fadeInUp 0.7s ease-out 0.2s both;
    }

    .section-title {
      font-size: 2.2rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      color: var(--text-primary);
      letter-spacing: -0.3px;
    }

    .section-title i {
      font-size: 2.5rem;
      color: var(--gold);
    }

    .section-subtitle {
      font-size: 0.95rem;
      color: var(--text-secondary);
      font-weight: 400;
    }

    /* ===== LESSONS FILTER ===== */
    .lessons-toolbar {
      display: flex;
      gap: 1rem;
      margin-bottom: 2.5rem;
      flex-wrap: wrap;
      animation: fadeInUp 0.7s ease-out 0.25s both;
    }

    .filter-btn {
      padding: 0.7rem 1.5rem;
      border: 2px solid var(--border-light);
      background: transparent;
      color: var(--text-secondary);
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .filter-btn:hover {
      border-color: var(--gold);
      color: var(--gold-light);
      background: var(--surface-1);
    }

    .filter-btn.active {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      border-color: var(--gold);
    }

    /* ===== LESSONS GRID ===== */
    .lessons-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .lesson-card {
      background: var(--theme-surface);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      animation: fadeInUp 0.6s ease-out both;
      animation-delay: calc(0.05s * var(--delay, 0));
    }

    .lesson-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(196, 150, 58, 0.1), transparent);
      transition: all 0.6s ease;
    }

    .lesson-card:hover {
      border-color: var(--gold);
      background: var(--theme-surface-2);
      transform: translateY(-12px);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.25);
    }

    .lesson-card:hover::before {
      right: 100%;
    }

    .lesson-header {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .lesson-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.2) 0%, rgba(196, 150, 58, 0.1) 100%);
      border: 2px solid var(--border-light);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 2rem;
      flex-shrink: 0;
    }

    .lesson-number {
      display: inline-block;
      font-size: 0.8rem;
      background: var(--surface-3);
      color: var(--gold);
      padding: 0.4rem 0.8rem;
      border-radius: 6px;
      font-weight: 700;
    }

    .lesson-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.8rem;
      line-height: 1.4;
    }

    .lesson-desc {
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      line-height: 1.6;
      flex-grow: 1;
    }

    .lesson-meta {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--border-light);
      margin-bottom: 1.5rem;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    .meta-item i {
      color: var(--gold);
      width: 18px;
      font-size: 1.1rem;
    }

    .lesson-progress {
      margin-bottom: 1.2rem;
    }

    .progress-label {
      font-size: 0.8rem;
      color: var(--text-secondary);
      margin-bottom: 0.6rem;
      display: flex;
      justify-content: space-between;
      font-weight: 600;
    }

    .progress-bar {
      width: 100%;
      height: 6px;
      background: var(--surface-3);
      border-radius: 3px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-radius: 3px;
      transition: width 0.5s ease;
    }

    .lesson-status {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.6rem 1rem;
      background: var(--surface-2);
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .lesson-status.completed {
      color: var(--accent);
      background: rgba(6, 167, 125, 0.1);
    }

    .lesson-status.in-progress {
      color: var(--gold);
      background: rgba(196, 150, 58, 0.1);
    }

    .lesson-status.not-started {
      color: var(--text-secondary);
      background: var(--surface-2);
    }

    .empty-state {
      grid-column: 1 / -1;
      background: var(--surface-2);
      border: 2px dashed var(--border-light);
      border-radius: 16px;
      padding: 4rem 2rem;
      text-align: center;
      animation: fadeInUp 0.7s ease-out;
    }

    .empty-icon {
      font-size: 5rem;
      color: var(--gold);
      opacity: 0.4;
      margin-bottom: 1rem;
    }

    .empty-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.8rem;
    }

    .empty-desc {
      color: var(--text-secondary);
      font-size: 1rem;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      animation: fadeInRight 0.7s ease-out 0.2s both;
    }

    .card {
      background: var(--theme-surface);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      margin-bottom: 2rem;
      transition: all 0.3s ease;
    }

    .card:hover {
      border-color: var(--border-strong);
      background: var(--theme-surface-2);
    }

    .card-title {
      font-size: 1.3rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      color: var(--text-primary);
    }

    .card-title i {
      font-size: 1.6rem;
      color: var(--gold);
    }

    /* Progress Card */
    .progress-item {
      margin-bottom: 1.8rem;
    }

    .progress-item:last-child {
      margin-bottom: 0;
    }

    .progress-item-label {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      margin-bottom: 0.7rem;
      font-weight: 600;
    }

    .progress-item-label-text {
      color: var(--text-primary);
    }

    .progress-item-label-value {
      color: var(--gold);
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.2rem;
      margin-bottom: 0;
    }

    .stat-box {
      background: var(--theme-surface-2);
      border: 1.5px solid var(--border-light);
      border-radius: 12px;
      padding: 1.3rem;
      text-align: center;
      transition: all 0.3s ease;
    }

    .stat-box:hover {
      border-color: var(--gold);
      background: var(--theme-surface);
      transform: translateY(-4px);
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 900;
      color: var(--gold);
      margin-bottom: 0.4rem;
    }

    .stat-label {
      font-size: 0.8rem;
      color: var(--text-secondary);
      font-weight: 600;
      text-transform: capitalize;
    }

    /* Info Items */
    .info-list {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .info-item {
      display: flex;
      gap: 1rem;
      align-items: flex-start;
      padding-bottom: 1.2rem;
      border-bottom: 1px solid var(--border-light);
    }

    .info-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .info-icon {
      width: 45px;
      height: 45px;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.15) 0%, rgba(196, 150, 58, 0.08) 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      flex-shrink: 0;
      font-size: 1.3rem;
    }

    .info-content h4 {
      font-size: 0.95rem;
      font-weight: 700;
      margin: 0 0 0.3rem 0;
      color: var(--text-primary);
    }

    .info-content p {
      font-size: 0.85rem;
      color: var(--text-secondary);
      margin: 0;
      line-height: 1.5;
    }

    /* Instructor Card */
    .instructor-section {
      background: var(--theme-surface-2);
      border: 1.5px solid var(--border-light);
      border-radius: 14px;
      padding: 2rem;
      text-align: center;
    }

    .instructor-avatar {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 800;
      font-size: 2.2rem;
      margin: 0 auto 1.2rem;
      box-shadow: 0 10px 30px rgba(196, 150, 58, 0.3);
    }

    .instructor-name {
      font-size: 1.1rem;
      font-weight: 800;
      margin-bottom: 0.3rem;
      color: var(--text-primary);
    }

    .instructor-title {
      font-size: 0.85rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    .instructor-btn {
      width: 100%;
      padding: 0.85rem 1rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      font-size: 0.95rem;
    }

    .instructor-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(196, 150, 58, 0.3);
    }

    /* CTA Button */
    .cta-button {
      width: 100%;
      padding: 1.2rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 800;
      font-size: 1.05rem;
      cursor: pointer;
      transition: all 0.4s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      box-shadow: 0 10px 30px rgba(196, 150, 58, 0.25);
    }

    .cta-button:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.4);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
      .main-content {
        grid-template-columns: 1fr;
      }

      .sidebar {
        order: -1;
      }

      .hero-section {
        grid-template-columns: 1fr;
        gap: 2rem;
      }

      .hero-content {
        padding-right: 0;
      }
    }

    @media (max-width: 768px) {
      .topbar { padding: 1rem 1.2rem; }
      .topbar-right { gap: 0.6rem; }

      .hero-section {
        padding: 2rem 1.5rem;
        grid-template-columns: 1fr;
      }

      .hero-title {
        font-size: 2.2rem;
      }

      .hero-visual {
        min-height: 250px;
      }

      .visual-icon {
        font-size: 130px;
      }

      .main-content {
        padding: 2rem 1.5rem;
        gap: 2rem;
      }

      .lessons-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }

      .section-title {
        font-size: 1.8rem;
      }

      .hero-buttons {
        flex-direction: column;
        width: 100%;
      }

      .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .hero-title { font-size: 1.8rem; }
      .hero-section { padding: 1.5rem 1rem; }
      .main-content { padding: 1.5rem 1rem; }
      .section-title { font-size: 1.5rem; }
    }

    a { text-decoration: none; color: inherit; }

    .topbar,
    .hero-section,
    .main-content {
      position: relative;
      z-index: 1;
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header class="topbar">
  <div class="topbar-left">
    <a href="{{ route('student.academy') }}" class="icon-btn" title="العودة للأكاديمية">
      <i class="ri-arrow-right-line"></i>
    </a>
    <a href="{{ route('profile.show') }}" class="icon-btn" title="الملف الشخصي">
      <i class="ri-user-line"></i>
    </a>
    <button class="icon-btn" id="courseThemeToggle" title="الوضع الليلي">
      <i class="ri-moon-line"></i>
    </button>
    <button class="icon-btn" id="courseShareBtn" title="مشاركة المسار">
      <i class="ri-share-line"></i>
    </button>
    <div class="g-badge">
      <i class="ri-flashlight-fill"></i>
      <span>{{ Auth::user()->points ?? 0 }} نقطة</span>
    </div>
  </div>
  <div class="search-wrap">
    <i class="ri-search-line search-icon"></i>
    <input type="text" placeholder="ابحث داخل الدروس...">
  </div>
</header>

<!-- HERO SECTION -->
<div class="hero-section">
  <div class="hero-content">
    <div class="breadcrumb">
      <i class="ri-graduation-cap-2-line"></i>
      الأكاديمية > المسار
    </div>

    <h1 class="hero-title">{{ $course->title }}</h1>
    <p class="hero-description">{{ $course->description ?? 'وصف المسار غير متوفر حاليًا. ابدأ الدروس بالترتيب لتحقيق أفضل تقدم.' }}</p>

    <div class="hero-badges">
      <div class="badge">
        <i class="ri-book-line"></i>
        {{ count($lessons ?? []) }} دروس تعليمية
      </div>
      <div class="badge">
        <i class="ri-time-line"></i>
        {{ $course->total_hours ?? '10' }} ساعة تدريب
      </div>
      <div class="badge">
        <i class="ri-bar-chart-line"></i>
        المستوى {{ $course->level ?? 'غير محدد' }}
      </div>
    </div>

    <div class="hero-buttons">
      <a href="{{ route('student.lesson.show', $lessons[0]['id'] ?? 0) }}" class="btn-primary" @if(empty($lessons)) style="opacity: 0.5; pointer-events: none;" @endif>
        <i class="ri-play-circle-fill"></i>
        ابدأ أول درس الآن
      </a>
      <button class="btn-secondary" id="courseShareBtn2">
        <i class="ri-share-line"></i>
        مشاركة مع الأصدقاء
      </button>
    </div>
  </div>

  <div class="hero-visual">
    <div class="visual-circle circle-1"></div>
    <div class="visual-circle circle-2"></div>
    <i class="visual-icon ri-graduation-cap-fill"></i>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
  <!-- LESSONS SECTION -->
  <div>
    <div class="section-header">
      <h2 class="section-title">
        <i class="ri-video-line"></i>
        محتوى الدروس التعليمية
      </h2>
      <p class="section-subtitle">{{ count($lessons ?? []) }} دروس منظمة لمتابعة التعلم خطوة بخطوة</p>
    </div>

    @if(!empty($lessons))
      <div class="lessons-toolbar">
        <button class="filter-btn active" id="filterAll">
          <i class="ri-layout-grid-line"></i> عرض الكل
        </button>
        <button class="filter-btn" id="filterCompleted">
          <i class="ri-check-circle-line"></i> المكتملة
        </button>
        <button class="filter-btn" id="filterPending">
          <i class="ri-hourglass-line"></i> غير المكتملة
        </button>
      </div>

      <div class="lessons-grid">
        @foreach($lessons as $index => $lesson)
          <a href="{{ route('student.lesson.show', $lesson['id']) }}" style="text-decoration: none;">
            <div class="lesson-card" data-status="{{ $lesson['completed'] ? 'completed' : 'pending' }}" style="--delay: {{ $index }}">
              <div class="lesson-header">
                <div class="lesson-icon">
                  <i class="{{ $lesson['content_icon'] ?? 'ri-book-open-line' }}"></i>
                </div>
                <div class="lesson-number">درس {{ $index + 1 }}</div>
              </div>

              <h3 class="lesson-title">{{ $lesson['title'] }}</h3>
              <p class="lesson-desc">استكشف هذا الدرس وراجع المحتوى التعليمي لتحقيق فهم أفضل للمفاهيم الأساسية.</p>

              <div class="lesson-meta">
                <div class="meta-item">
                  <i class="ri-time-line"></i>
                  <span>{{ $lesson['duration'] ?? 0 }} دقيقة</span>
                </div>
                <div class="meta-item">
                  <i class="{{ $lesson['content_icon'] ?? 'ri-play-circle-line' }}"></i>
                  <span>{{ $lesson['content_label'] ?? 'محتوى تعليمي' }}</span>
                </div>
              </div>

              <div class="lesson-progress">
                <div class="progress-label">
                  <span>التقدم</span>
                  <span style="color: var(--gold);">{{ $lesson['completed'] ? 100 : 0 }}%</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" style="width: {{ $lesson['completed'] ? 100 : 0 }}%"></div>
                </div>
              </div>

              <span class="lesson-status {{ $lesson['completed'] ? 'completed' : 'not-started' }}">
                <i class="ri-{{ $lesson['completed'] ? 'check-line' : 'play-line' }}"></i>
                {{ $lesson['completed'] ? 'مكتمل' : 'ابدأ الآن' }}
              </span>
            </div>
          </a>
        @endforeach
      </div>
    @else
      <div class="lessons-grid">
        <div class="empty-state">
          <div class="empty-icon"><i class="ri-movie-upload-line"></i></div>
          <h3 class="empty-title">قريبًا...</h3>
          <p class="empty-desc">لا توجد دروس متاحة في هذا المسار حتى الآن. أضف دروسًا جديدة للبدء.</p>
        </div>
      </div>
    @endif
  </div>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <!-- Progress Card -->
    <div class="card">
      <h3 class="card-title">
        <i class="ri-progress-5-line"></i>
        تقدمك في المسار
      </h3>

      <div class="progress-item">
        <div class="progress-item-label">
          <span class="progress-item-label-text">الدروس المكتملة</span>
          <span class="progress-item-label-value">{{ count(array_filter($lessons ?? [], fn($l) => $l['completed'])) }}/{{ count($lessons ?? []) }}</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: {{ count($lessons ?? []) > 0 ? (count(array_filter($lessons ?? [], fn($l) => $l['completed'])) / count($lessons ?? [])) * 100 : 0 }}%"></div>
        </div>
      </div>

      <div class="progress-item">
        <div class="progress-item-label">
          <span class="progress-item-label-text">إكمال البرنامج</span>
          <span class="progress-item-label-value">{{ $progress ?? 0 }}%</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: {{ $progress ?? 0 }}%"></div>
        </div>
      </div>
    </div>

    <!-- Stats Card -->
    <div class="card">
      <h3 class="card-title">
        <i class="ri-bar-chart-2-line"></i>
        الإحصائيات
      </h3>

      <div class="stats-grid">
        <div class="stat-box">
          <div class="stat-number">{{ count($lessons ?? []) }}</div>
          <div class="stat-label">إجمالي الدروس</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">{{ $course->total_hours ?? '0' }}</div>
          <div class="stat-label">عدد الساعات</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">{{ count(array_filter($lessons ?? [], fn($l) => $l['completed'])) }}</div>
          <div class="stat-label">مكتملة</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">{{ count($lessons ?? []) - count(array_filter($lessons ?? [], fn($l) => $l['completed'])) }}</div>
          <div class="stat-label">متبقية</div>
        </div>
      </div>
    </div>

    <!-- Instructor Card -->
    @if($course->instructor ?? false)
      <div class="card">
        <h3 class="card-title">
          <i class="ri-user-3-line"></i>
          المعلم
        </h3>

        <div class="instructor-section">
          <div class="instructor-avatar">
            {{ mb_substr($course->instructor->name ?? 'M', 0, 1) }}
          </div>
          <div class="instructor-name">{{ $course->instructor->name ?? 'المعلم' }}</div>
          <div class="instructor-title">{{ $course->instructor->title ?? 'مختص تعليمي' }}</div>
          <a class="instructor-btn" href="{{ route('student.messaging', ['contact' => $course->instructor->id]) }}">
            <i class="ri-mail-send-line"></i>
            تواصل مع المعلم
          </a>
        </div>
      </div>
    @endif

    <!-- Course Info Card -->
    <div class="card">
      <h3 class="card-title">
        <i class="ri-information-line"></i>
        معلومات المسار
      </h3>

      <div class="info-list">
        <div class="info-item">
          <div class="info-icon"><i class="ri-book-mark-line"></i></div>
          <div class="info-content">
            <h4>المستوى التعليمي</h4>
            <p>{{ $course->level ?? 'مستوى غير محدد' }}</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon"><i class="ri-checkbox-circle-line"></i></div>
          <div class="info-content">
            <h4>حالة المسار</h4>
            <p style="color: {{ ($course->status === 'published' ? 'var(--accent)' : ($course->status === 'draft' ? 'var(--warning)' : 'var(--text-secondary)')) }}">
              @if($course->status === 'published')
                منشور وجاهز
              @elseif($course->status === 'draft')
                مسودة قيد التطوير
              @else
                غير منشور
              @endif
            </p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon"><i class="ri-award-line"></i></div>
          <div class="info-content">
            <h4>شهادة الإنجاز</h4>
            <p>بعد إنهاء جميع الدروس يمكنك الحصول على شهادة إتمام المسار.</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon"><i class="ri-layout-grid-line"></i></div>
          <div class="info-content">
            <h4>محتوى المسار</h4>
            <p>فيديوهات تعليمية ومواد مساعدة وتمارين تطبيقية.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Button -->
  <button class="cta-button" id="continueLearningBtn">
    <i class="ri-play-circle-fill"></i>
    استمر في التعلم الآن
  </button>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('courseThemeToggle').addEventListener('click', function () {
      if (typeof window.toggleThemeUniversal === 'function') {
        window.toggleThemeUniversal();
      }
    });

    document.getElementById('courseShareBtn').addEventListener('click', shareAction);
    document.getElementById('courseShareBtn2').addEventListener('click', shareAction);

    document.getElementById('filterAll').addEventListener('click', function (e) { filterLessons('all', e); });
    document.getElementById('filterCompleted').addEventListener('click', function (e) { filterLessons('completed', e); });
    document.getElementById('filterPending').addEventListener('click', function (e) { filterLessons('pending', e); });

    document.getElementById('continueLearningBtn').addEventListener('click', function () {
      const firstLesson = document.querySelector('.lesson-card');
      if (firstLesson) firstLesson.click();
    });
  });

  function filterLessons(status, event) {
    const cards = document.querySelectorAll('.lesson-card');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('.filter-btn').classList.add('active');

    cards.forEach(card => {
      const cardStatus = card.dataset.status;
      const show = status === 'all' || cardStatus === status;
      card.parentElement.style.display = show ? '' : 'none';
    });
  }

  function shareAction() {
    const text = `{{ $course->title }} - من منصة جمعية إجلال`;
    if (navigator.share) {
      navigator.share({
        title: 'منصة جمعية إجلال',
        text: text,
        url: window.location.href
      });
    } else {
      alert(text);
    }
  }
</script>

    @include('components.account-theme-foot')
</body>
</html>




