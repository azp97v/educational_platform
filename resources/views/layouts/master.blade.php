<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'منصة إجلال التعليمية - نظام تعليمي ذكي من جمعية إجلال مكة المكرمة')">
    <meta name="robots" content="@yield('robots', 'noindex, nofollow')">
    <title>@yield('title', 'إجلال') - منصة إجلال التعليمية</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta property="og:site_name" content="منصة إجلال التعليمية">
    <meta property="og:title" content="@yield('title', 'إجلال') - منصة إجلال التعليمية">
    <meta property="og:description" content="@yield('description', 'منصة إجلال التعليمية - نظام تعليمي ذكي من جمعية إجلال مكة المكرمة')">
    <meta property="og:image" content="{{ asset('images/logo/logo.png') }}">
    <meta property="og:type" content="website">

    <!-- ًںŒ™ Load Theme ASAP (prevent flash) -->
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

    <!-- ًں“ڑ Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- ًںژ¨ Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    <!-- ًںژ¯ Master Styles -->
    <link rel="stylesheet" href="{{ asset('css/master.css') }}">

    <!-- ï؟½ Layout Styles -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand-theme-overrides.css') }}">
    @include('components.account-theme-head')

    <style>
        .notification-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        .notification-wrapper .notification-btn {
            position: relative;
        }
        .notification-badge {
            position: absolute;
            top: -6px;
            left: -6px;
            min-width: 20px;
            height: 20px;
            background-color: #EF4444;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            padding: 0 5px;
            line-height: 1;
            pointer-events: none;
        }
    </style>

    <!-- ï؟½ًں“„ Page-specific Styles -->
    @yield('styles')

    <!-- CSRF Token for Forms -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <!-- ًںژ¯ Layout Container -->
    <div class="app-container">

        <!-- ًں“± Sidebar Navbar -->
        @if(auth()->check())
            @include('components.sidebar')
        @endif

        <!-- ًں“– Main Content -->
        <main class="app-main">

            <!-- ⬆️ Top Bar -->
            @if(auth()->check())
                @include('components.topbar')
            @endif

            <!-- ًں“„ Page Content -->
            <div class="page-content">
                <!-- ًں”” Alerts/Notifications -->
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
                    <div class="alert alert-success animate-slideInDown">
                        <i class="ri-check-line"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger animate-slideInDown">
                        <i class="ri-close-line"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning animate-slideInDown">
                        <i class="ri-alert-line"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                <!-- ًں“‹ Main Content -->
                @yield('content')
            </div>

            <!-- ًں”— Footer -->
            @include('components.footer')
        </main>
    </div>

    <!-- 🎬 Master JavaScript -->
    @include('components.account-theme-foot')
    <script src="{{ asset('js/master.js') }}"></script>

    <!-- ًں“„ Page-specific Scripts -->
    @yield('scripts')

    <!-- ًںژ¯ Global App Scripts -->
    <script>
        // Initialize CSRF token for AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) {
            fetch.defaults = { headers: { 'X-CSRF-TOKEN': token } };
        }

        // Log app initialization
        console.log('App initialized');
    </script>

</body>

</html>



