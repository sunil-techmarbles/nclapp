<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 


class ReportEmail extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'email',
	    'type',
    ];

    public static function addRecord($email, $type)
    {
    	$result = false;
    	$reportEmail = new ReportEmail();
    	$reportEmail->email = $email;
    	$reportEmail->type = $type;
    	if($reportEmail->save())
    	{
    		$result = $reportEmail->id;
    	}
    	return $result;
    }

    public static function getAllRecord()
    {
    	return self::get();
    }

    public static function getRecordForEdit($value)
    {
    	return self::where(['type' => $value])
    		->pluck('email');
    }

    public static function deleteRecordByType($email, $type)
    {
    	$result = self::where(['type' => $type])->pluck('id');
    	if($result)
    	{
    		self::whereIn('id',$result)->delete();
    	}
    }
}