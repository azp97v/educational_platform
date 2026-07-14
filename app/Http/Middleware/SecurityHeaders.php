<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $nonce = Str::random(32);
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(self), geolocation=(), compute-pressure=(self)');

        if ($request->isSecure() || env('APP_ENV') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' https://*.pusher.com https://*.reverb.com https://cdn.jsdelivr.net https://unpkg.com https://www.youtube.com https://s.ytimg.com https://js-de.sentry-cdn.com https://browser.sentry-cdn.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://unpkg.com",
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://unpkg.com",
            "img-src 'self' data: blob: https:",
            "media-src 'self' data: blob: https:",
            "connect-src 'self' blob: ws: wss: https://*.pusher.com https://*.reverb.com http://localhost:* https://cdn.jsdelivr.net https://www.youtube.com https://staticimgly.com https://*.staticimgly.com https://*.100ms.live wss://*.100ms.live https://*.sentry.io https://*.ingest.sentry.io https://*.ingest.de.sentry.io",
            "worker-src 'self' blob: https://cdn.jsdelivr.net https://staticimgly.com https://*.staticimgly.com",
            "script-src-elem 'self' 'unsafe-inline' blob: https://cdn.jsdelivr.net https://unpkg.com https://*.pusher.com https://*.reverb.com https://www.youtube.com https://s.ytimg.com https://js-de.sentry-cdn.com https://browser.sentry-cdn.com",
            "frame-src 'self' https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
