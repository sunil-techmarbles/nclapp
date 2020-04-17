<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormFactor extends Model
{
	use SoftDeletes; 
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'form_factor_name',
        'status',
        'form_factor_value',
    ];
    
    public static function getRecord()
    {
		return self::where('status', 1)
			->get();
	}
}
