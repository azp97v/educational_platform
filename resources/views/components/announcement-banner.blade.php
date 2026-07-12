@auth
@php
    try {
        $__role = auth()->user()->role ?? 'student';
        $__routeName = optional(request()->route())->getName() ?? '';
        // اعتبر الصفحة "لوحة تحكم" إذا كانت من مسارات الداشبورد الرئيسية
        $__isDashboard = in_array($__routeName, [
            'home', 'dashboard', 'landing',
            'student.index', 'teacher.dashboard', 'teacher.dashboard.full', 'admin.index',
        ], true);
        $__annQuery = \App\Models\Announcement::active()->forRole($__role);
        if (!$__isDashboard) {
            $__annQuery->where('scope', 'site_wide');
        }
        $__announcements = $__annQuery->orderBy('created_at', 'desc')->limit(10)->get(['id', 'title', 'body', 'type']);
    } catch (\Throwable $e) {
        $__announcements = collect();
    }
@endphp

@if($__announcements->isNotEmpty())
<div class="ann-strip" data-ann-strip role="status" aria-live="polite">
    <div class="ann-strip__track" data-ann-track>
        @foreach($__announcements as $ann)
            @php
                $iconMap = [
                    'info'    => 'ri-information-line',
                    'success' => 'ri-checkbox-circle-line',
                    'warning' => 'ri-error-warning-line',
                    'danger'  => 'ri-close-circle-line',
                ];
                $ic = $iconMap[$ann->type] ?? $iconMap['info'];
            @endphp
            <span class="ann-strip__item ann-strip__item--{{ $ann->type ?: 'info' }}">
                <i class="{{ $ic }}" aria-hidden="true"></i>
                <strong>{{ $ann->title }}</strong>
                @if($ann->body)
                    <span class="ann-strip__sep">—</span>
                    <span class="ann-strip__body">{{ \Illuminate\Support\Str::limit($ann->body, 180) }}</span>
                @endif
            </span>
            <span class="ann-strip__gap" aria-hidden="true">•</span>
        @endforeach
        {{-- تكرار السلسلة مرة أخرى لضمان تدفق سلس بلا فجوات في الـmarquee --}}
        @foreach($__announcements as $ann)
            @php
                $iconMap = [
                    'info'    => 'ri-information-line',
                    'success' => 'ri-checkbox-circle-line',
                    'warning' => 'ri-error-warning-line',
                    'danger'  => 'ri-close-circle-line',
                ];
                $ic = $iconMap[$ann->type] ?? $iconMap['info'];
            @endphp
            <span class="ann-strip__item ann-strip__item--{{ $ann->type ?: 'info' }}" aria-hidden="true">
                <i class="{{ $ic }}"></i>
                <strong>{{ $ann->title }}</strong>
                @if($ann->body)
                    <span class="ann-strip__sep">—</span>
                    <span class="ann-strip__body">{{ \Illuminate\Support\Str::limit($ann->body, 180) }}</span>
                @endif
            </span>
            <span class="ann-strip__gap" aria-hidden="true">•</span>
        @endforeach
    </div>
</div>

<style>
.ann-strip {
    /* شريط أنيق مندمج داخل تدفّق المستند — لا يغطّي أي عنصر */
    position: relative;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
    background: var(--ann-bg, rgba(198, 166, 117, 0.10));
    border-bottom: 1px solid var(--ann-border, rgba(198, 166, 117, 0.22));
    color: var(--ann-text, inherit);
    font-family: inherit;
    height: 38px;
    line-height: 38px;
    z-index: 1;
    contain: content;
}
[data-theme="dark"] .ann-strip {
    --ann-bg: rgba(198, 166, 117, 0.08);
    --ann-border: rgba(198, 166, 117, 0.18);
    --ann-text: #d9c4a6;
}
[data-theme="light"] .ann-strip,
:not([data-theme]) .ann-strip {
    --ann-bg: rgba(141, 114, 82, 0.06);
    --ann-border: rgba(141, 114, 82, 0.18);
    --ann-text: #5a3f26;
}
.ann-strip__track {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    white-space: nowrap;
    animation: ann-marquee 42s linear infinite;
    will-change: transform;
    padding-inline: 20px;
}
.ann-strip:hover .ann-strip__track,
.ann-strip:focus-within .ann-strip__track {
    animation-play-state: paused;
}
.ann-strip__item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}
.ann-strip__item i { font-size: 15px; opacity: 0.85; }
.ann-strip__item strong { font-weight: 700; }
.ann-strip__sep { opacity: 0.55; margin: 0 2px; }
.ann-strip__body { opacity: 0.85; font-weight: 500; }
.ann-strip__gap { opacity: 0.35; font-weight: 700; }

/* ألوان دقيقة حسب النوع بدون كسر الثيم */
.ann-strip__item--success i { color: #16a34a; }
.ann-strip__item--warning i { color: #d97706; }
.ann-strip__item--danger  i { color: #dc2626; }
.ann-strip__item--info    i { color: #C6A675; }

@keyframes ann-marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* اتجاه RTL: نتحرّك من اليمين إلى اليسار طبيعياً */
[dir="rtl"] .ann-strip__track { animation-name: ann-marquee-rtl; }
@keyframes ann-marquee-rtl {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* الاستجابة للموبايل */
@media (max-width: 768px) {
    .ann-strip { height: 34px; line-height: 34px; }
    .ann-strip__track { animation-duration: 30s; gap: 10px; padding-inline: 12px; }
    .ann-strip__item { font-size: 12px; }
    .ann-strip__body { display: none; } /* على الموبايل نُبقي العنوان فقط */
}

/* احترام تفضيل تقليل الحركة */
@media (prefers-reduced-motion: reduce) {
    .ann-strip__track { animation: none; padding-inline: 20px; }
}
</style>
@endif
@endauth
