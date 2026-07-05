<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إجلال — منصة التعليم الذكية</title>
  @include('components.account-theme-head')
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    /* ── Base ─────────────────────────────────────────── */
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body {
      font-family:'Tajawal',sans-serif;
      background:var(--theme-page-bg,#f6f3ee);
      color:var(--theme-text,#222);
      overflow-x:hidden;
      transition:background 0.3s,color 0.3s;
    }

    /* ── CSS Variables overlay for landing ───────────── */
    :root {
      --gold:#C4963A; --gold-dark:#A07828; --gold-soft:rgba(196,150,58,0.12);
      --nav-h:68px;
      --radius:14px;
      --shadow:0 8px 32px rgba(0,0,0,0.07);
      --shadow-lg:0 20px 60px rgba(0,0,0,0.12);
      --transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── NAVBAR ─────────────────────────────────────── */
    .nav {
      position:fixed; inset:0 0 auto 0; height:var(--nav-h); z-index:100;
      display:flex; align-items:center; justify-content:space-between;
      padding:0 2rem;
      background:rgba(var(--theme-page-bg-rgb,246,243,238),0.85);
      backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px);
      border-bottom:1px solid var(--theme-border,rgba(0,0,0,0.06));
      transition:var(--transition);
    }
    html[data-theme="dark"] .nav { background:rgba(5,5,5,0.88); }
    .nav.scrolled { box-shadow:0 4px 20px rgba(0,0,0,0.1); }

    .nav-logo {
      font-size:1.5rem; font-weight:900; text-decoration:none; display:flex;
      align-items:center; gap:0.5rem;
      background:linear-gradient(135deg,var(--gold),var(--gold-dark));
      -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }
    .nav-logo i { -webkit-text-fill-color:var(--gold); }

    .nav-links {
      display:flex; gap:2rem; list-style:none; align-items:center;
    }
    .nav-links a {
      color:var(--theme-text,#333); text-decoration:none; font-weight:600;
      font-size:0.9rem; transition:color 0.2s; position:relative;
    }
    .nav-links a::after {
      content:''; position:absolute; bottom:-4px; left:0; width:0; height:2px;
      background:var(--gold); border-radius:2px; transition:width 0.25s;
    }
    .nav-links a:hover { color:var(--gold); }
    .nav-links a:hover::after { width:100%; }

    .nav-actions { display:flex; gap:0.75rem; align-items:center; }

    .theme-btn {
      width:38px; height:38px; border-radius:50%; border:none; cursor:pointer;
      background:var(--gold-soft); color:var(--gold); font-size:1rem;
      display:flex; align-items:center; justify-content:center;
      transition:var(--transition);
    }
    .theme-btn:hover { background:var(--gold); color:#fff; }

    .btn-outline {
      padding:0.5rem 1.2rem; border-radius:8px; font-weight:700;
      font-family:'Tajawal',sans-serif; font-size:0.88rem; cursor:pointer;
      text-decoration:none; transition:var(--transition);
      border:2px solid var(--gold); color:var(--gold); background:transparent;
    }
    .btn-outline:hover { background:var(--gold); color:#fff; transform:translateY(-2px); }

    .btn-solid {
      padding:0.5rem 1.3rem; border-radius:8px; font-weight:700;
      font-family:'Tajawal',sans-serif; font-size:0.88rem; cursor:pointer;
      text-decoration:none; transition:var(--transition); border:none;
      background:linear-gradient(135deg,var(--gold),var(--gold-dark)); color:#fff;
    }
    .btn-solid:hover { transform:translateY(-3px); box-shadow:0 10px 28px rgba(196,150,58,0.4); }

    /* Hamburger */
    .hamburger {
      display:none; flex-direction:column; gap:5px; cursor:pointer;
      background:none; border:none; padding:6px;
    }
    .hamburger span { width:22px; height:2px; background:var(--theme-text,#333); border-radius:2px; transition:var(--transition); }
    .hamburger.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity:0; }
    .hamburger.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }

    /* Mobile drawer */
    .mobile-menu {
      display:none; position:fixed; inset:var(--nav-h) 0 0 0; z-index:99;
      background:var(--theme-surface,#fff); flex-direction:column;
      padding:2rem 1.5rem; gap:1.5rem; overflow-y:auto;
      border-top:1px solid var(--theme-border,rgba(0,0,0,0.08));
    }
    .mobile-menu.open { display:flex; }
    .mobile-menu a { font-size:1.1rem; font-weight:700; color:var(--theme-text,#333); text-decoration:none; padding:0.75rem 0; border-bottom:1px solid var(--theme-border,rgba(0,0,0,0.06)); }
    .mobile-menu a:hover { color:var(--gold); }
    .mobile-menu .mobile-actions { display:flex; gap:0.75rem; padding-top:0.5rem; }

    /* ── HERO ─────────────────────────────────────────── */
    .hero {
      min-height:100vh; padding-top:var(--nav-h);
      display:flex; align-items:center;
      background:linear-gradient(160deg, var(--theme-page-bg,#f6f3ee) 55%, rgba(196,150,58,0.06) 100%);
      position:relative; overflow:hidden;
    }
    .hero-orb {
      position:absolute; border-radius:50%; pointer-events:none; animation:drift 8s ease-in-out infinite;
    }
    .hero-orb-1 { width:500px; height:500px; top:-120px; left:-120px; background:radial-gradient(circle,rgba(196,150,58,0.12) 0%,transparent 70%); }
    .hero-orb-2 { width:380px; height:380px; bottom:-80px; right:-80px; background:radial-gradient(circle,rgba(6,51,14,0.08) 0%,transparent 70%); animation-delay:-4s; }
    @keyframes drift { 0%,100%{transform:translate(0,0);} 50%{transform:translate(20px,30px);} }

    .hero-inner {
      max-width:1200px; margin:0 auto; padding:3rem 2rem;
      display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:center;
      position:relative; z-index:2;
    }

    .hero-badge {
      display:inline-flex; align-items:center; gap:0.4rem;
      background:var(--gold-soft); color:var(--gold); border-radius:20px;
      padding:0.35rem 0.9rem; font-size:0.8rem; font-weight:700;
      margin-bottom:1.2rem;
    }

    .hero-title {
      font-size:clamp(2rem,4.5vw,3.2rem); font-weight:900; line-height:1.25;
      margin-bottom:1.2rem; color:var(--theme-text,#1a1a1a);
    }
    .hero-title .accent {
      background:linear-gradient(135deg,var(--gold),var(--gold-dark));
      -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }

    .hero-desc {
      font-size:1.05rem; color:var(--theme-text-muted,#555);
      line-height:1.8; margin-bottom:2rem; opacity:0.85;
    }

    .hero-cta { display:flex; gap:1rem; flex-wrap:wrap; }
    .btn-lg {
      padding:0.85rem 2rem; border-radius:10px; font-weight:800;
      font-family:'Tajawal',sans-serif; font-size:1rem; cursor:pointer;
      text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;
      transition:var(--transition); border:none;
    }
    .btn-lg-primary {
      background:linear-gradient(135deg,var(--gold),var(--gold-dark)); color:#fff;
    }
    .btn-lg-primary:hover { transform:translateY(-4px); box-shadow:0 14px 35px rgba(196,150,58,0.4); }
    .btn-lg-ghost {
      background:var(--theme-surface,#fff); color:var(--gold);
      border:2px solid var(--gold);
    }
    .btn-lg-ghost:hover { background:var(--gold); color:#fff; transform:translateY(-4px); }

    /* Hero visual — floating feature cards */
    .hero-visual { position:relative; height:420px; }
    .float-cards {
      position:relative; width:100%; height:100%;
      display:flex; align-items:center; justify-content:center;
    }
    .fc { /* floating card */
      position:absolute;
      background:var(--theme-surface,#fff);
      border:1px solid var(--theme-border,rgba(196,150,58,0.2));
      border-radius:16px; padding:1.1rem 1.3rem;
      box-shadow:var(--shadow); backdrop-filter:blur(8px);
      display:flex; align-items:center; gap:0.75rem;
      font-size:0.85rem; font-weight:700; color:var(--theme-text,#222);
      white-space:nowrap; animation:floatCard 5s ease-in-out infinite;
    }
    .fc .fc-icon {
      width:38px; height:38px; border-radius:10px;
      display:flex; align-items:center; justify-content:center;
      font-size:1.1rem; background:var(--gold-soft); color:var(--gold); flex-shrink:0;
    }
    .fc-center { top:50%; left:50%; transform:translate(-50%,-50%); font-size:1rem; padding:1.4rem 1.8rem; z-index:2; animation-delay:0s; }
    .fc-tl { top:8%; left:5%; animation-delay:-1s; animation-duration:6s; }
    .fc-tr { top:10%; right:0%; animation-delay:-2s; animation-duration:7s; }
    .fc-bl { bottom:12%; left:0%; animation-delay:-3s; animation-duration:5.5s; }
    .fc-br { bottom:8%; right:5%; animation-delay:-0.5s; animation-duration:6.5s; }
    @keyframes floatCard {
      0%,100%{transform:translateY(0);} 50%{transform:translateY(-12px);}
    }
    .fc-center { animation:floatCard 5s ease-in-out infinite, none; }
    .fc-tl,.fc-tr,.fc-bl,.fc-br { animation:floatCard 6s ease-in-out infinite; }
    .fc-tl { animation-delay:-1s; }
    .fc-tr { animation-delay:-2.5s; }
    .fc-bl { animation-delay:-4s; }
    .fc-br { animation-delay:-0.8s; }

    .fc-ring {
      position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
      width:280px; height:280px; border-radius:50%;
      border:2px dashed rgba(196,150,58,0.2);
      animation:spin 30s linear infinite;
    }
    .fc-ring-2 {
      width:200px; height:200px;
      border-color:rgba(196,150,58,0.12);
      animation-direction:reverse; animation-duration:20s;
    }
    @keyframes spin { to{transform:translate(-50%,-50%) rotate(360deg);} }

    /* ── FEATURES ─────────────────────────────────────── */
    .section { padding:5rem 2rem; }
    .section-inner { max-width:1200px; margin:0 auto; }

    .section-header { text-align:center; margin-bottom:3.5rem; }
    .section-header h2 {
      font-size:clamp(1.7rem,3.5vw,2.4rem); font-weight:900;
      color:var(--theme-text,#1a1a1a); margin-bottom:0.75rem;
    }
    .section-header h2 .accent {
      background:linear-gradient(135deg,var(--gold),var(--gold-dark));
      -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }
    .section-header p { font-size:1rem; color:var(--theme-text,#666); opacity:0.7; max-width:520px; margin:0 auto; }

    .features-bg { background:var(--theme-surface,#fff); }

    .features-grid {
      display:grid; grid-template-columns:repeat(auto-fit,minmax(270px,1fr)); gap:1.5rem;
    }
    .feat-card {
      background:var(--theme-page-bg,#f6f3ee);
      border:1px solid var(--theme-border,rgba(0,0,0,0.05));
      border-radius:var(--radius); padding:2rem 1.5rem;
      transition:var(--transition); position:relative; overflow:hidden;
    }
    .feat-card::after {
      content:''; position:absolute; top:0; right:0; left:0; height:3px;
      background:linear-gradient(90deg,var(--gold),var(--gold-dark));
      transform:scaleX(0); transform-origin:right; transition:transform 0.3s;
    }
    .feat-card:hover { transform:translateY(-8px); box-shadow:var(--shadow-lg); }
    .feat-card:hover::after { transform:scaleX(1); }
    .feat-icon {
      width:56px; height:56px; border-radius:14px;
      background:var(--gold-soft); color:var(--gold);
      display:flex; align-items:center; justify-content:center;
      font-size:1.5rem; margin-bottom:1.2rem;
    }
    .feat-card h3 { font-size:1.05rem; font-weight:800; margin-bottom:0.6rem; color:var(--theme-text,#1a1a1a); }
    .feat-card p  { font-size:0.88rem; color:var(--theme-text,#666); line-height:1.7; opacity:0.75; }

    /* ── STATS ────────────────────────────────────────── */
    .stats-section {
      background:linear-gradient(135deg,var(--gold),var(--gold-dark));
      padding:4.5rem 2rem;
    }
    .stats-grid {
      max-width:900px; margin:0 auto;
      display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:2rem;
      text-align:center;
    }
    .stat-num {
      font-size:2.8rem; font-weight:900; color:#fff; line-height:1;
      margin-bottom:0.4rem; font-variant-numeric:tabular-nums;
    }
    .stat-lbl { color:rgba(255,255,255,0.9); font-size:0.95rem; font-weight:600; }

    /* ── HOW IT WORKS ─────────────────────────────────── */
    .steps-grid {
      display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
      gap:2rem; counter-reset:steps;
    }
    .step-card {
      text-align:center; padding:2rem 1.2rem;
      background:var(--theme-surface,#fff);
      border-radius:var(--radius);
      border:1px solid var(--theme-border,rgba(0,0,0,0.05));
      transition:var(--transition); counter-increment:steps;
      position:relative;
    }
    .step-card:hover { box-shadow:var(--shadow-lg); transform:translateY(-6px); }
    .step-num {
      width:48px; height:48px; border-radius:50%;
      background:linear-gradient(135deg,var(--gold),var(--gold-dark));
      color:#fff; font-size:1.2rem; font-weight:900;
      display:flex; align-items:center; justify-content:center;
      margin:0 auto 1.2rem;
    }
    .step-card h3 { font-size:1rem; font-weight:800; margin-bottom:0.5rem; color:var(--theme-text,#1a1a1a); }
    .step-card p  { font-size:0.85rem; color:var(--theme-text,#666); opacity:0.75; line-height:1.6; }

    /* ── CTA ──────────────────────────────────────────── */
    .cta-wrap { padding:4rem 2rem; }
    .cta-box {
      max-width:860px; margin:0 auto;
      background:linear-gradient(135deg,var(--gold),var(--gold-dark));
      border-radius:20px; padding:3.5rem 2rem; text-align:center;
      box-shadow:0 24px 64px rgba(196,150,58,0.35);
      position:relative; overflow:hidden;
    }
    .cta-box::before {
      content:''; position:absolute;
      width:300px; height:300px; border-radius:50%;
      background:rgba(255,255,255,0.06); top:-80px; left:-60px; pointer-events:none;
    }
    .cta-box h2 { font-size:clamp(1.6rem,4vw,2.2rem); color:#fff; font-weight:900; margin-bottom:0.8rem; }
    .cta-box p  { color:rgba(255,255,255,0.9); font-size:1rem; margin-bottom:1.8rem; max-width:480px; margin-left:auto; margin-right:auto; }
    .btn-cta {
      display:inline-flex; align-items:center; gap:0.5rem;
      padding:0.9rem 2.2rem; border-radius:10px; font-weight:800;
      font-family:'Tajawal',sans-serif; font-size:1rem; cursor:pointer;
      text-decoration:none; border:none; transition:var(--transition);
      background:#fff; color:var(--gold);
    }
    .btn-cta:hover { transform:translateY(-3px); box-shadow:0 12px 30px rgba(0,0,0,0.2); }

    /* ── FOOTER ──────────────────────────────────────── */
    .footer {
      padding:2.5rem 2rem; text-align:center;
      border-top:1px solid var(--theme-border,rgba(196,150,58,0.15));
      background:var(--theme-surface,#fff);
    }
    .footer-links {
      display:flex; gap:2rem; justify-content:center; flex-wrap:wrap;
      margin-bottom:1rem;
    }
    .footer-links a {
      color:var(--gold); text-decoration:none; font-size:0.88rem; font-weight:600;
      transition:opacity 0.2s;
    }
    .footer-links a:hover { opacity:0.7; }
    .footer-copy { font-size:0.82rem; color:var(--theme-text,#999); opacity:0.6; }

    /* ── RESPONSIVE ──────────────────────────────────── */
    @media (max-width:900px) {
      .hero-inner { grid-template-columns:1fr; gap:2rem; text-align:center; padding:2rem 1.5rem; }
      .hero-cta { justify-content:center; }
      .hero-visual { height:280px; }
      .fc-tl,.fc-tr,.fc-bl,.fc-br { display:none; }
      .nav-links { display:none; }
      .hamburger { display:flex; }
      .nav-actions .btn-outline,.nav-actions .btn-solid { display:none; }
    }
    @media (max-width:600px) {
      .hero-visual { height:200px; }
      .fc-center { padding:1rem 1.2rem; font-size:0.88rem; }
      .section { padding:3.5rem 1.2rem; }
      .stats-section { padding:3rem 1.2rem; }
    }
  </style>
</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────────────── -->
<nav class="nav" id="nav">
  <a href="{{ route('home') }}" class="nav-logo">
    <i class="ri-graduation-cap-fill"></i> إجلال
  </a>

  <ul class="nav-links">
    <li><a href="#features">المميزات</a></li>
    <li><a href="#how">كيف يعمل</a></li>
    <li><a href="#stats">الإحصائيات</a></li>
    <li><a href="{{ route('features-guide') }}">دليل الميزات</a></li>
  </ul>

  <div class="nav-actions">
    <button class="theme-btn" id="themeToggle" title="تبديل المظهر" aria-label="تبديل المظهر">
      <i class="ri-sun-line" id="themeIcon"></i>
    </button>
    <a href="{{ route('login') }}"    class="btn-outline">دخول</a>
    <a href="{{ route('register') }}" class="btn-solid">تسجيل جديد</a>
    <button class="hamburger" id="hamburger" aria-label="القائمة">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
  <a href="#features"              onclick="closeMenu()">المميزات</a>
  <a href="#how"                   onclick="closeMenu()">كيف يعمل</a>
  <a href="#stats"                 onclick="closeMenu()">الإحصائيات</a>
  <a href="{{ route('features-guide') }}" onclick="closeMenu()">دليل الميزات</a>
  <div class="mobile-actions">
    <a href="{{ route('login') }}"    class="btn-outline" style="flex:1;text-align:center;">دخول</a>
    <a href="{{ route('register') }}" class="btn-solid"   style="flex:1;text-align:center;">تسجيل جديد</a>
  </div>
</div>

<!-- ── HERO ─────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-orb hero-orb-1"></div>
  <div class="hero-orb hero-orb-2"></div>

  <div class="hero-inner">
    <!-- Text -->
    <div class="hero-text">
      <div class="hero-badge"><i class="ri-sparkle-fill"></i> منصة التعليم الذكية</div>
      <h1 class="hero-title">
        تعلّم بطريقة <span class="accent">ذكية</span><br>
        وطوّر <span class="accent">مهاراتك</span>
      </h1>
      <p class="hero-desc">
        منصة إجلال تجمع بين المحتوى التعليمي الاحترافي وأدوات التواصل المتقدمة،
        لتجربة تعليمية شاملة تصنع الفارق.
      </p>
      <div class="hero-cta">
        <a href="{{ route('register') }}" class="btn-lg btn-lg-primary">
          <i class="ri-rocket-line"></i> ابدأ الآن مجاناً
        </a>
        <a href="#features" class="btn-lg btn-lg-ghost">
          <i class="ri-arrow-down-line"></i> استكشف المزايا
        </a>
      </div>
    </div>

    <!-- Visual -->
    <div class="hero-visual">
      <div class="float-cards">
        <div class="fc-ring"></div>
        <div class="fc-ring fc-ring-2"></div>

        <div class="fc fc-center">
          <div class="fc-icon"><i class="ri-graduation-cap-fill"></i></div>
          <div>
            <div style="font-size:1rem;font-weight:900;">إجلال</div>
            <div style="font-size:0.75rem;opacity:0.6;font-weight:500;">منصة التعليم الذكية</div>
          </div>
        </div>

        <div class="fc fc-tl">
          <div class="fc-icon"><i class="ri-video-line"></i></div>
          دروس فيديو
        </div>

        <div class="fc fc-tr">
          <div class="fc-icon"><i class="ri-file-list-3-line"></i></div>
          اختبارات ذكية
        </div>

        <div class="fc fc-bl">
          <div class="fc-icon"><i class="ri-award-line"></i></div>
          شهادات معتمدة
        </div>

        <div class="fc fc-br">
          <div class="fc-icon"><i class="ri-bar-chart-2-line"></i></div>
          تتبع التقدم
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── FEATURES ──────────────────────────────────────────── -->
<section class="section features-bg" id="features">
  <div class="section-inner">
    <div class="section-header">
      <h2>لماذا <span class="accent">إجلال</span>؟</h2>
      <p>أدوات متكاملة صُممت لتجعل التعليم أكثر فاعلية ومتعة</p>
    </div>
    <div class="features-grid">
      <div class="feat-card">
        <div class="feat-icon"><i class="ri-play-circle-line"></i></div>
        <h3>محتوى تفاعلي</h3>
        <p>دروس فيديو ومقاطع صوتية عالية الجودة مع ملاحظات وتقييمات فورية</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon"><i class="ri-file-list-3-line"></i></div>
        <h3>اختبارات ذكية</h3>
        <p>نظام اختبار متقدم مع تحليل تفصيلي للإجابات وتصحيح فوري</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon"><i class="ri-bar-chart-line"></i></div>
        <h3>تتبع التقدم</h3>
        <p>إحصائيات مفصّلة وتقارير شاملة ترسم مسار تطورك يوماً بيوم</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon"><i class="ri-message-3-line"></i></div>
        <h3>تواصل مباشر</h3>
        <p>مراسلة فورية مع المعلمين والطلاب، مكالمات صوتية ومرئية داخل المنصة</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon"><i class="ri-award-line"></i></div>
        <h3>شهادات معتمدة</h3>
        <p>شهادات إتمام احترافية قابلة للتحقق تُضاف إلى سجلك التعليمي</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon"><i class="ri-smartphone-line"></i></div>
        <h3>تعلّم في أي مكان</h3>
        <p>منصة متجاوبة تعمل بكامل مزاياها على الجوال والتابلت والحاسوب</p>
      </div>
    </div>
  </div>
</section>

<!-- ── HOW IT WORKS ──────────────────────────────────────── -->
<section class="section" id="how">
  <div class="section-inner">
    <div class="section-header">
      <h2>كيف <span class="accent">يعمل</span>؟</h2>
      <p>ثلاث خطوات تفصلك عن بدء رحلتك التعليمية</p>
    </div>
    <div class="steps-grid">
      <div class="step-card">
        <div class="step-num">١</div>
        <h3>سجّل حسابك</h3>
        <p>أنشئ حسابك مجاناً في دقيقة واحدة واختر دورك كطالب أو معلم</p>
      </div>
      <div class="step-card">
        <div class="step-num">٢</div>
        <h3>التحق بالدورة</h3>
        <p>تصفح الدورات المتاحة والتحق بما يناسب مستواك وأهدافك</p>
      </div>
      <div class="step-card">
        <div class="step-num">٣</div>
        <h3>تعلّم واحتفل</h3>
        <p>شاهد الدروس، أجب على الاختبارات، وانل شهادة إتمامك بكل فخر</p>
      </div>
    </div>
  </div>
</section>

<!-- ── STATS ─────────────────────────────────────────────── -->
<section class="stats-section" id="stats">
  <div class="stats-grid">
    <div>
      <div class="stat-num" data-target="1000">0</div>
      <div class="stat-lbl">طالب نشط</div>
    </div>
    <div>
      <div class="stat-num" data-target="500">0</div>
      <div class="stat-lbl">محتوى تعليمي</div>
    </div>
    <div>
      <div class="stat-num" data-target="50">0</div>
      <div class="stat-lbl">معلم متخصص</div>
    </div>
    <div>
      <div class="stat-num" data-target="95">0</div>
      <div class="stat-lbl">% رضا المستخدمين</div>
    </div>
  </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────── -->
<div class="cta-wrap">
  <div class="cta-box">
    <h2>جاهز لبدء رحلتك التعليمية؟</h2>
    <p>انضم إلى آلاف الطلاب الذين يطوّرون مهاراتهم مع إجلال اليوم</p>
    <a href="{{ route('register') }}" class="btn-cta">
      <i class="ri-user-add-line"></i> سجّل الآن مجاناً
    </a>
  </div>
</div>

<!-- ── FOOTER ─────────────────────────────────────────────── -->
<footer class="footer">
  <div class="footer-links">
    <a href="#features">المميزات</a>
    <a href="{{ route('features-guide') }}">دليل الميزات</a>
    <a href="{{ route('login') }}">تسجيل الدخول</a>
    <a href="{{ route('register') }}">حساب جديد</a>
  </div>
  <p class="footer-copy">&copy; {{ date('Y') }} إجلال — جميع الحقوق محفوظة</p>
</footer>

<script>
/* ── Theme Toggle ─────────────────────────────── */
const themeToggle = document.getElementById('themeToggle');
const themeIcon   = document.getElementById('themeIcon');

function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  document.documentElement.classList.toggle('dark-mode', theme === 'dark');
  themeIcon.className = theme === 'dark' ? 'ri-moon-line' : 'ri-sun-line';
  try {
    localStorage.setItem('app-theme', theme);
    localStorage.setItem('theme', theme);
    sessionStorage.setItem('app-theme', theme);
  } catch(_) {}
}

// Read current theme (set by account-theme-head inline script)
applyTheme(document.documentElement.getAttribute('data-theme') || 'light');

themeToggle.addEventListener('click', () => {
  const current = document.documentElement.getAttribute('data-theme') || 'light';
  applyTheme(current === 'dark' ? 'light' : 'dark');
});

/* ── Hamburger ────────────────────────────────── */
const hamburger  = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu.classList.toggle('open');
});

function closeMenu() {
  hamburger.classList.remove('open');
  mobileMenu.classList.remove('open');
}

/* ── Navbar scroll ────────────────────────────── */
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 40);
}, { passive:true });

/* ── Counter animation ────────────────────────── */
function animateCounters() {
  document.querySelectorAll('.stat-num[data-target]').forEach(el => {
    const target = parseInt(el.dataset.target, 10);
    const suffix = el.dataset.suffix || (target === 95 ? '%' : '+');
    const duration = 1800;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = Math.floor(current).toLocaleString('ar-SA') + suffix;
      if (current >= target) clearInterval(timer);
    }, 16);
  });
}

const statsSection = document.querySelector('.stats-section');
const observer = new IntersectionObserver(entries => {
  if (entries[0].isIntersecting) {
    animateCounters();
    observer.disconnect();
  }
}, { threshold: 0.3 });
if (statsSection) observer.observe(statsSection);

/* ── Smooth scroll for anchor links ──────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      closeMenu();
      target.scrollIntoView({ behavior:'smooth', block:'start' });
    }
  });
});
</script>

@include('components.account-theme-foot')
</body>
</html>
