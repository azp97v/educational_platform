@extends('layouts.admin')
@section('title', 'إعدادات الإدارة')
@section('page_title', 'إعدادات المنصة')
@section('page_subtitle', 'تحكم شامل بهوية وتشغيل المنصة')
@section('content')
<style>
.admin-toggle{display:flex;align-items:center;gap:10px;padding:12px;background:var(--surface-2);border:1px solid var(--border-light);border-radius:12px;cursor:pointer;transition:all .2s;user-select:none}
.admin-toggle:hover{border-color:var(--gold);background:var(--gold-soft)}
.admin-toggle input[type="checkbox"]{width:20px;height:20px;accent-color:var(--gold);cursor:pointer}
.admin-toggle span{font-weight:600;color:var(--text);font-size:.95rem}
</style>
<section class="admin-grid">
    <article class="metric"><div class="k">المستخدمون</div><div class="v">{{ $totalUsers }}</div></article>
    <article class="metric"><div class="k">المسارات</div><div class="v">{{ $totalCourses }}</div></article>
    <article class="metric"><div class="k">الدروس</div><div class="v">{{ $totalLessons }}</div></article>
    <article class="metric"><div class="k">الاختبارات</div><div class="v">{{ $totalExams }}</div></article>
</section>

<section class="admin-card" style="margin-top:12px;">
    <h2>الإعدادات العامة</h2>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="admin-form-grid">
            @foreach(['platform_name', 'timezone', 'locale', 'session_timeout', 'max_login_attempts'] as $key)
                @php($setting = $settings->firstWhere('key', $key))
                <div>
                    <label>{{ $setting->label ?? $key }}</label>
                    <input name="{{ $key }}" value="{{ old($key, $setting->value ?? '') }}"
                           type="{{ in_array($key, ['session_timeout', 'max_login_attempts']) ? 'number' : 'text' }}"
                           min="{{ $key === 'session_timeout' ? 5 : ($key === 'max_login_attempts' ? 1 : '') }}"
                           max="{{ $key === 'session_timeout' ? 1440 : ($key === 'max_login_attempts' ? 20 : '') }}">
                </div>
            @endforeach
        </div>

        <h2 style="margin-top:24px;">ميزات المنصة</h2>
        <div class="admin-form-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr));">
            @foreach(['registration_enabled', 'smart_rewind_enabled', 'certificates_enabled', 'gamification_enabled'] as $key)
                @php($setting = $settings->firstWhere('key', $key))
                <label class="admin-toggle">
                    <input type="hidden" name="{{ $key }}" value="0">
                    <input type="checkbox" name="{{ $key }}" value="1" {{ old($key, $setting->value ?? '0') === '1' ? 'checked' : '' }}>
                    <span>{{ $setting->label ?? $key }}</span>
                </label>
            @endforeach
        </div>

        <div style="margin-top:20px;display:flex;gap:10px;">
            <button type="submit" class="admin-btn"><i class="ri-save-line"></i> حفظ الإعدادات</button>
        </div>
    </form>
</section>
@endsection