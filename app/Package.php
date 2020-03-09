<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Package extends Model
{

	use SoftDeletes;
	protected $dates = ['deleted_at'];

	
	protected $fillable = [
		'worker_id',
		'value',
		'location',
		'carrier',
		'freight_ground',
		'received',
		'recipient',
		'qty',
		'description',
		'req_name',
		'tracking_number',
		'ref_number',
		'expected_arrival',
		'order_date',
	];


	public static function AddPackage($packagedata) 
	{
		$result = false; 

		$newPackage = new Package(); 
		$newPackage->worker_id = $packagedata->worker_id; 
		$newPackage->value = $packagedata->value; 
		$newPackage->location = $packagedata->location; 
		$newPackage->carrier = $packagedata->carrier; 
		$newPackage->freight_ground = $packagedata->freight_ground; 
		$newPackage->recipient = $packagedata->recipient; 
		$newPackage->qty = $packagedata->qty; 
		$newPackage->description = $packagedata->description; 
		$newPackage->req_name = $packagedata->req_name;
		$newPackage->tracking_number = $packagedata->tracking_number;
		$newPackage->ref_number = $packagedata->ref_number;
		$newPackage->expected_arrival = $packagedata->expected_arrival;
		$newPackage->order_date = $packagedata->order_date;
		$newPackage->received = $packagedata->received;

		if($newPackage->save())
		{ 
			$result = true; 
		}   
		return $result; 
	}


	public static function getPackages($request)
	{
		
		$query = self::select('*');
		
		if($request->has('received'))
		{
			$query = $query->where('received' , 'LIKE' , '%'.$request->get('received').'%');
		} 

		if($request->has('description'))
		{
			$query = $query->where('description' , 'LIKE' , '%'.$request->get('description').'%');
		}

		if($request->has('req_name'))
		{
			$query = $query->where('req_name' , 'LIKE' , '%'.$request->get('req_name').'%');
		}

		if($request->has('tracking_number'))
		{
			$query = $query->where('tracking_number' , 'LIKE' , '%'.$request->get('tracking_number').'%');
		}
 
		if($request->has('ref_number'))
		{
			$query = $query->where('ref_number' , 'LIKE' , '%'.$request->get('ref_number').'%');
		}

		if($request->has('carrier'))
		{
			$query = $query->where('carrier' , 'LIKE' , '%'.$request->get('carrier').'%'); 
		}

		if($request->has('freight_ground'))
		{
			$query = $query->where('freight_ground' , 'LIKE' , '%'.$request->get('freight_ground').'%');
		}

		if($request->has('location'))
		{
			$query = $query->where('location' , 'LIKE' , '%'.$request->get('location').'%');
		}

		if($request->has('recipient'))
		{
			$query = $query->where('recipient' , 'LIKE' , '%'.$request->get('recipient').'%');
		}
 
		if($request->has('worker_id'))
		{
			$query = $query->where('worker_id' , 'LIKE' , '%'.$request->get('worker_id').'%');
		}	

		if($request->has('expected_arrival') && !empty($request->get('expected_arrival')))
		{
			$ea = explode(" - ", $request->get('expected_arrival') ); 
			
			$query = $query->where('expected_arrival' , '>=' , $ea[0] );
			$query = $query->where('expected_arrival' , '<=' , $ea[1] );
		}

		if($request->has('order_date') && !empty($request->get('order_date')))
		{
			$od = explode(" - ", $request->get('order_date') ); 
			
			$query = $query->where('order_date' , '>=' , $od[0] );
			$query = $query->where('order_date' , '<=' , $od[1] );
		}

		return $query->orderBy('id', 'DESC')
			->get();
	}
}
















