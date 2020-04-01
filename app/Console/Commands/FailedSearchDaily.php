<?php
namespace App\Console\Commands;
use App\ReportEmail;
use App\FailedSearch;

use Illuminate\Console\Command;

class FailedSearchDaily extends Command
{
    public $sessionReportDir , $basePath;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedSearch:daily';

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

        $fileName = $this->sessionReportDir .'/failedsearch_'.date('Y-m-d').'.csv';

        pr($fileName);
        die('Failed Search Daily');
        
    }
}