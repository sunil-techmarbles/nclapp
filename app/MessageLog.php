<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageLog extends Model
{
   	use SoftDeletes;
	protected $dates = ['deleted_at'];
	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	 	'message',
	 	'status',
		'type',
	];

	public static function AddBlanccoErrorLog($message)
	{
        $MessageLog = new MessageLog();
        $MessageLog->message = $message;
        $MessageLog->status = 'error';
        $MessageLog->type = 'blancco';
        return ($MessageLog->save()) ? true : false;
	}

	public static function AddBlanccoSuccessLog($message)
	{
        $MessageLog = new MessageLog();
        $MessageLog->message = $message;
        $MessageLog->status = 'success';
        $MessageLog->type = 'blancco';
        return ($MessageLog->save()) ? true : false;
	}
}