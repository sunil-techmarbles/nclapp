<?php
namespace App\Imports;

use App\ListData;
use App\ShopifyPricing;
use App\SessionData;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ShopifyProductImport implements ToCollection,  WithStartRow
{
    public $data;
    public function  __construct()
	{
	}

    public function collection(Collection $data)
    {
    	$data = $data->toArray();
    	$headers = $data[0];
	    unset($data[0]);
    	$csvRows = [];
	    foreach ($data as $row)
	    {
	        $csvRows[] = array_combine($headers, $row);
	    }
	    $csvRows = array_filter($csvRows);
    	foreach ($csvRows as $key => $fileData)
        {
        	$checkData = ShopifyPricing::getRecordByAssetId($fileData['Asset ID']);
            // print_r($checkData);
            $finalPrice = 0.00;
            $excludeTextsFromModel = array(
            	' non-vPro ',
            	' DT ',
            	' CMT ',
            	' SFF ',
            	' USDT ',
            	' sff ',
            	' DM ',
            	' TWR ',
            	' MT '
            );

            $Model = $fileData['Model'];
            foreach ($excludeTextsFromModel as $excludeTextFromModel)
            {
                if (strpos($Model, $excludeTextFromModel) !== FALSE)
                {
                    $Model = str_replace($excludeTextFromModel, '', $Model);
                }
            }
            ListData::updateRunStatus($asset = $fileData['Asset ID'], $status = "removed");
            SessionData::updateSessionRunStatus($asset = $fileData['Asset ID'], $status = "removed");
        	$record = [
        		'SerialNumber' => $fileData['SerialNumber'],
        		'Class' => $fileData['Class'],
        		'Brand' => $fileData['Brand'],
        		'Model' => $Model,
        		'Model_Number' => $fileData['Model #'],
        		'Form_Factor' => $fileData['Form_Factor'],
        		'Processor' => $fileData['Processor'],
        		'RAM' => $fileData['RAM'],
        		'Memory_Type' => $fileData['Memory_Type'],
        		'Memory_Speed' => $fileData['Memory_Speed'],
        		'Hard_Drive' => $fileData['Hard_Drive'],
        		'HD_Interface' => $fileData['HD_Interface'],
        		'HD_Type' => $fileData['HD_Type'],
        		'Condition' => $fileData['Condition'],
        		'Price' => $fileData['PRICE'],
        		'Final_Price' => $finalPrice,
        	];
            if ($checkData->count() > 0)
            {
            	$record['Model'] = $fileData['Model'];
                // echo "if".$fileData['Asset ID']."\n";
            	ShopifyPricing::upadateRecord($fileData['Asset ID'], $record);
            }
            else
            {
                // echo "else".$fileData['Asset ID']."\n";
            	ShopifyPricing::addRecord($fileData['Asset ID'], $record);
            }
        }
        // die;
    }

    /**
     * @return int
    */
    public function startRow(): int
    {
        return 1;
    } 
}
