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
            ->daily();

        $schedule->command('BlanccoMakor:api')
            ->daily();

        $schedule->command('WipeMakor:api')
            ->daily();

        $schedule->command('WipeBiosMakor:api')
            ->daily();

        $schedule->command('failedSearch:daily')
            ->daily();

        $schedule->command('faildSearch:weekly') 
            ->weekly();

        $schedule->command('WipeBiosBlanccoFiles:count')
            ->daily();

        $schedule->command('COA:weekly')
            ->weekly();

        $schedule->command('AsinPrice:update')
            ->daily();

        $schedule->command('Inventry:email')
            ->daily();

        $schedule->command('Shopify:sync')
            ->daily();

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
