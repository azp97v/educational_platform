<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إجلال - منصة التعليم الذكية | تعلم بدون حدود</title>
    @include('components.account-theme-head')
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Josefin+Slab:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #f6f3ee 0%, #f0ebe2 100%);
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== Navigation ===== */
        .navbar {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .logo {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
            list-style: none;
        }

        .nav-menu li a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-menu li a:hover {
            color: var(--gold);
        }

        .nav-menu li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            transition: width 0.3s ease;
        }

        .nav-menu li a:hover::after {
            width: 100%;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .dark-mode-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #666;
            transition: all 0.3s ease;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }

        .dark-mode-btn:hover {
            background: rgba(196, 150, 58, 0.1);
            color: var(--gold);
        }

        body.dark-mode .dark-mode-btn {
            color: #f6da09;
        }

        body.dark-mode .dark-mode-btn:hover {
            background: rgba(246, 218, 9, 0.15);
        }

        .btn-login, .btn-signup {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
        }

        .btn-login {
            color: var(--gold);
            border: 2px solid var(--gold);
            background: transparent;
        }

        .btn-login:hover {
            background: var(--gold);
            color: white;
            transform: translateY(-2px);
        }

        .btn-signup {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: white;
        }

        .btn-signup:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(196, 150, 58, 0.4);
        }

        /* ===== Hero Section ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f6f3ee 0%, #f0ebe2 100%);
            position: relative;
            overflow: hidden;
            padding: 100px 20px 60px;
            margin-top: 60px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(196, 150, 58, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 51, 14, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }

        .hero-container {
            max-width: 900px;
            z-index: 2;
            text-align: center;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .hero-content {
            text-align: right;
        }

        .hero-content h1 {
            font-family: 'Josefin Slab', serif;
            font-size: clamp(2rem, 8vw, 3.2rem);
            font-weight: 700;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #06330e 0%, #0a4a15 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
        }

        .hero-content .highlight {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 15px 35px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: white;
        }

        .btn-hero-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(196, 150, 58, 0.35);
        }

        .btn-hero-secondary {
            background: white;
            color: var(--gold);
            border: 2px solid var(--gold);
        }

        .btn-hero-secondary:hover {
            background: var(--gold);
            color: white;
            transform: translateY(-5px);
        }

        .hero-image {
            position: relative;
            height: 400px;
        }

        .hero-image-box {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(196, 150, 58, 0.1) 0%, rgba(10, 74, 21, 0.1) 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border: 2px solid rgba(196, 150, 58, 0.2);
            overflow: hidden;
        }

        .hero-image-box::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(196, 150, 58, 0.1) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%); }
            100% { transform: translateX(100%) translateY(100%); }
        }

        .hero-icon {
            font-size: 80px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.1));
        }

        /* ===== Features Section ===== */
        .features {
            padding: 80px 40px;
            background: white;
            margin-top: 60px;
            position: relative;
            z-index: 10;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-family: 'Josefin Slab', serif;
            font-size: 2.5rem;
            color: #06330e;
            margin-bottom: 15px;
        }

        .section-title .highlight {
            color: var(--gold);
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: linear-gradient(135deg, #f6f3ee 0%, #ffffff 100%);
            border: 2px solid rgba(196, 150, 58, 0.15);
            border-radius: 12px;
            padding: 35px 25px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(196, 150, 58, 0.35);
            box-shadow: 0 20px 40px rgba(196, 150, 58, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            font-size: 50px;
            color: var(--gold);
            margin-bottom: 20px;
            display: inline-block;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(196, 150, 58, 0.1) 0%, rgba(196, 150, 58, 0.05) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-card h3 {
            font-family: 'Josefin Slab', serif;
            font-size: 1.4rem;
            color: #06330e;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* ===== Stats Section ===== */
        .stats {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: white;
            padding: 80px 40px;
            margin-top: 40px;
        }

        .stats-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            color: #f6f3ee;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .stat-item p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.1rem;
        }

        /* ===== CTA Section ===== */
        .cta {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            padding: 80px 40px;
            text-align: center;
            margin: 40px 20px;
            border-radius: 15px;
            box-shadow: 0 20px 50px rgba(196, 150, 58, 0.3);
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 80px;
            margin-bottom: 80px;
        }

        .cta h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 20px;
            font-family: 'Josefin Slab', serif;
        }

        .cta p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta .btn-hero {
            background: white;
            color: var(--gold);
            padding: 15px 40px;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .cta .btn-hero:hover {
            background: #f6f3ee;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        /* ===== Footer ===== */
        .footer {
            background: transparent;
            color: #666;
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(196, 150, 58, 0.2);
            margin-top: 60px;
        }

        .footer p {
            margin-bottom: 10px;
        }

        .footer-links {
            display: flex;
            gap: 30px;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--gold);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #06330e;
        }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .hero-container {
                gap: 40px;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .nav-menu {
                display: none;
            }

            .hero-container {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 20px;
                text-align: center;
            }

            .hero-content {
                text-align: center;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }

            .hero-image {
                height: 260px;
            }

            .features {
                padding: 60px 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .cta {
                margin: 20px;
                padding: 40px 20px;
            }

            .cta h2 {
                font-size: 1.8rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }
        }

        @media (max-width: 480px) {
            .hero-title { font-size: 2rem; }
            .hero-subtitle { font-size: 1rem; }
            .section-title h2 { font-size: 1.5rem; }
            .navbar { padding: 12px 16px; }
            .cta h2 { font-size: 1.5rem; }
        }

        /* ===== Dark Mode Support ===== */
        body.dark-mode .navbar {
            background: rgba(45, 45, 45, 0.95);
        }

        body.dark-mode .nav-menu li a {
            color: #f0f0f0;
        }

        body.dark-mode .btn-login {
            color: #f6da09;
            border-color: #f6da09;
        }

        body.dark-mode .btn-login:hover {
            background: #f6da09;
            color: #06330e;
        }

        body.dark-mode .btn-signup {
            background: linear-gradient(135deg, #f6da09 0%, #e5caa0 100%);
            color: #06330e;
        }

        body.dark-mode .btn-signup:hover {
            box-shadow: 0 8px 20px rgba(246, 218, 9, 0.5);
        }

        body.dark-mode {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #f0f0f0;
        }

        body.dark-mode .hero {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        body.dark-mode .hero-content h1 {
            background: linear-gradient(135deg, #f6da09 0%, #e5caa0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        body.dark-mode .hero-content p {
            color: #bbb;
        }

        body.dark-mode .features {
            background: #2d2d2d;
        }

        body.dark-mode .feature-card {
            background: linear-gradient(135deg, #333 0%, #3a3a3a 100%);
            border-color: rgba(196, 150, 58, 0.25);
        }

        body.dark-mode .feature-card h3,
        body.dark-mode .section-title h2 {
            color: #f6da09;
        }

        body.dark-mode .feature-card p,
        body.dark-mode .section-title p {
            color: #bbb;
        }

        body.dark-mode .section-title .highlight {
            color: #f6da09;
        }

        body.dark-mode .footer {
            border-top-color: rgba(196, 150, 58, 0.3);
            color: #999;
        }

        body.dark-mode .footer-links a {
            color: #f6da09;
        }

        body.dark-mode .footer-links a:hover {
            color: #e5caa0;
        }

        body.dark-mode .cta {
            background: linear-gradient(135deg, #8B6F47 0%, #6B5632 100%);
        }

        body.dark-mode .cta h2 {
            color: #f6f3ee;
        }

        body.dark-mode .cta p {
            color: rgba(255, 255, 255, 0.9);
        }

        body.dark-mode .cta .btn-hero {
            background: var(--gold);
            color: white;
        }

        body.dark-mode .cta .btn-hero:hover {
            background: #d4a856;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-brain"></i> إجلال
        </div>
        <ul class="nav-menu">
            <li><a href="#features">المميزات</a></li>
            <li><a href="#stats">الإحصائيات</a></li>
            <li><a href="{{ route('features-guide') }}">التفاصيل</a></li>
        </ul>
        <div class="nav-buttons">
            <button id="darkModeToggle" class="dark-mode-btn" title="تبديل الوضع الليلي">
                <i class="fas fa-moon"></i>
            </button>
            <a href="{{ route('login') }}" class="btn-login">دخول</a>
            <a href="{{ route('register') }}" class="btn-signup">تسجيل جديد</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>
                    تعلم بطريقة <span class="highlight">ذكية</span><br>
                    وطور <span class="highlight">مهاراتك</span>
                </h1>
                <p>
                    منصة تعليمية حديثة متطورة تجمع بين التعليم التقليدي والتقنيات الحديثة لضمان تجربة تعليمية متميزة
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                        <i class="fas fa-rocket"></i> ابدأ الآن مجاناً
                    </a>
                    <a href="{{ route('features-guide') }}" class="btn-hero btn-hero-secondary">
                        <i class="fas fa-info-circle"></i> معرفة المزيد
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-box">
                    <div class="hero-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-title">
            <h2>لماذا <span class="highlight">إجلال</span>؟</h2>
            <p>نقدم لك أدوات وميزات متقدمة لتحقيق أهدافك التعليمية</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="ri-play-circle-line"></i>
                </div>
                <h3>محتوى تفاعلي</h3>
                <p>دروس فيديو عالية الجودة مع شرح مفصل ومحتوى غني</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="ri-medal-line"></i>
                </div>
                <h3>نظام التقييم الذكي</h3>
                <p>اختبارات متنوعة وتقييم فوري مع تحليل تفصيلي للأداء</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="ri-bar-chart-line"></i>
                </div>
                <h3>تتبع التقدم</h3>
                <p>احصائيات مفصلة وتقارير شاملة حول مسار تعلمك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="ri-team-line"></i>
                </div>
                <h3>تفاعل الطلاب</h3>
                <p>تواصل مستمر مع المعلمين والطلاب الآخرين</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="ri-smartphone-line"></i>
                </div>
                <h3>تعليم بدون حدود</h3>
                <p>تعليم من أي مكان وأي وقت على جميع الأجهزة</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="ri-award-line"></i>
                </div>
                <h3>إنجازات وحوافز</h3>
                <p>شارات وجوائز تحفزك على الاستمرار بالتعليم</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats" id="stats">
        <div class="stats-container">
            <div class="stat-item">
                <h3>1000+</h3>
                <p>طالب نشط</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>محتوى تعليمي</p>
            </div>
            <div class="stat-item">
                <h3>50+</h3>
                <p>معلم متخصص</p>
            </div>
            <div class="stat-item">
                <h3>%95</h3>
                <p>رضا المستخدمين</p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <div class="cta">
        <h2>جاهز لبدء رحلتك التعليمية؟</h2>
        <p>انضم إلى آلاف الطلاب الذين يطورون مهاراتهم مع إجلال</p>
        <a href="{{ route('register') }}" class="btn-hero">
            <i class="fas fa-user-plus"></i> سجل الآن مجاناً
        </a>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-links">
            <a href="{{ route('features-guide') }}">الميزات</a>
            <a href="#" title="قريباً">عن المنصة</a>
            <a href="#" title="قريباً">تواصل معنا</a>
            <a href="#" title="قريباً">سياسة الخصوصية</a>
        </div>
        <p>&copy; 2026 إجلال - جميع الحقوق محفوظة</p>
    </footer>

    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = darkModeToggle.querySelector('i');

        // تحقق من التفضيل المحفوظ
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            darkModeIcon.classList.remove('fa-moon');
            darkModeIcon.classList.add('fa-sun');
        }

        // تبديل الوضع الليلي
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                darkModeIcon.classList.remove('fa-moon');
                darkModeIcon.classList.add('fa-sun');
            } else {
                localStorage.setItem('darkMode', 'disabled');
                darkModeIcon.classList.remove('fa-sun');
                darkModeIcon.classList.add('fa-moon');
            }
        });

        // Navigate to section on link click
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Add scrolled class to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
@include('components.account-theme-foot')
</body>
</html>
