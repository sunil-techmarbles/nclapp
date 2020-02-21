<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class Supplies extends Model
{
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_name',	
		'item_url',	
		'qty',	
		'part_num',	
		'description',		
		'dept',	
		'price',		
		'vendor',	
		'low_stock',	
		'reorder_qty',	
		'dlv_time',	
		'bulk_options',		
		'email_subj',	
		'email_tpl',		
		'email_sent',
    ];


    // Get All Supplies
    public static function getAllSupplies()
    {
    	return self::get();
    }

    public static function addSupplies($request)
    {		
    	$result = false;
    	$supplies = new Supplies();
    	$supplies->item_name = $request->item_name ;
    	$supplies->item_url = $request->item_url;
    	$supplies->qty = $request->qty;
    	$supplies->part_num = $request->part_num;
    	$supplies->description = $request->description;
    	$supplies->dept	= $request->dept;	
		$supplies->price = $request->price;		
		$supplies->vendor = $request->vendor;	
		$supplies->low_stock = $request->low_stock;	
		$supplies->reorder_qty = $request->reorder_qty;	
		$supplies->dlv_time	= $request->dlv_time;	
		$supplies->bulk_options	= $request->bulk_options;		
		$supplies->email_subj = $request->email_subj;	
		$supplies->email_tpl = $request->email_tpl;		
		$supplies->email_sent = $request->email_sent;

		if($supplies->save())
		{
			$result = $supplies->id;
		}
    	
    	return $result;
    	# code...
    }
}
