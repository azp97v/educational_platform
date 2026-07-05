@extends('layouts.admin')
@section('title', 'ملف المستخدم - ' . $user->name)
@section('page_title', 'ملف المستخدم')
@section('page_subtitle', $user->name)

@section('content')

@if(session('password_reset'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:14px 20px;border-radius:8px;margin-bottom:16px;font-size:14px;font-weight:700;">
        🔑 {{ session('password_reset') }}
    </div>
@endif
@if(session('success'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#dc262622;border:1px solid #f8717166;color:#f87171;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✗ {{ session('error') }}</div>
@endif

<section class="admin-card" style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid rgba(255,255,255,.08);">
        @if($user->avatar_url)
            <img src="{{ asset('storage/' . $user->avatar_url) }}" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid rgba(198,166,117,.5);">
        @else
            <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#C6A675,#8D7252);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;color:#fff;flex-shrink:0;">{{ mb_substr($user->name,0,1) }}</div>
        @endif
        <div style="flex:1;">
            <div style="font-size:20px;font-weight:800;margin-bottom:4px;">{{ $user->name }}</div>
            @if($user->username)
                <div style="font-size:13px;opacity:.6;margin-bottom:4px;">{{ '@' . $user->username }}</div>
            @endif
            <div style="font-size:12px;opacity:.5;">انضم {{ $user->created_at?->diffForHumans() }} — {{ $user->created_at?->format('Y-m-d') }}</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @php
                $roleColors = ['admin'=>'#C6A675','teacher'=>'#059669','student'=>'#8D7252'];
                $roleLabels = ['admin'=>'مشرف','teacher'=>'معلم','student'=>'طالب'];
            @endphp
            <span style="padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;background:{{ ($roleColors[$user->role]??'#C6A675') }}33;color:{{ $roleColors[$user->role]??'#C6A675' }};">
                {{ $roleLabels[$user->role] ?? $user->role }}
            </span>
            @php
                $stColor = $user->status === 'blocked' ? '#ef4444' : ($user->status === 'inactive' ? '#f97316' : '#22c55e');
                $stLabel = $user->status === 'blocked' ? 'محظور' : ($user->status === 'inactive' ? 'غير نشط' : 'نشط');
            @endphp
            <span style="padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $stColor }}22;color:{{ $stColor }};">{{ $stLabel }}</span>
        </div>
    </div>

    {{-- Stats row --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:14px;margin-bottom:24px;">
        <div style="background:rgba(198,166,117,.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#C6A675;">{{ $enrollmentCount }}</div>
            <div style="font-size:11px;opacity:.6;margin-top:4px;">مسارات مسجّل</div>
        </div>
        <div style="background:rgba(34,197,94,.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#4ade80;">{{ $approvedEnrollments }}</div>
            <div style="font-size:11px;opacity:.6;margin-top:4px;">مسارات مقبولة</div>
        </div>
        <div style="background:rgba(96,165,250,.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#60a5fa;">{{ $messageCount }}</div>
            <div style="font-size:11px;opacity:.6;margin-top:4px;">رسائل مرسلة</div>
        </div>
        <div style="background:rgba(251,191,36,.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#fbbf24;">{{ $certCount }}</div>
            <div style="font-size:11px;opacity:.6;margin-top:4px;">شهادات</div>
        </div>
        <div style="background:rgba(249,115,22,.08);border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#fb923c;">{{ $inquiryCount }}</div>
            <div style="font-size:11px;opacity:.6;margin-top:4px;">استفسارات</div>
        </div>
    </div>

    {{-- Profile fields --}}
    <div class="admin-form-grid">
        <div><label>الاسم الكامل</label><input value="{{ $user->name }}" disabled></div>
        <div><label>البريد الإلكتروني</label><input value="{{ $user->email }}" disabled></div>
        <div><label>اسم المستخدم</label><input value="{{ $user->username ?? '—' }}" disabled></div>
        <div><label>رقم الهاتف</label><input value="{{ $user->phone ?? '—' }}" disabled></div>
        <div><label>الدور</label><input value="{{ $roleLabels[$user->role] ?? $user->role }}" disabled></div>
        <div><label>الحالة</label><input value="{{ $stLabel }}" disabled></div>
        <div><label>آخر نشاط</label><input value="{{ $user->last_activity_at ? \Carbon\Carbon::parse($user->last_activity_at)->diffForHumans() : '—' }}" disabled></div>
        <div><label>تاريخ التسجيل</label><input value="{{ $user->created_at?->format('Y-m-d H:i') }}" disabled></div>
    </div>

    {{-- Quick actions --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,.08);">
        <a href="{{ route('admin.edit', $user) }}" class="admin-btn"><i class="ri-edit-line"></i> تعديل</a>

        @if($user->role === 'student')
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('إعادة تعيين كلمة مرور الطالب؟')">
            @csrf
            <button type="submit" class="admin-btn secondary" style="background:rgba(249,115,22,.12);color:#fb923c;"><i class="ri-lock-password-line"></i> إعادة تعيين كلمة المرور</button>
        </form>
        @endif

        @if($user->status === 'blocked')
            <form method="POST" action="{{ route('admin.users.bulk') }}" onsubmit="return confirm('إلغاء حظر هذا المستخدم؟')">
                @csrf
                <input type="hidden" name="ids[]" value="{{ $user->id }}">
                <input type="hidden" name="action" value="unblock">
                <button type="submit" class="admin-btn secondary" style="background:rgba(34,197,94,.12);color:#4ade80;"><i class="ri-user-follow-line"></i> إلغاء الحظر</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.users.bulk') }}" onsubmit="return confirm('حظر هذا المستخدم؟')">
                @csrf
                <input type="hidden" name="ids[]" value="{{ $user->id }}">
                <input type="hidden" name="action" value="block">
                <button type="submit" class="admin-btn secondary" style="background:rgba(239,68,68,.12);color:#f87171;"><i class="ri-user-forbid-line"></i> حظر</button>
            </form>
        @endif

        <form method="POST" action="{{ route('admin.destroy', $user) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب نهائياً؟')">
            @csrf @method('DELETE')
            <button type="submit" class="admin-btn secondary" style="background:rgba(239,68,68,.1);color:#f87171;"><i class="ri-delete-bin-line"></i> حذف الحساب</button>
        </form>

        <a href="{{ route('admin.users') }}" class="admin-btn secondary"><i class="ri-arrow-right-line"></i> العودة</a>
    </div>
</section>

{{-- Enrollment history --}}
@if($enrollments->isNotEmpty())
<section class="admin-card">
    <h2>سجل التسجيل في المسارات</h2>
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead><tr><th>المسار</th><th>الحالة</th><th>تاريخ الالتحاق</th></tr></thead>
            <tbody>
            @foreach($enrollments as $en)
                <tr>
                    <td>{{ $en->course_name }}</td>
                    <td>
                        @if($en->status === 'approved')
                            <span style="color:#4ade80;font-size:11px;font-weight:700;">مقبول</span>
                        @elseif($en->status === 'rejected')
                            <span style="color:#f87171;font-size:11px;font-weight:700;">مرفوض</span>
                        @else
                            <span style="color:#fbbf24;font-size:11px;font-weight:700;">معلّق</span>
                        @endif
                    </td>
                    <td style="font-size:12px;opacity:.7;">{{ $en->enrolled_at ? \Carbon\Carbon::parse($en->enrolled_at)->format('Y-m-d') : '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

@endsection
