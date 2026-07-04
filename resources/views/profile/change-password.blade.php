<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--text-primary);
        }

        .topbar {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: linear-gradient(135deg, rgba(10, 20, 44, 0.92), rgba(8, 16, 36, 0.96));
            border-bottom: 1px solid rgba(198, 117, 46, 0.22);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-wrap {
            flex: 1;
            max-width: 450px;
            position: relative;
        }

        .search-wrap input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(198,117,46,0.25);
            border-radius: 20px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }

        .search-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 2px var(--gold-light);
        }

        .search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.03);
            color: #d9deea;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .icon-btn:hover {
            color: var(--gold);
            background: var(--gold-light);
        }

        .g-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--bg);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            color: var(--text-primary);
        }

        .g-xp i {
            color: var(--gold);
            font-size: 14px;
        }

        .u-av {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--gold);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
        }

        .u-av-img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid var(--gold);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card-bg);
            color: var(--gold);
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            margin-bottom: 24px;
            transition: var(--transition);
            border: 1px solid var(--gold-light);
            box-shadow: var(--shadow);
        }

        .back-button:hover {
            box-shadow: var(--shadow-hover);
            transform: translateX(-5px);
        }

        .panel {
            max-width: 760px;
            margin: 0 auto;
            background: var(--card-bg);
            border: 1px solid var(--gold-light);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .panel-header {
            margin-bottom: 22px;
        }

        .panel-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-title i {
            color: var(--gold);
        }

        .panel-subtitle {
            margin-top: 8px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 14px;
            border: 1px solid;
            font-size: 13px;
            font-weight: 600;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(52,199,89,0.12), rgba(52,199,89,0.06));
            border-color: rgba(52,199,89,0.25);
            color: #7df29a;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(198,166,117,0.12), rgba(198,166,117,0.06));
            border-color: rgba(198,166,117,0.25);
            color: #C6A675;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(255,59,48,0.12), rgba(255,59,48,0.06));
            border-color: rgba(255,59,48,0.25);
            color: #ff9f99;
        }

        .alert-danger ul {
            margin: 0;
            padding-right: 18px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--gold-light);
            background: rgba(255,255,255,0.04);
            color: var(--text-primary);
            padding: 12px 14px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            transition: var(--transition);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(198,117,46,0.12);
        }

        .hint {
            margin-top: 6px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .field-error {
            margin-top: 5px;
            color: #ff9f99;
            font-size: 12px;
            font-weight: 700;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
            box-shadow: 0 10px 25px rgba(198,117,46,0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(198,117,46,0.35);
        }

        .btn-secondary {
            background: var(--gold-light);
            color: var(--gold);
            border: 1px solid var(--gold);
        }

        .btn-secondary:hover {
            background: var(--gold);
            color: #fff;
        }

        @media (max-width: 768px) {
            .panel {
                padding: 22px;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }

        /* ===== Unified Theme Override ===== */
        body {
            background: var(--theme-page-bg) !important;
            color: var(--theme-text) !important;
        }

        .topbar {
            background: linear-gradient(90deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%) !important;
            border-bottom: 1px solid var(--theme-border) !important;
            box-shadow: var(--theme-shadow-soft) !important;
        }

        .icon-btn {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            border: 1px solid var(--theme-border) !important;
            background: var(--theme-surface-2) !important;
            color: var(--theme-gold) !important;
        }

        .search-wrap input {
            background: var(--theme-surface) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-text) !important;
        }

        .search-wrap input:focus {
            background: var(--theme-surface-2) !important;
            border-color: var(--theme-gold) !important;
        }

        .g-badge {
            background: var(--theme-surface-2) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-gold) !important;
        }

        .panel,
        .back-button {
            background: var(--theme-surface) !important;
            border: 1px solid var(--theme-border) !important;
        }

        .alert-info,
        .alert-success,
        .alert-danger {
            background: var(--theme-surface-2) !important;
            border-color: var(--theme-border) !important;
            color: var(--theme-text-soft) !important;
        }

        .form-input {
            background: var(--theme-surface-2) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-text) !important;
        }

        .form-input:focus {
            border-color: var(--theme-gold) !important;
            box-shadow: 0 0 0 3px var(--theme-gold-soft) !important;
        }

        .panel-title,
        .form-label {
            color: var(--theme-text) !important;
        }

        .panel-subtitle,
        .hint {
            color: var(--theme-text-soft) !important;
        }

        @media (max-width: 480px) {
            .panel { padding: 16px; }
        }
    </style>
