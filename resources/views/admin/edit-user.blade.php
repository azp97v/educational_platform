@extends('layouts.admin')
@section('title', 'تعديل مستخدم')
@section('page_title', 'تعديل المستخدم')
@section('page_subtitle', $user->email)
@section('content')
<section class="admin-card glow-card">
  <form class="admin-form" action="{{ route('admin.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="admin-form-grid">
      <div><label>البريد</label><input value="{{ $user->email }}" disabled></div>
      <div><label for="name">الاسم</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
      <div><label for="role">الدور</label><select id="role" name="role" required><option value="admin" @selected(old('role', $user->role)==='admin')>مشرف</option><option value="teacher" @selected(old('role', $user->role)==='teacher')>معلم</option><option value="student" @selected(old('role', $user->role)==='student')>طالب</option></select></div>
      <div><label for="status">الحالة</label><select id="status" name="status" required><option value="active" @selected(old('status', $user->status)==='active')>نشط</option><option value="inactive" @selected(old('status', $user->status)==='inactive')>غير نشط</option><option value="blocked" @selected(old('status', $user->status)==='blocked')>محظور</option></select></div>
      <div id="teacher_id_field" style="{{ old('role', $user->role) === 'student' ? '' : 'display:none' }}"><label for="teacher_id">المعلم المسؤول (اختياري)</label><select id="teacher_id" name="teacher_id"><option value="">بدون ربط</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('teacher_id', $user->teacher_id) == $teacher->id)>{{ $teacher->name }}</option>@endforeach</select></div>
    </div>
    <div class="admin-actions">
      <button class="admin-btn" type="submit">حفظ التعديلات</button>
      <a href="{{ route('admin.index') }}" class="admin-btn secondary">إلغاء</a>
    </div>
  </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('role')?.addEventListener('change', function() {
        document.getElementById('teacher_id_field').style.display = this.value === 'student' ? '' : 'none';
    });
});
</script>
@endsection
