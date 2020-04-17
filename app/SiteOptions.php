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
    

    public static function UpdateRecycleAddressOptions($request)
    {
    	dd( $request ); die; 

    }


    public static function CheckSettingExists($optionName, $optionGroup)
    {


    }

    public static function UpdateSingleSettings($optionName, $optionValue, $optionGroup)
    {


    }

     public static function AddSingleSettings($optionName, $optionValue, $optionGroup)
    {


    }


}
