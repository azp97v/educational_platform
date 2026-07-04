@extends('layouts.admin')
@section('title', 'ملف المستخدم')
@section('page_title', 'تفاصيل المستخدم')
@section('page_subtitle', $user->name)
@section('content')
<section class="admin-card glow-card">
  <div class="admin-form-grid">
    <div><label>الاسم</label><input value="{{ $user->name }}" disabled></div>
    <div><label>البريد</label><input value="{{ $user->email }}" disabled></div>
    <div><label>الدور</label><input value="{{ $user->role }}" disabled></div>
    <div><label>الحالة</label><input value="{{ $user->status ?? 'active' }}" disabled></div>
    <div><label>تاريخ الإنشاء</label><input value="{{ $user->created_at->format('Y-m-d H:i') }}" disabled></div>
    <div><label>آخر تحديث</label><input value="{{ $user->updated_at->format('Y-m-d H:i') }}" disabled></div>
  </div>
  <div class="admin-actions">
    <a href="{{ route('admin.edit', $user) }}" class="admin-btn">تعديل</a>
  </div>
</section>
@endsection
