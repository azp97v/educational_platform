@extends('layouts.admin')
@section('title', 'التحليلات')
@section('page_title', 'مركز التحليلات')
@section('page_subtitle', 'قراءة تشغيلية وتعليمية دقيقة')

@php
    $total = max(1, (int) $kpis['total_users']);
    $adminsPct = round(($roleDistribution['admins'] / $total) * 100);
    $teachersPct = round(($roleDistribution['teachers'] / $total) * 100);
    $studentsPct = round(($roleDistribution['students'] / $total) * 100);
@endphp

@section('content')
<section class="admin-grid">
    <article class="metric"><div class="k">إجمالي المستخدمين</div><div class="v">{{ $kpis['total_users'] }}</div></article>
    <article class="metric"><div class="k">الرسائل</div><div class="v">{{ $kpis['total_messages'] }}</div></article>
    <article class="metric"><div class="k">الاختبارات</div><div class="v">{{ $kpis['total_exams'] }}</div></article>
    <article class="metric"><div class="k">الاستفسارات</div><div class="v">{{ $kpis['total_inquiries'] }}</div></article>
</section>

<section class="admin-card">
    <h2>التوزيع النسبي للأدوار</h2>
    <div class="admin-form-grid">
        <div><div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>المشرفون</span><span>{{ $adminsPct }}%</span></div><div class="admin-progress"><span style="width:{{ $adminsPct }}%"></span></div></div>
        <div><div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>المعلمون</span><span>{{ $teachersPct }}%</span></div><div class="admin-progress"><span style="width:{{ $teachersPct }}%"></span></div></div>
        <div><div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>الطلاب</span><span>{{ $studentsPct }}%</span></div><div class="admin-progress"><span style="width:{{ $studentsPct }}%"></span></div></div>
        <div><div style="display:flex;justify-content:space-between;font-weight:800;margin-bottom:8px;"><span>طلبات معلقة</span><span>{{ $kpis['pending_enrollments'] }}</span></div><div class="admin-progress"><span style="width:{{ min(100,$kpis['pending_enrollments']*5) }}%"></span></div></div>
    </div>
</section>

<section class="admin-card">
    <h2>أحدث المستخدمين</h2>
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead><tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>آخر انضمام</th></tr></thead>
            <tbody>
            @forelse($latestUsers as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->created_at?->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="4">لا توجد بيانات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
