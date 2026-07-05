<!DOCTYPE html>
<html lang="ar" dir="rtl">
@php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>لوحة التحكم - معلم | إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root { --sidebar-w: 300px; --topbar-h: 70px; --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { overflow-x: hidden; }

    body {
      font-family: 'Tajawal', sans-serif;
      position: relative;
      background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 22%),
                  linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%) !important;
      color: var(--text-primary);
      min-height: 100vh;
      transition: background 0.3s, color 0.3s;
      overflow-x: hidden;
    }
    body::before {
      content: '' !important;
      position: fixed !important;
      top: -100px !important;
      left: -100px !important;
      width: 600px !important;
      height: 600px !important;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,214,122,0.28) 0%, rgba(255,214,122,0.12) 35%, transparent 65%) !important;
      filter: blur(80px);
      pointer-events: none !important;
      z-index: 1 !important;
      animation: ambientPulse 8s ease-in-out infinite;
      opacity: 1 !important;
    }
    body::after {
      content: '' !important;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(ellipse at 5% 5%, rgba(255,214,122,0.06) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
    }
    @keyframes ambientPulse {
      0%, 100% { opacity: 0.7; transform: scale(1); }
      50% { opacity: 1; transform: scale(1.08); }
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
      border-radius: 30px;
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

    .sidebar-footer { margin-top: auto; }
    .sidebar-footer form { width: 100%; }
    .logout-btn {
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 18px;
      border-radius: 18px;
      border: 1px solid rgba(255,59,48,0.15);
      background: rgba(255,255,255,0.94);
      color: #FF3B30;
      font-weight: 700;
      transition: var(--transition);
      text-decoration: none;
      cursor: pointer;
    }
    .logout-btn:hover { transform: translateY(-1px); box-shadow: 0 12px 28px rgba(255,59,48,0.12); }

    .main { margin-right: calc(var(--sidebar-w) + 22px); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      height: var(--topbar-h);
      background: transparent;
      backdrop-filter: blur(18px);
      border-bottom: none;
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
      font-size:12px;
      font-weight:800;
      color:var(--text-primary);
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: var(--transition);
    }
    .user-profile-btn:hover .u-name {
      text-shadow: 0 0 8px rgba(196,150,58,0.3);
    }
    .user-profile-btn .u-role { font-size:10px; color:var(--text-muted); font-weight:600; }
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
      opacity: 1;
    }
    .icon-btn:hover::before {
      width: 100%;
      height: 100%;
    }
    .icon-btn:hover {
      color: var(--gold);
      border-color: var(--gold);
      transform: scale(1.08);
      box-shadow: 0 0 16px rgba(196,150,58,0.3);
    }

    .notification-wrapper {
      position: relative;
      display: inline-flex;
      align-items: center;
      z-index: 1200;
      overflow: visible !important;
    }

    .notification-btn {
      position: relative;
      background: rgba(255,255,255,0.06);
      color: var(--text-secondary);
      overflow: visible !important;
      z-index: 1210;
    }

    .notification-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      left: auto;
      min-width: 24px;
      height: 24px;
      padding: 0 6px;
      border-radius: 50%;
      background: #FF3B30;
      color: #fff;
      display: flex !important;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 800;
      line-height: 1;
      pointer-events: none;
      box-shadow: 0 3px 10px rgba(255, 59, 48, 0.5);
      border: 2px solid var(--theme-surface);
      z-index: 1220 !important;
    }

    .notification-dropdown {
      position: absolute;
      top: calc(100% + 12px);
      right: 0;
      width: min(380px, calc(100vw - 24px));
      background: color-mix(in srgb, var(--card-bg, var(--theme-surface)) 98%, transparent);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 18px;
      box-shadow: 0 28px 80px rgba(0,0,0,0.18);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      z-index: 1200;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transform: translateY(-10px);
      transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
      backdrop-filter: blur(18px);
    }

    .notification-dropdown.active {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transform: translateY(0);
    }

    .notification-dropdown .dropdown-header,
    .notification-dropdown .dropdown-footer {
      padding: 14px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .notification-dropdown .dropdown-header h4 {
      margin: 0;
      font-size: 14px;
      font-weight: 800;
      color: var(--text-primary);
    }

    .notification-dropdown .btn-close {
      background: rgba(255,255,255,0.06);
      border: none;
      color: var(--text-primary);
      font-size: 18px;
      cursor: pointer;
      line-height: 1;
      width: 36px;
      height: 36px;
      border-radius: 14px;
      transition: background 0.2s ease;
    }
    .notification-dropdown .btn-close:hover {
      background: rgba(255,255,255,0.12);
    }

    .bell-toast {
      position: fixed;
      bottom: 24px;
      right: 24px;
      width: min(340px, calc(100% - 48px));
      background: color-mix(in srgb, var(--card-bg, var(--theme-surface)) 98%, transparent);
      color: var(--text-primary);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 18px;
      padding: 16px 18px;
      box-shadow: 0 22px 48px rgba(0,0,0,0.32);
      opacity: 0;
      transform: translateY(20px);
      transition: transform 0.35s ease, opacity 0.35s ease;
      z-index: 9999;
      pointer-events: none;
      backdrop-filter: blur(18px);
    }
    .bell-toast.visible {
      opacity: 1;
      transform: translateY(0);
    }
    .bell-toast strong {
      display: block;
      font-size: 14px;
      margin-bottom: 6px;
    }
    .bell-toast p {
      margin: 0;
      font-size: 13px;
      color: var(--text-secondary);
      line-height: 1.4;
    }

    .notification-dropdown .notification-list {
      max-height: 320px;
      overflow-y: auto;
      background: transparent;
      padding: 8px 0;
      scrollbar-width: thin;
      scrollbar-color: rgba(198, 166, 117, 0.9) rgba(255, 255, 255, 0.08);
    }
    .notification-dropdown .notification-list::-webkit-scrollbar { width: 8px; }
    .notification-dropdown .notification-list::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.08); border-radius: 999px; }
    .notification-dropdown .notification-list::-webkit-scrollbar-thumb { background: linear-gradient(180deg, rgba(198, 166, 117, 0.98), rgba(151, 114, 62, 0.95)); border-radius: 999px; }

    .notification-dropdown .notification-item {
      display: flex;
      gap: 12px;
      padding: 12px 16px;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      text-decoration: none;
      color: inherit;
      align-items: flex-start;
      transition: background 0.2s ease;
    }

    .notification-dropdown .notification-item:hover {
      background: rgba(255,255,255,0.04);
    }

    .notification-dropdown .notification-item.unread {
      background: rgba(255,214,122,0.08);
      border-right: 3px solid var(--gold);
    }

    .notification-dropdown .notification-item:last-child {
      border-bottom: none;
    }

    .notification-dropdown .notification-icon {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: rgba(255,214,122,0.12);
      display: grid;
      place-items: center;
      color: #FFD66D;
      font-size: 18px;
      flex-shrink: 0;
    }

    .notification-dropdown .notification-details {
      min-width: 0;
      flex: 1;
    }

    .notification-dropdown .notification-title {
      margin-bottom: 4px;
      font-size: 13px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .notification-dropdown .notification-text {
      font-size: 13px;
      color: var(--text-secondary);
      margin-bottom: 6px;
    }

    .notification-dropdown .notification-time {
      font-size: 11px;
      color: var(--text-muted);
    }

    .notification-dropdown .empty-message {
      padding: 16px;
      text-align: center;
      color: var(--text-secondary);
      font-size: 13px;
    }

    .notification-dropdown .btn-secondary {
      width: 100%;
      text-align: center;
      display: inline-block;
      padding: 10px 16px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 14px;
      color: var(--text-primary);
      font-weight: 700;
      font-size: 13px;
      text-decoration: none;
      transition: background 0.2s ease;
    }
    .notification-dropdown .btn-secondary:hover {
      background: rgba(255,255,255,0.14);
    }

    .topbar-right { display: flex; align-items: center; gap: 12px; }
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

    .content { padding: 0 32px 32px; flex: 1; }

    .page { display: none; animation: fadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    .page.active { display: block; }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .card {
      position: relative;
      background: rgba(255,255,255,0.035);
      border-radius: var(--radius-xl);
      box-shadow: 0 20px 60px rgba(0,0,0,0.35);
      border: 1px solid rgba(255,214,122,0.14);
      overflow: hidden;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s, border-color 0.3s;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }
    .card.visible {
      opacity: 1;
      transform: translateY(0);
    }
    .card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 220px;
      height: 220px;
      background: radial-gradient(circle, rgba(255,214,122,0.15) 0%, transparent 70%);
      border-radius: 50%;
      opacity: 1;
      transition: opacity 0.5s ease, transform 0.5s ease;
      pointer-events: none;
      z-index: 0;
    }
    .card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 0 45px rgba(196,150,58,0.25), 0 24px 70px rgba(0,0,0,0.35);
      border-color: rgba(196,150,58,0.3);
    }
    .card:hover::before {
      opacity: 1;
      transform: scale(1.3);
    }
    .glow-near::before {
      content: '';
      position: absolute;
      top: -26px;
      left: -26px;
      width: 160px;
      height: 160px;
      background: radial-gradient(circle, rgba(255,214,122,0.18), transparent 65%);
      border-radius: 50%;
      filter: blur(24px);
      opacity: 0.6;
      pointer-events: none;
      z-index: 0;
      transition: opacity 0.6s ease, filter 0.6s ease, width 0.6s ease, height 0.6s ease;
    }
    .glow-near::after {
      content: '';
      position: absolute;
      top: 8px;
      left: 8px;
      width: 140px;
      height: 140px;
      background: linear-gradient(135deg, rgba(255,214,122,0.16), transparent 70%);
      filter: blur(18px);
      opacity: 0.35;
      pointer-events: none;
      z-index: 0;
      transition: opacity 0.6s ease, filter 0.6s ease;
    }
    .glow-near:hover::before {
      opacity: 1;
      width: 200px;
      height: 200px;
      filter: blur(30px);
    }
    .glow-near:hover::after {
      opacity: 0.7;
      filter: blur(24px);
    }
    .card > * { position: relative; z-index: 1; }

    .stats-grid .card:nth-child(1) { animation-delay: 0.05s; }
    .stats-grid .card:nth-child(2) { animation-delay: 0.12s; }
    .stats-grid .card:nth-child(3) { animation-delay: 0.19s; }
    .stats-grid .card:nth-child(4) { animation-delay: 0.26s; }
    .grid-container .card:nth-child(1) { animation-delay: 0.08s; }
    .grid-container .card:nth-child(2) { animation-delay: 0.14s; }
    .grid-container .card:nth-child(3) { animation-delay: 0.20s; }
    .grid-container .card:nth-child(4) { animation-delay: 0.26s; }
    .grid-container .card:nth-child(5) { animation-delay: 0.32s; }
    .grid-container .card:nth-child(6) { animation-delay: 0.38s; }

    .section-hdr { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 0 10px; gap: 12px; flex-wrap: wrap; }
    .section-title { font-size: 20px; font-weight: 800; color: var(--text-primary); }
    .section-desc { color: var(--text-secondary); font-size: 14px; line-height: 1.6; max-width: 620px; }
    .dashboard-grid { display: grid; grid-template-columns: 1.35fr 0.9fr; gap: 20px; margin-bottom: 24px; }
    @media (max-width: 1100px) { .dashboard-grid { grid-template-columns: 1fr; } }

    .btn-primary {
      padding: 10px 20px;
      background: var(--gold); color: #fff;
      border: none; border-radius: var(--radius-md);
      font-family: 'Tajawal', sans-serif;
      font-size: 14px; font-weight: 700;
      cursor: pointer; display: flex; align-items: center; gap: 8px;
      transition: var(--transition);
      box-shadow: 0 4px 12px rgba(196,150,58,0.3);
      text-decoration: none;
    }
    .btn-primary:hover { background: var(--gold-dark); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(196,150,58,0.4); }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 24px; }
    .stat-card {
      padding: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      position: relative;
      overflow: hidden;
      transition: var(--transition);
      background: rgba(255,255,255,0.035);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255,214,122,0.12);
      border-radius: var(--radius-xl);
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 180px;
      height: 180px;
      background: radial-gradient(circle, rgba(255,214,122,0.12) 0%, transparent 70%);
      border-radius: 50%;
      opacity: 1;
      transition: opacity 0.4s ease, transform 0.4s ease;
      pointer-events: none;
    }
    .stat-card:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 0 40px rgba(255,214,122,0.18), 0 20px 60px rgba(0,0,0,0.35);
      border-color: rgba(196,150,58,0.3);
    }
    .stat-card:hover::before {
      opacity: 0.5;
      transform: scale(1.4);
    }
    .stat-card-link {
      text-decoration: none;
      color: inherit;
      cursor: pointer;
      display: flex;
    }
    .stat-card-link:hover {
      transform: translateY(-12px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }
    .stat-icon {
      width: 54px;
      height: 54px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 16px;
      position: relative;
      z-index: 1;
      transition: var(--transition);
    }
    .stat-card:hover .stat-icon {
      transform: scale(1.1) rotate(10deg);
    }
    .stat-icon.blue { background: rgba(198,166,117,0.1); color: #C6A675; box-shadow: 0 4px 12px rgba(198,166,117,0.15); }
    .stat-icon.green { background: rgba(52,199,89,0.1); color: #34C759; box-shadow: 0 4px 12px rgba(52,199,89,0.15); }
    .stat-icon.gold-i { background: var(--gold-light); color: var(--gold); box-shadow: 0 4px 12px rgba(196,150,58,0.2); }
    .stat-icon.purple { background: rgba(175,82,222,0.1); color: #AF52DE; box-shadow: 0 4px 12px rgba(175,82,222,0.15); }
    .stat-lbl { font-size: 13px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px; position: relative; z-index: 1; }
    .stat-val {
      font-size: 32px;
      font-weight: 900;
      color: var(--text-primary);
      line-height: 1;
      position: relative;
      z-index: 1;
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: bounce 3s ease-in-out infinite;
    }

    .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .item-card {
      padding: 24px;
      border: 1px solid rgba(255,214,122,0.12);
      position: relative;
      cursor: pointer;
      transition: var(--transition);
      overflow: hidden;
      background: rgba(255,255,255,0.04);
      backdrop-filter: blur(16px);
      border-radius: var(--radius-xl);
      box-shadow: 0 16px 48px rgba(0,0,0,0.26);
    }
    .item-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(255,214,122,0.12) 0%, transparent 70%);
      border-radius: 50%;
      opacity: 1;
      transition: opacity 0.4s ease, transform 0.4s ease;
      pointer-events: none;
      z-index: 0;
    }
    .item-card:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 0 40px rgba(255,214,122,0.18), 0 20px 60px rgba(0,0,0,0.35);
      border-color: rgba(196,150,58,0.25);
    }
    .item-card:hover::before {
      opacity: 1;
      transform: scale(1.3);
    }
    .item-opts { position: absolute; top: 16px; left: 16px; color: var(--text-muted); cursor: pointer; opacity: 0; transition: var(--transition); }
    .item-card:hover .item-opts { opacity: 1; }
    .item-icon-box {
      width: 48px;
      height: 48px;
      border: 2px solid var(--gold-light);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: var(--gold);
      margin-bottom: 16px;
      position: relative;
      z-index: 1;
      transition: var(--transition);
      box-shadow: 0 0 12px rgba(196,150,58,0.1);
    }
    .item-card:hover .item-icon-box {
      border-color: var(--gold);
      transform: rotate(10deg) scale(1.1);
      box-shadow: 0 0 20px rgba(196,150,58,0.25);
    }
    .item-title {
      font-size: 16px;
      font-weight: 800;
      margin-bottom: 6px;
      position: relative;
      z-index: 1;
    }
    .item-sub {
      font-size: 12px;
      color: var(--text-secondary);
      font-weight: 500;
      margin-bottom: 24px;
      position: relative;
      z-index: 1;
    }
    .item-foot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 16px;
      border-top: 1px dashed rgba(0,0,0,0.08);
      font-size: 13px;
      font-weight: 700;
      position: relative;
      z-index: 1;
    }
    .item-stat { color: var(--gold); }
    .item-stat-alt { color: var(--text-secondary); }

    .list-card { padding: 24px; }
    .list-card-hdr { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .list-card-hdr i {
      font-size: 20px;
      color: var(--danger);
      animation: pulse 2s infinite;
    }
    .list-card-hdr.q-hdr i { color: var(--gold); }
    .list-card-ttl {
      font-size: 16px;
      font-weight: 800;
      position: relative;
    }
    .list-card-ttl::after {
      content: '';
      position: absolute;
      bottom: -4px;
      right: 0;
      width: 40px;
      height: 3px;
      background: linear-gradient(90deg, var(--gold), transparent);
      border-radius: 2px;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .stu-warn-list { display: flex; flex-direction: column; gap: 12px; }
    .stu-warn-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 18px;
      border: 1px solid rgba(0,0,0,0.04);
      border-radius: var(--radius-md);
      position: relative;
      overflow: hidden;
      transition: var(--transition);
      cursor: pointer;
    }
    .stu-warn-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 4px;
      height: 100%;
      background: var(--danger);
      opacity: 0;
      transition: var(--transition);
      box-shadow: 2px 0 8px rgba(255,59,48,0.3);
    }
    .stu-warn-item::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, transparent 0%, rgba(255,59,48,0.05) 100%);
      opacity: 0;
      transition: var(--transition);
      z-index: -1;
    }
    .stu-warn-item:hover {
      background: var(--danger-light);
      border-color: var(--danger);
      transform: translateX(-4px);
      box-shadow: 0 4px 12px rgba(255,59,48,0.15);
    }
    .stu-warn-item:hover::before {
      opacity: 1;
      width: 6px;
    }
    .stu-warn-item:hover::after {
      opacity: 1;
    }
    .stu-warn-item-link {
      text-decoration: none;
      color: inherit;
      display: block;
      transition: var(--transition);
    }
    .stu-warn-item-link:hover {
      text-decoration: none;
    }
    .stu-warn-info { display: flex; align-items: center; gap: 12px; }
    .stu-av-small {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--gold), var(--gold-dark));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 800;
      color: #fff;
      box-shadow: 0 4px 12px rgba(196,150,58,0.2);
    }
    .stu-warn-name { font-size: 14px; font-weight: 700; margin-bottom: 3px; }
    .stu-warn-course { font-size: 11px; color: var(--text-muted); }
    .danger-badge {
      padding: 6px 12px;
      border-radius: 20px;
      background: var(--danger-light);
      color: var(--danger);
      font-size: 11px;
      font-weight: 800;
      animation: slideInRight 0.4s ease;
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes shimmer {
      0% { background-position: -1000px 0; }
      100% { background-position: 1000px 0; }
    }

    @keyframes glow {
      0%, 100% { box-shadow: 0 0 8px rgba(196,150,58,0.15); }
      50% { box-shadow: 0 0 20px rgba(196,150,58,0.3); }
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }

    @keyframes ripple {
      0% { transform: scale(0); opacity: 0.6; }
      100% { transform: scale(4); opacity: 0; }
    }

    .q-list { display: flex; flex-direction: column; gap: 12px; }
    .q-item {
      padding: 16px;
      border: 1px solid rgba(0,0,0,0.04);
      border-radius: var(--radius-md);
      position: relative;
      transition: var(--transition);
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .q-item::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 0;
      height: 100%;
      background: var(--gold-light);
      border-radius: 0 var(--radius-md) var(--radius-md) 0;
      transition: var(--transition);
      z-index: -1;
    }
    .q-item:hover::before {
      width: 100%;
    }
    .q-item:hover {
      border-color: var(--gold);
      transform: translateX(-4px);
    }
    .q-meta { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 11px; color: var(--text-muted); }
    .q-author {
      font-weight: 700;
      color: var(--text-secondary);
      background: var(--gold-light);
      padding: 2px 8px;
      border-radius: 10px;
    }
    .q-text {
      font-size: 14px;
      font-weight: 600;
      line-height: 1.5;
      color: var(--text-primary);
      position: relative;
      z-index: 1;
    }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-icon { font-size: 64px; margin-bottom: 16px; opacity: 0.5; }
    .empty-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .empty-text { color: var(--text-secondary); margin-bottom: 20px; }

    @media (max-width: 768px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .grid-container { grid-template-columns: 1fr; }
    }

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
  
    /* Explicit light-mode fix for teacher dashboard shell */
    html[data-theme="light"] body,
    body[data-theme="light"] {
      background: radial-gradient(circle at top left, rgba(255,214,122,0.12), transparent 22%), linear-gradient(180deg, #F4F6F8 0%, #FAFBFD 40%, #F4F6F8 100%) !important;
      color: #222B3D !important;
    }
    html[data-theme="light"] body::before {
      background: radial-gradient(circle, rgba(198, 166, 117, 0.18) 0%, rgba(198, 166, 117, 0.06) 35%, transparent 65%) !important;
    }
    html[data-theme="light"] .sidebar,
    body[data-theme="light"] .sidebar {
      background: #FFFFFF !important;
      border-left: 1px solid #DFE5EC !important;
      box-shadow: 0 8px 26px rgba(34, 43, 61, 0.08) !important;
    }
    html[data-theme="light"] .sidebar-logo::after,
    body[data-theme="light"] .sidebar-logo::after {
      background: linear-gradient(90deg, transparent, #C6A675, transparent) !important;
    }
    html[data-theme="light"] .nav-btn,
    body[data-theme="light"] .nav-btn {
      background: #FFFFFF !important;
      border: 1px solid #DFE5EC !important;
      color: #5E6675 !important;
    }
    html[data-theme="light"] .nav-btn i,
    body[data-theme="light"] .nav-btn i {
      color: #C6A675 !important;
    }
    html[data-theme="light"] .nav-btn.active,
    body[data-theme="light"] .nav-btn.active {
      background: rgba(198, 166, 117, 0.16) !important;
      border-color: rgba(151, 114, 44, 0.36) !important;
      color: #222B3D !important;
      box-shadow: none !important;
    }
    html[data-theme="light"] .topbar,
    body[data-theme="light"] .topbar {
      background: linear-gradient(90deg, #FFFFFF 0%, #F8FAFC 100%) !important;
      border-bottom: 1px solid #DFE5EC !important;
      box-shadow: 0 2px 10px rgba(34, 43, 61, 0.06) !important;
    }

    html[data-theme="light"] .notification-dropdown,
    body[data-theme="light"] .notification-dropdown {
      background: #FFFFFF !important;
      border: 1px solid #DFE5EC !important;
      box-shadow: 0 16px 34px rgba(34, 43, 61, 0.12) !important;
    }
    html[data-theme="light"] .notification-dropdown .notification-list,
    body[data-theme="light"] .notification-dropdown .notification-list {
      background: #FFFFFF !important;
    }
    html[data-theme="light"] .notification-dropdown .notification-item,
    body[data-theme="light"] .notification-dropdown .notification-item {
      border-bottom: 1px solid #E7ECF2 !important;
    }
    html[data-theme="light"] .notification-dropdown .notification-item.unread,
    body[data-theme="light"] .notification-dropdown .notification-item.unread {
      background: rgba(198, 166, 117, 0.08) !important;
      border-right: 3px solid #C6A675 !important;
    }
    html[data-theme="light"] .notification-dropdown .notification-title,
    html[data-theme="light"] .notification-dropdown .dropdown-header h4,
    html[data-theme="light"] .notification-dropdown .btn-close,
    body[data-theme="light"] .notification-dropdown .notification-title,
    body[data-theme="light"] .notification-dropdown .dropdown-header h4,
    body[data-theme="light"] .notification-dropdown .btn-close {
      color: #222B3D !important;
    }
    html[data-theme="light"] .notification-dropdown .notification-text,
    body[data-theme="light"] .notification-dropdown .notification-text {
      color: #5E6675 !important;
    }
    html[data-theme="light"] .notification-dropdown .notification-time,
    body[data-theme="light"] .notification-dropdown .notification-time {
      color: #7D8797 !important;
    }
    html[data-theme="light"] .notification-dropdown .btn-close,
    body[data-theme="light"] .notification-dropdown .btn-close {
      background: #E7ECF2 !important;
    }
    html[data-theme="light"] .notification-dropdown .btn-close:hover,
    body[data-theme="light"] .notification-dropdown .btn-close:hover {
      background: #D6DDE6 !important;
    }
    html[data-theme="light"] .notification-dropdown .btn-secondary,
    body[data-theme="light"] .notification-dropdown .btn-secondary {
      background: #F0F2F5 !important;
      border-color: #DFE5EC !important;
      color: #222B3D !important;
    }
    html[data-theme="light"] .notification-dropdown .btn-secondary:hover,
    body[data-theme="light"] .notification-dropdown .btn-secondary:hover {
      background: #E7ECF2 !important;
    }
    html[data-theme="light"] .bell-toast,
    body[data-theme="light"] .bell-toast {
      background: #FFFFFF !important;
      border-color: #DFE5EC !important;
      color: #222B3D !important;
    }
    html[data-theme="light"] .bell-toast p,
    body[data-theme="light"] .bell-toast p {
      color: #5E6675 !important;
    }

    html.teacher-account[data-theme="light"] .card,
    html.teacher-account[data-theme="light"] .stat-card,
    html.teacher-account[data-theme="light"] .item-card {
      background: rgba(255,255,255,0.55) !important;
      border: 1px solid rgba(255,214,122,0.15) !important;
      box-shadow: 0 12px 40px rgba(0,0,0,0.06) !important;
      backdrop-filter: blur(16px) !important;
      -webkit-backdrop-filter: blur(16px) !important;
    }
    html.teacher-account[data-theme="light"] .card:hover,
    html.teacher-account[data-theme="light"] .stat-card:hover,
    html.teacher-account[data-theme="light"] .item-card:hover {
      transform: translateY(-8px) scale(1.03) !important;
      box-shadow: 0 0 40px rgba(196,150,58,0.18), 0 20px 60px rgba(0,0,0,0.08) !important;
      border-color: rgba(196,150,58,0.3) !important;
    }
    html.teacher-account[data-theme="light"] .card::before,
    html.teacher-account[data-theme="light"] .stat-card::before,
    html.teacher-account[data-theme="light"] .item-card::before {
      background: radial-gradient(circle, rgba(255,214,122,0.18) 0%, transparent 70%) !important;
      opacity: 1 !important;
      transition: opacity 0.4s ease, transform 0.4s ease !important;
    }
    html.teacher-account[data-theme="light"] .card:hover::before,
    html.teacher-account[data-theme="light"] .stat-card:hover::before,
    html.teacher-account[data-theme="light"] .item-card:hover::before {
      opacity: 1 !important;
      transform: scale(1.3) !important;
    }
    html.teacher-account[data-theme="light"] body::before {
      position: fixed !important;
      top: -80px !important;
      left: -80px !important;
      width: 500px !important;
      height: 500px !important;
      border-radius: 50% !important;
      background: radial-gradient(circle, rgba(255,214,122,0.22) 0%, rgba(255,214,122,0.08) 40%, transparent 70%) !important;
      filter: blur(60px) !important;
      opacity: 1 !important;
      animation: ambientPulse 8s ease-in-out infinite !important;
    }
    html.teacher-account[data-theme="light"] .sidebar {
      background: rgba(255,255,255,0.7) !important;
      backdrop-filter: blur(24px) !important;
      border-color: rgba(255,214,122,0.12) !important;
    }

    /* ═══════════════════════════════════════════════════════════════
       RESPONSIVE DESIGN التصميم الاستجابي — Teacher Dashboard
    ═══════════════════════════════════════════════════════════════ */

    /* Hamburger menu button */
    .hamburger-btn {
      display: none;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 12px;
      background: rgba(255,255,255,0.06);
      color: var(--text-primary);
      font-size: 20px;
      cursor: pointer;
      transition: var(--transition);
      flex-shrink: 0;
    }
    .hamburger-btn:hover {
      background: var(--gold-light);
      border-color: var(--gold);
    }

    /* Sidebar backdrop */
    .sidebar-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.55);
      z-index: 9998;
    }
    .sidebar-backdrop.active { display: block; }

    /* Hamburger shows at ≤1280px */
    @media (max-width: 1280px) {
      .hamburger-btn { display: flex; }
      .topbar { padding: 0 20px !important; }
    }

    /* Sidebar fully hidden at ≤1024px — slides in on hamburger click */
    @media (max-width: 1024px) {
      .sidebar {
        position: fixed !important;
        transform: translateX(110%) !important;
        visibility: hidden !important;
        pointer-events: none !important;
        width: var(--sidebar-w) !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 100vh !important;
        z-index: 9999 !important;
        border-radius: 0 !important;
        padding: 28px 18px !important;
        overflow-y: auto !important;
        transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), visibility 0.3s !important;
      }
      .sidebar.sidebar-open {
        transform: translateX(0) !important;
        visibility: visible !important;
        pointer-events: auto !important;
      }
      .sidebar.sidebar-open .logo-name,
      .sidebar.sidebar-open .logo-sub,
      .sidebar.sidebar-open .nav-btn span {
        display: block !important;
      }
      .sidebar.sidebar-open .nav-btn {
        justify-content: flex-start !important;
        padding: 12px 16px !important;
        gap: 12px !important;
      }
      .main { margin-right: 0 !important; }
      .section-hdr { flex-direction: column !important; gap: 12px !important; }
    }

    /* Mobile — topbar simplified, search hidden */
    @media (max-width: 768px) {
      .topbar {
        padding: 0 14px !important;
        height: 60px !important;
        gap: 8px !important;
      }
      .topbar-left { gap: 8px !important; }
      .topbar-right { display: none !important; }
      .search-wrap { display: none !important; }
      .user-profile-btn {
        min-width: unset !important;
        padding: 8px 10px !important;
      }
      .user-profile-btn .u-info { display: none !important; }
      .content { padding: 16px !important; }
      .grid-container { grid-template-columns: 1fr 1fr !important; gap: 12px !important; }
    }

    @media (max-width: 640px) {
      .grid-container { grid-template-columns: 1fr !important; }
      .content { padding: 12px !important; }
      .section-hdr { padding: 0 !important; }
    }

    @media (max-width: 480px) {
      .hamburger-btn { width: 36px !important; height: 36px !important; font-size: 18px !important; }
      .icon-btn { width: 36px !important; height: 36px !important; font-size: 16px !important; }
      .topbar { padding: 0 10px !important; height: 56px !important; }
      .grid-container { grid-template-columns: 1fr !important; gap: 10px !important; }
      .stats-grid { grid-template-columns: 1fr !important; }
      .stat-card { padding: 16px !important; }
      .content { padding: 10px !important; }
    }
  </style>
</head>
<body>
@include('components.alerts')
<div class="sidebar-backdrop" id="teacherSidebarBackdrop"></div>
<div class="app">
  <aside class="sidebar" id="teacherSidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">
        @if(file_exists(public_path('images/logo/logo.png')))
          <img src="{{ asset('images/logo/logo.png?v=' . time()) }}" alt="إجلال" loading="lazy" />
        @else
          <i class="ri-book-read-fill"></i>
        @endif
      </div>
      <div class="logo-name">إجلال</div>
      <div class="logo-sub">المنصة التعليمية</div>
    </div>

    <nav class="sidebar-nav">
      <a href="{{ route('teacher.dashboard') }}" class="nav-btn active" id="nb-home">
        <i class="ri-home-4-line"></i><span>الرئيسية</span>
      </a>
      <a href="{{ route('teacher.courses') }}" class="nav-btn" id="nb-courses">
        <i class="ri-book-2-line"></i><span>المسارات</span>
      </a>
      <a href="{{ route('teacher.enrollment.requests') }}" class="nav-btn" id="nb-enrollment">
        <i class="ri-user-add-line"></i><span>طلبات الالتحاق</span>
      </a>
      <a href="{{ route('teacher.exams') }}" class="nav-btn" id="nb-exams">
        <i class="ri-file-list-line"></i><span>الاختبارات</span>
      </a>
      <a href="{{ route('teacher.analytics') }}" class="nav-btn" id="nb-analytics">
        <i class="ri-bar-chart-2-line"></i><span>نسبة الإنجاز</span>
      </a>
      <a href="{{ route('teacher.students') }}" class="nav-btn" id="nb-students">
        <i class="ri-team-line"></i><span>طلابي</span>
      </a>
      <a href="{{ route('teacher.certificates.students') }}" class="nav-btn" id="nb-certificates">
        <i class="ri-award-line"></i><span>الشهادات</span>
      </a>
      <a href="{{ route('teacher.questions.manage') }}" class="nav-btn" id="nb-inquiries">
        <i class="ri-chat-3-line"></i><span>الأسئلة والاستفسارات</span>
      </a>
      <a href="{{ route('teacher.messaging') }}" class="nav-btn" id="nb-messaging">
        <i class="ri-message-2-line"></i><span>المراسلة</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <form action="{{ route('teacher.logout') }}" method="POST" style="width: 100%;">
        @csrf
        <button type="submit" class="nav-btn logout" style="width: 100%; margin: 0; border: none;">
          <i class="ri-logout-box-r-line"></i><span>خروج</span>
        </button>
      </form>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <!-- Hamburger menu toggle for tablet/mobile -->
        <button class="hamburger-btn" id="teacherHamburger" title="فتح القائمة">
          <i class="ri-menu-line"></i>
        </button>
        <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
          <i class="ri-moon-line" id="darkIcon"></i>
        </button>
        <div class="notification-wrapper">
          <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
            <i class="ri-notification-3-line"></i>
            <span class="notification-badge" id="notificationBadge" style="display: {{ $unreadCount ? 'flex' : 'none' }}">{{ $unreadCount }}</span>
          </button>
          <div class="notification-dropdown" id="notificationDropdown">
            <div class="dropdown-header">
              <h4>الإشعارات</h4>
              <button class="btn-close" type="button" aria-label="إغلاق">×</button>
            </div>
            <div class="notification-list">
              <p class="empty-message">جارِ تحميل الإشعارات...</p>
            </div>
            <div class="dropdown-footer">
              <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm w-100">عرض الكل</a>
            </div>
          </div>
        </div>
        <a href="{{ route('profile.show') }}" class="user-profile-btn" title="عرض الملف الشخصي">
          <div class="u-info">
            <div class="u-name">{{ Auth::user()->name }}</div>
            <div class="u-role">معلم</div>
          </div>
          <div class="u-av">{{ mb_substr(Auth::user()->name, 0, 1) }}</div>
        </a>
      </div>
      <div class="topbar-right">
        <div class="search-wrap">
          <input type="text" placeholder="بحث...">
          <i class="ri-search-line search-icon"></i>
        </div>
      </div>
    </header>

    <div class="content">
      <div class="page active" id="page-home">
        <div class="section-hdr">
          <div>
            <div class="section-title">لوحة تحكم المعلم الرئيسية</div>
            <div class="section-desc">هذه الصفحة هي مركزك الرئيسي للوصول إلى جميع أجزاء المنصة ومتابعة الدورات والطلاب والطلبات والأسئلة والإحصاءات اليومية.</div>
          </div>
          <a href="{{ route('teacher.courses') }}" class="btn-primary"><i class="ri-dashboard-line"></i> الذهاب إلى لوحة المسارات</a>
        </div>

        <div class="stats-grid">
          <a href="{{ route('teacher.courses') }}" class="card stat-card stat-card-link">
            <div class="stat-icon gold-i"><i class="ri-book-open-line"></i></div>
            <div class="stat-lbl">المسارات النشطة</div>
            <div class="stat-val">{{ $myCourses->count() }}</div>
          </a>
          <a href="{{ route('teacher.students') }}" class="card stat-card stat-card-link">
            <div class="stat-icon green"><i class="ri-team-line"></i></div>
            <div class="stat-lbl">الطلاب المسجلون</div>
            <div class="stat-val">{{ $totalStudents }}</div>
          </a>
          <a href="{{ route('teacher.enrollment.requests') }}" class="card stat-card stat-card-link glow-near">
            <div class="stat-icon blue"><i class="ri-user-add-line"></i></div>
            <div class="stat-lbl">طلبات الالتحاق المعلقة</div>
            <div class="stat-val">{{ $pendingEnrollments }}</div>
          </a>
          <a href="{{ route('teacher.questions.manage') }}" class="card stat-card stat-card-link glow-near">
            <div class="stat-icon purple"><i class="ri-chat-3-line"></i></div>
            <div class="stat-lbl">أسئلة واستفسارات مفتوحة</div>
            <div class="stat-val">{{ $pendingQuestions + $pendingInquiries }}</div>
          </a>
        </div>

        <div class="section-hdr">
          <div class="section-title">الوصول السريع</div>
        </div>
        <div class="grid-container">
          <a href="{{ route('teacher.courses') }}" class="card item-card" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-book-2-line"></i></div>
            <div class="item-title">إدارة المسارات</div>
            <div class="item-sub">أنشئ ونسّق مساراتك التعليمية بسرعة.</div>
            <div class="item-foot"><span class="item-stat">عرض</span><span class="item-stat-alt">جميع المسارات</span></div>
          </a>
          <a href="{{ route('teacher.enrollment.requests') }}" class="card item-card glow-near" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-user-add-line"></i></div>
            <div class="item-title">طلبات الالتحاق</div>
            <div class="item-sub">راجع طلبات الانضمام المعلقة بسرعة.</div>
            <div class="item-foot"><span class="item-stat">{{ $pendingEnrollments }}</span><span class="item-stat-alt">طلبات قيد الانتظار</span></div>
          </a>
          <a href="{{ route('teacher.exams') }}" class="card item-card" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-file-list-line"></i></div>
            <div class="item-title">إدارة الاختبارات</div>
            <div class="item-sub">تحكم في الاختبارات والنتائج في مكان واحد.</div>
            <div class="item-foot"><span class="item-stat">{{ $totalExams }}</span><span class="item-stat-alt">اختبارات موجودة</span></div>
          </a>
          <a href="{{ route('teacher.analytics') }}" class="card item-card" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-bar-chart-box-line"></i></div>
            <div class="item-title">لوحة الإحصاءات</div>
            <div class="item-sub">تابع أداء الطلاب ونسبة الإنجاز.</div>
            <div class="item-foot"><span class="item-stat">{{ $totalCourses > 0 ? round(($activeStudents / $totalStudents) * 100) : 0 }}%</span><span class="item-stat-alt">تفاعل الطلاب</span></div>
          </a>
          <a href="{{ route('teacher.messaging') }}" class="card item-card glow-near" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-message-2-line"></i></div>
            <div class="item-title">المراسلة</div>
            <div class="item-sub">تابع الدردشة مع الطلاب مباشرة.</div>
            <div class="item-foot"><span class="item-stat">سريع</span><span class="item-stat-alt">الوصول للمحادثات</span></div>
          </a>
          <a href="{{ route('teacher.messaging') }}?section=contacts" class="card item-card glow-near" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-contacts-book-2-line"></i></div>
            <div class="item-title">جهات الاتصال</div>
            <div class="item-sub">اعرض قائمة طلابك ومعلومات الاتصال بهم.</div>
            <div class="item-foot"><span class="item-stat">دفتر</span><span class="item-stat-alt">جهات الاتصال</span></div>
          </a>
          <a href="{{ route('teacher.questions.manage') }}" class="card item-card glow-near" style="text-decoration:none;color:inherit;">
            <div class="item-icon-box"><i class="ri-question-answer-line"></i></div>
            <div class="item-title">الأسئلة والاستفسارات</div>
            <div class="item-sub">اطلع على كل الأسئلة المعلقة وأجب عليها.</div>
            <div class="item-foot"><span class="item-stat">{{ $pendingQuestions + $pendingInquiries }}</span><span class="item-stat-alt">منتظر الرد</span></div>
          </a>
        </div>

        <div class="dashboard-grid">
          <div class="card list-card">
            <div class="list-card-hdr q-hdr">
              <i class="ri-time-line"></i>
              <div class="list-card-ttl">المهام العاجلة</div>
            </div>
            <div class="q-list">
              @foreach($pendingEnrollmentsList as $enrollment)
                <a class="q-item" href="{{ route('teacher.enrollment.requests') }}">
                  <div class="q-meta"><span>طلب انضمام جديد</span><span>{{ $enrollment->created_at->format('d/m/Y') }}</span></div>
                  <div class="q-text">{{ $enrollment->student->name }} طلب الانضمام إلى {{ $enrollment->course->name }}</div>
                </a>
              @endforeach
              @foreach($recentQuestions as $question)
                <a class="q-item" href="{{ route('teacher.questions.manage') }}">
                  <div class="q-meta"><span>سؤال من {{ $question->student->name }}</span><span>{{ $question->created_at->format('d/m/Y') }}</span></div>
                  <div class="q-text">{{ Str::limit($question->question_text, 90) }}</div>
                </a>
              @endforeach
              @if($pendingEnrollmentsList->isEmpty() && $recentQuestions->isEmpty())
                <div class="empty-state">
                  <div class="empty-icon">✨</div>
                  <div class="empty-title">لا توجد مهام عاجلة</div>
                  <div class="empty-text">كل شيء محدث، تابع الأداء وابدأ بتنظيم الدروس.</div>
                </div>
              @endif
            </div>
          </div>

          <div class="card list-card">
            <div class="list-card-hdr">
              <i class="ri-flashlight-line"></i>
              <div class="list-card-ttl">أهم الطلاب النشطين</div>
            </div>
            <div class="stu-warn-list">
              @forelse($activeStudentsList as $progress)
                <div class="stu-warn-item">
                  <div class="stu-warn-info">
                    <div class="stu-av-small">{{ strtoupper(mb_substr($progress->user->name, 0, 1)) }}</div>
                    <div>
                      <div class="stu-warn-name">{{ $progress->user->name }}</div>
                      <div class="stu-warn-course">مسار: {{ $progress->lesson->course->name ?? 'غير متوفر' }}</div>
                    </div>
                  </div>
                  <div class="danger-badge">{{ intval($progress->progress_percentage) }}%</div>
                </div>
              @empty
                <div class="empty-state">
                  <div class="empty-icon">📚</div>
                  <div class="empty-title">لا يوجد نشاط كافٍ</div>
                  <div class="empty-text">سيظهر هنا الطلاب الأكثر تقدمًا عندما يبدأون المراحل.</div>
                </div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="section-hdr">
          <div class="section-title">آخر الاستفسارات والدورات</div>
        </div>
        <div class="grid-container">
          @foreach($recentInquiries as $inquiry)
            <a href="{{ route('teacher.questions.manage') }}" class="card item-card" style="text-decoration:none;color:inherit;">
              <div class="item-icon-box"><i class="ri-chat-3-line"></i></div>
              <div class="item-title">استفسار جديد من {{ $inquiry->student->name }}</div>
              <div class="item-sub">{{ Str::limit($inquiry->question_text, 80) }}</div>
              <div class="item-foot"><span class="item-stat">{{ $inquiry->course->name ?? 'عام' }}</span><span class="item-stat-alt">{{ $inquiry->created_at->format('d/m/Y') }}</span></div>
            </a>
          @endforeach
          @foreach($recentCourses as $course)
            <a href="{{ route('teacher.show', $course->id) }}" class="card item-card" style="text-decoration:none;color:inherit;">
              <div class="item-icon-box"><i class="ri-book-open-line"></i></div>
              <div class="item-title">{{ $course->name }}</div>
              <div class="item-sub">{{ Str::limit($course->description ?? 'وصف المسار غير متوفر', 70) }}</div>
              <div class="item-foot"><span class="item-stat">{{ $course->lessons()->count() }} درس</span><span class="item-stat-alt">مسار نشط</span></div>
            </a>
          @endforeach
        </div>
      </div>

      <div class="page" id="page-courses">
        <div class="section-hdr">
          <div class="section-title">المسارات التعليمية</div>
          <a href="{{ route('teacher.create') }}" class="btn-primary" style="text-decoration: none;">
            <i class="ri-add-line"></i> مسار جديد
          </a>
        </div>
        @if(count($myCourses) > 0)
          <div class="grid-container">
            @foreach($myCourses as $course)
              <a href="{{ route('teacher.show', $course->id) }}" class="card item-card" style="text-decoration: none; color: inherit; display: block;">
                <i class="ri-more-2-fill item-opts"></i>
                <div class="item-icon-box"><i class="ri-book-open-line"></i></div>
                <div class="item-title">{{ $course->name }}</div>
                <div class="item-sub">{{ Str::limit($course->description ?? '', 50) }}</div>
                <div class="item-foot">
                  <span class="item-stat-alt">{{ count($course->lessons ?? []) }} درس</span>
                  <span class="item-stat">{{ count($course->students ?? []) }} طالب</span>
                </div>
              </a>
            @endforeach
          </div>
        @else
          <div class="card empty-state">
            <div class="empty-icon">📘</div>
            <div class="empty-title">لا توجد مسارات</div>
            <div class="empty-text">ابدأ الآن وأنشئ مسارك التعليمي الأول</div>
          </div>
        @endif
      </div>

      <div class="page" id="page-students">
        <div class="section-hdr">
          <div class="section-title">الطلاب والتقدم</div>
        </div>
        <div class="card empty-state">
          <div class="empty-icon">👥</div>
          <div class="empty-title">لا توجد بيانات طلاب</div>
          <div class="empty-text">سيظهر الطلاب هنا عندما يسجلون في مساراتك</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const pages = ['home', 'courses', 'students'];

  function gotoPage(name) {
    pages.forEach(p => {
      let el = document.getElementById('page-' + p);
      let btn = document.getElementById('nb-' + p);
      if (el) el.classList.remove('active');
      if (btn) btn.classList.remove('active');
    });
    let el = document.getElementById('page-' + name);
    let btn = document.getElementById('nb-' + name);
    if (el) el.classList.add('active');
    if (btn) btn.classList.add('active');
  }

  let dark = false;
  function toggleDark() {
    dark = !dark;
    const theme = dark ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    document.body.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    const icon = document.getElementById('darkIcon');
    if (icon) {
      icon.className = dark ? 'ri-sun-line' : 'ri-moon-line';
    }
  }

  // Load saved theme on page load
  (function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    dark = savedTheme === 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    const icon = document.getElementById('darkIcon');
    if (icon) {
      icon.className = dark ? 'ri-sun-line' : 'ri-moon-line';
    }
  })();

  (function() {
    const notificationFetchRoute = '{{ route('notifications.fetch') }}';
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const closeBtn = document.querySelector('#notificationDropdown .btn-close');
    const list = document.querySelector('#notificationDropdown .notification-list');
    let notificationsLoaded = false;
    let latestNotificationId = 0;

    function escapeHtml(value) {
      const div = document.createElement('div');
      div.textContent = value;
      return div.innerHTML;
    }

    const iconMap = {
      enrollment: 'ri-user-add-line',
      inquiry: 'ri-question-line',
      message: 'ri-message-2-line',
      chat: 'ri-message-2-line',
      question: 'ri-chat-3-line',
      answer: 'ri-chat-3-line',
      support: 'ri-headset-line',
      announcement: 'ri-notification-3-line',
      alert: 'ri-alert-line',
      system: 'ri-notification-3-line',
      course: 'ri-book-open-line',
      lesson: 'ri-book-2-line',
      exam: 'ri-survey-line',
      achievement: 'ri-medal-line',
      attendance: 'ri-checkbox-circle-line',
      payment: 'ri-wallet-line',
      general: 'ri-notification-3-line'
    };

    function getNotificationIcon(item) {
      const icon = String(item.icon || '').trim();
      if (icon && icon.startsWith('ri-')) {
        return icon;
      }
      return iconMap[item.category] || iconMap[item.type] || iconMap.general;
    }

    function renderNotificationItems(items) {
      if (!list) return;
      if (!items.length) {
        list.innerHTML = '<p class="empty-message">لا توجد إشعارات جديدة</p>';
        return;
      }

      list.innerHTML = items.map(item => {
        return `
          <a href="${item.url}" class="notification-item ${item.read_at ? '' : 'unread'}">
            <div class="notification-icon"><i class="${getNotificationIcon(item)}"></i></div>
            <div class="notification-details">
              <div class="notification-title">${escapeHtml(item.title || 'إشعار جديد')}</div>
              <div class="notification-text">${escapeHtml(item.message || '')}</div>
              <div class="notification-time">${escapeHtml(item.created_at || '')}</div>
            </div>
          </a>
        `;
      }).join('');
    }

    function showBellToast(notification) {
      const toast = document.createElement('div');
      toast.className = 'bell-toast';
      toast.innerHTML = '<strong>' + escapeHtml(notification.title) + '</strong><p>' + escapeHtml(notification.message) + '</p>';
      document.body.appendChild(toast);
      setTimeout(function() { toast.classList.add('visible'); }, 50);
      setTimeout(function() {
        toast.classList.remove('visible');
        setTimeout(function() { toast.remove(); }, 400);
      }, 4200);
    }

    function refreshNotifications() {
      if (!notificationBtn || !notificationDropdown) return;

      fetch(notificationFetchRoute, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      })
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            if (list) list.innerHTML = '<p class="empty-message">تعذر تحميل الإشعارات</p>';
            return;
          }

          if (notificationBadge) {
            notificationBadge.textContent = data.unread_count || '';
            notificationBadge.style.display = data.unread_count ? 'flex' : 'none';
          }

          renderNotificationItems(data.notifications || []);
          notificationsLoaded = true;

          if (data.latest_id && data.latest_id > latestNotificationId) {
            const newItem = (data.notifications || []).find(function(item) { return item.id === data.latest_id; });
            if (newItem) {
              showBellToast(newItem);
            }
            latestNotificationId = data.latest_id;
          }
        })
        .catch(() => {
          if (list) list.innerHTML = '<p class="empty-message">فشل تحميل الإشعارات</p>';
        });
    }

    notificationBtn?.addEventListener('click', function(event) {
      event.stopPropagation();
      notificationDropdown?.classList.toggle('active');
      if (!notificationsLoaded) {
        refreshNotifications();
      }
    });

    closeBtn?.addEventListener('click', function(event) {
      event.stopPropagation();
      notificationDropdown?.classList.remove('active');
    });

    document.addEventListener('click', function(event) {
      if (event.target.closest('.notification-item')) {
        notificationDropdown?.classList.remove('active');
      }

      if (!event.target.closest('.notification-wrapper')) {
        notificationDropdown?.classList.remove('active');
      }
    });

    // Initial refresh when page loads
    refreshNotifications();

    // Refresh every 15 seconds
    setInterval(refreshNotifications, 15000);

    // Refresh when page becomes visible (user returns from another tab/page)
    document.addEventListener('visibilitychange', function() {
      if (document.hidden === false) {
        refreshNotifications();
      }
    });

    // Refresh when page focus changes
    window.addEventListener('focus', function() {
      refreshNotifications();
    });

  })();

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('darkBtn').addEventListener('click', toggleDark);
  });

  document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach(function(card, i) {
      setTimeout(function() {
        card.classList.add('visible');
      }, 80 + i * 60);
    });
  });

  // ═══ Hamburger Sidebar Toggle ═══
  document.addEventListener('DOMContentLoaded', function() {
    const teacherSidebar    = document.getElementById('teacherSidebar');
    const teacherHamburger  = document.getElementById('teacherHamburger');
    const teacherBackdrop   = document.getElementById('teacherSidebarBackdrop');

    function openTeacherSidebar() {
      if (teacherSidebar)   teacherSidebar.classList.add('sidebar-open');
      if (teacherBackdrop)  teacherBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeTeacherSidebar() {
      if (teacherSidebar)   teacherSidebar.classList.remove('sidebar-open');
      if (teacherBackdrop)  teacherBackdrop.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (teacherHamburger) {
      teacherHamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        if (teacherSidebar && teacherSidebar.classList.contains('sidebar-open')) {
          closeTeacherSidebar();
        } else {
          openTeacherSidebar();
        }
      });
    }

    if (teacherBackdrop) {
      teacherBackdrop.addEventListener('click', closeTeacherSidebar);
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeTeacherSidebar();
    });

    // Close sidebar when a nav link is clicked on mobile
    if (teacherSidebar) {
      teacherSidebar.querySelectorAll('.nav-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          if (window.innerWidth <= 1024) closeTeacherSidebar();
        });
      });
    }
  });
</script>
    @include('components.account-theme-foot')
</body>



