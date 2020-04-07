<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CoaWeekly extends Command
{
    public $COAReportDir , $basePath;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'COA:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->sessionReportDir = $this->basePath . "/session-reports";
        $date = date('Y-m-d');
        $fileName = 'failedsearch_'. $date .'.csv';
        $filePath =  $this->sessionReportDir .'/'.$fileName;
        $findDate = date('Y-m-d',strtotime('-7 days'));

        pr(        $filePath); die;
        
        // $FailedSearchRecords = FailedSearch::getRecordByDate($date);
        die("***");

    }
}
