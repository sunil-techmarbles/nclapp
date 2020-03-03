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
}
