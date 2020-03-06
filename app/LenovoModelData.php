<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;


class LenovoModelData extends Model
{
	
	  use SoftDeletes;
	  protected $dates = ['deleted_at'];

	  protected $fillable = [
	    'model',
	    'part_number',
    ];

   	public static function InsertNewPartNumber($modal, $partNumber)
   	{
   		  $result = false; 
        $LenovoModelData = new LenovoModelData();
        $LenovoModelData->model = $modal;   
        $LenovoModelData->part_number = $partNumber;
        if($LenovoModelData->save())
        {
            $result = $LenovoModelData->id;
        }  
        return $result; 
   	}

    public static function CheckIfPartNumberExists($partNumber)
    {
      $ifExist = false;
      $exits = self::where([ 'part_number' => $partNumber ])->first(); 
      if( $exits )
      {  
         $ifExist = true;
      }     
      return $ifExist;  
    }


}
