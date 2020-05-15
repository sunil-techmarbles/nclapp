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
        Commands\Blancco::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('blancco:api')
            ->hourly();

        $schedule->command('BlanccoMakor:api')
            ->hourly();

        $schedule->command('WipeMakor:api')
            ->hourly();

        $schedule->command('WipeBiosMakor:api')
            ->hourly();

        $schedule->command('failedSearch:daily')
            ->daily();

        $schedule->command('faildSearch:weekly') 
            ->weekly();

        $schedule->command('WipeBiosBlanccoFiles:count')
            ->hourly();

        $schedule->command('COA:weekly')
            ->weeklyOn(5, '13:00'); 

        $schedule->command('AsinPrice:update')
            ->twiceDaily(1, 13);

        // $schedule->command('Inventry:email')
        //     ->dailyAt('3:00'); 

        // $schedule->command('Shopify:sync')
            // ->twiceDaily(1, 13);

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
