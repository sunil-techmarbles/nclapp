<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingSystem extends Model
{
	use SoftDeletes; 
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'operating_system_name',
        'status',
        'operating_system_value',
    ];
    
    public static function getRecord()
    {
		return self::where('status', 1)
			->get();
	}
}
