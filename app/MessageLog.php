<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
	protected $fillable = [
	    'message',	
		'status',
		'type',
    ];
    
	public static function AddBlanccoErrorLog($message)
	{
		


	}


}
