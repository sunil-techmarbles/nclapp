<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyPricing extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'Asset_ID',
		'SerialNumber',
		'Class',
		'Brand',
		'Model',
		'Model_Number',
		'Form_Factor',
		'Processor',
		'RAM',
		'Memory_Type',
		'Memory_Speed',
		'Hard_Drive',
		'HD_Interface',
		'HD_Type',
		'Condition',
		'Price',
		'Final_Price',
    ];
}
