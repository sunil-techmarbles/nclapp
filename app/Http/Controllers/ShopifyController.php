<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use File;
use Config;
use App\Asin;
use App\ListData;
use App\ShopifyPricingCustom;
use App\ShopifyBarCode;
use App\ShopifyImages;
use App\ShopifyPricing;

class ShopifyController extends Controller
{
	public $basePath, $current, $baseUrl, $productMainSiteUrl;
	public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->current = Carbon::now();
    	$this->baseUrl = "https://14a2e3d9e7a3661419a88549b45aa63e:11305811ff0dcf66770d9a9e7e3d80ef@what-does-refurbished-mean.myshopify.com";
    	$this->productMainSiteUrl = "https://refurbconnection.com/products";
    }

    public function redirectToShopifySite($productId)
    {
		$dataUrl = $this->baseUrl."/admin/api/2019-04/products/".$productId.".json?fields=id,handle";
		$data = json_decode(file_get_contents($dataUrl),true);
		if(!empty($data['product']['handle']))
		{
			$producturl = $this->productMainSiteUrl."/".$data['product']['handle'];
			Redirect::away($producturl)
		}
    }

	public function index(Request $request)
	{
		if($request->get('goto'))
		{
			$this->redirectToShopifySite($request->get('goto'));
		}
		$running_list = [];
		$upc_count = [];
		$mdl_list = [];
		$ff_list = [];
		$cpu_list = [];
		return view('admin.shopify.list', compact('running_list', 'upc_count', 'cpu_list', 'ff_list', 'mdl_list' ));
	}

	public function setModelId(Request $request)
	{
		if($request->ajax())
        {
			$mid = $request->get('mid')
			$asin = str_replace('model','',$request->get('asin'));
			if($mid && $asin)
			{
				$result = ListData::updateModelID($mid, $asin);
				if($result)
				{
					return 'OK';
				}
			}
			else
			{
				return "Unable to set the model info";
			}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}
}
