<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('munchify:send-interview-reminders')->dailyAt('08:00');
Schedule::command('munchify:check-job-deadlines')->dailyAt('00:01');
Schedule::command('munchify:aggregate-analytics')->hourly();
