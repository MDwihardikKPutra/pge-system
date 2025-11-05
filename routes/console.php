<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule work reminder notifications
// Run every 30 minutes between 8 AM and 5 PM to check for reminders
Schedule::command('notifications:send-work-reminders')
    ->everyThirtyMinutes()
    ->between('8:00', '17:00'); // Run every 30 minutes between 8 AM and 5 PM
