<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class ListData extends Model
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
	 	'mid',
		'model',
		'technology',
		'asin',
		'asset',
		'grade',
		'cpu',
		'cpu_core',
		'cpu_model',
		'cpu_gen',
		'cpu_speed',
		'shopify_product_id',
		'status',
		'run_status',
	   	'added_on',
		'added_by',
	]

	public static function addListDataRecord($data)
    {
    	$result = false;
    	$listData = new ListData();
    	$listData->sid = $data->sid;
		$listData->mid = $data->mid;
		$listData->asset = $data->asset;
		$listData->added_by = $data->added_by;
		$listData->added_on = $data->added_on;
		$listData->model = $data->model;
		$listData->technology = $data->technology;
		$listData->asin = $data->asin;
		$listData->grade = $data->grade;
		$listData->cpu = $data->cpu;
		$listData->cpu_core = $data->cpu_core;
		$listData->cpu_model = $data->cpu_model;
		$listData->cpu_gen = $data->cpu_gen;
		$listData->cpu_speed = $data->cpu_speed;
		$listData->shopify_product_id = $data->shopify_product_id;
		$listData->status = $data->status;
		$listData->run_status = $data->run_status;
		if($listData->save())
		{
    		$result = $listData->id;
		}
		return $result;
    }

    public static function deleteListDataRecorde($mid, $text1 )
    {
        $recorde = self::where(["mid" => $mid, "asset" => $text1])->first();
        if($recorde->isEmpty())
        {
            $recorde->delete();
        }
    }
}
