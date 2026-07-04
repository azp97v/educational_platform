@extends('layouts.admin')

@php
    $tabs = [
        'overview' => ['label' => 'نظرة عامة', 'icon' => 'ri-dashboard-line', 'subtitle' => 'قراءة سريعة لحالة المنصة'],
        'courses' => ['label' => 'الدورات', 'icon' => 'ri-book-open-line', 'subtitle' => 'جودة المسارات وتقدمها'],
        'achievements' => ['label' => 'الإنجازات', 'icon' => 'ri-medal-line', 'subtitle' => 'التحفيز والمكافآت'],
        'settings' => ['label' => 'الإعدادات', 'icon' => 'ri-settings-3-line', 'subtitle' => 'إعدادات الإدارة والتشغيل'],
        'activity_log' => ['label' => 'سجل النشاط', 'icon' => 'ri-time-line', 'subtitle' => 'أحدث العمليات والتغييرات'],
    ];
    $active = $tabs[$page] ?? $tabs['overview'];
@endphp

@section('title', $active['label'] . ' | إدارة إجلال')
@section('page_title', $active['label'])
@section('page_subtitle', $active['subtitle'])

@section('content')
<section class="admin-profile-header glass-card">
    <div class="cover"></div>
    <div class="profile-content">
        <div class="avatar-block">
            <div class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <h2>{{ auth()->user()->name }}</h2>
                <div class="badges">
                    <span class="badge active">نشط</span>
                    <span class="badge role">مدير</span>
                </div>
                <p>{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="quick-actions">
            <a href="{{ route('admin.index') }}" class="admin-btn secondary">العودة للوحة</a>
        </div>
    </div>
</section>

<section class="admin-tabs glass-card">
    @foreach($tabs as $key => $tab)
        <a href="{{ route('admin.console', ['page' => $key]) }}" class="tab-link {{ $page === $key ? 'active' : '' }}">
            <i class="{{ $tab['icon'] }}"></i>
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</section>

@if($page === 'overview')
<section class="admin-overview-grid">
    <article class="glass-card stat-card"><i class="ri-group-line"></i><div><small>إجمالي المستخدمين</small><strong>{{ $totalUsers }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-user-star-line"></i><div><small>الطلاب</small><strong>{{ $students }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-user-voice-line"></i><div><small>المعلمون</small><strong>{{ $teachers }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-book-open-line"></i><div><small>المسارات</small><strong>{{ $totalCourses }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-file-list-3-line"></i><div><small>الاختبارات</small><strong>{{ $totalExams }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-award-line"></i><div><small>الشهادات</small><strong>{{ $totalCertificates }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-message-3-line"></i><div><small>الرسائل</small><strong>{{ $totalMessages }}</strong></div></article>
    <article class="glass-card stat-card"><i class="ri-questionnaire-line"></i><div><small>الاستفسارات</small><strong>{{ $totalInquiries }}</strong></div></article>
</section>
@endif

@if($page === 'courses')
<section class="glass-card panel">
    <h3>حالة الدورات</h3>
    <div class="course-list">
        @forelse($coursesData as $c)
            <div class="course-item">
                <div class="row-1">
                    <strong>{{ $c['name'] }}</strong>
                    <span class="badge role">{{ $c['status'] }}</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress-bar"><span style="width: {{ $c['progress'] }}%"></span></div>
                    <b>{{ $c['progress'] }}%</b>
                </div>
            </div>
        @empty
            <p class="muted">لا توجد دورات لعرضها حالياً.</p>
        @endforelse
    </div>
</section>
@endif

@if($page === 'achievements')
<section class="glass-card panel">
    <h3>الإنجازات</h3>
    <div class="achievement-grid">
        @forelse($achievementsData as $a)
            <article class="achievement-card">
                <i class="ri-medal-line"></i>
                <strong>{{ $a['name'] }}</strong>
                <small>{{ $a['date'] }}</small>
            </article>
        @empty
            <p class="muted">لا توجد إنجازات معرفة حالياً.</p>
        @endforelse
    </div>
</section>
@endif

@if($page === 'settings')
<section class="glass-card panel">
    <h3>إعدادات الإدارة</h3>
    <form class="admin-form admin-form-grid">
        <label>اسم المنصة<input type="text" value="{{ config('app.name') }}" readonly></label>
        <label>المنطقة الزمنية<input type="text" value="{{ config('app.timezone') }}" readonly></label>
        <label>مزود البريد<input type="text" value="{{ config('mail.default') }}" readonly></label>
        <label>عدد الوظائف الفاشلة<input type="text" value="{{ $failedJobs }}" readonly></label>
        <label>طلبات التسجيل المعلقة<input type="text" value="{{ $pendingEnrollments }}" readonly></label>
        <label>التذاكر المفتوحة<input type="text" value="{{ $openTickets }}" readonly></label>
    </form>
</section>
@endif

@if($page === 'activity_log')
<section class="glass-card panel">
    <h3>سجل النشاط</h3>
    <div class="timeline">
        @forelse($activityData as $item)
            <div class="timeline-item">
                <span class="dot"></span>
                <div class="entry">
                    <strong>{{ $item['title'] }}</strong>
                    <small>{{ $item['time'] }}</small>
                </div>
            </div>
        @empty
            <p class="muted">لا توجد أحداث حديثة.</p>
        @endforelse
    </div>
</section>
@endif
@endsection
