<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ isset($lesson) ? 'تعديل الدرس' : 'إنشاء درس جديد' }} - المنصة التعليمية</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root { --sidebar-w: 300px; --topbar-h: 70px; }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body { font-family: 'Tajawal', sans-serif; background: radial-gradient(circle at top left, rgba(255,214,122,0.16), transparent 22%), linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%); color: var(--text-primary); min-height: 100vh; transition: background 0.3s, color 0.3s; position: relative; overflow-x: hidden; }
    body::before { content: ''; position: fixed; top: 16px; left: 16px; width: 320px; height: 320px; border-radius: 50%; background: radial-gradient(circle, rgba(255,214,122,0.24), transparent 55%); filter: blur(72px); z-index: 0; pointer-events: none; }
    .app { display: flex; min-height: 100vh; position: relative; }

    .sidebar {
      width: var(--sidebar-w);
      position: fixed;
      top: 24px;
      right: 18px;
      bottom: 24px;
      background: var(--sidebar-bg, var(--theme-surface));
      backdrop-filter: blur(24px);
      border-left: 1px solid rgba(255,214,122,0.16);
      border-top-left-radius: 32px;
      border-bottom-left-radius: 32px;
      display: flex;
      flex-direction: column;
      padding: 28px 22px;
      gap: 20px;
      z-index: 10;
      box-shadow: -16px 28px 70px rgba(0,0,0,0.28);
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
      position: relative;
      transition: var(--transition);
      box-shadow: 0 0 16px rgba(196,150,58,0.2);
      overflow: hidden;
      animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-8px); }
    }
    .logo-icon img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 30px;
      display: block;
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

    .sidebar-nav {
      flex: 1;
      display: grid;
      gap: 14px;
      overflow-y: auto;
      padding: 0;
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
    }
    .nav-btn i { font-size: 20px; color: rgba(255,214,122,0.92); flex-shrink: 0; }
    .nav-btn span { color: inherit; flex: 1; }
    .nav-btn:hover {
      background: rgba(255,214,122,0.08);
      color: #F9F9FB;
      border-color: rgba(255,214,122,0.18);
    }
    .nav-btn.active {
      background: rgba(255,214,122,0.18);
      color: #FFFFFF;
      border-color: rgba(255,214,122,0.32);
      box-shadow: 0 20px 40px rgba(255,214,122,0.14);
      backdrop-filter: blur(18px);
    }
    .nav-btn.logout {
      color: #FF6C63;
      background: rgba(255,59,48,0.08);
      border-color: rgba(255,59,48,0.18);
    }

    .sidebar-footer { margin-top: auto; }
    .sidebar-footer form { width: 100%; }

    .main { margin-right: calc(var(--sidebar-w) + 18px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

    .topbar {
      height: var(--topbar-h);
      background: transparent;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      margin: 10px 0;
    }
    .topbar-left { display: flex; align-items: center; gap: 14px; }
    .icon-btn { background: none; border: none; font-size: 20px; color: var(--text-secondary); cursor: pointer; transition: var(--transition); }
    .icon-btn:hover { color: var(--gold); transform: scale(1.1); }
    .topbar-right { display: flex; align-items: center; gap: 12px; }

    .user-profile-btn {
      display: inline-flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 14px;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 999px;
      box-shadow: 0 8px 18px rgba(0,0,0,0.16);
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      min-width: 200px;
      text-decoration: none;
      color: inherit;
    }
    .user-profile-btn::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, transparent 0%, rgba(196,150,58,0.1) 100%);
      opacity: 0;
      transition: var(--transition);
    }
    .user-profile-btn:hover {
      border-color: rgba(255,214,122,0.6);
      box-shadow: 0 14px 34px rgba(0,0,0,0.2);
      transform: translateY(-2px);
      background: rgba(255,255,255,0.14);
    }
    .user-profile-btn:hover::before {
      opacity: 1;
    }
    .user-profile-btn .u-info { text-align: right; position: relative; z-index: 1; }
    .user-profile-btn .u-name {
      font-size: 12px;
      font-weight: 800;
      color: var(--text-primary);
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: var(--transition);
    }
    .user-profile-btn:hover .u-name {
      text-shadow: 0 0 8px rgba(196,150,58,0.3);
    }
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
      transition: var(--transition);
      position: relative;
      z-index: 1;
    }
    .user-profile-btn:hover .u-av {
      transform: scale(1.15) rotate(-5deg);
      box-shadow: 0 6px 16px rgba(196,150,58,0.35);
    }

    .search-wrap {
      width: min(100%, 360px);
      position: relative;
      display: block;
    }

    .search-wrap::before {
      content: '';
      position: absolute;
      top: -10px;
      left: -10px;
      width: 140px;
      height: 140px;
      border-radius: 50%;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.22), transparent 55%);
      opacity: 0.8;
      pointer-events: none;
      filter: blur(14px);
      transition: opacity 0.3s ease;
    }

    .search-wrap:hover::before { opacity: 1; }

    .search-wrap input {
      width: 100%;
      padding: 12px 48px 12px 16px;
      height: 44px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.14);
      border-radius: 40px;
      color: var(--text-primary);
      font-size: 14px;
      outline: none;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.08), 0 14px 35px rgba(0,0,0,0.18);
      transition: var(--transition);
    }

    .search-wrap input::placeholder { color: rgba(255,255,255,0.56); }

    .search-wrap input:focus { border-color: rgba(255,214,122,0.4); box-shadow: 0 0 0 4px rgba(255,214,122,0.08); }

    .search-icon {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255,255,255,0.7);
      font-size: 18px;
      pointer-events: none;
      transition: var(--transition);
    }

    .search-highlight {
      animation: highlightFlash 1.4s ease;
      outline: 2px solid rgba(255,214,122,0.35);
      outline-offset: 4px;
      background: rgba(255,214,122,0.12);
    }

    @keyframes highlightFlash {
      0% { box-shadow: 0 0 0 rgba(255,214,122,0.2); }
      50% { box-shadow: 0 0 0 16px rgba(255,214,122,0.06); }
      100% { box-shadow: 0 0 0 rgba(255,214,122,0); }
    }

    .content {
      flex: 1;
      overflow-y: auto;
      padding: 32px 40px;
      max-width: 1000px;
      width: 100%;
    }

    .page-header { margin-bottom: 40px; animation: slideInRight 0.6s ease-out; }
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(-30px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .page-title {
      font-size: 32px;
      font-weight: 900;
      margin-bottom: 10px;
      background: linear-gradient(135deg, var(--text-primary), var(--gold));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .page-subtitle {
      color: var(--text-secondary);
      font-size: 15px;
      font-weight: 500;
    }

    .form-section {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow);
      border-left: 4px solid var(--gold);
      position: relative;
      overflow: hidden;
      animation: slideInUp 0.6s ease-out;
    }
    @keyframes slideInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-section::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, rgba(196,150,58,0.15), transparent);
      border-radius: 50%;
      pointer-events: none;
      animation: floatGradient 6s ease-in-out infinite;
    }
    @keyframes floatGradient {
      0%, 100% { transform: translate(0, 0); }
      50% { transform: translate(-20px, -10px); }
    }
    .form-section::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100%;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(196,150,58,0.5), transparent);
      pointer-events: none;
    }
    .form-section:hover {
      box-shadow: var(--shadow-hover), 0 0 30px rgba(196,150,58,0.15);
      transform: translateY(-3px);
      transition: var(--transition);
    }

    .form-section-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 24px;
      padding-bottom: 14px;
      border-bottom: 2px solid var(--gold-light);
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      animation: fadeInDown 0.5s ease-out;
    }
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .form-section-title::before {
      content: '';
      width: 5px;
      height: 22px;
      background: linear-gradient(180deg, var(--gold), var(--gold-dark));
      border-radius: 3px;
      flex-shrink: 0;
      box-shadow: 0 2px 8px rgba(196,150,58,0.3);
    }
    .form-section-title::after {
      content: '';
      position: absolute;
      bottom: -2px;
      right: 0;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, var(--gold), transparent);
      opacity: 0;
      animation: slideRight 0.8s ease-out forwards;
    }
    @keyframes slideRight {
      from { width: 0; opacity: 0; }
      to { width: 100%; opacity: 1; }
    }

    .form-group {
      margin-bottom: 22px;
      animation: fadeIn 0.5s ease-out forwards;
      opacity: 0;
    }
    @keyframes fadeIn {
      to { opacity: 1; }
    }
    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }
    .form-group:nth-child(5) { animation-delay: 0.5s; }
    .form-group:last-child { margin-bottom: 0; }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 10px;
      text-align: right;
    }

    .required { color: var(--danger); }

    .form-input, .form-textarea, .form-select {
      width: 100%;
      padding: 14px 16px;
      border: 1.5px solid rgba(0,0,0,0.08);
      border-radius: var(--radius-md);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      color: var(--text-primary);
      background: linear-gradient(135deg, var(--bg), rgba(196,150,58,0.02));
      transition: var(--transition);
      text-align: right;
    }

    .form-select {
      appearance: none;
      background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill="C4963A" d="M10.3 14.6L4 8h12.6l-6.3 6.6z"/></svg>');
      background-repeat: no-repeat;
      background-position: left 12px center;
      background-size: 20px;
      padding-right: 40px;
      cursor: pointer;
    }

    .form-input:hover, .form-select:hover, .form-textarea:hover {
      border-color: rgba(196,150,58,0.3);
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.02), 0 0 12px rgba(196,150,58,0.1);
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: none;
      border-color: var(--gold);
      background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.05));
      box-shadow: 0 0 0 3px rgba(196,150,58,0.15), inset 0 0 0 1px rgba(196,150,58,0.3), inset 0 2px 4px rgba(0,0,0,0.02);
      transform: translateY(-1px);
    }

    .form-textarea {
      min-height: 120px;
      resize: vertical;
      max-height: 300px;
    }

    .form-hint {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
    }

    .btn-group {
      display: flex;
      gap: 14px;
      margin-top: 36px;
      padding-top: 24px;
      border-top: 2px solid rgba(196,150,58,0.1);
      animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .btn {
      padding: 13px 28px;
      border: none;
      border-radius: var(--radius-md);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
      flex: 1;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      color: white;
      box-shadow: 0 4px 16px rgba(196,150,58,0.3);
    }
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(196,150,58,0.4);
    }

    .btn-secondary {
      background: transparent;
      color: var(--text-secondary);
      border: 1.5px solid rgba(196,150,58,0.3);
    }
    .btn-secondary:hover {
      background: var(--gold-light);
      color: var(--gold);
      border-color: var(--gold);
      transform: translateY(-2px);
    }

    ::-webkit-scrollbar { display: none; }
    .sidebar, .main { -ms-overflow-style: none; scrollbar-width: none; }
    .sidebar::-webkit-scrollbar, .sidebar-nav::-webkit-scrollbar { width: 0; height: 0; background: transparent; }

    /* ===== Content Type Selector ===== */
    .content-type-selector {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
      animation: fadeIn 0.6s ease-out;
    }

    .content-type-option {
      padding: 24px 20px;
      border: 2px solid rgba(196,150,58,0.2);
      border-radius: var(--radius-lg);
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
      background: linear-gradient(135deg, rgba(196,150,58,0.02), transparent);
      position: relative;
      overflow: hidden;
    }

    .content-type-option::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(135deg, rgba(196,150,58,0.15), transparent);
      transition: var(--transition);
      z-index: -1;
    }

    .content-type-option:hover {
      border-color: var(--gold);
      box-shadow: 0 6px 16px rgba(196,150,58,0.15);
      transform: translateY(-4px);
    }

    .content-type-option:hover::before { width: 100%; }

    .content-type-option.active {
      background: linear-gradient(135deg, var(--gold-light), rgba(196,150,58,0.08));
      border-color: var(--gold);
      box-shadow: 0 8px 24px rgba(196,150,58,0.2), inset 0 0 20px rgba(196,150,58,0.1);
    }

    .content-type-option.active::after {
      content: '';
      position: absolute;
      top: 8px;
      right: 8px;
      width: 24px;
      height: 24px;
      background: var(--gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 14px;
      box-shadow: 0 4px 12px rgba(196,150,58,0.3);
    }

    .content-type-option.active::after {
      content: '✓';
    }

    .type-icon {
      font-size: 36px;
      color: var(--gold);
      margin-bottom: 12px;
      transition: var(--transition);
    }

    .content-type-option:hover .type-icon {
      transform: scale(1.15);
      filter: drop-shadow(0 4px 12px rgba(196,150,58,0.3));
    }

    .type-label {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 4px;
    }

    .type-sublabel {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .content-type-content {
      animation: slideInDown 0.4s ease-out;
    }

    /* ===== File Upload ===== */
    .file-upload-wrapper {
      margin-bottom: 20px;
    }

    .file-input {
      display: none;
    }

    .file-upload-label {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      border: 2px dashed rgba(196,150,58,0.4);
      border-radius: var(--radius-lg);
      background: linear-gradient(135deg, rgba(196,150,58,0.05), transparent);
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .file-upload-label::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(196,150,58,0.1), transparent);
      pointer-events: none;
    }

    .file-upload-label:hover {
      border-color: var(--gold);
      background: linear-gradient(135deg, rgba(196,150,58,0.1), rgba(196,150,58,0.02));
      box-shadow: 0 8px 24px rgba(196,150,58,0.15);
    }

    .file-upload-label.drag-over {
      border-color: var(--gold);
      background: linear-gradient(135deg, var(--gold-light), rgba(196,150,58,0.1));
      box-shadow: 0 12px 32px rgba(196,150,58,0.25);
      
    }

    .file-upload-icon {
      font-size: 48px;
      color: var(--gold);
      margin-bottom: 16px;
      transition: var(--transition);
    }

    .file-upload-label:hover .file-upload-icon {
      transform: scale(1.1) rotate(5deg);
      filter: drop-shadow(0 6px 16px rgba(196,150,58,0.3));
    }

    .file-upload-text {
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .file-upload-title {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 4px;
    }

    .file-upload-hint {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .file-upload-progress {
      margin-top: 16px;
      padding: 16px;
      background: linear-gradient(135deg, var(--gold-light), transparent);
      border-radius: var(--radius-md);
      text-align: center;
    }

    .progress-bar {
      width: 100%;
      height: 6px;
      background: rgba(196,150,58,0.2);
      border-radius: 3px;
      overflow: hidden;
      margin-bottom: 8px;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--gold), var(--gold-dark));
      border-radius: 3px;
      animation: progress 2s ease-in-out infinite;
    }

    @keyframes progress {
      0% { width: 0%; }
      50% { width: 100%; }
      100% { width: 0%; }
    }

    .progress-text {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 600;
    }

    .file-upload-preview {
      margin-top: 12px;
    }

    .preview-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      background: linear-gradient(135deg, var(--gold-light), transparent);
      border-radius: var(--radius-md);
      border-left: 4px solid var(--gold);
      animation: slideInRight 0.4s ease-out;
    }

    .preview-item i {
      font-size: 20px;
      color: var(--gold);
      flex-shrink: 0;
    }

    .preview-item span {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-primary);
      flex: 1;
      word-break: break-word;
    }

    .preview-delete {
      background: transparent;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 18px;
      transition: var(--transition);
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .preview-delete:hover {
      color: var(--danger);
      transform: scale(1.2);
    }

    /* ===== Toast Notifications Animations ===== */
    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(100px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slideOutRight {
      from {
        opacity: 1;
        transform: translateX(0);
      }
      to {
        opacity: 0;
        transform: translateX(100px);
      }
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-100px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slideOutLeft {
      from {
        opacity: 1;
        transform: translateX(0);
      }
      to {
        opacity: 0;
        transform: translateX(-100px);
      }
    }

    /* ===== YouTube Loading Indicator ===== */
    .youtube-loading-indicator {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      margin-top: 10px;
      background: linear-gradient(135deg, rgba(196,150,58,0.08), rgba(196,150,58,0.03));
      border: 1.5px solid rgba(196,150,58,0.3);
      border-radius: var(--radius-md);
      animation: slideInDown 0.4s ease-out;
    }

    .loading-spinner {
      width: 18px;
      height: 18px;
      border: 2.5px solid rgba(196,150,58,0.2);
      border-top-color: var(--gold);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      flex-shrink: 0;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .loading-text {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-primary);
      animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.6; }
    }

    @media (max-width: 768px) {
      .content-type-selector {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    /* ═══ Responsive ═══ */
    .hamburger-btn {
        display: none;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        background: rgba(255,255,255,0.06);
        color: var(--text-primary);
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .hamburger-btn:hover { background: rgba(255,214,122,0.1); border-color: rgba(255,214,122,0.3); }

    @media (max-width: 1024px) {
        .main { margin-right: 72px !important; }
        .topbar { padding: 0 20px; }
        .content { padding: 0 20px 20px !important; }
        .search-wrap { width: 240px; }
        .user-profile-btn { min-width: 150px; }
    }

    @media (max-width: 768px) {
        .hamburger-btn { display: flex; }
        .main { margin-right: 0 !important; }
        .topbar { padding: 0 12px; height: 56px; }
        .search-wrap { display: none; }
        .u-name, .u-role { display: none; }
        .user-profile-btn { min-width: auto; }
        .content { padding: 0 12px 12px !important; }
        .content-type-selector { grid-template-columns: repeat(2, 1fr); }
        .form-section { padding: 18px; }
    }

    @media (max-width: 480px) {
        .content-type-selector { grid-template-columns: 1fr 1fr; }
        .topbar { padding: 0 8px; height: 52px; }
        .icon-btn { width: 36px; height: 36px; }
        .hamburger-btn { width: 36px; height: 36px; }
        .content { padding: 0 8px 8px !important; }
    }
  </style>
</head>
<body>
  @include('components.alerts')
  <div class="app">
    @include('components.sidebar-unified')

    <div class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger-btn" id="sidebarToggle" title="فتح القائمة">
            <i class="ri-menu-line"></i>
          </button>
          <button class="icon-btn" id="darkBtn" title="تبديل الثيم">
            <i class="ri-moon-line" id="theme-icon"></i>
          </button>
          <button class="icon-btn" id="notificationBtn" title="الإشعارات">
            <i class="ri-notification-3-line"></i>
          </button>
          <a href="{{ route('profile.show') }}" class="user-profile-btn" title="عرض الملف الشخصي">
            <div class="u-info">
              <div class="u-name">{{ Auth::user()->name ?? 'المعلم' }}</div>
              <div class="u-role">معلم</div>
            </div>
            <div class="u-av">{{ strtoupper(mb_substr(Auth::user()->name ?? 'المعلم', 0, 1)) }}</div>
          </a>
        </div>
        <div class="topbar-right">
          <div class="search-wrap">
            <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
            <i class="ri-search-line search-icon"></i>
          </div>
        </div>
      </header>

      <div class="content">
        <div class="page-header">
          <h1 class="page-title">{{ isset($lesson) ? 'تعديل الدرس' : 'إنشاء درس جديد' }}</h1>
          <p class="page-subtitle">{{ isset($lesson) ? 'قم بتحديث بيانات الدرس' : 'أضف درس جديد لتحسين المحتوى التعليمي' }}</p>
        </div>

        <form action="{{ isset($lesson) ? route('teacher.updateLesson', $lesson->id) : route('teacher.addLesson', $course->id ?? 0) }}" method="POST" enctype="multipart/form-data" id="lessonForm" novalidate>
          @csrf
          @if(isset($lesson))
            @method('PUT')
          @endif
          <!-- Hidden field for lesson type -->
          <input type="hidden" name="lesson_type" id="lessonTypeInput" value="{{ $lesson->lesson_type ?? old('lesson_type', 'video-upload') }}">
          <!-- Hidden fields for file paths (uploaded via AJAX) -->
          <input type="hidden" name="video_file_path" id="videoFilePathInput" value="">
          <input type="hidden" name="audio_file_path" id="audioFilePathInput" value="">

          <div class="form-section">
            <div class="form-section-title">معلومات الدرس الأساسية</div>

            <div class="form-group">
              <label class="form-label"><span class="required">*</span> عنوان الدرس</label>
              <input type="text" name="name" class="form-input" placeholder="أدخل عنوان الدرس" value="{{ $lesson->name ?? old('name') }}" required>
              <div class="form-hint">اختر عنواناً واضحاً وجذاباً</div>
            </div>

            <div class="form-group">
              <label class="form-label"><span class="required">*</span> وصف الدرس</label>
              <textarea name="description" class="form-textarea" placeholder="اكتب وصفاً شاملاً للدرس..." required>{{ $lesson->description ?? old('description') }}</textarea>
              <div class="form-hint">اشرح محتوى الدرس والمهارات المكتسبة</div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">خيارات إضافة المحتوى <span style="font-size: 12px; font-weight: 400; color: var(--text-muted);">(اختر نوع المحتوى المراد إضافته)</span></div>

            <!-- Content Type Selector -->
            <div class="content-type-selector">
              <div class="content-type-option" data-type="video-upload">
                <div class="type-icon"><i class="ri-video-upload-line"></i></div>
                <div class="type-label">فيديو</div>
                <div class="type-sublabel">رفع مقطع فيديو</div>
              </div>

              <div class="content-type-option" data-type="audio-upload">
                <div class="type-icon"><i class="ri-mic-2-fill"></i></div>
                <div class="type-label">صوتي</div>
                <div class="type-sublabel">رفع مقطع صوتي</div>
              </div>

              <div class="content-type-option" data-type="other-content">
                <div class="type-icon"><i class="ri-file-text-line"></i></div>
                <div class="type-label">محتوى آخر</div>
                <div class="type-sublabel">نصوص وملاحظات</div>
              </div>

              <div class="content-type-option" data-type="youtube">
                <div class="type-icon"><i class="ri-youtube-line"></i></div>
                <div class="type-label">YouTube</div>
                <div class="type-sublabel">رابط من يوتيوب</div>
              </div>
            </div>

            <!-- Video Upload -->
            <div class="content-type-content" id="content-video-upload" style="display: none;">
              <div class="form-group">
                <label class="form-label"><i class="ri-video-upload-line" style="color: var(--gold); margin-left: 6px;"></i> رفع مقطع فيديو <span class="required">*</span></label>
                <div class="file-upload-wrapper">
                  <input type="file" id="videoFile" name="video_file" class="file-input" accept="video/*" style="display: none;">
                  <label for="videoFile" class="file-upload-label">
                    <div class="file-upload-icon"><i class="ri-video-upload-line"></i></div>
                    <div class="file-upload-text">
                      <div class="file-upload-title">انقر لاختيار فيديو أو اسحب الملف هنا</div>
                      <div class="file-upload-hint">الحد الأقصى: 500 MB (MP4, AVI, MOV)</div>
                    </div>
                  </label>
                  <!-- Video Loading Indicator -->
                  <div class="youtube-loading-indicator" id="videoLoading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <span class="loading-text">جاري معالجة الفيديو...</span>
                  </div>
                  <div class="file-upload-progress" id="videoProgress" style="display: none;">
                    <div class="progress-bar"><div class="progress-fill"></div></div>
                    <span class="progress-text">جاري الرفع...</span>
                  </div>
                  <div class="file-upload-preview" id="videoPreview">
                    <div class="preview-item" id="videoItem" style="display: none;">
                      <i class="ri-video-line"></i>
                      <span id="videoFileName"></span>
                      <button type="button" class="preview-delete" id="clearVideoBtn"><i class="ri-close-line"></i></button>
                    </div>
                  </div>
                </div>
                <div class="form-hint">سيتم تخزين الفيديو مؤقتاً بشكل آمن في متصفحك حتى تأكيد الإنشاء</div>
              </div>
            </div>

            <!-- Audio Upload -->
            <div class="content-type-content" id="content-audio-upload" style="display: none;">
              <div class="form-group">
                <label class="form-label"><i class="ri-mic-2-fill" style="color: var(--gold); margin-left: 6px;"></i> رفع مقطع صوتي <span class="required">*</span></label>
                <div class="file-upload-wrapper">
                  <input type="file" id="audioFile" name="audio_file" class="file-input" accept="audio/*" style="display: none;">
                  <label for="audioFile" class="file-upload-label">
                    <div class="file-upload-icon"><i class="ri-mic-2-fill"></i></div>
                    <div class="file-upload-text">
                      <div class="file-upload-title">انقر لاختيار ملف صوتي أو اسحب الملف هنا</div>
                      <div class="file-upload-hint">الحد الأقصى: 100 MB (MP3, WAV, M4A)</div>
                    </div>
                  </label>
                  <!-- Audio Loading Indicator -->
                  <div class="youtube-loading-indicator" id="audioLoading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <span class="loading-text">جاري معالجة الملف الصوتي...</span>
                  </div>
                  <div class="file-upload-progress" id="audioProgress" style="display: none;">
                    <div class="progress-bar"><div class="progress-fill"></div></div>
                    <span class="progress-text">جاري الرفع...</span>
                  </div>
                  <div class="file-upload-preview" id="audioPreview">
                    <div class="preview-item" id="audioItem" style="display: none;">
                      <i class="ri-music-2-line"></i>
                      <span id="audioFileName"></span>
                      <button type="button" class="preview-delete" id="clearAudioBtn"><i class="ri-close-line"></i></button>
                    </div>
                  </div>
                </div>
                <div class="form-hint">الملفات الصوتية توفر بديل رائع للفيديو - يتم تخزينها مؤقتاً حتى التأكيد</div>
              </div>
            </div>

            <!-- Other Content -->
            <div class="content-type-content" id="content-other-content" style="display: none;">
              <div class="form-group">
                <label class="form-label"><i class="ri-file-text-line" style="color: var(--gold); margin-left: 6px;"></i> المحتوى الإضافي</label>
                <textarea name="content" class="form-textarea" placeholder="أضف محتوى إضافي، ملاحظات، موارد أو معلومات مهمة...">{{ $lesson->content ?? old('content') }}</textarea>
                <div class="form-hint">يمكنك إضافة نصوص، روابء وملاحظات هامة للطلاب</div>
              </div>
            </div>

            <!-- YouTube Link -->
            <div class="content-type-content" id="content-youtube" style="display: none;">
              <div class="form-group">
                <label class="form-label"><i class="ri-youtube-line" style="color: var(--gold); margin-left: 6px;"></i> رابط فيديو YouTube</label>
                <input type="url" name="video_url" id="youtubeUrlInput" class="form-input" placeholder="https://youtube.com/watch?v=..." value="{{ $lesson->video_url ?? old('video_url') }}">

                <!-- Loading Indicator -->
                <div class="youtube-loading-indicator" id="youtubeLoading" style="display: none;">
                  <div class="loading-spinner"></div>
                  <span class="loading-text">جاري معالجة الرابط...</span>
                </div>

                <div class="form-hint">أضف رابط الفيديو من YouTube أو Vimeo أو أي منصة أخرى</div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">إعدادات الدرس</div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" id="durationLabel">مدة الدرس (دقائق:ثواني) <span id="durationHint" style="font-size: 11px; color: var(--text-muted);">(تُستخرج تلقائياً من الملف)</span></label>
                <input type="text" id="durationInput" name="duration" class="form-input" placeholder="مثال: 5:30" value="{{ $lesson->duration ?? old('duration') }}" pattern="^\d{1,3}:\d{2}(:\d{2})?$" title="تُستخرج المدة تلقائياً من الملف المرفوع" readonly style="opacity:0.8;cursor:default;">
                @error('duration')
                  <div style="color:var(--danger);font-size:12px;margin-top:6px;display:flex;align-items:center;gap:4px;">
                    <i class="ri-error-warning-line"></i> {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-label"><span class="required">*</span> ترتيب الدرس</label>
                <input
                  type="number"
                  name="order"
                  id="lessonOrderInput"
                  class="form-input"
                  placeholder="1"
                  min="1"
                  value="{{ isset($lesson) ? ($lesson->order ?? old('order')) : ($nextOrder ?? old('order', 1)) }}"
                  required
                  {{ isset($lesson) ? '' : 'readonly' }}
                >
                @if(!isset($lesson))
                  <div class="form-hint">يتم تحديد ترتيب الدرس تلقائياً حسب عدد الدروس الحالية</div>
                @endif
              </div>
            </div>
          </div>

          <div class="btn-group">
            <button type="submit" class="btn btn-primary"><i class="ri-check-line"></i> {{ isset($lesson) ? 'حفظ التغييرات' : 'إنشاء الدرس' }}</button>
            <a href="{{ route('teacher.show', $course->id ?? 0) }}" class="btn btn-secondary"><i class="ri-close-line"></i> إلغاء</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let darkMode = false;
    function toggleDarkMode() {
      darkMode = !darkMode;
      const theme = darkMode ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', theme);
      document.body.setAttribute('data-theme', theme);
      localStorage.setItem('theme', theme);
      const icon = document.getElementById('theme-icon');
      if (icon) {
        icon.className = darkMode ? 'ri-sun-line' : 'ri-moon-line';
      }
    }

    function runPageSearch(query) {
      const normalized = query.trim().toLowerCase();
      const content = document.querySelector('.content');
      if (!content) {
        return;
      }

      // Remove previous highlights
      document.querySelectorAll('.search-highlight').forEach(el => el.classList.remove('search-highlight'));

      if (!normalized) {
        return;
      }

      const selectors = [
        '.page-title',
        '.page-subtitle',
        '.form-section-title',
        '.form-label',
        '.form-hint',
        '.type-label',
        '.type-sublabel',
        '.file-upload-title',
        '.file-upload-hint',
        '.btn'
      ].join(',');

      const candidates = Array.from(content.querySelectorAll(selectors));
      const match = candidates.find(el => el.textContent.toLowerCase().includes(normalized));

      if (match) {
        match.classList.add('search-highlight');
        match.scrollIntoView({ behavior: 'smooth', block: 'center' });
        window.setTimeout(() => match.classList.remove('search-highlight'), 1600);
      }
    }

    (function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      darkMode = savedTheme === 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
      const icon = document.getElementById('theme-icon');
      if (icon) {
        icon.className = darkMode ? 'ri-sun-line' : 'ri-moon-line';
      }
    })();

    // ===== File Storage Management (Memory-based for large files) =====
    const STORAGE_KEYS = {
      videoFile: 'lesson_temp_video_meta',
      audioFile: 'lesson_temp_audio_meta'
    };

    // Memory storage for actual file objects (avoids localStorage size limits)
    const memoryStorage = {
      videoFile: null,
      audioFile: null
    };

    function saveFileToStorage(file, type) {
      // Store file object in memory
      memoryStorage[`${type}File`] = file;

      // Store only metadata in localStorage
      try {
        const metadata = {
          name: file.name,
          type: file.type,
          size: file.size,
          timestamp: Date.now()
        };
        localStorage.setItem(STORAGE_KEYS[`${type}File`], JSON.stringify(metadata));
      } catch (error) {
        console.warn('تخزين بيانات الملف: ' + error.message);
      }
    }

    function getFileFromStorage(type) {
      // Check memory first (fastest)
      if (memoryStorage[`${type}File`]) {
        return memoryStorage[`${type}File`];
      }

      // Check if metadata exists in localStorage
      try {
        const metadataStr = localStorage.getItem(STORAGE_KEYS[`${type}File`]);
        if (metadataStr) {
          const metadata = JSON.parse(metadataStr);
          return metadata;
        }
      } catch (error) {
        console.warn('تحميل بيانات الملف: ' + error.message);
      }
      return null;
    }

    function clearFileFromStorage(type) {
      memoryStorage[`${type}File`] = null;
      try {
        localStorage.removeItem(STORAGE_KEYS[`${type}File`]);
      } catch (error) {
        console.warn('مسح الملف: ' + error.message);
      }
    }

    function clearAllTempFiles() {
      clearFileFromStorage('video');
      clearFileFromStorage('audio');
    }

    // ===== Master DOMContentLoaded Initialization =====
    document.addEventListener('DOMContentLoaded', () => {
      // 1. Load saved data on page load
      const lessonTypeInput = document.getElementById('lessonTypeInput');
      const initialType = lessonTypeInput?.value;

      // Search box behavior
      const searchInputEl = document.getElementById('searchInput');
      if (searchInputEl) {
        let searchDelay;
        searchInputEl.addEventListener('input', function(e) {
          clearTimeout(searchDelay);
          searchDelay = setTimeout(() => runPageSearch(e.target.value), 220);
        });

        searchInputEl.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            runPageSearch(e.target.value);
          }
        });
      }

      // Load files from storage on page load
      const savedVideoData = getFileFromStorage('video');
      const savedAudioData = getFileFromStorage('audio');

      if (savedVideoData) {
        displayVideoPreview(savedVideoData.name);
      } else if (@json(isset($lesson) ? 'true' : 'false') === 'true') {
        // Check if there's a saved video file path (editing mode)
        @if(isset($lesson) && $lesson->video_file)
          const videoPreviewItem = document.getElementById('videoItem');
          const videoFileName = document.getElementById('videoFileName');
          if (videoPreviewItem && videoFileName) {
            videoFileName.textContent = '{{ basename($lesson->video_file) }}';
            videoPreviewItem.style.display = 'flex';
          }
        @endif
      }

      if (savedAudioData) {
        displayAudioPreview(savedAudioData.name);
      } else if (@json(isset($lesson) ? 'true' : 'false') === 'true') {
        // Check if there's a saved audio file path (editing mode)
        @if(isset($lesson) && $lesson->audio_file)
          const audioPreviewItem = document.getElementById('audioItem');
          const audioFileName = document.getElementById('audioFileName');
          if (audioPreviewItem && audioFileName) {
            audioFileName.textContent = '{{ basename($lesson->audio_file) }}';
            audioPreviewItem.style.display = 'flex';
          }
        @endif
      }

      // 2. Content Type Selection Setup
      const contentTypeOptions = document.querySelectorAll('.content-type-option');
      let selectedContentType = 'video-upload'; // Default selection

      contentTypeOptions.forEach(option => {
        option.addEventListener('click', () => {
          contentTypeOptions.forEach(opt => opt.classList.remove('active'));
          option.classList.add('active');

          const type = option.getAttribute('data-type');
          selectedContentType = type;
          // Update the hidden input field with the selected type
          document.getElementById('lessonTypeInput').value = type;
          showContentType(type);
          updateRequiredFields(type);
          updateDurationLockForType(type);
        });
      });

      function showContentType(type) {
        const contents = document.querySelectorAll('.content-type-content');
        contents.forEach(content => content.style.display = 'none');

        const selectedContent = document.getElementById(`content-${type}`);
        if (selectedContent) {
          selectedContent.style.display = 'block';
        }
      }

      function updateRequiredFields(type) {
        // We don't use HTML required attribute on file inputs - do validation in JavaScript instead
        // This prevents form submission issues from browser validation
      }

      // Set the correct content type on page load
      if (contentTypeOptions.length > 0) {
        const lessonTypeValue = document.getElementById('lessonTypeInput').value;
        const lessonTypeOption = document.querySelector(`[data-type="${lessonTypeValue}"]`);
        if (lessonTypeOption) {
          lessonTypeOption.classList.add('active');
          showContentType(lessonTypeValue);
          selectedContentType = lessonTypeValue;
          updateRequiredFields(lessonTypeValue);
          updateDurationLockForType(lessonTypeValue);
        } else {
          contentTypeOptions[0].classList.add('active');
          const defaultType = contentTypeOptions[0].getAttribute('data-type');
          showContentType(defaultType);
          updateRequiredFields(defaultType);
          updateDurationLockForType(defaultType);
        }
      }

      // 3. Initialize file uploads
      setupFileUpload('videoFile', 'video');
      setupFileUpload('audioFile', 'audio');

      // 4. Form submission handler - CRITICAL: Prevent file from being submitted
      const lessonForm = document.getElementById('lessonForm');
      if (lessonForm) {
        // Before any submit, ensure files are cleared
        const videoFileInput = document.getElementById('videoFile');
        const audioFileInput = document.getElementById('audioFile');

        // Listen to all input changes in file inputs to prevent file from being selected again
        videoFileInput?.addEventListener('change', (e) => {
          if (e.target.files.length > 0) {
            // File was selected, handle via AJAX
            handleFileSelected(e.target.files[0], 'video', 'videoFile');
          }
        });

        audioFileInput?.addEventListener('change', (e) => {
          if (e.target.files.length > 0) {
            // File was selected, handle via AJAX
            handleFileSelected(e.target.files[0], 'audio', 'audioFile');
          }
        });

        lessonForm.addEventListener('submit', (e) => {
          try {
            console.log('Form submit event triggered');
            e.preventDefault(); // Prevent default to add validation

            // CRITICAL: Clear all file inputs before submission to prevent POST Too Large
            videoFileInput.value = '';
            audioFileInput.value = '';

            const selectedType = document.getElementById('lessonTypeInput').value;
            const videoPath = document.getElementById('videoFilePathInput').value;
            const audioPath = document.getElementById('audioFilePathInput').value;
            const nameInput = document.querySelector('input[name="name"]');
            const descriptionInput = document.querySelector('textarea[name="description"]');
            const orderInput = document.querySelector('input[name="order"]');
            const contentInput = document.querySelector('textarea[name="content"]');
            const videoUrlInput = document.querySelector('input[name="video_url"]');

            console.log('Form validation check:', { selectedType, videoPath, audioPath, name: nameInput?.value, description: descriptionInput?.value, order: orderInput?.value });

            // Basic validation first
            if (!nameInput?.value || !nameInput.value.trim()) {
              showValidationError('يجب إدخال عنوان الدرس');
              return;
            }

            if (!descriptionInput?.value || !descriptionInput.value.trim()) {
              showValidationError('يجب إدخال وصف الدرس');
              return;
            }

            if (!orderInput?.value) {
              showValidationError('يجب إدخال ترتيب الدرس');
              return;
            }

            // Validation: Check if required file/content is present based on type
            if (selectedType === 'video-upload' && !videoPath) {
              console.log('Validation failed: video required but not uploaded');
              showValidationError('يجب رفع ملف فيديو أولاً');
              return;
            } else if (selectedType === 'audio-upload' && !audioPath) {
              console.log('Validation failed: audio required but not uploaded');
              showValidationError('يجب رفع ملف صوتي أولاً');
              return;
            } else if (selectedType === 'other-content' && (!contentInput?.value || !contentInput.value.trim())) {
              console.log('Validation failed: content required but not provided');
              showValidationError('يجب إدخال محتوى إضافي');
              return;
            } else if (selectedType === 'youtube' && (!videoUrlInput?.value || !videoUrlInput.value.trim())) {
              console.log('Validation failed: youtube url required but not provided');
              showValidationError('يجب إدخال رابط YouTube');
              return;
            }

            console.log('Form validation passed, submitting form');
            // Allow form submission - file inputs are already cleared
            lessonForm.submit(); // Explicitly submit the form
          } catch (error) {
            console.error('Error in form submission handler:', error);
            showValidationError('حدث خطأ: ' + error.message);
          }
        });
      }

      // 5. Check if duration was pre-filled on page load (for edit mode)
      const durationInput = document.getElementById('durationInput');
      if (durationInput && durationInput.value) {
        // Check if this is edit mode with existing data
        const videoUrl = document.querySelector('input[name="video_url"]')?.value;
        const videoFile = document.getElementById('videoFile')?.files?.length > 0;
        const audioFile = document.getElementById('audioFile')?.files?.length > 0;
        const savedVideoData = getFileFromStorage('video');
        const savedAudioData = getFileFromStorage('audio');

        if (videoUrl || videoFile || audioFile || savedVideoData || savedAudioData) {
          lockDurationInput();
        }
      }
    });

    // Event listeners for inline-event-free buttons
    document.getElementById('darkBtn')?.addEventListener('click', toggleDarkMode);
    document.getElementById('clearVideoBtn')?.addEventListener('click', clearVideoFile);
    document.getElementById('clearAudioBtn')?.addEventListener('click', clearAudioFile);

    // ===== AJAX File Upload Handler =====
    async function uploadFileViaAJAX(file, type) {
      const formData = new FormData();
      formData.append('file', file);
      formData.append('type', type);
      formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

      try {
        const response = await fetch('{{ route("teacher.api.upload-lesson-file") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
          },
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          // Store file path in hidden input
          if (type === 'video') {
            document.getElementById('videoFilePathInput').value = data.path;
            // IMPORTANT: Clear the actual file input to prevent it from being submitted
            document.getElementById('videoFile').value = '';
          } else if (type === 'audio') {
            document.getElementById('audioFilePathInput').value = data.path;
            // IMPORTANT: Clear the actual file input to prevent it from being submitted
            document.getElementById('audioFile').value = '';
          }

          return {
            success: true,
            path: data.path,
            filename: data.filename,
            size: data.size
          };
        } else {
          throw new Error(data.message || 'خطأ في الرفع');
        }
      } catch (error) {
        console.error('Upload error:', error);
        throw error;
      }
    }
    function setupFileUpload(inputId, uploadType) {
      const fileInput = document.getElementById(inputId);
      const uploadLabel = fileInput?.previousElementSibling;

      if (!fileInput) return;

      // Click to select file
      fileInput.addEventListener('change', (e) => {
        handleFileSelected(e.target.files[0], uploadType, inputId);
      });

      // Drag and drop
      if (uploadLabel) {
        uploadLabel.addEventListener('dragover', (e) => {
          e.preventDefault();
          uploadLabel.classList.add('drag-over');
        });

        uploadLabel.addEventListener('dragleave', () => {
          uploadLabel.classList.remove('drag-over');
        });

        uploadLabel.addEventListener('drop', (e) => {
          e.preventDefault();
          uploadLabel.classList.remove('drag-over');
          const files = e.dataTransfer.files;
          if (files.length > 0) {
            handleFileSelected(files[0], uploadType, inputId);
          }
        });
      }
    }

    async function handleFileSelected(file, type, inputId) {
      if (!file) return;

      const maxSize = type === 'video' ? 500 * 1024 * 1024 : 100 * 1024 * 1024;

      if (file.size > maxSize) {
        alert(`الملف كبير جداً! الحد الأقصى: ${type === 'video' ? '500' : '100'} MB`);
        document.getElementById(inputId).value = '';
        return;
      }

      // Show loading indicator
      const loadingId = type === 'video' ? 'videoLoading' : 'audioLoading';
      const loadingElement = document.getElementById(loadingId);
      if (loadingElement) {
        loadingElement.style.display = 'flex';
      }

      try {
        // Upload file via AJAX
        const uploadResult = await uploadFileViaAJAX(file, type);

        // Save file to storage (keep in memory)
        saveFileToStorage(file, type);

        // Display preview
        if (type === 'video') {
          displayVideoPreview(file.name);
        } else if (type === 'audio') {
          displayAudioPreview(file.name);
        }

        // Extract duration and lock field
        try {
          const durationFormatted = await extractDurationFromFile(file);
          document.getElementById('durationInput').value = durationFormatted;
          lockDurationInput();
          showDurationSuccess(`تم استخراج المدة تلقائياً: ${durationFormatted}`);
        } catch (error) {
          console.log('تعذر استخراج المدة:', error.message);
          // Leave field unlocked so teacher can enter manually if extraction failed
        }

        showUploadSuccess(`تم رفع ${type === 'video' ? 'الفيديو' : 'الملف الصوتي'} بنجاح`);
      } catch (error) {
        showUploadError(`فشل رفع الملف: ${error.message}`);
        document.getElementById(inputId).value = '';
      } finally {
        // Hide loading indicator
        if (loadingElement) {
          loadingElement.style.display = 'none';
        }
      }
    }

    function displayVideoPreview(fileName) {
      const videoPreviewItem = document.getElementById('videoItem');
      const videoFileName = document.getElementById('videoFileName');
      if (videoPreviewItem && videoFileName) {
        videoFileName.textContent = fileName;
        videoPreviewItem.style.display = 'flex';
      }
    }

    function displayAudioPreview(fileName) {
      const audioPreviewItem = document.getElementById('audioItem');
      const audioFileName = document.getElementById('audioFileName');
      if (audioPreviewItem && audioFileName) {
        audioFileName.textContent = fileName;
        audioPreviewItem.style.display = 'flex';
      }
    }

    function clearVideoFile() {
      document.getElementById('videoFile').value = '';
      document.getElementById('videoItem').style.display = 'none';
      document.getElementById('videoFilePathInput').value = ''; // Clear file path
      clearFileFromStorage('video');
      unlockDurationInput();
    }

    function clearAudioFile() {
      document.getElementById('audioFile').value = '';
      document.getElementById('audioItem').style.display = 'none';
      document.getElementById('audioFilePathInput').value = ''; // Clear file path
      clearFileFromStorage('audio');
      unlockDurationInput();
    }

    // ===== Upload Success/Error Messages =====
    function showUploadSuccess(message) {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, var(--success), #1cc55e);
        color: white;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(52, 199, 89, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideInRight 0.4s ease-out;
      `;
      toast.innerHTML = '<i class="ri-check-line" style="font-size: 18px;"></i>' + message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.4s ease-out forwards';
        setTimeout(() => toast.remove(), 400);
      }, 3000);
    }

    function showUploadError(message) {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: linear-gradient(135deg, var(--danger), #FF1744);
        color: white;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(255, 59, 48, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideInLeft 0.4s ease-out;
      `;
      toast.innerHTML = '<i class="ri-error-warning-line" style="font-size: 18px;"></i>' + message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.animation = 'slideOutLeft 0.4s ease-out forwards';
        setTimeout(() => toast.remove(), 400);
      }, 4000);
    }

    // ===== Validation Error Display =====
    function showValidationError(message) {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: linear-gradient(135deg, var(--danger), #FF1744);
        color: white;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(255, 59, 48, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideInLeft 0.4s ease-out;
      `;
      toast.innerHTML = '<i class="ri-error-warning-line" style="font-size: 18px;"></i>' + message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.animation = 'slideOutLeft 0.4s ease-out forwards';
        setTimeout(() => toast.remove(), 400);
      }, 4000);
    }

    // ===== Auto-fill Duration from Video/Audio Files =====
    function formatDurationSeconds(seconds) {
      seconds = Math.round(seconds);
      const minutes = Math.floor(seconds / 60);
      const secs = seconds % 60;
      return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }

    function extractDurationFromFile(file) {
      // createObjectURL avoids loading the entire file into memory — works for any size
      return new Promise((resolve, reject) => {
        const isAudio = file.type.startsWith('audio');
        const media   = document.createElement(isAudio ? 'audio' : 'video');
        const url     = URL.createObjectURL(file);
        let settled   = false;

        const cleanup = () => URL.revokeObjectURL(url);

        // 30-second timeout guard (browser needs to read enough of the file for metadata)
        const timeout = setTimeout(() => {
          if (!settled) { settled = true; cleanup(); reject(new Error('استغرق تحليل الملف وقتاً طويلاً')); }
        }, 30000);

        media.onloadedmetadata = () => {
          clearTimeout(timeout);
          if (settled) return;
          settled = true;
          cleanup();
          const secs = Math.round(media.duration);
          if (!isFinite(secs) || secs <= 0) return reject(new Error('مدة غير صالحة'));
          resolve(formatDurationSeconds(secs));
        };

        media.onerror = () => {
          clearTimeout(timeout);
          if (!settled) { settled = true; cleanup(); reject(new Error('الكودك غير مدعوم في المتصفح، أدخل المدة يدوياً')); }
        };

        media.preload = 'metadata';
        media.src = url;
      });
    }

    // Initialize file uploads is now handled in Master DOMContentLoaded

    // ===== YouTube Duration Auto-fill Functions =====
    function getYouTubeVideoId(url) {
      // Supports all YouTube URL formats:
      // https://youtube.com/watch?v=xxxx
      // https://www.youtube.com/watch?v=xxxx
      // https://youtu.be/xxxx
      // https://www.youtu.be/xxxx
      // https://youtube.com/embed/xxxx
      // https://youtube.com/shorts/xxxx
      // https://youtube.com/live/xxxx
      // https://youtube.com/watch?v=xxxx&t=10s (with timestamps)
      const regex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/|youtube\.com\/live\/)([^&\n?#]+)/;
      const match = url.match(regex);
      return match ? match[1] : null;
    }

    const videoUrlInput = document.querySelector('input[name="video_url"]');
    let durationFetchTimeout = null;
    let lastFetchedUrl = ''; // Track last fetched URL to avoid duplicate requests
    let youtubeRequestToken = 0;
    let youtubeInFlightUrl = '';
    let lastWarningUrl = '';
    let lastSuccessUrl = '';

    if (videoUrlInput) {
      // Helper function to process YouTube URL
      async function processYouTubeUrl(url) {
        const loadingIndicator = document.getElementById('youtubeLoading');

        if (!url) {
          unlockDurationInput();
          if (loadingIndicator) loadingIndicator.style.display = 'none';
          lastFetchedUrl = '';
          youtubeInFlightUrl = '';
          lastWarningUrl = '';
          lastSuccessUrl = '';
          return;
        }

        // Skip if same URL as last fetch
        if (url === lastFetchedUrl || url === youtubeInFlightUrl) {
          return;
        }

        if (url && (url.includes('youtube.com') || url.includes('youtu.be'))) {
          const videoId = getYouTubeVideoId(url);

          if (!videoId) {
            if (lastWarningUrl !== url) {
              showDurationWarning('رابط YouTube غير صحيح');
              lastWarningUrl = url;
            }
            unlockDurationInput();
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            lastFetchedUrl = '';
            return;
          }

          // Show loading indicator
          if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
          }

          const currentToken = ++youtubeRequestToken;
          youtubeInFlightUrl = url;

          // Send request to backend to get duration
          try {
            const response = await fetch('{{ route("teacher.api.youtube-duration") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
              },
              body: JSON.stringify({ url: url })
            });

            const data = await response.json();
            if (currentToken !== youtubeRequestToken) {
              return;
            }

            if (data.success && data.duration) {
              document.getElementById('durationInput').value = data.duration;
              lockDurationInput();
              if (lastSuccessUrl !== url) {
                showDurationSuccess(data.message);
                lastSuccessUrl = url;
              }
              lastFetchedUrl = url;
              lastWarningUrl = '';
            } else {
              if (lastWarningUrl !== url) {
                showDurationWarning(data.hint || data.error || 'تعذر استخراج المدة — أدخلها يدويًا في حقل المدة');
                lastWarningUrl = url;
              }
              unlockDurationInput();
              lastFetchedUrl = '';
              lastSuccessUrl = '';
            }
          } catch (error) {
            console.log('خطأ في الطلب:', error);
            if (lastWarningUrl !== url) {
              showDurationWarning('خطأ في الاتصال - يمكنك إدخال المدة يدوياً');
              lastWarningUrl = url;
            }
            unlockDurationInput();
            lastFetchedUrl = '';
            lastSuccessUrl = '';
          } finally {
            if (currentToken === youtubeRequestToken) {
              youtubeInFlightUrl = '';
            }
            // Hide loading indicator
            if (loadingIndicator) {
              loadingIndicator.style.display = 'none';
            }
          }
        } else {
          if (lastWarningUrl !== url) {
            showDurationWarning('الرابط المدخل ليس رابط YouTube صالح');
            lastWarningUrl = url;
          }
          unlockDurationInput();
          if (loadingIndicator) loadingIndicator.style.display = 'none';
          lastFetchedUrl = '';
          lastSuccessUrl = '';
          youtubeInFlightUrl = '';
        }
      }

      // Debounce function for input events
      function debounce(func, wait) {
        return function executedFunction(...args) {
          const later = () => {
            clearTimeout(durationFetchTimeout);
            func(...args);
          };
          clearTimeout(durationFetchTimeout);
          durationFetchTimeout = setTimeout(later, wait);
        };
      }

      // Blur event - immediate processing
      videoUrlInput.addEventListener('blur', async (e) => {
        const url = e.target.value.trim();
        await processYouTubeUrl(url);
      });

      // Input event with debounce - for real-time processing while typing
      const debouncedProcess = debounce(async (e) => {
        const url = e.target.value.trim();
        await processYouTubeUrl(url);
      }, 800); // 800ms delay

      videoUrlInput.addEventListener('input', debouncedProcess);

      // Change event - for paste operations
      videoUrlInput.addEventListener('change', async (e) => {
        const url = e.target.value.trim();
        await processYouTubeUrl(url);
      });

      // Paste event - immediate processing
      videoUrlInput.addEventListener('paste', async (e) => {
        // Wait for paste to complete
        setTimeout(async () => {
          const url = e.target.value.trim();
          await processYouTubeUrl(url);
        }, 100);
      });
    }

    // Manage duration lock state based on content type
    // other-content (text) → teacher types manually → unlock
    // video-upload / audio-upload / youtube → auto-filled only → lock
    function updateDurationLockForType(type) {
      const durationInput = document.getElementById('durationInput');
      if (!durationInput) return;

      if (type === 'other-content') {
        // Text content has no extractable media → let teacher enter manually
        unlockDurationInput();
        // Don't clear existing value in edit mode — teacher may have typed it before
      } else {
        // Media types: lock immediately; value will be filled by auto-extraction
        // Clear only if the field is empty (don't wipe a value the teacher already has)
        // But do lock it — it must come from the extractor, not manual typing
        lockDurationInput();
      }
    }

    // Lock/Unlock duration input
    function lockDurationInput() {
      const input = document.getElementById('durationInput');
      // Use readonly instead of disabled to allow form submission
      input.readOnly = true;
      input.style.opacity = '0.8';
      input.style.cursor = 'default';
      input.style.backgroundColor = input.value ? 'rgba(52, 199, 89, 0.08)' : '';
      input.title = 'تُستخرج المدة تلقائياً من الملف المرفوع';

      // Update hint text
      const hint = document.getElementById('durationHint');
      if (hint) hint.textContent = '(تُستخرج تلقائياً من الملف)';

      // Add visual indicator only when value is present
      const existing = input.parentElement.querySelector('.auto-filled-indicator');
      if (input.value && !existing) {
        const indicator = document.createElement('div');
        indicator.className = 'auto-filled-indicator';
        indicator.innerHTML = '<i class="ri-check-line"></i> تم ملؤها تلقائياً من الفيديو';
        indicator.style.cssText = 'font-size:11px;color:var(--success);font-weight:600;margin-top:4px;display:flex;align-items:center;gap:4px;';
        input.parentElement.appendChild(indicator);
      } else if (!input.value && existing) {
        existing.remove();
      }
    }

    function unlockDurationInput() {
      const input = document.getElementById('durationInput');
      input.readOnly = false;
      input.style.opacity = '1';
      input.style.cursor = 'text';
      input.style.backgroundColor = '';
      input.title = 'أدخل المدة يدوياً بصيغة دقائق:ثواني (مثال: 5:30)';
      input.placeholder = 'مثال: 5:30';

      // Update hint text
      const hint = document.getElementById('durationHint');
      if (hint) hint.textContent = '(أدخل المدة يدوياً)';

      // Remove indicator
      const indicator = input.parentElement.querySelector('.auto-filled-indicator');
      if (indicator) indicator.remove();
    }

    // ===== Duration Success/Warning Messages =====
    function showDurationSuccess(message) {
      const durationInput = document.getElementById('durationInput');
      const originalBorder = durationInput.style.borderColor;
      const originalBg = durationInput.style.backgroundColor;

      durationInput.style.borderColor = 'var(--success)';
      durationInput.style.backgroundColor = 'rgba(52, 199, 89, 0.08)';
      durationInput.style.boxShadow = '0 0 0 3px rgba(52, 199, 89, 0.2)';

      // Create toast notification
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, var(--success), #1cc55e);
        color: white;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(52, 199, 89, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideInRight 0.4s ease-out;
      `;
      toast.innerHTML = '<i class="ri-check-line" style="font-size: 18px;"></i>' + message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.4s ease-out forwards';
        setTimeout(() => toast.remove(), 400);
      }, 3000);

      setTimeout(() => {
        durationInput.style.borderColor = originalBorder;
        durationInput.style.backgroundColor = originalBg;
        durationInput.style.boxShadow = '';
      }, 2000);
    }

    function showDurationWarning(message) {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: linear-gradient(135deg, #FF9500, #FF7A00);
        color: white;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(255, 149, 0, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideInLeft 0.4s ease-out;
      `;
      toast.innerHTML = '<i class="ri-alert-line" style="font-size: 18px;"></i>' + message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.animation = 'slideOutLeft 0.4s ease-out forwards';
        setTimeout(() => toast.remove(), 400);
      }, 4000);
    }
  </script>
@include('components.notification-bell')
    @include('components.account-theme-foot')

{{-- ══ Duration Tracker Card — always visible ═══════════════════════ --}}
@php
  $hasBudget    = isset($totalSeconds) && $totalSeconds > 0;
  $courseEditUrl = route('teacher.edit', $course->id ?? 0);
@endphp

<div id="durationTracker" style="
  position: fixed;
  bottom: 24px;
  left: 24px;
  z-index: 9000;
  background: var(--theme-surface, #1a1a1a);
  border: 1px solid var(--border, #333);
  border-radius: 16px;
  padding: 16px 20px;
  min-width: 240px;
  max-width: 280px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.22);
  font-family: 'Tajawal', sans-serif;
  transition: box-shadow 0.3s, border-color 0.3s;
  direction: rtl;
">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <i class="ri-timer-2-line" style="font-size:18px;color:var(--gold,#C6A675);"></i>
    <span style="font-size:13px;font-weight:700;color:var(--text-primary);">ميزانية وقت المسار</span>
  </div>

@if($hasBudget)
  {{-- Full tracker when course has a duration --}}
  <div style="display:flex;flex-direction:column;gap:8px;">
    <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;">
      <span style="color:var(--text-secondary);">الإجمالي</span>
      <span id="dtTotal" style="font-weight:700;color:var(--text-primary);">—</span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;">
      <span style="color:var(--text-secondary);">المستخدم</span>
      <span id="dtUsed" style="font-weight:700;color:#FF9F40;">—</span>
    </div>
    <div style="height:1px;background:var(--border);margin:2px 0;"></div>
    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
      <span style="font-weight:700;color:var(--text-primary);">المتبقي</span>
      <span id="dtRemain" style="font-weight:800;color:#34C759;">—</span>
    </div>
  </div>
  <div style="margin-top:12px;background:var(--border);border-radius:8px;height:6px;overflow:hidden;">
    <div id="dtBar" style="height:100%;border-radius:8px;background:linear-gradient(90deg,#34C759,#C6A675);width:0%;transition:width 0.4s,background 0.4s;"></div>
  </div>
  <div id="dtWarning" style="display:none;margin-top:10px;background:rgba(255,59,48,0.12);border:1px solid rgba(255,59,48,0.3);border-radius:8px;padding:8px 10px;font-size:11px;color:#FF3B30;align-items:center;gap:6px;">
    <i class="ri-error-warning-line"></i>
    <span id="dtWarningText">مدة الدرس تتجاوز الوقت المتبقي</span>
  </div>
@else
  {{-- No duration set yet — prompt teacher to configure the course --}}
  <div style="font-size:12px;color:var(--text-secondary);line-height:1.6;margin-bottom:10px;">
    لم تُحدَّد مدة إجمالية للمسار بعد.<br>
    أضِفها لتفعيل التحكم في الوقت.
  </div>
  <a href="{{ $courseEditUrl }}"
     style="display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 12px;border-radius:10px;background:rgba(198,166,117,0.15);border:1px solid rgba(198,166,117,0.35);color:var(--gold,#C6A675);font-size:12px;font-weight:700;text-decoration:none;transition:background 0.2s;"
     onmouseover="this.style.background='rgba(198,166,117,0.25)'"
     onmouseout="this.style.background='rgba(198,166,117,0.15)'">
    <i class="ri-settings-3-line" style="font-size:14px;"></i>
    تعديل إعدادات المسار
  </a>
@endif
</div>

@if($hasBudget)
<script>
(function() {
  const TOTAL_SECS = {{ (int)$totalSeconds }};
  const USED_SECS  = {{ (int)$usedSeconds }};

  function secsToHuman(s) {
    if (s <= 0) return '0 د';
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    const sec = s % 60;
    const parts = [];
    if (h > 0)             parts.push(h + ' س');
    if (m > 0)             parts.push(m + ' د');
    if (sec > 0 && h === 0) parts.push(sec + ' ث');
    return parts.join(' ') || '0 د';
  }

  function parseDuration(val) {
    if (!val) return 0;
    const parts = val.trim().split(':').map(Number);
    if (parts.length === 3) return parts[0]*3600 + parts[1]*60 + parts[2];
    if (parts.length === 2) return parts[0]*60   + parts[1];
    return 0;
  }

  function updateTracker(newLessonSecs) {
    const used      = USED_SECS + newLessonSecs;
    const remaining = TOTAL_SECS - used;
    const pct       = Math.min(100, Math.round((used / TOTAL_SECS) * 100));
    const exceeded  = remaining < 0;

    document.getElementById('dtTotal').textContent  = secsToHuman(TOTAL_SECS);
    document.getElementById('dtUsed').textContent   = secsToHuman(used);
    document.getElementById('dtRemain').textContent =
      exceeded ? ('تجاوز بـ ' + secsToHuman(-remaining)) : secsToHuman(remaining);
    document.getElementById('dtRemain').style.color = exceeded ? '#FF3B30' : '#34C759';

    const bar = document.getElementById('dtBar');
    bar.style.width      = pct + '%';
    bar.style.background = exceeded
      ? 'linear-gradient(90deg,#FF3B30,#FF9500)'
      : pct > 80
        ? 'linear-gradient(90deg,#FF9F40,#C6A675)'
        : 'linear-gradient(90deg,#34C759,#C6A675)';

    const warn = document.getElementById('dtWarning');
    if (exceeded) {
      warn.style.display = 'flex';
      document.getElementById('dtWarningText').textContent =
        'مدة الدرس تتجاوز الوقت المتبقي! عدّل مدة المسار لحفظ هذا الدرس.';
      document.getElementById('durationTracker').style.borderColor = 'rgba(255,59,48,0.5)';
    } else {
      warn.style.display = 'none';
      document.getElementById('durationTracker').style.borderColor = 'var(--border)';
    }
  }

  const dInput = document.getElementById('durationInput');
  updateTracker(parseDuration(dInput ? dInput.value : ''));
  if (dInput) dInput.addEventListener('input', function() { updateTracker(parseDuration(this.value)); });
})();
</script>
@endif

</body>
</html>



