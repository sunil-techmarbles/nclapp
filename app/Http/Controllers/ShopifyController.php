<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShopifyBulkRemoveImport;
use App\Imports\ShopifyBulkUploadImport;
use App\Exports\InventoryExport;
use Carbon\Carbon;
use Redirect;
use File;
use Config;
use App\Asin;
use App\Session;
use App\MessageLog;
use App\ListData;
use App\FormModel;
use App\FormData;
use App\FormsConfig;
use App\ShopifyPricingCustom;
use App\ShopifyBarCode;
use App\ShopifyImages;
use App\ShopifyPricing;
use App\Traits\CommenShopifyTraits;
use App\Http\Controllers\AuditController;

class ShopifyController extends Controller
{
	use CommenShopifyTraits;

	public $basePath, $current, $baseUrl, $productMainSiteUrl, $wipeData2, $methodData, $finalPrice;

	/**
     * Instantiate a new ShopifyController instance.
     */
	public function __construct($searchDataArray=[])
	{
    	/**
     	* Set value for common uses in the ShopifyController instance.
     	*/

     	$this->basePath = base_path().'/public';
     	$this->current = Carbon::now();
     	$this->baseUrl = Config::get('constants.finalPriceConstants.shopifyBaseUrl');
     	$this->productMainSiteUrl = Config::get('constants.finalPriceConstants.productMainSiteUrl');
     	$this->wipeData2 = $this->basePath.'/wipe-data2';
     	$this->methodData = $searchDataArray;

    	/**
     	* Calling a method productPriceCalculation for get final Price from ShopifyController instance.
     	*/
     	$this->finalPrice = $this->productPriceCalculation($this->methodData);

     }

    /**
 	* method use to redirect to shopify site for given product.
 	*/
 	public function redirectToShopifySite($productId)
 	{
 		$producturl = '';
 		$dataUrl = $this->baseUrl."/admin/api/2019-04/products/".$productId.".json?fields=id,handle";
 		$data = json_decode(file_get_contents($dataUrl),true);
 		if(!empty($data['product']['handle']))
 		{
 			$producturl = $this->productMainSiteUrl."/".$data['product']['handle'];
 		}
 		return $producturl;
 	}

    /**
 	* set a price for given product.
 	*/
 	public function setPrice($id, $price)
 	{
 		if($price)
 		{
 			$data = [
 				'price' => $price,
 				'asin' => $id
 			];
 			ShopifyPricingCustom::deleteExistRecord($id);
 			ShopifyPricingCustom::addNewRecorde((object) $data);
 		}
 		return redirect()->route('inventory');
 	}

    /**
 	* Method is use to remove mulitilpe record from import file.
 	*/
 	public function bulkRemoveRecord($request)
 	{
 		if($request->hasFile('datafile'))
 		{
 			$error = '';
 			try
 			{
				/**
			 	* Create ShopifyBulkRemoveImport instance for import file to perform remove opration.
			 	*/
			 	$import = new ShopifyBulkRemoveImport();
			 	Excel::import($import,request()->file('datafile'));
			 }
			 catch (\Maatwebsite\Excel\Validators\ValidationException $e)
			 {
			 	$error = $e->getCode().' '.$e->getMessage();
			 }
			 catch (\Exception $ex)
			 {
			 	$error = $ex->getCode().' '.$ex->getMessage();
			 }
			 catch (\Error $er)
			 {
			 	$error = $er->getCode().' '.$er->getMessage();
			 }
			 if($error)
			 {
			 	return redirect()->route('inventory')->with('error', $error);
			 }
			 else
			 {
			 	return redirect()->route('inventory')->with('success', 'Records remove successfully');
			 }
			}
		}

   	/**
 	* Method is use to upload mulitilpe record from import file.
 	*/
 	public function bulkUploadRecord($request)
 	{
 		if($request->hasFile('bulk_data'))
 		{
 			$error = [];
 			$currentSession = Session::getOpenStatucRecord($request, $status = 'open');
 			try
 			{
				/**
			 	* Create ShopifyBulkUploadImport instance for import file to perform upload opration.
			 	*/
			 	$import = new ShopifyBulkUploadImport($request->get("cpuname"));
			 	Excel::import($import,request()->file('bulk_data'));
			 }
			 catch (\Maatwebsite\Excel\Validators\ValidationException $e)
			 {
			 	$error = $e->getCode().' '.$e->getMessage();
			 }
			 catch (\Exception $ex)
			 {
			 	$error = $ex->getCode().' '.$ex->getMessage();
			 }
			 catch (\Error $er)
			 {
			 	$error = $er->getCode().' '.$er->getMessage();
			 }
			 if($error)
			 {
			 	return redirect()->route('inventory')->with('error', $error);
			 }
			 else
			 {
			 	return redirect()->route('inventory')->with('success', 'Records upload successfully');
			 }
			}
		}

