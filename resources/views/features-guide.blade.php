<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
    <meta name="description" content="دليل شامل لجميع ميزات منصة إجلال التعليمية">
    <title>الدليل الشامل — منصة إجلال التعليمية</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-body), 'Tajawal', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            line-height: 1.7;
            min-height: 100vh;
        }

        /* ── الرأس ────────────────────────────────────────── */
        .fg-hero {
            text-align: center;
            padding: 60px 20px 40px;
            position: relative;
            overflow: hidden;
        }
        .fg-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 20%, rgba(198,166,117,.12), transparent 55%),
                        radial-gradient(circle at 70% 80%, rgba(198,166,117,.08), transparent 55%);
            pointer-events: none;
        }
        .fg-hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            background: rgba(198,166,117,.12);
            border: 1px solid rgba(198,166,117,.22);
            font-size: 13px;
            font-weight: 600;
            color: #C6A675;
            margin-bottom: 20px;
        }
        .fg-hero h1 {
            font-size: clamp(28px, 5vw, 44px);
            font-weight: 900;
            margin-bottom: 12px;
            color: var(--text-primary);
        }
        .fg-hero p {
            font-size: clamp(14px, 2.2vw, 17px);
            color: var(--text-muted, rgba(255,255,255,.6));
            max-width: 640px;
            margin: 0 auto;
        }

        /* ── التنقّل السريع ──────────────────────────────── */
        .fg-nav {
            display: flex;
            gap: 8px;
            padding: 12px 20px;
            overflow-x: auto;
            justify-content: center;
            flex-wrap: wrap;
            background: rgba(198,166,117,.04);
            border-top: 1px solid rgba(198,166,117,.14);
            border-bottom: 1px solid rgba(198,166,117,.14);
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
        }
        .fg-nav a {
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 600;
            transition: background .18s, color .18s;
            white-space: nowrap;
        }
        .fg-nav a:hover { background: rgba(198,166,117,.14); color: #C6A675; }

        /* ── الحاوية ────────────────────────────────────── */
        .fg-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }

        /* ── القسم ───────────────────────────────────────── */
        .fg-section { margin-bottom: 60px; scroll-margin-top: 80px; }
        .fg-section h2 {
            font-size: clamp(20px, 3vw, 26px);
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .fg-section h2 i {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(198,166,117,.14);
            border-radius: 10px;
            color: #C6A675;
            font-size: 22px;
        }
        .fg-section__intro {
            color: var(--text-muted, rgba(255,255,255,.55));
            font-size: 15px;
            margin-bottom: 22px;
            padding-inline-start: 56px;
        }

        /* ── شبكة الميزات ───────────────────────────────── */
        .fg-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 14px;
        }
        .fg-card {
            padding: 20px;
            background: rgba(198,166,117,.05);
            border: 1px solid rgba(198,166,117,.16);
            border-radius: 12px;
            transition: transform .2s, background .2s, border-color .2s;
        }
        .fg-card:hover {
            transform: translateY(-2px);
            background: rgba(198,166,117,.09);
            border-color: rgba(198,166,117,.32);
        }
        .fg-card__icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(198,166,117,.14);
            color: #C6A675;
            font-size: 20px;
            margin-bottom: 12px;
        }
        .fg-card__title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-primary);
        }
        .fg-card__body {
            font-size: 13px;
            color: var(--text-muted, rgba(255,255,255,.6));
            line-height: 1.7;
        }

        /* ── الروابط السريعة أسفل ──────────────────────── */
        .fg-cta {
            text-align: center;
            padding: 40px 20px;
            background: rgba(198,166,117,.06);
            border-radius: 16px;
            border: 1px solid rgba(198,166,117,.18);
            margin-top: 40px;
        }
        .fg-cta h3 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .fg-cta p {
            color: var(--text-muted, rgba(255,255,255,.6));
            margin-bottom: 20px;
        }
        .fg-cta__btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #C6A675;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            transition: transform .18s, box-shadow .18s;
        }
        .fg-cta__btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(198,166,117,.32);
        }

        /* ── ثيم فاتح ───────────────────────────────────── */
        [data-theme="light"] .fg-card,
        :not([data-theme]) .fg-card {
            background: rgba(141,114,82,.04);
            border-color: rgba(141,114,82,.14);
        }
        [data-theme="light"] .fg-card:hover,
        :not([data-theme]) .fg-card:hover {
            background: rgba(141,114,82,.08);
            border-color: rgba(141,114,82,.24);
        }

        /* ── الموبايل ───────────────────────────────────── */
        @media (max-width: 640px) {
            .fg-hero { padding: 40px 16px 30px; }
            .fg-section__intro { padding-inline-start: 0; }
            .fg-section h2 { flex-wrap: wrap; }
            .fg-nav { padding: 8px 10px; gap: 6px; justify-content: flex-start; }
            .fg-nav a {
                padding: 12px 14px;
                min-height: 44px;
                display: inline-flex;
                align-items: center;
                font-size: 13px;
            }
            .fg-container { padding: 24px 14px 40px; }
            .fg-grid { grid-template-columns: 1fr; gap: 10px; }
            .fg-card { padding: 16px; }
        }

        /* ── احترام تقليل الحركة ───────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            .fg-card:hover { transform: none; }
            .fg-cta__btn:hover { transform: none; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>

<div class="fg-hero">
    <div class="fg-hero__badge">
        <i class="ri-book-open-line"></i>
        <span>الدليل الشامل</span>
    </div>
    <h1>كل ما تحتاج معرفته عن منصة إجلال التعليمية</h1>
    <p>دليل تفصيلي لجميع الميزات والأدوات المتاحة للطلاب والمعلمين والإدارة. اختر القسم الذي يهمّك من الأعلى.</p>
</div>

<nav class="fg-nav" aria-label="التنقّل السريع">
    <a href="#students">للطلاب</a>
    <a href="#teachers">للمعلمين</a>
    <a href="#admin">للإدارة</a>
    <a href="#messaging">المراسلة</a>
    <a href="#calls">المكالمات</a>
    <a href="#exams">الاختبارات والشهادات</a>
    <a href="#gamification">النقاط والإنجازات</a>
    <a href="#account">الحساب والإعدادات</a>
</nav>

<div class="fg-container">

    {{-- ── للطلاب ────────────────────────────────────── --}}
    <section class="fg-section" id="students">
        <h2><i class="ri-graduation-cap-line"></i> للطلاب</h2>
        <p class="fg-section__intro">تعلّم بوتيرتك، تفاعل مع معلّمك، وتابع تقدّمك بلوحة تحكم ذكية.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-dashboard-2-line"></i></div>
                <div class="fg-card__title">لوحة تحكم شخصية</div>
                <div class="fg-card__body">إحصاءاتك، تقدّمك في المسارات، مواعيدك، وإشعاراتك في مكان واحد.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-play-circle-line"></i></div>
                <div class="fg-card__title">دروس مرئية وصوتية</div>
                <div class="fg-card__body">مشاهدة الدروس بجودة عالية مع تتبّع تلقائي لموقعك الأخير.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-sticky-note-line"></i></div>
                <div class="fg-card__title">ملاحظات وأسئلة</div>
                <div class="fg-card__body">اكتب ملاحظاتك على الدروس، وأرسل أسئلتك للمعلّم مباشرة من صفحة الدرس.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-file-download-line"></i></div>
                <div class="fg-card__title">تحميل المصادر</div>
                <div class="fg-card__body">حمّل ملفات الدرس ومصادره لدراستها لاحقاً حتى بلا اتصال.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-star-line"></i></div>
                <div class="fg-card__title">تقييم الدروس</div>
                <div class="fg-card__body">قيّم كل درس لتحسين تجربتك ومساعدة المعلّم على تطوير محتواه.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-user-add-line"></i></div>
                <div class="fg-card__title">التسجيل في المسارات</div>
                <div class="fg-card__body">تصفّح المسارات المتاحة وأرسل طلب التحاق بضغطة زر واحدة.</div>
            </div>
        </div>
    </section>

    {{-- ── للمعلمين ────────────────────────────────── --}}
    <section class="fg-section" id="teachers">
        <h2><i class="ri-user-star-line"></i> للمعلمين</h2>
        <p class="fg-section__intro">أنشئ محتوى تعليمي متكامل وأدِر طلابك واختباراتهم من مكان واحد.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-add-box-line"></i></div>
                <div class="fg-card__title">إنشاء المسارات</div>
                <div class="fg-card__body">صمّم مساراتك التعليمية بتقسيم منطقي للدروس وحدّد نوعها (فعلية أو تدريبية).</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-video-add-line"></i></div>
                <div class="fg-card__title">دروس متعدّدة الصيغ</div>
                <div class="fg-card__body">فيديو، صوت، أو محتوى نصّي. YouTube مدعوم مع استخراج تلقائي للمدّة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-file-list-3-line"></i></div>
                <div class="fg-card__title">اختبارات ذكية</div>
                <div class="fg-card__body">أسئلة اختيارية ومفتوحة، مدد زمنية، تصحيح تلقائي، وتقارير تفصيلية.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-question-answer-line"></i></div>
                <div class="fg-card__title">إدارة أسئلة الطلاب</div>
                <div class="fg-card__body">استلم أسئلة طلابك ورُدّ عليها من صفحة واحدة موحّدة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-award-line"></i></div>
                <div class="fg-card__title">مصمّم الشهادات</div>
                <div class="fg-card__body">قوالب جاهزة أو خصّص شهاداتك بحرية كاملة، وأصدرها للطلاب بضغطة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-bar-chart-2-line"></i></div>
                <div class="fg-card__title">تحليلات وأداء</div>
                <div class="fg-card__body">تابع نشاط طلابك، نتائج اختباراتهم، ومستوى تفاعلهم بمخطّطات واضحة.</div>
            </div>
        </div>
    </section>

    {{-- ── للإدارة ─────────────────────────────────── --}}
    <section class="fg-section" id="admin">
        <h2><i class="ri-shield-user-line"></i> للإدارة</h2>
        <p class="fg-section__intro">إدارة كاملة لكل ما يدور في المنصة — مستخدمون، مسارات، إعلانات، وأمن.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-group-line"></i></div>
                <div class="fg-card__title">إدارة المستخدمين</div>
                <div class="fg-card__body">إنشاء، تعديل، حذف، وإعادة تعيين كلمات المرور. عمليات جماعية وتصدير للـCSV.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-notification-3-line"></i></div>
                <div class="fg-card__title">الإعلانات الديناميكية</div>
                <div class="fg-card__body">أنشئ إعلانات متحرّكة تظهر لفئات معيّنة، بألوان مختلفة، ومدد انتهاء تلقائية.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-line-chart-line"></i></div>
                <div class="fg-card__title">تحليلات المنصة</div>
                <div class="fg-card__body">مقاييس فورية عن التسجيلات، توزيع الأدوار، أكثر المعلّمين نشاطاً، وأحدث المستخدمين.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-file-warning-line"></i></div>
                <div class="fg-card__title">المهام الفاشلة</div>
                <div class="fg-card__body">تتبّع الوظائف الخلفية الفاشلة، أعِد تشغيلها أو احذفها لضمان استقرار المنصة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-check-double-line"></i></div>
                <div class="fg-card__title">طلبات التسجيل</div>
                <div class="fg-card__body">راجع طلبات التسجيل في المسارات ووافق عليها أو ارفضها من مكان مركزي.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-settings-3-line"></i></div>
                <div class="fg-card__title">إعدادات المنصة</div>
                <div class="fg-card__body">تحكّم في هوية المنصة، سياساتها، وقواعد التفاعل من مكان واحد.</div>
            </div>
        </div>
    </section>

    {{-- ── المراسلة ────────────────────────────────── --}}
    <section class="fg-section" id="messaging">
        <h2><i class="ri-chat-3-line"></i> نظام المراسلة المتكامل</h2>
        <p class="fg-section__intro">دردشة فورية، حالات، مجموعات، ومكالمات — كل شيء في مكان واحد.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-flashlight-line"></i></div>
                <div class="fg-card__title">رسائل لحظية</div>
                <div class="fg-card__body">تسليم فوري عبر WebSocket. مؤشّرات "يكتب الآن" وقراءة الرسالة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-attachment-2"></i></div>
                <div class="fg-card__title">مرفقات متنوّعة</div>
                <div class="fg-card__body">صور، ملفات، PDF، تسجيلات صوتية، وGIFs — كل شيء بضغطة زر.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-lock-line"></i></div>
                <div class="fg-card__title">تشفير طرف-إلى-طرف</div>
                <div class="fg-card__body">رسائلك مشفّرة بمعيار AES-GCM قبل إرسالها. لا يمكن قراءتها إلا للطرفَين.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-history-line"></i></div>
                <div class="fg-card__title">حالات وقصص</div>
                <div class="fg-card__body">شارك حالة نصّية، صور، أو فيديو تختفي بعد 24 ساعة. أضف تفاعلات وردود.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-team-line"></i></div>
                <div class="fg-card__title">مجموعات وقنوات</div>
                <div class="fg-card__body">أنشئ محادثات جماعية أو قنوات نشر مع صلاحيات إدارية.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-folder-line"></i></div>
                <div class="fg-card__title">مجلدات المحادثات</div>
                <div class="fg-card__body">نظّم دردشاتك في مجلدات: عمل، عائلة، مفضّلة، أو أي تصنيف تختاره.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-bookmark-line"></i></div>
                <div class="fg-card__title">الرسائل المحفوظة</div>
                <div class="fg-card__body">احفظ الرسائل المهمّة للرجوع إليها بسرعة من قسم مخصّص.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-user-forbid-line"></i></div>
                <div class="fg-card__title">حظر وخصوصية</div>
                <div class="fg-card__body">تحكّم كامل في من يمكنه التواصل معك أو مشاهدة حالتك.</div>
            </div>
        </div>
    </section>

    {{-- ── المكالمات ────────────────────────────────── --}}
    <section class="fg-section" id="calls">
        <h2><i class="ri-phone-line"></i> المكالمات الصوتية والمرئية</h2>
        <p class="fg-section__intro">تواصل مباشر بجودة عالية — فردية أو جماعية، من أي جهاز.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-mic-line"></i></div>
                <div class="fg-card__title">مكالمات صوتية</div>
                <div class="fg-card__body">صوت واضح بجودة عالية عبر بنية تحتية إنتاجية موزّعة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-vidicon-line"></i></div>
                <div class="fg-card__title">مكالمات فيديو</div>
                <div class="fg-card__body">مقابلات فيديو مباشرة مع التحكّم في الكاميرا والميكروفون.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-group-2-line"></i></div>
                <div class="fg-card__title">مكالمات جماعية</div>
                <div class="fg-card__body">حصص وورش تفاعلية مع عدّة مشاركين، بأدوار وصلاحيات مختلفة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-computer-line"></i></div>
                <div class="fg-card__title">مشاركة الشاشة</div>
                <div class="fg-card__body">شارك شاشتك أثناء المكالمة لعرض المحتوى التعليمي أو التطبيقات.</div>
            </div>
        </div>
    </section>

    {{-- ── الاختبارات والشهادات ────────────────────── --}}
    <section class="fg-section" id="exams">
        <h2><i class="ri-award-line"></i> الاختبارات والشهادات</h2>
        <p class="fg-section__intro">اختبارات مصمّمة بذكاء وشهادات احترافية تحصد ثمار جهدك.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-time-line"></i></div>
                <div class="fg-card__title">اختبارات موقوتة</div>
                <div class="fg-card__body">حدّد مدّة الاختبار مع مؤقّت واضح للطالب أثناء الإجابة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-check-line"></i></div>
                <div class="fg-card__title">تصحيح فوري</div>
                <div class="fg-card__body">الأسئلة الاختيارية تُصحَّح تلقائياً — النتيجة تظهر فوراً بعد التسليم.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-medal-line"></i></div>
                <div class="fg-card__title">شهادات احترافية</div>
                <div class="fg-card__body">قوالب متعدّدة، تصميم مخصّص، ورقم فريد للتحقّق من الأصالة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-verified-badge-line"></i></div>
                <div class="fg-card__title">تحقّق من الشهادة</div>
                <div class="fg-card__body">صفحة عامّة تتيح لأي جهة التحقّق من صحّة الشهادة برقمها.</div>
            </div>
        </div>
    </section>

    {{-- ── النقاط والإنجازات ──────────────────────── --}}
    <section class="fg-section" id="gamification">
        <h2><i class="ri-trophy-line"></i> النقاط والإنجازات</h2>
        <p class="fg-section__intro">نظام تحفيزي كامل يجعل رحلة التعلّم مليئة بالتحدّي والمتعة.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-fire-line"></i></div>
                <div class="fg-card__title">سلسلة الأيام</div>
                <div class="fg-card__body">حافظ على دراستك يومياً لبناء سلسلة نشاط متواصلة وأرقام قياسية.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-coin-line"></i></div>
                <div class="fg-card__title">النقاط والمكافآت</div>
                <div class="fg-card__body">اكسب نقاطاً على كل درس مكتمل، اختبار ناجح، أو تفاعل مع المعلّم.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-trophy-line"></i></div>
                <div class="fg-card__title">لوحة المتصدّرين</div>
                <div class="fg-card__body">قارن أداءك مع زملائك في لوحة تنافسية محدَّثة يومياً.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-shield-star-line"></i></div>
                <div class="fg-card__title">شارات الإنجاز</div>
                <div class="fg-card__body">اكسب شارات فريدة على معالم مختلفة — من "أوّل درس" إلى "متعلّم مثابر".</div>
            </div>
        </div>
    </section>

    {{-- ── الحساب والإعدادات ──────────────────────── --}}
    <section class="fg-section" id="account">
        <h2><i class="ri-user-settings-line"></i> الحساب والإعدادات</h2>
        <p class="fg-section__intro">تحكّم كامل في هويّتك وخصوصيّتك ومظهر المنصة.</p>
        <div class="fg-grid">
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-contrast-2-line"></i></div>
                <div class="fg-card__title">الثيم الليلي والنهاري</div>
                <div class="fg-card__body">تبديل فوري بين المظهر الفاتح والداكن حسب تفضيلك أو وقت اليوم.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-mail-check-line"></i></div>
                <div class="fg-card__title">تحقّق البريد بـOTP</div>
                <div class="fg-card__body">تسجيل آمن برمز تحقّق يُرسل لبريدك — بلا كلمات مرور معقّدة.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-shield-keyhole-line"></i></div>
                <div class="fg-card__title">مصادقة ثنائية</div>
                <div class="fg-card__body">فعّل خطوة أمان إضافية لحسابك في المراسلة — 2FA بالبريد.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-notification-line"></i></div>
                <div class="fg-card__title">تفضيلات الإشعارات</div>
                <div class="fg-card__body">تحكّم دقيق في نوع الإشعارات: تسجيلات، رسائل، مكالمات، شهادات، أو نظام.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-devices-line"></i></div>
                <div class="fg-card__title">إدارة الجلسات</div>
                <div class="fg-card__body">شاهد الأجهزة النشطة على حسابك وأنهِ أي جلسة عن بُعد.</div>
            </div>
            <div class="fg-card">
                <div class="fg-card__icon"><i class="ri-download-cloud-line"></i></div>
                <div class="fg-card__title">تصدير بياناتك</div>
                <div class="fg-card__body">حمّل نسخة كاملة من رسائلك وبياناتك في أي وقت بضغطة زر.</div>
            </div>
        </div>
    </section>

    {{-- ── نداء العمل ─────────────────────────────── --}}
    <div class="fg-cta">
        <h3>جاهز للبدء؟</h3>
        <p>سجّل الآن وابدأ رحلتك التعليمية على منصة إجلال.</p>
        @auth
            <a href="{{ url('/dashboard') }}" class="fg-cta__btn">
                <i class="ri-dashboard-line"></i>
                <span>افتح لوحة تحكمك</span>
            </a>
        @else
            <a href="{{ route('register') }}" class="fg-cta__btn">
                <i class="ri-user-add-line"></i>
                <span>أنشئ حساباً جديداً</span>
            </a>
        @endauth
    </div>

</div>

</body>
</html>
