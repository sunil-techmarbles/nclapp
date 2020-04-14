<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Asin;
use App\MessageLog;
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
        $result = $this->UpdateAsinPriceCron();
        echo implode(',', $result). "These Asin number's price updated successfully"; 
        die();
    }

    public function UpdateAsinPriceCron()
    {
        $asinRecords = Asin::getAsinForPriceUpdate();
        $this->UpdateAsinCount = 0;
        $asinArray = [];
        foreach ($asinRecords as $key => $asinRecord)
        {
            if(!empty($asinRecord["asin"]) || $asinRecord["asin"] != 0)
            {
                $price = $this->getAsinPrice($asinRecord["asin"]);
                if($price)
                {
                    array_push($asinArray, $asinRecord["asin"]);
                    Asin::UpdateAsinPrice($price, $asinRecord['id'] );
                    $this->UpdateAsinCount++;
                }
            }
        }
        return $asinArray;
    }

    public function getAsinPrice($asin)
    {
        try
        {
            $url = "http://www.amazon.com/gp/aw/d/".$asin;
            $html = file_get_contents($url);
            $price = getBetween($html,'data-asin-price="','"');
            return $price;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}