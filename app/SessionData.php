<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class SessionData extends Model
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
		'run_status',
		'added_by',
		'added_on',
		'status',	
    ];

    public static function addSessionDataRecord($data, $current)
    {
    	$result = false;
    	$sessionData = new SessionData();
    	$sessionData->sid = $data->sid;
		$sessionData->aid = $data->aid;
		$sessionData->asset = $data->asset;
		$sessionData->added_by = $data->added_by;
		$sessionData->added_on = $data->added_on;
		if($sessionData->save())
		{
    		$result = $sessionData->id;
		}
		return $result;

    }

    public static function hasAssests($value)
    {
    	return self::where(["asset"=>$asset])->get();
    }

    public static function getSessionDataCount($id)
    {
		return self::where(['sid' => $id, 'status' => 'active'])
			->count();
    }

    public static function sessionSummary($currentSession)
    {
    	return self::select('session_data.aid', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
			->selectSub('count(session_data.aid)', 'cnt')
    		->join('asins as a', function($join) use($currentSession){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','=', $currentSession)
                        ->where('session_data.status','=', 'active');
                })
    		->groupBy('session_data.aid')
            ->get();
    }

    public static function sessionItems($currentSession)
    {
    	return self::select('session_data.aid', 'session_data.asset', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link', 'session_data.added_on')
			->selectSub('count(session_data.aid)', 'cnt')
    		->join('asins as a', function($join) use($currentSession){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','=', $currentSession)
                        ->where('session_data.status','=', 'active');
                })
    		->orderBy('session_data.aid', 'DESC')
    		->orderBy('session_data.asset', 'DESC')
            ->get();
    }

    public static function sessionParts($currentSession)
    {
    	return [];
    	return self::select('i.id', 'i.part_num', 'i.item_name', 'i.qty', 'i.vendor', 'i.dlv_time', 'i.low_stock', 'i.reorder_qty', 'i.email_tpl', 'i.email_subj')
            ->selectSub('sum(p.qty)', 'required_qty')
            ->selectSub('sum(p.qty) - i.qty', 'missing')
            ->join('supplies as i', function($join) use($currentSession){
                $join->on('d.aid', '=', 'i.id');
            })
            ->join('supplie_asin_models as p', function($join) use($currentSession){
                $join->on('i.id', '=', 'p.supplie_id');
            })
            ->join('session_data as d', function($join) use($currentSession){
                $join->on('d.aid', '=', 'p.asin_model_id')
                    ->where('d.sid','=', $currentSession)
                    ->where('d.status','=', 'active');
            })
            ->groupBy('i.id')
            ->get();
    }
}
