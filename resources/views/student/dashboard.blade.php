<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم | جمعية إجلال</title>
  <meta name="description" content="منصة إجلال التعليمية - لوحة تحكم الطالب">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <style>
    * {
      --gold: var(--theme-gold);
      --gold-light: rgba(198, 166, 117, 0.36);
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
      --surface-3: rgba(198, 166, 117, 0.10);
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
        radial-gradient(circle at 14% 8%, rgba(198, 166, 117, 0.24) 0%, transparent 34%),
        radial-gradient(circle at 88% 84%, rgba(198, 166, 117, 0.12) 0%, transparent 38%);
      pointer-events: none;
      z-index: 0;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
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

    .app {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 0;
      height: 100vh;
      position: relative;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      background: linear-gradient(180deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%) !important;
      border-right: 1px solid var(--theme-border);
      padding: 2rem 1.5rem;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      animation: fadeInLeft 0.6s ease-out;
      direction: ltr;
      scrollbar-gutter: stable;
      box-shadow: 0 0 28px rgba(0, 0, 0, 0.15);
    }

    .sidebar-logo {
      text-align: center;
      margin-bottom: 2rem;
      padding: 1rem 0 1.5rem 0;
      border-bottom: 1px solid var(--border-light);
    }

    .logo-icon {
      width: 100%;
      height: auto;
      margin: 0 auto 1.5rem auto;
      padding: 0.5rem 0;
      border-radius: 0;
      overflow: visible;
      background: transparent;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo-icon img {
      width: auto;
      height: auto;
      max-width: 90%;
      max-height: 140px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .logo-icon i {
      font-size: 2.5rem;
      color: var(--gold);
    }

    .logo-name {
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 0.2rem;
    }

    .logo-sub {
      font-size: 0.85rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
      margin-bottom: auto;
    }

    .nav-btn {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.2rem;
      background: transparent;
      color: var(--theme-text-soft);
      border: 1px solid transparent;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .nav-btn:hover {
      background: var(--theme-gold-soft);
      color: var(--theme-text);
      border-color: var(--theme-border-strong);
    }

    .nav-btn.active {
      background: linear-gradient(135deg, var(--theme-gold-soft) 0%, rgba(198, 166, 117, 0.08) 100%);
      border-color: var(--theme-gold);
      color: var(--theme-gold);
    }

    .nav-btn i {
      font-size: 1.3rem;
    }

    .sidebar-footer {
      border-top: 1px solid var(--border-light);
      padding-top: 1.5rem;
      margin-top: 2rem;
    }

    .logout {
      width: 100%;
      background: linear-gradient(135deg, rgba(211, 47, 47, 0.15) 0%, rgba(211, 47, 47, 0.08) 100%);
      border: 1.5px solid rgba(211, 47, 47, 0.3);
      color: #FF6B6B;
    }

    .logout:hover {
      background: rgba(211, 47, 47, 0.2);
      border-color: #FF6B6B;
    }

    /* ===== MAIN CONTENT ===== */
    .main {
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .topbar {
      background: linear-gradient(90deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%) !important;
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--theme-border);
      padding: 1.2rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 2rem;
      animation: slideUp 0.6s ease-out;
      box-shadow: var(--theme-shadow-soft);
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }

    .icon-btn {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      border: 1px solid var(--theme-border);
      background: var(--theme-surface-2);
      color: var(--theme-gold);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      transition: all 0.3s ease;
    }

    .icon-btn:hover {
      background: var(--theme-gold-soft);
      border-color: var(--theme-gold);
      transform: translateY(-3px);
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
    }

    .g-badge i {
      font-size: 1.2rem;
    }

    .search-wrap {
      flex: 1;
      max-width: 400px;
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

    /* ===== CONTENT AREA ===== */
    .content {
      flex: 1;
      overflow-y: auto;
      padding: 2rem;
      padding-bottom: 3rem;
    }

    /* ===== CUSTOM SCROLLBAR ===== */
    .sidebar::-webkit-scrollbar {
      width: 8px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: transparent;
      margin-right: 0;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-radius: 10px;
      transition: background 0.3s ease;
      margin-right: 0;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, var(--gold-light) 0%, var(--gold) 100%);
      box-shadow: 0 0 10px rgba(196, 150, 58, 0.4);
    }

    .content::-webkit-scrollbar {
      width: 8px;
    }

    .content::-webkit-scrollbar-track {
      background: transparent;
    }

    .content::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-radius: 10px;
      transition: background 0.3s ease;
      margin-right: 0;
    }

    .content::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, var(--gold-light) 0%, var(--gold) 100%);
      box-shadow: 0 0 10px rgba(196, 150, 58, 0.4);
    }

    .page {
      display: none;
      animation: fadeInUp 0.5s ease-out;
    }

    .page.active {
      display: block;
    }

    /* ===== PROFILE HEADER ===== */
    .profile-header {
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 2rem;
      align-items: center;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.1) 0%, rgba(196, 150, 58, 0.05) 100%);
      border: 1.5px solid var(--border-light);
      border-radius: 20px;
      padding: 2.5rem;
      margin-bottom: 3rem;
      animation: fadeInUp 0.6s ease-out;
    }

    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2.8rem;
      font-weight: 800;
      box-shadow: 0 15px 40px rgba(196, 150, 58, 0.3);
      border: 3px solid var(--gold-light);
      overflow: hidden;
      flex-shrink: 0;
    }

    .profile-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-avatar.placeholder {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    }

    .profile-info h1 {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      color: var(--text-primary);
    }

    .profile-info p {
      color: var(--text-secondary);
      font-size: 0.95rem;
      margin-bottom: 1rem;
    }

    .profile-badges {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .badge-item {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: var(--surface-2);
      padding: 0.6rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      color: var(--text-secondary);
      font-weight: 600;
    }

    .badge-item i {
      color: var(--gold);
      font-size: 1rem;
    }

    .profile-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }

    .stat-card {
      text-align: center;
      padding: 1.5rem;
      background: var(--surface-1);
      border-radius: 12px;
      border: 1px solid var(--border-light);
    }

    .stat-number {
      font-size: 2.2rem;
      font-weight: 900;
      color: var(--gold);
      margin-bottom: 0.3rem;
    }

    .stat-label {
      font-size: 0.85rem;
      color: var(--text-secondary);
      font-weight: 600;
    }

    /* ===== QUICK STATS ===== */
    .quick-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-box {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.1) 0%, rgba(196, 150, 58, 0.05) 100%);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      display: flex;
      align-items: center;
      gap: 1.5rem;
      transition: all 0.3s ease;
      animation: fadeInUp 0.6s ease-out both;
    }

    .stat-box:hover {
      border-color: var(--gold);
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.15) 0%, rgba(196, 150, 58, 0.08) 100%);
      transform: translateY(-4px);
    }

    .stat-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.2) 0%, rgba(196, 150, 58, 0.1) 100%);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 2.2rem;
      flex-shrink: 0;
    }

    .stat-content h3 {
      font-size: 1.8rem;
      font-weight: 900;
      color: var(--gold);
      margin-bottom: 0.3rem;
    }

    .stat-content p {
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    /* ===== COURSES SECTION ===== */
    .section-title {
      font-size: 1.8rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--border-light);
    }

    .section-title i {
      font-size: 2rem;
      color: var(--gold);
    }

    .courses-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .course-card {
      background: var(--surface-1);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.6s ease-out both;
    }

    .course-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(196, 150, 58, 0.1), transparent);
      transition: all 0.6s;
    }

    .course-card:hover {
      border-color: var(--gold);
      background: var(--surface-3);
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(196, 150, 58, 0.25);
    }

    .course-card:hover::before {
      right: 100%;
    }

    .course-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .course-icon {
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
    }

    .course-progress {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.85rem;
      color: var(--gold);
      font-weight: 700;
    }

    .course-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.8rem;
      line-height: 1.4;
    }

    .course-desc {
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
      line-height: 1.6;
    }

    .progress-bar-wrapper {
      margin-bottom: 1.5rem;
    }

    .progress-label {
      display: flex;
      justify-content: space-between;
      font-size: 0.8rem;
      color: var(--text-secondary);
      margin-bottom: 0.6rem;
      font-weight: 600;
    }

    .progress-bar {
      width: 100%;
      height: 8px;
      background: var(--theme-soft);
      border-radius: 4px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-radius: 4px;
      transition: width 0.5s ease;
    }

    .course-btn {
      width: 100%;
      padding: 0.9rem;
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
    }

    .course-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(196, 150, 58, 0.3);
    }

    /* ===== EXAMS SECTION ===== */
    .exams-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .exam-card {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.08) 0%, rgba(196, 150, 58, 0.03) 100%);
      border: 2px dashed var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      animation: fadeInUp 0.6s ease-out both;
    }

    .exam-card:hover {
      border-color: var(--gold);
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.12) 0%, rgba(196, 150, 58, 0.06) 100%);
    }

    .exam-icon {
      font-size: 3rem;
      color: var(--gold);
      margin-bottom: 1rem;
    }

    .exam-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .exam-meta {
      display: flex;
      justify-content: center;
      gap: 1.2rem;
      color: var(--text-secondary);
      font-size: 0.85rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .exam-meta span {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .exam-btn {
      width: 100%;
      padding: 0.9rem;
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
      text-decoration: none;
    }

    .exam-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(196, 150, 58, 0.3);
    }

    /* ===== MESSAGING SECTION ===== */
    .messaging-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.8rem;
      margin-bottom: 3rem;
    }

    .messaging-card {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 22px;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
      min-height: 260px;
      transition: all 0.35s ease;
      overflow: hidden;
      position: relative;
      box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
      animation: fadeInUp 0.6s ease-out both;
    }

    .messaging-card:hover {
      transform: translateY(-6px);
      border-color: rgba(196, 150, 58, 0.4);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 28px 60px rgba(0, 0, 0, 0.18);
    }

    .messaging-card::after {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at top left, rgba(196, 150, 58, 0.12), transparent 38%);
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .messaging-card:hover::after {
      opacity: 1;
    }

    .messaging-card.highlight {
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.2), rgba(255, 255, 255, 0.08));
      border-color: rgba(196, 150, 58, 0.45);
    }

    .messaging-icon {
      width: 70px;
      height: 70px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(196, 150, 58, 0.15), rgba(196, 150, 58, 0.3));
      color: var(--gold);
      font-size: 2.2rem;
      box-shadow: 0 10px 30px rgba(196, 150, 58, 0.12);
    }

    .messaging-label {
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--text-primary);
    }

    .messaging-value,
    .messaging-text {
      color: var(--text-secondary);
      line-height: 1.8;
      font-size: 0.98rem;
    }

    .messaging-value {
      font-weight: 700;
      color: var(--theme-text);
    }

    .messaging-link {
      margin-top: auto;
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      justify-content: flex-end;
      color: var(--gold);
      font-weight: 800;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .messaging-link:hover {
      color: var(--theme-text);
    }

    .messaging-card.highlight .messaging-link {
      color: var(--theme-text);
    }

    .messaging-card.highlight .messaging-icon {
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      color: white;
    }

    /* ===== LEADERBOARD ===== */
    .leaderboard-wrap {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .leaderboard-card {
      background: var(--surface-1);
      border: 1.5px solid var(--border-light);
      border-radius: 16px;
      padding: 2rem;
      animation: fadeInUp 0.6s ease-out;
    }

    .leaderboard-title {
      font-size: 1.3rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
      color: var(--text-primary);
    }

    .lb-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      border-bottom: 1px solid var(--border-light);
      transition: all 0.3s;
    }

    .lb-item:hover {
      background: var(--surface-2);
      border-radius: 8px;
      border-bottom: 1px solid var(--border-light);
    }

    .lb-rank {
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--gold);
      width: 40px;
      text-align: center;
    }

    .lb-name {
      flex: 1;
      font-weight: 600;
    }

    .lb-points {
      font-weight: 700;
      color: var(--gold);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .app {
        grid-template-columns: 1fr;
      }

      .sidebar {
        display: none;
      }

      .profile-header {
        grid-template-columns: 1fr;
      }

      .profile-stats {
        grid-template-columns: repeat(2, 1fr);
      }

      .leaderboard-wrap {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .content {
        padding: 1.5rem;
      }

      .profile-header {
        padding: 1.5rem;
      }

      .courses-grid, .exams-grid {
        grid-template-columns: 1fr;
      }

      .section-title {
        font-size: 1.5rem;
      }

      .profile-avatar {
        width: 100px;
        height: 100px;
        font-size: 2.2rem;
      }
    }

    a { text-decoration: none; color: inherit; }

    /* ═══════════════════════════════════════════════════════════════
       RESPONSIVE ADDITIONS — Student Dashboard
    ═══════════════════════════════════════════════════════════════ */

    /* Hamburger button */
    .hamburger-btn {
      display: none;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border: 1px solid var(--border-light);
      border-radius: 10px;
      background: var(--surface-1);
      color: var(--text-primary);
      font-size: 20px;
      cursor: pointer;
      transition: all 0.2s ease;
      flex-shrink: 0;
    }
    .hamburger-btn:hover { background: var(--surface-3); border-color: var(--gold); }

    /* Sidebar backdrop */
    .sidebar-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.55);
      z-index: 198;
      backdrop-filter: blur(2px);
      -webkit-backdrop-filter: blur(2px);
    }
    .sidebar-backdrop.active { display: block; }

    /* Override responsive for sidebar toggle */
    @media (max-width: 1024px) {
      .app {
        grid-template-columns: 1fr;
      }
      .sidebar {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 280px !important;
        height: 100vh !important;
        z-index: 199 !important;
        transform: translateX(110%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: flex !important;
        border-radius: 0 !important;
        overflow-y: auto !important;
      }
      .sidebar.sidebar-open {
        transform: translateX(0);
      }
      .hamburger-btn { display: flex; }
      .topbar { padding: 0 16px; }
      .content { padding: 16px; }
      .profile-header {
        grid-template-columns: 1fr;
      }
      .profile-stats {
        grid-template-columns: repeat(2, 1fr);
      }
      .leaderboard-wrap {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .content { padding: 12px; }
      .topbar { padding: 0 12px; height: 56px; }
      .search-wrap { display: none; }
      .g-badge span { display: none; }
      .courses-grid, .exams-grid { grid-template-columns: 1fr; }
      .section-title { font-size: 1.4rem; }
      .profile-avatar { width: 90px; height: 90px; font-size: 2rem; }
      .profile-stats { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
      .content { padding: 10px; }
      .profile-stats { grid-template-columns: 1fr; }
      .topbar { padding: 0 10px; height: 52px; }
      .hamburger-btn { width: 36px; height: 36px; font-size: 18px; }
      .icon-btn { width: 34px; height: 34px; font-size: 15px; }
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="studentSidebarBackdrop"></div>
<div class="app">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="studentSidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(file_exists(public_path('images/logo/logo.png'))): ?>
          <img src="<?php echo e(asset('images/logo/logo.png?v=' . time())); ?>" alt="جمعية إجلال" loading="lazy" />
        <?php else: ?>
          <i class="ri-book-mark-fill"></i>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>
      <div class="logo-name">جمعية إجلال</div>
      <div class="logo-sub">بمكة المكرمة</div>
    </div>

    <nav class="sidebar-nav">
      <button class="nav-btn active" id="nb-home">
        <i class="ri-layout-grid-line"></i>
        <span>لوحة التحكم</span>
      </button>
      <a href="<?php echo e(route('student.academy')); ?>" class="nav-btn">
        <i class="ri-graduation-cap-line"></i>
        <span>الأكاديمية</span>
      </a>
      <a href="<?php echo e(route('student.exams')); ?>" class="nav-btn">
        <i class="ri-survey-line"></i>
        <span>الاختبارات</span>
      </a>
      <a href="<?php echo e(route('student.competition')); ?>" class="nav-btn">
        <i class="ri-trophy-line"></i>
        <span>المتنافسين</span>
      </a>
      <a href="<?php echo e(route('student.achievements')); ?>" class="nav-btn">
        <i class="ri-medal-fill"></i>
        <span>الإنجازات</span>
      </a>
      <a href="<?php echo e(route('student.messaging')); ?>" class="nav-btn">
        <i class="ri-message-2-line"></i>
        <span>المراسلة</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <form action="<?php echo e(route('logout')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit" class="nav-btn logout">
          <i class="ri-logout-box-r-line"></i>
          <span>خروج</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <!-- Hamburger toggle for mobile/tablet -->
        <button class="hamburger-btn" id="studentHamburger" title="فتح القائمة">
          <i class="ri-menu-line"></i>
        </button>
        <a href="<?php echo e(route('profile.show')); ?>" class="icon-btn" title="الملف الشخصي">
          <i class="ri-user-line"></i>
        </a>
        <button class="icon-btn" id="dashThemeToggle" title="الوضع الليلي">
          <i class="ri-moon-line"></i>
        </button>
        <button class="icon-btn" title="التنبيهات">
          <i class="ri-notification-3-line"></i>
        </button>

        <div class="g-badge">
          <i class="ri-fire-fill"></i>
          <span><?php echo e($myStreak->current_streak ?? 0); ?> يوم</span>
        </div>
        <div class="g-badge">
          <i class="ri-flashlight-fill"></i>
          <span><?php echo e(Auth::user()->points ?? 0); ?> نقطة</span>
        </div>
      </div>

      <div class="search-wrap">
        <i class="ri-search-line search-icon"></i>
        <input type="text" placeholder="ابحث عن الدروس والمسارات...">
      </div>
    </header>

    <!-- CONTENT -->
    <div class="content">
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success') || session('error') || session('warning')): ?>
        <div class="page-alert" style="margin: 0 24px 24px; padding: 18px 20px; border-radius: 18px; background: var(--theme-surface); border: 1px solid var(--theme-border); color: var(--theme-text); box-shadow: var(--theme-shadow);">
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <strong style="color: var(--theme-success);">نجاح:</strong> <?php echo e(session('success')); ?>

          <?php elseif(session('warning')): ?>
            <strong style="color: var(--theme-pending);">تنبيه:</strong> <?php echo e(session('warning')); ?>

          <?php else: ?>
            <strong style="color: var(--theme-danger);">خطأ:</strong> <?php echo e(session('error')); ?>

          <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      <!-- PAGE: لوحة التحكم -->
      <div class="page active" id="page-home">

        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-avatar <?php echo e(Auth::user()->avatar_url ? '' : 'placeholder'); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->avatar_url): ?>
              <img src="<?php echo e(asset('storage/' . Auth::user()->avatar_url)); ?>" alt="<?php echo e(Auth::user()->name); ?>" />
            <?php else: ?>
              <?php echo e(mb_substr(Auth::user()->name, 0, 1)); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </div>

          <div class="profile-info">
            <h1><?php echo e(Auth::user()->name); ?></h1>
            <p>طالب متفاني في طلب العلم</p>
            <div class="profile-badges">
              <div class="badge-item">
                <i class="ri-fire-fill"></i>
                متميز منذ <?php echo e($myStreak->current_streak ?? 0); ?> أيام
              </div>
              <div class="badge-item">
                <i class="ri-award-fill"></i>
                <?php echo e(count($myCertificates ?? [])); ?> شهادات
              </div>
              <div class="badge-item">
                <i class="ri-star-fill"></i>
                <?php echo e(Auth::user()->points ?? 0); ?> نقطة
              </div>
            </div>
          </div>

          <div class="profile-stats">
            <div class="stat-card">
              <div class="stat-number"><?php echo e(count($enrolledCourses ?? [])); ?></div>
              <div class="stat-label">مسارات نشطة</div>
            </div>
            <div class="stat-card">
              <div class="stat-number"><?php echo e(count(array_filter($courseProgress ?? [], fn($p) => $p >= 100))); ?></div>
              <div class="stat-label">مكتملة</div>
            </div>
            <div class="stat-card">
              <div class="stat-number"><?php echo e($myStreak->current_streak ?? 0); ?></div>
              <div class="stat-label">أيام متواصلة</div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
          <div class="stat-box" style="--delay: 0; animation-delay: 0.1s;">
            <div class="stat-icon"><i class="ri-fire-fill"></i></div>
            <div class="stat-content">
              <h3><?php echo e($myStreak->current_streak ?? 0); ?></h3>
              <p>سلسلة التميز الحالية</p>
            </div>
          </div>

          <div class="stat-box" style="--delay: 1; animation-delay: 0.2s;">
            <div class="stat-icon"><i class="ri-flashlight-fill"></i></div>
            <div class="stat-content">
              <h3><?php echo e(Auth::user()->points ?? 0); ?></h3>
              <p>إجمالي النقاط المكتسبة</p>
            </div>
          </div>

          <div class="stat-box" style="--delay: 2; animation-delay: 0.3s;">
            <div class="stat-icon"><i class="ri-graduation-cap-fill"></i></div>
            <div class="stat-content">
              <h3><?php echo e(count($enrolledCourses ?? [])); ?></h3>
              <p>مسارات تعليمية نشطة</p>
            </div>
          </div>
        </div>

        <!-- Courses Section -->
        <h2 class="section-title">
          <i class="ri-book-line"></i>
          مساراتك التعليمية
        </h2>

        <div class="courses-grid">
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $enrolledCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e(route('student.course.show', $course->id)); ?>" style="text-decoration: none;">
              <div class="course-card">
                <div class="course-header">
                  <div class="course-icon"><i class="ri-book-open-line"></i></div>
                  <div class="course-progress">
                    <span><?php echo e($courseProgress[$course->id] ?? 0); ?>%</span>
                  </div>
                </div>

                <h3 class="course-title"><?php echo e($course->name); ?></h3>
                <p class="course-desc"><?php echo e(mb_substr($course->description ?? 'مسار تعليمي شامل', 0, 80)); ?>...</p>

                <div class="progress-bar-wrapper">
                  <div class="progress-label">
                    <span>التقدم</span>
                    <span style="color: var(--gold);"><?php echo e($courseProgress[$course->id] ?? 0); ?>%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo e($courseProgress[$course->id] ?? 0); ?>%"></div>
                  </div>
                </div>

                <button class="course-btn">
                  <i class="ri-play-circle-line"></i>
                  استمر في التعلم
                </button>
              </div>
            </a>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-secondary);">
              <p>لا توجد مسارات مسجلة حالياً</p>
            </div>
          <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Exams Section -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($availableExams)): ?>
          <h2 class="section-title">
            <i class="ri-survey-line"></i>
            الاختبارات المتاحة
          </h2>

          <div class="exams-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
              <div class="exam-card">
                <div class="exam-icon"><i class="ri-file-list-3-line"></i></div>
                <h3 class="exam-title"><?php echo e($exam['name']); ?></h3>
                <div class="exam-meta">
                  <span><i class="ri-timer-line"></i> <?php echo e($exam['duration']); ?> دقيقة</span>
                  <span><i class="ri-questionnaire-line"></i> <?php echo e($exam['questions_count']); ?> سؤال</span>
                </div>
                <a href="<?php echo e(route('student.exam.show', $exam['id'])); ?>" class="exam-btn">
                  <i class="ri-play-circle-line"></i>
                  ابدأ الاختبار
                </a>
              </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
          </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Messaging Section -->
        <h2 class="section-title">
          <i class="ri-message-2-line"></i>
          المراسلة
        </h2>

        <div class="messaging-grid">
          <div class="messaging-card">
            <div class="messaging-icon"><i class="ri-inbox-line"></i></div>
            <div class="messaging-label">صندوق الوارد</div>
            <div class="messaging-value"><?php echo e(Auth::user()->receivedMessages()->whereNull('read_at')->count()); ?> رسالة غير مقروءة</div>
            <a href="<?php echo e(route('student.messaging')); ?>" class="messaging-link">عرض المحادثات <i class="ri-arrow-left-s-line"></i></a>
          </div>

          <div class="messaging-card">
            <div class="messaging-icon"><i class="ri-mail-send-line"></i></div>
            <div class="messaging-label">إرسال رسالة</div>
            <div class="messaging-text">تواصل مع معلمك فوراً واستفسر عن أي درس أو واجب.</div>
            <a href="<?php echo e(route('student.messaging')); ?>" class="messaging-link">ابدأ الآن <i class="ri-arrow-left-s-line"></i></a>
          </div>

          <div class="messaging-card">
            <div class="messaging-icon"><i class="ri-chat-history-line"></i></div>
            <div class="messaging-label">سجل المحادثات</div>
            <div class="messaging-text">اطلع على جميع الرسائل السابقة وتابع أبعاد التواصل.</div>
            <a href="<?php echo e(route('student.messaging')); ?>" class="messaging-link">عرض السجل <i class="ri-arrow-left-s-line"></i></a>
          </div>

          <div class="messaging-card highlight">
            <div class="messaging-icon"><i class="ri-chat-3-line"></i></div>
            <div class="messaging-label">تنبيهات فورية</div>
            <div class="messaging-text">كن دائماً على اطلاع برسائل الدعم والأخبار التعليمية.</div>
            <a href="<?php echo e(route('student.messaging')); ?>" class="messaging-link">تفاصيل</a>
          </div>
        </div>

        <!-- Leaderboard Section -->
        <h2 class="section-title">
          <i class="ri-trophy-line"></i>
          ترتيب المتفوقين
        </h2>

        <div class="leaderboard-wrap">
          <div class="leaderboard-card">
            <div class="leaderboard-title">الأفضل اليوم</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topStudents->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
              <div class="lb-item">
                <div class="lb-rank"><?php echo e($index + 1); ?></div>
                <div class="lb-name"><?php echo e($student->name); ?></div>
                <div class="lb-points"><?php echo e($student->points); ?> XP</div>
              </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
              <div style="padding: 20px; text-align: center; color: var(--text-secondary);">
                لا توجد بيانات
              </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </div>

          <div class="leaderboard-card">
            <div class="leaderboard-title">الأفضل هذا الأسبوع</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topStudents->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
              <div class="lb-item">
                <div class="lb-rank"><?php echo e($index + 1); ?></div>
                <div class="lb-name"><?php echo e($student->name); ?></div>
                <div class="lb-points"><?php echo e($student->points); ?> XP</div>
              </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
              <div style="padding: 20px; text-align: center; color: var(--text-secondary);">
                لا توجد بيانات
              </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('nb-home').addEventListener('click', function () {
      switchPage('home');
    });

    document.getElementById('dashThemeToggle').addEventListener('click', function () {
      if (typeof window.toggleThemeUniversal === 'function') {
        window.toggleThemeUniversal();
      }
    });
  });

  function switchPage(pageName) {
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => page.classList.remove('active'));
    document.getElementById('page-' + pageName).classList.add('active');

    const navBtns = document.querySelectorAll('.nav-btn');
    navBtns.forEach(btn => btn.classList.remove('active'));
    document.getElementById('nb-' + pageName).classList.add('active');
  }

  // Student Sidebar Hamburger Toggle
  (function() {
    var sidebar   = document.getElementById('studentSidebar');
    var hamburger = document.getElementById('studentHamburger');
    var backdrop  = document.getElementById('studentSidebarBackdrop');
    function openSidebar() {
      if (sidebar)  sidebar.classList.add('sidebar-open');
      if (backdrop) backdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
      if (sidebar)  sidebar.classList.remove('sidebar-open');
      if (backdrop) backdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
    if (hamburger) hamburger.addEventListener('click', function(e) {
      e.stopPropagation();
      sidebar && sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    });
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeSidebar();
    });
  })();
</script>

@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>
