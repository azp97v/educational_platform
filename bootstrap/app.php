<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Locale must be set early so all views receive the correct locale
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // تسجيل نشاط المستخدم (تحديث الـ streak)
        $middleware->append(\App\Http\Middleware\UpdateUserActivity::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        // prepend so it runs LAST on the response — after StartSession sets no-cache,private
        $middleware->prepend(\App\Http\Middleware\NoCacheHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is(...['api/*', 'ajax/*', 'messaging/*'])) {
                $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                    ? $e->getStatusCode()
                    : 500;

                return response()->json([
                    'success' => false,
                    'message' => $status === 500 ? 'Internal server error' : $e->getMessage(),
                ], $status);
            }

            if (app()->isDownForMaintenance()) {
                return response()->view('errors.503', [], 503);
            }
        });

        $exceptions->reportable(function (Throwable $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (app()->bound(\Sentry\State\HubInterface::class)) {
                \Sentry\captureException($e);
            }

            \App\Models\SystemError::capture($e, request());
        });
    })->create();
