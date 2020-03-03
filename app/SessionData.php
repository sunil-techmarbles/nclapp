<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class SessionData extends Model
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
		'aid',
		'asset',
		'run_status',
		'added_by',
		'added_on',
		'status',	
    ];

    public static function addSessionDataRecord($data, $current)
    {
    	$result = false;
    	$sessionData = new SessionData();
    	$sessionData->sid = $data->sid;
		$sessionData->aid = $data->aid;
		$sessionData->asset = $data->asset;
		$sessionData->added_by = $data->added_by;
		$sessionData->added_on = $data->added_on;
		if($sessionData->save())
		{
    		$result = $sessionData->id;
		}
		return $result;

    }

    public static function hasAssests($value)
    {
    	return self::where(["asset"=>$asset])->get();
    }

    public static function getSessionDataCount($id)
    {
		return self::where(['sid' => $id, 'status' => 'active'])
			->count();
    }

    public static function sessionSummary($currentSession)
    {
    	$sql = "select d.aid, count(d.aid) as cnt, 
				a.asin, a.price, a.model, a.form_factor, a.cpu_core, a.cpu_model, a.cpu_speed, a.ram, a.hdd, a.os, a.webcam, a.notes, a.link
		 		from tech_sessions_data d inner join tech_asins a on d.aid = a.id where d.sid='$current_session' and d.status='active' group by d.aid";
		$sessionSummary = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sessionItems($currentSession)
    {
    	$sql = "select d.aid, d.asset, a.asin, a.price, a.model, a.form_factor, a.cpu_core, a.cpu_model, a.cpu_speed, a.ram, a.hdd, a.os, a.webcam, a.notes, a.link, d.added_on
		 		from tech_sessions_data d inner join tech_asins a on d.aid = a.id where d.sid='$current_session' and d.status='active' order by d.aid, d.asset";
		$session_items = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 
    }

    public static function sessionParts($currentSession)
    {
    	$sql = "select i.id, i.part_num, i.item_name, i.qty, sum(p.qty) as required_qty, sum(p.qty) - i.qty as missing,
				i.vendor, i.dlv_time, i.low_stock, i.reorder_qty, i.email_tpl, i.emails, i.email_subj
				from tech_inventory i inner join tech_asins_parts p on i.id = p.part_id 
				inner join tech_sessions_data d on p.asin_id = d.aid
				where d.sid = '$current_session' and d.status='active' group by i.id";
		$session_parts = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 
    }
}
