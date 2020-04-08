<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use File;
use Config;
use App\Shipment;
use App\FormsConfig;
use App\ShipmentsData;
use App\Asin;
use App\SessionData;
use App\Supplies;

class ShipmentController extends Controller
{
	public $basePath, $refurbLabels, $current, $refurbAssetData, $formData, $sessionReports, $completedRefurbLabels;

	public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->current = Carbon::now();
    	$this->sessionReports = $this->basePath.'/session-reports';
    	$this->formData = $this->basePath.'/form-data';
    	$this->refurbAssetData = $this->basePath.'/refurb-asset-data';
    	$this->refurbLabels = $this->basePath.'/refurb-labels';
    	$this->completedRefurbLabels =  $this->basePath.'/completed-refurb-labels';
    	if(!File::exists($this->completedRefurbLabels))
		{
			File::makeDirectory($this->completedRefurbLabels, $mode = 0777, true, true);
		}
		if (!File::exists($this->refurbAssetData))
		{
			File::makeDirectory($this->refurbAssetData, $mode = 0777, true, true);
		}
    }

    public function getAsset($request)
    {
    	$asset = $request->get("asset");
		if(File::exists($this->formData."/".$asset.".json"))
		{
			$data = json_decode(file_get_contents($this->formData."/".$asset.".json"),true);
			$tab = $data["radio_2"];
			$config = FormsConfig::getConfigValueByTab($tab, $group ='Description');
			$data["conf"] = [];
			foreach ($config as $fld)
			{
				$item=array();
				$itmid = $fld["qtype"] . "_" . $fld["id"];
				$qtype = $fld["qtype"];
				$grp = str_replace(array(" ","-",":",".","/"),"_",$fld["grp"]);
				$key = str_replace(array(" ","-",":",".","/"),"_",$fld["question"]);
				$vals = explode(";",$fld["options"]);
				if (stripos($fld["config"],"filltemplate")>0) $item["template"]=1;
				else $item["template"]=0;
				if (stripos($fld["config"],"fillmodel")>0) $item["fillmodel"]=1;
				else $item["fillmodel"]=0;
				$item["id"] = $itmid;
				$item["type"] = $qtype;
				$item["key"] = $key;
				$item["options"] = $vals;
				$item["new"] = "";
				$data["conf"][]=$item;
			}	
			$data["Model"] = "N/A";
			$data["CPU"] = "N/A";
			$data["RAM"] = "N/A";
			$data["RAM_type"] = "N/A";
			$data["HDD"] = "N/A";
			$data["HDD_type"] = "N/A";
			$data["asin"] = "0000000000";
			$data["asin_id"] = "0";
			$data["asin_match"] = "none";
			$data["upd_ram"] = "";
			$data["upd_hdd"] = "";
			$data["upd_os"] = "";
			$data["asins"] = [];
			$data["print"] = "";
			$data["pdf"] = "";
			$data['new_coa'] = "";
			$data['old_coa'] = "";
			$data['win8'] = "0";
			if(File::exists($this->refurbAssetData.'/'.$asset.'.json'))
			{
				$adata = json_decode(file_get_contents($this->refurbAssetData.'/'.$asset.'.json'),true);
				if(!empty($adata['new_coa'])) $data['new_coa'] = $adata['new_coa'];
				if(!empty($adata['old_coa'])) $data['old_coa'] = $adata['old_coa'];
				if(!empty($adata['win8'])) $data['win8'] = $adata['win8'];

			}
			
			if(File::exists($this->refurbLabels.'/'.$asset.".dat"))
			{
				$data["print"] = file_get_contents($this->refurbLabels.'/'.$asset.".dat");
			}

			if(File::exists($this->completedRefurbLabels."/".$asset.".pdf"))
			{
				$data["pdf"] = '<a class="btn btn-primary" style="float: right;margin-right: 5px;margin-left: 5px;" href="completed-refurb-labels/'.$asset.'.pdf" target="_blank">View Completed Label</a>';
			}
			$xml = false;
			if (File::exists($this->basePath."/wipe-data2/".$asset.".xml")) $xml=simplexml_load_file($this->basePath."/wipe-data2/".$asset.".xml");
			if (!$xml && File::exists($this->basePath."/wipe-data2/bios-data/".$asset.".xml")) $xml=simplexml_load_file($this->basePath."/wipe-data2/bios-data/".$asset.".xml");
			
			if ($xml)
			{
				$xmldata=[];
				$i = 0;
				foreach ($xml->component as $c)
				{
				    $i++;
				    $key = strval($c["name"]);
				    if(!isset($xmldata[$key])) $xmldata[$key]=[];
				    if(!in_array(strval($c),$xmldata[$key])) $xmldata[$key][] = strval($c); 
				}
				$data["Model"] = $xmldata["Model"][0];
				$data["CPU"] = $xmldata["ProcessorModel_Speed"][0];
				$data["RAM"] = trim($xmldata["Combined_RAM"][0]);
				$data["RAM_type"] = implode(",",$xmldata["MemoryType_Speed"]);
				$data["HDD"] = trim($xmldata["Combined_HD"][0]);
				if(isset($xmldata["Serial"])) $data["Serial"] = trim($xmldata["Serial"][0]);
				else $data["Serial"] = '000000';
				$data["HDD_type"] = implode(",",$xmldata["HardDriveType_Interface"]);
				$dr = explode(":",$data["RAM"]);
				$data["RAM"] = trim($dr[0]);
				if ($data["HDD"] !== "No_HD")
				{
					$dh = explode("_",$data["HDD"]);
					$data["HDD"] = $dh[0];	
				}
			}
			else
			{
				$data["Model"] = $asset.".xml not found";
			}
			
			if(!empty($data["CPU"]))
			{
				$parts1 = explode("_",$data["CPU"]);
				$parts2 = explode("-",$parts1[0]);
				if(!empty($parts1[1]) && !empty($parts2[1]))
				{
					$fields = ["id","model","asin","ram","hdd","os","cpu_core","cpu_model","cpu_speed","price"];
					$asins = Asin::getSpecificFirstRecord($fields, $data["Model"], $parts2[0], $parts2[1], $parts1[1]);
					if($asins)
					{
						$asins = $asins->toArray();
						$data["asin"] = $asins[0]["asin"];
						$data["asin_id"] = $asins[0]["id"];
						$data["upd_ram"] = trim($asins[0]["ram"]);
						$data["upd_hdd"] = trim($asins[0]["hdd"]);
						$data["upd_os"] = $asins[0]["os"];
						$data["asins"] = $asins;
						if(count($asins)>1) $data["asin_match"] = "partial";
						else $data["asin_match"] = "full";
					}
					else
					{
						$fields = ["id","model","asin","ram","hdd","os","cpu_core","cpu_model","cpu_speed","price"];
						$asins2 = Asin::getSpecificSecoundRecord($fields, $data["Model"], $parts2[0]);
						if($asins2)
						{
							$asins2 = $asins2->toArray();
							$data["asin"] = $asins2[0]["asin"];
							$data["asin_id"] = $asins2[0]["id"];
							$data["upd_ram"] = trim($asins2[0]["ram"]);
							$data["upd_hdd"] = trim($asins2[0]["hdd"]);
							$data["upd_os"] = $asins2[0]["os"];
							$data["asins"] = $asins2;
							$data["asin_match"] = "partial";
						}
					}
					if (!File::exists($this->refurbAssetData."/".$asset.".json")) file_put_contents($this->refurbAssetData."/".$asset.".json",json_encode($data));
				}
				if(!$data["asin_id"])
				{
					$fields = ["id","model","asin","ram","hdd","os","cpu_core","cpu_model","cpu_speed","price"];
					$asins2 = Asin::getSpecificTemplateRecord($fields);
					if($asins2)
					{
						$asins2 = $asins2->toArray();
						$data["asin"] = $asins2[0]["asin"];
						$data["asin_id"] = $asins2[0]["id"];
						$data["upd_ram"] = trim($asins2[0]["ram"]);
						$data["upd_hdd"] = trim($asins2[0]["hdd"]);
						$data["upd_os"] = $asins2[0]["os"];
						$data["asins"] = $asins2;
						$data["asin_match"] = "partial";
					}
				}
			}
			else
			{
				$fields = ["id","model","asin","ram","hdd","os","cpu_core","cpu_model","cpu_speed","price"];
				$asins2 = Asin::getSpecificTemplateRecord($fields);
				if($asins2)
				{
					$asins2 = $asins2->toArray();
					$data["asin"] = $asins2[0]["asin"];
					$data["asin_id"] = $asins2[0]["id"];
					$data["upd_ram"] = trim($asins2[0]["ram"]);
					$data["upd_hdd"] = trim($asins2[0]["hdd"]);
					$data["upd_os"] = $asins2[0]["os"];
					$data["asins"] = $asins2; 
					$data["asin_match"] = "partial";
				}
			}
			if (File::exists($this->refurbAssetData."/".$asset.".json"))
			{
				$adata = json_decode(file_get_contents($this->refurbAssetData."/".$asset.".json"),true);
				if($adata['asin_match'] == 'saved')
				{
					$data['asin_id'] = $adata['asin_id'];
					$data['asin_match'] = 'saved';
				}
			}
			return json_encode($data);			
		}
		else
		{
			return "0";
		}
    }

    public function getAssetsResult(Request $request, $assets)
    {
    	$result = ['status' => false, 'message' => ''];
    	$assetNumber = '';
		if(!empty($assets))
		{
			$sess = Shipment::getOpenShipment($request);
			$aid = 0;
			if($asin = $request->get('asin'))
			{
				$aid = Asin::getAsinsIdByAsin($asin);
			}
			foreach($assets as $asset)
			{
				if(!$aid) $aid = SessionData::getAsinsAidByAssest($asset);
				$sn = '';
				$old_coa = '';
				$new_coa = '';
				$win8 = 0;
				$refurbAssetData = $this->refurbAssetData.'/'.$asset.'.json';
				if(File::exists($refurbAssetData))
				{
					$adata = json_decode(file_get_contents($refurbAssetData),true);
					$new_coa = $adata['new_coa'];
					$old_coa = $adata['old_coa'];
					$win8 = $adata['win8'];
					$sn = $adata['Serial'];
					if(!$aid && !empty($adata['asin_id'])) $aid = $adata['asin_id'];
				}

				if(!$aid)
				{
					$data = $this->getAsset($request);
					if($data!='0')
					{
						$res = json_decode($data,true);
						if(!$aid) $aid = $res['asin_id'];
						$sn = (isset($res['Serial'])) ? $res['Serial'] : '';
					}
				}

				if(!$aid)
				{
					$assetFile = $this->formData.'/'.$asset.'.json';
					if(File::exists($assetFile))
					{
						$data = file_get_contents($assetFile);
						$res = json_decode($data,true);
						if(!empty($res['asin'])) $aid = $res['asin'];
					}
				}
				if($aid && strlen($asset)>3)
				{
					// echo 'i ma here';
					$data = [
						"sid" => $sess,
						"aid" => $aid,
						"sn" => $sn,
						"old_coa" => $old_coa,
						"new_coa" => $new_coa,
						"win8_activated" => $win8,
						"asset" => $asset,
						"added_by" => Sentinel::getUser()->first_name
					];
					// print_r($data);
					ShipmentsData::deleteOldShipmentData($sess,$asset);
					ShipmentsData::addShipmentData((object) $data, $this->current);
					SessionData::updateSessiontStatus($asset, $status='removed');
					// ShipmentsData::updateShipmentData($asset, );
					$assetNumber .= ",#" .$asset;
					$result = ['status' => true, 'message' => 'ASIN Record added for asset'.$assetNumber];
				}
				else
				{
					if(strlen($asset)>3)
					{
						$assetNumber .= ",#" .$asset;
						$result = ['status' => false, 'message' => 'ASIN Record not found for asset'.$assetNumber];
					};
				}
			}
		}
		return $result; 
    }

    public function shipmentItems(Request $request, $shipmentName, $shipmentId)
	{
		$r = '';
		$status = '';
		if($request->has('remove'))
		{
			$r = $request->get('remove');
			$status = 'removed';
			SessionData::updateSessiontStatus($r, $s='active');
		}
		if($request->has('restore'))
		{
			$r = $request->get('restore');
			$status = 'active';
		}
		ShipmentsData::updateShipmentStatus($r, $status , $shipmentId);
		$asins = ShipmentsData::getResultAsinsAndShipmentData($status='active', $shipmentId);
		foreach($asins as &$a)
		{
			$a['items'] = ShipmentsData::getResultAsinsAndShipmentDataByID($a['aid'], $status='active', $shipmentId);
		}
		unset($a);
		return $asins;
	}

	public function index(Request $request)
	{
		$asins = [];
		$assets = [];
		if($asset = $request->get('asset'))
		{
			$assets[] = $asset;
		}
		if($asset = $request->get('asset1'))
		{
			$assets = explode(PHP_EOL,$asset);
		}
		$assets = $this->getAssetsResult($request, $assets);
		if($request->has('s'))
		{
			$shipmentId = $request->get('s');
			$shipmentName = Shipment::getNameOfRecordByID($shipmentId);
			$asins = $this->shipmentItems($request, $shipmentName, $shipmentId);
		}
		$shipments = Shipment::getAllRecord($request);
		foreach($shipments as &$s)
		{
			$s['count'] = ShipmentsData::getShipmentCountByID($s['id']);
		}
		unset($s);
		if(!$assets['status'])
		{
			$status = 'error';
            \Session::flash($status, $assets['message']);
		}
		else
		{
			$status = 'success';
            \Session::flash($status, $assets['message']);
		}
		return view('admin.shipment.list', compact('shipments', 'asins', 'shipmentName', 'assets'));
	}

	public function addShipment(Request $request)
	{
		$validatedData = $request->validate(
			[
            	'session_name' => 'required',
        	],
       		[
        	'session_name.required' => 'Please add shipment name',
        ]);

		if($request->get('new_session') && $request->get('session_name'))
		{
			$currentSession = Shipment::getOpenShipment($request);
			$updateShipment = Shipment::updateShipmentRecord($request, $this->current);
			$addShipment = Shipment::addShipmentRecord($request, $this->current);
			if($addShipment)
			{
				$status = 'active';
				$sessionSummary = ShipmentsData::sessionSummary($currentSession, $status);
				$sessionItems = ShipmentsData::sessionItems($currentSession, $status);
				$parts = ShipmentsData::shipmentParts($currentSession, $status);

				foreach($parts as $p)
				{
					$nqty = max(0,$p["qty"]-$p["required_qty"]);
					Supplies::updateQuantityBySupplieID($p["id"], $nqty);
				}

				if (!empty($sessionItems))
				{
					$this->createShipmentReport($request, $sessionItems, $currentSession, $sessionSummary);
				}
				return redirect()->route('shipments')->with([
					'success', 'Shipment added successfully.'
				]);
			}
			else
			{
				return redirect()->route('shipments')->with([
					'error', 'Somethging went wrong'
				]);
			}
		}
	}

	public function createShipmentReport($request, $sessionItems, $currentSession, $sessionSummary)
	{
		$shipmentName = Shipment::getNameOfRecordByID($currentSession);
		$data = [
			"tems" => $sessionSummary, 
			"summary" => $sessionSummary,
			"name" => $shipmentName
		];

		if (!File::exists($this->sessionReports))
		{
			File::makeDirectory($this->sessionReports, 0777, true, true);
		}

		$fp = fopen($this->sessionReports.'/coa'.$currentSession.'.csv', "w");
		fputcsv($fp, ["Shipment ID","Asset","S/N","Old COA","New COA","WIN8","Model","CPU","Added"]);
		foreach ($sessionSummary as $i) {
		    $itm = [
		    	$i["id"],
		    	$i["asset"],
		    	$i["sn"],
		    	$i["old_coa"],
		    	$i["new_coa"],
		    	($i["win8_activated"]?'WIN8 Activated':''),
		    	$i["model"],
		    	$i['cpu_core'].' '.$i['cpu_model'].' CPU @' .$i['cpu_speed'],
		    	$i["added_on"]
		    ];
		    fputcsv($fp, $itm);
		}
		fclose($fp);
		$fp = fopen($this->sessionReports.'/shipment'.$currentSession.'.csv', "w");
		fputcsv($fp, ["ASIN","Asset","Model","Form Factor","S/N","CPU","Price","Added"]);
		foreach ($sessionSummary as $i)
		{
		    $fields = [
		    	$i["asin"],
				$i["asset"],
				$i["model"],
				$i["form_factor"],
				$i["sn"],
				$i["cpu_core"].' '.$i["cpu_model"].' CPU @ '.$i["cpu_speed"],
				number_format($i["price"],2),
				$i["added_on"]
		    ];
		    fputcsv($fp, $fields);
		    SessionData::updateSessionRunStatus($i["asset"], $status='shipped');
		}
		fclose($fp);
		$shipmentEmails = Config::get('constants.shipmentEmails');
		$subject = 'Shipment details';
		$name = $currentSession.'.csv';
		$files[] = $this->sessionReports.'/shipment'.$name;
		$files[] = $this->sessionReports.'/coa'.$name;
		Mail::send('admin.emails.shipmail', $data, function ($m) use ($subject, $shipmentEmails, $files, $name) {
            $m->to($shipmentEmails)->subject($subject);
            foreach($files as $file) {
                $m->attach($file, array(
                    'as' => $name,
                    'mime' => 'csv')
                );
            }
        });
	}
}
