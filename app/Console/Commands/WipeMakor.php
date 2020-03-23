<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WipeMakor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WipeMakor:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Wipe report data to Makpor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        die("I*******II"); 

    }
}
