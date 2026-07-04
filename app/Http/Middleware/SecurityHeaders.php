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

        if (str_contains($response->headers->get('Content-Type', 'text/html'), 'text/html')) {
            $content = $response->getContent();
            $content = preg_replace_callback(
                '/<script\b(?!\s[^>]*\bsrc\s*=\s*["\'])([^>]*)>/is',
                function ($m) use ($nonce) {
                    if (str_contains($m[0], 'nonce=')) {
                        return $m[0];
                    }
                    return '<script' . $m[1] . ' nonce="' . $nonce . '">';
                },
                $content
            );
            $response->setContent($content);
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure() || env('APP_ENV') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' https://*.pusher.com https://*.reverb.com https://cdn.jsdelivr.net https://unpkg.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://unpkg.com",
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://unpkg.com",
            "img-src 'self' data: blob: https:",
            "media-src 'self' data: blob: https:",
            "connect-src 'self' ws: wss: https://*.pusher.com https://*.reverb.com http://localhost:* https://cdn.jsdelivr.net",
            "worker-src 'self' blob:",
            "frame-src 'self' https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
