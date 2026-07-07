<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حذف الحساب - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-body, 'Tajawal', sans-serif);
            background: var(--theme-page-bg, #f5f5f5);
            color: var(--theme-text, #1C1C1E);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--theme-gold, #C6A675);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .header {
            background: linear-gradient(135deg, var(--theme-danger, #D64545), #ff5a52);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
        }

        .form-card {
            background: var(--theme-surface, white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--theme-shadow, 0 2px 8px rgba(0,0,0,0.1));
        }

        .warning-box {
            background: var(--theme-gold-soft, #fff3cd);
            border: 1px solid var(--theme-gold, #ffc107);
            color: var(--theme-gold-dark, #856404);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .warning-box h3 {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .warning-box ul {
            margin: 0;
            padding-right: 20px;
            font-size: 13px;
        }

        .warning-box li {
            margin-bottom: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--theme-text, #1C1C1E);
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--theme-border, #ddd);
            border-radius: 6px;
            background: var(--theme-page-bg, white);
            color: var(--theme-text, #1C1C1E);
            font-family: var(--font-body, 'Tajawal', sans-serif);
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input[type="password"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: var(--theme-danger, #D64545);
            box-shadow: 0 0 0 3px rgba(214, 69, 69, 0.1);
        }

        .error-message {
            color: var(--theme-danger, #D64545);
            font-size: 13px;
            margin-top: 5px;
        }

        .form-errors {
            background: rgba(214, 69, 69, 0.1);
            border: 1px solid rgba(214, 69, 69, 0.3);
            color: var(--theme-danger, #D64545);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .form-errors ul {
            margin: 0;
            padding-right: 20px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-family: var(--font-body, 'Tajawal', sans-serif);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: var(--theme-danger, #D64545);
            color: white;
            box-shadow: 0 4px 12px rgba(214, 69, 69, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(214, 69, 69, 0.4);
        }

        .btn-cancel {
            background: var(--theme-surface-2, #f5f5f5);
            color: var(--theme-text, #1C1C1E);
            border: 1px solid var(--theme-border, #ddd);
        }

        .btn-cancel:hover {
            background: var(--theme-soft, #e8e8e8);
        }

        @media (max-width: 768px) {
            .container { padding: 20px 16px; }
            .btn-danger, .btn-cancel { width: 100%; }
        }
        @media (max-width: 480px) {
            .container { padding: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('profile.show') }}" class="back-link">
            <i class="ri-arrow-right-line"></i> أرجع
        </a>

        <div class="header">
            <h1>حذف الحساب بشكل نهائي</h1>
        </div>

        <div class="form-card">
            <div class="warning-box">
                <h3><i class="ri-alert-fill"></i> تحذير أمني</h3>
                <ul>
                    <li>هذا الإجراء لا يمكن التراجع عنه</li>
                    <li>سيتم حذف جميع بيانات حسابك بشكل كامل</li>
                    <li>ستفقد الوصول إلى جميع المسارات والشهادات</li>
                    <li>لا يمكن استرجاع البيانات بعد الحذف</li>
                </ul>
            </div>

            @if ($errors->any())
                <div class="form-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.delete-account-confirm') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="form-group">
                    <label for="password">كلمة المرور الحالية</label>
                    <input type="password" id="password" name="password" required placeholder="أدخل كلمة المرور للتحقق">
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="confirmation">تأكيد حذف الحساب</label>
                    <input type="text" id="confirmation" name="confirmation" required placeholder="اكتب: تأكيد حذف حسابي">
                    <div style="font-size: 12px; color: var(--theme-muted, #6C6C70); margin-top: 5px;">اكتب 'تأكيد حذف حسابي' أو 'تاكيد حذف حسابي' بالضبط</div>
                    @error('confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line"></i> حذف حسابي بشكل نهائي
                    </button>
                    <a href="{{ route('profile.show') }}" class="btn btn-cancel">
                        <i class="ri-close-line"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
    @include('components.account-theme-foot')
</body>
</html>

