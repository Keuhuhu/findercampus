<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-expire laporan setiap hari jam 02:00 pagi
Schedule::command('laporan:auto-expire --days=30')->dailyAt('02:00');
