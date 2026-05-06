<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule commands
Schedule::command('app:send-deadline-reminders')->dailyAt('08:00')->description('Send deadline reminders at 8 AM daily');
Schedule::command('app:process-email-queue')->everyFiveMinutes()->description('Process email queue every 5 minutes');
Schedule::command('app:database-backup')->dailyAt('02:00')->description('Create database backup at 2 AM daily');
