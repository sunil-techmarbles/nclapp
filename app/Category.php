<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'category_name',
	    'value',
    ];

    public static function addRecord($data)
    {
        $result = false;
        $category = new Category();
        $category->category_name = $data->categoryname;
        $category->value = $data->categoryvalue;
        if($category->save())
        {
            $result = $category->id;
        }
        return $result;
    }

    public static function getAllRecord()
    {
    	return self::get();
    }

    public static function getCategoryName($value)
    {
        return self::where(['value' => $value])
            ->pluck('category_name')
            ->first();
    }

    public static function getRecordById($value)
    {
        return self::where(['id' => $value])
            ->first();
    }

    public static function updateRecord($data)
    {
        return self::where(['id' => intval($data->catId)])
            ->update([
                'category_name' => $data->categoryname,
                'value' => $data->categoryvalue,
            ]);
    }

    public static function deleteRecycleTwoCategory($recordId)
    {
        $output = false;
        $result = self::find($recordId);
        if($result)
        {
            $result->delete();
            $output = true;
        }
        return $output;
    }
}
