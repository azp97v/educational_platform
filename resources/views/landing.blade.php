<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إجلال | منصة التعلم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root{--gold:#C6A675;--gold-dark:#8D7252;--bg:#F4F6F8;--text:#222B3A;--muted:#667085;--card:#fff;--line:#E7ECF2}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Cairo,sans-serif;background:var(--bg);color:var(--text)}
        .wrap{max-width:1200px;margin:0 auto;padding:0 20px}
        .nav{position:sticky;top:0;background:#fff;border-bottom:1px solid var(--line);z-index:20}
        .nav .wrap{height:74px;display:flex;align-items:center;justify-content:space-between}
        .brand{font-weight:900;font-size:26px;color:var(--gold-dark)}
        .actions{display:flex;gap:10px}
        .btn{padding:10px 18px;border-radius:12px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
        .btn-outline{border:1px solid var(--gold);color:var(--gold-dark);background:#fff}
        .btn-main{background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff}
        .hero{padding:70px 0;background:radial-gradient(circle at 15% 15%,rgba(198,166,117,.2),transparent 35%),#fff;border-bottom:1px solid var(--line)}
        .hero-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:24px;align-items:center}
        h1{font-size:44px;line-height:1.25;margin-bottom:14px}
        .lead{color:var(--muted);font-size:18px;line-height:1.9;margin-bottom:18px}
        .hero-card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:20px;box-shadow:0 8px 28px rgba(17,24,39,.06)}
        .hero-kpis{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:12px}
        .kpi{border:1px solid var(--line);border-radius:12px;padding:14px;background:#fff}
        .kpi strong{display:block;font-size:28px;color:var(--gold-dark)}
        .kpi span{color:var(--muted);font-size:13px}
        .sec{padding:52px 0}
        .title{font-size:30px;margin-bottom:10px}
        .sub{color:var(--muted);margin-bottom:20px}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
        .card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px}
        .card i{font-size:26px;color:var(--gold)}
        .card h3{margin:8px 0 6px}
        .card p{color:var(--muted);font-size:14px;line-height:1.8}
        .foot{padding:24px 0;border-top:1px solid var(--line);color:var(--muted);font-size:14px;text-align:center}
        @media(max-width:980px){.hero-grid,.grid{grid-template-columns:1fr}h1{font-size:33px}}
    </style>
</head>
<body>
    <nav class="nav">
        <div class="wrap">
            <div class="brand">جمعية إجلال</div>
            <div class="actions">
                <a class="btn btn-outline" href="{{ route('login') }}"><i class="ri-login-box-line"></i>تسجيل الدخول</a>
                <a class="btn btn-main" href="{{ route('register') }}"><i class="ri-user-add-line"></i>إنشاء حساب</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="wrap hero-grid">
            <div>
                <h1>منصة تعليمية حديثة لإدارة التعلم باحتراف</h1>
                <p class="lead">إجلال تجمع بين تجربة تعلم قوية ولوحات متابعة ذكية للطلاب والمعلمين والإدارة في مكان واحد.</p>
                <div class="actions">
                    <a class="btn btn-main" href="{{ route('login') }}"><i class="ri-rocket-line"></i>ابدأ الآن</a>
                    <a class="btn btn-outline" href="{{ route('features-guide') }}"><i class="ri-compass-3-line"></i>استكشف الميزات</a>
                </div>
            </div>
            <div class="hero-card">
                <h3>لماذا إجلال؟</h3>
                <p class="sub">واجهة عربية واضحة، أداء سريع، وتحكم شامل لكل دور داخل المنصة.</p>
                <div class="hero-kpis">
                    <div class="kpi"><strong>24/7</strong><span>تشغيل مستمر</span></div>
                    <div class="kpi"><strong>+99%</strong><span>استقرار المنصة</span></div>
                    <div class="kpi"><strong>3</strong><span>لوحات أدوار متخصصة</span></div>
                    <div class="kpi"><strong>أمان</strong><span>صلاحيات دقيقة</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="sec">
        <div class="wrap">
            <h2 class="title">أهم المزايا</h2>
            <p class="sub">تصميم واضح، إدارة أسهل، متابعة أفضل.</p>
            <div class="grid">
                <article class="card"><i class="ri-dashboard-line"></i><h3>لوحات ذكية</h3><p>لوحات حديثة للطالب والمعلم والإدارة مع مؤشرات مباشرة.</p></article>
                <article class="card"><i class="ri-shield-check-line"></i><h3>صلاحيات دقيقة</h3><p>تحكم RBAC واضح لكل وظيفة مع مسارات آمنة.</p></article>
                <article class="card"><i class="ri-line-chart-line"></i><h3>تحليلات وتقارير</h3><p>متابعة الأداء والأنشطة وقياس التقدم بشكل بصري.</p></article>
            </div>
        </div>
    </section>

    <footer class="foot">© {{ date('Y') }} جمعية إجلال - جميع الحقوق محفوظة</footer>
</body>
</html>
