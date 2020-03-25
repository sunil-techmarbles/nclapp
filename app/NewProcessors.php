<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewProcessors extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'Manufacturer',
        'Name',
        'Type',
        'Model',
        'Generation',
        'Codename',
        'Cores',
        'Threads',
        'Socket',
        'Process',
        'Clock',
        'Multi',
        'Cache_L1_L2_L3',
        'TDP',
        'Released',
    ];

    public static function getProcessorData($series)
    {
    	return self::where('Model','=',$series)
						->first();
    }

    public static function getMissedProcessorData($model1, $model2)
    {
    	return self::select('*')
    		->where('Model', 'like', '%' .$model1. '%')
        	->where('Model', 'like', '%' .$model2. '%')
        	->first();
    }

    public static function updateProcessor($series, $cores)
    {
    	return self::where(['Model' => $series])
    		->update(['Cores' => $cores]);
    }
}