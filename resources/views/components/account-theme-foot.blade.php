@include('components.announcement-banner')
@include('components.search-bar')
@php($accountThemeJsVersion = @filemtime(public_path('js/account-theme-unified.js')) ?: time())
<script src="{{ asset('js/account-theme-unified.js') }}?v={{ $accountThemeJsVersion }}"></script>
