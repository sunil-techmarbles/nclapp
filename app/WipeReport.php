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

    public static function getWipeReportsFilesData($date)
    {
        return self::select('*')
                ->where('date', '>=', $date )
                ->get();
    }

    public static function InsertWipeReportsFileData($wipePdfFilesCount, $wipeBiosFilesCount, $blanccoPdfFilesCount, $TodayDate)
    {
        $WipeReport = new WipeReport();
        $WipeReport->wipe_data_pdf_count = $wipePdfFilesCount;
        $WipeReport->bios_data_file_count = $wipeBiosFilesCount;
        $WipeReport->blancco_pdf_data_count = $blanccoPdfFilesCount;
        $WipeReport->date = $TodayDate;
        return ( $WipeReport->save() ) ? true : false ;
    }

    public static function UpdateWipeReportsFileData($wipePdfFilesCount, $wipeBiosFilesCount, $blanccoPdfFilesCount, $TodayDate)
    {
        return self::where('date', '>=', $TodayDate )
                    ->update(['wipe_data_pdf_count' => $wipePdfFilesCount,
                              'bios_data_file_count' => $wipeBiosFilesCount,
                              'blancco_pdf_data_count' => $blanccoPdfFilesCount
                            ]);
    }
}