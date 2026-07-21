<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'إجلال') - منصة إجلال التعليمية</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1a3c6e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="إجلال">

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- Master Styles --}}
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --sidebar-width: 240px;
            --topbar-h: 70px;
            --gold: #C6A675;
            --gold-dark: #997722;
            --gold-light: rgba(198,166,117,0.14);
            --bg: #F4F6F8;
            --card-bg: #FFFFFF;
            --text-primary: #222B3D;
            --text-secondary: #5E6675;
            --text-muted: #7D8797;
            --danger: #D64545;
            --danger-light: rgba(214,69,69,0.12);
            --border: #E5E5EA;
            --shadow: 0 4px 24px rgba(0,0,0,0.04);
            --shadow-hover: 0 8px 32px rgba(0,0,0,0.08);
            --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        [data-theme="dark"] {
            --bg: #050505;
            --card-bg: #0F0F10;
            --text-primary: #F2F2F7;
            --text-secondary: #7D8797;
            --text-muted: #636366;
            --border: #2A2F3A;
            --shadow: 0 4px 24px rgba(0,0,0,0.4);
        }

        html, body { 
            height: 100%;
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        a { text-decoration: none; color: inherit; }

        .app {
            display: flex;
            min-height: 100vh;
            flex-direction: row-reverse;
        }

        {{-- الـ Main Content --}}
        .main {
            margin-right: calc(var(--sidebar-width) + var(--sidebar-offset, 0px));
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-right 0.3s;
        }

        {{-- الـ Topbar --}}
        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        {{-- الـ Content --}}
        .content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        .topbar .search-wrap {
            width: 320px;
            max-width: 100%;
            position: relative;
        }

        .topbar .search-wrap::before {
            content: '';
            position: absolute;
            top: -6px;
            left: -6px;
            width: 140px;
            height: 88px;
            pointer-events: none;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.28), transparent 52%);
            opacity: 0.75;
            border-radius: 18px;
            transition: opacity 0.3s ease;
        }

        .topbar .search-wrap input {
            width: 100%;
            padding: 14px 18px 14px 46px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 18px;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }

        .topbar .search-wrap input:focus {
            border-color: rgba(255,255,255,0.35);
            box-shadow: 0 0 0 4px rgba(255,255,255,0.08);
        }

        .topbar .search-wrap .search-icon,
        .topbar .search-wrapper .search-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.7);
            font-size: 16px;
            pointer-events: none;
        }

        .topbar .search-wrapper input {
            width: 100%;
            padding: 14px 18px 14px 46px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 18px;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }

        .topbar .search-wrapper input:focus {
            border-color: rgba(255,255,255,0.35);
            box-shadow: 0 0 0 4px rgba(255,255,255,0.08);
        }

        .search-highlight {
            animation: highlightFlash 1.2s ease;
            outline: 2px solid rgba(196,150,58,0.45);
            outline-offset: 4px;
        }

        @keyframes highlightFlash {
            0% { background: rgba(255,255,255,0.12); }
            50% { background: rgba(196,150,58,0.14); }
            100% { background: transparent; }
        }

        {{-- إخفاء الـ Scrollbar --}}
        .main, .content {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .main::-webkit-scrollbar,
        .content::-webkit-scrollbar {
            display: none;
        }

        {{-- Responsive --}}
        @media (max-width: 1024px) {
            .main {
                margin-right: 72px;
            }

            .topbar {
                padding: 0 16px;
            }

            .content {
                padding: 20px 16px;
            }
        }

        @media (max-width: 768px) {
            .main {
                margin-right: 0;
            }

            .topbar {
                padding: 0 12px;
            }

            .content {
                padding: 16px 12px;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/brand-theme-overrides.css') }}">
    @include('components.account-theme-head')
    
    {{-- Page Styles --}}
    @yield('styles')

    <!-- Sentry Browser SDK -->
    <script src="https://js-de.sentry-cdn.com/021ff6ade06b8bf73a6467b845f06dbc.min.js" crossorigin="anonymous"></script>
    <script>
    if (typeof Sentry !== 'undefined') {
    Sentry.onLoad(function() {
        Sentry.init({
            dsn: "https://021ff6ade06b8bf73a6467b845f06dbc@o4511728095199232.ingest.de.sentry.io/4511728109224016",
            environment: "{{ app()->environment() }}",
            integrations: [Sentry.browserTracingIntegration(), Sentry.replayIntegration({ maskAllText: false })],
            tracesSampleRate: 0.2,
            tracePropagationTargets: ["edu.ejlalmakkah.org.sa"],
            replaysSessionSampleRate: 0.05,
            replaysOnErrorSampleRate: 1.0,
        });
        @auth Sentry.setUser({ id: {{ auth()->id() }}, role: "{{ auth()->user()->role }}" }); @endauth
    });
    }
    </script>
</head>
<body data-role="{{ auth()->user()?->role ?? '' }}">
    {{-- Load Theme ASAP --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 
                              localStorage.getItem('theme') || 
                              sessionStorage.getItem('app-theme') || 
                              sessionStorage.getItem('theme') || 
                              'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <div class="app">
        {{-- Sidebar --}}
        @include('components.sidebar-unified')

        {{-- Main Content --}}
        <main class="main">
            {{-- Topbar --}}
            @if(auth()->check())
                @include('components.topbar', ['skipBellJs' => true])
            @endif

            {{-- Page Content --}}
            <div class="content">
                {{-- Alerts --}}
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="ri-check-line"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="ri-close-line"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Main Yield --}}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Scripts --}}
    @include('components.account-theme-foot')
    @if(auth()->check())
        @include('components.notification-bell')
    @endif
    @yield('scripts')

    <style>.search-hidden{display:none!important}</style>
    <script>
    // Generic page search — runs when topbar fires window.runPageSearch(query)
    (function() {
        const SELECTORS = [
            '.card', '.card-body', '[class*="course-card"]', '[class*="student-row"]',
            'tbody tr', '.list-item', '[class*="-item"]', '[class*="-row"]',
            '.stat-card', 'section', 'article',
        ];
        let _clearTimer = null;
        let _allEls = null; // cached on first search so we can restore hidden ones

        function getAllEls() {
            if (_allEls) return _allEls;
            const seen = new Set();
            const els = [];
            SELECTORS.forEach(sel => {
                document.querySelectorAll('.content ' + sel).forEach(el => {
                    if (!seen.has(el)) { seen.add(el); els.push(el); }
                });
            });
            _allEls = els;
            return els;
        }

        function clearSearch() {
            if (!_allEls) return;
            _allEls.forEach(el => {
                el.classList.remove('search-hidden', 'search-highlight');
            });
        }

        window.runPageSearch = function(query) {
            if (_clearTimer) clearTimeout(_clearTimer);
            if (!query || !query.trim()) { clearSearch(); return; }
            const q = query.trim().toLowerCase();
            const els = getAllEls();
            let firstMatch = null;
            els.forEach(el => {
                const text = el.innerText || el.textContent || '';
                const matches = text.toLowerCase().includes(q);
                el.classList.toggle('search-hidden', !matches);
                el.classList.remove('search-highlight');
                if (matches && !firstMatch) firstMatch = el;
            });
            if (firstMatch) {
                firstMatch.classList.add('search-highlight');
                firstMatch.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                _clearTimer = setTimeout(() => firstMatch.classList.remove('search-highlight'), 1400);
            }
        };
    })();
    </script>
</body>
</html>


