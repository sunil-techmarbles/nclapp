<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;

class WipeMakor extends Command
{
    public $wipeDataDir;

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
        $this->basePath  = base_path().'/public';
        $this->wipeDataDir = $this->basePath . "/wipe-data";

        $this->createMakorRequestFromWipeData();
        die("Wipe Makor api done");
    }

    /**
     *  Function for reading all the xml files form Wipe data and create makor request. 
     *
     * @return mixed
     */
    public function createMakorRequestFromWipeData()
    {
        pr($this->wipeDataDir);
        die("**");
    }

}