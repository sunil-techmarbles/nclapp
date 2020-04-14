<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplies extends Model
{   
    use SoftDeletes;

	protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_name',	
		'item_url',	
		'qty',	
		'part_num',	
		'description',		
		'dept',	
		'price',		
		'vendor',	
		'low_stock',	
		'reorder_qty',	
		'dlv_time',	
		'bulk_options',		
		'email_subj',	
		'email_tpl',		
		'email_sent',
    ];

    // Get All Supplies
    public static function getAllSupplies($request)
    {
    	$query = self::select('*');

        if ($request->has('s') || $request->has('f')) {
            $query->where($request->get('f'), 'like', '%' .$request->get('s'). '%');
        }

    	return $query->orderBy('id', 'DESC')
            ->get();
    }

    public static function addSupplies($request)
    {
    	$result = false;
    	$supplies = new Supplies();
    	$supplies->item_name = $request->item_name ;
    	$supplies->item_url = $request->item_url;
    	$supplies->qty = $request->qty;
    	$supplies->part_num = $request->part_num;
    	$supplies->description = $request->description;
    	$supplies->dept	= $request->dept;	
		$supplies->price = $request->price;		
		$supplies->vendor = $request->vendor;	
		$supplies->low_stock = $request->low_stock;	
		$supplies->reorder_qty = $request->reorder_qty;	
		$supplies->dlv_time	= $request->dlv_time;	
		$supplies->bulk_options	= $request->bulk_options;		
		$supplies->email_subj = $request->email_subj;	
		$supplies->email_tpl = $request->email_tpl;		
		if($supplies->save())
		{
			$result = $supplies->id;
		}
    	return $result;
    }

    public static function updateSupplieById($request)
    {
    	$result = false;
    	$updateSupplie = self::where(['id' => $request->id])
    		->update([
		    	'item_name' => $request->item_name,
		    	'item_url' => $request->item_url,
		    	'qty' => $request->qty,
		    	'part_num' => $request->part_num,
		    	'description' => $request->description,
		    	'dept'	=> $request->dept,
				'price' => $request->price,
				'vendor' => $request->vendor,
				'low_stock' => $request->low_stock,
				'reorder_qty' => $request->reorder_qty,
				'dlv_time'	=> $request->dlv_time,
				'bulk_options'	=> $request->bulk_options,
				'email_subj' => $request->email_subj,
				'email_tpl' => $request->email_tpl,
    		]);

		if($updateSupplie)
		{
			$result = true;
		}
    	return $result;
    	# code...
    }

    public static function updateQuantityBySupplieID($supplieId, $Qty)
    {
        return self::where(['id' => $supplieId])
            ->update([
                'qty' => $Qty,
            ]);
    }

    public static function getSupplieById($supplieId)
    {
    	return self::with(['getSupplieAsinModels', 'getSupplieEmails'])
    		->where(['id' => $supplieId])
    		->first();
    }

    public static function getMissingParts($asinID, $qty)
    {
		return self::select('supplies.*')
            ->selectSub('(p.qty * '.$qty.')', 'required_qty')
            ->selectSub('(p.qty * '.$qty.' - supplies.qty)', 'missing')
            ->join('supplie_asin_models as p', function($join) use($asinID){
                $join->on('supplies.id', '=', 'p.supplie_id')
                    ->where('p.asin_model_id','=',$asinID);
                })
            ->get();
    }

    public static function getSupplieDepartmentsByDistinct()
    {
        return self::distinct()
            ->get([
                'dept'
            ]);
    }

    public static function deleteBulkSupplieBelowDate($updatedAt)
    {
        return false;
        $bulkSupplyID = self::where('updated_at', '<' ,$updatedAt)->pluck('id');
        if($bulkSupplyID)
        {
            self::whereIn('id',$bulkSupplyID)->delete();
            return true;
        }
    }

    public static function getSupplieByLowQty($dt)
    {
        return self::where('qty','<=', 'low_stock')
            ->where('low_stock', '>', '0')
            ->where('email_sent', '<', $dt)
            ->get();
    }

    public static function deleteSupplieByID($sid)
    {
        $result = false;
        $supplie = self::find($sid);
        if($supplie->delete())
        {
            $result = true;
        }

        return $result;
    }

    public function getSupplieAsinModels()
    {
    	return $this->hasMany('App\SupplieAsinModel', 'supplie_id', 'id');
    }

    public function getSupplieEmails()
    {
    	return $this->hasMany('App\SupplieEmail', 'supplie_id', 'id');
    }

    public static function getSupplieDetailAndEmails($supplie_id)
    {
        return self::with(['getSupplieEmails'])
            ->where(['id' => $supplie_id])
            ->first();
    }

    public static function getEmailsAndSupplyrecord($date)
    {
        return self::with(['getSupplieEmails'])
            ->where('qty' ,'<=', 'low_stock')
            ->where('low_stock' ,'>', '0')
            ->where('email_sent' ,'<', $date)
            ->get();
    }

    public static function getAllPartsSpecificFields($specificFields, $orderBy, $asinID)
    {
        return self::with(['getSupplieAsinModels'])
            ->select($specificFields)
            ->orderBy($orderBy)
            ->get();
    }

    public static function updateMailSentTime($sid, $current)
    {
        return self::where(['id' => $sid])
            ->update(['email_sent' => $current]);
    }

    public static function getRecordByName($ptname)
    {
        return self::where(['item_name' => $ptname])
            ->pluck('id')
            ->first();
    }

    public static function getExportResult()
    {
        return self::select('id','item_name','item_url','qty','part_num','description','dept', 'price',
                'vendor','low_stock','reorder_qty','dlv_time','bulk_options', 'email_subj','email_tpl')
            ->with(['getSupplieEmails'])
            ->get();
    }

    public static function getSessionParts($session, $satus)
    {
        return self::select('supplies.id', 'supplies.part_num', 'supplies.item_name', 'supplies.qty', 'supplies.vendor', 'supplies.dlv_time', 'supplies.low_stock', 'supplies.reorder_qty', 'supplies.email_tpl', 'supplies.email_subj')
            ->selectSub('sum(p.qty)', 'required_qty')
            ->selectSub('sum(p.qty) - supplies.qty', 'missing')
            ->join('supplie_asin_models as p', function($join) use($session){
                $join->on('supplies.id', '=', 'p.supplie_id');
            })
            ->join('session_data as d', function($join) use($session, $satus){
                $join->on('d.aid', '=', 'p.asin_model_id')
                    ->where('d.sid','=', $session)
                    ->where('d.status','=', $satus);
            })
            ->groupBy('supplies.id')
            ->get();
    }
}
