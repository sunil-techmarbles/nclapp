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
}
