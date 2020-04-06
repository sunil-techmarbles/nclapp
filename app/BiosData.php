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

    public static function getWipeBiosFileData($date)
    {
        return self::select('*')
                ->where('added_date', '>=', $date )
                ->get();
    }

    public static function InsertWipeBiosFileData($fileName, $date)
    {
        $BiosData = new BiosData();
        $BiosData->bios_data_file = $fileName;
        $BiosData->added_date = $date;
        return ( $BiosData->save() ) ? true : false ;
    }
}
