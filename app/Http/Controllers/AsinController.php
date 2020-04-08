<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Asin;
use App\Supplies;
use App\SupplieEmail;
use App\SupplieAsinModel;

class AsinController extends Controller
{
	public $searchItemsLists;
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->searchItemsLists = array(
         'id'          =>'ID',
         'asin'        =>'ASIN',
         'price'       =>'Price',
         'manufacturer'=>'Manufacturer',
         'model'       =>'Model',
         'form_factor' =>'Form Factor',
         'cpu_core'    =>'CPU Core',
         'cpu_model'   =>'CPU Model',
         'cpu_speed'   =>'CPU Speed',
         'ram'         =>'RAM',
         'hdd'         =>'HDD',
         'os'          =>'OS',
         'webcam'      =>'Webcam',
         'notes'       =>'Notes',
         'link'        =>'Link',
         'notifications'=>'Notif.',
     );
    }

    public function index(Request $request)
    {
    	$searchItemsLists = $this->searchItemsLists;
    	$asinLists = Asin::getAllAsins($request);
    	return view ('admin.asin.list', compact('asinLists','searchItemsLists'));
    }

    public function addAsins(Request $request)
    {
    	return view ('admin.asin.add');
    }

    public function storeAsins(Request $request)
    {
        $validatedData = $request->validate([
            'asin' => 'required|unique:asins,asin',
            'manufacturer' => 'required',
            'model' => 'required',
            'form_factor' => 'required',
            'price' => 'regex:/^\d+(\.\d{1,2})?$/',
            'cpu_core' => 'required',
            'cpu_model' =>'required',
            'cpu_speed' =>'required',
            'ram' =>'required',
            'hdd' =>'required',
            'os' =>'required',
            'webcam' =>'required',
        ]);

        $asinId = Asin::storeAsinValue($request);
        if($asinId)
        {
            return redirect()->route('asin')->with('success','Item created successfully!');
        }
        else
        {
            return redirect()->route('asin')->with('error','Something went wrong! Please try again');
        }
    }

    public function editAsin(Request $request , $asinID)
    {
    	$asinDetail = Asin::getAsinById($asinID);
        if($asinDetail)
        {
            return view ('admin.asin.edit',compact('asinDetail'));
        }
        else
        {
            return redirect()->route('asin')->with('error','Something went wrong! Please try again');   
        }
        abort('404');
    }

    public function updateAsins(Request $request)
    {
        $validatedData = $request->validate([
            'asin' => 'required|unique:asins,asin,'.$userId,
            'manufacturer' => 'required',
            'model' => 'required',
            'form_factor' => 'required',
            'price' => 'regex:/^\d+(\.\d{1,2})?$/',
            'cpu_core' => 'required',
            'cpu_model' =>'required',
            'cpu_speed' =>'required',
            'ram' =>'required',
            'hdd' =>'required',
            'os' =>'required',
            'webcam' =>'required',
        ]);

        if(Asin::updateAsinRecord($request))
        {
            return redirect()->route('asin')->with('success','Item update successfully!');
        }
        else
        {
            return redirect()->route('asin')->with('error','Something went wrong! Please try again');
        }
    }

    public function partsAsin(Request $request, $asinID)
    {
        $qty = ($request->has('qty')) ? $request->get('qty') : 1;
        $pparts = ($request->has("ppart")) ? $request->get('ppart') : [];
        $specificFields = ['id','item_name','part_num','dept','vendor'];
        $asinsParts = Asin::getAsinById($asinID);
        $models = Asin::getAllAsins($request);
        $parts = Supplies::getMissingParts($asinID,$qty);
        $mailSent = [];
        $status = '';
        $message = '';

        if($request->has('assignasinsparts'))
        {
            $assignAsinsParts = ($request->get('mpart')) ? $request->get('mpart') : [];
            if(!empty($assignAsinsParts))
            {   
                $supplieAsinModelId = [];
                $supplieId = [];
                $currentParts = SupplieAsinModel::getSupplieIdExistsAsinValue($asinID);
                foreach ($currentParts as $key => $value) {
                    $supplieAsinModelId[] = $key;
                    $supplieId[] = $value;
                } 
                SupplieAsinModel::deleteSupplieAsinModel($supplieAsinModelId);
                foreach($assignAsinsParts as $assignAsinsPart)
                {
                    SupplieAsinModel::addSupplieAsinModel($asinID, $assignAsinsPart);
                }
            }
        }

        foreach($parts as $p)
        {
            if($request->has('withdraw'))
            {   
                if(in_array($p["id"],$pparts))
                {
                    $newQty = max(0,$p["qty"]-$p["required_qty"]);
                    Supplies::updateQuantityBySupplieID($p["id"], $newQty);
                    $status = 'success';
                    $message = 'Withdraw successfully';
                    \Session::flash($status, $message);
                }
            }

            if($p["missing"] > 0)
            {
                $p["reorder_qty"] = max($p["missing"] + $p["low_stock"],$p["reorder_qty"]);
                if($request->has('reorder'))
                {
                    $vars = array_keys($p->toArray());
                    $body = $p["email_tpl"];
                    foreach($vars as $v)
                    {
                        $body = str_replace("[".$v."]",$p[$v],$body);
                    }
                    $user = SupplieEmail::getsuppliersEmails(intval($p["id"]));
                    $subject = $p['email_subj'];
                    if(sizeof($user) > 0)
                    {
                        $current = Carbon::now();
                        Supplies::updateMailSentTime($p["id"],$current);
                        Mail::raw($body, function ($m) use ($subject,$user) {
                            $m->to($user->toArray())
                            ->subject($subject);
                        });
                        $mailSent[] = $p["part_num"];
                        $status = 'success';
                        $message = 'Part number '.implode(',', $mailSent).' Mail has been sent successfully';
                        \Session::flash($status, $message);
                    }
                    continue;
                }
            }
        }
        $departments = Supplies::getSupplieDepartmentsByDistinct();
        $allParts = resultInReadableform(
            Supplies::getAllPartsSpecificFields($specificFields, $orderBy='item_name', $asinID)
        );
        return view('admin.asin.parts', compact('asinsParts','qty','parts','models','departments','allParts'));
        abort('404');
    }

    public function deleteAsin(Request $request, $asinID)
    {
        $aid = intval($asinID);
        $result = Asin::deleteAsinByID($aid);        
        if ($result)
        {
            $response['status']  = 'success';
            $response['message'] = 'ASINs deleted successfully';
        }
        else
        {
            $response['status']  = 'error';
            $response['message'] = 'Unable to delete supplie';
        }
        return response()->json($response);
    }

    public function PartLookup(Request $request)
    {   
        $models = Asin::getAsinLookupFields();
        return view ('admin.partslook.list', compact('models'));
    }

    public function getASINNumber(Request $request)
    {
        if($request->ajax())
        {
            $asin = $request->get("asin");
            $id = Asin::getAsinsIdByAsin($asin);
            if (!$id)
            {
                $id = 0;
            }
            return $id;
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }
}
