<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ط§ظ„ط£ط³ط¦ظ„ط© - ظ„ظˆط­ط© طھط­ظƒظ… ط§ظ„ظ…ط¹ظ„ظ…</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
          --gold: #C4963A;
          --gold-dark: #A07A28;
          --gold-light: rgba(196,150,58,0.12);
          --success: #34C759;
          --danger: #FF3B30;
          --warning: #FF9500;
          --info: #C6A675;
          --primary-light: #F4F5FA;
          --primary-dark: #121212;
          --card-bg: #FFFFFF;
          --text-primary: #1a1a1a;
          --text-secondary: #666666;
          --text-muted: #999999;
          --border: rgba(0,0,0,0.04);
          --sidebar-bg: #F8F9FB;
          --topbar-h: 64px;
          --radius-lg: 16px;
          --radius-md: 12px;
          --shadow: 0 4px 24px rgba(0,0,0,0.04);
          --shadow-hover: 0 8px 32px rgba(0,0,0,0.08);
          --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        body[data-theme="dark"],
        html[data-theme="dark"] {
          --card-bg: #1e1e1e;
          --sidebar-bg: #181818;
          --text-primary: #f0f0f0;
          --primary-light: #1a1a1a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
          font-family: 'Tajawal', sans-serif;
          background: var(--primary-light);
          color: var(--text-primary);
          transition: var(--transition);
        }
        .container { display: flex; height: 100vh; }

        /* SIDEBAR */
        .sidebar {
          width: 260px;
          background: linear-gradient(180deg, var(--sidebar-bg) 0%, rgba(196,150,58,0.02) 100%);
          padding: 20px;
          overflow-y: auto;
          border-right: 1px solid var(--border);
          position: sticky;
          top: 0;
          height: 100vh;
          z-index: 1000;
        }
        .sidebar-logo {
          padding: 30px 20px 24px;
          text-align: center;
          margin-bottom: 10px;
          position: relative;
          overflow: hidden;
        }
        .sidebar-logo::after {
          content: '';
          position: absolute;
          bottom: 0;
          left: 20px;
          right: 20px;
          height: 1px;
          background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .logo-icon {
          width: 48px;
          height: 48px;
          margin: 0 auto 12px;
          color: var(--gold);
          font-size: 38px;
          display: flex;
          align-items: center;
          justify-content: center;
          background: var(--gold-light);
          border-radius: 14px;
          position: relative;
          transition: var(--transition);
          box-shadow: 0 0 16px rgba(196,150,58,0.2);
          animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
          0%, 100% { transform: translateY(0px); }
          50% { transform: translateY(-8px); }
        }
        .logo-name {
          font-size: 19px;
          font-weight: 800;
          color: var(--gold);
          position: relative;
          z-index: 1;
        }
        .logo-sub {
          font-size: 11px;
          font-weight: 600;
          color: var(--text-muted);
          margin-top: 4px;
          position: relative;
          z-index: 1;
        }
        .nav-btn {
          width: 100%;
          padding: 12px 14px;
          border: none;
          background: transparent;
          color: var(--text-secondary);
          cursor: pointer;
          border-radius: var(--radius-md);
          font-family: 'Tajawal', sans-serif;
          font-size: 14px;
          font-weight: 600;
          margin-bottom: 8px;
          display: flex;
          align-items: center;
          gap: 10px;
          transition: var(--transition);
          position: relative;
          overflow: hidden;
        }
        .nav-btn::before {
          content: '';
          position: absolute;
          top: 0;
          right: 0;
          width: 0;
          height: 100%;
          background: linear-gradient(90deg, transparent 0%, rgba(196,150,58,0.1) 100%);
          transition: var(--transition);
          z-index: -1;
        }
        .nav-btn:hover::before { width: 100%; }
        .nav-btn:hover { color: var(--gold); }
        .nav-btn.active {
          background: linear-gradient(135deg, var(--gold), var(--gold-dark));
          color: #fff;
        }
        .nav-btn.logout {
          color: #FF6C63;
          background: rgba(255,59,48,0.08);
          border: 1px solid rgba(255,59,48,0.18);
          font-weight: 700;
        }
        .nav-btn.logout:hover {
          background: rgba(255,59,48,0.16);
        }
        .nav-btn i { font-size: 18px; }
        .logout-btn {
          width: 100%;
          margin-top: auto;
          padding: 12px 14px;
          background: var(--danger);
          color: #fff;
          border: none;
          border-radius: var(--radius-md);
          font-family: 'Tajawal', sans-serif;
          font-size: 14px;
          font-weight: 600;
          cursor: pointer;
          transition: var(--transition);
          display: flex;
          align-items: center;
          gap: 8px;
        }
        .logout-btn:hover { background: #E63527; transform: translateY(-2px); }

        /* TOPBAR */
        .topbar {
          height: var(--topbar-h);
          background: transparent;
          border-bottom: 1px solid var(--border);
          padding: 0 24px;
          display: flex;
          align-items: center;
          justify-content: space-between;
          position: sticky;
          top: 0;
          z-index: 999;
          animation: slideDown 0.5s ease;
        }
        @keyframes slideDown {
          from { opacity: 0; transform: translateY(-10px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .topbar-left { display: flex; align-items: center; gap: 14px; flex: 1; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .search-wrap {
          flex: 1;
          max-width: 400px;
          position: relative;
        }
        .search-wrap input {
          width: 100%;
          padding: 12px 45px 12px 16px;
          background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.02));
          border: 1px solid rgba(0,0,0,0.04);
          border-radius: 40px;
          font-family: 'Tajawal', sans-serif;
          font-size: 14px;
          color: var(--text-primary);
          outline: none;
          box-shadow: var(--shadow);
          transition: var(--transition);
        }
        .search-wrap input:focus {
          border-color: var(--gold);
          box-shadow: 0 0 0 3px var(--gold-light);
          transform: scale(1.02);
        }
        .search-icon {
          position: absolute;
          right: 18px;
          top: 50%;
          transform: translateY(-50%);
          color: var(--text-muted);
          font-size: 18px;
        }
        .icon-btn {
          width: 42px;
          height: 42px;
          border: 1px solid rgba(0,0,0,0.04);
          border-radius: 50%;
          background: var(--card-bg);
          color: var(--text-secondary);
          font-size: 19px;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: var(--transition);
          box-shadow: var(--shadow);
          position: relative;
          overflow: hidden;
        }
        .icon-btn::before {
          content: '';
          position: absolute;
          width: 0;
          height: 0;
          background: var(--gold);
          border-radius: 50%;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          transition: var(--transition);
          z-index: -1;
          opacity: 0.15;
        }
        .icon-btn:hover::before { width: 100%; height: 100%; }
        .icon-btn:hover {
          color: var(--gold);
          border-color: var(--gold);
          transform: scale(1.08);
          box-shadow: 0 0 16px rgba(196,150,58,0.3);
        }
        .user-profile-btn {
          display: flex;
          align-items: center;
          gap: 10px;
          padding: 6px 14px 6px 6px;
          background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.04));
          border: 1px solid rgba(196,150,58,0.1);
          border-radius: 40px;
          box-shadow: var(--shadow);
          cursor: pointer;
          transition: var(--transition);
        }
        .user-profile-btn:hover {
          border-color: var(--gold);
          box-shadow: 0 4px 16px rgba(196,150,58,0.25);
          transform: translateY(-2px);
        }
        .u-av {
          width: 34px;
          height: 34px;
          background: linear-gradient(135deg, var(--gold), var(--gold-dark));
          color: #fff;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 14px;
          font-weight: 900;
          box-shadow: 0 4px 12px rgba(196,150,58,0.25);
        }
        .u-name { font-size: 12px; font-weight: 800; background: linear-gradient(135deg, #C4963A, #A07A28); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }

        /* MAIN */
        .main {
          flex: 1;
          display: flex;
          flex-direction: column;
          overflow-y: auto;
        }
        .content {
          flex: 1;
          padding: 24px;
          max-width: 1400px;
          margin: 0 auto;
          width: 100%;
        }

        /* ANIMATIONS */
        @keyframes float {
          0%, 100% { transform: translateY(0px); }
          50% { transform: translateY(-8px); }
        }
        @keyframes fadeUp {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }

        /* HEADER */
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

        /* FILTERS */
        .filter-bar {
          display: flex;
          gap: 12px;
          margin-bottom: 20px;
          flex-wrap: wrap;
          align-items: center;
        }
        .filter-select {
          padding: 10px 14px;
          background: var(--card-bg);
          border: 1px solid var(--border);
          border-radius: var(--radius-md);
          font-family: 'Tajawal', sans-serif;
          font-size: 14px;
          color: var(--text-primary);
          cursor: pointer;
          transition: var(--transition);
        }
        .filter-select:hover,
        .filter-select:focus {
          border-color: var(--gold);
          box-shadow: 0 0 0 3px var(--gold-light);
        }

        /* QUESTIONS TABLE/LIST */
        .questions-list {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }
        .question-item {
          background: var(--card-bg);
          border: 1px solid var(--border);
          border-radius: var(--radius-lg);
          padding: 16px;
          display: flex;
          align-items: start;
          gap: 14px;
          transition: var(--transition);
          animation: fadeUp 0.5s ease;
          position: relative;
          overflow: hidden;
        }
        .question-item::before {
          content: '';
          position: absolute;
          left: 0;
          top: 0;
          width: 4px;
          height: 100%;
          background: var(--gold);
          opacity: 0;
          transition: var(--transition);
        }
        .question-item:hover {
          border-color: var(--gold);
          box-shadow: var(--shadow-hover);
          transform: translateX(-4px);
        }
        .question-item:hover::before {
          opacity: 1;
        }

        .question-icon {
          width: 40px;
          height: 40px;
          background: var(--gold-light);
          color: var(--gold);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 18px;
          flex-shrink: 0;
          transition: var(--transition);
        }
        .question-item:hover .question-icon {
          transform: scale(1.1) rotate(10deg);
        }

        .question-content {
          flex: 1;
        }
        .question-text {
          font-size: 14px;
          font-weight: 700;
          color: var(--text-primary);
          margin-bottom: 6px;
          line-height: 1.4;
        }

        .question-meta {
          display: flex;
          gap: 16px;
          flex-wrap: wrap;
          font-size: 12px;
          color: var(--text-muted);
        }
        .meta-item {
          display: flex;
          align-items: center;
          gap: 4px;
        }

        .question-type {
          display: inline-flex;
          align-items: center;
          gap: 4px;
          padding: 4px 10px;
          background: var(--gold-light);
          color: var(--gold);
          border-radius: 12px;
          font-size: 11px;
          font-weight: 700;
        }
        .question-type.mcq { background: rgba(198,166,117,0.1); color: #C6A675; }
        .question-type.essay { background: rgba(52,199,89,0.1); color: #34C759; }
        .question-type.tf { background: rgba(255,149,0,0.1); color: #FF9500; }

        .question-actions {
          display: flex;
          gap: 8px;
          margin-right: 12px;
        }
        .action-btn {
          width: 32px;
          height: 32px;
          background: transparent;
          border: 1px solid var(--gold-light);
          color: var(--gold);
          border-radius: 6px;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 14px;
          transition: var(--transition);
        }
        .action-btn:hover {
          background: var(--gold);
          color: #fff;
          border-color: var(--gold);
          transform: scale(1.08);
        }

        /* BUTTON */
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
        }
        .btn:hover { background: var(--gold-dark); transform: translateY(-2px); }

        /* ===== HIDE SCROLLBAR ===== */
        ::-webkit-scrollbar {
          display: none;
        }

        .sidebar,
        .main,
        .content {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }
      </style>
</head>
<body>
<div class="container">
  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon"><i class="ri-book-read-fill"></i></div>
      <div class="logo-name">ط¥ط¬ظ„ط§ظ„</div>
      <div class="logo-sub">ط§ظ„ظ…ظ†طµط© ط§ظ„طھط¹ظ„ظٹظ…ظٹط©</div>
    </div>
    <nav style="display: flex; flex-direction: column; height: 100%;">
      <button class="nav-btn" data-href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i> ط§ظ„ط±ط¦ظٹط³ظٹط©</button>
      <button class="nav-btn" data-href="{{ route('teacher.courses') }}"><i class="ri-book-2-line"></i> ط§ظ„ظ…ط³ط§ط±ط§طھ</button>
      <button class="nav-btn" data-href="{{ route('teacher.exams') }}"><i class="ri-file-list-line"></i> ط§ظ„ط§ط®طھط¨ط§ط±ط§طھ</button>
      <button class="nav-btn" data-href="{{ route('teacher.analytics') }}"><i class="ri-bar-chart-2-line"></i> ظ†ط³ط¨ط© ط§ظ„ط¥ظ†ط¬ط§ط²</button>
      <button class="nav-btn" data-href="{{ route('teacher.students') }}"><i class="ri-team-line"></i> ط·ظ„ط§ط¨ظٹ</button>
      <button class="nav-btn active" data-href="{{ route('teacher.questions.manage') }}"><i class="ri-chat-3-line"></i> ط§ظ„ط£ط³ط¦ظ„ط© ظˆط§ظ„ط§ط³طھظپط³ط§ط±ط§طھ</button>
      <button class="nav-btn" data-href="{{ route('teacher.messaging') }}"><i class="ri-message-2-line"></i> ط§ظ„ظ…ط±ط§ط³ظ„ط©</button>
      <form action="{{ route('teacher.logout') }}" method="POST" style="margin-top: auto; display: flex; width: 100%;">
        @csrf
        <button type="submit" class="nav-btn logout" style="width: 100%; margin: 0;"><i class="ri-logout-box-line"></i> طھط³ط¬ظٹظ„ ط§ظ„ط®ط±ظˆط¬</button>
      </form>
    </nav>
  </div>

  <!-- MAIN -->
  <div class="main">
    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-left">
        <div class="search-wrap">
          <input type="text" placeholder="ط§ط¨ط­ط« ط¹ظ† ط³ط¤ط§ظ„...">
          <i class="ri-search-line search-icon"></i>
        </div>
      </div>
      <div class="topbar-right">
        <button class="icon-btn" id="notificationBtn" title="ط§ظ„ط¥ط´ط¹ط§ط±ط§طھ"><i class="ri-notification-3-line"></i></button>
        <button class="icon-btn" id="darkBtn" ><i class="ri-moon-line"></i></button>
        <button class="user-profile-btn">
          <div class="u-av">ظ…</div>
          <div style="text-align: right;"><div class="u-name">ط§ظ„ظ…ط¹ظ„ظ…</div><div style="font-size:10px; color: var(--text-muted);">ظ…ط¹ظ„ظ…</div></div>
        </button>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
        <div class="page-header" style="margin-bottom: 0;">
          <h1 class="page-title">ط§ظ„ط£ط³ط¦ظ„ط©</h1>
          <p class="page-subtitle">ط¨ظ†ظƒ ط§ظ„ط£ط³ط¦ظ„ط© - ط¥ظ†ط´ط§ط، ظˆط¥ط¯ط§ط±ط© ط§ظ„ط£ط³ط¦ظ„ط©</p>
        </div>
        <button class="btn"><i class="ri-add-line"></i> ط³ط¤ط§ظ„ ط¬ط¯ظٹط¯</button>
      </div>

      <!-- FILTERS -->
      <div class="filter-bar">
        <select class="filter-select">
          <option>ط¬ظ…ظٹط¹ ط§ظ„ظ…ط³ط§ظ‚ط§طھ</option>
          <option>ط§ظ„ظ‚ط±ط¢ظ† ط§ظ„ظƒط±ظٹظ…</option>
          <option>ط§ظ„ط­ط¯ظٹط« ط§ظ„ط´ط±ظٹظپ</option>
          <option>ط§ظ„ظپظ‚ظ‡ ط§ظ„ط¥ط³ظ„ط§ظ…ظٹ</option>
        </select>
        <select class="filter-select">
          <option>ط¬ظ…ظٹط¹ ط§ظ„ط£ظ†ظˆط§ط¹</option>
          <option>ط§ط®طھظٹط§ط± ظ…ظ† ظ…طھط¹ط¯ط¯</option>
          <option>طµط­/ط®ط·ط£</option>
          <option>ط¥ط¬ط§ط¨ط© ظ‚طµظٹط±ط©</option>
        </select>
        <select class="filter-select">
          <option>ط¬ظ…ظٹط¹ ط§ظ„ظ…ط³طھظˆظٹط§طھ</option>
          <option>ط³ظ‡ظ„</option>
          <option>ظ…طھظˆط³ط·</option>
          <option>طµط¹ط¨</option>
        </select>
      </div>

      <!-- QUESTIONS LIST -->
      <div class="questions-list">
        <div class="question-item">
          <div class="question-icon"><i class="ri-question-mark"></i></div>
          <div class="question-content">
            <div class="question-text">ظ…ط§ ظ‡ظˆ ط£ظˆظ„ ط³ظˆط±ط© ظپظٹ ط§ظ„ظ‚ط±ط¢ظ† ط§ظ„ظƒط±ظٹظ…طں</div>
            <div class="question-meta">
              <span class="meta-item"><i class="ri-book-mark-line"></i>ط§ظ„ظ‚ط±ط¢ظ† ط§ظ„ظƒط±ظٹظ…</span>
              <span class="meta-item"><i class="ri-time-line"></i>3 ط³ط§ط¹ط§طھ</span>
              <span class="meta-item"><i class="ri-eye-line"></i>142 ظ…ط±ط©</span>
              <span class="question-type mcq">ط§ط®طھظٹط§ط± ظ…ظ† ظ…طھط¹ط¯ط¯</span>
            </div>
          </div>
          <div class="question-actions">
            <button class="action-btn" title="طھط¹ط¯ظٹظ„"><i class="ri-edit-line"></i></button>
            <button class="action-btn" title="ط­ط°ظپ"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>

        <div class="question-item">
          <div class="question-icon"><i class="ri-question-mark"></i></div>
          <div class="question-content">
            <div class="question-text">ظƒظ… ط¹ط¯ط¯ ط³ظˆط± ط§ظ„ظ‚ط±ط¢ظ† ط§ظ„ظƒط±ظٹظ…طں</div>
            <div class="question-meta">
              <span class="meta-item"><i class="ri-book-mark-line"></i>ط§ظ„ظ‚ط±ط¢ظ† ط§ظ„ظƒط±ظٹظ…</span>
              <span class="meta-item"><i class="ri-time-line"></i>24 ط³ط§ط¹ط©</span>
              <span class="meta-item"><i class="ri-eye-line"></i>89 ظ…ط±ط©</span>
              <span class="question-type" style="background: rgba(255,149,0,0.1); color: #FF9500;">طµط­/ط®ط·ط£</span>
            </div>
          </div>
          <div class="question-actions">
            <button class="action-btn" title="طھط¹ط¯ظٹظ„"><i class="ri-edit-line"></i></button>
            <button class="action-btn" title="ط­ط°ظپ"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>

        <div class="question-item">
          <div class="question-icon"><i class="ri-question-mark"></i></div>
          <div class="question-content">
            <div class="question-text">ط§ط´ط±ط­ ظ…ط¹ظ†ظ‰ ظƒظ„ظ…ط© "ط§ظ„طھط¬ظˆظٹط¯" ظپظٹ ط§ظ„ط§طµط·ظ„ط§ط­ ط§ظ„ط´ط±ط¹ظٹطں</div>
            <div class="question-meta">
              <span class="meta-item"><i class="ri-book-mark-line"></i>ط§ظ„طھط¬ظˆظٹط¯ ظˆط§ظ„ظ‚ط±ط§ط،ط§طھ</span>
              <span class="meta-item"><i class="ri-time-line"></i>5 ط£ظٹط§ظ…</span>
              <span class="meta-item"><i class="ri-eye-line"></i>45 ظ…ط±ط©</span>
              <span class="question-type essay" style="background: rgba(52,199,89,0.1); color: #34C759;">ط¥ط¬ط§ط¨ط© ظ‚طµظٹط±ط©</span>
            </div>
          </div>
          <div class="question-actions">
            <button class="action-btn" title="طھط¹ط¯ظٹظ„"><i class="ri-edit-line"></i></button>
            <button class="action-btn" title="ط­ط°ظپ"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>

        <div class="question-item">
          <div class="question-icon"><i class="ri-question-mark"></i></div>
          <div class="question-content">
            <div class="question-text">ظ…ظ† ظ‡ظˆ ط§ظ„طµط­ط§ط¨ظٹ ط§ظ„ط°ظٹ ظ„ظ… ظٹط³ط¬ط¯ ظ„ظ„ط£طµظ†ط§ظ… ظ‚ط؟</div>
            <div class="question-meta">
              <span class="meta-item"><i class="ri-book-mark-line"></i>ط§ظ„ط­ط¯ظٹط« ط§ظ„ط´ط±ظٹظپ</span>
              <span class="meta-item"><i class="ri-time-line"></i>6 ط³ط§ط¹ط§طھ</span>
              <span class="meta-item"><i class="ri-eye-line"></i>78 ظ…ط±ط©</span>
              <span class="question-type mcq">ط§ط®طھظٹط§ط± ظ…ظ† ظ…طھط¹ط¯ط¯</span>
            </div>
          </div>
          <div class="question-actions">
            <button class="action-btn" title="طھط¹ط¯ظٹظ„"><i class="ri-edit-line"></i></button>
            <button class="action-btn" title="ط­ط°ظپ"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>

        <div class="question-item">
          <div class="question-icon"><i class="ri-question-mark"></i></div>
          <div class="question-content">
            <div class="question-text">ظ…ط§ ظ‡ظٹ ط´ط±ظˆط· طµط­ط© ط§ظ„طµظ„ط§ط© ظپظٹ ط§ظ„ظپظ‚ظ‡ ط§ظ„ط¥ط³ظ„ط§ظ…ظٹطں</div>
            <div class="question-meta">
              <span class="meta-item"><i class="ri-book-mark-line"></i>ط§ظ„ظپظ‚ظ‡ ط§ظ„ط¥ط³ظ„ط§ظ…ظٹ</span>
              <span class="meta-item"><i class="ri-time-line"></i>2 ظٹظˆظ…</span>
              <span class="meta-item"><i class="ri-eye-line"></i>156 ظ…ط±ط©</span>
              <span class="question-type essay" style="background: rgba(52,199,89,0.1); color: #34C759;">ط¥ط¬ط§ط¨ط© ظ‚طµظٹط±ط©</span>
            </div>
          </div>
          <div class="question-actions">
            <button class="action-btn" title="طھط¹ط¯ظٹظ„"><i class="ri-edit-line"></i></button>
            <button class="action-btn" title="ط­ط°ظپ"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>

        <div class="question-item">
          <div class="question-icon"><i class="ri-question-mark"></i></div>
          <div class="question-content">
            <div class="question-text">ظ‡ظ„ ط§ظ„ط­ط¬ ظپط±ط¶ ط¹ظ„ظ‰ ظƒظ„ ظ…ط³ظ„ظ… ظ…ط±ط© ظˆط§ط­ط¯ط©طں</div>
            <div class="question-meta">
              <span class="meta-item"><i class="ri-book-mark-line"></i>ط§ظ„ظپظ‚ظ‡ ط§ظ„ط¥ط³ظ„ط§ظ…ظٹ</span>
              <span class="meta-item"><i class="ri-time-line"></i>1 ط³ط§ط¹ط©</span>
              <span class="meta-item"><i class="ri-eye-line"></i>234 ظ…ط±ط©</span>
              <span class="question-type" style="background: rgba(255,149,0,0.1); color: #FF9500;">طµط­/ط®ط·ط£</span>
            </div>
          </div>
          <div class="question-actions">
            <button class="action-btn" title="طھط¹ط¯ظٹظ„"><i class="ri-edit-line"></i></button>
            <button class="action-btn" title="ط­ط°ظپ"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleDark() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';

    html.setAttribute('data-theme', newTheme);
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    const btn = document.getElementById('darkBtn');
    if (btn) {
      btn.innerHTML = newTheme === 'dark' ? '<i class="ri-sun-line"></i>' : '<i class="ri-moon-line"></i>';
    }
  }
  
  const theme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', theme);
  if(theme === 'dark') {
    const btn = document.getElementById('darkBtn');
    if (btn) {
      btn.innerHTML = '<i class="ri-sun-line"></i>';
    }
  }

  // Event delegation for sidebar nav buttons (eliminates inline onclick)
  document.querySelector('.sidebar nav').addEventListener('click', function (e) {
    const btn = e.target.closest('.nav-btn');
    if (btn && btn.dataset.href) {
      window.location.href = btn.dataset.href;
    }
  });

  // Dark mode toggle via addEventListener (eliminates inline onclick)
  document.getElementById('darkBtn').addEventListener('click', toggleDark);
</script>
@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>










