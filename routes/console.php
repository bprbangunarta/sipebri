<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Retention of the audit log is the owner's own policy, done by hand with `php artisan audit:prune`: the system never deletes audit entries by itself.
Schedule::command('audit:verify')->dailyAt('02:00')->onOneServer();
