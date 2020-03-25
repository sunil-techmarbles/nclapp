<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecycleRecordLine extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable = [
		'record_id',
		'pallet',
		'category',
		'lgross',
		'ltare',
		'price',
		'total_price',
		'pgi'
	];

	public static function addRecord($data)
	{
		$result = false;
        $recycleRecordLine = new RecycleRecordLine();
        $recycleRecordLine->record_id = $data->record_id;
        $recycleRecordLine->category = $data->category;
        $recycleRecordLine->lgross = $data->lgross;
        $recycleRecordLine->ltare = $data->ltare;
        $recycleRecordLine->price = $data->price;
        $recycleRecordLine->pgi = $data->pgi;
        $recycleRecordLine->total_price = $data->total_price;
        if($recycleRecordLine->save())
        {
            $result = $recycleRecordLine->id;
        }
        
        return $result;
	}

	public static function getAllRecycleRecordLineByRecordId($value)
	{
		return self::where([ 'record_id' => $value])
			->orderBy('pallet', 'ASC')
			->get();
	}

	public static function updateRecord($query, $fields)
	{
		return self::where($query)
			->update($fields);
	}

	public static function deleteRecord($value)
	{
		$recycleRecordLine = self::find($value);
		if($recycleRecordLine)
		{
			$recycleRecordLine ->delete();
			return true;
		}
		return false;
	}

	public static function getRecordGroupByCat($recordId)
	{
		return self::select('category')
			->selectSub('SUM(lgross)', 'total_lbs_gross')
			->selectSub('SUM(ltare)', 'total_lbs_tare')
			->selectSub('SUM(total_price)', 'total_price')
			->where(['record_id' => $recordId])
			->groupBy('category')
			->get();
	}
}
