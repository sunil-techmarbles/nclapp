<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class Asin extends Model
{
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asin',		
		'price',		
		'manufacturer',		
		'model',		
		'model_alias',		
		'notifications',	 	
		'form_factor',		
		'cpu_core',		
		'cpu_model',		
		'cpu_speed',	
		'ram',		
		'hdd',		
		'os',		
		'webcam',		
		'notes',		
		'link',		
		'shopify_product_id',	
    ];


    // Get All Supplies
    public static function getAllAsins()
    {
    	return self::get();
    }

    public static function getModelList()
    {
    	return self::select('asins.id', 'asins.asin', 'asins.model', 'asins.form_factor')
    		->orderBy('asins.model', 'DESC')
    		->get();	
    }
    
}
