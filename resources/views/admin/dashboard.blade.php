@extends('layouts.admin')

@section('title', 'لوحة الإدارة')
@section('page_title', 'لوحة القيادة التنفيذية')
@section('page_subtitle', 'مراقبة لحظية واتخاذ قرار سريع على مستوى المنصة بالكامل')

@section('content')
<section class="admin-grid">
    <article class="metric"><div class="k">إجمالي المستخدمين</div><div class="v">{{ $totalUsers }}</div></article>
    <article class="metric"><div class="k">المشرفون</div><div class="v">{{ $admins }}</div></article>
    <article class="metric"><div class="k">المعلمون</div><div class="v">{{ $teachers }}</div></article>
    <article class="metric"><div class="k">الطلاب</div><div class="v">{{ $students }}</div></article>
    <article class="metric"><div class="k">المسارات</div><div class="v">{{ $totalCourses }}</div></article>
    <article class="metric"><div class="k">الدروس</div><div class="v">{{ $totalLessons }}</div></article>
    <article class="metric"><div class="k">الاختبارات</div><div class="v">{{ $totalExams }}</div></article>
    <article class="metric"><div class="k">الشهادات</div><div class="v">{{ $totalCertificates }}</div></article>
</section>

@php
    $total = max(1, (int) $totalUsers);
    $studentsPct = round(($students / $total) * 100);
    $teachersPct = round(($teachers / $total) * 100);
    $activePct = round(($activeUsers / $total) * 100);
@endphp

<section class="admin-form-grid">
    <article class="admin-card">
        <h2>مؤشرات الأداء الحية</h2>
        <div class="admin-form" style="gap:14px;">
            <div>
                <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>نسبة الطلاب</span><span>{{ $studentsPct }}%</span></div>
                <div class="admin-progress"><span style="width: {{ $studentsPct }}%"></span></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>نسبة المعلمين</span><span>{{ $teachersPct }}%</span></div>
                <div class="admin-progress"><span style="width: {{ $teachersPct }}%"></span></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>معدل النشاط</span><span>{{ $activePct }}%</span></div>
                <div class="admin-progress"><span style="width: {{ $activePct }}%"></span></div>
            </div>
        </div>
    </article>

    <article class="admin-card">
        <h2>مختصر التشغيل</h2>
        <div class="admin-grid" style="grid-template-columns:repeat(2,minmax(120px,1fr));">
            <div class="metric"><div class="k">الرسائل</div><div class="v">{{ $totalMessages }}</div></div>
            <div class="metric"><div class="k">الاستفسارات</div><div class="v">{{ $totalInquiries }}</div></div>
            <div class="metric"><div class="k">طلبات معلقة</div><div class="v">{{ $pendingEnrollments }}</div></div>
            <div class="metric"><div class="k">عمليات فاشلة</div><div class="v">{{ $failedJobs }}</div></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.create') }}" class="admin-btn"><i class="ri-user-add-line"></i> إنشاء مستخدم</a>
            <a href="{{ route('admin.analytics') }}" class="admin-btn secondary"><i class="ri-line-chart-line"></i> التحليلات</a>
            <a href="{{ route('admin.liveops') }}" class="admin-btn secondary"><i class="ri-pulse-line"></i> Live Ops</a>
        </div>
    </article>
</section>

<section class="admin-card">
    <h2>جميع الحسابات</h2>
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead><tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>الحالة</th><th>العمليات</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="admin-badge-role role-{{ $user->role }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        <span class="admin-badge-status status-{{ $user->status ?? 'active' }}">
                            {{ $user->status ?? 'active' }}
                        </span>
                    </td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="{{ route('admin.show', $user) }}" class="admin-btn secondary">عرض</a>
                        <a href="{{ route('admin.edit', $user) }}" class="admin-btn secondary">تعديل</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">لا يوجد مستخدمون.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:10px;">{{ $users->onEachSide(1)->links() }}</div>
</section>
@endsection
