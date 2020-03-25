<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recycle extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable = [
		'Type_of_Scrap',
		'PRICE',
		'TYPE',
		'status'
	];
	public static function addRecord($data)
	{
		$result = false;
        $recycle = new Recycle();
        $recycle->Type_of_Scrap = $data->Type_of_Scrap;
        $recycle->status = $data->status;
        if($recycle->save())
        {
            $result = $recycle->id;
        }
        
        return $result;
	}
	public static function getAllRecord($field, $query)
	{
		return self::where($query)
			->pluck($field);
	}

	public static function getAllRecordOrderBy($query)
	{
		return self::orderBy($query['field'], $query['order'])
			->get();
	}

	public static function getTypeOfScrap($request)
	{
		return self::where(['Type_of_Scrap' => $request->category])
			->get();
	}

	public static function getAllTypeOfScrap($query)
	{
		return self::where($query)
			->pluck('Type_of_Scrap');
	}

	public static function deleteRecord($value)
	{
		$recycle = self::where(['Type_of_Scrap' => $value]);
		if($recycle)
		{
			$recycle ->delete();
			return true;
		}
		return false;
	}

	public static function approveCategoryRecord($value)
	{
		return self::where(['Type_of_Scrap' => $value])
			->update(['status' => '0']);
	}

	public static function updateRecord($data, $query)
	{
		return self::where($query)
			->update($data);
	}
}
