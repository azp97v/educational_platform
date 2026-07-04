<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رابط إعادة تعيين كلمة المرور</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
            margin: 15px 0;
        }
        .reset-button {
            text-align: center;
            margin: 30px 0;
        }
        .reset-link {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
        }
        .reset-link:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
        .warning strong {
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 إعادة تعيين كلمة المرور</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>مرحباً،</p>
            
            <p>لقد طلبت إعادة تعيين كلمة المرور لحسابك. انقر على الزر أدناه لإنشاء كلمة مرور جديدة:</p>

            <div class="reset-button">
                <a href="{{ url('/reset-password/' . $token) }}" class="reset-link">
                    إعادة تعيين كلمة المرور
                </a>
            </div>

            <p>أو انسخ ولصق هذا الرابط في متصفحك:</p>
            <p style="color: #10b981; word-break: break-all; font-size: 12px;">
                {{ url('/reset-password/' . $token) }}
            </p>

            <!-- Warning -->
            <div class="warning">
                <strong>⚠️ تنبيه أمان مهم:</strong>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li>هذا الرابط صالح لمدة 60 دقيقة فقط</li>
                    <li>لا تشارك هذا الرابط مع أي شخص</li>
                    <li>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد</li>
                </ul>
            </div>

            <p>إذا واجهت أي مشكلة، يرجى التواصل معنا على البريد الإلكتروني للدعم.</p>

            <p>شكراً،<br>
            <strong>فريق جمعية إجلال التعليمية</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} جمعية إجلال التعليمية. جميع الحقوق محفوظة.</p>
            <p>هذا هو بريد آلي، يرجى عدم الرد عليه.</p>
        </div>
    </div>
</body>
</html>
