@extends('layouts.admin')
@section('title', 'مراقبة أخطاء النظام')
@section('page_title', 'مراقبة أخطاء النظام')
@section('page_subtitle', 'كل استثناء PHP/Laravel يُسجَّل هنا تلقائياً — فحصه وتحديده كمُعالَج أو حذفه')

@section('content')

@if(session('success'))
    <div style="background:#16a34a22;border:1px solid #4ade8066;color:#4ade80;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#dc262622;border:1px solid #f8717166;color:#f87171;padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px;">✗ {{ session('error') }}</div>
@endif

<style>
.err-badge { display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px; }
.err-badge.e500 { background:rgba(239,68,68,.15); color:#f87171; }
.err-badge.e400 { background:rgba(249,115,22,.15); color:#fb923c; }
.err-badge.resolved { background:rgba(34,197,94,.12); color:#4ade80; }
.err-type { font-family:monospace; font-size:12px; color:#a78bfa; background:rgba(167,139,250,.08); border-radius:4px; padding:2px 7px; }
.err-url { font-size:11px; font-family:monospace; color:#94a3b8; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }
.err-msg { font-size:12px; color:#e2e8f0; max-width:320px; word-break:break-word; }
.trace-box { font-family:monospace; font-size:10px; line-height:1.5; color:#94a3b8; background:#0f172a; border-radius:6px; padding:12px; max-height:200px; overflow-y:auto; white-space:pre-wrap; word-break:break-all; }
.filter-tabs { display:flex; gap:8px; margin-bottom:18px; }
.filter-tab { padding:7px 18px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; border:1px solid rgba(255,255,255,.1); color:#94a3b8; transition:.15s; }
.filter-tab.active { background:rgba(139,92,246,.2); border-color:#8b5cf6; color:#a78bfa; }
.filter-tab:hover:not(.active) { background:rgba(255,255,255,.05); color:#e2e8f0; }
details summary { cursor:pointer; font-size:11px; color:#64748b; user-select:none; }
details summary:hover { color:#94a3b8; }
</style>

{{-- Filters --}}
<div class="filter-tabs">
    <a href="{{ route('admin.errors', ['filter' => 'unresolved']) }}" class="filter-tab {{ $filter === 'unresolved' ? 'active' : '' }}">
        غير مُعالَجة
        @if(($adminStatsMini['unresolvedErrors'] ?? 0) > 0)
            <span style="background:#ef4444;color:#fff;font-size:10px;border-radius:999px;padding:1px 7px;margin-right:4px;">{{ $adminStatsMini['unresolvedErrors'] }}</span>
        @endif
    </a>
    <a href="{{ route('admin.errors', ['filter' => 'all']) }}" class="filter-tab {{ $filter === 'all' ? 'active' : '' }}">الكل</a>
    <a href="{{ route('admin.errors', ['filter' => 'resolved']) }}" class="filter-tab {{ $filter === 'resolved' ? 'active' : '' }}">مُعالَجة</a>
</div>

{{-- Bulk actions --}}
@if($systemErrors->total() > 0)
<div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:14px;">
    @if($filter !== 'resolved')
    <form method="POST" action="{{ route('admin.errors.resolve-all') }}" onsubmit="return confirm('تحديد جميع الأخطاء الظاهرة كمُعالَجة؟')">
        @csrf
        <button type="submit" class="admin-btn secondary" style="background:rgba(34,197,94,.1);color:#4ade80;">
            <i class="ri-check-double-line"></i> تحديد الكل كمُعالَج
        </button>
    </form>
    @endif
    @if($filter === 'resolved')
    <form method="POST" action="{{ route('admin.errors.delete-resolved') }}" onsubmit="return confirm('حذف جميع الأخطاء المُعالَجة نهائياً؟')">
        @csrf @method('DELETE')
        <button type="submit" class="admin-btn secondary" style="background:rgba(239,68,68,.1);color:#f87171;">
            <i class="ri-delete-bin-line"></i> حذف المُعالَجة ({{ $systemErrors->total() }})
        </button>
    </form>
    @endif
</div>
@endif

<section class="admin-card">
    @if($systemErrors->total() === 0)
        <div style="text-align:center;padding:50px;opacity:.45;">
            <div style="font-size:48px;margin-bottom:12px;">🛡</div>
            <div style="font-size:16px;font-weight:600;">{{ $filter === 'unresolved' ? 'لا توجد أخطاء غير مُعالَجة' : 'لا توجد أخطاء' }}</div>
            <div style="font-size:13px;margin-top:6px;">المنصة تعمل بشكل طبيعي.</div>
        </div>
    @else
    <div style="overflow:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>النوع</th>
                    <th>الرسالة</th>
                    <th>الـ URL</th>
                    <th>المستخدم</th>
                    <th>الوقت</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            @foreach($systemErrors as $err)
            <tr>
                <td style="color:#64748b;font-size:12px;">#{{ $err->id }}</td>
                <td>
                    <span class="err-type">{{ $err->type }}</span>
                    <span class="err-badge {{ $err->status_code >= 500 ? 'e500' : 'e400' }}" style="margin-top:4px;display:block;width:fit-content;">
                        {{ $err->status_code }}
                    </span>
                </td>
                <td>
                    <div class="err-msg">{{ Str::limit($err->message, 120) }}</div>
                    @if($err->file)
                        <div style="font-size:10px;color:#64748b;margin-top:3px;font-family:monospace;">
                            {{ Str::after($err->file, '/var/www/eglal-educational-platform/') }}:{{ $err->line }}
                        </div>
                    @endif
                    @if($err->trace)
                    <details style="margin-top:6px;">
                        <summary>عرض الـ Stack Trace</summary>
                        <div class="trace-box" style="margin-top:6px;">{{ $err->trace }}</div>
                    </details>
                    @endif
                </td>
                <td>
                    <span class="err-url" title="{{ $err->url }}">
                        {{ $err->method ? '[' . $err->method . '] ' : '' }}{{ $err->url ? parse_url($err->url, PHP_URL_PATH) : '—' }}
                    </span>
                </td>
                <td style="font-size:12px;color:#94a3b8;">
                    {{ $err->user_id ? '#' . $err->user_id : '—' }}
                    @if($err->user_ip)
                        <div style="font-size:10px;color:#475569;">{{ $err->user_ip }}</div>
                    @endif
                </td>
                <td style="font-size:12px;color:#94a3b8;white-space:nowrap;">
                    {{ $err->created_at->diffForHumans() }}
                    <div style="font-size:10px;color:#475569;">{{ $err->created_at->format('Y-m-d H:i') }}</div>
                </td>
                <td>
                    @if($err->resolved)
                        <span class="err-badge resolved">مُعالَج</span>
                        @if($err->resolved_at)
                            <div style="font-size:10px;color:#475569;margin-top:3px;">{{ $err->resolved_at->format('Y-m-d H:i') }}</div>
                        @endif
                    @else
                        <span class="err-badge e500">جديد</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-direction:column;">
                        @if(!$err->resolved)
                        <form method="POST" action="{{ route('admin.errors.resolve', $err) }}">
                            @csrf
                            <button type="submit" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;width:100%;background:rgba(34,197,94,.1);color:#4ade80;">
                                <i class="ri-check-line"></i> مُعالَج
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.errors.delete', $err) }}" onsubmit="return confirm('حذف هذا الخطأ نهائياً؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-btn secondary" style="font-size:11px;padding:4px 10px;width:100%;background:rgba(239,68,68,.08);color:#f87171;">
                                <i class="ri-delete-bin-line"></i> حذف
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $systemErrors->links() }}</div>
    @endif
</section>

@endsection
