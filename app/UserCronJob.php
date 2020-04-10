<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCronJob extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
		'cron_job',
		'status',
    ];

    public static function addRecord($data)
    {
    	$userCronJob = new UserCronJob();
    	$userCronJob->user_id = $data['user_id'];
		$userCronJob->cron_job = $data['cron_job'];
		$userCronJob->status = $data['status'];
		return ($userCronJob->save()) ? true : false;
    }

    public static function getCronJobName($value)
    {
    	return self::where('user_id', $value)->pluck('cron_job');
    }

    public static function deleteRecord($value)
    {
    	$getCronId = self::where(['user_id' => $value])->pluck('id');
    	if($getCronId)
    	{
    		self::whereIn('user_id', $getCronId)->delete();
    	}
    }

    public function usersList()
    {
		return $this->belongsTo('App\User');
	}
}
