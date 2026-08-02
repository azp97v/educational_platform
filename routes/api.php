<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes — V1
|--------------------------------------------------------------------------
| Auth via Sanctum SPA session cookies.
| EnsureFrontendRequestsAreStateful handles CSRF for same-domain SPA calls.
| No hardcoded TURN credentials. APP_DEBUG=false in production.
*/

Route::prefix('v1')->name('api.v1.')
    ->middleware(EnsureFrontendRequestsAreStateful::class)
    ->group(function () {

        // Public — rate-limited to prevent brute-force / signup abuse
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('login',    [AuthController::class, 'login'])->name('login');
            Route::post('register', [AuthController::class, 'register'])->name('register');
        });

        // Protected endpoints
        Route::middleware('auth:sanctum')->group(function () {

            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('user',    [AuthController::class, 'me'])->name('user');

            // Dashboard
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Courses (any authenticated user)
            Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

            // Admin only — teacher must NOT be able to manage users or change roles
            Route::middleware('role:admin')->group(function () {
                Route::apiResource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
            });
        });
    });
