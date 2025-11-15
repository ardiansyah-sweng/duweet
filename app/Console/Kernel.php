<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int,string>
     */
    protected $commands = [
        \App\Console\Commands\AccountsRecount::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('accounts:recount')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // load(__DIR__.'/Commands');
    }
}
