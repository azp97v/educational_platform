<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>تعديل {{ $course->name ?? 'المسار' }} - معلم | إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('app-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
    })();
  </script>
  <style>
    :root { --sidebar-w: 300px; --topbar-h: 70px; }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    html { min-height: 100%; }

    body {
      font-family: 'Tajawal', sans-serif;
      min-height: 100vh;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 20%),
                  radial-gradient(circle at bottom right, rgba(255,214,122,0.08), transparent 18%),
                  linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%);
      color: var(--text-primary);
      transition: background 0.3s, color 0.3s;
      overflow-x: hidden;
      overflow-y: auto;
      position: relative;
    }

    body::before {
      content: '';
      position: fixed;
      top: 18px;
      left: 18px;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      background: rgba(255,214,122,0.16);
      filter: blur(84px);
      pointer-events: none;
      z-index: 0;
    }

    .app {
      display: flex;
      min-height: 100vh;
      position: relative;
      z-index: 2;
    }

    .sidebar {
      width: var(--sidebar-w);
      background: var(--sidebar-bg, var(--theme-surface));
      position: fixed;
      right: 18px;
      top: 24px;
      bottom: 24px;
      display: flex;
      flex-direction: column;
      z-index: 200;
      box-shadow: -16px 28px 70px rgba(0,0,0,0.28);
      border-left: 1px solid rgba(255,214,122,0.16);
      border-top-left-radius: 32px;
      border-bottom-left-radius: 32px;
      backdrop-filter: blur(24px);
      padding: 28px 22px;
      gap: 20px;
    }

    .sidebar::before {
      content: '';
      position: absolute;
      top: 24px;
      right: 24px;
      width: 120px;
      height: 120px;
      background: radial-gradient(circle at top right, rgba(255,214,122,0.12), transparent 55%);
      pointer-events: none;
      filter: blur(24px);
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
      width: 88px;
      height: 88px;
      margin: 0 auto 12px;
      color: var(--gold);
      font-size: 38px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--gold-light);
      border-radius: 14px;
      transition: var(--transition);
      box-shadow: 0 0 16px rgba(196,150,58,0.18);
      animation: float 3s ease-in-out infinite;
      overflow: hidden;
    }

    .logo-icon img { width: 100%; height: 100%; object-fit: contain; border-radius: 30px; display: block; }

    .logo-name { font-size: 18px; font-weight: 800; color: var(--gold); position: relative; z-index: 1; }
    .logo-sub { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-top: 4px; position: relative; z-index: 1; letter-spacing: 0.02em; }

    .sidebar-nav {
      flex: 1;
      display: grid;
      gap: 14px;
      overflow-y: auto;
      padding: 0;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .sidebar-nav::-webkit-scrollbar {
      display: none;
      width: 0;
      height: 0;
    }

    .nav-btn {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      width: 100%;
      padding: 16px 22px;
      min-height: 62px;
      background: rgba(255,255,255,0.06);
      border-radius: 24px;
      border: 1px solid rgba(255,255,255,0.08);
      color: var(--text-muted);
      font-family: 'Tajawal', sans-serif;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      transition: var(--transition);
      backdrop-filter: blur(14px);
      cursor: pointer;
      overflow: hidden;
    }

    .nav-btn i { font-size: 20px; color: rgba(255,214,122,0.92); flex-shrink: 0; }
    .nav-btn span { color: inherit; flex: 1; }
    .nav-btn:hover { background: rgba(255,214,122,0.08); color: #F9F9FB; border-color: rgba(255,214,122,0.18); }
    .nav-btn.active { background: rgba(255,214,122,0.18); color: #fff; border-color: rgba(255,214,122,0.32); box-shadow: 0 20px 40px rgba(255,214,122,0.14); backdrop-filter: blur(18px); }
    .nav-btn.logout { color: #FF6C63; background: rgba(255,59,48,0.08); border-color: rgba(255,59,48,0.18); }

    .sidebar-footer { margin-top: auto; }
    .sidebar-footer form { width: 100%; }

    .main {
      margin-right: calc(var(--sidebar-w) + 18px);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding-bottom: 32px;
      overflow-y: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      height: var(--topbar-h);
      background: transparent;
      backdrop-filter: blur(18px);
      border-bottom: 1px solid transparent;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      animation: slideDown 0.5s ease;
      box-shadow: none;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .topbar-left { display: flex; align-items: center; gap: 14px; }
    .user-profile-btn {
      display: inline-flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 14px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 999px;
      box-shadow: 0 10px 26px rgba(0,0,0,0.12);
      cursor: pointer;
      transition: var(--transition);
      overflow: hidden;
      min-width: 200px;
      text-decoration: none;
      color: inherit;
      position: relative;
    }
    .user-profile-btn::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, transparent 0%, rgba(196,150,58,0.1) 100%);
      opacity: 0;
      transition: var(--transition);
    }
    .user-profile-btn:hover { border-color: rgba(255,214,122,0.5); box-shadow: 0 18px 42px rgba(0,0,0,0.16); transform: translateY(-1px); background: rgba(255,255,255,0.14); }
    .user-profile-btn:hover::before { opacity: 1; }
    .user-profile-btn .u-info { text-align: right; z-index: 1; }
    .user-profile-btn .u-name { font-size: 12px; font-weight: 800; color: var(--text-primary); background: linear-gradient(135deg, var(--gold), var(--gold-dark)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .user-profile-btn .u-role { font-size: 10px; color: var(--text-muted); font-weight: 600; }
    .user-profile-btn .u-av {
      width: 30px;
      height: 30px;
      background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1));
      color: #111;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 900;
      box-shadow: 0 4px 10px rgba(255,214,122,0.24);
    }
    .user-profile-btn:hover .u-av { transform: scale(1.15) rotate(-5deg); box-shadow: 0 6px 16px rgba(196,150,58,0.35); }

    .icon-btn {
      width: 42px;
      height: 42px;
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 50%;
      background: var(--card-bg);
      color: var(--text-secondary);
      font-size: 19px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 12px 34px rgba(0,0,0,0.10);
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
      opacity: 0.12;
    }
    .icon-btn:hover::before { width: 100%; height: 100%; }
    .icon-btn:hover { color: var(--gold); border-color: var(--gold); transform: scale(1.05); }

    .content { padding: 32px; flex: 1; }

    .page-header { margin-bottom: 28px; }
    .page-header h1 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 6px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .page-header p { color: var(--text-secondary); font-size: 14px; margin-bottom: 8px; }
    .breadcrumb { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 13px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }

    .form-container { display: grid; grid-template-columns: 1.2fr 1fr; gap: 26px; align-items: start; }

    .form-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(255,255,255,0.08);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }
    .form-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle at 100% 0%, rgba(255,214,122,0.08), transparent);
      pointer-events: none;
    }
    .form-card:hover { box-shadow: 0 12px 32px rgba(255,214,122,0.12); border-color: rgba(255,214,122,0.18); }
    .form-card h2 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      z-index: 2;
    }
    .form-card h2 i { color: var(--gold); font-size: 22px; }

    .form-group { margin-bottom: 24px; position: relative; z-index: 2; }
    .form-group:last-child { margin-bottom: 0; }

    label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); font-size: 14px; }
    .required { color: var(--danger); margin-right: 4px; }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 12px;
      background: rgba(255,255,255,0.04);
      color: var(--text-primary);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      transition: var(--transition);
      outline: none;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus,
    select:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(255,214,122,0.14);
      transform: translateY(-1px);
    }

    textarea { resize: vertical; min-height: 100px; }

    .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

    .form-actions { display: flex; gap: 12px; margin-top: 30px; }

    .btn { padding: 12px 24px; border: none; border-radius: var(--radius-md); font-family: 'Tajawal', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: var(--transition); flex: 1; }
    .btn-primary { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #111; box-shadow: 0 4px 12px rgba(196,150,58,0.3); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(196,150,58,0.4); }
    .btn-secondary { background: rgba(255,255,255,0.06); color: var(--text-primary); border: 1px solid rgba(255,255,255,0.14); }
    .btn-secondary:hover { background: rgba(255,255,255,0.12); }
    .btn-danger { background: linear-gradient(135deg, var(--danger), #FF5A52); color: #fff; box-shadow: 0 4px 12px rgba(255,59,48,0.3); }
    .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(255,59,48,0.4); }

    .preview-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 26px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(255,255,255,0.08);
      position: sticky;
      top: 100px;
      transition: var(--transition);
      overflow: hidden;
      height: fit-content;
    }
    .preview-card::before {
      content: '';
      position: absolute;
      top: -20px;
      left: -20px;
      width: 180px;
      height: 180px;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.28), transparent 55%);
      filter: blur(24px);
      pointer-events: none;
      z-index: 1;
    }
    .preview-card > * { position: relative; z-index: 2; }
    .preview-card:hover { box-shadow: 0 12px 32px rgba(255,214,122,0.12); border-color: rgba(255,214,122,0.18); }

    .preview-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; position: relative; z-index: 2; }
    .preview-card h3 i { color: var(--gold); }

    .course-preview {
      background: linear-gradient(135deg, rgba(255,214,122,0.16), rgba(255,214,122,0.04));
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 16px;
      text-align: center;
      transition: var(--transition);
      position: relative;
      z-index: 2;
    }

    .course-preview-image {
      width: 100%;
      height: 140px;
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 48px;
      margin-bottom: 12px;
      box-shadow: 0 4px 12px rgba(196,150,58,0.2);
    }

    .course-preview h4 { font-size: 18px; font-weight: 800; margin-bottom: 10px; }
    .course-preview p { color: var(--text-secondary); line-height: 1.75; }

    .preview-info { display: flex; justify-content: space-between; gap: 10px; padding: 14px 0; border-top: 1px solid rgba(255,255,255,0.08); }
    .preview-info label { color: var(--text-muted); font-size: 13px; }
    .preview-info strong { color: var(--text-primary); }

    @media (max-width: 1200px) {
      .form-container { grid-template-columns: 1fr; }
      .preview-card { position: static; margin-top: 20px; }
    }

    @media (max-width: 768px) {
      .app { flex-direction: column; }
      .sidebar { position: relative; width: 100%; right: auto; top: auto; bottom: auto; border-left: none; border-radius: 0; box-shadow: none; border: none; margin-bottom: 18px; }
      .main { margin-right: 0; }
      .topbar { padding: 0 20px; }
      .content { padding: 20px; }
      .form-card { padding: 24px; }
      .preview-card { padding: 24px; }
      .form-actions { flex-direction: column; }
    }

    @media (max-width: 480px) {
      .topbar { flex-wrap: wrap; gap: 12px; padding: 16px; }
      .content { padding: 16px; }
      .course-preview-image { height: 160px; }
    }

    html, body, .sidebar, .sidebar-nav, .main {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    html::-webkit-scrollbar,
    body::-webkit-scrollbar,
    .sidebar::-webkit-scrollbar,
    .sidebar-nav::-webkit-scrollbar,
    .main::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }

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

    .topbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      height: var(--topbar-h);
      background: transparent;
      backdrop-filter: blur(18px);
      border-bottom: 1px solid transparent;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      animation: slideDown 0.5s ease;
      box-shadow: none;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .topbar-left, .topbar-right { display: flex; align-items: center; gap: 14px; }
    .topbar-right { flex: 1; justify-content: flex-end; }
    .search-wrap { width: 100%; max-width: 420px; position: relative; }
    .search-wrap input {
      width: 100%;
      padding: 12px 48px 12px 16px;
      background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.02));
      border: 1px solid rgba(255,255,255,0.10);
      border-radius: 40px;
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      color: var(--text-primary);
      outline: none;
      transition: var(--transition);
    }

    .search-wrap input::placeholder { color: var(--text-muted); }
    .search-wrap input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(255,214,122,0.14); }
    .search-icon { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px; pointer-events: none; }

    .notification-btn { position: relative; }
    .icon-btn { width: 42px; height: 42px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; background: var(--card-bg); color: var(--text-secondary); font-size: 19px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); box-shadow: 0 12px 34px rgba(0,0,0,0.10); overflow: hidden; }
    .icon-btn::before { content: ''; position: absolute; width: 0; height: 0; background: var(--gold); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%); transition: var(--transition); z-index: -1; opacity: 0.12; }
    .icon-btn:hover::before { width: 100%; height: 100%; }
    .icon-btn:hover { color: var(--gold); border-color: var(--gold); transform: scale(1.05); }

    .user-profile-btn { display: inline-flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 999px; box-shadow: 0 10px 26px rgba(0,0,0,0.12); cursor: pointer; transition: var(--transition); overflow: hidden; min-width: 200px; text-decoration: none; color: inherit; position: relative; }
    .user-profile-btn::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, transparent 0%, rgba(196,150,58,0.1) 100%); opacity: 0; transition: var(--transition); }
    .user-profile-btn:hover { border-color: rgba(255,214,122,0.5); box-shadow: 0 18px 42px rgba(0,0,0,0.16); transform: translateY(-1px); background: rgba(255,255,255,0.14); }
    .user-profile-btn:hover::before { opacity: 1; }
    .user-profile-btn .u-info { text-align: right; z-index: 1; }
    .user-profile-btn .u-name { font-size: 12px; font-weight: 800; color: var(--text-primary); background: linear-gradient(135deg, var(--gold), var(--gold-dark)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .user-profile-btn .u-role { font-size: 10px; color: var(--text-muted); font-weight: 600; }
    .user-profile-btn .u-av { width: 30px; height: 30px; background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1)); color: #111; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 900; box-shadow: 0 4px 10px rgba(255,214,122,0.24); }
    .user-profile-btn:hover .u-av { transform: scale(1.15) rotate(-5deg); box-shadow: 0 6px 16px rgba(196,150,58,0.35); }

    .content { padding: 32px; flex: 1; }

    .page-header {
      margin-bottom: 28px;
      animation: slideInDown 0.5s ease;
    }
    .page-header h1 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 6px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .page-header p {
      color: var(--text-secondary);
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
    }
    .page-header p i { color: var(--gold); }
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      font-size: 13px;
    }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a { color: var(--gold); text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }

    .form-container {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 26px;
      align-items: start;
    }

    .form-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(0,0,0,0.02);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }
    .form-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle at 100% 0%, rgba(196,150,58,0.08), transparent);
      pointer-events: none;
    }
    .form-card:hover {
      box-shadow: 0 12px 32px rgba(196,150,58,0.12);
      border-color: var(--gold);
    }
    .form-card h2 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      z-index: 2;
    }
    .form-card h2 i { color: var(--gold); font-size: 22px; }

    .form-group {
      margin-bottom: 24px;
      position: relative;
      z-index: 2;
    }
    .form-group:last-child { margin-bottom: 0; }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text-primary);
      font-size: 14px;
    }
    .required { color: var(--danger); margin-right: 4px; }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: var(--card-bg);
      color: var(--text-primary);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      transition: var(--transition);
      outline: none;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus,
    select:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px var(--gold-light);
    }

    textarea {
      resize: vertical;
      min-height: 100px;
    }

    .form-hint {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 30px;
    }

    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: var(--radius-md);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: var(--transition);
      flex: 1;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      color: #fff;
      box-shadow: 0 4px 12px rgba(196,150,58,0.3);
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(196,150,58,0.4);
    }

    .btn-secondary {
      background: var(--bg);
      color: var(--text-primary);
      border: 2px solid var(--border);
    }
    .btn-secondary:hover {
      background: var(--gold-light);
      border-color: var(--gold);
      color: var(--gold);
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--danger), #FF5A52);
      color: #fff;
      box-shadow: 0 4px 12px rgba(255,59,48,0.3);
    }
    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(255,59,48,0.4);
    }

    .preview-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 26px;
      box-shadow: var(--shadow);
      border: 2px solid var(--border);
      position: sticky;
      top: 100px;
      transition: var(--transition);
      overflow: hidden;
      height: fit-content;
    }
    .preview-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 20% 50%, rgba(196,150,58,0.08), transparent);
      pointer-events: none;
    }
    .preview-card:hover {
      box-shadow: 0 12px 32px rgba(196,150,58,0.15);
      border-color: var(--gold);
    }

    .preview-card h3 {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      position: relative;
      z-index: 2;
    }
    .preview-card h3 i { color: var(--gold); }

    .course-preview {
      background: linear-gradient(135deg, var(--gold-light) 0%, rgba(196,150,58,0.08) 100%);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 16px;
      border: 2px dashed var(--gold);
      text-align: center;
      transition: var(--transition);
      position: relative;
      z-index: 2;
    }

    .course-preview-image {
      width: 100%;
      height: 140px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 48px;
      margin-bottom: 12px;
      box-shadow: 0 4px 12px rgba(196,150,58,0.2);
    }

    .course-preview h4 {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 6px;
      position: relative;
      z-index: 2;
    }

    .course-preview p {
      font-size: 12px;
      color: var(--text-secondary);
      margin-bottom: 12px;
      position: relative;
      z-index: 2;
    }

    .preview-info {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      padding: 6px 0;
      position: relative;
      z-index: 2;
    }
    .preview-info label { margin-bottom: 0; font-weight: 700; color: var(--text-primary); }
    .preview-info strong { color: var(--gold); }

    @keyframes slideInDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1200px) {
      .form-container { grid-template-columns: 1fr; }
      .preview-card { position: static; margin-top: 20px; }
    }

    @media (max-width: 768px) {
      .app { flex-direction: column; }
      .sidebar { position: relative; width: 100%; right: auto; top: auto; bottom: auto; border-left: none; border-radius: 0; box-shadow: none; border: none; margin-bottom: 18px; }
      .main { margin-right: 0; }
      .content { padding: 20px; }
      .form-card { padding: 24px; }
      .preview-card { padding: 24px; }
      .form-actions { flex-direction: column; }
    }

    @media (max-width: 480px) {
      .topbar { flex-wrap: wrap; gap: 12px; padding: 16px; }
      .content { padding: 16px; }
      .course-preview-image { height: 160px; }
    }

    html, body, .sidebar, .sidebar-nav, .main {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    html::-webkit-scrollbar,
    body::-webkit-scrollbar,
    .sidebar::-webkit-scrollbar,
    .sidebar-nav::-webkit-scrollbar,
    .main::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }
  </style>
