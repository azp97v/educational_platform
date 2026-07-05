@extends('layouts.admin')
@section('title', 'الإعلانات')
@section('page_title', 'إدارة الإعلانات')
@section('page_subtitle', 'نشر الإعلانات وإخطار المستخدمين حسب الدور')

@section('content')

@if(session('success'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✓ {{ session('success') }}</div>
@endif

<style>
.ann-type-info    { background:#0284c722;color:#38bdf8; }
.ann-type-success { background:#16a34a22;color:#4ade80; }
.ann-type-warning { background:#d9770622;color:#fb923c; }
.ann-type-danger  { background:#dc262622;color:#f87171; }
.ann-role-all     { background:rgba(108,99,255,.15);color:#a78bfa; }
.ann-role-admin   { background:#7c3aed22;color:#c4b5fd; }
.ann-role-teacher { background:#06594622;color:#34d399; }
.ann-role-student { background:#1d4ed822;color:#60a5fa; }
.ann-badge { display:inline-block;font-size:10px;font-weight:700;padding:2px 10px;border-radius:20px; }
</style>

<div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
    <a href="{{ route('admin.announcements.create') }}" class="admin-btn"><i class="ri-add-line"></i> إعلان جديد</a>
</div>

<section class="admin-card">
    @if($announcements->isEmpty())
        <div style="text-align:center;padding:50px;opacity:.4;">
            <div style="font-size:40px;margin-bottom:10px;"><i class="ri-notification-off-line"></i></div>
            <div>لا توجد إعلانات بعد. أنشئ أول إعلان الآن.</div>
        </div>
    @else
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>النوع</th>
                    <th>الجمهور</th>
                    <th>الحالة</th>
                    <th>ينتهي في</th>
                    <th>المنشئ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            @foreach($announcements as $ann)
                @php
                    $typeLabels = ['info'=>'معلومة','success'=>'نجاح','warning'=>'تحذير','danger'=>'تنبيه'];
                    $roleLabels = ['all'=>'الجميع','admin'=>'المشرفون','teacher'=>'المعلمون','student'=>'الطلاب'];
                    $isExpired  = $ann->expires_at && $ann->expires_at->isPast();
                @endphp
                <tr>
                    <td>
                        <div style="font-size:13px;font-weight:600;max-width:200px;">{{ Str::limit($ann->title, 40) }}</div>
                        <div style="font-size:11px;opacity:.5;margin-top:2px;">{{ Str::limit($ann->body, 50) }}</div>
                    </td>
                    <td><span class="ann-badge ann-type-{{ $ann->type }}">{{ $typeLabels[$ann->type] ?? $ann->type }}</span></td>
                    <td><span class="ann-badge ann-role-{{ $ann->target_role }}">{{ $roleLabels[$ann->target_role] ?? $ann->target_role }}</span></td>
                    <td>
                        @if(!$ann->is_active || $isExpired)
                            <span style="color:#f87171;font-size:11px;font-weight:700;">{{ $isExpired ? 'منتهي' : 'معطّل' }}</span>
                        @else
                            <span style="color:#4ade80;font-size:11px;font-weight:700;">نشط</span>
                        @endif
                    </td>
                    <td style="font-size:11px;opacity:.6;">
                        {{ $ann->expires_at ? $ann->expires_at->format('Y-m-d') : '—' }}
                    </td>
                    <td style="font-size:11px;opacity:.65;">{{ $ann->author?->name ?? '—' }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.announcements.edit', $ann) }}" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;">تعديل</a>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" onsubmit="return confirm('حذف هذا الإعلان؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;background:rgba(239,68,68,.1);color:#f87171;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;">{{ $announcements->links() }}</div>
    @endif
</section>
@endsection
