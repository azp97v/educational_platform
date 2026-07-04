<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم التحقق بنجاح - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Segoe UI', sans-serif;
            background: var(--theme-page-bg);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px;
            direction: rtl;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
            padding-top: max(40px, env(safe-area-inset-top));
            transition: background .3s, color .3s;
            color: var(--text-primary);
        }

        html[data-theme="dark"] body,
        body.dark-mode {
            background: var(--theme-page-bg);
            color: var(--text-primary);
        }

        html[data-theme="dark"] .success-card,
        body.dark-mode .success-card {
            background: var(--theme-surface);
            border-color: rgba(198, 166, 117, 0.15);
            box-shadow: 0 20px 80px rgba(0,0,0,.4);
        }

        html[data-theme="dark"] .title,
        body.dark-mode .title {
            background: linear-gradient(135deg, var(--theme-gold-dark) 0%, #f0c060 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        html[data-theme="dark"] .subtitle,
        body.dark-mode .subtitle { color: var(--text-secondary); }

        html[data-theme="dark"] .feature-item,
        body.dark-mode .feature-item {
            background: var(--theme-surface-2);
            border-color: var(--theme-border);
        }

        html[data-theme="dark"] .feature-label,
        body.dark-mode .feature-label { color: var(--text-secondary); }

        html[data-theme="dark"] .cta-text,
        body.dark-mode .cta-text {
            color: var(--text-primary);
            background: var(--theme-gold-soft);
            border-right-color: var(--theme-gold-dark);
        }

        html[data-theme="dark"] .navigation-links,
        body.dark-mode .navigation-links { border-top-color: var(--theme-border); }

        html[data-theme="dark"] .nav-link,
        body.dark-mode .nav-link { color: var(--theme-gold); }

        html[data-theme="dark"] .nav-separator,
        body.dark-mode .nav-separator { color: var(--text-muted); }

        html[data-theme="dark"] .footer-text,
        body.dark-mode .footer-text { color: var(--text-muted); }

        html[data-theme="dark"] .btn-secondary,
        body.dark-mode .btn-secondary {
            background: var(--theme-surface-2);
            color: var(--theme-gold);
            border-color: var(--theme-border-strong);
        }

        /* Animated Background Elements */
        .bg-decoration {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.6;
            animation: float 8s ease-in-out infinite;
        }

        .decoration-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(246, 218, 9, 0.2) 0%, transparent 70%);
            top: -50px;
            right: -50px;
            animation-delay: 0s;
        }

        .decoration-2 {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(198, 117, 46, 0.15) 0%, transparent 70%);
            bottom: -30px;
            left: -30px;
            animation-delay: 2s;
        }

        .decoration-3 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(6, 51, 14, 0.1) 0%, transparent 70%);
            top: 50%;
            left: 10%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-30px); }
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 600px;
            width: 100%;
        }

        .success-card {
            background: var(--theme-surface);
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 80px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid var(--theme-border);
            animation: slideUpFade 0.8s ease-out;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--theme-gold) 0%, var(--theme-gold-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 3.5rem;
            color: #000;
            box-shadow: 0 15px 40px rgba(198, 117, 46, 0.25);
            animation: scaleRotate 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleRotate {
            0% {
                transform: scale(0) rotate(-45deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.1) rotate(10deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .title {
            font-family: 'Josefin Slab', serif;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--theme-gold) 0%, var(--theme-gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 40px;
            line-height: 1.8;
            font-weight: 500;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        .feature-item {
            background: var(--theme-surface);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(198, 117, 46, 0.1);
            transition: all 0.4s ease;
            animation: slideInUp 0.6s ease-out both;
        }

        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }

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

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(198, 117, 46, 0.15);
            border-color: rgba(198, 117, 46, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
        }

        .feature-icon-1 { color: var(--theme-gold); }
        .feature-icon-2 { color: var(--theme-gold-light); }
        .feature-icon-3 { color: var(--theme-gold-dark); }
        .feature-icon-4 { color: var(--theme-gold); }

        .feature-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            flex-direction: column;
        }

        @media (min-width: 480px) {
            .action-buttons {
                flex-direction: row;
            }

            .action-buttons .btn {
                flex: 1;
            }
        }

        .btn-dashboard {
            background: linear-gradient(135deg, var(--theme-gold) 0%, var(--theme-gold-dark) 100%);
            color: var(--text-primary);
            box-shadow: 0 8px 25px rgba(198, 166, 117, 0.3);
            margin-top: 15px;
            width: 100%;
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(198, 166, 117, 0.4);
        }

        .btn {
            flex: 1;
            padding: 16px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.4s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Cairo', sans-serif;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--theme-gold) 0%, var(--theme-gold-dark) 100%);
            color: var(--text-primary);
            box-shadow: 0 8px 25px rgba(198, 166, 117, 0.3);
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(198, 166, 117, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--theme-surface-2);
            color: var(--theme-gold);
            border: 2px solid var(--theme-border-strong);
            box-shadow: 0 4px 15px rgba(198, 166, 117, 0.1);
        }

        .btn-secondary:hover {
            background: var(--theme-gold);
            color: var(--text-primary);
            border-color: var(--theme-gold);
            box-shadow: 0 8px 25px rgba(198, 166, 117, 0.3);
            transform: translateY(-3px);
        }

        .btn-secondary:active {
            transform: translateY(-1px);
        }

        .navigation-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(198, 117, 46, 0.1);
            flex-wrap: wrap;
        }

        .nav-link {
            color: var(--theme-gold);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .nav-link:hover {
            background: var(--theme-gold-soft);
            color: var(--theme-gold-dark);
        }

        .nav-separator {
            color: var(--text-muted);
        }

        .footer-text {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 20px;
            padding-top: 15px;
        }

        .footer-text a {
            color: var(--theme-gold);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .footer-text a:hover {
            color: var(--theme-gold-dark);
            text-decoration: underline;
        }

        .cta-text {
            font-size: 1.05rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
            font-weight: 600;
            padding: 15px;
            background: var(--theme-gold-soft);
            border-right: 4px solid var(--theme-gold);
            border-radius: 8px;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            body {
                padding: 20px 15px;
                padding-top: max(20px, env(safe-area-inset-top));
            }

            .success-card {
                padding: 35px 25px;
                border-radius: 16px;
            }

            .title {
                font-size: 2rem;
            }

            .success-icon {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
                margin-bottom: 20px;
            }

            .subtitle {
                font-size: 1rem;
                margin-bottom: 30px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                margin-bottom: 30px;
            }

            .feature-item {
                padding: 18px;
            }

            .feature-label {
                font-size: 0.9rem;
            }

            .cta-text {
                font-size: 0.95rem;
                padding: 12px;
                margin-bottom: 15px;
            }

            .action-buttons {
                gap: 12px;
                margin-top: 25px;
            }

            .btn {
                padding: 14px 20px;
                font-size: 0.95rem;
            }

            .btn-dashboard {
                margin-top: 10px;
                padding: 14px 20px;
            }

            .navigation-links {
                gap: 10px;
                margin-top: 20px;
                padding-top: 15px;
            }

            .nav-link {
                font-size: 0.85rem;
                padding: 6px 10px;
            }

            .footer-text {
                font-size: 0.8rem;
                margin-top: 15px;
                padding-top: 12px;
            }
        }
    </style>
</head>
<body dir="rtl">
    <!-- Animated Background Decorations -->
    <div class="bg-decoration decoration-1"></div>
    <div class="bg-decoration decoration-2"></div>
    <div class="bg-decoration decoration-3"></div>

    <div class="container">
        <div class="success-card">
            <!-- Success Icon -->
            <div class="success-icon">
                <i class="ri-check-line"></i>
            </div>

            <!-- Title and Subtitle -->
            <h1 class="title">تم التحقق بنجاح! 🎉</h1>
            <p class="subtitle">
                مبروك عليك! تم إنشاء حسابك بنجاح وتم التحقق من بريدك الإلكتروني.
                <br>
                أنت الآن جاهز للبدء في رحلتك التعليمية الرائعة معنا.
            </p>

            <!-- Features Grid -->
            <div class="features-grid">
                <div class="feature-item">
                    <span class="feature-icon feature-icon-1">
                        <i class="ri-book-open-line"></i>
                    </span>
                    <div class="feature-label">دروس متنوعة</div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon feature-icon-2">
                        <i class="ri-bar-chart-line"></i>
                    </span>
                    <div class="feature-label">تتبع التقدم</div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon feature-icon-3">
                        <i class="ri-group-line"></i>
                    </span>
                    <div class="feature-label">مجتمع تعليمي</div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon feature-icon-4">
                        <i class="ri-trophy-line"></i>
                    </span>
                    <div class="feature-label">جوائز وشارات</div>
                </div>
            </div>

            <!-- Call to Action Text -->
            <div class="cta-text">
                👉 اختر أحد الخيارات أدناه للمتابعة:
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('student.index') }}" class="btn btn-primary">
                    <i class="ri-dashboard-line"></i>
                    لوحة التحكم
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="ri-login-box-line"></i>
                    تسجيل الدخول
                </a>
            </div>

            <!-- Dashboard Button Alternative -->
            <a href="{{ route('student.index') }}" class="btn btn-dashboard">
                <i class="ri-arrow-right-line"></i>
                انتقل مباشرة إلى لوحة التحكم
            </a>

            <!-- Additional Navigation Links -->
            <div class="navigation-links">
                <a href="{{ route('student.index') }}" class="nav-link">
                    <i class="ri-dashboard-line"></i> لوحة التحكم
                </a>
                <span class="nav-separator">|</span>
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="ri-user-line"></i> دخول
                </a>
                <span class="nav-separator">|</span>
                <a href="{{ route('landing') }}" class="nav-link">
                    <i class="ri-global-line"></i> الرئيسية
                </a>
                <span class="nav-separator">|</span>
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="ri-arrow-left-line"></i> الصفحة الرئيسية
                </a>
            </div>

            <!-- Footer with Help Text -->
            <div class="footer-text">
                <p>
                    💡 في حالة وجود أي مشاكل في الدخول، 
                    <a href="{{ route('login') }}">اضغط هنا للدخول مجدداً</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Theme Manager Script -->
    @include('components.account-theme-foot')
</body>
</html>

