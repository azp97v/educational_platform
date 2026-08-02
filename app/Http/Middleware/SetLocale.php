<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['ar', 'en']);

        $raw = $request->cookie('locale')
            ?? session('locale')
            ?? config('app.locale');

        // Cast to string — guards against array-shaped cookie injection
        $locale = in_array((string) $raw, $supported, true)
            ? (string) $raw
            : config('app.locale', 'ar');

        App::setLocale($locale);

        return $next($request);
    }
}
