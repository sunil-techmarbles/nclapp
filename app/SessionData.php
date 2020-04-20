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
    	return self::where(["asset"=>$value])->get();
    }

    public static function getSessionDataCount($id)
    {
		return self::where(['sid' => $id, 'status' => 'active'])
			->count();
    }

    public static function updateSessionStatus($session,$r,$satus)
    {
    	return self::where(["sid"=> $session,"asset" => $r])
            ->update(["status"=> $satus]);
    }

    public static function updateRecord($request)
    {
        return self::where(["asset" => $request['asset']])->update(['aid' => $request['aid']]);
    }

    public static function updateSessiontStatus($r, $status)
    {
        return self::where(["asset"=> $r])
            ->update(["status"=> $status]);
    }

    public static function updateSessionRunStatus($asset, $status)
    {
        return self::where(["asset"=> $asset])
            ->update(["run_status"=> $status]);
    }

    public static function updateRecordRunStatus($ids, $text)
    {
        $rowCount = self::WhereIn("asset" ,$ids)->count();
        $output =  self::whereIn("asset" ,$ids)->update(['run_status' => $text]);
        if($output)
        {
            return ['count' => $rowCount, 'output' => $output];
        }
        else
        {
            return ['count' => 0, 'output' => 0];
        }
    }
    
    public static function getAsinsAidByAssest($value)
    {
        return self::where(['asset'=>$value])
            ->pluck('aid')
            ->first();
    }

    public static function deleteSeesionDataRecorde($sess, $text1 )
    {
        $recorde = self::where(["sid" => $sess, "asset" => $text1])->first();
        if($recorde)
        {
			$recorde->delete();
        }
    }

    public static function getSessionItems($session,$satus)
    {
    	return self::select('session_data.aid', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
			->selectSub('count(session_data.aid)', 'cnt')
    		->join('asins as a', function($join) use($session, $satus){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','=', $session)
                        ->where('session_data.status','=', $satus);
                })
    		->groupBy('session_data.aid')
            ->get();
    }

    public static function getSessionAssets($session,$satus)
   	{
   		return self::select('aid','asset','status')
   			->where(['sid' => $session])
   			->get();
   	}

    public static function sessionSummary($currentSession)
    {
    	return self::select('session_data.aid', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
			->selectSub('count(session_data.aid)', 'cnt')
    		->join('asins as a', function($join) use($currentSession){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','=', $currentSession)
                        ->where('session_data.status','=', 'active');
                })
    		->groupBy('session_data.aid')
            ->get();
    }

    public static function sessionItems($currentSession)
    {
    	return self::select('session_data.aid', 'session_data.asset', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link', 'session_data.added_on')
			->selectSub('count(session_data.aid)', 'cnt')
    		->join('asins as a', function($join) use($currentSession){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','=', $currentSession)
                        ->where('session_data.status','=', 'active');
                })
    		->orderBy('session_data.aid', 'DESC')
    		->orderBy('session_data.asset', 'DESC')
            ->get();
    }

    public static function sessionParts($currentSession)
    {
    	return self::select('i.id', 'i.part_num', 'i.item_name', 'i.qty', 'i.vendor', 'i.dlv_time', 'i.low_stock', 'i.reorder_qty', 'i.email_tpl', 'i.email_subj')
            ->selectSub('sum(p.qty)', 'required_qty')
            ->selectSub('sum(p.qty) - i.qty', 'missing')
            ->join('supplies as i', function($join) use($currentSession){
                $join->on('session_data.aid', '=', 'i.id');
            })
            ->join('supplie_asin_models as p', function($join) use($currentSession){
                $join->on('i.id', '=', 'p.supplie_id');
            })
            ->join('session_data as d', function($join) use($currentSession){
                $join->on('d.aid', '=', 'p.asin_model_id')
                    ->where('d.sid','=', $currentSession)
                    ->where('d.status','=', 'active');
            })
            ->groupBy('i.id')
            ->get();
    }

    public static function getrunningListFromSessionData()
    {
        return self::select('session_data.aid', 'a.shopify_product_id', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
            ->selectSub('count(session_data.aid)', 'cnt')
            ->join('asins as a', function($join){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','>=', '9')
                        ->where('session_data.status','=', 'active')
                        ->where('session_data.run_status','=', 'active');
                })
            ->groupBy('session_data.aid')
            // ->get();
            ->paginate(10);
    }

    public static function getrunningListItemsFromSessionData($id)
    {
        return self::select('session_data.aid', 'session_data.sid', 'session_data.asset', 'session_data.added_on', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
            ->join('asins as a', function($join) use ($id) {
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('session_data.sid','>=', '9')
                        ->where('session_data.aid','=', $id)
                        ->where('session_data.status','=', 'active')
                        ->where('session_data.run_status','=', 'active');
                })
            ->get();
    }

    public static function getRunningListExport()
    {
        return self::select('session_data.asset', 'a.model', 'a.form_factor', 'a.price', 'a.asin', 'session_data.added_on')
            ->selectSub("concat(a.cpu_core, ' ', a.cpu_model, '@', a.cpu_speed)", 'CPU')
            ->join('asins as a', function($join) {
                $join->on('session_data.aid', '=', 'a.id')
                    ->where('session_data.sid','>=', '9')
                    ->where('session_data.status','=', 'active')
                    ->where('session_data.run_status','=', 'active');
                })
            ->get();
    }

    public static function getRunListForSyncProcess($asinId)
    {
        return self::select('session_data.asset', 'a.*')
            ->join('asins as a', function($join) use ($asinId) {
                $join->on('session_data.aid', '=', 'a.id')
                    ->where('session_data.sid','>=', '9')
                    ->where('session_data.status','=', 'active')
                    ->where('session_data.run_status','=', 'active')
                    ->where('a.asin','=', $asinId);
                })
            ->groupBy('session_data.aid')
            ->get();
    }

    public static function getAsinInventrySectionData()
    {
        return self::select('session_data.aid', 'session_data.added_on', 'a.asin', 'a.price', 'a.model', 'a.form_factor', 'a.cpu_core',  'a.cpu_model', 'a.cpu_speed', 'a.ram', 'a.hdd', 'a.os', 'a.webcam', 'a.notes', 'a.link')
            ->selectSub('count(session_data.aid)', 'cnt')
            ->join('asins as a', function($join){
                $join->on('session_data.aid', '=', 'a.id')
                        ->where('a.asin','!=', '0')
                        ->where('session_data.status','=', 'active');
                })
            ->groupBy('a.model', 'a.form_factor', 'a.cpu_core', 'a.cpu_model', 'a.cpu_speed')
            ->get();
    }

     public static function getSessionData()
    {
        return self::select('aid', 'asset', 'status')
            ->where(['status' => 'active'])
            ->get();
    }

    public static function CheckAssetExist($assetId)
    {
        return self::where(["asset"=>$assetId])->get()->first();
    }
}
