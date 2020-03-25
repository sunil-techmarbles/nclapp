<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IbmModel extends Model
{
	public static function getIBMManufacturerModel($serialNumber)
    {
    	if (!empty($serialNumber)) 
    	{
        	$new = array_reverse(explode(" ", $serialNumber));
	        if (isset($new[0])) 
	        {
	            $new = str_replace('-[', '', $new[0]);
	            $serialNumber = str_replace(']-', '', $new);
	        }
    	}
        $PartNumber = self::select('model')
            ->where(["part_number"=> $serialNumber])
            ->first();
        return $PartNumber->model;
    }
}
