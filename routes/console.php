<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('messaging:delete-expired')->daily();
Schedule::command('messaging:deactivate-inactive')->daily();
Schedule::command('calls:cleanup')->everyMinute()->withoutOverlapping();

if (PHP_OS_FAMILY === 'Windows') {
    Schedule::exec(sprintf(
        'powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%s" -DbName "%s" -DbUser "%s" -DbPass "%s" -DbHost "%s"',
        base_path('scripts/backup.ps1'),
        config('database.connections.mysql.database'),
        config('database.connections.mysql.username'),
        config('database.connections.mysql.password'),
        config('database.connections.mysql.host'),
    ))->daily()->at('03:00')->withoutOverlapping();
}
