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

	public static function addRecord($data)
	{
		$result = false;
        $recycleRecord = new RecycleRecord();
        $recycleRecord->name = $data->name;
        $recycleRecord->started = $data->started;
        $recycleRecord->closed = $data->closed;
        $recycleRecord->status = $data->status;
        if($recycleRecord->save())
        {
            $result = $recycleRecord->id;
        }
        
        return $result;
	}

	public static function updateRecord($query, $fields)
	{
		return self::where($query)
			->update($fields);
	}

	public static function getRecord($value)
	{
		$query = self::with('recycleRecordLines')
			->select('recycle_records.*')
			->selectSub('COUNT(rrl.id)', 'total')
    		->join('recycle_record_lines as rrl', function($join){
            	$join->on('recycle_records.id', '=', 'rrl.record_id');
            });
    	if($value)
    	{
    		$query = $query->where(['recycle_records.status' => '1']);
    	}
    	return $query->groupBy('recycle_records.id')
    		->orderBy('recycle_records.id', 'DESC')
            ->get();
	}

	public function recycleRecordLines()
	{
		return $this->hasMany('App\RecycleRecordLine', 'record_id', 'id');
	}

	public static function getRecordByName($value)
	{
		return self::where(['name' => $value])
			->pluck('id');
	}

	public static function getRecordById($request)
	{
		return self::where(['id' => intval($request->id)])
			->get();
	}
}
