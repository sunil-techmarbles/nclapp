<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecycleRecord extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable = [
		'name',
		'file_path',
		'status',
		'items',
		'started',
		'closed',
	];

	public static function getJoinRecod($value='')
	{
		return self::select('recycle_records.*')
			->selectSub('COUNT(rrl.id)', 'total')
    		->join('recycle_record_lines as rrl', function($join){
                $join->on('recycle_records.id', '=', 'rrl.record_id');
                })
    		->groupBy('recycle_records.id')
    		->orderBy('recycle_records.id', 'DESC')
            ->get();
	}
}
