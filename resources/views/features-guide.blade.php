<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل الميزات الجديدة</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        [data-theme="dark"] input,
        [data-theme="dark"] textarea,
        [data-theme="dark"] select {
            background: #2a2a2a !important;
            color: #f5f5f5 !important;
            border-color: #444 !important;
        }

        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder {
            color: #888 !important;
        }

        [data-theme="dark"] input[type="radio"],
        [data-theme="dark"] input[type="checkbox"] {
            accent-color: var(--gold) !important;
            cursor: pointer;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-body), 'Tajawal', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: var(--gold);
        }

        .subtitle {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 40px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .feature-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            border-top: 4px solid var(--gold);
        }

        .feature-icon {
            font-size: 32px;
            color: var(--gold);
            margin-bottom: 12px;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: var(--text-secondary);
            margin-bottom: 12px;
        }

        .links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--gold);
            text-decoration: none;
            padding: 8px 12px;
            background: #f5f5f7;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .link:hover {
            background: var(--gold);
            color: white;
        }

        .test-info {
            background: #E3F2FD;
            border-right: 4px solid #2196F3;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 40px;
        }

        .test-info p {
            margin: 8px 0;
            font-size: 14px;
        }

        .test-info strong {
            color: #1565C0;
        }

        .checklist {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .checklist h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--gold);
        }

        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #E5E5EA;
        }

        .checklist-item:last-child {
            border-bottom: none;
        }

        .check-icon {
            color: var(--success);
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .checklist-text {
            flex: 1;
        }

        .checklist-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .checklist-desc {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 28px;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .container { padding: 20px 16px; }
            h1 { font-size: 24px; }
            .subtitle { font-size: 15px; }
            .features { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .container { padding: 12px; }
            h1 { font-size: 20px; }
            .feature-card { padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 نظام التعليم الذكي - الميزات الجديدة</h1>
        <p class="subtitle">دليل شامل للميزات المضافة حديثاً</p>

        <div class="test-info">
            <p><strong>بيانات اختبار:</strong></p>
            <p>المعلم: teacher@test.com | كلمة المرور: password</p>
            <p>الطالب: student1@test.com | كلمة المرور: password</p>
        </div>

        <div class="section">
            <h2 class="section-title">
                <i class="ri-star-line"></i> الميزات الرئيسية
            </h2>
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="ri-logout-box-line"></i>
                    </div>
                    <h3>✅ تسجيل الخروج</h3>
                    <p>خاصية تسجيل الخروج الآمن من النظام</p>
                    <p style="font-size: 12px; color: #666;">الموجودة في الزاوية العلوية اليسرى</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="ri-book-3-line"></i>
                    </div>
                    <h3>✅ إدارة المسارات</h3>
                    <p>إنشاء وتعديل وحذف المسارات التعليمية</p>
                    <div class="links">
                        <a href="/teacher/create" class="link">
                            <i class="ri-add-line"></i> إنشاء مسار جديد
                        </a>
                        <a href="/teacher" class="link">
                            <i class="ri-list-check"></i> عرض مساراتي
                        </a>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="ri-video-line"></i>
                    </div>
                    <h3>✅ إدارة الدروس</h3>
                    <p>إضافة دروس مع روابط فيديو</p>
                    <p style="font-size: 12px; color: #666;">داخل صفحة تحرير المسار</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="ri-file-list-line"></i>
                    </div>
                    <h3>✅ إدارة الاختبارات</h3>
                    <p>إنشاء اختبارات وأسئلة شاملة</p>
                    <p style="font-size: 12px; color: #666;">داخل صفحة تحرير المسار</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="ri-chat-3-line"></i>
                    </div>
                    <h3>✅ أسئلة الطلاب</h3>
                    <p>نظام تفاعلي للرد على أسئلة الطلاب</p>
                    <div class="links">
                        <a href="/teacher/inquiries" class="link">
                            <i class="ri-arrow-right-line"></i> عرض الأسئلة (معلم)
                        </a>
                        <a href="/s/inquiries" class="link">
                            <i class="ri-arrow-right-line"></i> أسئلتي (طالب)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="checklist">
                <h2>✨ قائمة أعمال المعلم</h2>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">1. إنشاء مسار جديد</div>
                        <div class="checklist-desc">انقر على "مسار جديد" وأدخل اسم المسار والوصف</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">2. إضافة دروس</div>
                        <div class="checklist-desc">انقر على "تحرير" ثم أضف دروس مع روابط الفيديو</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">3. إنشاء اختبارات</div>
                        <div class="checklist-desc">اختر درساً وأنشئ اختبار مع أسئلة متعددة</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">4. الرد على أسئلة الطلاب</div>
                        <div class="checklist-desc">اذهب إلى "أسئلة الطلاب" وأجب على الأسئلة المعلقة</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">5. تسجيل الخروج</div>
                        <div class="checklist-desc">انقر على زر "خروج" في الزاوية العلوية</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="checklist">
                <h2>✨ قائمة أعمال الطالب</h2>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">1. عرض الدروس</div>
                        <div class="checklist-desc">اذهب إلى لوحة الطالب واختر مسار لعرض الدروس</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">2. مشاهدة الفيديوهات</div>
                        <div class="checklist-desc">شاهد الفيديوهات المرتبطة بالدروس</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">3. حل الاختبارات</div>
                        <div class="checklist-desc">أجب على أسئلة الاختبارات وشاهد نتائجك</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">4. طرح الأسئلة</div>
                        <div class="checklist-desc">اذهب إلى "أسئلتي" واطرح أسئلة على المعلم</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon"><i class="ri-check-line"></i></div>
                    <div class="checklist-text">
                        <div class="checklist-title">5. تسجيل الخروج</div>
                        <div class="checklist-desc">انقر على زر "خروج" في الزاوية العلوية</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px; padding: 20px; background: #F0F2F5; border-radius: 12px;">
            <p style="font-size: 16px; color: var(--text-primary); margin-bottom: 20px;">
                <strong>اختبر النظام:</strong>
            </p>
            <a href="/login" style="display: inline-block; padding: 12px 24px; background: var(--gold); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="ri-login-box-line"></i> اذهب لتسجيل الدخول
            </a>
        </div>
    </div>
@include('components.account-theme-foot')
</body>
</html>
