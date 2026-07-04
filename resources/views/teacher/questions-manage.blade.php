<!DOCTYPE html>
<html lang="ar" dir="rtl">
@php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأسئلة والاستفسارات - لوحة المعلم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Load Theme ASAP -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <style>
        :root { --sidebar-w: 300px; --topbar-h: 70px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        .hamburger-btn { display: none !important; }
        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(255,214,122,0.20), transparent 20%),
                        radial-gradient(circle at bottom right, rgba(255,214,122,0.08), transparent 18%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 45%, var(--theme-page-bg) 100%);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        .app { display: flex; min-height: 100vh; position: relative; }
        .app::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 24px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,214,122,0.18);
            filter: blur(72px);
            pointer-events: none;
            z-index: 0;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg, var(--theme-surface));
            position: fixed;
            right: 18px; top: 24px;
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
            box-shadow: 0 0 16px rgba(196,150,58,0.18);
            animation: float 3s ease-in-out infinite;
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
        .logo-name { font-size: 17px; font-weight: 800; color: var(--gold); position: relative; z-index: 1; }
        .logo-sub { font-size: 10px; font-weight: 600; color: var(--text-muted); margin-top: 4px; position: relative; z-index: 1; letter-spacing: 0.02em; }

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
            cursor: pointer;
            position: relative;
            overflow: hidden;
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

        /* MAIN */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-right: calc(var(--sidebar-w) + 18px);
        }

        /* TOPBAR */
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
            border: 2px solid var(--sidebar-bg, var(--theme-surface));
            z-index: 1220 !important;
        }
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: min(380px, calc(100vw - 24px));
            background: var(--sidebar-bg, var(--theme-surface));
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            box-shadow: 0 28px 80px rgba(0,0,0,0.18);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1100;
            backdrop-filter: blur(18px);
        }
        .notification-dropdown.active {
            display: flex;
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
            padding: 14px 16px;
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
            color: var(--gold);
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
        .bell-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: min(340px, calc(100% - 48px));
            background: var(--sidebar-bg, var(--theme-surface));
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
            transform: scale(1.02);
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

        .content {
            padding: 0 32px 32px;
            min-height: calc(100vh - var(--topbar-h) - 20px);
            overflow: auto;
            position: relative;
        }

        .page-header {
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            max-width: 720px;
            line-height: 1.6;
        }

        .inquiries-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .sidebar-footer {
            padding: 16px 20px 30px;
            border-top: 1px solid rgba(255,214,122,0.08);
            margin-top: auto;
        }

        .inquiry-card {
            background: rgba(255,255,255,0.08);
            border-radius: 22px;
            padding: 30px;
            box-shadow: 0 22px 66px rgba(0,0,0,0.26);
            transition: var(--transition);
            border: 1px solid rgba(255,214,122,0.12);
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(16px);
            z-index: 0;
        }

        .inquiry-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,214,122,0.14);
            filter: blur(24px);
            opacity: 0.8;
            pointer-events: none;
            transition: var(--transition-base);
            z-index: -1;
        }

        .inquiry-card:hover {
            box-shadow: 0 24px 70px rgba(0,0,0,0.32);
            transform: translateY(-4px);
            border-color: rgba(255,214,122,0.22);
        }

        .inquiry-card:hover::before {
            transform: translateX(-8px) scale(1.05);
            opacity: 1;
        }

        .inquiry-card.answered::before {
            background: rgba(52,199,89,0.14);
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .inquiry-icon-wrapper {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gold-light), rgba(196, 150, 58, 0.08));
            border-radius: var(--radius-md);
            color: var(--gold);
            font-size: 24px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .inquiry-icon-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--radius-md);
        }

        .inquiry-icon-wrapper .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1));
            color: white;
            font-weight: 900;
            font-size: 18px;
        }

        .inquiry-card.answered .inquiry-icon-wrapper {
            background: rgba(52, 199, 89, 0.1);
            color: var(--success);
        }

        .inquiry-info {
            flex: 1;
        }

        .inquiry-student {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .inquiry-date {
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .inquiry-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .inquiry-status.pending {
            background: rgba(255, 159, 64, 0.15);
            color: #FF9F40;
        }

        .inquiry-status.answered {
            background: rgba(52, 199, 89, 0.15);
            color: var(--success);
        }

        .inquiry-type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(196, 150, 58, 0.1);
            color: var(--gold);
        }

        .inquiry-course {
            padding: 16px 18px;
            background: rgba(255,214,122,0.12);
            border-radius: 18px;
            font-size: 14px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 10px;
            border-right: 3px solid var(--gold);
            backdrop-filter: blur(12px);
            min-height: 56px;
            line-height: 1.5;
        }

        .inquiry-text {
            padding: 14px 16px;
            background: rgba(255,255,255,0.08);
            border-radius: var(--radius-md);
            border-right: 3px solid rgba(255,214,122,0.22);
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-primary);
            backdrop-filter: blur(12px);
        }

        .inquiry-answer {
            padding: 12px 14px;
            background: rgba(52, 199, 89, 0.08);
            border-radius: var(--radius-md);
            border-right: 3px solid var(--success);
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-primary);
        }

        .inquiry-answer-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .inquiry-actions {
            display: flex;
            gap: 10px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .inquiry-action-btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            background: linear-gradient(135deg, rgba(255,214,122,0.95), rgba(196,150,58,0.95));
            color: white;
            border-radius: 20px;
            font-family: 'Tajawal', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 14px 32px rgba(255,214,122,0.16);
        }

        .inquiry-action-btn:hover {
            box-shadow: 0 4px 12px rgba(196, 150, 58, 0.3);
            transform: translateY(-2px);
        }

        .inquiry-action-btn.delete {
            background: transparent;
            border-color: var(--danger);
            color: var(--danger);
        }

        .inquiry-action-btn.delete:hover {
            background: rgba(255, 59, 48, 0.1);
        }

        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: var(--text-secondary);
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover { color: var(--text-primary); }

        .form-group {
            padding: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg);
            resize: vertical;
            min-height: 120px;
            outline: none;
        }

        .form-group textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-light);
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: var(--radius-md);
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(196, 150, 58, 0.3);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            background: var(--gold-light);
            color: var(--gold);
            border-color: var(--gold);
        }

        .hamburger-btn {
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
        }

        @media (max-width: 768px) {
            .hamburger-btn { display: flex !important; }
            .main { margin-right: 0 !important; }
            .topbar { padding: 0 12px; height: 56px; }
            .search-wrap { display: none; }
            .u-name, .u-role { display: none; }
            .user-profile-btn { min-width: auto; }
            .inquiries-container { grid-template-columns: 1fr; }
            .content { padding: 0 12px 12px !important; }
        }

        @media (max-width: 480px) {
            .topbar { padding: 0 8px; }
            .content { padding: 0 8px 8px !important; }
            .icon-btn { width: 36px; height: 36px; font-size: 17px; }
            .hamburger-btn { width: 36px; height: 36px; font-size: 18px; }
        }

        ::-webkit-scrollbar {
            display: none;
        }

        .sidebar,
        .main {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
              <button class="btn-close" type="button">×</button>
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
      <div class="page-header">
        <h1 class="page-title">الأسئلة والاستفسارات</h1>
        <p class="page-subtitle">الرد على أسئلة واستفسارات الطلاب عن الدروس</p>
      </div>

      <!-- Statistics -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(255,159,64,0.1), transparent); border-right: 3px solid #FF9F40;">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">قيد الانتظار</div>
            <div style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, #FF9F40, #FF7A30); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $pendingQuestions->total() + $pendingInquiries->count() }}</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(52,199,89,0.1), transparent); border-right: 3px solid var(--success);">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">تمت الإجابة</div>
            <div style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, var(--success), #2FA562); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $answeredQuestions->count() + $answeredInquiries->count() }}</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(196,150,58,0.1), transparent); border-right: 3px solid var(--gold);">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">المجموع</div>
            <div style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $pendingQuestions->total() + $pendingInquiries->count() + $answeredQuestions->count() + $answeredInquiries->count() }}</div>
        </div>
      </div>

      <!-- Pending -->
      @if($pendingQuestions->count() > 0)
        <div style="margin-bottom: 40px;">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <i class="ri-time-line" style="font-size: 20px; color: #FF9F40;"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">الأسئلة المعلقة</h2>
            <span style="background: rgba(255,159,64,0.15); color: #FF9F40; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $pendingQuestions->total() }}</span>
          </div>
          <div class="inquiries-container">
            @foreach($pendingQuestions as $question)
              <div class="inquiry-card">
                <div class="inquiry-header">
                  <div class="inquiry-icon-wrapper">
                    @if($question->student->avatar_url)
                      <img src="{{ asset('storage/' . $question->student->avatar_url) }}" alt="{{ $question->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($question->student->name, 0, 2) }}</div>
                    @endif
                  </div>
                  <div style="flex: 1;">
                    <div class="inquiry-student">{{ $question->student->name }}</div>
                    <div class="inquiry-date"><i class="ri-calendar-line"></i> {{ $question->created_at->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="inquiry-status pending"><i class="ri-time-line"></i> قيد الانتظار</span>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                  <div class="inquiry-course" style="flex: 1; margin: 0;">
                    <i class="ri-book-line"></i> <strong>{{ $question->lesson->name ?? $question->lesson->title ?? 'درس غير معروف' }}</strong>
                  </div>
                  <span class="inquiry-type">سؤال درس</span>
                </div>
                <div class="inquiry-text">{{ Str::limit($question->question_text, 150) }}</div>
                <div class="inquiry-actions">
                  <button type="button" data-item-type="question" data-item-id="{{ $question->id }}" class="inquiry-action-btn answer-open-btn">
                    <i class="ri-reply-all-line"></i> الرد على السؤال
                  </button>
                </div>
              </div>
            @endforeach
          </div>
          <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $pendingQuestions->links() }}
          </div>
        </div>
      @endif

      @if($pendingInquiries->count() > 0)
        <div style="margin-bottom: 40px;">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <i class="ri-time-line" style="font-size: 20px; color: #FF9F40;"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">الاستفسارات المعلقة</h2>
            <span style="background: rgba(255,159,64,0.15); color: #FF9F40; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $pendingInquiries->count() }}</span>
          </div>
          <div class="inquiries-container">
            @foreach($pendingInquiries as $inquiry)
              <div class="inquiry-card">
                <div class="inquiry-header">
                  <div class="inquiry-icon-wrapper">
                    @if($inquiry->student->avatar_url)
                      <img src="{{ asset('storage/' . $inquiry->student->avatar_url) }}" alt="{{ $inquiry->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($inquiry->student->name, 0, 2) }}</div>
                    @endif
                  </div>
                  <div style="flex: 1;">
                    <div class="inquiry-student">{{ $inquiry->student->name }}</div>
                    <div class="inquiry-date"><i class="ri-calendar-line"></i> {{ $inquiry->created_at->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="inquiry-status pending"><i class="ri-time-line"></i> قيد الانتظار</span>
                </div>
                <div class="inquiry-course" style="margin: 10px 0;">
                  <i class="ri-book-line"></i> <strong>{{ $inquiry->lesson->name ?? $inquiry->lesson->title ?? 'درس غير معروف' }}</strong>
                </div>
                <div class="inquiry-text">{{ Str::limit($inquiry->question_text, 150) }}</div>
                <div class="inquiry-actions">
                  <button type="button" data-item-type="inquiry" data-item-id="{{ $inquiry->id }}" class="inquiry-action-btn answer-open-btn">
                    <i class="ri-reply-all-line"></i> الرد على الاستفسار
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($pendingQuestions->count() == 0 && $pendingInquiries->count() == 0)
        <div class="empty-state" style="margin-bottom: 40px;">
          <i class="ri-inbox-line"></i>
          <h3>لا توجد أسئلة معلقة</h3>
          <p>جميع الأسئلة والاستفسارات تمت الإجابة عليها</p>
        </div>
      @endif

      <!-- Answered -->
      @if($answeredQuestions->count() > 0)
        <div style="margin-bottom: 40px;">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <i class="ri-checkbox-circle-line" style="font-size: 20px; color: var(--success);"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">الأسئلة المجابة</h2>
            <span style="background: rgba(52,199,89,0.15); color: var(--success); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $answeredQuestions->count() }}</span>
          </div>
          <div class="inquiries-container">
            @foreach($answeredQuestions as $question)
              <div class="inquiry-card answered">
                <div class="inquiry-header">
                  <div class="inquiry-icon-wrapper">
                    @if($question->student->avatar_url)
                      <img src="{{ asset('storage/' . $question->student->avatar_url) }}" alt="{{ $question->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($question->student->name, 0, 2) }}</div>
                    @endif
                  </div>
                  <div style="flex: 1;">
                    <div class="inquiry-student">{{ $question->student->name }}</div>
                    <div class="inquiry-date"><i class="ri-calendar-line"></i> {{ $question->created_at->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="inquiry-status answered"><i class="ri-check-double-line"></i> مجاب عليه</span>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                  <div class="inquiry-course" style="flex: 1; margin: 0;">
                    <i class="ri-book-line"></i> <strong>{{ $question->lesson->name ?? $question->lesson->title ?? 'درس غير معروف' }}</strong>
                  </div>
                  <span class="inquiry-type">سؤال درس</span>
                </div>
                <div class="inquiry-text">{{ Str::limit($question->question_text, 150) }}</div>
                <div class="inquiry-answer">
                  <div class="inquiry-answer-label"><i class="ri-check-line"></i> الإجابة</div>
                  {{ Str::limit($question->answer_text, 200) }}
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($answeredInquiries->count() > 0)
        <div>
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <i class="ri-checkbox-circle-line" style="font-size: 20px; color: var(--success);"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">الاستفسارات المجابة</h2>
            <span style="background: rgba(52,199,89,0.15); color: var(--success); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $answeredInquiries->count() }}</span>
          </div>
          <div class="inquiries-container">
            @foreach($answeredInquiries as $inquiry)
              <div class="inquiry-card answered">
                <div class="inquiry-header">
                  <div class="inquiry-icon-wrapper">
                    @if($inquiry->student->avatar_url)
                      <img src="{{ asset('storage/' . $inquiry->student->avatar_url) }}" alt="{{ $inquiry->student->name }}">
                    @else
                      <div class="avatar-placeholder">{{ mb_substr($inquiry->student->name, 0, 2) }}</div>
                    @endif
                  </div>
                  <div style="flex: 1;">
                    <div class="inquiry-student">{{ $inquiry->student->name }}</div>
                    <div class="inquiry-date"><i class="ri-calendar-line"></i> {{ $inquiry->created_at->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="inquiry-status answered"><i class="ri-check-double-line"></i> مجاب عليه</span>
                </div>
                <div class="inquiry-course" style="margin: 10px 0;">
                  <i class="ri-book-line"></i> <strong>{{ $inquiry->lesson->name ?? $inquiry->lesson->title ?? 'درس غير معروف' }}</strong>
                </div>
                <div class="inquiry-text">{{ Str::limit($inquiry->question_text, 150) }}</div>
                <div class="inquiry-answer">
                  <div class="inquiry-answer-label"><i class="ri-check-line"></i> الإجابة</div>
                  {{ Str::limit($inquiry->answer_text ?? 'بدون إجابة', 200) }}
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($answeredQuestions->count() == 0 && $answeredInquiries->count() == 0)
        <div class="empty-state">
          <i class="ri-checkbox-circle-line"></i>
          <h3>لم تجب على أي أسئلة بعد</h3>
          <p>ابدأ بالرد على الأسئلة المعلقة</p>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Modal -->
<div id="answerModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span id="answerModalTitle">الرد على السؤال</span>
      <button class="modal-close" id="answerModalCloseBtn">×</button>
    </div>
    <form id="answerForm" method="POST">
      @csrf
      <div class="form-group">
        <label>الإجابة</label>
        <textarea name="answer_text" placeholder="اكتب إجابتك هنا..." required></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="answerModalCancelBtn">إلغاء</button>
        <button type="submit" class="btn btn-primary">إرسال الإجابة</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Dark Mode
  function toggleDark() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';

    html.setAttribute('data-theme', newTheme);
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    localStorage.setItem('app-theme', newTheme);

    const icon = document.getElementById('darkIcon');
    if (icon) {
      icon.className = newTheme === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
    }
  }

  // Load saved theme
  (function() {
    const theme = localStorage.getItem('theme') || localStorage.getItem('app-theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
    if (theme === 'dark') {
      const icon = document.getElementById('darkIcon');
      if (icon) {
        icon.className = 'ri-sun-line';
      }
    }
  })();

  // Modal
  function openAnswerModal(itemType, itemId) {
    const endpoint = itemType === 'inquiry' ? `/teacher/inquiries/${itemId}/answer` : `/teacher/questions/${itemId}/answer`;
    const title = itemType === 'inquiry' ? 'الرد على الاستفسار' : 'الرد على السؤال';
    document.getElementById('answerForm').action = endpoint;
    document.getElementById('answerModalTitle').textContent = title;
    document.getElementById('answerModal').classList.add('active');
  }

  function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('darkBtn').addEventListener('click', toggleDark);

    document.querySelectorAll('.answer-open-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        openAnswerModal(this.dataset.itemType, this.dataset.itemId);
      });
    });

    document.getElementById('answerModalCloseBtn').addEventListener('click', function() {
      closeModal('answerModal');
    });

    document.getElementById('answerModalCancelBtn').addEventListener('click', function() {
      closeModal('answerModal');
    });
  });

  document.getElementById('answerModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('active');
    }
  });

  // Init theme icon
  window.addEventListener('load', function() {
    const theme = document.documentElement.getAttribute('data-theme');
    const icon = document.getElementById('darkIcon');
    if (theme === 'dark') {
      icon.className = 'ri-sun-line';
    }
  });

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

</script>
    @include('components.account-theme-foot')
</body>
</html>



