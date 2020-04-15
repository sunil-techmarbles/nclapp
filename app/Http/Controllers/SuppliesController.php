<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Exports\SuppliesExport;
use App\Imports\SuppliesImport;
use Carbon\Carbon;
use Excel;
use App\Supplies;
use App\SupplieEmail;
use App\SupplieAsinModel;
use App\Asin;
use App\UserCronJob;

class SuppliesController extends Controller
{
	public $searchItemsLists, $adminEmails, $e_mails, $emailTemplate;
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->searchItemsLists = array(
			'id' => 'Item ID',
			'item_name' => 'Item Name',
			'qty' => 'Quantity',
			'part_num' => 'P/N',
			'description' => 'Description',
			'dept' => 'Department',
			'price' => 'Price',
			'vendor' => 'Vendor',
			'low_stock' => 'Low Stock',
			'reorder_qty' => 'Reorder Qty',
			'dlv_time' => 'Delivery Time',
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
    }

    public function index(Request $request)
    {
		$searchItemsLists = $this->searchItemsLists;
    	$supplieLists = Supplies::getAllSupplies($request);
    	return view ('admin.supplies.list', compact('supplieLists','searchItemsLists'));
    }

    public function addSupplies()
    {
        $models = Asin::getModelList();
        return view ('admin.supplies.add',compact('models'))->with([
            'adminEmails' => $this->adminEmails,
            'emailTemplate' => $this->emailTemplate
        ]);
    }

    public function storeSupplies(Request $request)
    {
        $pageaction = isset($request->pageaction) ? $request->pageaction : '';
        $validatedData = $request->validate([
            'item_name' => 'required',
            'qty' => 'required|integer',
            'part_num' => 'required',
            'dept' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'vendor' => 'required',
            'low_stock' => 'required|integer',
            'reorder_qty' => 'required|integer',
        ]);

        $supplieID = Supplies::addSupplies($request);
        if($supplieID)
        {
            if($request->get('email'))
            {   
                $supplieEmails = array_filter(explode(',',$request->get('email')));
                foreach ($supplieEmails as $key => $email)
                {
                    SupplieEmail::addSupplieEmail($email, $supplieID);
                }
            }

            if($request->get('applicable_models'))
            {
                $applicableModels = array_filter($request->get('applicable_models'));
                foreach ($applicableModels as $key => $applicableModel)
                {
                    SupplieAsinModel::addSupplieAsinModel($applicableModel, $supplieID);
                }
            }
            return redirect()->route('supplies',['pageaction'=>$pageaction])->with('success','Item created successfully!');
        }
        else
        {
            return redirect()->route('supplies',['pageaction'=>$pageaction])->with('error','Something went wrong! Please try again');
        }
    }

    public function editSupplies(Request $request)
    {
        $supplieID = $request->id;
        $pageaction = isset($request->pageaction) ? $request->pageaction : '';
        $supplieDetail = Supplies::getSupplieById($supplieID);
        $models = Asin::getModelList();
        if($supplieDetail)
        {
            return view ('admin.supplies.edit',compact('models', 'supplieDetail'))->with([
                'adminEmails' => $this->adminEmails,
                'emailTemplate' => $this->emailTemplate
            ]);
        }
        else
        {
            return redirect()->route('supplies',['pageaction' => $pageaction])->with('error','Something went wrong! Please try again');   
        }
        abort('404');
    }

    public function updateSupplies(Request $request)
    {
        $pageaction = isset($request->pageaction) ? $request->pageaction : '';
        $validatedData = $request->validate([
            'item_name' => 'required',
            'qty' => 'required|integer',
            'part_num' => 'required',
            'dept' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'vendor' => 'required',
            'low_stock' => 'required|integer',
            'reorder_qty' => 'required|integer',
        ]);
        $result = Supplies::updateSupplieById($request);
        $supplieID = $request->id;
        if($result)
        {
            if($request->get('email'))
            {   
                $supplieEmails = array_filter(explode(',',$request->get('email')));
                foreach ($supplieEmails as $key => $email)
                {
                    SupplieEmail::addSupplieEmail($email, $supplieID);
                }
            }

            if($request->get('applicable_models'))
            {
                $applicableModels = array_filter($request->get('applicable_models'));
                foreach ($applicableModels as $key => $applicableModel)
                {
                    SupplieAsinModel::addSupplieAsinModel($applicableModel, $supplieID);
                }
            }

            if($request->get('exists_email'))
            {
                $supplieEmailId = array_unique(array_filter($request->get('exists_email')));
                SupplieEmail::deleteSupplieEmail($supplieEmailId);
            }

            if($request->get('exists_asinid'))
            {
                $supplieAsinModelId = array_unique(array_filter($request->get('exists_asinid')));
                SupplieAsinModel::deleteSupplieAsinModel($supplieAsinModelId);                
            }

            return redirect()->route('supplies',['pageaction' => $pageaction])->with('success','Item update successfully!');
        }
        else
        {
            return redirect()->route('supplies',['pageaction' => $pageaction])->with('error','Something went wrong! Please try again');
        }
    }

