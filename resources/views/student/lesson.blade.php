<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($lesson->title); ?> | جمعية إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <style>
    * {
      --gold: var(--theme-gold);
      --dark-gold: var(--theme-gold-dark);
      --light: #f6f3ee;
      --dark: #1a1a1a;
      --gray: #666666;
      --light-gray: #999999;
      --light-gold: var(--theme-gold-soft);
      --success: var(--theme-success);
      --danger: var(--theme-danger);
      --bg-dark: var(--theme-page-bg);
      --bg-darker: var(--theme-surface);
      --card-bg: var(--theme-soft);
      --card-hover: var(--theme-soft-2);
      --border-color: var(--theme-border-strong);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background:
      radial-gradient(circle at 20% 20%, var(--theme-gold-soft), transparent 34%),
      linear-gradient(135deg, var(--theme-page-bg) 0%, var(--theme-surface) 50%, var(--theme-surface-2) 100%);
      color: var(--theme-text);
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
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

    @keyframes glow {
      0%, 100% { box-shadow: 0 0 10px rgba(198, 166, 117, 0.2); }
      50% { box-shadow: 0 0 20px rgba(198, 166, 117, 0.4); }
    }

    /* ===== MAIN WRAPPER ===== */
    .lesson-container {
      display: grid;
      grid-template-columns: 450px 1fr;
      gap: 0;
      height: 100vh;
    }

    /* ===== LEFT SECTION (MAIN PLAYER) ===== */
    .player-section {
      display: flex;
      flex-direction: column;
      padding: 3rem 2.5rem;
      overflow-y: auto;
      height: 100%;
      animation: slideInLeft 0.7s ease-out;
    }

    /* Header */
    .player-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1.5px solid var(--border-color);
    }

    .back-btn {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      color: var(--gold);
      text-decoration: none;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .back-btn:hover {
      gap: 1.2rem;
      color: var(--light-gold);
    }

    .back-btn i { font-size: 1.3rem; }

    .player-controls {
      display: flex;
      gap: 0.9rem;
    }

    .control-btn {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      border: 1.5px solid var(--border-color);
      background: rgba(198, 166, 117, 0.08);
      color: var(--gold);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .control-btn:hover {
      background: var(--gold);
      color: var(--theme-text);
      border-color: var(--gold);
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(198, 166, 117, 0.3);
    }

    /* VIDEO PLAYER */
    .video-container {
      width: 100%;
      aspect-ratio: 16 / 9;
      background: #000;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 30px 70px rgba(198, 166, 117, 0.3);
      margin-bottom: 2.5rem;
      position: relative;
      opacity: 1;
      visibility: visible;
      flex-shrink: 0;
    }

    .video-container video,
    .video-container audio,
    .video-container iframe {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      opacity: 1;
      visibility: visible;
      position: relative;
      z-index: 2;
      pointer-events: auto;
    }

    .video-container audio {
      height: auto;
      min-height: 40px;
      object-fit: initial;
    }

    .empty-player {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(198, 166, 117, 0.12) 0%, rgba(165, 122, 40, 0.05) 100%);
      gap: 1.5rem;
    }

    .empty-icon {
      font-size: 100px;
      animation: glow 2.5s ease-in-out infinite;
    }

    .empty-text {
      color: rgba(255, 255, 255, 0.7);
      font-size: 1.25rem;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    .youtube-fallback-overlay {
      position: absolute;
      inset: 0;
      display: none;
      align-items: center;
      justify-content: center;
      background: rgba(5, 10, 22, 0.86);
      z-index: 4;
      padding: 1.2rem;
    }

    .seek-flash {
      position: absolute;
      top: 0;
      bottom: 0;
      width: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      color: #fff;
      background: rgba(0, 0, 0, 0.25);
      z-index: 3;
      pointer-events: none;
      animation: seekFlashFade 0.5s ease-out;
    }
    .seek-flash-left { left: 0; border-radius: 0 18px 18px 0; }
    .seek-flash-right { right: 0; border-radius: 18px 0 0 18px; }
    @keyframes seekFlashFade {
      0% { opacity: 0; }
      20% { opacity: 1; }
      100% { opacity: 0; }
    }
    .seek-amount {
      display: block;
      text-align: center;
      font-size: 1.1rem;
      font-weight: 800;
      margin-top: 4px;
      text-shadow: 0 2px 8px rgba(0,0,0,0.6);
    }

    .youtube-fallback-card {
      width: min(560px, 100%);
      text-align: center;
      border: 1px solid rgba(198, 166, 117, 0.28);
      border-radius: 14px;
      padding: 1.1rem 1rem;
      background: rgba(18, 24, 38, 0.94);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
    }

    .youtube-fallback-title {
      color: #fff;
      font-weight: 800;
      margin-bottom: 0.45rem;
      font-size: 1.02rem;
    }

    .youtube-fallback-message {
      color: rgba(255, 255, 255, 0.86);
      font-size: 0.92rem;
      line-height: 1.65;
      margin-bottom: 0.9rem;
    }

    .youtube-fallback-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.45rem;
      text-decoration: none;
      color: var(--theme-text, #101317);
      background: var(--gold);
      border: 1px solid rgba(198, 166, 117, 0.8);
      border-radius: 10px;
      padding: 0.55rem 0.95rem;
      font-weight: 800;
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .youtube-fallback-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(198, 166, 117, 0.35);
    }

    /* VIDEO INFO BAR */
    .video-info-bar {
      background: linear-gradient(90deg, rgba(198, 166, 117, 0.15) 0%, rgba(198, 166, 117, 0.05) 100%);
      border: 1.5px solid var(--border-color);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 2.5rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 1.5rem;
      backdrop-filter: blur(10px);
      animation: slideUp 0.8s ease-out 0.2s both;
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.95rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .info-item:hover {
      color: var(--gold);
      transform: translateY(-2px);
    }

    .info-item i {
      color: var(--gold);
      font-size: 1.2rem;
      transition: transform 0.3s ease;
    }

    .info-item:hover i {
      transform: scale(1.15);
    }

    /* LESSON DESCRIPTION */
    .lesson-content {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 2.2rem;
      margin-bottom: 2.5rem;
      color: var(--theme-text);
      line-height: 1.95;
      letter-spacing: 0.3px;
      backdrop-filter: blur(10px);
      animation: slideUp 0.8s ease-out 0.3s both;
      transition: all 0.3s ease;
    }

    .lesson-content:hover {
      border-color: rgba(198, 166, 117, 0.4);
      background: var(--card-hover);
    }

    .lesson-content h2 {
      font-size: 2.1rem;
      margin: 0 0 1.2rem 0;
      color: var(--gold);
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .lesson-content p {
      color: rgba(255, 255, 255, 0.88);
      margin: 1rem 0;
      text-align: justify;
    }

    .lesson-content p:first-of-type {
      font-size: 1.05rem;
      font-weight: 500;
      opacity: 0.98;
    }

    .lesson-content strong {
      color: var(--light-gold);
      font-weight: 700;
    }

    /* LESSON MATERIALS */
    .materials-section {
      background: linear-gradient(135deg, rgba(198, 166, 117, 0.18) 0%, rgba(165, 122, 40, 0.1) 100%);
      border: 1.5px solid var(--border-color);
      border-radius: 16px;
      padding: 2rem;
      margin-bottom: 2.5rem;
      backdrop-filter: blur(10px);
      animation: slideUp 0.8s ease-out 0.4s both;
      transition: all 0.3s ease;
    }

    .materials-section:hover {
      border-color: var(--gold);
      box-shadow: 0 15px 40px rgba(198, 166, 117, 0.2);
    }

    .materials-title {
      color: var(--gold);
      font-weight: 700;
      margin-bottom: 1.3rem;
      display: flex;
      align-items: center;
      gap: 0.9rem;
      font-size: 1.15rem;
      letter-spacing: 0.5px;
    }

    .materials-title i { font-size: 1.4rem; }

    /* ===== RIGHT SIDEBAR ===== */
    .sidebar-right {
      grid-column: 1;
      padding: 3rem 2.5rem;
      overflow-y: auto;
      height: 100%;
      direction: ltr;
      display: flex;
      flex-direction: column;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.3) 100%);
      animation: slideInRight 0.7s ease-out;
      backdrop-filter: blur(5px);
      border-left: 1px solid var(--border-color);
    }

    .sidebar-right > * {
      direction: rtl;
    }

    /* Sidebar Sections */
    .sidebar-section {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 1.8rem;
      margin-bottom: 1.8rem;
      backdrop-filter: blur(10px);
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      animation: slideUp 0.6s ease-out both;
    }

    .sidebar-section:nth-child(1) { animation-delay: 0.1s; }
    .sidebar-section:nth-child(2) { animation-delay: 0.2s; }
    .sidebar-section:nth-child(3) { animation-delay: 0.3s; }
    .sidebar-section:nth-child(4) { animation-delay: 0.4s; }
    .sidebar-section:nth-child(5) { animation-delay: 0.5s; }

    .sidebar-section:hover {
      border-color: rgba(198, 166, 117, 0.5);
      background: var(--card-hover);
      box-shadow: 0 15px 40px rgba(198, 166, 117, 0.15);
      transform: translateY(-4px);
    }

    .sidebar-title {
      font-size: 1.1rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      margin: 0 0 1.4rem 0;
      color: var(--theme-text);
      letter-spacing: 0.3px;
    }

    .sidebar-title i {
      color: var(--gold);
      font-size: 1.4rem;
      transition: transform 0.3s ease;
    }

    .sidebar-section:hover .sidebar-title i {
      transform: scale(1.2) rotate(10deg);
    }

    /* PROGRESS CARD */
    .progress-card {
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      border-radius: 16px;
      padding: 2.2rem;
      color: var(--theme-text);
      text-align: center;
      margin-bottom: 1.8rem;
      box-shadow: 0 20px 50px rgba(198, 166, 117, 0.35);
      animation: slideUp 0.6s ease-out both;
    }

    .progress-value {
      font-size: 3.5rem;
      font-weight: 900;
      margin-bottom: 0.5rem;
      text-shadow: 0 3px 12px rgba(0, 0, 0, 0.4);
      letter-spacing: -1px;
    }

    .progress-label {
      font-size: 0.95rem;
      opacity: 0.96;
      font-weight: 600;
      margin-bottom: 1.2rem;
      letter-spacing: 0.5px;
    }

    .progress-bar {
      width: 100%;
      height: 12px;
      background: rgba(0, 0, 0, 0.25);
      border-radius: 12px;
      overflow: hidden;
      margin: 1.5rem 0;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, white 0%, rgba(255, 255, 255, 0.9) 100%);
      border-radius: 12px;
      width: var(--progress, 0%);
      transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
    }

    .progress-info {
      font-size: 0.9rem;
      opacity: 0.95;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    /* LESSONS LIST */
    .lessons-wrapper {
      max-height: 450px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .lesson-item {
      padding: 1.2rem;
      border-radius: 13px;
      cursor: pointer;
      border-left: 4px solid transparent;
      background: rgba(255, 255, 255, 0.05);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      text-decoration: none;
      color: inherit;
    }

    .lesson-item:hover {
      background: rgba(198, 166, 117, 0.2);
      border-left-color: var(--gold);
      transform: translateX(-6px);
      box-shadow: 0 8px 20px rgba(198, 166, 117, 0.15);
    }

    .lesson-item.active {
      background: linear-gradient(135deg, rgba(198, 166, 117, 0.5) 0%, rgba(165, 122, 40, 0.3) 100%);
      color: var(--theme-text);
      border-left-color: var(--theme-text);
      box-shadow: 0 12px 30px rgba(198, 166, 117, 0.25);
      border-radius: 13px;
    }

    .lesson-item-info h5 {
      margin: 0 0 0.5rem 0;
      font-size: 0.98rem;
      color: var(--theme-text);
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .lesson-item-info .duration {
      font-size: 0.8rem;
      opacity: 0.7;
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      align-items: center;
      gap: 0.3rem;
    }

    .lesson-check {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: rgba(198, 166, 117, 0.25);
      color: var(--gold);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      flex-shrink: 0;
      transition: all 0.3s ease;
      border: 1px solid rgba(198, 166, 117, 0.4);
    }

    .lesson-item.active .lesson-check {
      background: rgba(255, 255, 255, 0.35);
      color: var(--theme-text);
      border-color: var(--theme-text);
      box-shadow: 0 0 12px rgba(255, 255, 255, 0.3);
    }

    /* ACTIONS */
    .actions-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 0;
    }

    .action-btn {
      padding: 1.1rem 1.3rem;
      border: none;
      border-radius: 13px;
      font-size: 0.95rem;
      font-family: 'Cairo', sans-serif;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      width: 100%;
      letter-spacing: 0.3px;
      position: relative;
      overflow: hidden;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: var(--theme-text);
      box-shadow: 0 12px 30px rgba(198, 166, 117, 0.25);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 18px 45px rgba(198, 166, 117, 0.4);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.08);
      color: var(--gold);
      border: 1.5px solid var(--border-color);
    }

    .btn-secondary:hover {
      background: var(--gold);
      color: var(--theme-text);
      border-color: var(--gold);
      box-shadow: 0 12px 30px rgba(198, 166, 117, 0.3);
      transform: translateY(-2px);
    }

    /* MODAL DIALOG STYLES */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      z-index: 10000;
      align-items: center;
      justify-content: center;
      animation: fadeIn 0.3s ease;
      backdrop-filter: blur(3px);
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-dialog {
      background: linear-gradient(135deg, rgba(10, 14, 39, 0.98) 0%, rgba(22, 33, 62, 0.98) 100%);
      border: 1.5px solid var(--border-color);
      border-radius: 20px;
      padding: 2.5rem;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
      animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .modal-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.8rem;
      padding-bottom: 1.2rem;
      border-bottom: 1.5px solid var(--border-color);
    }

    .modal-header-icon {
      font-size: 2rem;
      color: var(--gold);
    }

    .modal-header h2 {
      margin: 0;
      font-size: 1.4rem;
      color: var(--theme-text);
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .modal-close {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
      width: 40px;
      height: 40px;
      border: none;
      background: rgba(255, 255, 255, 0.08);
      color: var(--gold);
      border-radius: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      transition: all 0.3s ease;
      border: 1.5px solid rgba(198, 166, 117, 0.3);
    }

    .modal-close:hover {
      background: var(--danger);
      color: var(--theme-text);
      border-color: var(--danger);
    }

    .modal-content {
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
      margin-bottom: 2rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
    }

    .form-group label {
      font-size: 0.95rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.85);
      letter-spacing: 0.3px;
    }

    .form-group input,
    .form-group textarea {
      padding: 1.2rem;
      border: 1.5px solid var(--border-color);
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      color: var(--theme-text);
      font-family: 'Cairo', sans-serif;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      resize: vertical;
      min-height: 120px;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--gold);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 0 20px rgba(198, 166, 117, 0.3);
    }

    .form-group textarea::placeholder,
    .form-group input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .modal-footer {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
    }

    .btn-cancel {
      padding: 0.95rem 1.8rem;
      background: rgba(255, 255, 255, 0.08);
      color: rgba(255, 255, 255, 0.7);
      border: 1.5px solid var(--border-color);
      border-radius: 11px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 600;
      transition: all 0.3s ease;
      font-family: 'Cairo', sans-serif;
    }

    .btn-cancel:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(198, 166, 117, 0.5);
      color: var(--theme-text);
    }

    .btn-submit {
      padding: 0.95rem 2.5rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: var(--theme-text);
      border: none;
      border-radius: 11px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 700;
      transition: all 0.3s ease;
      font-family: 'Cairo', sans-serif;
      box-shadow: 0 10px 25px rgba(198, 166, 117, 0.3);
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 35px rgba(198, 166, 117, 0.4);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    /* RATING SECTION */
    .rating-box {
      text-align: center;
      padding: 1.2rem 0;
    }

    .stars {
      font-size: 2.3rem;
      letter-spacing: 0.5rem;
      margin: 1.2rem 0;
      cursor: pointer;
      display: flex;
      justify-content: center;
      gap: 0.3rem;
    }

    .star {
      color: rgba(198, 166, 117, 0.35);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      display: inline-block;
      cursor: pointer;
    }

    .star:hover,
    .star.active {
      color: var(--gold);
      transform: scale(1.3) rotate(15deg);
      filter: drop-shadow(0 0 8px rgba(198, 166, 117, 0.5));
    }

    .rating-label {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
      font-weight: 600;
      letter-spacing: 0.3px;
      margin-top: 0.5rem;
    }

    /* NOTES SECTION */
    .notes-item {
      background: rgba(198, 166, 117, 0.12);
      padding: 1rem;
      border-radius: 11px;
      border-left: 3px solid var(--gold);
      animation: slideUp 0.4s ease-out;
    }

    .notes-time {
      font-size: 0.75rem;
      color: var(--gold);
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .notes-text {
      font-size: 0.92rem;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 0.6rem;
      line-height: 1.5;
    }

    .notes-delete {
      font-size: 0.75rem;
      padding: 0.4rem 0.8rem;
      background: var(--danger);
      color: var(--theme-text);
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.25s ease;
      font-weight: 600;
    }

    .notes-delete:hover {
      background: var(--theme-danger, #c7272a);
      transform: scale(1.05);
    }

    /* SCROLLBAR */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(198, 166, 117, 0.35);
      border-radius: 4px;
      transition: background 0.3s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(198, 166, 117, 0.6);
    }

    /* ===== MODAL STYLES ===== */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.72);
      backdrop-filter: blur(6px);
      z-index: 9999;
      animation: fadeIn 0.3s ease-out;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-dialog {
      background: linear-gradient(145deg, color-mix(in srgb, var(--theme-surface, #101827) 96%, transparent) 0%, color-mix(in srgb, var(--theme-surface-2, #1f2937) 98%, transparent) 100%);
      border: 1px solid var(--theme-border, rgba(198, 166, 117, 0.28));
      border-radius: 16px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
      animation: slideUp 0.4s ease-out;
      position: relative;
      overflow: hidden;
    }

    .modal-dialog::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(198, 166, 117, 0.14) 0%, transparent 72%);
      pointer-events: none;
    }

    .modal-close {
      position: absolute;
      top: 1.2rem;
      right: 1.2rem;
      background: none;
      border: none;
      color: var(--text-muted, #b5bed0);
      font-size: 1.5rem;
      cursor: pointer;
      z-index: 1;
      transition: all 0.3s ease;
      padding: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-close:hover {
      color: #c6752e;
      transform: rotate(90deg) scale(1.1);
    }

    .modal-header {
      background: linear-gradient(90deg, color-mix(in srgb, var(--theme-gold, #c6752e) 16%, transparent) 0%, color-mix(in srgb, var(--theme-gold-dark, #97722c) 10%, transparent) 100%);
      padding: 2rem 2rem 1.5rem 2rem;
      border-bottom: 1px solid rgba(198, 166, 117, 0.24);
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .modal-header-icon {
      font-size: 2rem;
      color: #c6752e;
      animation: slideInRight 0.5s ease-out;
    }

    .modal-header h2 {
      margin: 0;
      font-family: 'Josefin Slab', serif;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .modal-content {
      padding: 2rem;
      max-height: 400px;
      overflow-y: auto;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
    }

    .form-group label {
      font-weight: 600;
      font-size: 0.95rem;
      color: var(--theme-text, #ffffff);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .form-group textarea {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(198, 166, 117, 0.32);
      border-radius: 10px;
      color: var(--theme-text);
      padding: 1rem 1.2rem;
      font-family: 'Cairo', sans-serif;
      font-size: 0.95rem;
      resize: vertical;
      min-height: 120px;
      max-height: 250px;
      transition: all 0.3s ease;
      text-align: right;
    }

    .form-group textarea::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .form-group textarea:focus {
      outline: none;
      background: rgba(255, 255, 255, 0.08);
      border-color: #c6752e;
      box-shadow: 0 0 0 3px rgba(198, 166, 117, 0.2);
    }

    .form-group small,
    .form-group-hint {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.5);
      text-align: right;
    }

    html[data-theme="light"] .form-group small,
    html[data-theme="light"] .form-group-hint {
      color: var(--theme-text-soft) !important;
    }

    .modal-footer {
      display: flex;
      gap: 1rem;
      padding: 1.5rem 2rem;
      border-top: 1px solid rgba(198, 166, 117, 0.22);
      justify-content: flex-start;
    }

    .btn-cancel {
      flex: 1;
      padding: 0.9rem 1.5rem;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(198, 166, 117, 0.25);
      color: var(--theme-text);
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-cancel:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .btn-submit {
      flex: 1;
      padding: 0.9rem 1.5rem;
      background: linear-gradient(90deg, var(--theme-gold, #c6752e) 0%, var(--theme-gold-dark, #97722c) 100%);
      border: none;
      color: var(--theme-text);
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.35s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(198, 166, 117, 0.34);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    /* ===== LIGHT MODE ===== */
    /* Note: html.light-mode is a dead class — the theme system uses data-theme="light" */
    html[data-theme="light"] {
      --card-bg: #ffffff;
      --card-hover: #f8f6f2;
      --border-color: #DFE5EC;
    }

    html[data-theme="light"] .player-header {
      border-bottom-color: var(--theme-border);
    }

    html[data-theme="light"] .video-info-bar,
    html[data-theme="light"] .lesson-content,
    html[data-theme="light"] .materials-section {
      color: var(--theme-text);
    }

    html[data-theme="light"] .lesson-content p {
      color: var(--theme-text) !important;
    }

    html[data-theme="light"] .back-btn,
    html[data-theme="light"] .control-btn {
      color: var(--theme-gold);
      border-color: var(--theme-border);
      background: #ffffff;
    }

    html[data-theme="light"] .back-btn:hover,
    html[data-theme="light"] .control-btn:hover {
      background: var(--theme-gold-soft);
      border-color: var(--theme-gold);
    }

    html[data-theme="light"] .sidebar-section {
      background: #ffffff;
      border-color: var(--theme-border);
      box-shadow: 0 1px 6px rgba(34, 43, 61, 0.06);
    }

    html[data-theme="light"] .sidebar-section:hover {
      background: #faf9f7;
      border-color: rgba(198, 166, 117, 0.45);
      box-shadow: 0 4px 18px rgba(198, 166, 117, 0.12);
    }

    html[data-theme="light"] .sidebar-title {
      color: var(--theme-text);
    }

    html[data-theme="light"] .stat-value,
    html[data-theme="light"] .stat-label,
    html[data-theme="light"] .meta-item,
    html[data-theme="light"] .meta-value {
      color: var(--theme-text);
    }

    html[data-theme="light"] .meta-label {
      color: var(--theme-text-soft);
    }

    html[data-theme="light"] .progress-bar-bg {
      background: var(--theme-surface-2);
    }

    html[data-theme="light"] .info-item {
      color: var(--theme-text-soft) !important;
    }

    html[data-theme="light"] .youtube-fallback-card {
      background: var(--theme-surface) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08) !important;
    }

    html[data-theme="light"] .youtube-fallback-title {
      color: var(--theme-text) !important;
    }

    html[data-theme="light"] .youtube-fallback-message {
      color: var(--theme-text-soft) !important;
    }

    html[data-theme="light"] .sidebar-right {
      background: linear-gradient(135deg, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0.3) 100%) !important;
    }

    html[data-theme="light"] .lesson-item {
      background: rgba(0, 0, 0, 0.03) !important;
    }

    html[data-theme="light"] .lesson-item:hover {
      background: rgba(198, 166, 117, 0.12) !important;
    }

    /* ── Comprehensive light-mode text/bg fixes ─────────────────────────── */
    html[data-theme="light"] .empty-text,
    html[data-theme="light"] .lesson-item-info .duration,
    html[data-theme="light"] .rating-label,
    html[data-theme="light"] .notes-text,
    html[data-theme="light"] .notes-footer,
    html[data-theme="light"] .section-subtitle,
    html[data-theme="light"] .video-info-sub,
    html[data-theme="light"] .nav-lesson-sub {
      color: var(--theme-text-soft) !important;
    }

    html[data-theme="light"] .form-group label {
      color: var(--theme-text) !important;
    }

    html[data-theme="light"] .form-group input,
    html[data-theme="light"] .form-group textarea {
      background: rgba(0, 0, 0, 0.04) !important;
      color: var(--theme-text) !important;
    }

    html[data-theme="light"] .form-group textarea::placeholder,
    html[data-theme="light"] .form-group input::placeholder {
      color: var(--theme-text-soft) !important;
      opacity: 0.6;
    }

    html[data-theme="light"] .form-group small,
    html[data-theme="light"] .form-group small[style] {
      color: var(--theme-text-soft) !important;
    }

    html[data-theme="light"] .btn-cancel {
      background: rgba(0, 0, 0, 0.06) !important;
      color: var(--theme-text) !important;
      border-color: var(--theme-border) !important;
    }

    html[data-theme="light"] .btn-cancel:hover {
      background: rgba(0, 0, 0, 0.1) !important;
    }

    html[data-theme="light"] .lesson-section-header,
    html[data-theme="light"] .lesson-section-title {
      color: var(--theme-text) !important;
    }

    html[data-theme="light"] .sidebar-section {
      color: var(--theme-text);
    }

    html[data-theme="light"] .materials-label,
    html[data-theme="light"] .materials-badge {
      color: var(--theme-text);
    }

    html[data-theme="light"] .lesson-item:not(.active) .lesson-item-info h5 {
      color: var(--theme-text);
    }
    /* ──────────────────────────────────────────────────────────────────── */

    html.light-mode .modal-dialog,
    html[data-theme="light"] .modal-dialog {
      background: linear-gradient(135deg, #ede9e0 0%, #e0dcd3 100%);
    }

    html.light-mode .modal-header,
    html[data-theme="light"] .modal-header {
      background: linear-gradient(90deg, rgba(198, 166, 117, 0.1) 0%, rgba(160, 122, 40, 0.05) 100%);
      border-bottom-color: rgba(0, 0, 0, 0.1);
    }

    html.light-mode .modal-dialog,
    html.light-mode .modal-header h2,
    html.light-mode .modal-header-icon,
    html.light-mode .form-group label,
    html[data-theme="light"] .modal-dialog,
    html[data-theme="light"] .modal-header h2,
    html[data-theme="light"] .modal-header-icon,
    html[data-theme="light"] .form-group label {
      color: #1a1a1a;
    }

    html.light-mode .form-group textarea,
    html[data-theme="light"] .form-group textarea {
      background: rgba(0, 0, 0, 0.02);
      border-color: rgba(0, 0, 0, 0.1);
      color: #1a1a1a;
    }

    html.light-mode .form-group textarea::placeholder,
    html[data-theme="light"] .form-group textarea::placeholder {
      color: rgba(0, 0, 0, 0.4);
    }

    html.light-mode .form-group textarea:focus,
    html[data-theme="light"] .form-group textarea:focus {
      background: rgba(0, 0, 0, 0.04);
      border-color: var(--gold);
    }

    html.light-mode .btn-cancel,
    html[data-theme="light"] .btn-cancel {
      background: rgba(0, 0, 0, 0.03);
      border-color: rgba(0, 0, 0, 0.1);
      color: #1a1a1a;
    }

    html.light-mode .btn-cancel:hover,
    html[data-theme="light"] .btn-cancel:hover {
      background: rgba(0, 0, 0, 0.06);
      border-color: rgba(0, 0, 0, 0.15);
    }

    html.light-mode .modal-footer,
    html[data-theme="light"] .modal-footer {
      border-top-color: rgba(0, 0, 0, 0.1);
    }

    html.light-mode ::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.02);
    }

    html.light-mode ::-webkit-scrollbar-thumb {
      background: rgba(198, 166, 117, 0.3);
    }

    html.light-mode ::-webkit-scrollbar-thumb:hover {
      background: rgba(198, 166, 117, 0.5);
    }

    /* Unified Modal Override */
    .modal-overlay {
      background: rgba(0, 0, 0, 0.76) !important;
      backdrop-filter: blur(7px) !important;
    }

    .modal-dialog {
      background: linear-gradient(145deg, color-mix(in srgb, var(--theme-surface, #101827) 97%, transparent) 0%, color-mix(in srgb, var(--theme-surface-2, #1f2937) 99%, transparent) 100%) !important;
      border: 1px solid var(--theme-border, rgba(198, 166, 117, 0.3)) !important;
      border-radius: 16px !important;
    }

    .modal-header {
      background: linear-gradient(90deg, rgba(198, 166, 117, 0.14) 0%, rgba(151, 114, 44, 0.08) 100%) !important;
      border-bottom: 1px solid rgba(198, 166, 117, 0.24) !important;
    }

    .modal-header h2,
    .modal-header-icon {
      color: var(--theme-text, #ffffff) !important;
    }

    .form-group textarea {
      background: var(--theme-soft, rgba(255, 255, 255, 0.06)) !important;
      border: 1px solid color-mix(in srgb, var(--theme-gold, #c6752e) 35%, var(--theme-border, transparent)) !important;
      color: var(--theme-text, #ffffff) !important;
    }

    .form-group textarea:focus {
      border-color: var(--theme-gold, #c6752e) !important;
      box-shadow: 0 0 0 3px rgba(198, 166, 117, 0.2) !important;
    }

    .btn-cancel {
      background: var(--theme-soft, rgba(255, 255, 255, 0.08)) !important;
      border: 1px solid var(--theme-border, rgba(198, 166, 117, 0.24)) !important;
      color: var(--theme-text, #ffffff) !important;
    }

    .btn-submit {
      background: linear-gradient(90deg, var(--theme-gold, #c6752e) 0%, var(--theme-gold-dark, #97722c) 100%) !important;
      box-shadow: 0 8px 25px rgba(198, 166, 117, 0.3) !important;
    }

    html[data-theme="light"] .modal-dialog {
      background: linear-gradient(145deg, #f7f4ee 0%, #efebe2 100%) !important;
      border-color: rgba(151, 114, 44, 0.22) !important;
    }

    html[data-theme="light"] .modal-header {
      background: linear-gradient(90deg, rgba(198, 166, 117, 0.12), rgba(151, 114, 44, 0.06)) !important;
    }

    html[data-theme="light"] .modal-header h2,
    html[data-theme="light"] .modal-header-icon,
    html[data-theme="light"] .form-group label {
      color: #222b36 !important;
    }

    html[data-theme="light"] .form-group textarea {
      background: #ffffff !important;
      color: #222b36 !important;
      border-color: rgba(151, 114, 44, 0.22) !important;
    }

    html[data-theme="light"] .btn-cancel {
      background: #ffffff !important;
      color: #222b36 !important;
    }

    /* RESPONSIVE */
    @media (max-width: 1500px) {
      .lesson-container {
        grid-template-columns: 1fr 400px;
      }
      .player-section {
        padding: 2.5rem 2rem;
      }
    }

    @media (max-width: 1200px) {
      .lesson-container {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto;
      }
      .player-section {
        padding: 2rem;
      }
      .sidebar-right {
        padding: 2.5rem 2rem;
        max-height: none;
        border-right: none;
        border-top: 1px solid var(--border-color);
      }
    }

    @media (max-width: 768px) {
      .lesson-container {
        grid-template-columns: 1fr;
      }
      .player-section {
        padding: 1.5rem 1rem;
      }
      .sidebar-right {
        padding: 1.5rem 1rem;
      }
      .video-container {
        aspect-ratio: 16 / 9;
        margin-bottom: 1.8rem;
        border-radius: 14px;
      }
      .control-btn {
        width: 42px;
        height: 42px;
        font-size: 1.2rem;
      }
      .action-btn {
        font-size: 0.85rem;
        padding: 0.9rem 1rem;
      }
      .sidebar-section {
        padding: 1.4rem;
      }
      .lesson-content {
        padding: 1.6rem;
      }
      .lesson-content h2 {
        font-size: 1.6rem;
      }
      .progress-value {
        font-size: 2.8rem;
      }
      .lessons-wrapper {
        max-height: 350px;
      }
    }

    @media (max-width: 480px) {
      .player-section {
        padding: 1.2rem 0.8rem;
      }
      .sidebar-right {
        padding: 1.2rem 0.8rem;
      }
      .video-info-bar {
        grid-template-columns: 1fr;
      }
      .control-btn {
        width: 38px;
        height: 38px;
        font-size: 1rem;
      }
      .player-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
      }
    }
  </style>
</head>
<body>

<div class="lesson-container">
  <!-- RIGHT: SIDEBAR -->
  <div class="sidebar-right">
    <!-- Progress Card -->
    <div class="progress-card">
      <div class="progress-value" id="progressPercent"><?php echo e(count($lessonsWithProgress) > 0 ? min(100, round(collect($lessonsWithProgress)->where('completed', true)->count() / count($lessonsWithProgress) * 100)) : 0); ?>%</div>
      <div class="progress-label">نسبة الإكمال</div>
      <div class="progress-bar">
        <div class="progress-fill" style="--progress: <?php echo e(count($lessonsWithProgress) > 0 ? min(100, round(collect($lessonsWithProgress)->where('completed', true)->count() / count($lessonsWithProgress) * 100)) : 0); ?>%"></div>
      </div>
      <div class="progress-info"><?php echo e(collect($lessonsWithProgress)->where('completed', true)->count()); ?>/<?php echo e(count($lessonsWithProgress)); ?> دروس</div>
    </div>

    <!-- Lessons List -->
    <div class="sidebar-section">
      <div class="sidebar-title">
        <i class="ri-list-check-2"></i> الدروس
      </div>
      <div class="lessons-wrapper">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $lessonsWithProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
          <a href="<?php echo e(route('student.lesson.show', $ls['id'])); ?>" style="text-decoration: none; color: inherit;">
            <div class="lesson-item <?php echo e($ls['is_current'] ? 'active' : ''); ?>">
              <div class="lesson-item-info">
                <h5><?php echo e($ls['title']); ?></h5>
                <div class="duration"><i class="ri-time-line"></i> <?php echo e($ls['duration'] ?? 0); ?> د</div>
              </div>
              <div class="lesson-check">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ls['completed']): ?>
                  <i class="ri-check-fill"></i>
                <?php else: ?>
                  <i class="ri-circle-line"></i>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
              </div>
            </div>
          </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
          <div style="text-align: center; padding: 2rem 0; color: var(--light-gray); font-size: 0.9rem;">
            <i class="ri-inbox-line" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
            <div>لا توجد دروس</div>
          </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>
    </div>

    <!-- Rating Section -->
    <div class="sidebar-section">
      <div class="sidebar-title">
        <i class="ri-star-line"></i> التقييم
      </div>
      <div class="rating-box">
        <div class="stars" id="starRating">
          <span class="star" data-rating="1">★</span>
          <span class="star" data-rating="2">★</span>
          <span class="star" data-rating="3">★</span>
          <span class="star" data-rating="4">★</span>
          <span class="star" data-rating="5">★</span>
        </div>
        <div class="rating-label" id="ratingLabel">اختر تقييمك</div>
      </div>
    </div>

    <!-- Actions -->
    <div class="sidebar-section">
      <div class="actions-group">
        <button class="action-btn btn-primary" id="markCompleteBtn">
          <i class="ri-check-double-line"></i> إكمال الدرس
        </button>
        <button class="action-btn btn-secondary" id="addNoteBtn">
          <i class="ri-sticky-note-line"></i> اطرح ملاحظة</button>
        <button class="action-btn btn-secondary" id="askQuestionBtn">
          <i class="ri-question-line"></i> اسأل سؤالا</button>
      </div>
    </div>

    <!-- Notes List -->
    <div class="sidebar-section">
      <div class="sidebar-title">
        <i class="ri-sticky-note-2-line"></i> ملاحظاتك
      </div>
      <div id="notesList" style="display: flex; flex-direction: column; gap: 0.8rem;"></div>
    </div>

    <!-- Questions List -->
    <div class="sidebar-section">
      <div class="sidebar-title">
        <i class="ri-question-answer-line"></i> أسئلتك
      </div>
      <div id="questionsList" style="display: flex; flex-direction: column; gap: 0.8rem;"></div>
    </div>
  </div>

  <!-- LEFT: PLAYER SECTION -->
  <div class="player-section">
    <!-- Header -->
    <div class="player-header">
      <a href="<?php echo e(route('student.academy')); ?>" class="back-btn">
        <i class="ri-arrow-right-line"></i> العودة
      </a>
      <div class="player-controls">
        <button class="control-btn" id="toggleBookmarkBtn" title="حفظ"><i class="ri-bookmark-line" id="bookmarkIcon"></i></button>
        <button class="control-btn" id="shareLessonBtn" title="مشاركة"><i class="ri-share-line"></i></button>
        <button class="control-btn" title="تبديل الثيم" id="themeToggle"><i class="ri-moon-line"></i></button>
      </div>
    </div>

    <!-- Video Player -->
    <div class="video-container">
      <?php
        $mediaDisk = config('media.disk', 'public');
        $videoSource = null;
        $audioSource = null;
        $videoMimeType = 'video/mp4';
        $audioMimeType = 'audio/mpeg';

        if (!empty($lesson->video_file)) {
          $videoSource = \Illuminate\Support\Str::startsWith($lesson->video_file, ['http://', 'https://'])
            ? $lesson->video_file
            : route('student.lesson.media', ['lesson' => $lesson->id, 'type' => 'video']);
          if (function_exists('guessMessagingMimeType')) {
            $videoMimeType = guessMessagingMimeType($lesson->video_file);
          }
        }

        if (!empty($lesson->audio_file)) {
          $audioSource = \Illuminate\Support\Str::startsWith($lesson->audio_file, ['http://', 'https://'])
            ? $lesson->audio_file
            : route('student.lesson.media', ['lesson' => $lesson->id, 'type' => 'audio']);
          if (function_exists('guessMessagingMimeType')) {
            $audioMimeType = guessMessagingMimeType($lesson->audio_file);
          }
        }
      ?>
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lesson->lesson_type === 'video-upload' && $lesson->video_file): ?>
        <video id="mainVideo" controls preload="metadata" style="width: 100%; height: 100%; object-fit: cover;">
          <source src="<?php echo e($videoSource); ?>" type="<?php echo e($videoMimeType); ?>">
        </video>
      <?php elseif($lesson->lesson_type === 'audio-upload' && $lesson->audio_file): ?>
        <div class="empty-player">
          <div class="empty-icon">🎧</div>
          <audio controls style="width: 80%; max-width: 400px;">
            <source src="<?php echo e($audioSource); ?>" type="<?php echo e($audioMimeType); ?>">
          </audio>
        </div>
      <?php elseif($lesson->lesson_type === 'youtube' && $lesson->video_url): ?>
        <?php
          $youtubeUrl = trim((string) $lesson->video_url);
          $normalizedYoutubeUrl = $youtubeUrl;
          if ($normalizedYoutubeUrl !== '' && !preg_match('#^https?://#i', $normalizedYoutubeUrl)) {
            $normalizedYoutubeUrl = 'https://' . ltrim($normalizedYoutubeUrl, '/');
          }

          $videoId = null;
          $parts = @parse_url($normalizedYoutubeUrl);
          $host = strtolower($parts['host'] ?? '');
          $host = preg_replace('/^www\./', '', $host);
          $path = trim((string) ($parts['path'] ?? ''), '/');
          $query = (string) ($parts['query'] ?? '');
          $queryParams = [];
          if ($query !== '') {
            parse_str($query, $queryParams);
          }

          $candidates = [];
          if (!empty($queryParams['v'])) {
            $candidates[] = $queryParams['v'];
          }
          if (!empty($queryParams['vi'])) {
            $candidates[] = $queryParams['vi'];
          }
          if ($normalizedYoutubeUrl !== '' && preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/|e/))([A-Za-z0-9_-]{11})~i', $normalizedYoutubeUrl, $m)) {
            $candidates[] = $m[1];
          }
          if (str_contains($host, 'youtu.be') && $path !== '') {
            $candidates[] = explode('/', $path)[0];
          }
          if ($path !== '' && preg_match('~^(?:embed|shorts|live|v|e)/([^/?#]+)~i', $path, $m)) {
            $candidates[] = $m[1];
          }
          if ($path !== '' && str_contains($host, 'youtube')) {
            $pathParts = explode('/', $path);
            $first = strtolower($pathParts[0] ?? '');
            if (in_array($first, ['embed', 'shorts', 'live', 'v', 'e'], true) && !empty($pathParts[1])) {
              $candidates[] = $pathParts[1];
            }
          }

          foreach ($candidates as $candidate) {
            $candidate = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $candidate);
            if (preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate)) {
              $videoId = $candidate;
              break;
            }
          }

          $watchUrl = $videoId ? "https://www.youtube.com/watch?v={$videoId}" : null;
        ?>
        <?php if($videoId): ?>
          <div
            id="player"
            data-video-id="<?php echo e($videoId); ?>"
            data-original-url="<?php echo e($normalizedYoutubeUrl); ?>"
            data-watch-url="<?php echo e($watchUrl); ?>"
            style="width: 100%; height: 100%;"
          ></div>
          <div id="youtubeFallbackOverlay" class="youtube-fallback-overlay" aria-live="polite">
            <div class="youtube-fallback-card">
              <div class="youtube-fallback-title">لا يمكن تشغيل الفيديو داخل المنصة</div>
              <div id="youtubeFallbackMessage" class="youtube-fallback-message">هذا الفيديو مقيد من YouTube ولا يدعم التضمين داخل المواقع.</div>
              <a id="youtubeFallbackLink" class="youtube-fallback-btn" href="#" target="_blank" rel="noopener noreferrer">
                <i class="ri-external-link-line"></i>
                <span>فتح الفيديو في YouTube</span>
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="empty-player">
            <div class="empty-icon">&#x1F4F9;</div>
            <div class="empty-text">رابط يوتيوب غير صالح</div>
          </div>
        <?php endif; ?>
            <?php else: ?>
        <div class="empty-player">
          <div class="empty-icon">&#x1F4F9;</div>
          <div class="empty-text">لم يتم رفع محتوى بعد</div>
        </div>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Video Info Bar -->
    <div class="video-info-bar">
      <div class="info-item"><i class="ri-time-line"></i> <span id="viewTime">0:00</span></div>
      <div class="info-item"><i class="ri-calendar-line"></i> <?php echo e($lesson->created_at ? $lesson->created_at->format('d M Y') : '---'); ?></div>
      <div class="info-item"><i class="ri-eye-line"></i> <span id="viewCount">128</span> مشاهدة</div>
      <div class="info-item"><i class="ri-star-fill" style="color: var(--gold, #FFD700);"></i> <span id="lessonAverageRating">-</span>/5</div>
    </div>

    <!-- Lesson Description -->
    <div class="lesson-content">
      <h2><?php echo e($lesson->title); ?></h2>
      <p><?php echo e($lesson->description ?? 'وصف الدرس سيظهر هنا'); ?></p>
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lesson->content): ?>
        <p style="margin-top: 1.5rem; opacity: 0.85; border-top: 1px solid rgba(198, 166, 117, 0.2); padding-top: 1rem;">
          <?php echo nl2br($lesson->content); ?>

        </p>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Materials -->
    <div class="materials-section">
      <div class="materials-title">
        <i class="ri-file-download-line"></i> المواد التعليمية
      </div>
      <button class="action-btn btn-secondary" id="downloadResourcesBtn" style="margin-top: 1rem;">
        <i class="ri-download-cloud-line"></i> تحميل جميع المواد
      </button>
    </div>
  </div>
</div>

<!-- MODALS -->
<!-- Add Note Modal -->
<div class="modal-overlay" id="noteModal">
  <div class="modal-dialog">
    <button class="modal-close" id="closeNoteModalBtn"><i class="ri-close-line"></i></button>
    <div class="modal-header">
      <div class="modal-header-icon"><i class="ri-sticky-note-fill"></i></div>
      <h2>إضافة ملاحظة</h2>
    </div>
    <div class="modal-content">
      <div class="form-group">
        <label for="noteText">اكتب ملاحظتك هنا:</label>
        <textarea id="noteText" placeholder="أضف ملاحظة مهمة عن الدرس..." maxlength="500"></textarea>
        <small class="form-group-hint" style="font-size: 0.8rem;">الحد الأقصى 500 حرف</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="closeNoteModalBtn2">إلغاء</button>
      <button class="btn-submit" id="submitNoteBtn"><i class="ri-check-line"></i> حفظ الملاحظة</button>
    </div>
  </div>
</div>

<!-- Edit Note Modal -->
<div class="modal-overlay" id="editNoteModal">
  <div class="modal-dialog">
    <button class="modal-close" onclick="closeEditNoteModal()"><i class="ri-close-line"></i></button>
    <div class="modal-header">
      <div class="modal-header-icon"><i class="ri-edit-2-fill"></i></div>
      <h2>تعديل الملاحظة</h2>
    </div>
    <div class="modal-content">
      <div class="form-group">
        <label for="editNoteText">تعديل نص الملاحظة:</label>
        <textarea id="editNoteText" placeholder="اكتب ملاحظتك..." maxlength="500"></textarea>
        <small class="form-group-hint" style="font-size:0.8rem;">الحد الأقصى 500 حرف</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeEditNoteModal()">إلغاء</button>
      <button class="btn-submit" onclick="saveEditNote()"><i class="ri-check-line"></i> حفظ التعديل</button>
    </div>
  </div>
</div>

<!-- Ask Question Modal -->
<div class="modal-overlay" id="questionModal">
  <div class="modal-dialog">
    <button class="modal-close" id="closeQuestionModalBtn"><i class="ri-close-line"></i></button>
    <div class="modal-header">
      <div class="modal-header-icon"><i class="ri-question-fill"></i></div>
      <h2>اسأل سؤالا</h2>
    </div>
    <div class="modal-content">
      <div class="form-group">
        <label for="questionText">اكتب سؤالك للمعلم:</label>
        <textarea id="questionText" placeholder="اطرح سؤالك أو استفسارك عن الدرس..." maxlength="600"></textarea>
        <small class="form-group-hint" style="font-size: 0.8rem;">الحد الأقصى 600 حرف</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="closeQuestionModalBtn2">إلغاء</button>
      <button class="btn-submit" id="submitQuestionBtn"><i class="ri-send-plane-fill"></i> إرسال السؤال</button>
    </div>
  </div>
</div>

<script>
<?php
  $requiresPlaybackProof = in_array($lesson->lesson_type, ['video-upload', 'audio-upload', 'youtube'], true);
  $currentProgressStatus = $currentProgress->status ?? '';
  $isLessonCompleted = ($currentProgressStatus === 'completed');
  $currentProgressPct = (int) ($currentProgress->progress_percentage ?? 0);
  $canCompleteInitial = ($currentProgressPct >= 90) || $isLessonCompleted;
?>
let userNotes = [];
let userQuestions = {!! json_encode($lessonQuestions->map(function($q){ return ['id'=>$q->id,'text'=>$q->question_text,'time'=>$q->created_at->format('Y-m-d H:i'),'status'=>$q->status]; })->values()) !!};
let isBookmarked = false;
let currentRating = 0;
let isDarkMode = true;

const LESSON_ID = <?php echo e($lesson->id); ?>;
const PROGRESS_URL  = '<?php echo e(route("student.progress.update", $lesson->id)); ?>';
const DOWNLOAD_URL  = '<?php echo e(route("student.lesson.resources.download", $lesson->id)); ?>';
const INQUIRY_URL   = '<?php echo e(route("student.inquiry.store")); ?>';
const NOTES_URL     = '<?php echo e(route("student.lesson.notes.get", $lesson->id)); ?>';
const RATING_URL    = '<?php echo e(route("student.lesson.rating.get", $lesson->id)); ?>';
const REQUIRES_PLAYBACK_PROOF = <?php echo json_encode($requiresPlaybackProof, 15, 512) ?>;
const playback = {
  kind: null,
  media: null,
  youtubePlayer: null,
  youtubeTimer: null,
  youtubeInitTimer: null,
  youtubeErrorShown: false,
  youtubeBlocked: false,
  duration: 0,
  current: 0,
  maxWatched: 0,
  lastTime: 0,
  lastTrackAt: 0,
  canComplete: <?php echo json_encode($canCompleteInitial, 15, 512) ?>,
  completed: <?php echo json_encode($isLessonCompleted, 15, 512) ?>,
  completeInFlight: false,
  guardSeek: false,
  lastSeekWarningAt: 0,
};

document.addEventListener('DOMContentLoaded', () => {
  loadUserNotes();
  displayQuestions();
  loadBookmarkStatus();
  loadSavedRating();

  const savedMax = Number(localStorage.getItem('lesson_' + LESSON_ID + '_max_watched_seconds') || 0);
  if (Number.isFinite(savedMax) && savedMax > 0) {
    playback.maxWatched = savedMax;
    playback.lastTime = savedMax;
  }

  initializePlayerTracking();
  initializeSeekControls();

  // Star rating via event delegation
  document.getElementById('starRating').addEventListener('click', (e) => {
    const star = e.target.closest('.star');
    if (star && star.dataset.rating) {
      setRating(parseInt(star.dataset.rating, 10));
    }
  });

  // Action buttons
  document.getElementById('markCompleteBtn').addEventListener('click', markAsComplete);
  document.getElementById('addNoteBtn').addEventListener('click', addNote);
  document.getElementById('askQuestionBtn').addEventListener('click', askQuestion);

  // Player control buttons
  document.getElementById('toggleBookmarkBtn').addEventListener('click', toggleBookmark);
  document.getElementById('shareLessonBtn').addEventListener('click', shareLesson);
  document.getElementById('themeToggle').addEventListener('click', toggleDark);

  // Download resources
  document.getElementById('downloadResourcesBtn').addEventListener('click', downloadResources);

  // Note modal buttons
  document.getElementById('closeNoteModalBtn').addEventListener('click', closeNoteModal);
  document.getElementById('closeNoteModalBtn2').addEventListener('click', closeNoteModal);
  document.getElementById('submitNoteBtn').addEventListener('click', submitNote);

  // Question modal buttons
  document.getElementById('closeQuestionModalBtn').addEventListener('click', closeQuestionModal);
  document.getElementById('closeQuestionModalBtn2').addEventListener('click', closeQuestionModal);
  document.getElementById('submitQuestionBtn').addEventListener('click', submitQuestion);

  // Note actions via event delegation
  document.getElementById('notesList').addEventListener('click', (e) => {
    const del  = e.target.closest('.notes-delete');
    const edit = e.target.closest('.notes-edit');
    if (del  && del.dataset.noteIndex  !== undefined) deleteNote(parseInt(del.dataset.noteIndex, 10));
    if (edit && edit.dataset.noteIndex !== undefined) openEditNote(parseInt(edit.dataset.noteIndex, 10));
  });

  document.getElementById('noteModal').addEventListener('click', (e) => {
    if(e.target.id === 'noteModal') closeNoteModal();
  });

  document.getElementById('editNoteModal').addEventListener('click', (e) => {
    if(e.target.id === 'editNoteModal') closeEditNoteModal();
  });

  document.getElementById('questionModal').addEventListener('click', (e) => {
    if(e.target.id === 'questionModal') closeQuestionModal();
  });

  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape') {
      closeNoteModal();
      closeEditNoteModal();
      closeQuestionModal();
    }
  });
});

function initializePlayerTracking() {
  const video = document.getElementById('mainVideo');
  const audio = document.querySelector('.video-container audio');
  const youtubeFrame = document.getElementById('player');

  if (video || audio) {
    playback.kind = 'html5';
    playback.media = video || audio;

    playback.media.addEventListener('loadedmetadata', () => {
      playback.duration = Number(playback.media.duration) || 0;
      updateVideoTime(playback.media.currentTime || 0);
      sendTrackProgress(playback.media.currentTime || 0, playback.duration, true);
    });

    playback.media.addEventListener('timeupdate', () => {
      handlePlaybackTick(playback.media.currentTime || 0, Number(playback.media.duration) || 0);
    });

    // Let learners seek freely; progress tracking should never fight the native controls.
    playback.media.addEventListener('seeking', () => {
      playback.current = Math.max(0, Number(playback.media.currentTime) || 0);
    });

    playback.media.addEventListener('seeked', () => {
      handlePlaybackTick(playback.media.currentTime || 0, Number(playback.media.duration) || 0);
      sendTrackProgress(Math.max(playback.maxWatched, playback.current), playback.duration, true);
    });

    playback.media.addEventListener('ended', () => {
      handlePlaybackTick(playback.media.duration || playback.maxWatched, playback.media.duration || playback.duration);
      autoCompleteLesson();
    });

    updateVideoTime(0);
    return;
  }

  if (youtubeFrame) {
    playback.kind = 'youtube';
    loadYouTubeApi();
    return;
  }

  updateVideoTime(0);
}

const seekState = { forward: { time: 0, amount: 5 }, backward: { time: 0, amount: 5 } };
const PROGRESSIVE_RESET_MS = 1000;
const PROGRESSIVE_MAX = 40;

function seekPlaybackBy(baseDelta) {
  const media = playback.media;
  if (!media || !Number.isFinite(media.duration) || media.duration <= 0) return;

  const direction = baseDelta > 0 ? 'forward' : 'backward';
  const now = Date.now();
  const state = seekState[direction];

  let delta;
  if (now - state.time < PROGRESSIVE_RESET_MS) {
    delta = Math.min(Math.abs(state.amount) * 2, PROGRESSIVE_MAX);
  } else {
    delta = Math.abs(baseDelta);
  }
  delta = baseDelta > 0 ? delta : -delta;

  state.amount = Math.abs(delta);
  state.time = now;

  const next = Math.max(0, Math.min(media.duration - 0.1, media.currentTime + delta));
  try { media.currentTime = next; } catch (_) {}
  showSeekFeedback(direction, Math.abs(delta));
}

function showSeekFeedback(direction, amount) {
  const container = document.querySelector('.video-container');
  if (!container) return;
  const flash = document.createElement('div');
  flash.className = 'seek-flash ' + (direction === 'forward' ? 'seek-flash-right' : 'seek-flash-left');
  const icon = direction === 'forward' ? 'ri-forward-10-line' : 'ri-replay-10-line';
  flash.innerHTML = '<i class="' + icon + '"></i><span class="seek-amount">' + (direction === 'forward' ? '+' : '−') + amount + 'ث</span>';
  container.appendChild(flash);
  setTimeout(() => flash.remove(), 500);
}

function initializeSeekControls() {
  if (!playback.media) return;

  document.addEventListener('keydown', (e) => {
    const target = e.target;
    const isTyping = target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable);
    if (isTyping) return;

    if (e.key === 'ArrowRight') {
      e.preventDefault();
      seekPlaybackBy(5);
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      seekPlaybackBy(-5);
    }
  });

  const container = document.querySelector('.video-container');
  if (!container) return;

  let lastTapTime = 0;
  let lastTapSide = null;

  container.addEventListener('touchend', (e) => {
    const rect = container.getBoundingClientRect();
    const touch = e.changedTouches && e.changedTouches[0];
    if (!touch) return;

    const x = touch.clientX - rect.left;
    const side = x < rect.width / 2 ? 'left' : 'right';
    const now = Date.now();

    if (lastTapSide === side && (now - lastTapTime) < 350) {
      // Native <video>/<audio> controls render left-to-right regardless of page direction.
      seekPlaybackBy(side === 'right' ? 10 : -10);
      lastTapTime = 0;
      lastTapSide = null;
    } else {
      lastTapTime = now;
      lastTapSide = side;
    }
  });
}

function loadYouTubeApi() {
  if (window.YT && typeof window.YT.Player === 'function') {
    onYouTubeIframeAPIReady();
    return;
  }
  const existing = document.getElementById('youtubeIframeApiScript');
  if (!existing) {
    const tag = document.createElement('script');
    tag.id = 'youtubeIframeApiScript';
    tag.src = 'https://www.youtube.com/iframe_api';
    tag.referrerPolicy = 'strict-origin-when-cross-origin';
    document.head.appendChild(tag);
  }
}
function onYouTubeIframeAPIReady() {
  console.log('[YouTube] IFrame API ready');
  createYoutubePlayer();
}
function createYoutubePlayer() {
  if (playback.youtubePlayer || !(window.YT && window.YT.Player)) {
    return;
  }
  const youtubeFrame = document.getElementById('player');
  if (!youtubeFrame) {
    return;
  }
  const videoId = (youtubeFrame.dataset.videoId || '').trim();
  if (!videoId) {
    playback.youtubeBlocked = true;
    showYoutubeBlockedFallback(2);
    return;
  }
  clearYoutubeInitTimeout();
  playback.youtubeInitTimer = setTimeout(() => {
    onPlayerError({ data: 5, timeout: true });
  }, 10000);
  playback.youtubePlayer = new YT.Player('player', {
    videoId: videoId,
    playerVars: {
      rel: 0,
      modestbranding: 1,
      playsinline: 1,
      origin: window.location.origin,
      enablejsapi: 1,
      controls: 1
    },
    events: {
      onReady: onPlayerReady,
      onStateChange: function(event) {
        if (event.data === YT.PlayerState.PLAYING) {
          startYoutubeTracking();
        } else {
          stopYoutubeTracking();
        }
        if (event.data === YT.PlayerState.ENDED) {
          let current = playback.current;
          let duration = playback.duration;
          try {
            current = Number(playback.youtubePlayer.getCurrentTime()) || current;
            duration = Number(playback.youtubePlayer.getDuration()) || duration;
          } catch (e) {
            // no-op
          }
          handlePlaybackTick(Math.max(current, duration), duration);
          autoCompleteLesson();
        }
      },
      onError: onPlayerError
    }
  });
}
function onPlayerReady(event) {
  console.log('[YouTube] Player ready');
  clearYoutubeInitTimeout();
  hideYoutubeBlockedFallback();
  try {
    const iframe = event && event.target && typeof event.target.getIframe === 'function'
      ? event.target.getIframe()
      : null;
    if (iframe) {
      iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
    }
  } catch (e) {
    console.warn('[YouTube] Could not set referrer policy on iframe', e);
  }
  try {
    playback.duration = Number(playback.youtubePlayer.getDuration()) || 0;
    sendTrackProgress(playback.current, playback.duration, true);
  } catch (e) {
    console.warn('[YouTube] Player ready with no duration yet', e);
  }
}
function onPlayerError(event) {
  console.error('[YouTube] Player error', event);
  clearYoutubeInitTimeout();
  if (playback.youtubeErrorShown) return;
  playback.youtubeErrorShown = true;
  playback.youtubeBlocked = true;
  showYoutubeBlockedFallback(Number(event && event.data));
  showNotification('تعذر تشغيل فيديو YouTube داخل المنصة. جرّب فتحه مباشرة في YouTube.', 'error');
}
function clearYoutubeInitTimeout() {
  if (playback.youtubeInitTimer) {
    clearTimeout(playback.youtubeInitTimer);
    playback.youtubeInitTimer = null;
  }
}

function showYoutubeBlockedFallback(errorCode) {
  const overlay = document.getElementById('youtubeFallbackOverlay');
  const messageEl = document.getElementById('youtubeFallbackMessage');
  const linkEl = document.getElementById('youtubeFallbackLink');
  if (!overlay || !messageEl || !linkEl) {
    return;
  }

  const messages = {
    100: 'هذا الفيديو غير متاح (قد يكون محذوفًا أو خاصًا).',
    101: 'صاحب الفيديو منع تشغيله خارج YouTube (Embedding disabled).',
    150: 'هذا الفيديو مقيد حسب المنطقة أو العمر أو سياسات التضمين.',
    5: 'حدثت مشكلة في تشغيل الفيديو داخل المتصفح الحالي.',
    2: 'رابط الفيديو غير صالح أو معرف الفيديو غير صحيح.'
  };

  messageEl.textContent = messages[errorCode] || 'لا يمكن تشغيل هذا الفيديو داخل المنصة بسبب قيود من YouTube.';
  const watchUrl = getYoutubeWatchUrl();
  linkEl.href = watchUrl || 'https://www.youtube.com/';
  overlay.style.display = 'flex';
}

function hideYoutubeBlockedFallback() {
  const overlay = document.getElementById('youtubeFallbackOverlay');
  if (overlay) {
    overlay.style.display = 'none';
  }
}

function startYoutubeTracking() {
  stopYoutubeTracking();
  playback.youtubeTimer = setInterval(() => {
    if (!playback.youtubePlayer || typeof playback.youtubePlayer.getCurrentTime !== 'function') {
      return;
    }

    let current = 0;
    let duration = 0;
    try {
      current = Number(playback.youtubePlayer.getCurrentTime()) || 0;
      duration = Number(playback.youtubePlayer.getDuration()) || 0;
    } catch (e) {
      return;
    }

    handlePlaybackTick(current, duration);

  }, 1000);
}

function stopYoutubeTracking() {
  if (playback.youtubeTimer) {
    clearInterval(playback.youtubeTimer);
    playback.youtubeTimer = null;
  }
  clearYoutubeInitTimeout();
}

function updateVideoTime(seconds = 0) {
  const safe = Math.max(0, Number(seconds) || 0);
  const m = Math.floor(safe / 60);
  const s = Math.floor(safe % 60);
  const label = m + ':' + (s < 10 ? '0' : '') + s;
  const el = document.getElementById('viewTime');
  if (el) {
    el.textContent = label;
  }
}

function handlePlaybackTick(currentSeconds, durationSeconds) {
  const current = Math.max(0, Number(currentSeconds) || 0);
  const duration = Math.max(0, Number(durationSeconds) || 0);

  playback.current = current;
  playback.duration = duration || playback.duration;

  if (current >= playback.lastTime) {
    playback.maxWatched = Math.max(playback.maxWatched, current);
    localStorage.setItem('lesson_' + LESSON_ID + '_max_watched_seconds', String(playback.maxWatched));
  }

  playback.lastTime = current;
  updateVideoTime(current);

  if (REQUIRES_PLAYBACK_PROOF && playback.duration > 0) {
    const watchedPercent = Math.floor((playback.maxWatched / playback.duration) * 100);
    if (watchedPercent >= 90) {
      playback.canComplete = true;
    }
  } else {
    playback.canComplete = true;
  }

  sendTrackProgress(playback.maxWatched, playback.duration);
}

function sendTrackProgress(watchedSeconds, durationSeconds, force = false) {
  const now = Date.now();
  if (!force && now - playback.lastTrackAt < 3500) {
    return;
  }
  playback.lastTrackAt = now;

  fetch(PROGRESS_URL, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      action: 'track',
      watched_seconds: Number(watchedSeconds) || 0,
      duration_seconds: Number(durationSeconds) || 0
    })
  })
  .then(r => r.ok ? r.json() : null)
  .then(d => {
    if (!d || !d.success) {
      return;
    }
    if (typeof d.progress_percentage === 'number' && d.progress_percentage >= 90) {
      playback.canComplete = true;
    }
    if (d.can_complete === true) {
      playback.canComplete = true;
    }
  })
  .catch(() => {
    // network hiccup: ignore silently
  });
}

function showSeekWarning() {
  const now = Date.now();
  if (now - playback.lastSeekWarningAt < 2500) {
    return;
  }
  playback.lastSeekWarningAt = now;
  showNotification('يمكنك التحكم في موضع التشغيل بحرية.', 'info');
}

function markAsComplete() {
  if (playback.completeInFlight) {
    return;
  }

  if (playback.completed) {
    showNotification('تم تسجيل هذا الدرس كمكتمل مسبقًا.', 'info');
    return;
  }

  if (REQUIRES_PLAYBACK_PROOF && !playback.canComplete) {
    showNotification('أكمل مشاهدة 90% من المحتوى على الأقل قبل إكمال الدرس.', 'error');
    return;
  }

  submitCompletion(false);
}

function autoCompleteLesson() {
  if (playback.completed || playback.completeInFlight) {
    return;
  }

  if (REQUIRES_PLAYBACK_PROOF && playback.duration > 0) {
    const watchedPercent = Math.floor((Math.max(playback.maxWatched, playback.current) / playback.duration) * 100);
    if (watchedPercent < 90) {
      return;
    }
  }

  submitCompletion(true);
}

function submitCompletion(isAuto) {
  playback.completeInFlight = true;

  const watched = Math.max(playback.maxWatched, playback.current, 0);
  const duration = Math.max(playback.duration, 0);

  fetch(PROGRESS_URL, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      action: 'complete',
      watched_seconds: watched,
      duration_seconds: duration
    })
  })
  .then(async (response) => {
    const data = await response.json().catch(() => ({}));
    if (!response.ok || !data.success) {
      const msg = data.message || 'تعذر إكمال الدرس الآن.';
      throw new Error(msg);
    }
    return data;
  })
  .then(() => {
    playback.completed = true;
    playback.canComplete = true;
    showNotification(isAuto ? 'تم إكمال الدرس تلقائيًا بعد انتهاء المشاهدة.' : 'تم إكمال الدرس بنجاح.', 'success');
    setTimeout(() => location.reload(), 1000);
  })
  .catch((error) => {
    showNotification(error.message || 'تعذر إكمال الدرس.', 'error');
  })
  .finally(() => {
    playback.completeInFlight = false;
  });
}

function onVideoEnded() {
  autoCompleteLesson();
}

function addNote() {
  openNoteModal();
}

const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

async function loadUserNotes() {
  try {
    const res = await fetch(NOTES_URL, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    userNotes = (data.notes || []).map(n => ({
      id: n.id, text: n.text,
      time: new Date(n.created_at).toLocaleString('ar-SA'),
    }));
  } catch (_) {
    const saved = localStorage.getItem('lesson_' + LESSON_ID + '_notes');
    if (saved) try { userNotes = JSON.parse(saved); } catch (_) {}
  }
  displayNotes();
}

function displayNotes() {
  const list = document.getElementById('notesList');
  if(userNotes.length === 0) {
    list.innerHTML = '<div style="text-align: center; padding: 1rem; color: var(--light-gray); font-size: 0.85rem;"><i class="ri-message-line" style="font-size: 1.8rem; opacity: 0.4; margin-bottom: 0.5rem; display: block;"></i>لا توجد ملاحظات</div>';
  } else {
    list.innerHTML = userNotes.map((n, i) => `
      <div class="notes-item">
        <div class="notes-time">${n.time}</div>
        <div class="notes-text">${escapeHtml(n.text)}</div>
        <div style="display:flex;gap:0.4rem;margin-top:0.4rem;">
          <button class="notes-edit"  data-note-index="${i}" data-note-id="${n.id || ''}" style="font-size:0.75rem;padding:0.35rem 0.7rem;background:var(--gold,#c6a675);color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">تعديل</button>
          <button class="notes-delete" data-note-index="${i}" data-note-id="${n.id || ''}" style="font-size:0.75rem;padding:0.35rem 0.7rem;background:var(--danger,#c7272a);color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">حذف</button>
        </div>
      </div>
    `).join('');
  }
}

function displayQuestions() {
  const list = document.getElementById('questionsList');
  if (!list) return;
  if (userQuestions.length === 0) {
    list.innerHTML = '<div style="text-align: center; padding: 1rem; color: var(--light-gray); font-size: 0.85rem;"><i class="ri-question-line" style="font-size: 1.8rem; opacity: 0.4; margin-bottom: 0.5rem; display: block;"></i>لا توجد أسئلة</div>';
    return;
  }
  const statusLabel = { pending: 'قيد الانتظار', answered: 'تمت الإجابة', closed: 'مغلق' };
  const statusColor = { pending: '#c6a675', answered: '#34c759', closed: '#888' };
  list.innerHTML = userQuestions.map(q => {
    const st = q.status || 'pending';
    return `<div class="notes-item">
      <div class="notes-time">${q.time}</div>
      <div class="notes-text">${escapeHtml(q.text)}</div>
      <div style="margin-top:0.35rem;">
        <span style="font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;background:${statusColor[st] || '#888'}22;color:${statusColor[st] || '#888'};font-weight:600;">${statusLabel[st] || st}</span>
      </div>
    </div>`;
  }).join('');
}

function escapeHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function deleteNote(i) {
  const note = userNotes[i];
  if (note?.id) {
    try {
      await fetch(NOTES_URL + '/' + note.id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
    } catch (_) {}
  }
  userNotes.splice(i, 1);
  displayNotes();
}

let _editingNoteIndex = null;

function openEditNote(i) {
  _editingNoteIndex = i;
  document.getElementById('editNoteText').value = userNotes[i]?.text || '';
  document.getElementById('editNoteModal').classList.add('active');
  document.getElementById('editNoteText').focus();
}

function closeEditNoteModal() {
  document.getElementById('editNoteModal').classList.remove('active');
  document.getElementById('editNoteText').value = '';
  _editingNoteIndex = null;
}

async function saveEditNote() {
  const i    = _editingNoteIndex;
  const note = userNotes[i];
  const text = document.getElementById('editNoteText').value.trim();
  if (!text || text.length < 2) {
    showNotification('يرجى كتابة ملاحظة أوضح (حرفان على الأقل).', 'error');
    return;
  }
  if (note?.id) {
    try {
      const res = await fetch(NOTES_URL + '/' + note.id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ text }),
      });
      const data = await res.json();
      if (!res.ok || !data.success) throw new Error('');
    } catch (_) {}
  }
  userNotes[i].text = text;
  displayNotes();
  closeEditNoteModal();
  showNotification('تم تحديث الملاحظة بنجاح.', 'success');
}

function toggleBookmark() {
  isBookmarked = !isBookmarked;
  const icon = document.getElementById('bookmarkIcon');
  icon.className = isBookmarked ? 'ri-bookmark-fill' : 'ri-bookmark-line';
  icon.style.color = isBookmarked ? 'var(--gold)' : 'inherit';
  localStorage.setItem('lesson_' + LESSON_ID + '_bookmarked', isBookmarked);
}

function loadBookmarkStatus() {
  isBookmarked = localStorage.getItem('lesson_' + LESSON_ID + '_bookmarked') === 'true';
  if(isBookmarked) {
    document.getElementById('bookmarkIcon').className = 'ri-bookmark-fill';
    document.getElementById('bookmarkIcon').style.color = 'var(--gold)';
  }
}

function shareLesson() {
  navigator.clipboard?.writeText(window.location.href).catch(() => null);
  showNotification('تم نسخ رابط الدرس.', 'success');
}

function downloadResources() {
  showNotification('جاري تجهيز الموارد للتحميل...', 'info');
  window.location.href = DOWNLOAD_URL;
}

function askQuestion() {
  openQuestionModal();
}

function applyRatingUI(rating) {
  currentRating = rating;
  document.querySelectorAll('#starRating .star').forEach((s, i) => {
    if (i < rating) s.classList.add('active');
    else s.classList.remove('active');
  });
  const labels = ['ضعيف', 'مقبول', 'جيد', 'رائع', 'ممتاز'];
  const lbl = document.getElementById('ratingLabel');
  if (lbl) lbl.textContent = labels[rating - 1];
}

async function setRating(rating) {
  applyRatingUI(rating);
  try {
    const res = await fetch(RATING_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ rating }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error('');
    const avgEl = document.getElementById('lessonAverageRating');
    if (avgEl && data.average) avgEl.textContent = data.average;
    showNotification('تم تحديث تقييمك بنجاح.', 'success');
  } catch (_) {
    showNotification('تم حفظ التقييم محلياً.', 'info');
  }
}

async function loadSavedRating() {
  try {
    const res = await fetch(RATING_URL, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    if (data.my_rating) applyRatingUI(data.my_rating);
    const avgEl = document.getElementById('lessonAverageRating');
    if (avgEl && data.average) avgEl.textContent = data.average;
  } catch (_) {
    const saved = Number(localStorage.getItem('lesson_' + LESSON_ID + '_rating') || 0);
    if (saved >= 1 && saved <= 5) applyRatingUI(saved);
  }
}

function showNotification(msg, type = 'info') {
  const root = document.documentElement;
  const gv = (name) => getComputedStyle(root).getPropertyValue(name).trim();
  const colors = {
    success: gv('--theme-success') || '#06a77d',
    error: gv('--theme-danger') || '#D32F2F',
    info: gv('--theme-gold') || '#C6752E'
  };
  const div = document.createElement('div');
  div.style.cssText = `position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem; background: ${colors[type] || colors.info}; color: var(--theme-text); border-radius: 12px; z-index: 9999; font-weight: 600; animation: slideInRight 0.3s ease; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4); letter-spacing: 0.3px;`;
  div.textContent = msg;
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 3000);
}

// Theme toggling is handled globally by account-theme-unified.js

function openNoteModal() {
  document.getElementById('noteModal').classList.add('active');
  document.getElementById('noteText').focus();
}

function closeNoteModal() {
  document.getElementById('noteModal').classList.remove('active');
  document.getElementById('noteText').value = '';
}

async function submitNote() {
  const text = document.getElementById('noteText').value.trim();
  if (!text || text.length < 2) {
    showNotification('يرجى كتابة ملاحظة أوضح (حرفان على الأقل).', 'error');
    return;
  }
  try {
    const res = await fetch(NOTES_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ text }),
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.message || 'تعذر حفظ الملاحظة.');
    userNotes.unshift({
      id: data.note.id, text: data.note.text,
      time: new Date(data.note.created_at).toLocaleString('ar-SA'),
    });
    displayNotes();
    closeNoteModal();
    showNotification('تمت إضافة الملاحظة بنجاح.', 'success');
  } catch (e) {
    showNotification(e.message || 'تعذر إرسال الملاحظة الآن.', 'error');
  }
}

function openQuestionModal() {
  document.getElementById('questionModal').classList.add('active');
  document.getElementById('questionText').focus();
}

function closeQuestionModal() {
  document.getElementById('questionModal').classList.remove('active');
  document.getElementById('questionText').value = '';
}

function submitQuestion() {
  const question = document.getElementById('questionText').value.trim();
  if(!question) {
    showNotification('الرجاء كتابة السؤال.', 'error');
    return;
  }
  if (question.length < 10) {
    showNotification('يرجى كتابة سؤال أوضح (10 أحرف على الأقل).', 'error');
    return;
  }

  fetch(INQUIRY_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      lesson_id: LESSON_ID,
      inquiry_type: 'question',
      question_text: question
    })
  })
  .then(async (response) => {
    const data = await response.json().catch(() => ({}));
    if (!response.ok || !data.success) {
      const firstValidationError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
      throw new Error(firstValidationError || data.message || 'تعذر إرسال السؤال.');
    }
    return data;
  })
  .then(data => {
    closeQuestionModal();
    userQuestions.unshift({
      id: data.inquiry_id,
      text: question,
      time: new Date().toLocaleString('ar-SA'),
      status: 'pending',
    });
    displayQuestions();
    showNotification('تم إرسال سؤالك للمعلم بنجاح.', 'success');
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('حدث خطأ في الإرسال: ' + error.message, 'error');
  });
}

