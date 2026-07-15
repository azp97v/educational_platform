try {
window.Echo = new Echo.default({
    broadcaster: 'reverb',
    key: @json(config('broadcasting.connections.reverb.key')),
    wsHost: @json(parse_url(config('app.url'), PHP_URL_HOST)),
    wsPort: 443,
    forceTLS: true,
    enabledTransports: ['wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
    },
});
} catch (e) { window.Echo = null; console.warn('[Echo] WebSocket init failed:', e); }

const { createApp } = Vue;

const safeLocalJson = (key, fallback) => {
try {
const raw = localStorage.getItem(key);
if (!raw) return fallback;
const parsed = JSON.parse(raw);
return parsed && typeof parsed === 'object' ? parsed : fallback;
} catch (_) {
localStorage.removeItem(key);
return fallback;
}
};