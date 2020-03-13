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
use App\ShopifyPricingCustom;
use App\ShopifyBarCode;
use App\ShopifyImages;
use App\ShopifyPricing;

class ShopifyController extends Controller
{
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

 	public function inventoryCSV(Request $request)
 	{
 		if(!empty($request->get('csv')))
 		{
 			return Excel::download(new InventoryExport, 'inventory.csv');
 		}
 	}

 	public function syncAllToShopify(Request $request)
 	{
 		if($request->ajax())
		{
			if (isset($request->ids) && !empty($request->ids))
	 		{
	 			foreach ($request->ids as $key => $id)
	 			{
	 				$allRunningList = ListData::getFormModelAndListData($id);
	 				print_r($allRunningList->toArray());
	 				die;
	 				if (!empty($allRunningList))
	 				{
	 					$logger = new Error_Logger();
	 					$log_file_path = __DIR__ . '/../error-logs/shopify-api.log';
	 					$log_path = 'error-logs/shopify-api.log';
	 					if (!file_exists($log_file_path))
	 					{
	 						if (!is_writable(__DIR__ . "/../error-logs/"))
	 						{
	 							echo 'Please create this file ' . $log_path;
	 							return;
	 						}
	 						touch($log_file_path);
	 					}
	 					$logger->lfile($log_file_path);
	 					$running_list = $allRunningList[0];
	 					$asin = createAsinFromData($running_list);
	 					$image_asin = createImageAsinFromData($running_list);
	 					$all_images = glob('../' . IMAGE_PATH_NEW . $image_asin . '*');
	 					$insert_data_array = getAdditionalDataForNewRunlist($running_list['id'], $db);
	 					$insert_data_array['processer_gen'] = getProcessorGenration($running_list['cpu_model']);
	 					$running_list['cpu_model'] = getProcessorModel($running_list['cpu_model']);
	 					$running_list['condition'] = CONDITION;
	 					$model_data = explode(' ', $running_list['model']);
	 					$insert_data_array['series'] = $model_data[0];
	 					$running_list['form_factor'] = $running_list['technology'];
	 					$running_list['asin'] = $asin;
	 					$running_list['manufacturer'] = getManufacturerForNewRunlistdata($db, $model_data[0]);
	 					$running_list['list_id'] = $running_list['list_id'];
	 					$price = productPriceCalculation($db, $running_list);
	 					$apple_data = getShopifyAppleDataFromTable($db, $running_list);
	 					if (!empty($apple_data))
	 					{
	 						$insert_data_array['product_class'] = 'Apple';
	 						$running_list['manufacturer'] = $apple_data['Manufacturer'];
	 					}
	 					switch ($insert_data_array['product_class'])
	 					{
	 						case 'Computer':
	 						$data_object = new Shopify_Computer($running_list, $insert_data_array, $insert_data_array['product_class']);
	 						break;
	 						case 'Laptop':
	 						$data_object = new Shopify_Laptop($running_list, $insert_data_array, $insert_data_array['product_class']);
	 						break;
	 						case 'All_In_One':
	 						$data_object = new Shopify_Laptop($running_list, $insert_data_array, $insert_data_array['product_class']);
	 						break;
	 						case 'Printer':
	 						case 'Apple':
	 						$data_object = new Shopify_Apple_Laptop($running_list, $insert_data_array, $apple_data, $insert_data_array['product_class']);
	 						break;
	 						default:
	 						$error = "Can't sync product. Reason: Class " . $insert_data_array['product_class'] . " not found for asin " . $asin . ". Valid classes are Computer & Laptop";
	 						$return_error[] = $error;
	 						$logger->lwrite($error);
	 						continue;
	 						break;
	 					}
	 					$data = $data_object->data;
	 					$bar_code = getBarCode($asin, $db);
	 					if ($price == 0 || $price == 0.00)
	 					{
	 						$price = 299.99;
	 					}
	 					$variant_data['variant'] = [
	 						"price" => $price,
	 						"sku" => strtolower($asin),
	 						"inventory_management" => "shopify",
	 						"barcode" => $bar_code,
	 						"weight" => $insert_data_array['weight'],
	 					];
	 					if (empty($running_list['shopify_product_id']) || $running_list['shopify_product_id'] == 0) {
	 						$return_error[] = createShopifyNewRunlistProduct($db, $data, $running_list, $variant_data);
	 					}
	 					else
	 					{
	 						$data['product']['id'] = $running_list['shopify_product_id'];
	 						$productsurl = SHOPIFY_BASE_URL . "/admin/api/2019-04/products/" . $running_list['shopify_product_id'] . ".json";
	 						$shopify_get_product = getApiData($productsurl);
	 						if (isset($shopify_get_product['errors']) && strtolower($shopify_get_product['errors']) == "not found")
	 						{
	 							$return_error[] = createShopifyNewRunlistProduct($db, $data, $running_list, $variant_data);
	 						}
	 						else
	 						{
	 							if (isset($_REQUEST['asin']) && !empty($_REQUEST['asin']))
	 							{
	 								$return_error[] = updateShopifyNewRunlistProduct($db, $data, $running_list, $variant_data);
	 							}
	 						}
	 					}
	 				}
	 				else
	 				{
	 				}
	 			}
	 			print_r($return_error);
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
 }
