<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplieEmail extends Model
{
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplie_id',	
		'email',	
    ];

    public static function addSupplieEmail($request, $supplie_id)
    {       
        $result = false;
        $supplieEmail = new SupplieEmail();
        $supplieEmail->supplie_id = $supplie_id;
        $supplieEmail->email = $request->email;

        if($supplieEmail->save())
        {
            $result = $supplieEmail->id;
        }
        
        return $result;
        # code...
    }
}
