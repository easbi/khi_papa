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
        \App\Console\Commands\DispatchMessages::class,
        \App\Console\Commands\SendBirthdayReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Kirim reminder ulang tahun setiap hari pukul 00:20
        $schedule->command('app:send-birthday-reminders --no-interaction')
            ->dailyAt('00:20')
            ->appendOutputTo(storage_path('logs/send-birthday-reminders.log'));

        // Kirim laporan bulanan terakhir pukul 14:00
        $schedule->command('send-notif-leader-monthly-report --no-interaction')
            ->lastDayOfMonth('14:00')
            ->appendOutputTo(storage_path('logs/send-notif-leader-monthly-report.log'));

        // Dispatch daily messages tiap hari kerja pukul 11:30
        $schedule->command('app:dispatch-messages --no-interaction')
            ->weekdays()
            ->dailyAt('15:22')
            ->appendOutputTo(storage_path('logs/dispatch-messages.log'));

        // Kirim activity reminder setiap 10 menit
        $schedule->command('send-activity-reminders --no-interaction')
            ->everyTenMinutes()
            ->appendOutputTo(storage_path('logs/send-activity-reminders.log'));

        // Proses queue worker setiap menit (job queue akan diproses setiap 1 menit)
        $schedule->command('queue:work --stop-when-empty --timeout=50 --tries=3 --no-interaction')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/queue-worker.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
