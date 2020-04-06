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

    public static function getBlanccoFileData($date)
    {
        return self::select('*')
                ->where('added_date', '>=', $date )
                ->get();
    }

    public static function InsertBlanccoFileData($fileName, $date)
    {
        $BlanccoPdf = new BlanccoPdf();
        $BlanccoPdf->blancco_pdf_file = $fileName;
        $BlanccoPdf->added_date = $date;
        return ( $BlanccoPdf->save() ) ? true : false ;
    }
}
