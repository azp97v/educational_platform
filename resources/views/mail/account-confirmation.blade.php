<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم تأكيد حسابك</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #C4963A 0%, #A07A28 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .success-badge {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #C4963A 0%, #A07A28 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(196, 150, 58, 0.3);
        }
        .content h2 {
            font-size: 28px;
            color: #333;
            margin: 20px 0 10px 0;
            font-weight: 700;
        }
        .content p {
            font-size: 16px;
            color: #555;
            margin: 15px 0;
            line-height: 1.8;
        }
        .account-info {
            background: linear-gradient(135deg, rgba(196, 150, 58, 0.05) 0%, rgba(160, 122, 40, 0.05) 100%);
            border-right: 4px solid #C4963A;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: right;
        }
        .account-info p {
            margin: 10px 0;
            font-size: 14px;
        }
        .account-info strong {
            color: #C4963A;
        }
        .features {
            text-align: right;
            margin: 30px 0;
        }
        .features h3 {
            color: #A07A28;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .features li {
            padding: 10px 0;
            color: #666;
            font-size: 14px;
            border-bottom: 1px solid #e9ecef;
        }
        .features li:before {
            content: "✓ ";
            color: #C4963A;
            font-weight: 700;
            margin-left: 10px;
        }
        .cta-button {
            background: linear-gradient(135deg, #C4963A 0%, #A07A28 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 20px 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(196, 150, 58, 0.3);
        }
        .secondary-button {
            background: white;
            color: #C4963A;
            border: 2px solid #C4963A;
        }
        .secondary-button:hover {
            background: rgba(196, 150, 58, 0.05);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
        }
        .footer a {
            color: #C4963A;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .content {
                padding: 20px;
            }
            .content h2 {
                font-size: 22px;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌟 إجلال</h1>
        </div>
        
        <div class="content">
            <div class="success-badge">✓</div>
            
            <h2>مرحباً بك في إجلال!</h2>
            
            <p>
                تم تأكيد حسابك بنجاح. أنت الآن جزء من عائلة منصة إجلال التعليمية الرائعة!
            </p>
            
            <div class="account-info">
                <p>
                    <strong>الاسم:</strong> {{ $userName }}
                </p>
                <p>
                    <strong>البريد الإلكتروني:</strong> {{ $userEmail }}
                </p>
            </div>
            
            <div class="features">
                <h3>ماذا يمكنك فعله الآن؟</h3>
                <ul>
                    <li>الوصول الكامل لجميع الدورات التعليمية</li>
                    <li>تتبع مسارك التعليمي وإحصائياتك</li>
                    <li>التفاعل مع المحتوى التعليمي</li>
                    <li>الحصول على شهادات عند إكمال الدورات</li>
                    <li>المشاركة مع معلميك والطلاب الآخرين</li>
                </ul>
            </div>
            
            <p style="color: #A07A28; font-weight: 600;">
                نتطلع لرؤيتك تحقق النجاح على المنصة! 🚀
            </p>
            
            <a href="{{ route('dashboard') }}" class="cta-button">
                ← انتقل إلى لوحة التحكم
            </a>
            <a href="{{ route('courses.index') }}" class="cta-button secondary-button">
                استكشف الدورات
            </a>
        </div>
        
        <div class="footer">
            <p>
                © 2024 منصة إجلال التعليمية - جميع الحقوق محفوظة
                <br>
                لديك أي استفسارات؟ <a href="mailto:support@eglal.com">تواصل معنا</a>
            </p>
        </div>
    </div>
</body>
</html>
