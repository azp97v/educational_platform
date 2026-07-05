@extends('layouts.admin')
@section('title', 'سجل النشاط')
@section('page_title', 'سجل نشاط المشرفين')
@section('page_subtitle', 'تعقب كامل لكل إجراء يقوم به المشرفون على المنصة')

@section('content')

@php
$actionLabels = [
    'user_created'          => ['label' => 'إنشاء مستخدم',          'color' => '#22c55e',  'icon' => 'ri-user-add-line'],
    'user_deleted'          => ['label' => 'حذف مستخدم',            'color' => '#ef4444',  'icon' => 'ri-user-unfollow-line'],
    'password_reset'        => ['label' => 'إعادة تعيين كلمة مرور', 'color' => '#f97316',  'icon' => 'ri-lock-password-line'],
    'bulk_block'            => ['label' => 'حظر جماعي',              'color' => '#ef4444',  'icon' => 'ri-user-forbid-line'],
    'bulk_unblock'          => ['label' => 'إلغاء حظر جماعي',       'color' => '#22c55e',  'icon' => 'ri-user-follow-line'],
    'bulk_delete'           => ['label' => 'حذف جماعي',             'color' => '#ef4444',  'icon' => 'ri-delete-bin-line'],
    'enrollment_approved'   => ['label' => 'قبول التحاق',           'color' => '#22c55e',  'icon' => 'ri-checkbox-circle-line'],
    'enrollment_rejected'   => ['label' => 'رفض التحاق',            'color' => '#f87171',  'icon' => 'ri-close-circle-line'],
    'settings_updated'      => ['label' => 'تحديث الإعدادات',       'color' => '#a78bfa',  'icon' => 'ri-settings-3-line'],
    'announcement_created'  => ['label' => 'إعلان جديد',            'color' => '#38bdf8',  'icon' => 'ri-notification-3-line'],
    'announcement_updated'  => ['label' => 'تحديث إعلان',           'color' => '#60a5fa',  'icon' => 'ri-edit-line'],
    'announcement_deleted'  => ['label' => 'حذف إعلان',             'color' => '#f87171',  'icon' => 'ri-delete-bin-line'],
];
@endphp

<section class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h2 style="margin:0;">سجل العمليات</h2>
        <span style="font-size:12px;opacity:.5;">{{ $logs->total() }} إجراء مسجّل</span>
    </div>

    @if($logs->isEmpty())
        <div style="text-align:center;padding:50px;opacity:.4;">
            <div style="font-size:40px;margin-bottom:10px;"><i class="ri-history-line"></i></div>
            <div>لا يوجد سجل نشاط بعد. ستظهر هنا العمليات فور تنفيذها.</div>
        </div>
    @else
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الإجراء</th>
                    <th>المشرف</th>
                    <th>الهدف</th>
                    <th>التفاصيل</th>
                    <th>IP</th>
                    <th>الوقت</th>
                </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                @php
                    $meta = $actionLabels[$log->action] ?? ['label' => $log->action, 'color' => '#94a3b8', 'icon' => 'ri-information-line'];
                @endphp
                <tr>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:{{ $meta['color'] }};">
                            <i class="{{ $meta['icon'] }}"></i>
                            {{ $meta['label'] }}
                        </span>
                    </td>
                    <td>
                        @if($log->admin)
                            <div style="font-size:12px;font-weight:600;">{{ $log->admin->name }}</div>
                            <div style="font-size:10px;opacity:.5;">{{ $log->admin->email }}</div>
                        @else
                            <span style="opacity:.4;">—</span>
                        @endif
                    </td>
                    <td style="font-size:11px;opacity:.6;">
                        @if($log->target_type && $log->target_id)
                            {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                        @else
                            —
                        @endif
                    </td>
                    <td style="font-size:11px;opacity:.65;max-width:220px;word-break:break-word;">{{ $log->details ?? '—' }}</td>
                    <td style="font-size:11px;font-family:monospace;opacity:.5;">{{ $log->ip_address ?? '—' }}</td>
                    <td style="font-size:11px;opacity:.6;">
                        {{ $log->created_at?->format('Y-m-d H:i') }}<br>
                        <span style="font-size:10px;opacity:.7;">{{ $log->created_at?->diffForHumans() }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;">{{ $logs->links() }}</div>
    @endif
</section>
@endsection
