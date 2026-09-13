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
