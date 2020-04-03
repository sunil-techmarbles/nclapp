<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormModel extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'tab',
	    'technology',
	    'model',
	    'asin_model',
    ];

    public static function saveFormRecord($data)
    {
        $result = false;
        $formModel = new FormModel();
        $formModel->tab = $data->tab;
        $formModel->technology = $data->technology;
        $formModel->model = $data->model;
        if($formModel->save())
        {
            $result = $formModel->id;
        }
        return $result;
    }

    public static function getFormModelRecord($fields, $request, $isType)
    {
    	$query = self::select($fields);
        if ($isType == 'true')
        {
    		$query->where(["tab"=> $request->get("tab"), "technology"=> $request->get("tech")]);
        }
        if ($isType == 'true' || $isType == 'false')
        {
    		$query->where('model', 'LIKE', '%' .$request->get("part"). '%');
        }
    	return $query->get();
    }

    public static function getAsinModelRecord($mid)
    {
        return self::where(["id" => $mid])
            ->pluck("asin_model")
            ->first();
    }

    public static function getAsinModel($id)
    {
        return self::select('id','tab')
            ->where('asin_model','LIKE', '%'. $id .'%')
            ->get();
    }

    public static function getFormModelTab($value)
    {
        return self::where(["id" => $value])
            ->pluck("tab")
            ->first();
    }

     public static function getFormModelByID($value)
    {
        return self::where(["id" => $value])
            ->pluck("model")
            ->first();
    }

    public static function getFormAllRecordExist($product, $technology, $model)
    {
        return self::where(["tab" => $product, "technology" => $technology, "model" => $model])
            ->first();
    }

    public static function pluckCustomField($query, $field)
    {
        return self::where($query)
            ->pluck($field);
    }
}
