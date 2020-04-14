<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Asin;
use File;
use Illuminate\Support\Facades\DB;
use App\Supplies;
use App\SupplieAsinModel;
use App\SupplieEmail;
use App\MessageLog;
use App\UserCronJob;

class AsinController extends Controller
{
	public $searchItemsLists, $adminEmails, $e_mails, $emailTemplate, $emailSubject;
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

        $this->e_mails = [];
        $this->adminEmails = UserCronJob::getCronJobUserEmails('defaultEmails');
        if($this->adminEmails->count() > 0)
        {
            foreach ($this->adminEmails as $key => $value) {
                $this->e_mails[] = $value->email;
            }
        }
        $this->adminEmails = ($this->adminEmails->count() > 0) ? $this->e_mails : array(
            'richy@itamg.com',
            'randy@itamg.com',
            'kamal@itamg.com',
        );

        $this->emailTemplate = "Hi, 
            We are running low on item: 
            [item_name] 
            Part Number: [part_num]
            Current Qty: [qty]

            We get this item from Vendor: [vendor]. Suggested Reorder Quantity is: [reorder_qty]. This item usually ships: [dlv_time]. 

            Please Reorder As Soon As Possible. 
        Thanks!";

        $this->emailSubject = "Running Low On An Item! - Reorder Request";
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

    public function getHttpResponseCode($url)
    {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    public function getAsinPrice($asin)
    {
        $url = "http://www.amazon.com/gp/aw/d/".$asin;
        if($this->getHttpResponseCode($url) == "404")
        {
            MessageLog::addLogMessageRecord("asin number don't exist","asin price","failure");
            $price = 0;
        }
        else
        {
            $html = file_get_contents($url);
            $price = getBetween($html,'data-asin-price="','"');
        }
        return $price;
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

        $asin = $request->asin;
        if($asin)
        {
            $price = $this->getAsinPrice($asin);
            if($price) $request->merge(['price' => $price]);
        }

        $asinId = Asin::storeAsinValue($request);
        if($asinId)
        {
            $savedId = $asinId;
            if($request->model != "Template")
            {
                $tplid = Asin::getAsinIdFormModel([
                    "model" => "Template",
                    "form_factor" => $request->form_factor
                ]);
                if($tplid)
                {
                    $supIds = SupplieAsinModel::getSupplieIdExistsAsinValue($tplid);
                    foreach ($supIds as $key => $value)
                    {
                        SupplieAsinModel::addSupplieAsinModel($savedId, $value);
                    }
                }

                $ffsubs = [
                    "Notebook" => "Laptop",
                    "Ultra Small Form Factor" => "USFF",
                    "Small Form Factor" => "SFF",
                    "Tiny Desktop" => "Desktop"
                ];

                if(isset($ffsubs[$request->form_factor]))
                {
                    $ff = $ffsubs[$request->form_factor];
                }
                else
                {
                    $ff = $request->form_factor;
                }
                $ptname = $request->manufacturer." ".$request->model." ".$ff."Product Card";
                $ptid = Supplies::getRecordByName($ptname);
                if($ptid)
                {
                    SupplieAsinModel::addSupplieAsinModel($savedId, $ptid);
                }
                else
                {
                    $data = [
                        "item_name"   => $ptname,
                        "qty"         => 0,
                        "item_url"    => '',
                        "description"    => '',
                        "description"    => '',
                        "bulk_options"    => '',
                        "part_num"    => "N/A",
                        "dept"        => "Refurb - Supplies",
                        "vendor"      => "Minute Men",
                        "low_stock"   => 50,
                        "price"       => 0.4,
                        "reorder_qty" => 100,
                        "dlv_time"    => "1 day",
                        "email_subj"  => $this->emailSubject,
                        "email_tpl"   => $this->emailTemplate,
                    ];
                    $suppliesID = Supplies::addSupplies((object) $data);
                    foreach ($this->adminEmails as $key => $value)
                    {
                        SupplieEmail::addSupplieEmail($value, $suppliesID);
                    }
                    SupplieAsinModel::addSupplieAsinModel($savedId, $suppliesID);
                }
                $ptname = $request->manufacturer." ".$request->model." ".$ff." Skin";
                $ptid = Supplies::getRecordByName($ptname);
                if($ptid)
                {
                    SupplieAsinModel::addSupplieAsinModel($savedId, $ptid);
                }
                else
                {
                    $data = [
                        "item_name"   => $ptname,
                        "qty"         => 0,
                        "item_url"    => '',
                        "description"    => '',
                        "bulk_options"    => '',
                        "part_num"    => "IBM / Black",
                        "dept"        => "Refurb - Supplies",
                        "vendor"      => "LidStyles",
                        "low_stock"   => 10,
                        "reorder_qty" => 50,
                        "price"       => 5,
                        "dlv_time"    => "1-2 days Express | 3-5 Days Ground",
                        "email_subj"  => $this->emailSubject,
                        "email_tpl"   => $this->emailTemplate,
                    ];
                    $suppliesID = Supplies::addSupplies((object) $data);
                    foreach ($this->adminEmails as $key => $value)
                    {
                        SupplieEmail::addSupplieEmail($value, $suppliesID);
                    }
                    SupplieAsinModel::addSupplieAsinModel($savedId, $suppliesID);
                }
            }
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
        $id = $request->id;
        $validatedData = $request->validate([
            'asin' => 'required|unique:asins,asin,'.$id,
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