   	/**
 	* Main function.
 	*/
 	public function index(Request $request)
 	{
 		if($request->get('goto'))
 		{
			/**
		 	* redirectToShopifySite call .
		 	*/
		 	$producturl = $this->redirectToShopifySite($request->get('goto'));
		 	if($producturl != '')
		 	{
		 		return redirect($producturl);
		 	}
		 	else
		 	{
		 		return redirect()->route('inventory');
		 	}
		 }

		 if($request->get('set_price'))
		 {
		 	$price = $request->get('setprice');

			/**
		 	* setPrice call .
		 	*/
		 	$this->setPrice($request->get('set_price'), $price);
		 }

		 if($request->get('remove'))
		 {
		 	ListData::updateRunStatus($request->get('remove'), $status='removed');
		 	redirect()->route('inventory');
		 }

		 if($request->get('bulk_remove'))
		 {
			/**
		 	* bulkRemoveRecord call with Request instance object.
		 	*/
		 	$this->bulkRemoveRecord($request);
		 }

		 if($request->get('bulk_upload'))
		 {
			/**
		 	* bulkUploadRecord call with Request instance object.
		 	*/
		 	$this->bulkUploadRecord($request);
		 }

		 $priceList = [];
		 $productIds = [];
		 $productsUrl = $this->baseUrl."/admin/api/2019-04/products.json?limit=250";
		 $products = json_decode(file_get_contents($productsUrl),true);
		 if(!empty($products['products']))
		 {
		 	foreach($products['products'] as $p)
		 	{
		 		if(!empty($p['variants'][0]['price'])) $priceList[$p['id']] = $p['variants'][0]['price'];
		 		if(!empty($p['variants'][0]['sku']))
		 		{
		 			$sku = $p['variants'][0]['sku'];
		 			ListData::updateShopifyProductId($p['id'], $sku);
		 			$productIds[$p['variants'][0]['sku']] = $p['id'];
		 		}
		 	}
		 }

		 ListData::updateShopifyAsinId($asinValue='', $model='none');
		 $data = ListData::getSelectedFields($fields= ['id','asset','mid','technology'], $query= ['asin' => '']);
		 foreach($data as $itm)
		 {
		 	$asset = $itm['asset'];
			// $debug .= $asset.": Loaded \n";
		 	$file = "";
		 	$this->wipeData2 = $this->basePath.'/wipe-data2';
		 	if (File::exists($this->wipeData2.'/'.$asset.'.xml'))
		 	{
		 		$file = $this->wipeData2.'/'.$asset.'.xml';
		 	}
		 	elseif(File::exists($this->wipeData2.'/bios-data/'.$asset.'.xml'))
		 	{
		 		$file = $this->wipeData2.'/bios-data/'.$asset.'.xml';
		 	}
		 	if($file)
		 	{
				// $debug .= $asset.": File readable ".$file. " \n";
		 		$xml = simplexml_load_file($this->wipeData2.'/'.$asset.'.xml');
		 		if($xml)
		 		{
					// $debug .= $asset.": File loaded\n";
		 			$xmlData = [];
		 			$i = 0;
		 			if(is_array($xml->component))
		 			{
		 				foreach ($xml->component as $c)
		 				{
		 					$i++;
		 					$key = strval($c["name"]);
		 					if(!isset($xmlData[$key])) 
		 						$xmlData[$key]=[];
		 					if(!in_array(strval($c),$xmlData[$key])) 
		 						$xmlData[$key][] = strval($c);
		 				}
		 			}
		 			if(!empty($xmlData["Model"][0]))
		 			{
		 				$model = trim(str_ireplace([
		 					' non-vPro',
		 					' DT',
		 					' CMT',
		 					' SFF',
		 					' USDT',
		 					' DM',
		 					' TWR',
		 					' MT',
		 					' AIO'], '' , $xmlData["Model"][0])
		 			);
		 				$fields = ['model' => $model];
		 				$query = ["id" => $itm['id']];
		 				ListData::updateSelectedFields($fields, $query);
		 			}
		 			else
		 			{
		 				$model = "none";
		 			}
		 			if(!empty($xmlData["ProcessorModel_Speed"][0]) && $model !='none')
		 			{
						// $debug .= $asset.": CPU Data found\n";
		 				$cpu = $xmlData["ProcessorModel_Speed"][0];
		 				$parts1 = explode("_",$cpu);
		 				$parts2 = explode("-",$parts1[0]);
		 				if(count($parts1)==2 && count($parts2)==2)
		 				{
							// $debug .= $asset.": CPU Data parced ".$cpu." \n";
		 					$gen = substr($parts2[1],0,1);
		 					if($itm['mid'])
		 					{
		 						$query = ["id" => $itm['mid']];
		 						$field = 'technology';
		 						$ff = FormModel::pluckCustomField($query, $field);
		 						if(count($ff) > 0)
		 						{
		 							$ff = $ff[0];
		 						}
		 					}
		 					else
		 					{
		 						$ff = $itm['technology'];
		 					} 
		 					if(!empty($parts1[1]) && !empty($parts2[1]) && !empty($ff))
		 					{
								// $debug .= $asset.": Data Validated ".$ff." \n";
		 						$update = [
		 							'model' => $model,
		 							'cpu' => $cpu,
		 							'cpu_core' => $parts2[0],
		 							'cpu_model' => $parts2[1],
		 							'cpu_speed' => $parts1[1],
		 							'cpu_gen' => $gen,
		 							'technology' => $ff
		 						];
		 						$asin = createAsinFromData($update);
		 						$update['asin'] = $asin;
		 						if(!empty($product_ids[$asin]))
		 						{
		 							$update['shopify_product_id'] = $product_ids[$asin];
		 						}
		 						if(!$itm['mid'])
		 						{
		 							$mid = ListData::getSelectedFields($fields = ["mid"], 
		 								$query = ['asin'=> $asin, 'mid[>]' => 0]
		 							);
		 							if($mid)
		 							{
		 								$update['mid'] = $mid;
		 							} 
		 						}
		 						ListData::updateSelectedFields($update, $query = ["id"=>$itm['id']]);
								// $debug .= $db->last()."\n\n";
		 					}
		 				}
		 			}
		 		}
		 	}
		 }

		 ListData::updateSelectedFields($fields= ['shopify_product_id' => ''], $query= ['shopify_product_id' => '0']);
		 $data = ListData::getSelectedFieldsByGroupBy($fields= ['asin','shopify_product_id']);
		 foreach($data as $d)
		 {
		 	$spi = $d['shopify_product_id'];
		 	$asin = $d['asin'];
		 	ListData::updateSelectedFields($fields= ['shopify_product_id' => $spi], $query= ['shopify_product_id'=> '', 'asin'=> $asin]);
		 }
		 ListData::updateShopifyAsinId($asinValue='', $model='none');
		 $upcCount = ShopifyBarCode::countEmptyAsinId($asin='');
		 $mdlList = [];
		 $ffList = [];
		 $cpuList = [];
		 $runningList = ListData::getrunningList();
		 $tcnt = 0;
		 foreach($runningList as &$r)
		 {
		 	$mid = $r['asin'];
		 	$tcnt += $r['cnt'];
		 	$r['items'] = ListData::getSelectedFields(
		 		$fields = ['mid','asset','added_on','model','technology','cpu'], 
		 		$query = [
		 			'asin' => $mid,
		 			'status' => 'active',
		 			'run_status' => 'active'
		 		]
		 	);
		 	$r['cpus'] = ListData::getDistinctRecordForCPU($mid);
		 	if(!in_array($r['model'],$mdlList))
		 	{
		 		$mdlList[] = $r['model'];
		 	}
		 	if(!in_array($r['technology'],$ffList))
		 	{
		 		$ffList[] = $r['technology'];
		 	}
		 	$r['cpg'] = strtolower($r['cpu_core']."-".$r['cpu_gen']).'gen';
		 	if(!in_array($r['cpg'],$cpuList))
		 	{
		 		$cpuList[] = $r['cpg'];
		 	}
		 	if(!empty($r['shopify_product_id']) && !empty($priceList[$r['shopify_product_id']]))
		 	{
		 		$r['shopify_price'] = $priceList[$r['shopify_product_id']];
		 	}
		 	else
		 	{
		 		$r['shopify_price'] = 0;
		 	}
			// to be removed
		 	if(empty($r['shopify_priduct_id']))
		 	{
		 		$a = createAsinFromData($r);
		 		if($a != $r['asin'])
		 		{
		 			$data = ListData::updateSelectedFields($fields= ['asin' => $a], $query= ['asin' => $r['asin']]);
		 			$r['asin'] = $a;
		 		}
		 	}
		 }
		 unset($r);
		 asort($mdlList);
		 asort($ffList);
		 asort($cpuList);
		 return view('admin.shopify.list', compact('runningList','upcCount','mdlList','ffList','cpuList','tcnt'));
		}

