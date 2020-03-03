<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoaReport extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'asset',
	    'sn',
	    'old_coa',
	    'new_coa',
	    'status',
	    'added_on',
    ];

    public static function getRecordByID($id)
    {
        return self::where(["asset" => $id])->first();
    }

    public static function addRecord($asset, $sn, $request, $current)
    {
        $result = false;
        $coaReport = new CoaReport();
        $coaReport->sn = $sn ;
        $coaReport->old_coa = $request->old_coa;
        $coaReport->asset = $asset;
        $coaReport->new_coa = $request->new_coa;
        $asinAsset->added_on = $current;   
        if($coaReport->save())
        {
            $result = $coaReport->id;
        }
        
        return $result;
    }

    public static function updateRecord($asset, $sn, $request)
    {
    	return self::where(["asset" => $asset])->update([
    		"sn" => $sn,
    		"old_coa" => $request->old_coa,
    		"new_coa" => $request->new_coa
    	]);
    }


    public static function getCoaReportFields() 
    { 
        return self::select('asset','sn','old_coa','new_coa','added_on')->get();
    }

}
