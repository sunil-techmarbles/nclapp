<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class FormsConfig extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [

    	'tab',
		'tab_order',
		'grp',
		'question',
		'qtype',
		'allow_new',
		'config',
		'required',
		'sort',	
		'default_val',
		'is_active',
		'options',
		'grades',
		'xml_grp',
    ];

    public static function addRecord($request)
    {
    	$formsConfig = new FormsConfig();
    	$formsConfig->tab = $request['tab'];
		$formsConfig->tab_order = $request['tab_order'];
		$formsConfig->grp = $request['grp'];
		$formsConfig->question = $request['question'];
		$formsConfig->qtype = $request['qtype'];
		$formsConfig->allow_new = $request['allow_new'];
		$formsConfig->config = $request['config'];
		$formsConfig->required = $request['required'];
		$formsConfig->sort = $request['sort'];
		$formsConfig->default_val = $request['default_val'];
		$formsConfig->is_active = $request['is_active'];
		$formsConfig->options = $request['options'];
		$formsConfig->grades = $request['grades'];
		$formsConfig->xml_grp = $request['xml_grp'];

		if($formsConfig->save())
		{
			return $formsConfig->id;
		}

		return false;
    }

    public static function getConfigValueByTab($tab, $group)
    {
    	return self::where(['tab' => $tab, 'grp' => $group])->get();
    }


    public static function GetTab( $tabname ){  
    	
    	return self::where(['tab' => $tabname, 'is_active' => 'yes'])->get(); 

	} 

}
