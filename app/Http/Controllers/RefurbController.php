<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CoaReportExport;
use App\Exports\IssuesReportExport;
use Carbon\Carbon;
use Config;
use File;
use DB;
use Excel;
use App\Refurb;
use App\FormsConfig;
use App\Asin;
use App\SessionData;
use App\CoaReport;
use App\ShipmentsData;
use App\AsinIssue;

class RefurbController extends Controller
{
	public $basePath, $process, $formData, $refurbAssetData, $refurbLabels, $wipeDataTwo;
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->process = Config::get('constants.process');
    	$this->basePath = base_path().'/public';
    	$this->formData = $this->basePath.'/form-data';
    	$this->refurbAssetData = $this->basePath.'/refurb-asset-data';
    	$this->refurbLabels = $this->basePath.'/refurb-labels';
    	$this->wipeDataTwo = $this->basePath.'/wipe-data2';
    }

    public function index(Request $request)
    {
    	return view ('admin.refurb.index')->with(['process' => $this->process]);
    }

    public function getAsset(Request $request)
    {
    	if($request->ajax())
    	{
			$asset = $request->get("asset");
			$assetFile = $this->formData.'/'.$asset.'.json';
			$refurbAssetData = $this->refurbAssetData.'/'.$asset.'.json';
			$refurbLabels = $this->refurbLabels.'/'.$asset.'.dat';
			$wipeDataTwo = $this->wipeDataTwo.'/'.$asset.'.xml';
			if (File::exists($assetFile))
			{
				$data = json_decode(file_get_contents($assetFile), true);
				$tab = $data["radio_2"];
				$config = FormsConfig::getConfigValueByTab($tab, $group='Description');
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
				
				if(File::exists($refurbAssetData))
				{
					$adata = json_decode(file_get_contents($refurbAssetData),true);
					if(!empty($adata['new_coa'])) $data['new_coa'] = $adata['new_coa'];
					if(!empty($adata['old_coa'])) $data['old_coa'] = $adata['old_coa'];
					if(!empty($adata['win8'])) $data['win8'] = $adata['win8'];
				}				
				if(File::exists($refurbLabels))
				{
					$data["print"] = file_get_contents($refurbLabels);
				}
				if (File::exists($this->basePath.'/completed-refurb-labels/'.$asset.'.pdf'))
				{
					$data["pdf"] = '<a class="btn btn-primary" style="float: right;margin-right: 5px;margin-left: 5px;" href="'.url('/completed-refurb-labels/').'/'.$asset.'.pdf" target="_blank">View Completed Label</a>';
				}
				$xml = false;
				if (File::exists($wipeDataTwo)) $xml= simplexml_load_file($wipeDataTwo);
				if (!$xml && File::exists($this->basePath.'/wipe-data2/bios-data/'.$asset.".xml"))
				{
					$xml = simplexml_load_file($this->basePath.'/wipe-data2/bios-data/'.$asset.".xml");
				}
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
					$data["Model_Not_f"] = true;
					// $data["Serial"] = '000000';
				}
				$fields = ["id","model","asin","ram","hdd","os","cpu_core","cpu_model","cpu_speed","price"];
				if(!empty($data["CPU"]))
				{
					$parts1 = explode("_",$data["CPU"]);
					$parts2 = explode("-",$parts1[0]);
					if(!empty($parts1[1]) && !empty($parts2[1]))
					{
						$asins = Asin::getSpecificFirstRecord($fields, $data["Model"], $parts2[0], $parts2[1], $parts1[1]);
						$asins = $asins->toArray();
						if($asins)
						{
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
							$asins2 = Asin::getSpecificSecoundRecord($fields, $data["Model"], $parts2[0]);
							$asins2 = $asins2->toArray();
							if($asins2)
							{
								$data["asin"] = $asins2[0]["asin"];
								$data["asin_id"] = $asins2[0]["id"];
								$data["upd_ram"] = trim($asins2[0]["ram"]);
								$data["upd_hdd"] = trim($asins2[0]["hdd"]);
								$data["upd_os"] = $asins2[0]["os"];
								$data["asins"] = $asins2;
								$data["asin_match"] = "partial";
							}
						}
						if (!File::exists($this->refurbAssetData))
						{
							File::makeDirectory($this->refurbAssetData, 0777, true, true);
						}
						if (!File::exists($refurbAssetData))
						{
							file_put_contents($refurbAssetData,json_encode($data));
						}
					}
					if(!$data["asin_id"])
					{
						$asins2 = Asin::getSpecificThirdRecord($fields, $query="Template");
						$asins2 = $asins2->toArray();
						if($asins2)
						{
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
					$asins2 = Asin::getSpecificThirdRecord($fields, $query="Template");
					$asins2 = $asins2->toArray();
					if($asins2)
					{
						$data["asin"] = $asins2[0]["asin"];
						$data["asin_id"] = $asins2[0]["id"];
						$data["upd_ram"] = trim($asins2[0]["ram"]);
						$data["upd_hdd"] = trim($asins2[0]["hdd"]);
						$data["upd_os"] = $asins2[0]["os"];
						$data["asins"] = $asins2; 
						$data["asin_match"] = "partial";
					}
				}
				if (File::exists($refurbAssetData))
				{
					$adata = json_decode(file_get_contents($refurbAssetData),true);
					if($adata['asin_match'] == 'saved')
					{
						$data['asin_id'] = $adata['asin_id'];
						$data['asin_match'] = 'saved';
					}
				}
				return response()->json(['result' => $data, 'status' => true]);
			} 
			else
			{
				return response()->json(['result' => [], 'status' => false]);
			}
		}
    	else
    	{
    		return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
    	}
	}

	public function saveAsin(Request $request)
	{
		if($request->ajax())
		{
			$asset = $request->get("asset");
			$aid = $request->get("aid");
			$refurbAssetData = $this->refurbAssetData.'/'.$asset.'.json';
			if($asset && $aid)
			{
				SessionData::updateRecord(["aid" => $aid, "asset" => $asset]);
				
			}
			if(File::exists($refurbAssetData))
			{
				$adata = json_decode(file_get_contents($refurbAssetData),true);
				$adata['asin_id'] = $aid;
				$adata['asin_match'] = 'saved';
				file_put_contents($refurbAssetData,json_encode($adata));
			}
			return response()->json(['message' => 'Added successfully', 'type' => 'success', 'status' => true]);
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
		}
	}

	public function saveCOA(Request $request)
	{
		if($request->ajax())
		{
			$current = Carbon::now();
			$asset = $request->get("asset");
			$refurbAssetData = $this->refurbAssetData.'/'.$asset.'.json';
			if(File::exists($refurbAssetData))
			{
				$adata = json_decode(file_get_contents($refurbAssetData),true);
				$adata['new_coa'] = $request->get("new_coa");
				$adata['old_coa'] = $request->get("old_coa");
				$adata['win8'] = $request->get("win8");
				$sn = $adata['Serial'];
				if(!empty($request->get("asin"))) $adata['asin_id'] = $request->get("asin");
				file_put_contents($refurbAssetData, json_encode($adata));
			}
			else
			{
				$sn="";
			}
			ShipmentsData::updateRecord($request);
			if($request->get("new_coa") || $request->get("win8"))
			{
				if($request->get("win8"))
				{
					$request->merge(['old_coa' => 'WIN8 Activated']);
				} 

				$result = CoaReport::getRecordByID($asset);
	            if(!$result)
	            {
	            	CoaReport::addRecord($asset, $sn, $request, $current);
				}
				else
				{
					CoaReport::updateRecord($asset, $sn, $request);
				}
			}
			return response()->json(['message' => 'Added successfully', 'type' => 'success', 'status' => true]);
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
		}
	}

	public function saveIssue(Request $request)
	{
		if($request->ajax())
		{
			$current = Carbon::now();
			$asinIsuesID = AsinIssue::storeRecord($request, $current);
			if($asinIsuesID)
			{
				return response()->json(['message' => 'Added successfully', 'type' => 'success', 'status' => true]);
			}
			else
			{
				return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
			}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
		}
	}

	public function setWholesale(Request $request)
	{
		if($request->ajax())
		{
			$asset = $request->get("a");
			$alist = explode(PHP_EOL,$asset);
			$alist = array_unique($alist);
			$upd = SessionData::updateRecordRunStatus($alist, $text= 'wholesale');
			if($upd['output'])
			{
				return response()->json(['message' => count($upd).' assets updated', 'type' => 'success', 'status' => true]);
			}
			else
			{
				return response()->json(['message' => 'Nothing to update', 'type' => 'error', 'status' => false]);
			}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
		}
	}

	public function savePrint(Request $request)
	{
		if($request->ajax())
		{	
			$print = $request->get("print");
			$asset = $request->get("asset");
			if (!File::exists($this->refurbLabels))
			{
				File::makeDirectory($this->refurbLabels, 0777, true, true);
			}
			if($print && $asset)
			{
				$refurbLabels = $this->refurbLabels.'/'.$asset.'.dat';
				file_put_contents($refurbLabels,$print);
			}
			
			return response()->json(['message' => 'saved successfully', 'type' => 'success', 'status' => true]);
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
		}
	}

	public function checkCOA(Request $request)
	{
		if($request->ajax())
		{
			$asset = $request->get("asset");
			$refurbAssetData = $this->refurbAssetData.'/'.$asset.'.json';
			if(File::exists($refurbAssetData))
			{
				$adata = json_decode(file_get_contents($refurbAssetData),true);
				$adata['new_coa'] = $request->get("new_coa");
				$adata['old_coa'] = $request->get("old_coa");
				$adata['win8'] = $request->get("win8");
				$sn = $adata['Serial'];
				if(!empty($request->get("asin"))) $adata['asin_id'] = $request->get("asin");
				file_put_contents($refurbAssetData,json_encode($adata));
			}
			else
			{
				$sn="";
			}
			if($request->win8) $request->old_coa = 'WIN8 Activated';
            $id = CoaReport::getIdOfCoaReport($asset);
            if($id == "")
            {
            	return response()->json(['message' => 'OKi', 'type' => 'success', 'status' => true]);
			}
			else
			{
            	return response()->json(['message' => 'OK', 'type' => 'success', 'status' => true]);
			}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'type' => 'error', 'status' => false]);
		}
	}
 
	public function ExportcoaReport()
	{    
 		return (new CoaReportExport)->download('coa.csv', \Maatwebsite\Excel\Excel::CSV);  
	}

	public function ExportIssueReport()
	{
		return (new IssuesReportExport)->download('issues.csv', \Maatwebsite\Excel\Excel::CSV); 
	}
}