	/**
 	* Method productPriceCalculation use to get final price of product.
 	*/
 	public function productPriceCalculation($runningList)
 	{
 		if(!empty($runningList))
 		{
 			$priceDetail = ShopifyPricing::getShopifyPriceList($runningList);
 			$totalRecord = $priceDetail->count();
 			$calculateSalePrice = 0;
 			if (!empty($priceDetail))
 			{
 				foreach ($priceDetail as $key => $value)
 				{
 					if (isset($value['RAM']) && (empty($value['RAM']) || $value['RAM'] == 'No_RAM'))
 					{
 						$ramPrice = 10;
 					}
 					else
 					{
 						$ramPrice = Config::get('constants.finalPriceConstants.ramCost');
 					}

 					if (isset($value['Hard_Drive']) && (empty($value['Hard_Drive']) || $value['Hard_Drive'] == 'No_HD'))
 					{
 						$hardDrivePrice = 11;
 					}
 					else
 					{
 						$hardDrivePrice = Config::get('constants.finalPriceConstants.hdCost');
 					}
 					$totalCalulatePrice = $value['Price'] + $ramPrice + $hardDrivePrice;
 					$calculateSalePrice += $totalCalulatePrice;
 				}
 				try
 				{
 					$averagePrice = $calculateSalePrice / $totalRecord;
 				}
 				catch( \Exception $e )
 				{
 					$averagePrice = 0;
 				}
 				$salePrice = $averagePrice * Config::get('constants.finalPriceConstants.priceMargin');
 				$finalPrice = $salePrice + Config::get('constants.finalPriceConstants.osCost') + Config::get('constants.finalPriceConstants.processingCost');
 				$finalPrice = number_format((float) $finalPrice, 2, '.', '');
 				$priceDetail = ShopifyPricing::updateShopifyPriceFinalPrice($runningList, $finalPrice);
 			}
 			else
 			{
 				$finalPrice = getFinalPriceFromCustomPrice($runningList);
 				if(!$finalPrice)
 				{
 					$finalPrice = 0;
 				}
 			}
 			return $finalPrice;
 		}
 	}

