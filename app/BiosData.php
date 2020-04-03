<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BiosData extends Model
{
   	use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'bios_data_file',
	    'added_date',
    ];

    
}
