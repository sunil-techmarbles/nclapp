<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplies;
use App\SupplieEmail;
use App\SupplieAsinModel;
use App\Asin;

class SuppliesController extends Controller
{
	public $searchItemsLists, $adminEmails, $emailTemplate;
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

        $this->adminEmails = array(
            'richy@itamg.com' => 'richy@itamg.com',
            'randy@itamg.com' => 'randy@itamg.com',
            'kamal@itamg.com' => 'kamal@itamg.com',
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

    public function index()
    {
		$searchItemsLists = $this->searchItemsLists;
    	$supplieLists = Supplies::getAllSupplies();
    	return view ('admin.supplies.list', compact('supplieLists','searchItemsLists'));
    }

    public function addSupplies()
    {
        $models = Asin::getModelList();
       
        return view ('admin.supplies.add',compact('models'))->with([
            'adminEmails' => $this->adminEmails,
            'emailTemplate' => $this->emailTemplate
        ]);

    	# code...
    }

    public function storeSupplies(Request $request)
    {
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

        if($supplieID){
            SupplieEmail::addSupplieEmail($supplieID, $request);
            SupplieAsinModel::addSupplieAsinModel($supplieID, $request);
            return redirect()->route('supplies')->with('success','Item created successfully!');
        }
        else{
            return redirect()->route('supplies')->with('error','Something went wrong! Please try again');
        }
        # code...
    }
    public function exportSupplies()
    {
    	# code...
    }

    public function importSupplies()
    {
    	# code...
    }
}
