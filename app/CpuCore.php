<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CpuCore extends Model
{
	use SoftDeletes; 
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'cpu_core_name',
        'status',
        'cpu_core_value',
    ];
    
    public static function getRecord()
    {
		return self::where('status', 1)
			->get();
	}
}
