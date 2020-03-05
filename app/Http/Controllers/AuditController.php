<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormsConfig;
use App\Audit;
use App\LenovoModelData;


class AuditController extends Controller
{  
	
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

 
    public function index(Request $request)
    { 
    	$forms_data = FormsConfig::GetTab($tab = 'Notes');    
    	$output = "";
    	$cgrp = "X"; 

    	foreach ( $forms_data as $formdata ) 
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
    	return view('admin.audit.index' ,  compact('output') );    
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
	
	public function get_form_dropdown($fld) 
	{
		$output = "";
		if ($fld["qtype"]=="dropdown") {
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
 
}
