<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteOptions extends Model
{
   use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'option_name',
        'option_value',
        'option_group',
    ];
    

    


}
