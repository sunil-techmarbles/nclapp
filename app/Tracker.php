<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tracker extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user',
		'activity',
		'start',
		'duration',
    ];

    public static function addRecord($request)
    {		
    	$result = false;
    	$tracker = new Tracker();
    	$tracker->user = $request->user ;
    	$tracker->activity = $request->activity;
    	$tracker->start = $request->start;
    	$tracker->duration = $request->duration;
		if($tracker->save())
		{
			$result = $tracker->id;
		}
    	
    	return $result;
    }

    public static function getSearchFilterResult($request)
    {
        $query = self::select('*');
        $dateTo = date("Y-m-d")." 23:59:59";
        $dateFrom = date("Y-m-d")." 00:00:00";
        if($request->has('dates'))
        {
            $dates = $request->get('dates');
            $adates = explode(" - ",$dates);
            $dateFrom = date("Y-m-d",strtotime($adates[0]))." 00:00:00";
            $dateTo = date("Y-m-d",strtotime($adates[1]))." 23:59:59";
        }
        $query = $query->whereBetween('start', [$dateFrom, $dateTo]);
        if($request->has("user"))
        {
            $user = $request->get("user");
            $query = $query->where(['user' => $user]);
        }

        if($request->has("activity"))
        {
            $act = $request->get("activity");
            $query = $query->where('activity','LIKE', '%'.$act.'%');
        }
        $query = $query->where('activity','!=', "");
        $query = $query->orderBy('start');
        return $query->get();
    }

    public static function getUserRecord($value='')
    {
        return self::groupBy('user')
            ->pluck('user');
    }

    public static function getActivityRecord($value='')
    {
        return self::where('activity', '!=', '')
            ->groupBy('activity')
            ->pluck('activity');
    }
}
