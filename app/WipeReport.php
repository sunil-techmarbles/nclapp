<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WipeReport extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'wipe_data_pdf_count',
        'bios_data_file_count',
        'blancco_pdf_data_count',
        'date',
    ];

    public static function getWipeReportsFilesCountData( $dateFrom, $dateTo )
    {
		return self::whereBetween( 'date', [$dateFrom, $dateTo] )
					->get() ;
    }
}