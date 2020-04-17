<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HddType extends Model
{
	use SoftDeletes; 
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'hdd_type_name',
        'status',
        'hdd_type_value',
    ];
    
    public static function getRecord()
    {
		return self::where('status', 1)
			->get();
	}
}
