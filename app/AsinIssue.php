<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class AsinIssue extends Model
{
   	use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'sn',
	    'issue',
	    'asset',
	    'status',
	    'added_on',
    ];

    public static function storeRecord($request, $current)
    {
        $result = false;
        $asinIssue = new AsinIssue();
        $asinIssue->sn = $request->sn ;
        $asinIssue->issue = $request->issue;
        $asinIssue->asset = $request->asset;
        $asinIssue->added_on = $current;   
        if($asinIssue->save())
        {
            $result = $asinIssue->id;
        }
        
        return $result;
    }
}
