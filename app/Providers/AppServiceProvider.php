<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(\App\Models\Course::class, \App\Policies\CoursePolicy::class);
        Gate::policy(\App\Models\SmartRewind::class, \App\Policies\SmartRewindPolicy::class);
    }
}