</head>
<body>
@include('components.alerts')
<div class="app">
  @include('components.sidebar-unified')

  <main class="main">
    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-left">
        <button class="icon-btn" id="themeToggle" title="تبديل الثيم">
          <i class="ri-moon-line"></i>
        </button>
        <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
          <i class="ri-notification-3-line"></i>
        </button>
        <a href="{{ route('profile.show') }}" class="user-profile-btn" title="عرض الملف الشخصي">
          <div class="u-info">
            <div class="u-name">{{ Auth::user()->name }}</div>
            <div class="u-role">معلم</div>
          </div>
          <div class="u-av">{{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</div>
        </a>
      </div>
      <div class="topbar-right">
        <div class="search-wrap">
          <input type="text" placeholder="ابحث هنا...">
          <i class="ri-search-line search-icon"></i>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      @if ($errors->any())
        <div style="background: #FF3B30; color: white; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;">
          <ul style="list-style: none;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Page Header -->
      <div class="page-header">
        <h1>تعديل المسار</h1>
        <p>قم بتحديث معلومات المسار التعليمي</p>
        <div class="breadcrumb">
          <a href="{{ route('teacher.courses') }}">المسارات</a>
          <span>/</span>
          <a href="{{ route('teacher.show', $course->id) }}">{{ $course->name }}</a>
          <span>/</span>
          <span>تعديل</span>
        </div>
      </div>

      <!-- Form Section -->
      <div class="form-container">
        <!-- Form Card -->
        <div class="form-card">
          <h2><i class="ri-edit-box-line"></i> بيانات المسار</h2>

          <form action="{{ route('teacher.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Course Name -->
            <div class="form-group">
              <label>
                <span class="required">*</span>
                اسم المسار
              </label>
              <input
                type="text"
                name="name"
                placeholder="أدخل اسم المسار التعليمي"
                value="{{ old('name', $course->name) }}"
                required
                
              >
              <div class="form-hint">اختر اسماً جذاباً ووصفياً</div>
            </div>

            <!-- Description -->
            <div class="form-group">
              <label>
                <span class="required">*</span>
                الوصف
              </label>
              <textarea
                name="description"
                placeholder="اكتب وصفاً شاملاً للمسار التعليمي..."
                required
                
              >{{ old('description', $course->description) }}</textarea>
              <div class="form-hint">اشرح محتوى المسار والأهداف بشكل واضح</div>
            </div>

            <!-- Duration -->
            <div class="form-group">
              <label>المدة المتوقعة (ساعات)</label>
              <input
                type="number"
                name="duration"
                placeholder="مثلاً: 20"
                min="1"
                value="{{ old('duration', $course->duration) }}"
                
              >
            </div>

            <!-- Form Actions -->
            <div class="form-actions" style="gap: 8px; margin-top: 30px;">
              <button type="submit" class="btn btn-primary">
                <i class="ri-check-line"></i> حفظ التغييرات
              </button>
              <a href="{{ route('teacher.show', $course->id) }}" class="btn btn-secondary">
                <i class="ri-close-line"></i> إلغاء
              </a>
            </div>
          </form>

          <!-- Delete Section -->
          <div style="border-top: 2px solid var(--border); margin-top: 30px; padding-top: 20px;">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--danger); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
              <i class="ri-delete-bin-line"></i> منطقة الحذف
            </h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
              عند حذف المسار، سيتم حذف جميع الدروس والاختبارات المرتبطة به. هذا الإجراء لا يمكن التراجع عنه.
            </p>
            <form action="{{ route('teacher.delete', $course->id) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger">
                <i class="ri-delete-bin-line"></i> حذف المسار
              </button>
            </form>
          </div>
        </div>

        <!-- Preview Card -->
        <div class="preview-card">
          <h3><i class="ri-eye-line"></i> معاينة المسار</h3>

          <div class="course-preview" id="preview">
            <div class="course-preview-image">
              <i class="ri-book-2-line"></i>
            </div>
            <h4>{{ $course->name }}</h4>
            <p>{{ Str::limit($course->description ?? '', 80) }}</p>
            @if($course->duration)
              <div class="preview-info">
                <label>المدة:</label>
                <strong>{{ $course->duration }} ساعة</strong>
              </div>
            @endif
          </div>

          <div style="background: var(--gold-light); border-radius: 8px; padding: 12px; margin-top: 16px;">
            <p style="font-size: 12px; color: var(--gold-dark); display: flex; align-items: center; gap: 8px;">
              <i class="ri-information-line"></i>
              تم إنشاء المسار في {{ $course->created_at->diffForHumans() }}
            </p>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
  // Dark Mode Toggle
  const themeToggle = document.getElementById('themeToggle');
  const html = document.documentElement;
  const themeIcon = themeToggle?.querySelector('i');

  function updateTheme() {
    const currentTheme = html.getAttribute('data-theme');
    const isDark = currentTheme === 'dark';
    if (themeIcon) {
      themeIcon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
    }
  }

  themeToggle?.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('app-theme', newTheme);
    updateTheme();
  });

  window.addEventListener('load', () => {
    const savedTheme = localStorage.getItem('app-theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateTheme();
  });

  // Preview Update
  function updatePreview() {
    const name = document.querySelector('input[name="name"]').value || 'اسم المسار';
    const description = document.querySelector('textarea[name="description"]').value || 'أضف وصفاً للمسار';
    const duration = document.querySelector('input[name="duration"]').value || '-';

    const preview = document.getElementById('preview');
    preview.innerHTML = `
      <div class="course-preview-image">
        <i class="ri-book-2-line"></i>
      </div>
      <h4>${name}</h4>
      <p>${description.substring(0, 80)}</p>
      ${duration !== '-' ? `<div class="preview-info"><label>المدة:</label><strong>${duration} ساعة</strong></div>` : ''}
    `;
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Attach input event listeners for preview update
    const previewInputs = document.querySelectorAll('input[name="name"], textarea[name="description"], input[name="duration"]');
    previewInputs.forEach(function(el) { el.addEventListener('input', updatePreview); });
    // Attach submit listener for delete confirmation
    const deleteForm = document.querySelector('form[action*="delete"]');
    if (deleteForm) {
      deleteForm.addEventListener('submit', function(e) {
        if (!confirm('هل أنت متأكد من أنك تريد حذف هذا المسار؟ لا يمكن التراجع عن هذا الإجراء.')) {
          e.preventDefault();
        }
      });
    }
  });
</script>
@include('components.notification-bell')
    @include('components.account-theme-foot')
</body>
</html>



