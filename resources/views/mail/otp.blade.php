<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f6f3ee 0%, #f0ebe2 100%);
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #06330e 0%, #0a4a15 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .content {
            padding: 40px 30px;
            text-align: right;
        }

        .greeting {
            font-size: 18px;
            color: #06330e;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            color: #555;
            margin-bottom: 30px;
            font-size: 15px;
            line-height: 1.8;
        }

        .otp-box {
            background: linear-gradient(135deg, rgba(246, 218, 9, 0.1) 0%, rgba(229, 202, 160, 0.1) 100%);
            border: 2px solid #f6da09;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }

        .otp-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #f6da09;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .expiry-note {
            color: #f6820c;
            font-size: 13px;
            margin-top: 15px;
            font-weight: 600;
        }

        .security-warning {
            background: rgba(255, 59, 48, 0.05);
            border-right: 4px solid #ff3b30;
            padding: 15px;
            margin: 30px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #d32f2f;
        }

        .security-warning strong {
            display: block;
            margin-bottom: 8px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #06330e 0%, #0a4a15 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 51, 14, 0.3);
        }

        .footer {
            background: #f9f7f3;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #999;
            border-top: 1px solid #e5e5e5;
        }

        .footer p {
            margin: 8px 0;
        }

        .footer a {
            color: #06330e;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .divider {
            height: 1px;
            background: #e5e5e5;
            margin: 20px 0;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                border-radius: 8px;
            }

            .content {
                padding: 30px 20px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 رمز التحقق من البريد</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p class="greeting">
                مرحباً {{ $name }}،
            </p>

            <!-- Message -->
            <p class="message">
                شكراً لتسجيلك معنا. لاستكمال تفعيل حسابك، يرجى استخدام رمز التحقق أدناه في صفحة التحقق.
            </p>

            <!-- OTP Box -->
            <div class="otp-box">
                <p class="otp-label">رمز التحقق الخاص بك:</p>
                <div class="otp-code">{{ $otp }}</div>
                <p class="expiry-note">✓ صحيح لمدة {{ $expiresInMinutes }} دقيقة</p>
            </div>

            <!-- Security Warning -->
            <div class="security-warning">
                <strong>⚠️ نصيحة أمان مهمة:</strong>
                <span>لا تشارك هذا الرمز مع أحد. فريق الدعم الرسمي لن يطلب منك هذا الرمز.</span>
            </div>

            <!-- Instructions -->
            <p class="message" style="background: rgba(52, 199, 89, 0.05); padding: 15px; border-radius: 6px; border-right: 4px solid #34c759;">
                <strong>الخطوات التالية:</strong><br>
                1. انسخ رمز التحقق أعلاه<br>
                2. عودّ إلى صفحة التحقق<br>
                3. الصق الرمز في حقل الإدخال<br>
                4. اضغط "تحقق من البريد"
            </p>

            <!-- Button -->
            <div class="button-container">
                <a href="{{ url('/auth/otp-verify') }}" class="button">الذهاب إلى صفحة التحقق</a>
            </div>

            <div class="divider"></div>

            <!-- Additional Info -->
            <p class="message" style="font-size: 14px; color: #888;">
                إذا لم تقم بهذا الطلب أو لا تتوقع استقبال هذا البريد، يرجى تجاهل هذه الرسالة.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>نحتاج مساعدتك؟</strong></p>
            <p>
                <a href="{{ url('/support') }}">قسم الدعم</a> |
                <a href="{{ url('/contact') }}">تواصل معنا</a> |
                <a href="{{ url('/faq') }}">الأسئلة الشائعة</a>
            </p>
            <div class="divider" style="background: #ddd; margin: 15px 0;"></div>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة. جميع الحقوق المحفوظة.</p>
        </div>
    </div>
</body>
</html>
