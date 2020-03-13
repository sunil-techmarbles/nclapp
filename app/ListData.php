<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class ListData extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	 	'sid',
	 	'mid',
		'model',
		'technology',
		'asin',
		'asset',
		'grade',
		'cpu',
		'cpu_core',
		'cpu_model',
		'cpu_gen',
		'cpu_speed',
		'shopify_product_id',
		'status',
		'run_status',
	   	'added_on',
		'added_by',
	];

	public static function addListDataRecord($data)
    {
    	$result = false;
    	$listData = new ListData();
    	$listData->sid = $data->sid;
		$listData->mid = $data->mid;
		$listData->asset = $data->asset;
		$listData->added_by = $data->added_by;
		$listData->added_on = $data->added_on;
		$listData->model = $data->model;
		$listData->technology = $data->technology;
		$listData->asin = $data->asin;
		$listData->grade = $data->grade;
		$listData->cpu = $data->cpu;
		$listData->cpu_core = $data->cpu_core;
		$listData->cpu_model = $data->cpu_model;
		$listData->cpu_gen = $data->cpu_gen;
		$listData->cpu_speed = $data->cpu_speed;
		$listData->shopify_product_id = $data->shopify_product_id;
		$listData->status = $data->status;
		$listData->run_status = $data->run_status;
		if($listData->save())
		{
    		$result = $listData->id;
		}
		return $result;
    }

    public static function deleteListDataRecorde($mid, $text1)
    {
        $recorde = self::where(["mid" => $mid, "asset" => $text1])->first();
        if($recorde->isEmpty())
        {
            $recorde->delete();
        }
    }

    public static function updateModelID($mid, $asin)
    {
    	return self::where(['asin' => $asin,'mid' => 0])
    		->update(['mid' => $mid]);
    }

    public static function updateRunStatus($value, $status)
    {
    	return self::where(["asset" => $value])
    		->update(["run_status" => $status]);
    }

    public static function checkRecordExist($value)
    {
    	return self::where(["asset" => $value])
    		->first();
	}

	public static function updateShopifyProductId($productID, $asinId)
	{
		return self::where(["asin" => $asinId])
			->update(["shopify_product_id" => $productID]);
	}

	public static function updateShopifyAsinId($asinValue, $model)
	{
		return self::where(['model' => $model])
			->update(['asin' => $asinValue]);
	}

	public static function getSelectedFields($fields, $query)
	{
		return self::select($fields)
			->where($query)
			->get();
	}

	public static function updateSelectedFields($fields, $query)
	{
		return self::where($query)
			->update($fields);
	}

	public static function getSelectedFieldsByGroupBy($fields)
	{
		return self::select($fields)
			->where('shopify_product_id', '!=', '0')
			->where('shopify_product_id', '!=', '')
			->where('asin',  '!=', '')
			->groupBy(['asin', 'shopify_product_id'])
			->get();
	}

	public static function getrunningList()
	{
		return self::select('asin','technology','model','cpu_core','cpu_gen','shopify_product_id')
			->selectSub('count(asin)', 'cnt')
			->selectSub('max(mid)', 'mid')
            ->where(['status' => 'active', 'run_status' => 'active'])
            ->where('asin',  '!=', '')
            ->groupBy(['asin','technology','model','cpu_core','cpu_gen','shopify_product_id'])
            ->get();
	}

	public static function getDistinctRecordForCPU($mid)
	{
		return self::where(['asin' => $mid, 'status' => 'active', 'run_status' => 'active'])
			->groupBy('cpu')
			->count();
	}

	public static function getInventoryExportResult()
	{ 
		return self::select('asset','model','technology','cpu','added_on')
			->where(['status' => 'active', 'run_status' => 'active'])
			->where('asin', '!=', '')
			->get();
	}

	public static function getFormModelAndListData($id)
	{
		return self::select('list_data.*','a.tab','a.technology','a.id')
			->selectSub('count(list_data.id)', 'cnt')
			->selectSub('list_data.id', 'list_id')
    		->join('form_models as a', function($join){
                $join->on('list_data.mid', '=', 'a.id');
                })
    		->where(['list_data.status' => 'active','list_data.run_status' => 'active', 'list_data.asin' => $id])
    		->where('list_data.model', '!=', 'none')
    		->where('list_data.id', '>', 0)
    		->groupBy('list_data.id')
            ->first();
		}
}