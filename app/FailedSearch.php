<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailedSearch extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
		'model_or_part',
		'partNo',
		'Brand',
		'Category',
		'require_pn',
		'on_datetime',
		'is_active',
    ];

    public static function addRecord($data)
    {
    	$failedSearch = new FailedSearch();
    	$failedSearch->model_or_part = $data['model_or_part'];
		$failedSearch->partNo = $data['partNo'];
		$failedSearch->Brand = $data['Brand'];
		$failedSearch->Category = $data['Category'];
		$failedSearch->on_datetime = $data['on_datetime'];
		return ($failedSearch->save()) ? true : false;
    }

    public static function getAllRecord($value='')
    {
    	return self::get();
    }

    public static function getRecordForEdit($id)
    {
    	return self::where(['id' => $id])
    		->first();
    }

    public static function deleteRecord($id)
    {
    	$status = false;
    	$failedSearch = self::find($id);
    	if($failedSearch)
    	{
    		$failedSearch->delete();
    		$status = true;
    	}
    	return $status;
    }

    public static function getRecordByDate($date)
    {
        return self::select('*')
            ->where('on_datetime', '>=',  $date )
            ->get();
    }
}
