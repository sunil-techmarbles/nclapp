<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CountWipeBiosBlancoFilesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WipeBiosBlanccoFiles:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command adds data to blanco , wipe , bios table and wipe reports table to get the total file count in wipe report section.';

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
        die("******");
    }
}
