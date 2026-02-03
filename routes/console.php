<?php

use App\Models\PendingUser;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::call(function () {
    PendingUser::where('otp_expires_at', '<', now())
        ->delete();
})->everyMinute();
