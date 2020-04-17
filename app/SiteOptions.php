<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteOptions extends Model
{
   use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'option_name',
        'option_value',
        'option_group',
    ];

    public static function GetRecycleAddressOptions($optionGroup)
    {
    	return self::where(['option_group' => $optionGroup])
			->get();
    }
    
    public static function UpdateRecycleAddressOptions($request)
    {
    	$shipFromAddress = self::CheckSettingExists('RecycleShipFromAddress' , 'RecycleBolSettings');
		if( !empty( $shipFromAddress ))
		{
			self::UpdateSingleSettings('RecycleShipFromAddress' , $request->shipFromAddress, 'RecycleBolSettings');
		}
		else
		{
			self::AddSingleSettings('RecycleShipFromAddress' , $request->shipFromAddress, 'RecycleBolSettings');
		}

		$shipToAddress = self::CheckSettingExists('RecycleShipToAddress' , 'RecycleBolSettings');
		if( !empty( $shipToAddress ))
		{
			self::UpdateSingleSettings('RecycleShipToAddress' , $request->shipToAddress, 'RecycleBolSettings');
		}
		else
		{
			self::AddSingleSettings('RecycleShipToAddress' , $request->shipToAddress, 'RecycleBolSettings');
		}

		$shipFromContact = self::CheckSettingExists('RecycleShipFromContact' , 'RecycleBolSettings');
		if( !empty( $shipFromContact ))
		{
			self::UpdateSingleSettings('RecycleShipFromContact' , $request->shipFromContact, 'RecycleBolSettings');
		}
		else
		{
			self::AddSingleSettings('RecycleShipFromContact' , $request->shipFromContact, 'RecycleBolSettings');
		}

		$shipToContact = self::CheckSettingExists('RecycleShipToContact' , 'RecycleBolSettings');
		if( !empty( $shipToContact ))
		{
			self::UpdateSingleSettings('RecycleShipToContact' , $request->shipToContact, 'RecycleBolSettings');
		}
		else
		{
			self::AddSingleSettings('RecycleShipToContact' , $request->shipToContact, 'RecycleBolSettings');
		}
    }

    public static function CheckSettingExists($optionName, $optionGroup)
    {
    	return self::where(['option_name' => $optionName, 'option_group' => $optionGroup])
			->get()->first();
    }

    public static function UpdateSingleSettings($optionName, $optionValue, $optionGroup)
    {
    	return self::where(['option_name' => $optionName,'option_group' => $optionGroup])
    		->update(['option_value' => $optionValue]);
    }

    public static function AddSingleSettings($optionName, $optionValue, $optionGroup)
    {
    	$SiteOptions = new SiteOptions();
    	$SiteOptions->option_name = $optionName;
		$SiteOptions->option_value = $optionValue;
		$SiteOptions->option_group = $optionGroup;
		return ($SiteOptions->save()) ? true : false;
    }
}