<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyPricingCustom extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'asin',
		'price',
    ];

    public static function addNewRecorde($data)
    {
        $result = false;
        $shopifyPricingCustom = new ShopifyPricingCustom();
        $shopifyPricingCustom->price = $data->price;
        $shopifyPricingCustom->asin = $data->asin;
        if($shopifyPricingCustom->save())
        {
            $result = true;
        }

        return $result;
    }

    public static function deleteExistRecord($id)
    {
        $result = false;
        $customPriceObject = self::where(['asin' => $id])->first();
        if($customPriceObject)
        {
            $customPriceObject->delete();
            $result = true;
        }
        return $resultl
    }

    public static function getFinalPriceFromCustomPrice($runningList)
    {
        return self::where(['asin' => $runningList['asin']])
            ->pluck("price")
            ->first();
    }
}
