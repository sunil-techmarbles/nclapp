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
	
	public static function addLogMessageRecord($message, $type, $status)
	{
        $messageLog = new MessageLog();
        $messageLog->message = $message;
        $messageLog->status = $status;
        $messageLog->type = $type;
        return ($messageLog->save()) ? true : false;
	}
}