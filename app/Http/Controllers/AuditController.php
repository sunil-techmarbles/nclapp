<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormsConfig;
use App\Audit;
use Config;
use File;
use App\LenovoModelData;

class AuditController extends Controller
{
	public $basePath, $formData, $sandboxMode;
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->formData = $this->basePath.'/form-data';
    	$this->sandboxMode = false;
    }

	public function AddPartNumber(Request $request) 
	{  
		$newpartnumber = LenovoModelData::InsertNewPartNumber( $request->modal, $request->partnumber ); 

		if(!empty( $newpartnumber ) && $newpartnumber != false)
		{	
			$response['status']  = 'success';
        	$response['message'] = 'New Part Number added'; 
		}
		else 
		{ 
			$response['status']  = 'error';
        	$response['message'] = 'Unable to add Part Number'; 
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
			
	        if( method_exists( $this , "get_form_$qtype" ) ) 
	        {
				$function = "get_form_$qtype"; 
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
		$damageScores = Config::get('constants.auditDamageScores');;
		$refurbBlacklist = Config::get('constants.auditRefurbBlacklist');;
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
  					<label class='ttl' for='$itemid'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>
  					<input type='text' value='".$fld["default_val"]."' class='form-control' id='$itemid' name='$itemid' ".$fld["config"].($fld["required"]?" required='true'":"") ."/>
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
  					<label class='ttl' for='$itemid'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>
  					<input type='number' value='".$fld["default_val"]."' class='form-control' id='$itemid' name='$itemid' ".$fld["config"].($fld["required"]?" required='true'":"") ."/>
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
  					<label class='ttl' for='$itemid'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label>
  					<textarea class='form-control' rows='5' style='width:100%' id='$itemid' name='$itemid' ".$fld["config"].($fld["required"]?" required='true'":"") .">".$fld["default_val"]."</textarea>
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
				    <label class='btn' for='$itemid'><input type='checkbox' value='1' id='$itemid' name='$itemid' ".$fld["config"]."/>".
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
							$output .= "<div class='cb-cnt'><label class='btn' for='$itemid'><input class='calculate_grade' data-grade='$grade' type='checkbox' value='".htmlentities($oname, ENT_QUOTES).
								"' id='$itemid' name='$itemname' ".$fld["config"].($fld["required"]?" required='true'":"") ."/>
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
				$output .= "<div class='form-inline'><label for='$itemid'>$olbl</label> <input type='text' class='form-control' id='$itemid' name='$itemid'/></div>";
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
					<label class='btn' for='$itemid'><input type='radio' id='$itemid'  value='".htmlentities($oname, ENT_QUOTES)."' name='$itemname' "
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
				$output .= "<div class='form-inline'>
					<label for='$itemid'><input type='radio' id='$itemid'  value='Other:' name='$itemname' ".$fld["config"] . ($fld["required"]?" required='true'":"") ."/>
					<span>$olbl</span> <input type='text' class='form-control' id='$dataid' name='$dataid' $addopts onClick='$(\"#$itemid\").prop( \"checked\", true )'/>
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
  					<label class='ttl' for='$itemid'>" . $fld["question"] . ($fld["required"]?" <span class='req'>*</span>":"") ."</label><br/>
  					<select class='form-control' id='$itemid' name='$itemid' ".$fld["config"].($fld["required"]?" required='true'":"") .">" .
  					self::getOptions($options,$fld["default_val"]).  
  					"</select></div>";
			if ($fld["allow_new"]) 
			{
				$selid = $itemid;
				$itemid = $fld["qtype"] . "_" . $fld["id"] . "_new";
				$output .= " <div class='form-group' style='vertical-align:bottom;'><label for='$itemid'>Other:</label> <input type='text' class='form-control' 
				id='$itemid' name='$itemid' onClick='$(\"#$selid\").val($(\"#$selid option:last-child\").val())' /></div>";
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
						{	
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
		$checkAssetidInAdditionalXmldatafolder = $this->checkFile($this->basePath.'/blancco/xml_data/*', $travelerId );
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
}
