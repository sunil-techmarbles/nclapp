<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplies;

class SuppliesController extends Controller
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
    }


    public function index()
    {
		$searchItemsLists = $this->searchItemsLists;
    	$supplieLists = Supplies::getAllSupplies();
    	return view ('admin.supplies.list', compact('supplieLists','searchItemsLists'));
    }

    public function addsupplies()
    {

    }
    	# code...
    public function exportsupplies()
    {
    	# code...
    }

    public function storesupplies()
    {
    	# code...
    }

    public function importsupplies()
    {
    	# code...
    }
}
