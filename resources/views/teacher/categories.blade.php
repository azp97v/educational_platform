@extends('layouts.app-unified')

@section('title', 'إدارة الفئات')

@section('styles')
<style>
/* ─── Page layout ─── */
.cats-wrap { max-width: 780px; }

/* ─── Page header ─── */
.page-hdr {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 12px; margin-bottom: 28px;
}
.page-hdr-title { font-size: 22px; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
.page-hdr-title i { color: var(--gold); font-size: 24px; }
.page-hdr-sub { font-size: 13px; color: var(--text-muted); margin-top: 3px; }
.badge-count {
  background: var(--gold-light); color: var(--gold);
  font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
  border: 1px solid rgba(198,166,117,.3);
}

/* ─── Alerts ─── */
.alert {
  padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 20px;
  font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;
}
.alert-success { background: rgba(52,199,89,.1); border: 1px solid rgba(52,199,89,.3); color: #34c759; }
.alert-error   { background: var(--danger-light); border: 1px solid rgba(214,69,69,.3); color: var(--danger); }

/* ─── Add form card ─── */
.add-card {
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius-lg); padding: 22px 24px;
  margin-bottom: 24px; box-shadow: var(--shadow);
}
.add-card-title {
  font-size: 14px; font-weight: 700; color: var(--text-secondary);
  margin-bottom: 14px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: .5px;
}
.add-card-title i { color: var(--gold); }
.add-row { display: flex; gap: 10px; }
.add-row input[type="text"] {
  flex: 1; padding: 11px 14px; border-radius: var(--radius-md);
  border: 1.5px solid var(--border); background: var(--bg);
  color: var(--text-primary); font-family: Tajawal, sans-serif; font-size: 14px;
  outline: none; transition: border-color .2s, box-shadow .2s;
}
.add-row input[type="text"]::placeholder { color: var(--text-muted); }
.add-row input[type="text"]:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(198,166,117,.15); }
.btn-add {
  padding: 11px 20px; border-radius: var(--radius-md); border: none;
  background: var(--gold); color: #fff; font-weight: 700; font-size: 14px;
  cursor: pointer; font-family: Tajawal, sans-serif;
  display: flex; align-items: center; gap: 6px;
  transition: opacity .2s, transform .15s; white-space: nowrap;
}
.btn-add:hover { opacity: .88; transform: translateY(-1px); }

/* ─── Category list ─── */
.cat-list { display: flex; flex-direction: column; gap: 8px; }

.cat-row {
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius-md); padding: 14px 18px;
  display: flex; align-items: center; gap: 12px;
  box-shadow: var(--shadow); transition: border-color .2s, box-shadow .2s;
}
.cat-row:hover { border-color: rgba(198,166,117,.35); box-shadow: var(--shadow-hover); }

.cat-row-icon { color: var(--gold); font-size: 18px; flex-shrink: 0; }

