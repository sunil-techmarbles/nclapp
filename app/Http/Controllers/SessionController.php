<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SessionsImport;
use Carbon\Carbon;
use File;
use PDF;
use Config;
use App\Asin;
use App\Supplies;
use App\Shipment;
use App\Session;
use App\ShipmentsData;
use App\SessionData;
use App\SupplieEmail;

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

    public function getAssetsAsinId($request, $currentSession)
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
			                "sid" => $currentSession,
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
    	$currentSession = Session::getOpenStatucRecord($request, $status = 'open');
    	if(count($currentSession) > 0)
    	{
	    	$currentSession = $currentSession[0];
	    	Session::updateSessionRecord($status="closed", $this->current);
	    	Session::addSessionRecord($request, $this->current);
			$sessionSummary = SessionData::sessionSummary($currentSession);
			$sessionItems = SessionData::sessionItems($currentSession);
			$sessionParts = SessionData::sessionParts($currentSession);
			if (!empty($sessionItems))
			{		
				$fp = fopen($this->sessionReports.'/session'.$currentSession.'.csv', "w");
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

				if (!File::exists($this->sessionReports))
				{
					File::makeDirectory($this->sessionReports, 0777, true, true);
				}
				
				$pdf = PDF::loadView('admin.pdf.sessdetails', $data);
				$pdf->save($this->sessionReports.'/session'.$currentSession.'.pdf');
				$sessionEmails = Config::get('constants.sessionEmails');
				$subject = 'Session details';
				$name = $currentSession.'.csv';
				$name2 = $currentSession.'.pdf';
				$files[] = [
					'url' => $this->sessionReports.'/session'.$name, 
					'name' =>  $name,
					'extension' => substr($name, strpos($name, ".") + 1)
				];
				$files[] = [
					'url' => $this->sessionReports.'/session'.$name2,
					'name' => $name2,
					'extension' => substr($name2, strpos($name2, ".") + 1)
				];
				Mail::send('admin.emails.sessemail', $data, function ($m) use ($subject, $sessionEmails, $files) {
		            $m->to($sessionEmails)->subject($subject);
		            foreach($files as $file)
		            {
		                $m->attach($file['url'], array(
		                    'as' => $file['name'],
		                    'mime' => $file['extension'])
		                );
		            }
		        });
			}
    	}
    }

    public function sessionSearchAndWithdrawAndReorder($request, $session)
    {
		$sessionName = Session::getCurrentSessionName($session);
		$result = ['status' => false, 'message' => 'Something went wrong'];
		if($r = $request->get('remove'))
		{
			$outpout = SessionData::updateSessionStatus($session,$r,$satus='removed');
			if($outpout)
			{
				$result = ['status' => true, 'message' => 'Remove successfully'];
			}
		}
		if($r = $request->get('restore'))
		{
			$outpout = SessionData::updateSessionStatus($session,$r,$satus='active');
			if($outpout)
			{
				$result = ['status' => true, 'message' => 'Restore successfully'];
			}
		}
		return $result;
    }

    public function index(Request $request)
	{
		$failures = [];
		$items = [];
		$parts = [];
		$sessions = [];
		$assets = [];
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
						$this->getAssetsAsinId($import->data, $currentSession);
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
				}
			}
		}
		if($request->get('new_session') && $request->get('session_name') && !$request->get('bulk_upload'))
		{
			$this->sendSessionPdfReport($request);						
		}
		$sessions = Session::getSessionRecord($request);
		foreach($sessions as &$s)
		{
			$s['count'] = SessionData::getSessionDataCount($s['id']);
		}
		if($session = $request->get('s'))
		{
			$sessionName = Session::getCurrentSessionName($session);
			$output = $this->sessionSearchAndWithdrawAndReorder($request, $session);
			if($output['status'])
			{
				$status = 'success';
                $message = $output['message'];
                \Session::flash($status, $message);
			}

			$items = SessionData::getSessionItems($session, $satus='active');
			$assts = SessionData::getSessionAssets($session, $satus='active');
			foreach($assts as $a)
			{
				if(!empty($a['asset']))
				{
					if(!isset($assets['asin'.$a['aid']])) $assets['asin'.$a['aid']] = ["active"=>[],"removed"=>[]];
					$assets['asin'.$a['aid']][$a['status']][] = $a['asset'];	
				}
			}
			$parts = Supplies::getSessionParts($session, $satus='active');
			if(!$pparts = $request->get("ppart")) $pparts = [];
			foreach($parts as $p)
	        {
	            if($request->has('withdraw'))
	            {
	                if(in_array($p["id"],$pparts))
	                {
	                    $newQty = max(0,$p["qty"]-$p["required_qty"]);
	                    Supplies::updateQuantityBySupplieID($p["id"], $newQty);
	                    $status = 'success';
	                    $message = 'Withdraw successfully';
	                    \Session::flash($status, $message);
	                }
	            }
	            if($p["missing"] > 0)
	            {
	                $p["reorder_qty"] = max($p["missing"] + $p["low_stock"],$p["reorder_qty"]);
	                if($request->has('reorder'))
	                {
	                    $vars = array_keys($p->toArray());
	                    $body = $p["email_tpl"];
	                    foreach($vars as $v)
	                    {
	                        $body = str_replace("[".$v."]",$p[$v],$body);
	                    }
	                    $user = SupplieEmail::getsuppliersEmails(intval($p["id"]));
	                    $subject = $p['email_subj'];
	                    if(sizeof($user) > 0)
	                    {
	                        $current = Carbon::now();
	                        Supplies::updateMailSentTime($p["id"],$current);
	                        Mail::raw($body, function ($m) use ($subject,$user) {
	                            $m->to($user->toArray())
	                            ->subject($subject);
	                        });
	                        $mailSent[] = $p["part_num"];
	                        $status = 'success';
	                        $message = 'Part number '.implode(',', $mailSent).' Mail has been sent successfully';
	                        \Session::flash($status, $message);
	                    }
	                    continue;
	                }
	            }
	        }
		}
		return view('admin.sessions.list', compact('sessionName', 'sessions', 'pageMessage', 'assets', 'parts','items'));
	}
}
