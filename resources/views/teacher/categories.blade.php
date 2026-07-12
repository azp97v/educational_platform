@extends('layouts.app-unified')

@section('title', 'إدارة الفئات')

@push('styles')
<style>
.cat-page { max-width: 720px; margin: 0 auto; padding: 32px 16px; }
.cat-header { margin-bottom: 28px; }
.cat-header h1 { font-size: 26px; font-weight: 800; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark, #b8861a) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 6px; }
.cat-header p { color: var(--text-muted, #888); font-size: 14px; }

/* Add form */
.cat-add-card {
  background: var(--theme-surface, #1e1e2e);
  border: 1px solid rgba(198,166,117,.18);
  border-radius: 18px;
  padding: 22px 24px;
  margin-bottom: 28px;
}
.cat-add-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; color: var(--gold); }
.cat-add-row { display: flex; gap: 10px; }
.cat-add-row input[type="text"] {
  flex: 1; padding: 11px 14px; border-radius: 12px; border: 1.5px solid rgba(198,166,117,.25);
  background: var(--theme-bg, #111); color: inherit; font-family: Tajawal, sans-serif; font-size: 14px;
  outline: none; transition: border-color .2s;
}
.cat-add-row input[type="text"]:focus { border-color: var(--gold); }
.cat-add-row button {
  padding: 11px 20px; border-radius: 12px; border: none; background: rgba(198,166,117,.15);
  color: var(--gold); font-weight: 700; font-size: 14px; cursor: pointer; font-family: Tajawal, sans-serif;
  display: flex; align-items: center; gap: 6px; transition: background .2s, transform .15s;
  white-space: nowrap;
}
.cat-add-row button:hover { background: rgba(198,166,117,.28); transform: translateY(-1px); }

/* List */
.cat-list { display: flex; flex-direction: column; gap: 10px; }
.cat-item {
  background: var(--theme-surface, #1e1e2e);
  border: 1px solid rgba(198,166,117,.1);
  border-radius: 14px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: border-color .2s, box-shadow .2s;
}
.cat-item:hover { border-color: rgba(198,166,117,.3); box-shadow: 0 4px 20px rgba(198,166,117,.08); }
.cat-item-icon { color: var(--gold); font-size: 18px; flex-shrink: 0; }
.cat-item-name { flex: 1; font-weight: 600; font-size: 15px; }
.cat-item-count { font-size: 12px; color: var(--text-muted, #888); margin-left: 8px; white-space: nowrap; }
.cat-item-actions { display: flex; gap: 6px; flex-shrink: 0; }

/* Edit inline form */
.cat-edit-form { display: none; flex: 1; align-items: center; gap: 8px; }
.cat-edit-form.open { display: flex; }
.cat-item.editing .cat-item-name { display: none; }
.cat-item.editing .cat-edit-form { display: flex; }
.cat-edit-form input {
  flex: 1; padding: 8px 12px; border-radius: 10px; border: 1.5px solid var(--gold);
  background: var(--theme-bg, #111); color: inherit; font-family: Tajawal, sans-serif; font-size: 14px; outline: none;
}
.cat-edit-form button { padding: 8px 14px; border-radius: 10px; border: none; cursor: pointer; font-family: Tajawal, sans-serif; font-size: 13px; font-weight: 700; }
.btn-save-edit { background: rgba(198,166,117,.2); color: var(--gold); }
.btn-save-edit:hover { background: rgba(198,166,117,.35); }
.btn-cancel-edit { background: rgba(255,255,255,.06); color: var(--text-muted, #888); }
.btn-cancel-edit:hover { background: rgba(255,255,255,.1); }

/* Action buttons */
.btn-icon {
  width: 34px; height: 34px; border-radius: 10px; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; font-size: 16px;
  transition: background .2s, color .2s, transform .15s;
}
.btn-icon:hover { transform: translateY(-1px); }
.btn-edit-cat  { background: rgba(198,166,117,.1); color: var(--gold); }
.btn-edit-cat:hover { background: rgba(198,166,117,.25); }
.btn-del-cat   { background: rgba(255,59,48,.1); color: #ff3b30; }
.btn-del-cat:hover { background: rgba(255,59,48,.22); }

/* Alerts */
.alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 18px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.alert-success { background: rgba(52,199,89,.12); border: 1px solid rgba(52,199,89,.3); color: #34c759; }
.alert-error   { background: rgba(255,59,48,.12); border: 1px solid rgba(255,59,48,.3); color: #ff3b30; }
.cat-empty { text-align: center; padding: 40px; color: var(--text-muted, #888); font-size: 14px; }
.cat-empty i { font-size: 40px; opacity: .3; display: block; margin-bottom: 10px; }

@media (max-width: 480px) {
  .cat-add-row { flex-direction: column; }
  .cat-item { flex-wrap: wrap; }
}
</style>
@endpush

@section('content')
<div class="cat-page">
  <div class="cat-header">
    <h1><i class="ri-price-tag-3-line"></i> إدارة الفئات</h1>
    <p>أضف فئات جديدة أو عدّل الموجودة — تظهر في قوائم إنشاء وتعديل المسارات</p>
  </div>

  @if(session('success'))
    <div class="alert alert-success"><i class="ri-checkbox-circle-line"></i> {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-error"><i class="ri-error-warning-line"></i> {{ $errors->first() }}</div>
  @endif

  <!-- Add Category -->
  <div class="cat-add-card">
    <h3><i class="ri-add-circle-line"></i> إضافة فئة جديدة</h3>
    <form action="{{ route('teacher.categories.store') }}" method="POST">
      @csrf
      <div class="cat-add-row">
        <input type="text" name="name" placeholder="اسم الفئة (مثل: الفقه الإسلامي)" maxlength="100"
               value="{{ old('name') }}" required autofocus>
        <button type="submit"><i class="ri-add-line"></i> إضافة</button>
      </div>
    </form>
  </div>

  <!-- Categories List -->
  <div class="cat-list">
    @forelse($categories as $cat)
      <div class="cat-item" id="cat-row-{{ $cat->id }}">
        <i class="ri-price-tag-3-line cat-item-icon"></i>

        <span class="cat-item-name">{{ $cat->name }}</span>
        <span class="cat-item-count">{{ $cat->courses_count }} {{ $cat->courses_count == 1 ? 'مسار' : 'مسارات' }}</span>

        <!-- Inline edit form -->
        <form class="cat-edit-form" action="{{ route('teacher.categories.update', $cat->id) }}" method="POST">
          @csrf @method('PUT')
          <input type="text" name="name" value="{{ $cat->name }}" maxlength="100" required>
          <button type="submit" class="btn-save-edit">حفظ</button>
          <button type="button" class="btn-cancel-edit" onclick="cancelEdit({{ $cat->id }})">إلغاء</button>
        </form>

        <div class="cat-item-actions">
          <button class="btn-icon btn-edit-cat" onclick="startEdit({{ $cat->id }})" title="تعديل">
            <i class="ri-edit-line"></i>
          </button>
          @if($cat->courses_count === 0)
            <form action="{{ route('teacher.categories.destroy', $cat->id) }}" method="POST"
                  onsubmit="return confirm('حذف الفئة «{{ addslashes($cat->name) }}»؟')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-icon btn-del-cat" title="حذف">
                <i class="ri-delete-bin-line"></i>
              </button>
            </form>
          @else
            <button class="btn-icon btn-del-cat" disabled title="مرتبطة بمسارات — لا يمكن الحذف" style="opacity:.35;cursor:not-allowed;">
              <i class="ri-delete-bin-line"></i>
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="cat-empty">
        <i class="ri-price-tag-3-line"></i>
        لا توجد فئات بعد — أضف أولى الفئات من الأعلى
      </div>
    @endforelse
  </div>
</div>

<script>
function startEdit(id) {
  const row = document.getElementById('cat-row-' + id);
  row.classList.add('editing');
  row.querySelector('.cat-edit-form input').focus();
}
function cancelEdit(id) {
  document.getElementById('cat-row-' + id).classList.remove('editing');
}
</script>
@endsection
