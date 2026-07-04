<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $start = microtime(true);
        $checks = [];
        $healthy = true;

        // Database
        try {
            DB::connection()->getPdo();
            $db = ['status' => 'ok', 'driver' => DB::connection()->getDriverName()];
        } catch (\Throwable $e) {
            $db = ['status' => 'error', 'message' => $e->getMessage()];
            $healthy = false;
        }
        $checks['database'] = $db;

        // Cache (Redis)
        try {
            Cache::store('redis')->set('health_check', microtime(true), 1);
            $cached = Cache::store('redis')->get('health_check');
            $checks['cache'] = ['status' => 'ok', 'write' => $cached !== null];
        } catch (\Throwable $e) {
            $checks['cache'] = ['status' => 'error', 'message' => $e->getMessage()];
            $healthy = false;
        }

        // Redis direct
        try {
            Redis::connection()->ping();
            $checks['redis'] = ['status' => 'ok'];
        } catch (\Throwable $e) {
            $checks['redis'] = ['status' => 'error', 'message' => $e->getMessage()];
            $healthy = false;
        }

        // Storage
        try {
            $storage = Storage::disk('local');
            $storage->put('health_check.txt', microtime(true));
            $checks['storage'] = ['status' => 'ok', 'writable' => $storage->exists('health_check.txt')];
            $storage->delete('health_check.txt');
        } catch (\Throwable $e) {
            $checks['storage'] = ['status' => 'error', 'message' => $e->getMessage()];
            $healthy = false;
        }

        $elapsed = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => $healthy ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'response_time_ms' => $elapsed,
            'app_env' => app()->environment(),
            'app_debug' => config('app.debug'),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }
}
