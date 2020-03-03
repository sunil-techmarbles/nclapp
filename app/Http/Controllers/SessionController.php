<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SessionsImport;
use Carbon\Carbon;
use File;
use Config;
use App\Asin;
use App\Shipment;
use App\Session;
use App\ShipmentsData;
use App\SessionData;
use App\AsinAsset;

class SessionController extends Controller
{
    public $basePath, $refurbLabels, $current, $refurbAssetData, $formData, $sessionReports;

	public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->current = Carbon::now();
    	$this->sessionReports = $this->basePath.'/session-reports';
    	$this->formData = $this->basePath.'/form-data';
    	$this->formData = $this->basePath.'/form-data';
    	$this->refurbAssetData = $this->basePath.'/refurb-asset-data';
    	$this->refurbLabels = $this->basePath.'/refurb-labels';
    }

    public function getAssetsAsinId($request)
    {
    	$pageMessage = [];
		foreach ($request as $key => $value)
		{
			$rcheck = $value['rcheck'];
			if ($rcheck > 0)
			{	
			    $asset = $rcheck;
			    $manuf = $value['manuf'];
			    $model = $value['model'];
			    $serial= $value['serial'];
			    $cpuModel = $value['cpuModel'];
			    $cpuSpeed = $value['cpuSpeed'];
			    $ram = $value['ram'];
			    $cpudata = explode("-",$cpu_model);
			    $cpuCore = strtolower($cpudata[0]);
			    $cpuMdl = strtolower($cpudata[1]);
			    if(!SessionData::hasAssests($asset))
			    {	
			    	echo "true";
			        $fields = ["id","model","asin","ram","hdd","os","cpu_core","cpu_model","cpu_speed","price"];
			        $asins = Asin::getSpecificFourthRecord($fields, $model, $cpuCore, $cpuMdl);
			        if(!$asins)
			        {
			            $asins = Asin::getSpecificFifthRecord($fields, $model, $cpuCore);
			        }
			        if(!$asins)
			        {
			            $asins = Asin::getSpecificSixthRecord($fields, $model);
			        }
			        
			        if(count($asins)==1)
			        {
			            $aid = $asins[0]["id"];
			            $data = [
			                "sid" => $current_session,
			                "aid" => $aid,
			                "asset" => $asset,
			                "added_by" => Sentinel::getUser()->first_name.' - '.Sentinel::getUser()->last_name,
			                "added_on" => $this->current
			            ];
			            SessionData::addSessionDataRecord((object) $data, $current);
			        }
			        elseif(!$asins)
			        {
			            $pageMessage[] = "The ASIN match for the Asset $asset (".$manuf.$model.','.$cpuModel.','.$cpuSpeed.") was not found";
			        }
			        else
			        {
			            $out = "Please select matching ASIN for the Asset $asset (".$manuf.$model.','. $cpuModel.','.$cpuSpeed.','. $ram."):<br/>";
			            $out.= "<select id='asset'".$asset."' onchange='setBulkAsin(this.id)'><option value=''>Select</option>";
			            foreach($asins as $a)
			            {
			                $out.= "<option value='".$a['id']."'>".$a['asin']." ".$a['model']." ".$a['cpu_core']."-".$a['cpu_model']."</option>";
			            }
			            $out.= "</select>";
			            $pageMessage[] = $out;
			        }
			    }
			}
		}
		return $pageMessage;
    }

    public function sendSessionPdfReport($request)
    {
    	$currentSession = Session::getOpenStatucRecord($request, $status='open');
    	$currentSession = $currentSession[0];
    	Session::updateSessionRecord($status="closed", $this->current);
    	Session::addSessionRecord($request, $this->current);
		$sessionSummary = SessionData::sessionSummary($currentSession);
		$sessionItems = SessionData::sessionItems($currentSession);
		$sessionParts = SessionData::sessionParts($currentSession);
		if (!empty($sessionItems))
		{		
			$fp = fopen('session-reports/session'.$current_session.'.csv', "w");
			fputcsv($fp, ["ASIN","Asset","Model","Form Factor","CPU","Price","Added"]);
			foreach ($sessionItems as $i)
			{
			    $fields = [
			    	$i["asin"],
					$i["asset"],
					$i["model"],
					$i["form_factor"],
					$i["cpu_core"].' '.$i["cpu_model"].' CPU @ '.$i["cpu_speed"],
					number_format($i["price"],2),
					$i["added_on"]
			    ];
			    fputcsv($fp, $fields);
			}
			fclose($fp);
			$data = [
				"items" => $sessionItems,
				"parts" => $sessionParts,
				"summary" => $sessionSummary,
				"name" => Session::getCurrentSessionName($currentSession)
			];
			$out = new Output("_sess_email.php",$data);
			$eml = $out->render();
			
			$out = new Output("_sess_details.php",$data);
			$att = $out->render();
			
			if (!file_exists('session-reports')) {
			    mkdir('session-reports', 0777, true);
			}
			
			$post = [
			    'apikey' => '2ca05a5d-cafe-464e-856b-780246c03780',
			    'value' => $att,
			    'PageSize' => 'Letter',
			    'MarginLeft' => 20,
			    'MarginTop' => 15
			];

			$ch = curl_init('http://api.html2pdfrocket.com/pdf');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

			// execute!
			$response = curl_exec($ch);

			// close the connection, release resources used
			curl_close($ch);
			
			file_put_contents('session-reports/session'.$current_session.'.pdf',$response);
			
			$mail = new Email();
			$attach = dirname(dirname(__FILE__)).'/session-reports/session'.$current_session.'.pdf';
			$attach2 = dirname(dirname(__FILE__)).'/session-reports/session'.$current_session.'.csv';
			if(is_readable($attach)) $mid = $mail->queue(str_replace(",",";",SESSION_EMAILS),'Session details',$eml,true,'',$attach.';'.$attach2);
			$mail->release($mid);
		}
		Utils::redir("index.php?page=sessions&s=$current_session&reorder=1&newlink=".time());
    }

    public function sessionSearchAndWithdrawAndReorder($request)
    {
		$sess_name = $db->get("tech_sessions","name",["id"=>$sess]);
		if($r = $request->get('remove'))
		{
			$db->update("tech_sessions_data",["status"=>"removed"],["AND"=>["sid"=>$sess,"asset"=>$r]]);
			Utils::redir("index.php?page=sessions&s=".$sess. "&t=".time());
		}
		if($r = $req->getParam('restore'))
		{
			$db->update("tech_sessions_data",["status"=>"active"],["AND"=>["sid"=>$sess,"asset"=>$r]]);
			Utils::redir("index.php?page=sessions&s=".$sess. "&t=".time());
		}
		$sql = "select d.aid, count(d.aid) as cnt, 
				a.asin, a.price, a.model, a.form_factor, a.cpu_core, a.cpu_model, a.cpu_speed, a.ram, a.hdd, a.os, a.webcam, a.notes, a.link
		 		from tech_sessions_data d inner join tech_asins a on d.aid = a.id where d.sid='$sess' and d.status='active' group by d.aid";
		$items = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 	
		
		$sql = "select d.aid, d.asset, d.status
		 		from tech_sessions_data d where d.sid='$sess'";
		$assts = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$assets = [];
		foreach($assts as $a)
		{
			if(!empty($a['asset']))
			{
				if(!isset($assets['asin'.$a['aid']])) $assets['asin'.$a['aid']] = ["active"=>[],"removed"=>[]];
				$assets['asin'.$a['aid']][$a['status']][] = $a['asset'];	
			}
		} 
		
		$sql = "select i.id, i.part_num, i.item_name, i.qty, sum(p.qty) as required_qty, sum(p.qty) - i.qty as missing,
				i.vendor, i.dlv_time, i.low_stock, i.reorder_qty, i.email_tpl, i.emails, i.email_subj
				from tech_inventory i inner join tech_asins_parts p on i.id = p.part_id 
				inner join tech_sessions_data d on p.asin_id = d.aid
				where d.sid = '$sess' and d.status='active' group by i.id";
		$parts = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 	
		$page_msg = [];
		$miss=0;
		$cnt = 0;
		if($request->get("reorder")) $mail = new Email();
		$process = $request->get("withdraw");
		if(!$pparts = $request->get("ppart")) $pparts = [];
		foreach($parts as $p)
		{
			if($process)
			{
				if(in_array($p["id"],$pparts))
				{
					$nqty = max(0,$p["qty"]-$p["required_qty"]);
					$db->update("tech_inventory",["qty"=>$nqty],["id"=>$p["id"]]);
				}
			}
			if($p["missing"]>0)
			{
				$p["reorder_qty"] = max($p["missing"] + $p["low_stock"],$p["reorder_qty"]);
				$miss += $p["missing"];
				if($request->get("reorder"))
				{
					$vars = array_keys($p);
					$body = $p["email_tpl"];
					
					foreach($vars as $v)
					{
						$body = str_replace("[$v]",$p[$v],$body);
					}
					$emails = explode(",",$p["emails"]);
					$mid = $mail->queue(implode(";",$emails),$p["email_subj"],$body); //implode(";",$emails)
					$cnt++;
				}
			}
		}
		if($process) Utils::redir("index.php?page=sessions&s=".$sess. "&t=".time());
		if($request->get("reorder"))
		{
			$page_msg[] = "$cnt emails sent";
			$miss=0;
			$mail->release();
			if($req->getParam("newlink"))
			{
				Utils::redir("index.php?page=sessions");
			}
			else
			{
				Utils::redir("index.php?page=sessions&s=".$sess. "&t=".time());
			}
		}	
    }

    public function index(Request $request)
	{
		$failures = [];
		$items = [];
		$parts = [];
		$sessions = [];
		if ($request->isMethod('post'))
		{
			if($request->has('bulk_upload'))
			{
				if($request->hasFile('bulk_data'))
				{
					$currentSession = Session::getOpenStatucRecord($request, $status='open');
					try
					{	
						$import = new SessionsImport();
						Excel::import($import,request()->file('bulk_data'));
					}
					catch (\Maatwebsite\Excel\Validators\ValidationException $e)
					{
						$failures = $e->failures();
						foreach ($failures as $failure)
						{
							$failure->row(); // row that went wrong
							$failure->attribute(); // either heading key (if using heading row concern) or column index
						}
					}
					$pageMessage = $this->getAssetsAsinId($import->data);
					// if($request->get('new_session') && $request->get('session_name') && !$request->get('bulk_upload'))
					// {
					// 	$pageMessage = $this->sendSessionPdfReport($request);						
					// }
					$sessions = Session::getSessionRecord($request);
					// foreach($sessions as &$s)
					// {
					// 	$s['count'] = Session::getSessionDataCount($s['id']);
					// }
					// if($sess = $request->get('s'))
					// {		
					// 	$this->sessionSearchAndWithdrawAndReorder($request);
					// }
					// print_r($sessions);
				}
			}
		}
		return view('admin.sessions.list', compact('sessions','pageMessage'));

	}
}
