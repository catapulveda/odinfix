<?php

namespace App\Console;

use App\Console\Commands\DeleteDomains;
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
        // Commands\Inspire::class,
        Commands\TaskExecute::class,
        Commands\Test::class,
        Commands\MultiLoginTasks::class,
        Commands\DeleteMultiloginTasks::class,
        DeleteDomains::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('task:exec')->cron('* * * * *');
        // $schedule->command('inspire')
        //          ->hourly();
    }
}
