<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule stock price updates
Schedule::command('stocks:refresh')
    ->dailyAt('09:30')
    ->name('Morning Stock Refresh')
    ->description('Update stock prices from Yahoo Finance at market open');

Schedule::command('stocks:refresh')
    ->dailyAt('16:00')
    ->name('Market Close Stock Refresh')
    ->description('Update stock prices at market close');

// Optional: Refresh specific high-value stocks more frequently during market hours
Schedule::command('stocks:refresh --symbol=BBRI --symbol=KEEN --symbol=MPMX')
    ->hourly()
    ->between('09:00', '16:00')
    ->weekdays()
    ->name('Hourly High-Value Stock Refresh')
    ->description('Update major stock holdings every hour during trading hours');
