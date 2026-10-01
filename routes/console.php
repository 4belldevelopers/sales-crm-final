<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Add scheduler here
Schedule::command('interview:send-reminder')->everyMinute();
Schedule::command('temp-attachments:clean')->hourly();

// AppTracker Transactional Outbox: Process pending ES & Reverb events and prune completed events
Schedule::command('outbox:process-es --limit=100')
    ->everyMinute()
    ->withoutOverlapping(2)
    ->runInBackground();

Schedule::command('outbox:process-reverb --limit=100')
    ->everyMinute()
    ->withoutOverlapping(2)
    ->runInBackground();

Schedule::command('apptracker:prune-outbox --days=7')
    ->dailyAt('03:30')
    ->withoutOverlapping(10)
    ->runInBackground();

