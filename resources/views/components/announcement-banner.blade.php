@auth
@php
    try {
        $__role = auth()->user()->role ?? 'student';
        $__announcements = \App\Models\Announcement::active()
            ->forRole($__role)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get(['id', 'title', 'body', 'type']);
    } catch (\Throwable $e) {
        $__announcements = collect();
    }
@endphp

@if($__announcements->isNotEmpty())
<div id="ann-stack" style="position:fixed;top:0;left:0;right:0;z-index:99999;display:flex;flex-direction:column;gap:0;pointer-events:none;">
    @foreach($__announcements as $ann)
    @php
        $annColors = [
            'info'    => ['bg' => '#1d4ed8', 'border' => '#3b82f6', 'icon' => 'ri-information-line',  'light' => '#eff6ff'],
            'success' => ['bg' => '#065f46', 'border' => '#10b981', 'icon' => 'ri-checkbox-circle-line','light' => '#ecfdf5'],
            'warning' => ['bg' => '#92400e', 'border' => '#f59e0b', 'icon' => 'ri-error-warning-line', 'light' => '#fffbeb'],
            'danger'  => ['bg' => '#991b1b', 'border' => '#ef4444', 'icon' => 'ri-close-circle-line',  'light' => '#fef2f2'],
        ];
        $ac = $annColors[$ann->type] ?? $annColors['info'];
    @endphp
    <div id="ann-{{ $ann->id }}"
         data-ann-id="{{ $ann->id }}"
         style="background:{{ $ac['bg'] }};border-bottom:2px solid {{ $ac['border'] }};color:#fff;padding:10px 20px;display:flex;align-items:center;gap:12px;pointer-events:all;animation:ann-slide-down .35s ease;font-family:inherit;">
        <i class="{{ $ac['icon'] }}" style="font-size:18px;flex-shrink:0;opacity:.9;"></i>
        <div style="flex:1;min-width:0;">
            <span style="font-weight:700;font-size:13px;">{{ $ann->title }}</span>
            @if($ann->body)
                <span style="font-size:12px;opacity:.85;margin-right:8px;">— {{ Str::limit($ann->body, 120) }}</span>
            @endif
        </div>
        <button onclick="annDismiss({{ $ann->id }})"
                title="إغلاق"
                style="background:rgba(255,255,255,.15);border:none;color:#fff;width:24px;height:24px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;transition:background .15s;"
                onmouseover="this.style.background='rgba(255,255,255,.3)'"
                onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <i class="ri-close-line"></i>
        </button>
    </div>
    @endforeach
</div>

<style>
@keyframes ann-slide-down {
    from { transform: translateY(-100%); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
}
</style>

<script>
(function() {
    var dismissed = JSON.parse(localStorage.getItem('ann-dismissed') || '{}');
    document.querySelectorAll('[data-ann-id]').forEach(function(el) {
        var id = el.getAttribute('data-ann-id');
        if (dismissed[id]) el.style.display = 'none';
    });
    var stack = document.getElementById('ann-stack');
    if (stack && stack.querySelectorAll('[data-ann-id]:not([style*="display: none"])').length === 0) {
        stack.style.display = 'none';
    }
})();

function annDismiss(id) {
    var el = document.getElementById('ann-' + id);
    if (el) {
        el.style.transition = 'opacity .25s, transform .25s';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(function() {
            el.style.display = 'none';
            var dismissed = JSON.parse(localStorage.getItem('ann-dismissed') || '{}');
            dismissed[id] = Date.now();
            localStorage.setItem('ann-dismissed', JSON.stringify(dismissed));
            // Hide stack if all dismissed
            var stack = document.getElementById('ann-stack');
            if (stack && stack.querySelectorAll('[data-ann-id]:not([style*="display: none"])').length === 0) {
                stack.style.display = 'none';
            }
        }, 260);
    }
}
</script>
@endif
@endauth
