<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Config;

class Blancco extends Command
{
    public $basePath;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blancco:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blancco api for getting data of mobiles and create pdf and xml files for makor api';

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
        $this->basePath  = base_path().'/public/blancco/'; 
        $this->GetAllDataFileBlancco();
    }

    public function GetAllDataFileBlancco()
    {
        $format = "xml";
        $requestFilePath = $this->basePath . "all-reports.xml";
        $requestFileName = 'all-reports.xml';
        $this->blancooCurlRequest($requestFilePath, $requestFileName, 'fulldata', $format, 'data', 'all');
    }

    public function blancooCurlRequest($requestFilePath , $requestFileName , $type , $format , $returnFileName , $reportUuid)
    {

    } 

}