<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyImages extends Model
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
        'image',
		'shopify_image_id',
    ];

    public static function addimagerecord($data)
    {
        $ShopifyImages = new ShopifyImages();
        $shopifyImages->asin = $data->asin;
        $shopifyImages->image = $data->image;
        $shopifyImages->shopify_image_id = $data->shopify_image_id;
        return ($shopifyImages->save()) ? true : false;
    }

    public static function getImageId($asin,$singleImage)
    {
        return self::where(['asin' => $asin, 'image' => $singleImage])
            ->where('shopify_image_id', '!=', 0)
            ->pluck('shopify_image_id')
            ->first();
    }
}
