<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyBarCode extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'upc',
		'asin',
    ];

    public static function countEmptyAsinId($value)
    {
        return self::selectSub('COUNT(id)', 'count')
            ->where(['asin' => $value])
            ->get();
    }

    public static function getUPS($value, $orderBy)
    {
        $query = self::where(['asin' => $value]);
        if($orderBy != '')
        {
            $query->orderBy($orderBy);
        }
        return $query->pluck('upc')
            ->first();
    }

    public static function updateQueryFields($query, $fields)
    {
        return self::where($fields)
            ->update($query);
    }
}
