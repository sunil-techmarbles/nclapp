<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplieAsinModel extends Model
{
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplie_id',	
		'asin_model_id',	
    ];

    public static function addSupplieAsinModel($request, $supplie_id)
    {       
        $result = false;
        $supplieAsinModel = new SupplieAsinModel();
        $supplieAsinModel->supplie_id = $supplie_id;
        $supplieAsinModel->asin_model_id = $request->asin_model_id;

        if($supplieAsinModel->save())
        {
            $result = $supplieAsinModel->id;
        }
        
        return $result;
        # code...
    }
}
