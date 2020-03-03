<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class Shipment extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'name',	
		'started_on',
		'updated_on',
		'status',	
    ];

    public static function getAllRecord($request)
    {
    	return self::select('id','name','started_on','updated_on','status')
    		->orderBy('status', 'DESC')
    		->orderBy('started_on', 'DESC')
    		->get();
    }

    public static function addShipmentRecord($request, $current)
    {
    	$satus = false;
    	$shipment = new Shipment();
    	$shipment->name = $request->session_name;	
		$shipment->started_on = $current;

		if($shipment->save())
		{
			$satus = $shipment->id;
		}
		return $satus;
    }

    public static function updateShipmentRecord($request, $current)
    {
    	return self::where(['status' => 'open'])
    		->update(['updated_on'=> $current, 'status'=>'closed']);
    }

    public static function getOpenShipment($request)
    {
    	return self::where(['status' => 'open'])->pluck('id');
    }

    public static function getNameOfRecordByID($ID)
    {
    	return self::where(['id' => $ID])
    		->pluck('name')
    		->first();
    }
}