.cat-row-info { flex: 1; min-width: 0; }
.cat-row-name { font-weight: 700; font-size: 15px; color: var(--text-primary); }
.cat-row-meta { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

.cat-row-actions { display: flex; gap: 6px; flex-shrink: 0; align-items: center; }

/* Inline edit form */
.cat-edit-form { display: none; flex: 1; align-items: center; gap: 8px; }
.cat-row.editing .cat-row-info { display: none; }
.cat-row.editing .cat-edit-form { display: flex; }
.cat-edit-form input {
  flex: 1; padding: 8px 12px; border-radius: var(--radius-md);
  border: 1.5px solid var(--gold); background: var(--bg);
  color: var(--text-primary); font-family: Tajawal, sans-serif; font-size: 14px; outline: none;
}
.cat-edit-form button {
  padding: 8px 14px; border-radius: var(--radius-md); border: none;
  cursor: pointer; font-family: Tajawal, sans-serif; font-size: 13px; font-weight: 700;
  transition: opacity .15s;
}
.btn-save { background: var(--gold-light); color: var(--gold); border: 1px solid rgba(198,166,117,.3); }
.btn-save:hover { opacity: .8; }
.btn-cancel { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }
.btn-cancel:hover { opacity: .8; }

/* Icon buttons */
.btn-icon {
  width: 34px; height: 34px; border-radius: var(--radius-md); border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; font-size: 16px;
  transition: background .15s, transform .12s;
}
.btn-icon:hover:not(:disabled) { transform: translateY(-1px); }
.btn-edit  { background: var(--gold-light); color: var(--gold); }
.btn-edit:hover { background: rgba(198,166,117,.28); }
.btn-del   { background: var(--danger-light); color: var(--danger); border: none; }
.btn-del:hover { background: rgba(214,69,69,.22); }
.btn-del:disabled { opacity: .35; cursor: not-allowed; }

/* Empty state */
.cat-empty {
  background: var(--card-bg); border: 2px dashed var(--border);
  border-radius: var(--radius-lg); padding: 48px 24px;
  text-align: center; color: var(--text-muted);
}
.cat-empty i { font-size: 44px; opacity: .3; display: block; margin-bottom: 12px; color: var(--gold); }
.cat-empty p { font-size: 14px; }

@media (max-width: 520px) {
  .add-row { flex-direction: column; }
  .cat-row { flex-wrap: wrap; }
}
</style>
@endsection

@section('content')
<div class="cats-wrap">

  {{-- Header --}}
  <div class="page-hdr">
    <div>
      <div class="page-hdr-title">
        <i class="ri-price-tag-3-line"></i>
        إدارة الفئات
        <span class="badge-count">{{ $categories->count() }}</span>
      </div>
      <div class="page-hdr-sub">أضف وعدّل فئات المسارات — تظهر في قوائم إنشاء وتعديل المسارات</div>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success"><i class="ri-checkbox-circle-line"></i> {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-error"><i class="ri-error-warning-line"></i> {{ $errors->first() }}</div>
  @endif

  {{-- Add Category --}}
  <div class="add-card">
    <div class="add-card-title"><i class="ri-add-circle-line"></i> إضافة فئة جديدة</div>
    <form action="{{ route('teacher.categories.store') }}" method="POST">
      @csrf
      <div class="add-row">
        <input type="text" name="name" placeholder="اسم الفئة (مثل: الفقه الإسلامي)"
               maxlength="100" value="{{ old('name') }}" required autofocus>
        <button type="submit" class="btn-add"><i class="ri-add-line"></i> إضافة</button>
      </div>
    </form>
  </div>

  {{-- List --}}
  <div class="cat-list">
    @forelse($categories as $cat)
      <div class="cat-row" id="cat-{{ $cat->id }}">
        <i class="ri-price-tag-3-line cat-row-icon"></i>

        <div class="cat-row-info">
          <div class="cat-row-name">{{ $cat->name }}</div>
          <div class="cat-row-meta">
            {{ $cat->courses_count }}
            {{ $cat->courses_count == 1 ? 'مسار' : 'مسارات' }}
          </div>
        </div>

        <form class="cat-edit-form" action="{{ route('teacher.categories.update', $cat->id) }}" method="POST">
          @csrf @method('PUT')
          <input type="text" name="name" value="{{ $cat->name }}" maxlength="100" required>
          <button type="submit" class="btn-save">حفظ</button>
          <button type="button" class="btn-cancel js-cancel" data-id="{{ $cat->id }}">إلغاء</button>
        </form>

        <div class="cat-row-actions">
          <button class="btn-icon btn-edit js-edit" data-id="{{ $cat->id }}" title="تعديل">
            <i class="ri-edit-line"></i>
          </button>
          @if($cat->courses_count === 0)
            <form class="js-del-form" action="{{ route('teacher.categories.destroy', $cat->id) }}" method="POST"
                  data-name="{{ $cat->name }}">
              @csrf @method('DELETE')
              <button type="submit" class="btn-icon btn-del" title="حذف">
                <i class="ri-delete-bin-line"></i>
              </button>
            </form>
          @else
            <button class="btn-icon btn-del" disabled
                    title="مرتبطة بـ{{ $cat->courses_count }} مسار — لا يمكن الحذف">
              <i class="ri-delete-bin-line"></i>
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="cat-empty">
        <i class="ri-price-tag-3-line"></i>
        <p>لا توجد فئات بعد — أضف أولى الفئات من الأعلى</p>
      </div>
    @endforelse
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Edit buttons
  document.querySelectorAll('.js-edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var row = document.getElementById('cat-' + btn.dataset.id);
      row.classList.add('editing');
      row.querySelector('.cat-edit-form input').focus();
    });
  });

  // Cancel buttons
  document.querySelectorAll('.js-cancel').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.getElementById('cat-' + btn.dataset.id).classList.remove('editing');
    });
  });

  // Delete forms — require explicit confirmation
  document.querySelectorAll('.js-del-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (confirm('حذف الفئة «' + form.dataset.name + '»؟')) {
        form.submit();
      }
    });
  });
});
</script>
@endsection
