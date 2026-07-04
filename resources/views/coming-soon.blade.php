<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قيد الإعداد - إجلال</title>
    @include('components.account-theme-head')
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-body), 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            text-align: center;
            color: white;
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        a {
            display: inline-block;
            padding: 12px 30px;
            background: white;
            color: var(--gold);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        a:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔨</div>
        <h1>قيد الإعداد</h1>
        <p>جاري بناء صفحات الطالب الجديدة...</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="padding:12px 30px;background:white;color:var(--gold);border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:16px;font-family:var(--font-body),Tajawal,sans-serif;">
                الخروج
            </button>
        </form>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
