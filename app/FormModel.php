<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormModel extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'tab',
	    'technology',
	    'model',
	    'asin_model',
    ];

    public static function getFormModelRecord($fields, $request, $isType)
    {
    	$query = self::select($fields);
        if ($isType == 'true')
        {
    		$query->where(["tab"=> $request->get("tab"), "technology"=> $request->get("tech")]);
        }
        if ($isType == 'true' || $isType == 'false')
        {
    		$query->where('model', 'LIKE', '%' .$request->get("part"). '%');
        }
    	return $query->get();
    }
    
}
