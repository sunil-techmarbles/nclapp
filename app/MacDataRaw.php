<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MacDataRaw extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable = [
        'Manufacturer',
        'Apple_Model_Combined',
        'Apple_Order_No',
        'EMC',
        'Processor_Quantity',
        'Processor_Manufacturer',
        'Processor_Type',
        'Processor_Model',
        'Processor_Core',
        'Processor_Speed',
        'Processor_Generation',
        'RAM_Type',
        'RAM_Speed',
        'MaximumRAM',
        'Motherboard_RAM',
        'RAM_Slots',
        'Video_Card',
        'Built_in_Display',
        'Native_Resolution',
        'Storage_Dimensions',
        'Storage_Interface',
        'Standard_Optical',
        'Dimensions',
        'Avg_Weight',
    ];

    public static function getRecord($value)
    {
        return self::where('Apple_Model_Combined', 'LIKE', '%'.$value.'%')
            ->first();    
    }

    public static function getMakorAppleDataFromTable($searchData)
    {
    	$query = self::select('*'); 
    	$query->where('Apple_Model_Combined', 'like', '%' .$searchData['ComputerModel']. '%');

    	if ( $query->count() == 1 ) {
    		return $query->get()->first();
    	}

    	$wipeProcessorSpeed = '';
	    if (isset($searchData['Processors']['Processor'][0]))
	    {
	        $wipeProcessorName = $searchData['Processors']['Processor'][0]['Name'];
	        $wipeProcessorSpeed = $searchData['Processors']['Processor'][0]['Speed'];
	        $wipeProcessorSpeed = MHzToGHz($wipeProcessorSpeed);
	    }
	    elseif (isset($searchData['Processors']['Processor']['Speed']))
	    {
	        $wipeProcessorName = $searchData['Processors']['Processor']['Name'];
	        $wipeProcessorSpeed = $searchData['Processors']['Processor']['Speed'];
	        $wipeProcessorSpeed = MHzToGHz($wipeProcessorSpeed);
	    }

	    //match based on Processor Speed
    	foreach ( $query->get() as $key => $result )
    	{
    		if (!empty($result['Processor_Speed']))
    		{
    			$dbProcessorSpeed = MHzToGHz($result['Processor_Speed']);
	            if (floatval($dbProcessorSpeed) == floatval($wipeProcessorSpeed))
	            {
	                return $result;
	                break;
	            }
    		}
    	}

        //match based on Processor Speed
        //remove extra spsaces
        $wipeProcessorName = preg_replace('/\s+/', ' ', $wipeProcessorName);
        if (strpos($wipeProcessorName, "-") === FALSE)
        {
            $wipeProcessorName = str_ireplace(" CPU ", "-", $wipeProcessorName);
        }

        foreach ( $query->get() as $key => $result)
        {
            if (!empty($result['Processor_Model']))
            {
                $dbProcessorModal = trim($result['Processor_Model']);
                if ( stripos($wipeProcessorName, $dbProcessorModal) !== FALSE )
                {
                   return $result;
                   break;
                }
            }
        }
    	
    	return array();
    }
}