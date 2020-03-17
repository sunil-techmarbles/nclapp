<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LenovoModelData extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'model',
        'part_number',
    ];

    public static function InsertNewPartNumber($model, $partNumber)
    {
        $result = false;
        $LenovoModelData = new LenovoModelData();
        $LenovoModelData->model = $model;
        $LenovoModelData->part_number = $partNumber;
        if($LenovoModelData->save())
        {
            $result = $LenovoModelData->id;
        }
        return $result;
    }

    public static function CheckIfPartNumberExists($partNumber)
    {
        $exits = self::where([ 'part_number' => $partNumber ])->first();
        return ( $exits  ) ? true : false ;
    }
}