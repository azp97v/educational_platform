@extends('layouts.admin')
@section('title', $announcement ? 'تعديل الإعلان' : 'إعلان جديد')
@section('page_title', $announcement ? 'تعديل الإعلان' : 'إنشاء إعلان جديد')
@section('page_subtitle', 'نشر إعلان موجّه لفئة معينة من المستخدمين')

@section('content')
<section class="admin-card" style="max-width:700px;">
    <form method="POST" action="{{ $announcement ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}">
        @csrf
        @if($announcement) @method('PUT') @endif

        @if($errors->any())
            <div style="background:#dc262622;border:1px solid #f8717166;color:#f87171;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                <ul style="margin:0;padding-right:16px;">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="admin-form">
            <div>
                <label>عنوان الإعلان <span style="color:#f87171">*</span></label>
                <input type="text" name="title" value="{{ old('title', $announcement?->title) }}" placeholder="مثال: صيانة مجدولة يوم السبت" required>
            </div>

            <div>
                <label>نص الإعلان <span style="color:#f87171">*</span></label>
                <textarea name="body" rows="4" placeholder="اكتب تفاصيل الإعلان هنا..." required style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:8px;padding:10px 14px;font-size:13px;resize:vertical;">{{ old('body', $announcement?->body) }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label>النوع <span style="color:#f87171">*</span></label>
                    <select name="type" required style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:8px;padding:10px 14px;font-size:13px;">
                        @foreach(['info'=>'معلومة','success'=>'نجاح','warning'=>'تحذير','danger'=>'تنبيه'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('type', $announcement?->type) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>الجمهور المستهدف <span style="color:#f87171">*</span></label>
                    <select name="target_role" required style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:8px;padding:10px 14px;font-size:13px;">
                        @foreach(['all'=>'الجميع','admin'=>'المشرفون','teacher'=>'المعلمون','student'=>'الطلاب'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('target_role', $announcement?->target_role ?? 'all') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label>تاريخ انتهاء الصلاحية (اختياري)</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $announcement?->expires_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div style="display:flex;align-items:center;gap:10px;padding-top:24px;">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $announcement?->is_active ?? true) ? 'checked' : '' }}
                        style="accent-color:#C6A675;width:16px;height:16px;">
                    <label for="is_active" style="font-weight:600;cursor:pointer;">نشر الإعلان فوراً</label>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="admin-btn">
                    <i class="ri-{{ $announcement ? 'save' : 'notification-3' }}-line"></i>
                    {{ $announcement ? 'حفظ التعديلات' : 'نشر الإعلان' }}
                </button>
                <a href="{{ route('admin.announcements') }}" class="admin-btn secondary">إلغاء</a>
            </div>
        </div>
    </form>
</section>
@endsection
