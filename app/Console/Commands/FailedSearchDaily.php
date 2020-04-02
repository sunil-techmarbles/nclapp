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
        $date = date('Y-m-d');

        $FailedSearchRecords = FailedSearch::getRecordByDate($date);

        $this->CreateCSVFile($FailedSearchRecords, $fileName);

        die("****");


        die('Failed Search Daily');
    }


    /**
     * Create a CSV file.
     *
     * @return mixed
     */
    public function CreateCSVFile($FailedSearchRecords, $fileName)
    {
        $fp = fopen(dirname(__FILE__).'/'.$fileName, "w");
        fputcsv($fp, ["Model","partNo","Brand","Category","require_pn","Added"]);

        foreach ($FailedSearchRecords as $i)
        {
            $row = [
                    $i['model_or_part'],
                    $i['partNo'],
                    $i['Brand'],
                    $i['Category'],
                    $i['require_pn'],
                    $i['on_datetime']
                ];
            fputcsv($fp, $row);
        }
        fclose($fp);
    }   
}