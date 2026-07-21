<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة الإدارة | جمعية إجلال')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-console.css') }}?v={{ @filemtime(public_path('css/admin-console.css')) ?: time() }}">
    <!-- Sentry Browser SDK -->
    <script src="https://js-de.sentry-cdn.com/021ff6ade06b8bf73a6467b845f06dbc.min.js" crossorigin="anonymous"></script>
    <script>
    if (typeof Sentry !== 'undefined') {
    Sentry.onLoad(function() {
        Sentry.init({
            dsn: "https://021ff6ade06b8bf73a6467b845f06dbc@o4511728095199232.ingest.de.sentry.io/4511728109224016",
            environment: "{{ app()->environment() }}",
            integrations: [Sentry.browserTracingIntegration()],
            tracesSampleRate: 0.2,
            tracePropagationTargets: ["edu.ejlalmakkah.org.sa"],
        });
        @auth Sentry.setUser({ id: {{ auth()->id() }}, role: "admin" }); @endauth
    });
    }
    </script>
</head>
<body class="admin-console" data-admin-live="true" data-role="admin">
<div class="admin-app">
    @include('admin.partials.sidebar')

    <main class="admin-main">
        @include('admin.partials.topbar')

        <section class="admin-content">
            @if(session('success'))
                <div class="admin-alert admin-alert-success"><i class="ri-checkbox-circle-line"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="admin-alert admin-alert-error"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="admin-alert" style="background:rgba(245,158,11,0.12);border-right:3px solid #f59e0b;color:#f59e0b;padding:10px 16px;border-radius:8px;margin-bottom:12px;font-size:13px;"><i class="ri-alert-line"></i> {{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="admin-alert admin-alert-error"><i class="ri-close-circle-line"></i> {{ $errors->first() }}</div>
            @endif

            @yield('content')
        </section>
    </main>
</div>

@include('components.account-theme-foot')
<script src="{{ asset('js/admin-console.js') }}?v={{ @filemtime(public_path('js/admin-console.js')) ?: time() }}"></script>
</body>
</html>
