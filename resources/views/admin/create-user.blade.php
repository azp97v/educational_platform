@extends('layouts.admin')
@section('title', 'إنشاء مستخدم')
@section('page_title', 'إنشاء مستخدم جديد')
@section('page_subtitle', 'إضافة حساب جديد ضمن أدوار المنصة')
@section('content')
<section class="admin-card glow-card">
  <form class="admin-form" action="{{ route('admin.store') }}" method="POST">
    @csrf
    <div class="admin-form-grid">
      <div><label for="name">الاسم</label><input id="name" name="name" value="{{ old('name') }}" required></div>
      <div>
        <label for="username">اسم المستخدم (User Name) <span style="color:var(--danger)">*</span></label>
        <input id="username" name="username" value="{{ old('username') }}" placeholder="مثال: ahmed_2024" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_.]+" style="padding-right:70px;">
        <span id="usernameStatus" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:700;"></span>
        <small style="color:var(--text-muted);font-size:11px;">يُستخدم للوصول إليك في تطبيق المراسلة. يجب أن يكون فريداً.</small>
      </div>
      <div><label for="email">البريد الإلكتروني</label><input id="email" name="email" type="email" value="{{ old('email') }}" required></div>
      <div><label for="password">كلمة المرور</label><input id="password" name="password" type="password" required></div>
      <div><label for="password_confirmation">تأكيد كلمة المرور</label><input id="password_confirmation" name="password_confirmation" type="password" required></div>
      <div><label for="role">الدور</label><select id="role" name="role" required><option value="admin">مشرف</option><option value="teacher">معلم</option><option value="student" selected>طالب</option></select></div>
      <div id="teacher_id_field"><label for="teacher_id">المعلم المسؤول (اختياري)</label><select id="teacher_id" name="teacher_id"><option value="">بدون ربط</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->name }}</option>@endforeach</select></div>
    </div>
    <div class="admin-actions">
      <button class="admin-btn" type="submit">حفظ المستخدم</button>
      <a href="{{ route('admin.index') }}" class="admin-btn secondary">إلغاء</a>
    </div>
  </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('role')?.addEventListener('change', function() {
        document.getElementById('teacher_id_field').style.display = this.value === 'student' ? '' : 'none';
    });

    var usernameInput = document.getElementById('username');
    var usernameStatus = document.getElementById('usernameStatus');
    var checkTimer = null;
    var parentDiv = usernameInput ? usernameInput.parentElement : null;
    if (parentDiv) parentDiv.style.position = 'relative';

    if (usernameInput && usernameStatus) {
        usernameInput.addEventListener('input', function() {
            var val = this.value.trim();
            if (checkTimer) clearTimeout(checkTimer);
            usernameStatus.textContent = '';
            usernameStatus.style.color = '';

            if (val.length < 3) {
                usernameStatus.textContent = '3+ أحرف';
                usernameStatus.style.color = 'var(--text-muted)';
                return;
            }

            checkTimer = setTimeout(function() {
                usernameStatus.textContent = '...';
                usernameStatus.style.color = 'var(--text-muted)';
                fetch('{{ route("admin.check-username") }}?username=' + encodeURIComponent(val), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.available) {
                        usernameStatus.textContent = '✓ متوفر';
                        usernameStatus.style.color = 'var(--success, #06a77d)';
                    } else {
                        usernameStatus.textContent = '❌ محجوز';
                        usernameStatus.style.color = 'var(--danger, #D32F2F)';
                    }
                })
                .catch(function() {
                    usernameStatus.textContent = '';
                });
            }, 400);
        });
    }
});
</script>
@endsection
