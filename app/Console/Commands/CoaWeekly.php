<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CoaReport;
use App\Asin;
use App\ReportEmail;
use App\UserCronJob;
use File;
use Illuminate\Support\Facades\Mail;

class CoaWeekly extends Command
{
    public $COAReportDir , $basePath, $refurbAssetDataDir;
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
        $this->refurbAssetDataDir = $this->basePath.'/refurb-asset-data';
        $date = date('Y-m-d');
        $fileName = 'coa_'. $date .'.csv';
        $filePath =  $this->sessionReportDir .'/'.$fileName;
        $findDate = date('Y-m-d',strtotime('-7 days'));
        $CoaReportRecords = CoaReport::getRecordByDate($findDate);
        $this->CreateCSVFile($CoaReportRecords, $filePath);
        $this->SendCOAReportWeekly($fileName, $filePath);
        die('Weekly COA report Sent');
    }


    public function SendCOAReportWeekly($fileName, $filePath)
    {
        $e_mails = [];
        $emails = ReportEmail::getRecordForEdit('coa');
        $reportEmailsWeekly = UserCronJob::getCronJobUserEmails('coaEmails');
        if($reportEmailsWeekly->count() > 0)
        {
            foreach ($reportEmailsWeekly as $key => $value) {
                $e_mails[] = $value->email;
            }
        }
        $emailsToSend = ($reportEmailsWeekly->count() > 0) ? $e_mails : explode(', ', $emails[0]);
        // $emailsToSend =  explode(', ', $emails[0]);
        $subject = "Weekly COA Report";
        $body = "Please find the weekly COA report attached";
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
    public function CreateCSVFile($CoaReportRecords, $filePath)
    {
        $fp = fopen( $filePath, "w" );
        fputcsv($fp, ["Asset","S/N","Old COA","New COA","WIN8","Model","CPU","Manufacturer","Added"]);
        foreach ($CoaReportRecords as $key => $CoaReportRecord)
        {
            if($CoaReportRecord['old_coa']=='WIN8 Activated')
            {
                $CoaReportRecord['old_coa'] = '';
                $CoaReportRecord['win8'] = 'Activated';
            }
            else
            {
                $CoaReportRecord['win8'] = '';
            }
            $model = $cpu = $maunf = '';
            $refurbAssetDataFile = $this->refurbAssetDataDir.'/'.$CoaReportRecord['asset'].'.json';
            if(File::exists($refurbAssetDataFile))
            {
                $adata = json_decode(file_get_contents($refurbAssetDataFile),true);
                $model = empty($adata['Model']) ? '' : $adata['Model'];
                $cpu = empty($adata['CPU']) ? '' : $adata['CPU'];
                if(!empty($adata['asin_id']))
                {
                    $manufacturer =  Asin::getAsinManufactureData($adata['asin_id']);
                    $maunf = empty($manufacturer['manufacturer']) ? '' : $manufacturer['manufacturer'];
                }
            }
            $row = [
                $CoaReportRecord['asset'],
                $CoaReportRecord['sn'],
                $CoaReportRecord['old_coa'],
                $CoaReportRecord['new_coa'],
                $CoaReportRecord['win8'],
                $model,
                $cpu,
                $maunf,
                $CoaReportRecord['added_on']
            ];
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}