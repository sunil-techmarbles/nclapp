<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class ShipmentsData extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	    'sid',		
		'aid',
		'asset',
		'sn',
		'old_coa',
		'new_coa',
		'win8_activated',
		'added_by',
		'added_on',
		'status',
    ];

    public static function updateRecord($request)
    {
        return self::where(["asset" => $request['asset']])->update(
        	[
				'new_coa'        => $request->new_coa,
				'old_coa'        => $request->old_coa,
				'win8_activated' => $request->win8
			]
        );
    }
}
