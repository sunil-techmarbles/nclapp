<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Asin;
use File;

class AsinPriceUpdate extends Command
{
    public $UpdateAsinCount;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AsinPrice:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update price for ASINs in ASIN Section';

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
        // $subject = 'AsinPrice:update '. date('Y-m-d h:i:s');
        // $emailsToSend = "sunil.techmarbles@gmail.com";
        // Mail::raw('Test Crons for AsinPrice:update', function($m) use ( $subject, $emailsToSend)
        // {
        //         $m->to( $emailsToSend )->subject($subject);
        // });
 
        $this->UpdateAsinPriceCron();
        echo "$this->UpdateAsinCount records updated"; 
        die();
    }

    public function UpdateAsinPriceCron()
    {
        $asinRecords = Asin::getAsinForPriceUpdate();
        $this->UpdateAsinCount = 0;
        foreach ($asinRecords as $key => $asinRecord)
        {
            if(!empty($asinRecord["asin"]) || $asinRecord["asin"] != 0)
            {
                $price = $this->getAsinPrice($asinRecord["asin"]);
                if($price )
                {
                    Asin::UpdateAsinPrice($price, $asinRecord['id'] );
                    $this->UpdateAsinCount++;
                }
            }
        }
    }

    public function getAsinPrice($asin)
    {
        // if(File::exists("http://www.amazon.com/gp/aw/d/".$asin))
        // {
            $html = file_get_contents("http://www.amazon.com/gp/aw/d/$asin");
            $price = $this->getBetween($html,'data-asin-price="','"');
            pr( $price );  die;
            return $price; 
        // }
            return false;
    }

    public function getBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        if ($len<=0) return '';
        return substr($string, $ini, $len);
    }
}