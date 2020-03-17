<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
		return ($formsConfig->save()) ? true : false;
    }

    public static function getAllRecord()
    {
    	return self::get();
    }

    public static function getConfigValueByTab($tab, $group)
    {
    	return self::where(['tab' => $tab, 'grp' => $group])
    		->get();
    }

    public static function getTab($tabname, $isActive )
    {
    	$query = self::where(['tab' => $tabname]);
    	if($isActive != '')
    	{
    		$query->where(['is_active' => $isActive]);
    	}
		return $query->orderBy('tab_order')
    		->get();
	}

	public static function getFormConfigFields($key, $group)
	{
		return self::select('xml_grp', 'options')
			->where(['tab' => $key, 'grp' =>  $group])
			->get();
	}

	public static function getFormConfigDataByCommenQuery($query)
	{
		return self::select('*')
			->where($query)
			->get();
	}
}