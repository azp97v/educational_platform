<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إنشاء اختبار جديد - لوحة التحكم</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root { --sidebar-w: 300px; --topbar-h: 70px; }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Tajawal', sans-serif;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.16), transparent 22%), linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%);
      color: var(--text-primary);
      min-height: 100vh;
      transition: background 0.3s, color 0.3s;
      position: relative;
      overflow-x: hidden;
    }
    body::before {
      content: '';
      position: fixed;
      top: 16px;
      left: 16px;
      width: 320px;
      height: 320px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,214,122,0.24), transparent 55%);
      filter: blur(72px);
      z-index: 0;
      pointer-events: none;
    }

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
      animation: float 3s ease-in-out infinite;
      overflow: hidden;
    }
    .logo-icon img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 14px;
      display: block;
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

    .search-wrap { width: 400px; position: relative; }
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
    .search-wrap input::placeholder {
      color: var(--text-muted);
      font-weight: 500;
    }
    .search-wrap input:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px var(--gold-light);
    }
    .search-icon {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 18px;
      pointer-events: none;
      transition: var(--transition);
    }
    .search-wrap input:focus ~ .search-icon {
      color: var(--gold);
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
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
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
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
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
      box-shadow: var(--shadow-hover) , 0 0 30px rgba(196,150,58,0.15);
      transform: translateY(-3px);
      transition: var(--transition);
      border-left-color: var(--gold);
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
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
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
      from {
        width: 0;
        opacity: 0;
      }
      to {
        width: 100%;
        opacity: 1;
      }
    }

    .form-group {
      margin-bottom: 22px;
      animation: fadeIn 0.5s ease-out forwards;
      opacity: 0;
    }
    @keyframes fadeIn {
      to {
        opacity: 1;
      }
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
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      position: relative;
      transition: var(--transition);
    }
    .form-label::after {
      content: '';
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, var(--gold), transparent);
      transition: var(--transition);
      position: absolute;
      right: 0;
      bottom: -4px;
    }
    .form-group:focus-within .form-label::after {
      width: 100%;
    }

    .form-input, .form-select, .form-textarea {
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
      position: relative;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
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
      font-family: 'Tajawal', sans-serif;
      max-height: 300px;
    }

    /* Select Options Styling */
    .form-select option {
      background: var(--card-bg);
      color: var(--text-primary);
      padding: 12px 16px;
      border: none;
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      font-weight: 500;
    }

    .form-select option:checked {
      background-color: var(--gold);
      color: #fff;
      font-weight: 600;
    }

    .form-select option:hover {
      background-color: rgba(196,150,58,0.2);
      color: var(--text-primary);
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
      justify-content: flex-end;
      padding-top: 24px;
      border-top: 2px solid rgba(196,150,58,0.1);
      animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
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
      text-transform: none;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      right: 100%;
      width: 100%;
      height: 100%;
      background: rgba(255,255,255,0.15);
      transition: var(--transition);
      z-index: -1;
    }

    .btn:hover::before {
      right: 0;
    }

    .btn::after {
      content: '';
      position: absolute;
      top: 50%;
      right: -20%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.2);
      transition: all 0.6s ease;
      transform: translate(-50%, -50%);
    }

    .btn:active::after {
      width: 200px;
      height: 200px;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      color: white;
      box-shadow: 0 4px 16px rgba(196,150,58,0.3), 0 2px 8px rgba(0,0,0,0.1);
      position: relative;
    }
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(196,150,58,0.4), 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-primary:active {
      transform: translateY(-1px);
    }

    .btn-secondary {
      background: transparent;
      color: var(--text-secondary);
      border: 1.5px solid rgba(196,150,58,0.4);
      position: relative;
    }
    .btn-secondary:hover {
      background: linear-gradient(135deg, rgba(196,150,58,0.08), rgba(196,150,58,0.04));
      color: var(--gold);
      border-color: var(--gold);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(196,150,58,0.15);
    }

    /* Disabled Button State */
    .btn:disabled, button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    .btn-primary:disabled {
      background: linear-gradient(135deg, #A0A0A6, #8A8A8F);
      color: #F2F2F7;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-primary:disabled:hover {
      transform: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .alert {
      padding: 16px;
      border-radius: var(--radius-lg);
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 14px;
      border-left: 4px solid;
      position: relative;
      overflow: hidden;
      animation: slideInBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      backdrop-filter: blur(10px);
    }

    @keyframes slideInBounce {
      0% {
        opacity: 0;
        transform: translateX(30px);
      }
      70% {
        transform: translateX(-5px);
      }
      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .alert::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, transparent, rgba(255,255,255,0.2));
      pointer-events: none;
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% {
        transform: translateX(100%);
      }
      100% {
        transform: translateX(-100%);
      }
    }

    .alert-warning {
      background: linear-gradient(135deg, rgba(255,165,0,0.1), rgba(255,165,0,0.04));
      border-left-color: #FF9500;
      color: var(--text-primary);
      box-shadow: 0 4px 16px rgba(255,165,0,0.1), inset 0 1px 0 rgba(255,255,255,0.3);
    }

    .alert-info {
      background: linear-gradient(135deg, rgba(198,166,117,0.1), rgba(198,166,117,0.04));
      border-left-color: #C6A675;
      color: var(--text-primary);
      box-shadow: 0 4px 16px rgba(198,166,117,0.1), inset 0 1px 0 rgba(255,255,255,0.3);
    }
    html.teacher-account[data-theme="light"] .alert-info {
      background: linear-gradient(135deg, rgba(67, 126, 247, 0.16), rgba(67, 126, 247, 0.08));
      border-left-color: #2f67d8;
      color: #1f2f4d !important;
      box-shadow: 0 8px 22px rgba(67, 126, 247, 0.18), inset 0 1px 0 rgba(255,255,255,0.75);
    }
    html.teacher-account[data-theme="light"] .alert-info strong,
    html.teacher-account[data-theme="light"] .alert-info p,
    html.teacher-account[data-theme="light"] .alert-info div,
    html.teacher-account[data-theme="light"] .alert-info span,
    html.teacher-account[data-theme="light"] .alert-info i {
      color: #1f2f4d !important;
      opacity: 1 !important;
    }

    .sidebar-footer { margin-top: auto; }

    .sidebar-footer form { width: 100%; }

    .logout-btn {
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 18px;
      background: transparent;
      color: var(--danger);
      border: none;
      border-radius: var(--radius-lg);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: var(--transition);
    }
    .logout-btn:hover { background: rgba(255,59,48,0.08); }

    @media (max-width: 1024px) {
      .main { margin-right: 90px !important; }
    }

    @media (max-width: 768px) {
      .main { margin-right: 0 !important; }
      .form-row { grid-template-columns: 1fr; }
      .content { padding: 20px; }
    }

    /* Extra Creative Touches */
    .content form {
      animation: pageLoad 0.8s ease-out;
    }
    @keyframes pageLoad {
      from {
        opacity: 0;
        filter: blur(20px);
      }
      to {
        opacity: 1;
        filter: blur(0);
      }
    }

    /* Stagger animation for form sections */
    .form-section:nth-of-type(1) {
      animation-delay: 0.1s;
    }
    .form-section:nth-of-type(2) {
      animation-delay: 0.2s;
    }
    .form-section:nth-of-type(3) {
      animation-delay: 0.3s;
    }

    /* Enhanced placeholder styling */
    .form-input::placeholder,
    .form-textarea::placeholder {
      color: var(--text-muted);
      font-style: italic;
      font-weight: 400;
    }

    /* Number input enhancement */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    input[type="number"] {
      -moz-appearance: textfield;
    }

    /* Focus visible state */
    .form-input:focus-visible,
    .form-textarea:focus-visible,
    .form-select:focus-visible {
      outline: 2px solid transparent;
      outline-offset: 2px;
    }

    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
    }

    /* Content form wrapper */
    .content form {
      position: relative;
      z-index: 1;
    }

    /* Alert icon animation */
    .alert i {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      animation: bounce 2s infinite;
    }
    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-4px);
      }
    }

    /* ===== HIDE SCROLLBAR ===== */
    ::-webkit-scrollbar {
      display: none;
    }

    .sidebar,
    .main {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
</head>
<body>
  @include('components.alerts')
  <div class="app">
    @include('components.sidebar-unified')

    <!-- MAIN -->
    <div class="main">
      <!-- TOPBAR -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger-btn" id="sidebarToggle" title="فتح القائمة">
            <i class="ri-menu-line"></i>
          </button>
          <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
            <i class="ri-moon-line" id="darkIcon"></i>
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

      <!-- CONTENT -->
      <div class="content">
        <div class="page-header">
          <h1 class="page-title">إنشاء اختبار جديد</h1>
          <p class="page-subtitle">قم بتحديد بيانات الاختبار وإضافة الأسئلة</p>
        </div>

        @if($errors->any())
          <div class="alert alert-warning">
            <i class="ri-alert-line"></i>
            <div>
              <strong>حدثت بعض الأخطاء:</strong>
              <ul style="margin-top: 8px; margin-right: 12px;">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        @if(session('success'))
          <div class="alert" style="background: linear-gradient(135deg, rgba(52,199,89,0.1), rgba(52,199,89,0.04)); border-left-color: #34C759; color: var(--text-primary); box-shadow: 0 4px 16px rgba(52,199,89,0.1), inset 0 1px 0 rgba(255,255,255,0.3);">
            <i class="ri-check-double-line" style="color: #34C759;"></i>
            <div>
              <strong>رائع!</strong><br>
              {{ session('success') }}
            </div>
          </div>
        @endif

        @php
          $lessonId = request()->query('lesson');
          $formAction = $lessonId ? route('teacher.createExam', ['lesson' => $lessonId]) : '#';
        @endphp

        <form action="{{ $formAction }}" method="POST" id="createExamForm" data-route-template="{{ $lessonId ? '' : route('teacher.createExam', ['lesson' => '__LESSON__']) }}">
          @csrf

          <!-- Basic Information Section -->
          <div class="form-section">
            <div class="form-section-title">معلومات الاختبار الأساسية</div>

            <div class="form-group">
              <label class="form-label" for="lesson_id">اختر الدرس</label>
              <select
                id="lesson_id"
                name="lesson_id"
                class="form-select"
                required
               
              >
                <option value="">-- اختر درساً --</option>
                @forelse($lessons as $lesson)
                  <option value="{{ $lesson->id }}" {{ request()->query('lesson') == $lesson->id ? 'selected' : '' }}>
                    {{ $lesson->title }} ({{ $lesson->course->name }})
                  </option>
                @empty
                  <option value="" disabled>لا توجد دروس متاحة</option>
                @endforelse
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="examName">اسم الاختبار</label>
              <input
                type="text"
                id="examName"
                name="name"
                class="form-input"
                placeholder="مثال: اختبار الفصل الأول"
                value="{{ old('name') }}"
                required
              >
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="passingScore">الحد الأدنى للنجاح</label>
                <input
                  type="number"
                  id="passingScore"
                  name="passing_score"
                  class="form-input"
                  placeholder="مثال: 70"
                  value="{{ old('passing_score', 70) }}"
                  min="0"
                  max="100"
                  required
                >
              </div>

              <div class="form-group">
                <label class="form-label" for="attemptsAllowed">عدد المحاولات المسموح</label>
                <select
                  id="attemptsAllowed"
                  name="attempts_allowed"
                  class="form-select"
                  required
                >
                  <option value="1" {{ old('attempts_allowed', 1) == 1 ? 'selected' : '' }}>محاولة واحدة</option>
                  <option value="2" {{ old('attempts_allowed', 1) == 2 ? 'selected' : '' }}>محاولتان</option>
                  <option value="3" {{ old('attempts_allowed', 1) == 3 ? 'selected' : '' }}>3 محاولات</option>
                  <option value="5" {{ old('attempts_allowed', 1) == 5 ? 'selected' : '' }}>5 محاولات</option>
                  <option value="10" {{ old('attempts_allowed', 1) == 10 ? 'selected' : '' }}>10 محاولات</option>
                  <option value="0" {{ old('attempts_allowed', 1) == 0 ? 'selected' : '' }}>غير محدود</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Exam Settings Section -->
          <div class="form-section">
            <div class="form-section-title">إعدادات الاختبار</div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="duration">مدة الاختبار (بالدقائق)</label>
                <input
                  type="number"
                  id="duration"
                  name="duration"
                  class="form-input"
                  placeholder="30"
                  value="{{ old('duration', 30) }}"
                  min="1"
                  max="600"
                >
              </div>

              <div class="form-group">
                <label class="form-label" for="expires_at">تاريخ انتهاء الاختبار</label>
                <input
                  type="datetime-local"
                  id="expires_at"
                  name="expires_at"
                  class="form-input"
                  value="{{ old('expires_at') }}"
                >
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', 0) ? 'checked' : '' }}>
                <span>نشر الاختبار للطلاب</span>
              </label>
            </div>
          </div>

          <!-- Instructions Section -->
          <div class="form-section">
            <div class="form-section-title">تعليمات الاختبار</div>

            <div class="form-group">
              <label class="form-label" for="instructions">تعليمات الاختبار (اختياري)</label>
              <textarea
                id="instructions"
                name="instructions"
                class="form-textarea"
                placeholder="أضف تعليمات خاصة للاختبار توضح للطلاب كيفية الإجابة..."
              >{{ old('instructions') }}</textarea>
            </div>
          </div>

          <!-- Info Alert -->
          <div class="alert alert-info">
            <i class="ri-information-line"></i>
            <div>
              <strong>ملاحظة مهمة:</strong> بعد إنشاء الاختبار، ستتمكن من إضافة الأسئلة والإجابات من صفحة إدارة الاختبارات.
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="btn-group">
            <a href="{{ route('teacher.exams') }}" class="btn btn-secondary">
              <i class="ri-close-line"></i> إلغاء
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
              <i class="ri-check-line"></i> إنشاء الاختبار
            </button>
          </div>
        </form>
      </div>
    </div>
  @include('components.notification-bell')

  <script>
    function toggleDark() {
      const currentTheme = localStorage.getItem('theme') || 'light';
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', newTheme);
      document.body.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateDarkIcon();
    }

    function updateDarkIcon() {
      const theme = localStorage.getItem('theme') || 'light';
      const icon = document.getElementById('darkIcon');
      if (theme === 'dark') {
        icon.className = 'ri-sun-line';
      } else {
        icon.className = 'ri-moon-line';
      }
    }

    function initUserTheme() {
      const theme = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', theme);
      updateDarkIcon();
    }

    function updateFormAction(lessonId) {
      if (!lessonId) {
        document.getElementById('createExamForm').action = '#';
        document.getElementById('createExamForm').disabled = true;
        return;
      }
      var routeTemplate = document.getElementById('createExamForm').dataset.routeTemplate;
      if (routeTemplate) {
        document.getElementById('createExamForm').action = routeTemplate.replace('__LESSON__', lessonId);
      } else {
        document.getElementById('createExamForm').action = window.location.origin + '/teacher/lessons/' + lessonId + '/exams';
      }
      document.getElementById('createExamForm').disabled = false;
    }

    function validateForm() {
      const lessonSelect = document.getElementById('lesson_id');
      if (!lessonSelect.value) {
        alert('يرجى اختيار درس قبل إنشاء الاختبار');
        lessonSelect.focus();
        return false;
      }
      return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
      initUserTheme();
      const darkBtn = document.getElementById('darkBtn');
      if (darkBtn) { darkBtn.addEventListener('click', toggleDark); }
      const examForm = document.getElementById('createExamForm');
      if (examForm) {
        examForm.addEventListener('submit', function(e) {
          if (!validateForm()) { e.preventDefault(); }
        });
      }
      const lessonSelect = document.getElementById('lesson_id');
      const submitBtn = document.getElementById('submitBtn');

      if (lessonSelect && lessonSelect.value) {
        updateFormAction(lessonSelect.value);
        submitBtn.disabled = false;
      } else {
        submitBtn.disabled = true;
      }

      lessonSelect.addEventListener('change', function() {
        updateFormAction(this.value);
        submitBtn.disabled = !this.value;
      });
    });
  </script>
    @include('components.account-theme-foot')
</body>
</html>



