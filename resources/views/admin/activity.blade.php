@extends('layouts.admin')
@section('title', 'سجل النشاط')
@section('page_title', 'سجل النشاط')
@section('page_subtitle', 'تعقب آخر التغييرات')
@section('content')
<section class="admin-card">
    <h2>أحدث النشاطات</h2>
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead><tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>الحالة</th><th>آخر تحديث</th></tr></thead>
            <tbody>
            @forelse($recentUsers as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->role }}</td>
                    <td>{{ $u->status ?? 'active' }}</td>
                    <td>{{ $u->updated_at?->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="5">لا توجد بيانات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
