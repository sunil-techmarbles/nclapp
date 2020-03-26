<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IbmModel extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'model',
        'part_number',
    ];
    
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
        return (!empty($PartNumber->model)) ? $PartNumber->model : '';
    }
}