 	public function setModelId(Request $request)
 	{
 		if($request->ajax())
 		{
 			$mid = $request->get('mid');
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

 	public function getAdditionalDataForNewRunlist($id)
 	{
 		$config = FormData::getAllRecord($id);
	    $insertDataArray = [];
	    $availablePort = [];
	    $availableVedioPort = [];
	    $insertDataArray['weight'] = '';
	    $insertDataArray['height'] = '';
	    $insertDataArray['width'] = '';
	    $insertDataArray['length'] = '';
	    $insertDataArray['graphics_processor'] = '';
	    $insertDataArray['model'] = '';
	    $insertDataArray['product_class'] = '';
	    $insertDataArray['condition'] = '';
	    $insertDataArray['asset_number'] = '';
	    $insertDataArray['screen_size'] = '';
	    $insertDataArray['color'] = '';
	    $insertDataArray['Memory_Slots'] = '';
	    $insertDataArray['Max_Memory_Capacity'] = '';
	    $config = $config->toArray();
	    $additionalDataArray = json_decode($config['data']);
	    if (!empty($additionalDataArray))
	    {
	        foreach ($additionalDataArray->items as $key => $additionalData)
	        {
	            if ($additionalData->key == 'Weight')
	            {
	                $insertDataArray['weight'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Height')
	            {
	                $insertDataArray['height'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Width')
	            {
	                $insertDataArray['width'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Length')
	            {
	                $insertDataArray['length'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Graphics_Processor')
	            {
	                $insertDataArray['graphics_processor'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Model')
	            {
	                $insertDataArray['model'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Product_Name')
	            {
	                $insertDataArray['product_class'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Asset_Number') 
	            {
	                $insertDataArray['asset_number'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Size')
	            {
	                $insertDataArray['screen_size'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Color')
	            {
	                $insertDataArray['color'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Memory_Slots')
	            {
	                $insertDataArray['Memory_Slots'] = $additionalData->value[0];
	            }
	            if ($additionalData->key == 'Max_Memory_Capacity')
	            {
	                $insertDataArray['Max_Memory_Capacity'] = $additionalData->value[0];
	            }
	      	   	if ($additionalData->key == 'RJ_45' && $additionalData->value[0] == 'Yes')
	      	   	{
	                $availablePort[] = str_replace("_", " ", $additionalData->key);
	            }
	            if ($additionalData->key == 'USB_2_0_Ports' && $additionalData->value[0] != '0')
	            {
	                $availablePort[] = str_replace("_", " ", $additionalData->key) . '*' . $additionalData->value[0];
	            }
	            if ($additionalData->key == 'USB_3_0_Ports' && $additionalData->value[0] != '0')
	            {
	                $availablePort[] = str_replace("_", " ", $additionalData->key) . '*' . $additionalData->value[0];
	            }
	            if ($additionalData->key == 'USB_C_Ports' && $additionalData->value[0] != '0')
	            {
	                $availablePort[] = str_replace("_", " ", $additionalData->key) . '*' . $additionalData->value[0];
	            }
	            if ($additionalData->key == 'SD_Card_Reader' && $additionalData->value[0] == 'Yes')
	            {
	                $availablePort[] = str_replace("_", " ", $additionalData->key);
	         	}
	            if ($additionalData->key == 'Headphone_Jack' && $additionalData->value[0] == 'Yes')
	            {
	                $availablePort[] = str_replace("_", " ", $additionalData->key);
	            }
	            if ($additionalData->key == 'Microphone_Jack' && $additionalData->value[0] == 'Yes')
	            {
	                $availablePort[] = str_replace("_", " ", $additionalData->key);
	            }
	            if ($additionalData->key == 'Available_Video_Ports' && !empty($additionalData->value[0]))
	            {
	                $availableVedioPort = implode(",", $additionalData->value);
	            }
	        }
	    }
	    $insertDataArray['available_port'] = implode(",", $availablePort);
	    $insertDataArray['available_vedio_port'] = $availableVedioPort;
	    return $insertDataArray;
	}

 	public function getShopifyAppleDataFromTable($searchData)
 	{
	    // $searchData['model'] = str_replace(' )', ')', $searchData['model']);
	    // if (strpos($searchData['model'], 'Mac') !== false)
	    // {
	    //     echo 'true';
	    // }
	    // else
	    // {
	    //     return;
	    // }
	    // $query = "select * from Mac_Data_Raw where `Apple_Model_Combined` LIKE '%" . $searchData['model']."%'";
	    // $results = $mysqli->query($query)->fetchAll(PDO::FETCH_ASSOC);

	    // if (empty($results))
	    // {
	    //     echo "MODEL NOT FOUND FOR APPLE PRODUCT, Model :" . $searchData['model'];
	    //     return;
	    // }
	    // if (count($results) == 1)
	    // {
	    //     return $results[0];
	    // }
	    // //match based on Processor Speed
	    // //remove extra spsaces
	    // $wipeProcessorname = $searchData['cpu_core'] . '-' . $searchData['cpu_model'];
	    // foreach ($results as $key => $result)
	    // {
	    //     if (!empty($result['Processor_Model']))
	    //     {
	    //         $dbProcessormodel = trim($result['Processor_Model']);
	    //         if (stripos($wipeProcessorname, $dbProcessormodel) !== FALSE)
	    //         {
	    //             $return = $result;
	    //             break;
	    //         }
	    //     }
	    // }
	    // if (!empty($return))
	    // {
	    //     return $return;
	    // }
	    // else
	    // {
	    //     return $results[0];
	    // }
 		return '';
	}

 	public function inventoryCSV(Request $request)
 	{
 		if(!empty($request->get('csv')))
 		{
 			return Excel::download(new InventoryExport, 'inventory.csv');
 		}
 	}

 	public function getBarCode($asin)
 	{
 		$upc = ShopifyBarCode::getUPS($asin, $orderby = '');
	    if (empty($upc))
	    {
	    	$upc = ShopifyBarCode::getUPS($asin='', $orderby = 'id');
	        $barCode = $upc[0]['upc'];
	        ShopifyBarCode::updateQueryFields(['asin' => $asin], ['upc' => $barCode]);
	    }
	    else
	    {
	        $barCode = $upc[0]['upc'];
	    }
	    return $barCode;
	}

	public function insertShopifyNewRunlistImages($asin, $shopifyProductId, $imageName)
	{
	    $correctedAsin = correctAsinForImages($asin);
	    $allImages = glob('../'.config('constants.finalPriceConstants.imagePathNew').$correctedAsin . '*');
	    foreach ($allImages as $key => $singleImage)
	    {
	        $singleImage = str_replace('../', '', $singleImage);
	        $imgUrl = __DIR__ . "/" . $singleImage;
	        $image = explode("_", $singleImage);
	        foreach ($image as $key => $checkImage)
	        {
	            if (strpos($checkImage, '.') !== FALSE)
	            {
	                $image = $checkImage;
	                break;
	            }
	        }

	        $image = explode(".", $image);
	        $position = $image[0];
	        $data['image'] = [
	            "position" => $position,
	            "attachment" => base64_encode(file_get_contents($imgUrl)),
	            "filename" => $image_name . "-" . $position . "." . $image[1]
	        ];
	        $imageData = ShopifyImages::getImageId($asin,$singleImage);
	        if (empty($imageData))
	        {
	            $url = $this->baseUrl."/admin/api/2019-04/products/".$shopifyProductId."/images.json";
	            $shopifyImageData = postApiData($url, $data);
	            if (isset($shopifyImageData['errors']))
			    {
			    	$message = "Error: " . $productsurl . "<br>" . $shopifyImageData['errors'];
			    	$type = 'Shopify product image';
			    	$status = 'failure';
			    	MessageLog::addLogMessageRecord($message, $type, $status);
			    }
			    $data = [
			    	'asin' => $asin,
			    	'image' => $singleImage,
			    	'shopify_image_id' => $shopifyImageData['image']['id']
			    ];
			    ShopifyImages::addimagerecord((object) $data);
	        }
	        else
	        {
	            $url = $this->baseUrl."/admin/api/2019-04/products/".$shopifyProductId."/images/".$imageData[0]['shopify_image_id'].".json";
	            $shopifyImageData = putApiData($url, $data);
	        }
	    }
	}

	public function createShopifyNewRunlistProduct($data, $runningList, $variantData)
	{
		$response = [];
	    $productsurl = $this->baseUrl."/admin/api/2019-04/products.json";
	    $shopifyData = postApiData($productsurl, $data);
	    if (isset($shopifyData['errors']))
	    {
	    	$message = "Error: " . $productsurl . "<br>" . $shopifyData['errors'];
	    	$type = 'Shopify product';
	    	$status = 'failure';
	    	MessageLog::addLogMessageRecord($message, $type, $status);
	    }

	    if ($shopifyData)
	    {
	        $allRunningList = ListData::updateSelectedFields($fields=['shopify_product_id' => $shopifyData['product']['id']], $query=['id' => $runningList['list_id']]);
	        $variantData['variant']['id'] = $shopifyData['product']['variants'][0]['id'];
	        $variantData['variant']['product_id'] = $shopifyData['product']['id'];
	        $productsurl = $this->baseUrl."/admin/api/2019-04/variants/" . $shopifyData['product']['variants'][0]['id'] . ".json";
	        $shopifyVariantData = putApiData($productsurl, $variantData);
	        if (isset($shopifyVariantData['errors']))
	        {
		        $message = "Error: " . $productsurl . "<br>" . $shopifyVariantData['errors'];
		    	$type = 'Shopify variant data';
		    	$status = 'failure';
		    	MessageLog::addLogMessageRecord($message, $type, $status);
		    }
	        $imageName = createImageName($data['product']['title']);
	        $this->insertShopifyNewRunlistImages($runningList['asin'], $shopifyData['product']['id'], $imageName);
	        $meassge = 'Created asin: ' . $runningList['model'] . ' => Shopify product id:' . $shopifyData['product']['id'];
	        $body = "Hi,

			The following product(s) have been created:
			".$runningList['model']." ".$runningList['form_factor']." ".$runningList['cpu_core']." " .
			                getProcessorGenration($runningList['cpu_model'])." ".$shopifyData['product']['id'].$this->productMainSiteUrl."

			/".$shopifyData['product']['handle'] . "
			Please review the listing and ensure the following:
			The product is created
			Pricing is correct
			SKU and UPC are assigned
			Product Type and Vendor is correct
			Tags are applied
			-
			Images are correct and accetable size
			Page layout is good
			Product options are assigned
			Reviews are added
			Google shopping feed settings are correct ";
			$user = config('constants.finalPriceConstants.syncProductAddedMailUser');
			$subject = "New Product Created";
			Mail::raw($body, function ($m) use ($subject,$user) {
                $m->to($user)
                ->subject($subject);
            });
            $status = true;
	    }
	    else
	    {
	        $meassge = 'Not created for asin:' . $runningList['asin'];
	        $status = false;
	    }
	    $response = ['message' => $meassge,
			'status' => $status];
		return $response;
	}

	public function updateShopifyNewRunlistProduct($data, $runningList, $variantData)
	{
		$response = [];
	    $productsurl = $this->baseUrl."/admin/api/2019-04/products/" . $runningList['shopify_product_id'] . ".json";
	    $shopifyData = putApiData($productsurl, $data);
	    if ($shopifyData)
	    {
	        $variantData['variant']['id'] = $shopifyData['product']['variants'][0]['id'];
	        $variantData['variant']['product_id'] = $shopifyData['product']['id'];
	        $productsurl = $this->baseUrl."/admin/api/2019-04/variants/" . $shopifyData['product']['variants'][0]['id'] . ".json";
	        $shopifyVariantData = putApiData($productsurl, $variantData);
	        $imageName = createImageName($data['product']['title']);
	        $this->insertShopifyNewRunlistImages($runningList['asin'], $shopifyData['product']['id'], $imageName);
	        $message = 'Updated Model: ' . $runningList['model'] . ' Shopify product id:' . $shopifyData['product']['id'];
	    	$status = true;
	    }
	    else
	    {
	        $message = 'Not updated for Model:' . $runningList['model'];
	        $status = false;
	    }
	    $response = ['message' => $meassge,
			'status' => $status];
	    return $response;
	}

 	public function syncAllToShopify(Request $request)
 	{
 		if($request->ajax())
		{	$output = '';
			$status = true;
			if (isset($request->ids) && !empty($request->ids))
	 		{
	 			foreach ($request->ids as $key => $id)
	 			{
	 				$allRunningList = ListData::getFormModelAndListData($id);
	 				if (!empty($allRunningList))
	 				{
	 					$runningList = $allRunningList->toArray();
	 					$asin = createAsinFromData($runningList);
	 					$imageAsin = createImageAsinFromData($runningList);
	 					$allImages = glob($this->basePath.'/'.config('constants.finalPriceConstants.imagePathNew').$imageAsin.'*');
	 					$insertDataArray = $this->getAdditionalDataForNewRunlist($runningList['id']);
	 					$insertDataArray['processer_gen'] = getProcessorGenration($runningList['cpu_model']);
	 					$runningList['cpu_model'] = getProcessorModel($runningList['cpu_model']);
	 					$runningList['condition'] = config('constants.finalPriceConstants.condition');
	 					$modelData = explode(' ', $runningList['model']);
	 					$insertDataArray['series'] = $modelData[0];
	 					$runningList['form_factor'] = $runningList['technology'];
	 					$runningList['asin'] = $asin;
	 					$runningList['manufacturer'] = getManufacturerForNewRunlistdata($modelData[0]);
	 					$runningList['list_id'] = $runningList['list_id'];
	 					$price = $this->productPriceCalculation($runningList);
	 					$appleData = $this->getShopifyAppleDataFromTable($runningList);
	 					if (!empty($appleData))
	 					{
	 						$insertDataArray['product_class'] = 'Apple';
	 						$runningList['manufacturer'] = $appleData['Manufacturer'];
	 					}
	 					switch ($insertDataArray['product_class'])
	 					{
	 						case 'Computer':
	 							$dataObject = $this->init($runningList, $insertDataArray, $appleData='', $insertDataArray['product_class'], 'computer');
	 							break;
	 						case 'Laptop':
	 							$dataObject = $this->init($runningList, $insertDataArray, $appleData='', $insertDataArray['product_class'], 'laptop');
	 							break;
	 						case 'All_In_One':
	 							$dataObject = $this->init($runningList, $insertDataArray, $appleData='', $insertDataArray['product_class'], 'laptop');
	 							break;
	 						case 'Printer':
	 						case 'Apple':
	 							$dataObject = $this->init($runningList, $insertDataArray, $appleData, $insertDataArray['product_class'], 'apple');
	 							break;
	 						default:
		 						$error = "Can't sync product. Reason: Class " . $insertDataArray['product_class'] . " not found for asin " . $asin . ". Valid classes are Computer & Laptop";
		 						$output = $error;
						    	$type = 'Shopify sync product';
						    	$status = 'failure';
						    	MessageLog::addLogMessageRecord($error, $type, $status);
	 							continue;
	 							break;
	 					}
	 					$data = $dataObject;
	 					$barCode = $this->getBarCode($asin);
	 					if ($price == 0 || $price == 0.00)
	 					{
	 						$price = 299.99;
	 					}
	 					$variantData['variant'] = [
	 						"price" => $price,
	 						"sku" => strtolower($asin),
	 						"inventory_management" => "shopify",
	 						"barcode" => $barCode,
	 						"weight" => $insertDataArray['weight'],
	 					];
	 					if (empty($runningList['shopify_product_id']) || $runningList['shopify_product_id'] == 0)
	 					{
	 						$output = $this->createShopifyNewRunlistProduct($data, $runningList, $variantData);
	 					}
	 					else
	 					{
	 						$data['product']['id'] = $runningList['shopify_product_id'];
	 						$productsurl = $this->baseUrl."/admin/api/2019-04/products/".$runningList['shopify_product_id'] . ".json";
	 						$shopifyGetProduct = getApiData($productsurl);
	 						if (isset($shopifyGetProduct['errors']) && strtolower($shopifyGetProduct['errors']) == "not found")
	 						{
	 							$output = $this->createShopifyNewRunlistProduct($data, $runningList, $variantData);
	 						}
	 						else
	 						{
	 							if (isset($request->asin) && !empty($request->asin))
	 							{
	 								$output = $this->updateShopifyNewRunlistProduct($data, $runningList, $variantData);
	 							}
	 						}
	 					}
						return response()->json($output);
	 				}
	 				else
	 				{
	 					$output = "Nothing found.";
						$status = false;
	 					return response()->json(['message' => $output, 'status' => $status]);
	 				}
	 			}
	 		}
	 		else
	 		{
	 			return response()->json(['message' => 'Please select minimum one product to sync.', 'status' => false]);
	 		}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
  	}

  	public function updateShopifyProductNewRunListPrice($baseurl, $runningList, $variantData)
  	{
  		$message = [];
	    $productsurl = $baseurl . "/admin/api/2019-04/products/" . $runningList['shopify_product_id'] . ".json";
	    $shopifyData = getApiData($productsurl);
	    if ($shopifyData)
	    {
	        $variantData['variant']['id'] = $shopifyData['product']['variants'][0]['id'];
	        $variantData['variant']['product_id'] = $shopifyData['product']['id'];
	        $productsurl = $baseurl . "/admin/api/2019-04/variants/" . $shopifyData['product']['variants'][0]['id'] . ".json";
	        $shopifyVariantData = putApiData($productsurl, $variantData);
	        $returnMessage = 'Updated asin: ' . $runningList['model'] . ' Shopify product id:' . $shopifyData['product']['id'];
	        $status = true;
	    }
	    else
	    {
	        $returnMessage = 'Not updated for Model:' . $runningList['model'];
	        $status = false;
	    }
	    $message = ['message' => $returnMessage, 'status' => $status];
	    return $message;
	}

  	public function updateProductPriceToShopify(Request $request)
  	{
  		if($request->ajax())
  		{
  			if (isset($request->id) && !empty($request->id))
	 		{
	 			$allRunningList = ListData::getListDataForPriceUpdate($request->id);
			  	if (!empty($allRunningList))
			  	{
				    $baseurl = $this->basePath;
				    $meassge = '';
				    $runninglist = $allRunningList[0];
   				    $runninglist['condition'] =  config('constants.finalPriceConstants.condition');
				    $runninglist['form_factor'] = $runninglist['technology'];
				    $price = $this->productPriceCalculation($runninglist);
				    if ($price == 0 || $price == 0.00)
				    {
				        $price = 299.99;
				    }
				    $variantData['variant'] = [
				        "price" => $price,
				    ];

				    $meassge = updateShopifyProductNewRunListPrice($this->basePath, $runninglist, $variantData);
				    return response()->json($meassge);
				}
				else
				{
					return response()->json(['message' => 'No Data Found for this ASIN', 'status' => false]);
				}
			}
  		}
  		else
  		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
  		}
  	}

  	public function modelDataTemplate(Request $request)
  	{
		$tplid = intval($request->get("tplid"));
		$data = FormData::getFormDataRecordForTemplate($tplid);
		if (!$data)
		{
			$data = array("items" => array());
		}
		else 
		{
			$data = json_decode($data,true);
		}
		$output = "";
		$items = array();
		$tab = FormModel::getFormModelTab($tplid);
		$modelname = FormModel::getFormModelByID($tplid);
		$config = FormsConfig::getFormConfigDataByCommenQuery($query = ['tab' => $tab]);
		foreach ($config as $fld)
		{
			$itmid = $fld["qtype"] . "_" . $fld["id"];
			$itmidnew = $fld["qtype"] . "_" . $fld["id"]. "_new";
			$qtype = $fld["qtype"];
			if ($fld["question"] == "Model")
			{
				$modelitm = array(
					"template" => 1,
					"fillmodel" => 1,
					"id" => $itmid,
					"type" => "text",
					"key" => "Model",
					"options" => array(""),
					"new" => "",
					"value" => array($modelname)
				);
			}
			$formObjects = new AuditController();
			if (stripos($fld["config"],"fillmodel"))
			{
				if (method_exists($formObjects, "get_form_$qtype"))
				{
					$mtd = "get_form_$qtype";
					$output .= "<div class='formitem'>" . $formObjects->$mtd($fld) . "</div>";
				}
			}
		}	
		if(count($data["items"])>0)
		{
			foreach ($data["items"] as $itm)
			{
				if ($itm["fillmodel"]==1)
				{
					$items[] = $itm;
				}
			}
		}
		else
		{
			$items[] = $modelitm;
		}
  		return view('admin.shopify.template', compact('output', 'items'));
  		abort('404');
  	}
}