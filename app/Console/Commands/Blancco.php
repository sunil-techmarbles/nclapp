<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Config;

class Blancco extends Command
{
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

    public $basePath ; 

    public function __construct()
    {
        parent::__construct();
        $this->basePath  = base_path().'/public/blancco/'; 
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // for getting data.xml of all blancco data 
        $this->GetAllDataFileBlancco();
    }

    public function GetAllDataFileBlancco()
    {
        $format = "xml";
        $requestFilePath = $this->basePath . "all-reports.xml";
        $requestFileName = 'all-reports.xml';
        $this->blancooCurlRequest($requestFilePath, $requestFileName, 'fulldata', $format, 'data', 'all');
    }

    public function blancooCurlRequest($request_filePath , $request_file_name , $type , $format , $return_file_name , $reportUuid)
    {
    }
}










