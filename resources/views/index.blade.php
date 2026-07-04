<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المنصة التعليمية - تعلم بذكاء</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f6f3ee 0%, #f0ebe2 100%);
            position: relative;
            overflow: hidden;
            padding: 20px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        body.dark-mode .hero {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(246, 218, 9, 0.15) 0%, transparent 70%);
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
            background: radial-gradient(circle, rgba(10, 74, 21, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .hero-content {
            max-width: 800px;
            z-index: 2;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero h1 {
            font-size: clamp(2rem, 8vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #06330e 0%, #0a4a15 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero p {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            color: #555;
            margin-bottom: 40px;
            line-height: 1.8;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .btn-hero-primary {
            padding: 16px 40px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #06330e 0%, #0a4a15 100%);
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-hero-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(6, 51, 14, 0.3);
        }

        .btn-hero-secondary {
            padding: 16px 40px;
            font-size: 1rem;
            font-weight: 600;
            border: 2px solid #06330e;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: transparent;
            color: #06330e;
            text-decoration: none;
            display: inline-block;
        }

        .btn-hero-secondary:hover {
            background: #06330e;
            color: white;
            transform: translateY(-4px);
        }

        /* Features Section */
        .features {
            padding: 80px 20px;
            background: white;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: clamp(1.75rem, 5vw, 2.5rem);
            color: #06330e;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .section-header p {
            font-size: 1.1rem;
            color: #777;
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
            background: linear-gradient(135deg, rgba(6, 51, 18, 0.05) 0%, rgba(6, 51, 18, 0.02) 100%);
            border: 1px solid rgba(6, 51, 18, 0.1);
            border-radius: 12px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.6s ease-out;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: #f6da09;
            box-shadow: 0 15px 40px rgba(6, 51, 18, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            color: #06330e;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            line-height: 1.8;
        }

        /* Statistics Section */
        .stats {
            padding: 60px 20px;
            background: linear-gradient(135deg, #06330e 0%, #0a4a15 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }

        .stat-item h4 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #f6da09;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        /* CTA Section */
        .cta {
            padding: 80px 20px;
            background: linear-gradient(135deg, #f6f3ee 0%, #f0ebe2 100%);
            text-align: center;
        }

        .cta h2 {
            font-size: clamp(1.75rem, 5vw, 2.5rem);
            color: #06330e;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .cta p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: #999;
            padding: 40px 20px;
            text-align: center;
        }

        footer p {
            margin: 10px 0;
        }

        footer a {
            color: #f6da09;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-hero-primary,
            .btn-hero-secondary {
                width: 100%;
                max-width: 300px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body dir="rtl">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>تعلم بذكاء مع منصتنا التعليمية</h1>
            <p>
                منصة تعليمية شاملة توفر لك تجربة تعلم استثنائية مع أدوات متقدمة وتفاعل مباشر مع المعلمين.
            </p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-hero-primary">
                    ابدأ الآن مجاناً
                </a>
                <a href="{{ route('login') }}" class="btn-hero-secondary">
                    تسجيل الدخول
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="section-header">
            <h2>لماذا تختار منصتنا؟</h2>
            <p>نقدم لك الأدوات اللازمة للتعلم الفعال والمستمر</p>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="ri-book-open-line"></i></div>
                <h3>محتوى غني</h3>
                <p>
                    مكتبة شاملة من الدروس والشروحات المتقنة من قبل خبراء في مجالاتهم.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="ri-focus-3-line"></i></div>
                <h3>تعلم مخصص</h3>
                <p>
                    نظام تعلم ذكي يتكيف مع سرعة كل متعلم وأسلوبه الشخصي.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="ri-user-heart-line"></i></div>
                <h3>معلمون محترفون</h3>
                <p>
                    فريق من المعلمين المؤهلين المتاح للإجابة على أسئلتك.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="ri-bar-chart-box-line"></i></div>
                <h3>تتبع التقدم</h3>
                <p>
                    احصل على تقارير مفصلة عن تقدمك وأداؤك في كل درس.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="ri-medal-line"></i></div>
                <h3>نظام النقاط والشارات</h3>
                <p>
                    اكسب نقاط وشارات أثناء التعلم وتنافس مع الآخرين.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="ri-smartphone-line"></i></div>
                <h3>متاح في كل مكان</h3>
                <p>
                    تعلم على أي جهاز وفي أي وقت يناسبك بسهولة وأمان.
                </p>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <h4>50,000+</h4>
                <p>طالب نشط</p>
            </div>
            <div class="stat-item">
                <h4>1,000+</h4>
                <p>درس شامل</p>
            </div>
            <div class="stat-item">
                <h4>98%</h4>
                <p>نسبة الرضا</p>
            </div>
            <div class="stat-item">
                <h4>24/7</h4>
                <p>دعم متاح</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2>هل أنت مستعد للبدء؟</h2>
        <p>انضم إلينا اليوم وابدأ رحلتك التعليمية معنا</p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn-hero-primary">
                إنشاء حساب الآن
            </a>
            <a href="{{ route('login') }}" class="btn-hero-secondary">
                هل لديك حساب بالفعل؟
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} المنصة التعليمية. جميع الحقوق محفوظة.</p>
        <p>
            <a href="#">سياسة الخصوصية</a> |
            <a href="#">شروط الخدمة</a> |
            <a href="#">تواصل معنا</a>
        </p>
    </footer>

    <!-- Theme Manager Script -->
    <script src="{{ asset('js/theme-manager.js') }}"></script>
</body>
</html>
