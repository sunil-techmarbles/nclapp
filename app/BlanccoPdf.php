<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BlanccoPdf extends Model
{
  	use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'blancco_pdf_file',
	    'added_date',
    ];
}
