<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class AsinAsset extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'sid',
	    'aid',
	    'asset',
	    'added_by',
	    'status',
	    'run_status',
	    'added_on',
    ];

    public static function storeRecord($request, $current)
    {
        $result = false;
        $asinAsset = new AsinAsset();
        $asinAsset->sid = $request->sid ;
        $asinAsset->aid = $request->aid;
        $asinAsset->asset = $request->asset;
        $asinAsset->added_by = $request->added_by;
        $asinAsset->run_status = $request->run_status;
        $asinAsset->added_on = $current;   
        if($asinAsset->save())
        {
            $result = $asinAsset->id;
        }
        
        return $result;
    }

   	public static function updateRecord($request)
    {
        return self::where(["asset" => $request['asset']])->update(['aid' => $request['aid']]);
    }

    public static function updateRecordRunStatus($ids, $text )
    {
        $rowCount = self::WhereIn("asset" ,$ids)->count();
        $output =  self::whereIn("asset" ,$ids)->update(['run_status' => $text]);
        if($output)
        {
            return ['count' => $rowCount, 'output' => $output];
        }
        else
        {
            return ['count' => 0, 'output' => 0];
        }
    }
}
