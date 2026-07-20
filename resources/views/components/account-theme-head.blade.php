<script nonce="{{ $cspNonce ?? '' }}">
    (function () {
        var currentRole = @json(auth()->check() ? auth()->user()->role : null);
        function safeGet(storage, key) {
            try { return storage.getItem(key); } catch (_) { return null; }
        }
        function safeSet(storage, key, value) {
            try { storage.setItem(key, value); } catch (_) {}
        }

        var theme = safeGet(localStorage, 'app-theme') ||
            safeGet(localStorage, 'theme') ||
            safeGet(sessionStorage, 'app-theme') ||
            safeGet(sessionStorage, 'theme') ||
            'light';

        theme = theme === 'dark' ? 'dark' : 'light';
        safeSet(localStorage, 'app-theme', theme);
        safeSet(localStorage, 'theme', theme);
        safeSet(sessionStorage, 'app-theme', theme);
        safeSet(sessionStorage, 'theme', theme);
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.classList.toggle('dark-mode', theme === 'dark');
        if (currentRole) {
            document.documentElement.setAttribute('data-user-role', currentRole);
        }
        document.documentElement.classList.toggle('teacher-account', currentRole === 'teacher');
    })();
</script>
@php($accountThemeCssVersion = @filemtime(public_path('css/account-theme-unified.css')) ?: time())
@php($mobileResponsiveCssVersion = @filemtime(public_path('css/mobile-responsive.css')) ?: time())
<link rel="stylesheet" href="{{ asset('css/account-theme-unified.css') }}?v={{ $accountThemeCssVersion }}">
<link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}?v={{ $mobileResponsiveCssVersion }}">
