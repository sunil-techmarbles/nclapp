<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Redirect;
use Config;
use File;

use App\Recycle;
use App\RecycleRecord;
use App\RecycleRecordLine;

class RecycleController extends Controller
{
	public $basePath, $current, $wipeData2;
	/**
     * Instantiate a new RecycleController instance.
     */
	public function __construct($searchDataArray=[])
	{
    	/**
     	* Set value for common uses in the RecycleController instance.
     	*/
     	$this->basePath = base_path().'/public';
     	$this->current = Carbon::now();
     	$this->wipeData2 = $this->basePath.'/wipe-data2';
    }
    /**
 	* Method recycleSecondIndex use for Recycle 2 
 	*/
    public function recycleSecondIndex(Request $request)
  	{
  		return view('admin.recycle-second.list');
  	}
  	/**
 	* Method recycleSecondIndex use for Recycle 
 	*/
  	public function recycleFirstIndex(Request $request)
  	{
  		$currentUser = Sentinel::getUser()->first_name.' - '.Sentinel::getUser()->last_name;
  		$selected = '';
  		//get all categories
  		$categories = Recycle::getAllRecord($single='Type_of_Scrap', $query=['status'=>0]);
		//get unapproved categories
		$order = [
			'field' => 'id',
			'order' => 'DESC'
		];
		$unapporovedCategories = Recycle::getAllRecordOrderBy($order);
		//get recycle files
		$recycleDataFiles = RecycleRecord::getJoinRecod();
  		return view('admin.recycle-first.list', compact('recycleDataFiles', 'unapporovedCategories', 'categories', 'currentUser', 'selected'));
  	}
}