    public function updateQtyReorder(Request $request)
    {   
        if($request->ajax())
        {
            // $reorderRty = 0;
            // $sid = intval($request->supplieid);
            // $qty = intval($request->quantity);
            // if(isset($request->supplieid))
            // {
            //     $itmid = addslashes($request->itemid);
            //     $reorderRty = $request->qty;
            //     $records = Supplies::getSupplieByID($sid);
            // }
            // else
            // {
            //     $dt = date("Y-m-d",strtotime("-7 days"));
            //     $records = Supplies::getSupplieByLowQty($dt);
            // }
            // $cnt=0;
            // $mail = new Email();
            // foreach ($records as $r) {
            //     $vars = array_keys($r);
            //     if($reorderRty) $r["reorder_qty"] = $reorderRty;
            //     $body = $r["email_tpl"];
            //     foreach($vars as $v) {
            //         $body = str_replace("[$v]",$r[$v],$body);
            //     }
            //     $emails = explode(",",$r["emails"]);
            //     $mid = $mail->queue(implode(";",$emails),$r["email_subj"],$body); //implode(";",$emails)
            //     $cnt++;
            //     $db->update("tech_inventory",["email_sent"=>date("Y-m-d H:i:s")],["id"=>$r["id"]]);
            // } 
            // $mail->release();
            // if(isset($_GET['itemid'])){
            //     session_start();
            //     $_SESSION['content_saved'] = "
            //                 <div style='width: 100%'>
            //                     <div style='padding: 10px; color:#fff; background: #67B915; font-weight: bold'>Reorder request has been sent</div>
            //                 </div>
            //                 ";
            //     header('location: index.php');
            //     die();
            // }
            $sid = intval($request->supplieid);
            $qty = intval($request->quantity);
            // $result = Supplies::updateQuantityBySupplieID($sid,$qty);        
            // if ($result) 
            // {
                $supplieEmails = Supplies::getSupplieDetailAndEmails($sid);
                $varKeys = array_keys($supplieEmails->toArray());
                $supplieEmails["reorder_qty"] = ($qty) ? $qty : 0 ;
                $body = $supplieEmails['email_tpl'];
                $subject = $supplieEmails['email_subj'];
                foreach($varKeys as $v)
                {
                    $body = str_replace("[".$v."]",$supplieEmails[$v],$body);
                }

                $user = [];
                foreach ($supplieEmails['getSupplieEmails'] as $key => $value)
                {
                    array_push($user, $value['email']);
                }
                // $response['message'] = 'Quantity of supply update successfully';
                if(sizeof($user) > 0)
                {
                    $current = Carbon::now();
                    Supplies::updateMailSentTime($sid,$current);
                    Mail::raw($body, function ($m) use ($subject,$user) {
                        $m->to($user)
                        ->subject($subject);
                    });
                    $response['message'] = 'Reorder request has been sent';
                    $response['status']  = 'success';
                }
            // }
            else 
            {
                $response['status']  = 'error';
                $response['message'] = 'Something went wrong! Please try again';
            }
            return response()->json($response);
        }
        else
        {
            $pageaction = isset($request->pageaction) ? $request->pageaction : '';
            $sid = intval($request->supplieid);
            $qty = intval($request->qty);
            $result = Supplies::updateQuantityBySupplieID($sid,$qty);
            if ($result)
            {
                return redirect()->route('supplies',['pageaction' => $pageaction])->with('success','Item quantity update successfully!');
            }
            else
            {
                return redirect()->route('supplies',['pageaction' => $pageaction])->with('error','Something went wrong! Please try again');   
            }
        }
    }

    public function exportSupplies()
    {
    	return Excel::download(new SuppliesExport, 'supplies.xlsx');
    }

    public function importSupplies(Request $request)
    {
        $pageaction = isset($request->pageaction) ? $request->pageaction : '';
        $validatedData = $request->validate([
                'impfile' => 'required|mimes:xlsx,csv,txt',
            ],
            [
                'impfile.required' => 'Please upload a file',
                'impfile.mimes' => 'Only csv and excel file allowed',
            ]
        );
    	Excel::import(new SuppliesImport,request()->file('impfile'));
        return redirect()->route('supplies',['pageaction' => $pageaction])->with('success','Record import successfully');
    }

    public function deleteSupplie(Request $request, $supplieID)
    {
        $sid = intval($supplieID);
        $result = Supplies::deleteSupplieByID($sid);        
        if ($result)
        {
            $response['status']  = 'success';
            $response['message'] = 'Supply deleted successfully';
        }
        else
        {
            $response['status']  = 'error';
            $response['message'] = 'Unable to delete supply';
        }
        return response()->json($response);
    }

}
