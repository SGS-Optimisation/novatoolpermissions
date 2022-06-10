<?php

namespace App\Console;

use App\Console\Commands\FlaggedRulesReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Nova\Trix\PruneStaleAttachments;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            //(new PruneStaleAttachments)();
        })->daily();

        $schedule->command('report:jobs-previous-day:send')->dailyAt('7:00');
        $schedule->command('rules:reviewing:remind')->dailyAt('8:00');
        $schedule->command('rules:flagged:remind')->weeklyOn(1, '8:00');
        $schedule->command('cache:clear')->lastDayOfMonth();
        $schedule->command('cache:warmup')->hourly();
        $schedule->command('user:jobteam:populate')->dailyAt('0:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
