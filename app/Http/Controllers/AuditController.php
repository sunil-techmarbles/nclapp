<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\FormsConfig;
use App\Audit;
use Config;
use File;
use App\LenovoModelData;
use App\FormModel;
use App\Asin;
use App\FormData;
use App\Session;
use App\SessionData;
use App\UserCronJob;
use App\ListData;

class AuditController extends Controller
{

	public $basePath, $formData, $e_mails, $sandboxMode, $wipeDataAdditional, $wipeDataMobile, $adminEmails;
	/**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->e_mails = [];
        $this->adminEmails = UserCronJob::getCronJobUserEmails('newItemAdditionRequest');
        if($this->adminEmails->count() > 0)
        {
            foreach ($this->adminEmails as $key => $value) {
                $this->e_mails[] = $value->email;
            }
        }
        $this->adminEmails = ($this->adminEmails->count() > 0) ? $this->e_mails : Config::get('constants.adminEmail');
		$this->sandboxMode = false;
		$this->basePath = base_path().'/public';
		$this->current = Carbon::now();
		$this->wipeDataAdditional = $this->basePath.'/wipe-data-additional';
		$this->wipeDataMobile = $this->basePath.'/wipe-data-mobile';
		$this->formData = $this->basePath.'/form-data';
		if(!File::exists($this->formData))
		{
			File::makeDirectory($this->formData, $mode = 0777, true, true);
		}
		if (!File::exists($this->wipeDataAdditional))
		{
			File::makeDirectory($this->wipeDataAdditional, $mode = 0777, true, true);
		}
		if (!File::exists($this->wipeDataMobile))
		{
			File::makeDirectory($this->wipeDataMobile, $mode = 0777, true, true);
		}
	}

	public function AddPartNumber(Request $request) 
	{
		$checkPartNumber = LenovoModelData::CheckIfPartNumberExists( trim($request->partnumber) );
		if( $checkPartNumber )
		{
			$response['status']  = 'error';
			$response['title']  = 'Already Exist';
			$response['message'] = 'Part Number already exists'; 
			return response()->json($response);
		} 
		$newpartnumber = LenovoModelData::InsertNewPartNumber( $request->modal, $request->partnumber ); 
		if(!empty( $newpartnumber ) && $newpartnumber != false)
		{
			$response['status']  = 'success';
			$response['title']  = 'Added';
			$response['message'] = 'Part Number has been added successfully'; 
		}
		else
		{
			$response['status']  = 'error';
			$response['title']  = 'Unable to add';
			$response['message'] = 'Something went wrong, Unable to add try again'; 
		} 
		return response()->json($response);
	}

	public function renderHtml($formsDatas)
	{
		$output = "";
		$cgrp = "X"; 
		foreach ( $formsDatas as $formdata ) 
		{
			$qtype = $formdata->qtype;
			$grp = $formdata->grp;
			if ($grp != $cgrp) 
			{
				if ($cgrp != "X" && $cgrp != "") $output .= "</div>";
				if ($grp != "") $output .= "<div class='question-group'><h3>$grp</h3>";
				$cgrp = $grp;
			}
			
			if( method_exists($this , "get_form_".$qtype) ) 
			{
				$function = "get_form_".$qtype;
				$output .= "<div class='formitem'>" . $this->$function($formdata) . "</div>";
			}
		}
		if ($grp != "") $output .= "</div>";
		return $output;
	}

	public function index(Request $request)
	{ 
		$formsDatas = FormsConfig::getTab($tab = 'Notes', $isActive = 'Yes');
		$output = $this->renderHtml($formsDatas);
		$damageScores = Config::get('constants.auditDamageScores');
		$refurbBlacklist = Config::get('constants.auditRefurbBlacklist');
		return view('admin.audit.index' ,  compact('output', 'damageScores', 'refurbBlacklist') );    
	}

	public function get_form_text($fld) 
	{
		$output = "";
		if ($fld["qtype"]=="text") 
		{
			$itemid = $fld["qtype"] . "_" . $fld["id"];
			$output = "
			<div class='form-group'>
			<label class='ttl' for='".$itemid."'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>
			<input type='text' value='".$fld["default_val"]."' class='form-control' id='".$itemid."' name='".$itemid."' ".$fld["config"].($fld["required"]?" required='true'":"") ."/>
			</div>";
		}
		return $output;
	}

	public function get_form_number($fld)
	{
		$output = "";
		if ($fld["qtype"]=="number")
		{
			$itemid = $fld["qtype"] . "_" . $fld["id"];
			$output = "
			<div class='form-group'>
			<label class='ttl' for='".$itemid."'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>
			<input type='number' value='".$fld["default_val"]."' class='form-control' id='".$itemid."' name='".$itemid."' ".$fld["config"].($fld["required"]?" required='true'":"") ."/>
			</div>";
		}
		return $output;
	}
	
	public function get_form_area($fld)
	{
		$output = "";
		if ($fld["qtype"]=="area")
		{
			$itemid = $fld["qtype"] . "_" . $fld["id"];
			$output = "
			<div class='form-group' style='width:100%'>
			<label class='ttl' for='".$itemid."'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label>
			<textarea class='form-control' rows='5' style='width:100%' id='".$itemid."' name='".$itemid."' ".$fld["config"].($fld["required"]?" required='true'":"") .">".$fld["default_val"]."</textarea>
			</div>";
		}
		return $output;
	}
	
	public function get_form_bool($fld) 
	{
		$output = "";
		if ($fld["qtype"]=="bool") 
		{
			$itemid = $fld["qtype"] . "_" . $fld["id"];
			$output = "
			<div class='btn-group btn-group-horizontal' data-toggle='buttons'>
			<label class='btn' for='".$itemid."'><input type='checkbox' value='1' id='".$itemid."' name='".$itemid."' ".$fld["config"]."/>".
			"<i class='fa fa-square-o fa-2x'></i><i class='fa fa-check-square-o fa-2x'></i> <span>" . $fld["question"] . "</span></label>
			</div>";
		}
		return $output;
	}
	
	public function get_form_mult($fld)
	{
		$output = "";
		if ($fld["qtype"]=="mult")
		{
			$options = explode(";",$fld["options"]);
			$grades = explode(";",$fld["grades"]);

			if($fld["sort"]!="no") natsort($options);
			$output = "<label class='ttl'>".$fld["question"].($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>";
			$itemname = $fld["qtype"] . "_" . $fld["id"] . "[]";
			foreach($options as $oid=>$oname)
			{
				foreach($grades as $key => $grade)
				{
					if($oid == $key)
					{

						$itemid = $fld["qtype"] . "_" . $fld["id"] . "_" . $oid;
						$output .= "<div class='cb-cnt'><label class='btn' for='".$itemid."'><input class='calculate_grade' data-grade='".$grade."' type='checkbox' value='".htmlentities($oname, ENT_QUOTES).
						"' id='".$itemid."' name='".$itemname."' ".$fld["config"].($fld["required"]?" required='true'":"") ."/>
						<span>" . $oname . "</span></label></div>";
					}
				}
			}
			if ($fld["allow_new"]) 
			{
				$confdata = explode(" ",$fld["config"]);
				$olbl="Other:";
				foreach($confdata as $cd) 
				{
					$itm = explode("=",$cd);
					if(count($itm)==2 && trim($itm[0])=="data-customlabel") $olbl = trim(str_replace('"','',$itm[1]));
				}
				$itemid = $fld["qtype"] . "_" . $fld["id"] . "_new";
				$output .= "<div class='form-group'><label for='".$itemid."'>$olbl</label> <input type='text' class='form-control' id='".$itemid."' name='".$itemid."'/></div>";
			}
		}
		return $output;
	} 
	
	public function get_form_radio($fld)
	{
		$output = "";
		if ($fld["qtype"]=="radio")
		{
			$output .= "<div class='form-group'>";
			$options = explode(";",$fld["options"]);
			if($fld["sort"]!="no") natsort($options);
			if (count($options)==1) $fld["config"] .= " checked='checked'";
			$output .= "<label class='ttl'>".$fld["question"].($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>";
			$itemname = $fld["qtype"] . "_" . $fld["id"];
			foreach($options as $oid=>$oname)
			{
				$itemid = $fld["qtype"] . "_" . $fld["id"] . "_" . $oid;
				$output .= "
				<label class='btn' for='$itemid'><input type='radio' id='".$itemid."'  value='".htmlentities($oname, ENT_QUOTES)."' name='".$itemname."' "
				.$fld["config"].($fld["required"]?" required='true'":"").($fld["default_val"]==$oname?" checked='checked'":"")."/>
				<span>" . $oname . "</span></label>";
			}
			if ($fld["allow_new"]) 
			{
				$confdata = explode(" ",$fld["config"]);
				$olbl="Other:";
				$addopts="";
				foreach($confdata as $cd) 
				{
					$itm = explode("=",$cd);
					if(count($itm)==2 && trim($itm[0])=="data-customlabel") $olbl = trim(str_replace('"','',$itm[1]));
					if(count($itm)==2 && trim($itm[0])=="data-capitalize") $addopts.=' style="text-transform:uppercase"';
				}
				$itemid = $fld["qtype"] . "_" . $fld["id"] . "_newitm";
				$dataid = $fld["qtype"] . "_" . $fld["id"] . "_new";
				$output .= "<div class='form-group'>
				<label for='".$itemid."'><input type='radio' id='".$itemid."'  value='Other:' name='".$itemname."' ".$fld["config"] . ($fld["required"]?" required='true'":"") ."/>
				<span>".$olbl."</span> <input type='text' class='form-control' id='".$dataid."' name='".$dataid."' ".$addopts." onClick='$(\"#".$itemid."\").prop( \"checked\", true )'/>
				</label></div>";
			}
			$output .= "</div>";
			
		}
		return $output;
	}

	public static function getOptions($arr,$cv = "")
	{
		$res = "";
		$cval= strval($cv);
		if(is_array($arr))
		{
			foreach($arr as $val)
			{
				if ($val=="Please select") $key="";
				else $key = htmlentities($val, ENT_QUOTES);
				$res .= "<option value='".$key."'";
				if($cval == strval($val)) $res .= " selected";
				$res .= ">".$val."</option>\n";
			}
		}
		return $res;
	}
	
	public function get_form_dropdown($fld)
	{
		$output = "";
		if ($fld["qtype"]=="dropdown")
		{
			$options = explode(";",$fld["options"]);
			if($fld["sort"]!="no") natsort($options);
			array_unshift($options , 'Please select');
			if ($fld["allow_new"]) array_push($options,"Other:");
			$itemid = $fld["qtype"] . "_" . $fld["id"];
			$output = "
			<div class='form-group'>
			<label class='ttl' for='".$itemid."'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>
			<select class='form-control' id='".$itemid."' name='".$itemid."' ".$fld["config"].($fld["required"]?" required='true'":"") .">" .
			self::getOptions($options,$fld["default_val"]).  
			"</select></div>";
			if ($fld["allow_new"]) 
			{
				$selid = $itemid;
				$itemid = $fld["qtype"] . "_" . $fld["id"] . "_new";
				$output .= " <div class='form-group' style='vertical-align:bottom;'><label for='".$itemid."'>Other:</label> <input type='text' class='form-control' 
				id='".$itemid."' name='".$itemid."' onClick='$(\"#".$selid."\").val($(\"#".$selid." option:last-child\").val())' /></div>";
			}
		}
		return $output;
	}

	public function checkFile($path,$fname)
	{
		$files = glob($path);
		foreach($files as $f)
		{
			if (stripos($f,$fname)!==false) return true;
		}
		return false;
	}

	public static function getBetween($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		if ($len<=0) return '';
		return substr($string, $ini, $len);
	}

	public function checkTravelerId(Request $request)
	{
		if($request->ajax())
		{
			$travelerId = $request->get("trid");
			$fname =  $this->formData.'/'.$travelerId .'.json';
			$fname2 = $this->checkFile($this->basePath.'/wipe-data/*',$travelerId);
			$fname3 = $this->checkFile($this->basePath.'/wipe-data/bios-data/*',$travelerId);
			$fname4 = $this->checkFile($this->basePath.'/makor-processed-data/wipe-data/*',$travelerId);
			$fname5 = $this->checkFile($this->basePath.'/makor-processed-data/bios-data/*',$travelerId);
			if( File::exists( $fname ))
			{
				$resp = "Duplicate";
			}
			elseif ($fname2 || $fname3 || $this->sandboxMode)
			{
				$resp = "OK";
			}
			else
			{
				$resp = "Missing";
			}

			if($fname2 || $fname4)
			{
				if ($fname2) $files = glob($this->basePath.'/wipe-data/*.xml');
				if ($fname4) $files = glob($this->basePath.'/makor-processed-data/wipe-data/*.xml');
				foreach($files as $key => $f)
				{
					if ( stripos($f,$travelerId ) !== false )
					{	
						try
						{	$ram = '';
							$ramstr="";
							$hddstr="";
							$xml = simplexml_load_file($f);
							if(isset($xml->Report->Hardware->RAM))
							{
								$ram = (string) $xml->Report->Hardware->RAM->TotalCapacity;
								$ramdata = [];
								$ramsticks = $xml->Report->Hardware->RAM->Stick;
								foreach($ramsticks as $s)
								{
									if (!empty($s->Capacity))
									{
										if(isset($ramdata["CAP".$s->Capacity])) $ramdata["CAP".$s->Capacity]+=1;
										else $ramdata["CAP".$s->Capacity]=1;
									}
								}
								foreach($ramdata as $c=>$r)
								{
									if (!empty($ramstr)) $ramstr.=";";
									$ramstr.= str_replace("CAP","",$c)."_x_".$r;
								}
							}
							if(isset($xml->Report->Hardware->Devices))
							{
								$hdd = (array) $xml->Report->Hardware->Devices->Device;
								$hddata=[];
								foreach( $hdd as $h )
								{
									if (!empty($h->Gigabytes))
									{
										if($h->Gigabytes>1000) $label = "CAP".round($h->Gigabytes/1000,1)."TB";
										else $label = "CAP".round($h->Gigabytes,0)."GB";
										if(isset($hddata[$label])) $hddata[$label]+=1;
										else $hddata[$label]=1;
									}
								}

								foreach($hddata as $c=>$r)
								{
									if (!empty($hddstr)) $hddstr.=";";
									$hddstr.= str_replace("CAP","",$c)."_x_".$r;
								}
							}
							$resp = "Data|".$ram.":_".$ramstr."|".$hddstr."|CPU:";
							if(isset($xml->Report->Hardware->ComputerModel))
							{
								$model = strtolower($xml->Report->Hardware->ComputerModel);
							}

							if(isset($xml->Report->Hardware->Processors))
							{
								$cpus = $xml->Report->Hardware->Processors->Processor;
								foreach($cpus as $cpu)
								{
									$cpuname = $cpu->Name;
									$resp .= strtolower($cpuname);
									$speed = trim(self::getBetween($cpuname,"@","GHz"));
									$resp .= "|".$speed;
								}
							}
						}
						catch (Exception $e)
						{
							return $resp;
						}
					}
				}
			}
			else
			{
				if($fname3 || $fname5)
				{
					if($fname3) $files = glob($this->basePath.'/wipe-data/bios-data/*.xml');
					if($fname5) $files = glob($this->basePath.'/makor-processed-data/bios-data/*.xml');
					foreach($files as $f)
					{
						if (stripos($f,$travelerId)!==false)
						{
							try {
								$xml=simplexml_load_file($f);
								$resp = "Data|||CPU:";
								$model = "";
								$cpu = "";
								if(isset($xml->node->node))
								{
									foreach($xml->node->node->node as $s)
									{
										if($s["id"]=="cpu:0" || $s["id"]=="cpu")
										{
											$cpu = $s->product;
											$resp .= strtolower($cpu);
											$speed = trim(self::getBetween($cpu,"@","GHz"));
											$resp .= "|".$speed;
										}
									}
								}
							}
							catch (Exception $e)
							{
								return $resp;
							}
						}
					}
				}
			}
			return $resp;
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function getTab(Request $request)
	{
		$output = "";
		$tab = $request->get("tab");
		if(!empty($tab))
		{
			$formsDatas = FormsConfig::getTab($tab, $isActive = 'Yes');
			$output = $this->renderHtml($formsDatas);
		}
		return $output;
	}

	public function CheckTravelerIdForMobile(Request $request)
	{
		$response = '';
		$travelerId = $request->get( "trid" );
		$checkAssetidInXmldatafolder = $this->checkFile($this->basePath.'/wipe-data-mobile/*', $travelerId );
		$checkAssetidInAdditionalXmldatafolder = $this->checkFile($this->basePath.'/blancco/xml-data/*', $travelerId );
		$checkAssetidInExecutedXmldatafolder = $this->checkFile($this->basePath.'/makor-processed-data/additional-mobile-executed/*', $travelerId );
		$checkAssetidInExecutedAdditionlXmldatafolder = $this->checkFile($this->basePath.'/makor-processed-data/blancco-mobile-executed/*', $travelerId );
		if( $checkAssetidInXmldatafolder )
		{
			$response = 'OK';
		}
		else if ( $checkAssetidInAdditionalXmldatafolder )
		{
			$response = 'OK';
		}
		else if ( $checkAssetidInExecutedXmldatafolder )
		{
			$response = 'OK';
		}
		else if ( $checkAssetidInExecutedAdditionlXmldatafolder )
		{
			$response = 'OK';
		}
		else
		{
			$response = 'Missing';
		}
		return $response ;
	}

	public function getModels(Request $request)
	{
		if($request->ajax())
		{
			$res = "";
			$fields = ["id","model","technology"];
			if($request->get("tab") && $request->get("tech"))
			{
				$models = FormModel::getFormModelRecord($fields, $request, $isType = 'true');
			}
			else
			{	
				$models = FormModel::getFormModelRecord($fields, $request, $isType = 'false');
			}

			foreach($models as $f)
			{
				$res.="<p style=\"cursor:pointer\" id=\"model".$f['id']."\" onclick=\"getModelData(".$f['id'].",'".$request->get("tgt")."')\">" . $f['model'] . " (". $f['technology'] . ")</p>";
			}
			return $res;
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function loadModel(Request $request)
	{
		if($request->ajax())
		{
			$mid = $request->get("m");
			$response = FormData::getFormDataRecord($type='model', $mid);
			if (!$response)
			{
				$response = "false";
			} 
			else
			{
				$asin = FormModel::getAsinModelRecord($mid);
				$data = json_decode($response,true);
				$data["asin"] = $asin;
				if($asin!='0')
				{
					$asin = explode(",",$asin);
					$data["models"] = Asin::getModelFromAsin($asin, $notifications=1);
					if(!$data["models"]) $data['asin'] = 0;
				}
				$response = json_encode($data);
			}
			return $response;
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function savePartNumber(Request $request)
	{
		if($request->ajax())
		{
			$model = strtoupper($request->get('m'));
			$pn    = strtoupper($request->get('p'));
			$lenovoModelEx = LenovoModelData::CheckIfPartNumberExists($pn);
			if(!$lenovoModelEx)
			{
				LenovoModelData::InsertNewPartNumber($model, $pn);
				return "Part Number has been added successfully";
			}
			else
			{
				return "Part Number already exists";
			}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function getFiles(Request $request)
	{
		if($request->ajax())
		{
			$res = "";
			$part = $request->get("part");
			$dst = $request->get("tgt");
			$files = scandir($this->formData);
			foreach($files as $f)
			{
				$f = str_replace(".json","",$f);
				if (stristr($f,$part)) $res.="<a href='javascript:void(0)' onclick=\"addTrId('".$f."','".$dst."')\">" . $f . "</a><br/>";
			}
			return $res;
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function loadXML(Request $request)
	{
		if($request->ajax())
		{
			$fname = $request->get("trid");
			if (File::exists($this->formData."/".$fname.".json"))
			{
				$itm = json_decode(file_get_contents($this->formData."/".$fname.".json"),true);
				if(!empty($itm["model"]))
				{
					$asins = [];
					$asin = FormModel::getAsinModelRecord(intval($itm["model"]));
					if($asin)
					{
						$asin = array_push($asins, $asin);
					}
					$itm["models"] = Asin::getModelFromAsin($asins, $notifications='');
				}
				else
				{
					$itm["models"] = [];
				}
				$res = json_encode($itm);
				print_r($res);
				die;
				return $res;
			}
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function loadLast(Request $request)
	{
		if($request->ajax())
		{
			$authUserName = Sentinel::getUser()->first_name;
			$data = FormData::getLastRecordByAuthUser($authUserName, $type='data');
			if (count($data)==1)
			{
				$itm = json_decode($data[0]["data"],true);
				if(!empty($itm["model"]))
				{
					$asin = FormModel::getAsinModelRecord($itm["model"]);
					if(strpos($asin,",") !== false)
					{
						$asin = explode(",",$asin);
					}
					else
					{
						$asin = [$asin];
					}
					$itm["models"] = Asin::getModelFromAsin($asin, $notifications='');
				}
				else
				{
					$itm["models"] = [];
				}
				$res = json_encode($itm);
			}
			else $res = "false";
			return $res;
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function getAssetModels(Request $request)
	{
		$asset = $request->get("a");
		$asins = [];
		if (File::exists($this->formData.'/'.$asset.'.json'))
		{
			$data = [];
			$data["Model"] = "N/A";
			$data["CPU"] = "N/A";
			$xml = false;
			if (File::exists($this->basePath.'/wipe-data2/'.$asset.'.xml'))
			{
				$xml = simplexml_load_file($this->basePath.'/wipe-data2/'.$asset.'.xml');
			}
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
			}

			if(!empty($data["CPU"]))
			{
				$parts1 = explode("_",$data["CPU"]);
				$parts2 = explode("-",$parts1[0]);
				if(!empty($parts1[1]) && !empty($parts2[1]))
				{
					$asins = Asin::getAssestModelAsinResult($data["Model"], $parts2, $parts1);
					if(!$asins)
					{
						$asins = Asin::getAssestModelAsinOtherResult($data["Model"], $parts2);
					}
				}
			}
		}
		return $asins;
	}

	public function getRefNotification(Request $request)
	{
		if($request->ajax())
		{
			$asset = $request->get("a");
			$model = $request->get("m");
			$data = [
				"models"  => [],
				"cpuname" => ""
			];

			if(!empty($model))
			{
				$asin = FormModel::getAsinModelRecord(intval($model));
				if(strpos(',', $asin))
				{
					$asins = explode(',',$asin);
				}
				else
				{
					$asins = explode(',',$asin);
				}
				if($asin!='0')
				{
					$data["models"] = Asin::getModelFromAsin($asins, $notifications=1);
				}
			}
			else
			{
				$data["models"] = $this->getAssetModels($request);
			}
			$travelerId = $asset;
			$fname =  $this->formData.'/'.$travelerId .'.json';
			$fname2 = $this->checkFile($this->basePath.'/wipe-data/*',$travelerId);
			$fname3 = $this->checkFile($this->basePath.'/wipe-data/bios-data/*',$travelerId);
			$fname4 = $this->checkFile($this->basePath.'/makor-processed-data/wipe-data/*',$travelerId);
			$fname5 = $this->checkFile($this->basePath.'/makor-processed-data/bios-data/*',$travelerId);
			if($fname2 || $fname4)
			{
				if ($fname2) $files = glob($this->basePath.'/wipe-data/*.xml');
				if ($fname4) $files = glob($this->basePath.'/makor-processed-data/wipe-data/*.xml');
				foreach($files as $f)
				{
					if (stripos($f,$travelerId)!==false)
					{
						try
						{
							$xml = simplexml_load_file($f);
							if(isset($xml->Report->Hardware->Processors))
							{
								$cpus = $xml->Report->Hardware->Processors->Processor;
								foreach($cpus as $cpu)
								{
									$data["cpuname"] = strtolower($cpu->Name);
								}
							}
						}
						catch (Exception $e)
						{
							$data["cpuname"] = "";
						}
					}
				}
			}
			else
			{
				if($fname3 || $fname5)
				{
					if($fname3) $files = glob($this->basePath.'/wipe-data/bios-data/*.xml');
					if($fname5) $files = glob($this->basePath.'/makor-processed-data/bios-data/*.xml');
					foreach($files as $f)
					{
						if (stripos($f,$travelerId)!==false)
						{
							try
							{
								$xml=simplexml_load_file($f);
								if(isset($xml->node->node))
								{
									foreach($xml->node->node->node as $s)
									{
										if($s["id"]=="cpu:0" || $s["id"]=="cpu")
										{
											$cpu = $s->product;
											$data["cpuname"] = strtolower($cpu);
										}
									}
								}
							}
							catch (Exception $e)
							{
								$data["cpuname"] = "";
							}
						}
					}
				}
			}
			return json_encode($data);
		}
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function getPreview(Request $request)
	{
		if($request->ajax())
		{
			$data = array();
			$valid=1;
			$ctab = $request->get("radio_2");
			$config = FormsConfig::getTab($tabname='Notes', $isActive='');
			if (!empty($ctab))
			{
				$config2 = FormsConfig::getTab($ctab, $isActive='');
				$config = array_merge($config->toArray(),$config2->toArray());
			}
			foreach ($config as $fld)
			{
				$itmid = $fld["qtype"] . "_" . $fld["id"];
				$itmidnew = $fld["qtype"] . "_" . $fld["id"]. "_new";
				$qtype = $fld["qtype"];
				$grp = $fld["grp"];
				$key = $fld["question"];
				if($fld["required"]) $key.="*";
				$vals = explode(";",$fld["options"]);
				if ($request->get($itmid) || $request->get($itmidnew) || $request->get($itmid)==="0" || $request->get($itmidnew)==="0")
				{
					if ($qtype=="mult")
					{
						$resp = $request->get($itmid);
						if($request->get($itmidnew))
						{
							$resp[]=$request->get($itmidnew);
						}
						$response = implode("<br>",$resp);
					}
					elseif($qtype=="radio" || $qtype=="dropdown")
					{
						if($request->get($itmidnew))
						{
							$response = $request->get($itmidnew);
						}
						else
						{
							$response = $request->get($itmid);
						} 
					}
					else
					{
						$response = $request->get($itmid);
					}

					if ($key=="Original HDD Size" || $key=="HDD Size")
					{
						if($response>50)
						{
							$response .= "GB";
						}
						else
						{
							$response .= "TB";
						}
					}
					if ($key=="Original RAM Size" || $key=="RAM Size")
					{
						$response .= "GB";
					}
					if (isset($data[$key]))
					{
						$data[$key] .= "<br>".$response;
					}
					else
					{
						$data[$key] = $response;
					}
				}
				else
				{
					if (!isset($data[$key]))
					{
						if($fld["required"])
						{
							$data[$key] = "<span style='color:red'>Not specified</span>";
							$valid = 0;
						}
						else $data[$key] = "Not specified";
					}
				}
			}
			$output  = "<table class='table table-hover' id='preview-table'>";
			$output .= "<tr><th>Question</th><th>Value</th></tr>";
			foreach ($data as $key=>$val)
			{
				$output .= "<tr><td>" . $key."</td><td>" . $val. "</td></tr>";
			}
			$output .= "</table>";
			if ($valid) $output .= "<input type='button' id='frmSubmitBtn' disabled class='btn btn-default border' value='Submit' onclick='frmSubmit()'/> ";
			$output .= "<input type='button' class='btn btn-default border' value='Edit' onclick='hidePreview()'/>";
			return $output;
		}	
		else
		{
			return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
		}
	}

	public function storeAuditRecord(Request $request)
	{
		$authUserName = Sentinel::getUser()->first_name;
		$functionGroups = FormsConfig::getFormConfigFields($request->get('radio_2'), $group = 'Description');
		$descrTypes = array();
		foreach ($functionGroups as $functionGroup)
		{
			$xmlGroups = explode(';', $functionGroup['xml_grp']);
			$options = explode(';', $functionGroup['options']);
			foreach ($options as $key => $option)
			{
				if (isset($xmlGroups[$key]))
				{
					$descrTypes[$option] = trim($xmlGroups[$key]);
				}
			}
		}

		$data["items"] = array();
		if ($asin = $request->get('asinid'))
		{
			$data["asin"] = $asin;
			$refurb = $request->get('refurb');
			$sess = Session::getOpenStatucRecord($request, $status='open');
			if(count($sess) > 0) $sess = $sess[0];
			if ($sess && $refurb)
			{
				SessionData::deleteSeesionDataRecorde($sess, $request->get("text_1"));
				$sessionRecorde = [
					"sid" => $sess,
					"aid" => $asin,
					"asset" => $request->get("text_1"),
					"added_by" => $authUserName,
					"added_on" => $this->current
				];
				SessionData::addSessionDataRecord((object) $sessionRecorde ,$this->current);
			}
		}
		else
		{
			$data["asin"] = 0;
		}

		if ($model = $request->get('modelid'))
		{
			$data["model"] = $model;
			$grade = $request->get('grade');
			if ($grade == "A" || $grade == "A+")
			{
				ListData::deleteListDataRecorde($model, $request->get("text_1"));
				$tech = FormModel::getFormModelTab($model);
				if ($tech == "Laptop" || $tech == "Computer" || $tech == "All In One")
				{
					$listData = [
						"mid" => $model,
						"cpu" => $request->get("cpuname"),
						"grade" => $grade,
						"asset" => $request->get("text_1"),
						"added_by" => $authUserName,
						"added_on" => $this->current
					];
					ListData::addListDataRecord((object) $listData);
				}
			}
		}
		else
		{
			$data["model"] = 0;
		}
		$outxml = array("user" => $authUserName, "processed" => $this->current);
		$config = FormsConfig::getAllRecord();
		$travelerId = "";
		foreach ($config as $i => $fld)
		{
			$item = array();
			$itmid = $fld["qtype"] . "_" . $fld["id"];
			$itmidnew = $fld["qtype"] . "_" . $fld["id"] . "_new";
			$qtype = $fld["qtype"];
			$grp = str_replace(array(" ", "-", ":", ".", "/"), "_", $fld["grp"]);
			$key = str_replace(array(" ", "-", ":", ".", "/"), "_", $fld["question"]);
			$vals = explode(";", $fld["options"]);
			if (stripos($fld["config"], "filltemplate") > 0)
			{
				$item["template"] = 1;
			}
			else
			{
				$item["template"] = 0;
			}
			if (stripos($fld["config"], "fillmodel") > 0)
			{
				$item["fillmodel"] = 1;
			}
			else
			{
				$item["fillmodel"] = 0;
			}
			$item["id"] = $itmid;
			$item["type"] = $qtype;
			$item["key"] = $key;
			$item["options"] = $vals;
			$item["new"] = "";
			$itmval = $request->get($itmid);
			$itmvalnew = $request->get($itmidnew);
			if (($itmval !== false && $itmval !== "") || ($itmvalnew !== false && $itmvalnew !== ""))
			{
				if ($key == "Asset_Number")
				{
					$travelerId = $request->get($itmid);
					$data["text_1"] = $request->get($itmid);
				}
				if ($key == "Product_Name")
				{
					$data["radio_2"] = $request->get($itmid);
					$product = $request->get($itmid);
				}
				if ($key == "Technology")
				{
					$technology = $request->get($itmid);
				}
				if ($key == "Model")
				{
					$model = $request->get($itmid);
				}
				if ($qtype == "mult")
				{
					$resp = $request->get($itmid);
					if($resp)
					{
						if (!empty($itmvalnew))
						{
							$resp[] = $request->get($itmidnew);
							$item["new"] = $request->get($itmidnew);
						}
						$item["value"] = $resp;
						$response = array("mult" => $resp);
					}
					else
					{
						continue;
					}
				}
				elseif ($qtype == "radio")
				{
					if (!empty($itmvalnew))
					{
						$response = $request->get($itmidnew);
						$item["new"] = $request->get($itmidnew);
					}
					else
					{
						$response = $request->get($itmid);
					}
					$item["value"] = array($response);
				}
				elseif ($qtype == "dropdown")
				{
					if (!empty($itmvalnew))
					{
						$response = $request->get($itmidnew);
						$item["new"] = $request->get($itmidnew);
					}
					else
					{
						$response = $request->get($itmid);
					}
					$item["value"] = array($response);
				}
				else
				{
					$response = $request->get($itmid);
					if($item["key"] == 'Model')
					{
						if(strpos($response, "("))
						{	
							$model = substr($response, 0, strpos($response, "("));
							$item["value"] = array(trim($model));
						}
						else
						{
							$item["value"] = array($response);
						}
					}
					else
					{
						$item["value"] = array($response);
					}
				}
				$data["items"][] = $item;
				if ($key == "HDD_Size" || $key == "Original_HDD_Size")
				{
					if ($response > 50)
					{
						$response .= "GB";
					}
					else
					{
						$response .= "TB";
					}
				}
				if ($key == "RAM_Size" || $key == "Original_RAM_Size")
				{
					$response .= "GB";
				}
				if (!empty($grp))
				{
					if ($grp == "Description" && is_array($response))
					{
						if(is_array($response["mult"]))
						{
							foreach ($response["mult"] as $itm)
							{
								if (isset($descr_types[$itm]))
								{
									$descr = $descr_types[$itm];
								}
								else
								{
									$descr = "Additional";
								}
								$outxml[$grp][$descr]["mult"][] = $itm;
								if ($descr !== $key)
								{
									$outxml[$grp][$key]["mult"][] = $itm;
								}
							}
						}
					}
					else
					{
						if($response)
						{
							$outxml[$grp][$key] = $response;
						}
						else
						{
							continue;
						}
					}
				}
				else
				{
					if (!empty($outxml[$key]))
					{
						if($response)
						{
							$outxml[$key] .= ";" . $response;
						}
						else
						{
							continue;
						}
					}
					else
					{
						if($response)
						{
							if($key == 'Model')
							{
								if(strpos($response, "("))
								{	
									$model = substr($response, 0, strpos($response, "("));
									$outxml[$key] = trim($model);
								}
								else
								{
									$outxml[$key] = $response;
								}
							}
							else
							{
								$outxml[$key] = $response;
							}
						}
						else
						{
							continue;
						}
					}
				}
				$adminEmails = $this->adminEmails;
				$subject = "New item addition request";
				if (!empty($itmvalnew) && !in_array($itmvalnew, $vals) && stripos($fld["config"], "allowcustom") === false)
				{
					$body = $authUserName . " requested to add the value '" . $request->get($itmidnew) . "'" .
					"to the set of options for question '" . $fld["question"] . "' in '" . $fld["tab"] . "' tab (ID:" . $fld["id"] . ").\n" ."Please verify and make corresponding change in form configuration.";
					Mail::raw($body, function ($m) use ($subject, $adminEmails) {
						$m->to($adminEmails)->subject($subject);
					});
				}
			}
		}
		$xmlData = new \SimpleXMLElement('<?xml version="1.0"?><data/>');
		$this->array_to_xml($outxml, $xmlData);
		if ($travelerId != "")
		{
			if ($product == "Mobile_Device")
			{
				$fname = $this->wipeDataMobile.'/'.$travelerId.'.xml';
			}
			else
			{
				$fname = $this->wipeDataAdditional.'/'.$travelerId.'.xml';
			}
			$result = $xmlData->asXML($fname);
		}
		else
		{
			$result = false;
		}
		// print_r($request->All());
		// print_r($outxml);
		// print_r($data);
		// echo "product - ".$product ;
		// echo "technology - ".$technology ;
		// echo "model - ".$model ;
		// echo $travelerId;
		// die;
		if ($travelerId != "")
		{
			$fname = $this->formData.'/'.$travelerId.'.json';
			file_put_contents($fname, json_encode($data));
			FormData::deleteFormDataRecorde($type = "data", $authUserName);
			$formData = array(
				"type" => "data",
				"user" => $authUserName,
				"trid" => $travelerId,
				"product" => $product,
				"data" => json_encode($data)
			);
			FormData::saveFormDataRecorde((object) $formData);
		}

		$model = $outxml['Model'];
		$product = $outxml['Product_Name'];
		$technology = $outxml['Technology'];
		if(strpos($model, "("))
		{	
			$model = substr($model, 0, strpos($model, "("));
			$model = trim($model);
		}
		else
		{
			$model = $outxml['Model'];
		}
		if (!empty($product) && !empty($technology) && !empty($model))
		{
			$add = $request->get("addModel");
			if ($add)
			{
				if (!FormModel::getFormAllRecordExist($product, $technology, $model))
				{
					$formModelData = [
						"tab" => $product,
						"technology" => $technology,
						"model" => $model
					];
					FormModel::saveFormRecord((object) $formModelData);
				}
			}
			$modelid = FormModel::getFormAllRecordExist($product, $technology, $model);
			
			if($modelid)
			{
				FormData::deleteFormDataRecordeByID($type='model', $modelid->id);
				$formData = [
					"type" => "model",
					"user" => $authUserName,
					"trid" => $modelid->id,
					"product" => $product,
					"data" => json_encode($data)
				];
				FormData::saveFormDataRecorde((object) $formData);
			}
		}
		$pageaction = $request->pageaction;
		if($result)
		{
			return redirect()->route('audit', ["pageaction" => $pageaction, "redirect" => "true"])->with('success', "Your data has been saved");
		}
		else
		{
			return redirect()->route('audit', ["pageaction" => $pageaction, "redirect" => "true"])->with('error', "Unable to save the file");
		}
	}

	public function array_to_xml($data, &$xmlData)
	{
		foreach ($data as $key => $value)
		{
			if (is_numeric($key))
			{
				$key = 'item' . $key; //dealing with <0/>..<n/> issues
			}
			if (is_array($value))
			{
				if (!empty($value["mult"]))
				{
					foreach ($value["mult"] as $val)
					{
						if(!empty($key))
						{
							$xmlData->addChild($key, htmlspecialchars($val));
						}
					}
				}
				else
				{
					$subnode = $xmlData->addChild($key);
					$this->array_to_xml($value, $subnode);
				}
			}
			else
			{
				if($key)
				{
					$xmlData->addChild($key, htmlspecialchars($value));
				}
			}
		}
	}
}     
