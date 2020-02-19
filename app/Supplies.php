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
		'applicable_models',	
		'dept',	
		'price',		
		'vendor',	
		'low_stock',	
		'reorder_qty',	
		'dlv_time',	
		'bulk_options',		
		'emails',	
		'email_subj',	
		'email_tpl',		
		'email_sent',
    ];


    // Get All Supplies
    public static function getAllSupplies()
    {
    	return self::get();
    }
}
