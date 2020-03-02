<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use File;
use App\Shipment;
use App\ShipmentsData;
use App\Asin;
use App\AsinAsset;

class ShipmentController extends Controller
{
	public $basePath, $refurbLabels, $current, $refurbAssetData, $formData;

	public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->current = Carbon::now();
    	$this->formData = $this->basePath.'/form-data';
    	$this->refurbAssetData = $this->basePath.'/refurb-asset-data';
    	$this->refurbLabels = $this->basePath.'/refurb-labels';
    }

    public function getAssetsResult(Request $request, $assets)
    {
    	$result = ['status' => true, 'message' => ''];
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
				if(!$aid) $aid = AsinAsset::getAsinsAidByAssest($asset);
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
					$data = [
						"sid" => $sess,
						"aid" => $aid,
						"sn" => $sn,
						"old_coa" => $old_coa,
						"new_coa" => $new_coa,
						"win8_activated" => $win8,
						"asset" => $asset,
						"added_by" => Sentinel::getUser()->first_name.' - '.Sentinel::getUser()->last_name
					];
					ShipmentsData::deleteOldShipmentData($sess,$asset);
					ShipmentsData::addShipmentData($data);
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

    public function shipmentItems(Request $request, $shipmentName)
	{
		$r = '';
		$status = '';
		if($request->has('remove'))
		{
			$r = $request->get('remove');
			$status = 'removed';
		}
		if($request->has('restore'))
		{
			$r = $request->get('restore');
			$status = 'active';
		}
		ShipmentsData::updateShipmentStatus($r, $status , $shipmentName);
		$asins = ShipmentsData::getResultAsinsAndShipmentData($status='active', $shipmentName);
		foreach($asins as &$a)
		{
			$aid = $a['aid'];
			$a['items'] = ShipmentsData::getResultAsinsAndShipmentDataByID($aid, $status='active', $shipmentName);
		}
		return $asins;
	}

	public function index(Request $request)
	{
		$asins = [];
		$assets = [];
		if($asset = $request->get('asset')){
			$assets[] = $asset;
		}
		if($asset = $request->get('asset1')){
			$assets = explode(PHP_EOL,$asset);
		}
		$assets = $this->getAssetsResult($request, $assets);
		if($request->has('s'))
		{
			$shipmentName = Shipment::getNameOfRecordByID($request->id);
			$asins = $this->shipmentItems($request, $shipmentName);
		}
		$shipments = Shipment::getAllRecord($request);
		foreach($shipments as &$s)
		{
			$s['count'] = ShipmentsData::getShipmentCountByID($s['id']);
		}
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
		$shipment = Shipment::addShipmentRecord($request, $this->current);
		if($shipment)
		{
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


	// public function FunctionName($value='')
	// {
	// 	if($req->getParam('new_session') && $req->getParam('session_name')) {
	// 	$current_session = $db->get('tech_shipments','id',['status'=>'open']);
	// 	$db->update("tech_shipments",['updated_on'=>date('Y-m-d H:i:s'),'status'=>'closed'],['status'=>'open']);
	// 	$db->insert("tech_shipments",['started_on'=>date('Y-m-d H:i:s'),'name'=>$req->getParam('session_name')]);

	// 	$sql = "select d.aid, s.id, count(d.aid) as cnt,
	// 			a.asin, a.price, a.model, a.form_factor, a.cpu_core, a.cpu_model, a.cpu_speed, a.ram, a.hdd, a.os, a.webcam, a.notes, a.link
	// 	 		from tech_shipments_data d inner join tech_asins a on d.aid = a.id inner join tech_shipments s on d.sid = s.id
	// 	 		where d.sid='$current_session' and d.status='active' group by d.aid";
	// 	$session_summary = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	// 	$sql = "select d.aid, s.id, d.old_coa, d.new_coa, d.win8_activated, d.asset, d.sn, d.added_on,
	// 			a.asin, a.price, a.model, a.form_factor, a.cpu_core, a.cpu_model, a.cpu_speed, a.ram, a.hdd, a.os, a.webcam, a.notes, a.link
	// 	 		from tech_shipments_data d inner join tech_asins a on d.aid = a.id inner join tech_shipments s on d.sid = s.id
	// 	 		where d.sid='$current_session' and d.status='active' order by d.aid, d.asset";
	// 	$session_items = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	// 	$sql = "select i.id, i.part_num, i.item_name, i.qty, sum(p.qty) as required_qty, sum(p.qty) - i.qty as missing,
	// 			i.vendor, i.dlv_time, i.low_stock, i.reorder_qty, i.email_tpl, i.emails, i.email_subj
	// 			from tech_inventory i inner join tech_asins_parts p on i.id = p.part_id
	// 			inner join tech_shipments_data d on p.asin_id = d.aid
	// 			where d.sid = '$current_session' and d.status='active' group by i.id";
	// 	$parts = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	// 	foreach($parts as $p) {
	// 		$nqty = max(0,$p["qty"]-$p["required_qty"]);
	// 		$db->update("tech_inventory",["qty"=>$nqty],["id"=>$p["id"]]);
	// 	}

	// 	if (!empty($session_items)) {
	// 		$data = ["items"=>$session_items,"summary"=>$session_summary,"name"=>$db->get("tech_shipments","name",["id"=>$current_session])];
	// 		$out = new Output("_ship_email.php",$data);
	// 		$eml = $out->render();

	// 		if (!file_exists('session-reports')) {
	// 		    mkdir('session-reports', 0777, true);
	// 		}

	// 		$fp = fopen('session-reports/coa'.$current_session.'.csv', "w");
	// 		fputcsv($fp, ["Shipment ID","Asset","S/N","Old COA","New COA","WIN8","Model","CPU","Added"]);
	// 		foreach ($session_items as $i) {
	// 		    $itm = [
	// 		    	$i["id"],
	// 		    	$i["asset"],
	// 		    	$i["sn"],
	// 		    	$i["old_coa"],
	// 		    	$i["new_coa"],
	// 		    	($i["win8_activated"]?'WIN8 Activated':''),
	// 		    	$i["model"],
	// 		    	$i['cpu_core'].' '.$i['cpu_model'].' CPU @' .$i['cpu_speed'],
	// 		    	$i["added_on"]
	// 		    ];
	// 		    fputcsv($fp, $itm);
	// 		}
	// 		fclose($fp);


	// 		$fp = fopen('session-reports/shipment'.$current_session.'.csv', "w");
	// 		fputcsv($fp, ["ASIN","Asset","Model","Form Factor","S/N","CPU","Price","Added"]);
	// 		foreach ($session_items as $i) {
	// 		    $fields = [
	// 		    	$i["asin"],
	// 				$i["asset"],
	// 				$i["model"],
	// 				$i["form_factor"],
	// 				$i["sn"],
	// 				$i["cpu_core"].' '.$i["cpu_model"].' CPU @ '.$i["cpu_speed"],
	// 				number_format($i["price"],2),
	// 				$i["added_on"]
	// 		    ];
	// 		    fputcsv($fp, $fields);
	// 		    $db->update("tech_sessions_data",['run_status'=>'shipped'],['asset'=>$i["asset"]]);
	// 		}
	// 		fclose($fp);

	// 		$mail = new Email();
	// 		$attach = dirname(dirname(__FILE__)).'/session-reports/shipment'.$current_session.'.csv';
	// 		$attach2 = dirname(dirname(__FILE__)).'/session-reports/coa'.$current_session.'.csv';
	// 		$mid = $mail->queue(str_replace(",",";",SHIPMENT_EMAILS),'Shipment details',$eml,true,'',$attach.';'.$attach2);
	// 		$mail->release($mid);
	// 	}
	// 	Utils::redir("index.php?page=shipments&s=$current_session&newlink=".time());
	// 	}
	// }
}
