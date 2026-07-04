<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق</title>
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
        .content p {
            font-size: 16px;
            color: #555;
            margin: 15px 0;
        }
        .code-box {
            background: linear-gradient(135deg, rgba(196, 150, 58, 0.1) 0%, rgba(160, 122, 40, 0.1) 100%);
            border: 2px solid #C4963A;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
            display: inline-block;
            min-width: 250px;
        }
        .code {
            font-size: 48px;
            font-weight: 700;
            color: #C4963A;
            letter-spacing: 8px;
            margin: 0;
            font-family: 'Courier New', monospace;
        }
        .code-info {
            font-size: 14px;
            color: #A07A28;
            margin-top: 15px;
        }
        .warning {
            background: #FFF3CD;
            border-right: 4px solid #FFC107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: right;
            color: #856404;
            font-size: 14px;
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
            margin: 20px 0;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(196, 150, 58, 0.3);
        }
        .icons {
            font-size: 40px;
            margin-bottom: 20px;
        }
        @media (max-width: 480px) {
            .content {
                padding: 20px;
            }
            .code {
                font-size: 36px;
                letter-spacing: 6px;
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
            <h1>🔐 إجلال</h1>
        </div>
        
        <div class="content">
            <div class="icons">🎉</div>
            
            <p style="font-size: 18px; color: #333;">
                <strong>مرحباً بك!</strong>
            </p>
            
            <p>
                لقد اتخذت خطوة رائعة نحو بناء منصتك التعليمية. 
                <br>
                استخدم الكود التالي لتأكيد بريدك الإلكتروني:
            </p>
            
            <div class="code-box">
                <p class="code">{{ $code }}</p>
                <div class="code-info">✓ صحيح لمدة 10 دقائق</div>
            </div>
            
            <div class="warning">
                <strong>⚠️ تنبيه مهم:</strong> لا تشارك هذا الكود مع أحد. فريق إجلال لن يطلب منك هذا الكود أبداً.
            </div>
            
            <p style="color: #777; font-size: 14px;">
                إذا لم تكن قد أنشأت حساباً على إجلال، تجاهل هذا البريد الإلكتروني.
            </p>
            
            <a href="{{ route('register') }}" class="cta-button">
                ← العودة لإكمال التسجيل
            </a>
        </div>
        
        <div class="footer">
            <p>
                هذا البريد تم إرساله إلى <strong>{{ $email }}</strong>
                <br>
                © 2024 منصة إجلال التعليمية - جميع الحقوق محفوظة
            </p>
        </div>
    </div>
</body>
</html>
