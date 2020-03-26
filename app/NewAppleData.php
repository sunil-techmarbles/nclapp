<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewAppleData extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'Case_Type',
        'Model',
        'Model#',
        'EMC',
        'Family',
        'Processor_Qty',
        'Processor_Manufacturer',
        'Processor_Type',
        'Processor_Model',
        'Processor_Core',
        'Processor_Speed',
        'Processor_Generation',
        'Processor_Socket',
        'Processor_Codename',
        'Standard_Optical',
        'Video_Card',
        'RAM_Slots',
        'Built_in_Display',
        'Native_Resolution',
        'Storage_Dimensions',
        'Column_21',
    ];

	public static function getMakorAppleManufacturerModel($model, $processorModel)
	{
		$query = self::select('*');
        $query->where('Model', 'like', '%' .$model. '%');
        if( $query->count() ==  1 )
        {
        	return $query->get()->first();
        }
        else
        {
        	foreach ($query->get() as $key => $modelData)
        	{
        		if(strtolower($processorModel) == strtolower($modelData['Processor_Model']))
        		{
        			$data = $modelData;
                	break;
        		}
        		else
        		{
        			$data = "DUPLICATES";
        		}
        	}
        	return $data;
        }
    	return false;
    }
}