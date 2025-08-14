<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendActivityReminders::class,
        \App\Console\Commands\SendNotifLeaderMonthlyReport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:send-birthday-reminders')->dailyAt('00:20');
        $schedule->command('send-notif-leader-monthly-report')->monthlyOn(1, '07:30');

        $schedule->command('app:dispatch-messages')->dailyAt('13:30')->weekdays();
        $schedule->command('send-activity-reminders')->everyMinute();
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping()->appendOutputTo(storage_path('logs/queue-worker.log'));

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