</head>
<body>
    <script>
        (function() {
            let savedTheme = localStorage.getItem('theme');
            if (!savedTheme) {
                savedTheme = '{{ auth()->user()->role === "teacher" ? "dark" : "light" }}';
                localStorage.setItem('theme', savedTheme);
            }
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>

    <header class="topbar">
        <div class="topbar-left">
            <a href="{{ auth()->user()->role === 'teacher' ? route('teacher.dashboard') : route('student.index') }}" class="icon-btn" title="الرجوع">
                <i class="ri-arrow-right-line"></i>
            </a>
            <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
                <i class="ri-moon-line" id="darkIcon"></i>
            </button>
            <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
                <i class="ri-notification-3-line"></i>
            </button>
            @include('components.notification-bell')
            @if(auth()->user()->role === 'student')
                <div class="g-badge g-xp">
                    <span id="userXP">{{ auth()->user()->points ?? 0 }}</span>
                    <i class="ri-flashlight-fill"></i>
                </div>
            @endif
        </div>

        <div class="search-wrap">
            <input type="text" placeholder="بحث...">
            <i class="ri-search-line search-icon"></i>
        </div>

        <div class="topbar-right">
            @if(auth()->user()->avatar_url)
                <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" alt="{{ auth()->user()->name }}" class="u-av-img">
            @else
                <div class="u-av">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            @endif
        </div>
    </header>

    <div class="container">
        <a href="{{ route('profile.show') }}" class="back-button">
            <i class="ri-arrow-right-line"></i> العودة
        </a>

        <section class="panel">
            <div class="panel-header">
                <h1 class="panel-title"><i class="ri-lock-password-line"></i> تغيير كلمة المرور</h1>
                <p class="panel-subtitle">حدّث كلمة المرور للحفاظ على أمان الحساب.</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="alert alert-info">
                يُفضّل أن تحتوي كلمة المرور على حروف كبيرة وصغيرة وأرقام ورموز خاصة.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">كلمة المرور الحالية</label>
                    <input class="form-input" type="password" id="current_password" name="current_password" required>
                    <div class="hint">أدخل كلمة المرور الحالية للتحقق من الهوية.</div>
                    @error('current_password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">كلمة المرور الجديدة</label>
                    <input class="form-input" type="password" id="password" name="password" required>
                    <div class="hint">اجعلها قوية وصعبة التخمين.</div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                    <input class="form-input" type="password" id="password_confirmation" name="password_confirmation" required>
                    <div class="hint">أعد إدخال كلمة المرور الجديدة للتأكيد.</div>
                    @error('password_confirmation')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-check-line"></i> تحديث كلمة المرور
                    </button>
                    <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                        <i class="ri-close-line"></i> إلغاء
                    </a>
                </div>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateDarkModeIcon();
            document.getElementById('darkBtn')?.addEventListener('click', function() {
                if (typeof toggleDarkMode === 'function') toggleDarkMode();
            });
        });

        function toggleDarkMode() {
            if (typeof window.toggleThemeUniversal === 'function') {
                window.toggleThemeUniversal();
            } else {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
                localStorage.setItem('theme', isDark ? 'light' : 'dark');
            }
            updateDarkModeIcon();
        }

        function updateDarkModeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const darkIcon = document.getElementById('darkIcon');
            if (darkIcon) {
                darkIcon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
            }
        }
    </script>
    @include('components.account-theme-foot')
</body>
</html>




