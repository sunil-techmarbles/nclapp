<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class ShipmentsData extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	    'sid',		
		'aid',
		'asset',
		'sn',
		'old_coa',
		'new_coa',
		'win8_activated',
		'added_by',
		'added_on',
		'status',
    ];

    public static function addShipmentData($request, $current)
    {
        $result = false;
        $shipmentsData = new ShipmentsData();
        $shipmentsData->sid = $request->sid ;
        $shipmentsData->aid = $request->aid;
        $shipmentsData->asset = $request->asset;
        $shipmentsData->added_by = $request->added_by;
        $shipmentsData->sn = $request->sn;
        $shipmentsData->old_coa = $request->old_coa;
        $shipmentsData->new_coa = $request->new_coa;
        $shipmentsData->win8_activated = $request->win8_activated;
        $shipmentsData->added_on = $current;   
        if($shipmentsData->save())
        {
            $result = $shipmentsData->id;
        }
        
        return $result;
    }


    public static function updateRecord($request)
    {
        return self::where(["asset" => $request->asset])
        ->update(
        	[
				'new_coa'        => $request->new_coa,
				'old_coa'        => $request->old_coa,
				'win8_activated' => $request->win8
			]
        );
    }

    public static function updateShipmentData($asset, $status)
    {
        return self::where(["asset" => $asset])
            ->update(["status" => $status]);
    }

    public static function getShipmentCountByID($Id)
    {
    	return self::where([
                'sid'=>$Id,
    			'status'=>'active'
    		])->count();
    }

    public static function updateShipmentStatus($r, $status , $shipmentName)
    {
    	return self::where(["sid"=> $shipmentName, "asset"=> $r])
    		->update(["status"=> $status]);
    }

    public static function getResultAsinsAndShipmentData($status, $shipmentName)
    {
    	return self::select('shipments_data.aid', 'shipments_data.sid', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
			->selectSub('count(shipments_data.id)', 'cnt')
    		->join('asins as a', function($join) use($shipmentName, $status){
                $join->on('shipments_data.aid', '=', 'a.id');                        
            })
            ->where('shipments_data.sid','=', $shipmentName)
            ->where('shipments_data.status','=', $status)
    		->groupBy('shipments_data.aid')
            ->get();
    }

    public static function getResultAsinsAndShipmentDataByID($aid, $status, $shipmentName)
    {
    	return self::select('shipments_data.aid', 'shipments_data.sid', 'shipments_data.old_coa', 'shipments_data.new_coa', 'shipments_data.win8_activated', 'shipments_data.asset', 'shipments_data.sn', 'shipments_data.added_on','a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
    		->join('asins as a', function($join) use($shipmentName, $status, $aid){
                $join->on('shipments_data.aid', '=', 'a.id');
            })
            ->where('shipments_data.sid','=',$shipmentName)
            ->where('shipments_data.status','=',$status)
            ->where('shipments_data.aid','=',$aid)
    		->orderBy('shipments_data.aid')
    		->orderBy('shipments_data.asset')
            ->get();
    }

    public static function deleteOldShipmentData($sess, $asset)
    {
    	$result = false;
        $shipmentsData = self::where(["sid" => $sess,"asset" => $asset])->first();
        if($shipmentsData)
        {
            if($shipmentsData->delete())
            {
                $result = true;
            }
        }
        return $result;
    }

    public static function sessionSummary($currentSession, $status)
    {
        return self::select('shipments_data.aid', 's.id', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
            ->selectSub('count(shipments_data.id)', 'cnt')
            ->join('asins as a', function($join) use ($currentSession, $status){
                $join->on('shipments_data.aid', '=', 'a.id');
            })
            ->join('shipments as s', function($join) use ($currentSession){
                $join->on('shipments_data.sid', '=', 's.id');
            })
            ->where('shipments_data.sid','=', $currentSession)
            ->where('shipments_data.status','=', $status)
            ->groupBy('shipments_data.aid')
            ->get();
    }

    public static function sessionItems($currentSession, $status)
    {
        return self::select('shipments_data.aid', 's.id', 'shipments_data.old_coa',  'shipments_data.new_coa', 'shipments_data.win8_activated', 'shipments_data.asset', 'shipments_data.sn', 'shipments_data.added_on', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
            ->join('asins as a', function($join) use ($currentSession, $status){
                $join->on('shipments_data.aid', '=', 'a.id');
            })
            ->join('shipments as s', function($join) use ($currentSession, $status){
                $join->on('shipments_data.sid', '=', 's.id');
            })
            ->where('shipments_data.sid','=', $currentSession)
            ->where('shipments_data.status','=', $status)
            ->orderBy('shipments_data.aid', 'DESC')
            ->orderBy('shipments_data.asset', 'DESC')
            ->get();
    }

    public static function shipmentParts($currentSession, $status)
    {
        return self::select('i.id', 'i.part_num', 'i.item_name', 'i.qty', 'i.vendor', 'i.dlv_time', 'i.low_stock', 'i.reorder_qty', 'i.email_tpl', 'i.email_subj')
            ->selectSub('sum(p.qty)', 'required_qty')
            ->selectSub('sum(p.qty) - i.qty', 'missing')
            ->join('supplies as i', function($join) use ($currentSession){
                $join->on('shipments_data.aid', '=', 'i.id');
            })
            ->join('supplie_asin_models as p', function($join) use ($currentSession){
                $join->on('i.id', '=', 'p.supplie_id');
            })
            ->join('shipments_data as d', function($join) use ($currentSession, $status){
                $join->on('d.aid', '=', 'p.asin_model_id');                    ;
            })
            ->where('d.sid','=', $currentSession)
            ->where('d.status','=', $status)
            ->groupBy('i.id')
            ->get();
    }
}
