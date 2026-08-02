<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes — V1
|--------------------------------------------------------------------------
| All routes return JSON. Auth via Sanctum session cookies (SPA mode).
| No hardcoded TURN credentials. APP_DEBUG=false in production.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public endpoints
    Route::post('login',    [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    // Protected endpoints
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user',    [AuthController::class, 'me'])->name('user');

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Courses
        Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

        // Admin / Teacher only
        Route::middleware('role:admin,teacher')->group(function () {
            Route::apiResource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
        });
    });
});
