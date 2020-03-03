<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class Session extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	    'name',	
		'started_on',
		'updated_on',
		'status',	
    ];

    public static function addSessionRecord($request, $current)
    {
    	$result = false;
    	$session = new Session();
    	$session->started_on = $current;
    	$session->name = $request->session_name;
    	if($session->save())
    	{
    		$result = $session->id;
    	}
    	return $result;
    }

    public static function getOpenStatucRecord($request, $status)
    {
    	return self::where(['status'=> $status])
    		->pluck('id');
    }

    public static function getSessionRecord($request)
    {	
    	return self::select('id','name','started_on','updated_on','status')
			->orderBy('status', 'DESC')
			->orderBy('started_on', 'DESC')
			->get();
    }

    public static function updateSessionRecord($status, $current)
    {
    	return self::where(['status' => 'open'])
    		->update(['updated_on' => $current,
    			'status'=> $status
    		]);
    }

    public static function getCurrentSessionName($currentSession)
    {
    	return self::where(["id" => $currentSession])
    		->pluck('name')
    		->first();
    }

}
