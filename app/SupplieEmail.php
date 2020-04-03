<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplieEmail extends Model
{
    use SoftDeletes;
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

    public static function addSupplieEmail($email, $supplie_id)
    {       
        $result = false;
        $supplieEmail = new SupplieEmail();
        $supplieEmail->supplie_id = $supplie_id;
        $supplieEmail->email = $email;

        if($supplieEmail->save())
        {
            $result = $supplieEmail->id;
        }
        
        return $result;
        # code...
    }

    public static function deleteSupplieEmail($supplieEmailId)
    {
        if(self::whereIn('id',$supplieEmailId)->delete())
        {
            return true;
        }
    }

    public static function deleteBulkSupplieEmail($supplieID)
    {
        return false;
        $bulkSupplieEmailsID = self::where(['supplie_id' => $suplieID])->pluck('id');
        if($bulkSupplieEmailsID)
        {
            self::whereIn('id',$bulkSupplieEmailsID)->delete();
            return true;
        }
    }

    public static function getsuppliersEmails($suplieID)
    {
        return self::where(['supplie_id' => $suplieID])->pluck('email');
    }

    public static function getSupplieEmails($sID)
    {
        return self::where(['supplie_id' => $sID])
            ->pluck('email');
    }
}
