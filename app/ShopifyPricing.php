<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyPricing extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'Asset_ID',
		'SerialNumber',
		'Class',
		'Brand',
		'Model',
		'Model_Number',
		'Form_Factor',
		'Processor',
		'RAM',
		'Memory_Type',
		'Memory_Speed',
		'Hard_Drive',
		'HD_Interface',
		'HD_Type',
		'Condition',
		'Price',
		'Final_Price',
    ];

    public static function addRecord($Asset_ID, $record)
    {
    	$result = false;
    	$shopifyPricing = new ShopifyPricing();
    	$shopifyPricing->Asset_ID = $Asset_ID;
		$shopifyPricing->SerialNumber = $record['SerialNumber'];
		$shopifyPricing->Class = $record['Class'];
		$shopifyPricing->Brand = $record['Brand'];
		$shopifyPricing->Model = $record['Model'];
		$shopifyPricing->Model_Number = $record['Model_Number'];
		$shopifyPricing->Form_Factor = $record['Form_Factor'];
		$shopifyPricing->Processor = $record['Processor'];
		$shopifyPricing->RAM = $record['RAM'];
		$shopifyPricing->Memory_Type = $record['Memory_Type'];
		$shopifyPricing->Memory_Speed = $record['Memory_Speed'];
		$shopifyPricing->Hard_Drive = $record['Hard_Drive'];
		$shopifyPricing->HD_Interface = $record['HD_Interface'];
		$shopifyPricing->HD_Type = $record['HD_Type'];
		$shopifyPricing->Condition = $record['Condition'];
		$shopifyPricing->Price = $record['Price'];
		$shopifyPricing->Final_Price = $record['Final_Price'];
		if($shopifyPricing->save())
		{
			$result = $shopifyPricing->id;
		}
		return $result;
    }

    public static function upadateRecord($Asset_ID, $record)
    {
    	return self::where(['Asset_ID' => $Asset_ID])
    		->update($record);
    }

    public static function getShopifyPriceList($request)
    {
    	return self::where([
	    		'Condition' => $request['condition'],
	    		'Form_Factor' => $request['form_factor'],
	    		'Model' => $request['model']
    		])
    		->where('Processor', 'LIKE', '%'.$request['cpu_core'].'%')
    		->get();
    }

    public static function updateShopifyPriceFinalPrice($request, $finalPrice)
    {
    	return self::where([
	    		'Condition' => $request['condition'],
	    		'Form_Factor' => $request['form_factor'],
	    		'Model' => $request['model']
    		])
    		->where('Processor', 'LIKE', '%'.$request['cpu_core'].'%')
    		->update(['Final_Price' => $finalPrice]);
    }

    public static function getRecordForImport($request)
    {
    	return self::select('*')
    		->where([
    			'Form_Factor' => $request->form_factor,
    			'Condition' => $request->condition,
    			'Model' => $request->model,
			])
    		->where('Processor', 'LIKE' ,'%'.$request->processor.'%')
            ->get();
    }

    public static function getRecordByAssetId($fileData)
    {
    	return self::where(['Asset_ID' => $fileData])
    		->get();
    }
}