function getYoutubeWatchUrl() {
  const frame = document.getElementById('player');
  if (!frame) {
    return '';
  }
  const directWatchUrl = frame.dataset.watchUrl || '';
  if (directWatchUrl) {
    return directWatchUrl;
  }
  const id = frame.dataset.videoId || '';
  if (id) {
    return `https://www.youtube.com/watch?v=${id}`;
  }
  try {
    const sourceUrl = frame.dataset.originalUrl || frame.getAttribute('src') || '';
    const u = new URL(sourceUrl, window.location.origin);
    const fromQuery = u.searchParams.get('v') || u.searchParams.get('vi');
    const fromPath = (u.pathname.match(/\/(?:embed|shorts|live|v|e)\/([A-Za-z0-9_-]{11})/) || [null, null])[1];
    const fromShort = (sourceUrl.match(/youtu\.be\/([A-Za-z0-9_-]{11})/) || [null, null])[1];
    const fallbackId = fromQuery || fromPath || fromShort;
    return fallbackId ? `https://www.youtube.com/watch?v=${fallbackId}` : '';
  } catch (e) {
    return '';
  }
}
</script>
    @include('components.account-theme-foot')
</body>
</html>
<?php /**PATH C:\xampp\educational - Copy (2)\resources\views/student/lesson.blade.php ENDPATH**/ ?>














