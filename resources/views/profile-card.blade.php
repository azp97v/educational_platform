<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name }} | منصة إجلال التعليمية</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font-body), 'Tajawal', sans-serif;
            background: radial-gradient(circle at top left, rgba(255,214,122,0.18), transparent 22%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 100%);
            color: var(--text-primary); min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 24px;
        }
        .card {
            background: var(--theme-surface); backdrop-filter: blur(24px);
            border: 1px solid var(--border-strong); border-radius: 24px;
            padding: 40px 32px; width: 100%; max-width: 360px; text-align: center;
            box-shadow: var(--shadow);
        }
        .avatar {
            width: 112px; height: 112px; border-radius: 50%; margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: 42px; font-weight: 800; color: #000; overflow: hidden;
            border: 3px solid var(--border-strong);
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .name { font-size: 22px; font-weight: 800; margin-bottom: 8px; }
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 16px; border-radius: 999px; font-size: 13px; font-weight: 700;
            background: var(--gold-light); color: var(--gold);
        }
        .footer { margin-top: 28px; font-size: 12px; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="card">
        <div class="avatar">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $name }}">
            @else
                {{ mb_substr($name, 0, 1) }}
            @endif
        </div>
        <div class="name">{{ $name }}</div>
        <span class="role-badge">
            <i class="ri-{{ $role === 'teacher' ? 'graduation-cap' : 'user' }}-line"></i>
            {{ $role === 'teacher' ? 'معلم' : ($role === 'student' ? 'طالب' : $role) }}
        </span>
        <div class="footer">منصة إجلال التعليمية</div>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
