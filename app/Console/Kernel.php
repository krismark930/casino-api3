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
        \App\Console\Commands\LotteryCheckoutCommand::class,
        \App\Console\Commands\AGINTransactionCommand::class,
        \App\Console\Commands\XINTransactionCommand::class,
        \App\Console\Commands\YOPLAYTransactionCommand::class,
        \App\Console\Commands\HUNTERTransactionCommand::class,
        \App\Console\Commands\BBINTransactionCommand::class,
        \App\Console\Commands\PTTransactionCommand::class,
        \App\Console\Commands\MGTransactionCommand::class,
        \App\Console\Commands\SportsCheckoutCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('lottery:checkout')->everyMinute();
        $schedule->command('agin:transaction')->everyMinute();
        $schedule->command('xin:transaction')->everyMinute();
        $schedule->command('yoplay:transaction')->everyMinute();
        $schedule->command('hunter:transaction')->everyMinute();
        $schedule->command('bbin:transaction')->everyMinute();
        $schedule->command('mg:transaction')->everyMinute();
        $schedule->command('pt:transaction')->everyMinute();
        $schedule->command('sports:checkout')->everyMinute();
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
