<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShopifyProductImport;
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
    public $basePath;
	/**
     * Instantiate a new ShopifyController instance.
     */
	public function __construct($searchDataArray=[])
	{
        $this->basePath = base_path().'/public';
    }

    public function index(Request $request)
    {
    	$messageLogs = MessageLog::getLatestRecord();
    	return view('admin.message-log.list', compact('messageLogs'));
    }

    public function importIndex(Request $request)
    {
        if (isset($request->model) || isset($request->form_factor) || isset($request->processor) || isset($request->condition))
        {
            $runlist = array();
            $runlist['condition'] = $request->condition;
            $runlist['form_factor'] = $request->form_factor;
            $runlist['model'] = $request->model;
            $runlist['cpu_core'] = $request->processor;
            $finalPrice = new ShopifyController($runlist);
            $shopifyPriceData = ShopifyPricing::getRecordForImport($request);
            $shopifyPriceData = ($shopifyPriceData) ? $shopifyPriceData->toArray() : [];
        }
        return view('admin.import.list', compact('shopifyPriceData'));
    }

    public function shopifyProductImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            $targetDir = $this->basePath.'/'."import-file/";
            $targetFile = $targetDir.$file->getClientOriginalName();
            $fileMoved = $file->move($targetDir, $file->getClientOriginalName());
            if($fileMoved)
            {
                try
                {
                    $import = new ShopifyProductImport();
                    Excel::import($import,$fileMoved);
                    $message = 'Successfully Updated All Records.';
                    $status = 'success';
                }
                catch (\Maatwebsite\Excel\Validators\ValidationException $e)
                {
                    $message = $e->getCode().' '.$e->getMessage();
                    $status = 'error';
                }
                catch (\Exception $ex)
                {
                    $message = $ex->getCode().' '.$ex->getMessage();
                    $status = 'error';
                }
                catch (\Error $er)
                {
                    $message = $er->getCode().' '.$er->getMessage();
                    $status = 'error';
                }
                return redirect()->back()->with($status,$message);
            }
            else
            {
                return redirect()->back()->with('error',"Sorry, there was an error uploading your file.");
            }
        }
    }
}