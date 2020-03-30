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
use App\SessionData;
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
}