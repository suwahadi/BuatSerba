<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::job(new \App\Jobs\CheckOrderExpirationJob)->everyMinute();

// Premium membership expiry check - runs daily at 1 AM
Schedule::command('premium:expire-memberships')->dailyAt('01:00');
