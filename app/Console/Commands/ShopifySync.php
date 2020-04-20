<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ListData;
use Illuminate\Support\Facades\Mail;

class ShopifySync extends Command
{
    public $skuToIgnore = ['HP-600-M602-CE390A','HP-400-M401dn-CF280X','HP-P2035-CE505A','HP-400-M401dn-CF280X','HP-P2055dn-05A-CE505A','HP-600-M601-CE390A','HP-400-m401' ];
    public $baseurl = "https://14a2e3d9e7a3661419a88549b45aa63e:11305811ff0dcf66770d9a9e7e3d80ef@what-does-refurbished-mean.myshopify.com";
    public $sandboxMode = true;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Shopify:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cron Update Shopify SKU quantitiy based active counts in Inventory section';

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
        $subject = 'Shopify:sync '. date('Y-m-d h:i:s');
        $emailsToSend = "sunil.techmarbles@gmail.com";
        Mail::raw('Test Crons for Shopify:sync', function($m) use ( $subject, $emailsToSend)
        {
                $m->to( $emailsToSend )->subject($subject);
        });
        $stock = [];
        $getRunningList = ListData::getrunningList();

        foreach ($getRunningList as $RunningList)
        {
            $asin = strtolower($RunningList['asin']);
            if($asin) $stock[$asin] = $RunningList['cnt'];
        }

        $productsurl = $this->baseurl."/admin/api/2019-04/products.json?limit=250";
        $adjusturl = $this->baseurl."/admin/api/2019-04/inventory_levels/adjust.json";

        $products = json_decode($this->getApiDatas($productsurl),true);

        if(!empty($products['products']))
        {
            echo count($products['products'])." products loaded<br><br>";

            foreach($products['products'] as $product)
            {
                if(!empty($product['variants']))
                {
                    foreach($product['variants'] as $varient)
                    {
                        if($varient['sku'] != '')
                        {
                            ListData::updateShopifyProductId($product['id'] , $varient['sku'] );
                            echo $varient['sku'].": ".$varient['inventory_quantity']." on Shopify stock. <br>";
                            $levelsurl = $this->baseurl."/admin/api/2019-04/inventory_levels.json?inventory_item_ids=".$varient['inventory_item_id'];
                            $ldata = $this->getApiDatas($levelsurl);
                            $idata = json_decode($ldata,true);

                            if( isset($idata["inventory_levels"]))
                            {
                                foreach( $idata["inventory_levels"] as $inventryLevel )
                                {
                                    if($inventryLevel["location_id"] == "16299065399")
                                    {
                                        $currentQty = $inventryLevel["available"];
                                        $location = $inventryLevel["location_id"];
                                        $newQty = ( isset($stock[strtolower($varient['sku'])]) ) ? $stock[strtolower($varient['sku'])] : 0;

                                        if($currentQty != $newQty && strlen($varient['sku'])>10 && !in_array($varient['sku'], $this->skuToIgnore))
                                        {
                                            $adjust = $newQty - $currentQty;
                                            if( $this->sandboxMode) {
                                                $data = ["inventory_item_id"=>$varient['inventory_item_id'],"location_id"=>$location,"available_adjustment"=>$adjust];
                                                $res = $this->postApiDatas($adjusturl, $data);
                                                echo $res."<br>";
                                                if( $newQty > 0 )
                                                {
                                                    $productType = (stripos($varient['sku'],"-lap-")!== false) ? "Laptop" : "Computer";
                                                    $data = ["product"=>["id"=>$product['id'], "product_type"=> $productType]];
                                                }
                                                else
                                                {
                                                    $data = ["product"=>["id"=>$product['id'], "product_type"=> "OPTIONS_HIDDEN_PRODUCT"]];
                                                }
                                                if( $product['product_type'] != $data['product']['product_type'] )
                                                {
                                                    $url = $this->baseurl . "/admin/api/2019-04/products/".$product['id'].".json";
                                                    $res = $this->putApiDatas($url, $data);
                                                    echo $res."<br>";
                                                }
                                            } else {
                                                echo "The quantity on Location $location will be updated to $newQty <br>";
                                            }
                                        }
                                    }
                                }
                            }
                            echo "<br>";
                        }
                    }
                }
            }
        }
        die("sync shopify");
    }

    public function getApiDatas($productsurl)
    {
        $ch              = curl_init();
        curl_setopt($ch, CURLOPT_URL, $productsurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
        $apidata         = curl_exec($ch);
        curl_close($ch);
        return $apidata;
    }

    function postApiDatas($url,$data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close ($ch);
        if (!$response) 
        {
            return false;
        }
        return $response;
    }

    function putApiDatas($url,$data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
        if (!$response) 
        {
            return false;
        }
        return $response;
    }
}