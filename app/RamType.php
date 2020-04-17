<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RamType extends Model
{
	use SoftDeletes; 
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'ram_type_name',
        'status',
        'ram_type_value',
    ];
    
    public static function getRecord()
    {
		return self::where('status', 1)
			->get();
	}
}
