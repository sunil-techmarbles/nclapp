<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
		'ramtype',
		'hddtype',		
		'os',		
		'webcam',		
		'notes',		
		'link',		
		'shopify_product_id',	
    ];

    public static function getAsinManufactureData($asinId)
    {
        return self::select('asins.manufacturer')
                ->where(['id' => $asinId])
                ->first();
    }

    public static function getAllAsins($request)
    {
        $query = self::select('*');
        if ($request->has('s') || $request->has('f')) {
            $query->where($request->get('f'), 'like', '%' .$request->get('s'). '%');
        }
        return $query->orderBy('id', 'DESC')
            // ->get();
        ->paginate(10);
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
        $asins->ramtype = $request->ramtype;
        $asins->hddtype = $request->hddtype;       
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
                'ramtype' => $request->ramtype,
				'hddtype' => $request->hddtype,
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

    public static function getAsinForPriceUpdate()
    {
        return self::select('asins.id', 'asins.asin', 'asins.price')
            ->where('asin', '!=' , '0' )
            ->orWhere('asin', '!=' , '' )
            ->get();
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

    public static function updateShopifyProductId($productId, $asinId)
    {
        return self::where(['asin' => $asinId])
            ->update(['shopify_product_id' => $productId]);
    }

    public static function UpdateAsinPrice($price, $asinRecordid)
    {
        return self::where(['id' => $asinRecordid])
            ->update(['price' => $price]);
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
    
    public static function getSpecificFourthRecord($fields, $model, $cpuCore, $cpuMdl, $formfactor)
    {
        return self::select($fields)
            ->where(function($q) use ($model, $cpuCore, $cpuMdl, $formfactor){
                $q->where([
                    "cpu_core" => $cpuCore,
                    "form_factor" => $formfactor,
                ]);
                $q->where('cpu_model', 'LIKE', $cpuMdl);
                $q->orWhere('model', 'LIKE', $model);
                $q->orWhere("model_alias", 'LIKE', $model);
            })
            ->get();
    }

    public static function getSpecificFifthRecord($fields, $model, $cpuCore)
    {
        return self::select($fields)
            ->where(function($q) use ($model, $cpuCore){
                $q->where([
                    "cpu_core" => $cpuCore,
                ]);
                $q->orWhere('model', 'LIKE', $model);
                $q->orWhere("model_alias", 'LIKE', $model);
            })
            ->get();
    }

    public static function getSpecificTemplateRecord($fields)
    {
        return self::select($fields)
            ->where('model', '!=', "Template")
            ->get();
    }

    public static function getSpecificSixthRecord($fields, $model)
    {
        return self::select($fields)
            ->where(function($q) use ($model){
                $q->orWhere('model', 'LIKE', $model);
                $q->orWhere("model_alias", 'LIKE', $model);
            })
            ->get();
    }

    public static function getAsinLookupFields() 
    {
        return self::select('id','asin','price','model','form_factor','cpu_core','cpu_model',
            'cpu_speed','ram','hdd')
            ->where('asin', '!=' , '0' )
            ->get();  
    }

    public static function getModelFromAsin($asin, $notifications)
    {
        return self::select("id","model","cpu_core","cpu_model","cpu_speed")
            ->whereIn("id", $asin)
            ->where(["notifications" => $notifications])
            ->get();
    }

    public static function getAssestModelAsinResult($data, $parts2, $parts1)
    {
        return self::select(["id","model","cpu_core","cpu_model","cpu_speed"])
            ->where(function($q) use ($data, $parts2, $parts1){
                $q->orWhere(["model" => $data,
                    "notifications" => 1,
                    "cpu_core" => $parts2[0],
                    "cpu_speed"=>$parts1[1]]
                );
                $q->orWhere('cpu_model', 'LIKE', $parts2[1]);
                $q->orWhere("model_alias", 'LIKE', $data);
            })
            ->get();
    }

    public static function getAssestModelAsinOtherResult($data, $parts2)
    {
        return self::select(["id","model","cpu_core","cpu_model","cpu_speed"])
            ->where(function($q) use ($data, $parts2, $parts1){
                $q->orWhere([
                    "model" => $data,
                    "notifications" => 1,
                    "cpu_core" => $parts2[0],
                    "cpu_speed"=>$parts1[1]
                ]);
                $q->orWhere("model_alias", 'LIKE', $data);
            })
            ->get();
    }

    public static function getAsinIdFormModel($query)
    {
        return self::where($query)
            ->pluck('id')
            ->first();
    }
}
