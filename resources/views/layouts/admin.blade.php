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
</head>
<body class="admin-console" data-admin-live="true">
<div class="admin-app">
    @include('admin.partials.sidebar')

    <main class="admin-main">
        @include('admin.partials.topbar')

        <section class="admin-content">
            @if(session('success'))
                <div class="admin-alert admin-alert-success"><strong>نجاح:</strong> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="admin-alert admin-alert-error"><strong>تنبيه:</strong> {{ $errors->first() }}</div>
            @endif

            @yield('content')
        </section>
    </main>
</div>

@include('components.account-theme-foot')
<script src="{{ asset('js/admin-console.js') }}?v={{ @filemtime(public_path('js/admin-console.js')) ?: time() }}"></script>
</body>
</html>
