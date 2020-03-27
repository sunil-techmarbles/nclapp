<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItamgRecycleInventory extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
		'Brand',
		'Model',
		'PartNo',
		'Category',
		'Notes',
		'Value',
		'Status',
		'require_pn',
    ];

    public static function addRecord($data)
    {
        $itamgRecycleInventory = new ItamgRecycleInventory();
        $itamgRecycleInventory->Model = $data['Model'];
        $itamgRecycleInventory->PartNo = $data['PartNo'];
        $itamgRecycleInventory->Brand = $data['Brand'];
        $itamgRecycleInventory->Notes = $data['Notes'];
        $itamgRecycleInventory->Value = $data['Value'];
        $itamgRecycleInventory->Status = $data['Status'];
        $itamgRecycleInventory->require_pn = $data['require_pn'];
        return ($itamgRecycleInventory->save()) ? true : false;
    }

    public static function getAllRecord($value='')
    {
    	return self::get();
    }

    public static function getResult($query, $fields)
    {
    	return self::select($fields)
    		->where($query)
    		->get();
    }

    public static function getStatusByModelAndPartNumber($request, $fields)
    {
    	return self::select($fields)
    		->where(['Model' => $request->search])
    		->orWhere(['PartNo' => $request->search])
    		->get();
    }

    public static function deleteRecycleTwo($recordId)
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
