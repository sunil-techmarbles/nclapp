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
        return self::select('*')
                ->where('added_date', '>=', $date )
                ->get();
    }

    public static function InsertWipeFileData($fileName, $date)
    {
        $WipeDataPdf = new WipeDataPdf();
        $WipeDataPdf->wipe_data_pdf_file = $fileName;
        $WipeDataPdf->added_date = $date;
        return ( $WipeDataPdf->save() ) ? true : false ;
    }
}
