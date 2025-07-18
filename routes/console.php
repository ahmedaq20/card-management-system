<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:check')
    ->daily() // يشغله مرة كل يوم
    ->at('08:00') // اختياري: يشغله الساعة 8 صباحًا
    ->runInBackground();