<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItamgRecycleInventory extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
		'Brand',
		'Model',
		'PartNo',
		'Category',
		'Notes',
		'Value',
		'Status',
		'require_pn',
    ];

    public static function getAllRecord($value='')
    {
    	return self::get();
    }
}
