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

    
    public static function AddUpdatePackage($packagedata) 
    {
    	$result = false; 
   		return $result;
    }




}
