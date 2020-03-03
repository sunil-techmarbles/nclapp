<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use File;
use Config;
use App\Asin;
use App\Shipment;
use App\Session;
use App\ShipmentsData;
use App\SessionData;
use App\AsinAsset;

class SessionController extends Controller
{
    public $basePath, $refurbLabels, $current, $refurbAssetData, $formData, $sessionReports;

	public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->current = Carbon::now();
    	$this->sessionReports = $this->basePath.'/session-reports';
    	$this->formData = $this->basePath.'/form-data';
    	$this->formData = $this->basePath.'/form-data';
    	$this->refurbAssetData = $this->basePath.'/refurb-asset-data';
    	$this->refurbLabels = $this->basePath.'/refurb-labels';
    }

    public function index(Request $request)
	{
		$sessions = [];
		return view('admin.sessions.list', compact('sessions'));
	}
}
