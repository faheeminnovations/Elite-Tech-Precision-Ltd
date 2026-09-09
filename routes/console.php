<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule PPM reminders to run daily at 8:00 AM
Schedule::command('emails:send-ppm-reminders')
    ->dailyAt('08:00')
    ->description('Send PPM reminder emails for overdue and upcoming PPMs');

// Schedule customer response reminders to run daily at 9:00 AM
Schedule::command('emails:send-customer-response-reminders')
    ->dailyAt('09:00')
    ->description('Send customer response review reminder emails');

// Schedule no-contract customer reminders to run weekly on Monday at 10:00 AM
Schedule::command('emails:send-no-contract-reminders')
    ->weeklyOn(1, '10:00')
    ->description('Send reminders for customers with no active contracts');

// Schedule engineer reminders to run daily at 7:00 AM
Schedule::command('emails:send-engineer-reminders')
    ->dailyAt('07:00')
    ->description('Send engineer task reminder emails');
