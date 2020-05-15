<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Supplies;
use App\SupplieEmail;
use App\SupplieAsinModel;
use App\Asin;
use Carbon\Carbon;
use App\UserCronJob;
use App\MessageLog;

class InventryEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Inventry:email';

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
        // $subject = 'Inventry:email '. date('Y-m-d h:i:s');
        // $emailsToSend = "sunil.techmarbles@gmail.com";
        // Mail::raw('Test Crons for Inventry:email', function($m) use ( $subject, $emailsToSend)
        // {
        //         $m->to( $emailsToSend )->subject($subject);
        // });

        $reorderQty = 0;
        $dt = date("Y-m-d",strtotime("-7 days"));
        $supplyDatas = Supplies::getEmailsAndSupplyrecord($dt);
        if($supplyDatas->count() > 0)
        {
            $supplyDatas = $supplyDatas->toArray();
            foreach ($supplyDatas as $key => $r)
            {
                $varKeys = array_keys($r);
                //get_supplie_emails
                if($reorderQty) $r[$key]["reorder_qty"] = $reorderQty;
                $body = $r["email_tpl"];
                $subject = $r['email_subj'];
                foreach($varKeys as $v)
                {
                    if($v == 'get_supplie_emails')
                    {
                        break;
                    }
                    $body = str_replace("[".$v."]",$r[$v],$body);
                }
                $user = [];
                foreach ($r['get_supplie_emails'] as $key => $value)
                {
                    if (filter_var($value['email'], FILTER_VALIDATE_EMAIL))
                    {
                        array_push($user, $value['email']);
                    }
                }
                if(sizeof($user) > 0 && $r['dlv_time'] && $r["item_name"] && $r["vendor"] && $r["part_num"] && $r["qty"])
                {
                    $current = Carbon::now();
                    Supplies::updateMailSentTime($r["id"],$current);
                    Mail::raw($body, function ($m) use ($subject,$user) {
                        $m->to($user)
                        ->subject($subject);
                    });
                    MessageLog::addLogMessageRecord('supply '.$r["id"]." request for reorder has been sent","Supply Reorder Mail", "Success");
                }
            }
        }
        die('done');
    } 
}

