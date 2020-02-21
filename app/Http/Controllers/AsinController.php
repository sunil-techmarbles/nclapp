<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Asin;

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

    public function index()
    {
    	$searchItemsLists = $this->searchItemsLists;
    	$asinLists = Asin::getAllAsins();
    	return view ('admin.asin.list', compact('asinLists','searchItemsLists'));
    }

    public function addAsins()
    {
    	# code...
    }

    public function editAsin()
    {
    	
    }
}
