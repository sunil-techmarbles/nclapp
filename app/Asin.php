<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class Asin extends Model
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
		'manufacturer',		
		'model',		
		'model_alias',		
		'notifications',	 	
		'form_factor',		
		'cpu_core',		
		'cpu_model',		
		'cpu_speed',	
		'ram',		
		'hdd',		
		'os',		
		'webcam',		
		'notes',		
		'link',		
		'shopify_product_id',	
    ];

    public static function getAllAsins($request)
    {
        $query = self::select('*');

        if ($request->has('s') || $request->has('f')) {
            $query->where($request->get('f'), 'like', '%' .$request->get('s'). '%');
        }

        return $query->orderBy('id', 'DESC')
            ->get();
    }

    public static function storeAsinValue($request)
    {
        $result = false;
        $asins = new Asin();
        $asins->asin = $request->asin ;
        $asins->price = $request->price;
        $asins->manufacturer = $request->manufacturer;
        $asins->model = $request->model;
        $asins->model_alias = $request->model_alias;
        $asins->notifications = $request->notifications;   
        $asins->price = $request->price;     
        $asins->form_factor = $request->form_factor;   
        $asins->cpu_core = $request->cpu_core; 
        $asins->cpu_model = $request->cpu_model; 
        $asins->cpu_speed = $request->cpu_speed;   
        $asins->ram = $request->ram;       
        $asins->hdd = $request->hdd;   
        $asins->os = $request->os;     
        $asins->webcam = $request->webcam;     
        $asins->notes = $request->notes;     
        $asins->link = $request->link;  
        $asins->shopify_product_id = $request->shopify_product_id;  
        if($asins->save())
        {
            $result = $asins->id;
        }
        
        return $result;
    }

    public static function updateAsinRecord($request)
    {
        return self::where(['id' => $request->id])
            ->update([
                'asin' => $request->asin,
                'price' => $request->price,
                'manufacturer' => $request->manufacturer,
                'model' => $request->model,
                'model_alias' => $request->model_alias,
                'notifications' => $request->notifications,
                'price' => $request->price,
                'form_factor' => $request->form_factor,
                'cpu_core' => $request->cpu_core,
                'cpu_model' => $request->cpu_model,
                'cpu_speed'=> $request->cpu_speed,
                'ram' => $request->ram,
                'hdd' => $request->hdd,
                'os' => $request->os,
                'webcam' => $request->webcam,
                'notes' => $request->notes,
                'link' => $request->link,
                'shopify_product_id' => $request->shopify_product_id,
            ]);
    }

    public static function getModelList()
    {
    	return self::select('asins.id', 'asins.asin', 'asins.model', 'asins.form_factor')
    		->orderBy('asins.model', 'DESC')
    		->get();	
    }

    public static function getAsinsPartsById($asinID='')
    {
        return self::where(['id' => $asinID])
            ->first();
    }

    public static function getAsinsIdByAsin($asinID='')
    {
        return self::where(['asin' => $asinID])
            ->pluck('id')
            ->first();
    }

    public static function getAsinById($asinID)
    {
        return self::where(['id' => $asinID])
            ->first();
    }

    public static function deleteAsinByID($aID)
    {
        $result = false;
        $asins = self::find($aID);
        if($asins->delete())
        {
            $result = true;
        }

        return $result;
    }

    public static function getSpecificFirstRecord($fields, $model, $part1, $part2, $part3)
    {
        return self::select($fields)
            ->where(function($q) use ($model, $part1, $part2, $part3){
                $q->where([
                    "cpu_core"=> $part1,
                    "cpu_speed" => $part3
                ]);
                $q->where("cpu_model", 'LIKE', $part2);
                $q->orWhere('model', $model);
                $q->orWhere("model_alias", 'LIKE', $model);
            })
            ->get();
    }

    public static function getSpecificSecoundRecord($fields, $model, $part1)
    {
        return self::select($fields)
            ->where(function($q) use ($model, $part1){
                $q->where([
                    "cpu_core"=> $part1,
                ]);
                $q->orWhere('model', $model);
                $q->orWhere("model_alias", 'LIKE', $model);
            })
            ->get();
    }

    public static function getSpecificThirdRecord($fields, $query)
    {
        return self::select($fields)
            ->where(function($q) use ($query){
               $q->where("model", '!=', $query);
            })
            ->get();
    }
    
}
