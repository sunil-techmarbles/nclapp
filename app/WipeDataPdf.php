<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class WipeDataPdf extends Model
{
   	use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'wipe_data_pdf_file',
	    'added_date',
    ];

    public static function getWipeFileData($date)
    {
        pr( $date ); die;
        
    }



    
}
