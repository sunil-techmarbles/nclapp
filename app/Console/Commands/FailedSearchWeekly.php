<?php
namespace App\Console\Commands;
use App\ReportEmail;
use App\FailedSearch;
use App\UserCronJob;
use Illuminate\Support\Facades\Mail;

use Illuminate\Console\Command;

class FailedSearchWeekly extends Command
{
    public $sessionReportDir , $basePath;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faildSearch:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to send Failed Search Weekly Report ';

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
        // $subject = 'faildSearch:weekly '. date('Y-m-d h:i:s');
        // $emailsToSend = "sunil.techmarbles@gmail.com";
        // Mail::raw('Test Crons for faildSearch:weekly', function($m) use ( $subject, $emailsToSend)
        // {
        //         $m->to( $emailsToSend )->subject($subject);
        // });
        $this->basePath  = base_path().'/public';
        $this->sessionReportDir = $this->basePath . "/session-reports";
        $date = date('Y-m-d');
        $fileName = 'search_'.$date.'.csv';
        $filePath =  $this->sessionReportDir .'/'.$fileName;
        $findDate = date('Y-m-d',strtotime('-7 days'));
        $FailedSearchRecords = FailedSearch::getRecordByDate($findDate);
        $this->CreateCSVFile($FailedSearchRecords, $filePath);
        $this->SendFailedSearchReportDaily( $fileName, $filePath  );
        die('Failed Search Weekly Report Sent');
    }

    public function SendFailedSearchReportDaily( $fileName, $filePath )
    {
        $emails = ReportEmail::getRecordForEdit('Weekly');
        $e_mails = [];
        $reportEmailsWeekly = UserCronJob::getCronJobUserEmails('reportEmailsWeekly');
        if($reportEmailsWeekly->count() > 0)
        {
            foreach ($reportEmailsWeekly as $key => $value) {
                $e_mails[] = $value->email;
            }
        }
        $emailsToSend = ($reportEmailsWeekly->count() > 0) ? $e_mails : explode(', ', $emails[0]);
        // $emailsToSend =  explode(', ', $emails[0]);
        $subject = "Weekly Failed Search Report";
        $body = "Please find the Failed Search report attached";
        Mail::raw($body, function($m) use ( $subject, $emailsToSend, $filePath, $fileName )
        {
                $m->to( $emailsToSend )->subject($subject);
                $m->attach( $filePath , array(
                            'as' => $fileName,
                            'mime' => 'csv')
                        );
        });
    }


    /**
     * Create a CSV file.
     *
     * @return mixed
     */
    public function CreateCSVFile($FailedSearchRecords, $fileName)
    {
        $fp = fopen( $fileName, "w" );
        fputcsv($fp, ["Model","partNo","Brand","Category","require_pn","Added"]);

        if( !empty( $FailedSearchRecords ))
        {
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
        }
        else
        {
             $row = [
                    'No data found',
                ];
                fputcsv($fp, $row);
        }
        fclose($fp);
    }
}
