<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormData extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'type',
	    'user',
	    'trid',
	    'product',
	    'data'
    ];

    public static function saveFormDataRecorde($data)
    {
        $result = false;
        $formData = new FormData();
        $formData->type = $data->type;
        $formData->user = $data->user;
        $formData->trid = $data->trid;
        $formData->product = $data->product;
        $formData->data = $data->data;
        if($formData->save())
        {
            $result = $formData->id;
        }
        return $result;
    }

    public static function getFormDataRecord($type, $mid)
    {
    	return self::where(["type" => $type, "trid"=> $mid])
	    	->pluck('data')
	    	->first();
    }

    public static function getFormDataRecordForTemplate($value)
    {
        return self::where(["trid"=> $value])
            ->pluck('data')
            ->first();
    }

    public static function getLastRecordByAuthUser($authUserName, $type)
    {
        return self::where(['type' => $type, 'user' => $authUserName])
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
    }

    public static function deleteFormDataRecorde($type, $authUserName )
    {
        $recorde = self::where(["type" => $type, "user" => $authUserName])->first();
        if($recorde)
        {
            if(!$recorde->isEmpty())
            {
                $recorde->delete();
            }
        }
    }

    public static function deleteFormDataRecordeByID($type, $id )
    {
        $recorde = self::where(["type" => $type, "trid" => $id])->first();
        if($recorde)
        {
            if(!$recorde->isEmpty())
            {
                $recorde->delete();
            }
        }
    }

    public static function getAllRecord($id)
    {
        return self::where(['trid' => $id])
            ->first();
    }

    public function upadateFormDataByQuery($fields, $query)
    {
        return self::where(["trid" => $tplid])
            ->update($fields);
    }
}
