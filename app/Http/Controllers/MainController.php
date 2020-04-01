<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use Redirect;
use File;
use Config;
use App\Asin;
use App\Session;
use App\ShopifyPricing;
use App\MessageLog;

class MainController extends Controller
{
	/**
     * Instantiate a new ShopifyController instance.
     */
	public function __construct($searchDataArray=[])
	{
    }

    public function index(Request $request)
    {
    	$messageLogs = MessageLog::getLatestRecord();
    	return view('admin.message-log.list', compact('messageLogs'));
    }

    public function importIndex(Request $request)
    {
        $shopifyPriceData = '';
        $message = '';
        if (isset($request->model) || isset($request->form_factor) || isset($request->processor) || isset($request->condition))
        {
            $runlist = array();
            $runlist['condition'] = $request->condition;
            $runlist['form_factor'] = $request->form_factor;
            $runlist['model'] = $request->model;
            $runlist['cpu_core'] = $request->processor;
            $finalPrice = new App\Http\Controllers\ShopifyController($runlist);
            $shopifyPriceData = ShopifyPricing::getRecordForImport($request);
        }
        return view('admin.import.list', compact('shopifyPriceData','message'));
    }
}