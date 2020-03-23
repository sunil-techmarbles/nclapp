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
use PDF;
use App\Recycle;
use App\RecycleRecord;
use App\RecycleRecordLine;

class RecycleController extends Controller
{
	public $basePath, $current, $wipeData2, $filePath, $logo, $returlPath;
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
        $this->filePath = $this->basePath.'/recycle/files/pdf';
        $this->returlPath = '/recycle/files/pdf';
        $this->logo = $this->basePath.'/recycle/logo.jpg';
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
		$recycleDataFiles = RecycleRecord::getRecord($value=0);
  		return view('admin.recycle-first.list', compact('recycleDataFiles', 'unapporovedCategories', 'categories', 'currentUser', 'selected'));
  	}

    /**
    * Method getTarePrice return tare price on basis user input 
    */
    public function getTarePrice($request)
    {
        switch ($request->tare)
        {
            case 'P':
                $tare = 35;
                break;

            case 'G':
                $tare = 60;
                break;

            case 'I':
                $tare = 0;
                break;
        }
        return $tare;
    }

    /**
    * Method recyclRecord Add new Recycl Record  
    */
    public function recyclRecord(Request $request)
    {
        if (isset($request->action) && $request->action == 'new_record')
        {
            //**** Calculate Price Start
            $catData = Recycle::getTypeOfScrap($request);
            $tare = $this->getTarePrice($request);
            $lbstare = $request->gross_weight - $tare;
            $calulatePrice = $catData[0]['PRICE'] * $lbstare;
            $totalPrice = number_format((float) $calulatePrice, 2, '.', '');
            $price = number_format((float) $catData[0]['PRICE'], 2, '.', '');
            //*** claculate Price End
            $fileName = "Recycle_BOL.xlsx";
            $fileCreatedDate = $this->current;
            $totalRows = 75;
            $checkFile = RecycleRecord::getRecord($value=1);
            $checkFile = (!$checkFile->isEmpty()) ? $checkFile->toArray() : [];
            $newData = [
                'name' => $fileName,
                'started' => $fileCreatedDate,
                'closed' => '',
                'status' => '1'
            ];
            if (empty($checkFile))
            {
                $sqlData = RecycleRecord::addRecord((object) $newData);
            }
            elseif (isset($checkFile[0]['total']) && $checkFile[0]['total'] == $totalRows)
            {
                $updateRecord = [
                    'name' => "Recycle_BOL".$this->current.'.xlsx',
                    'closed' => $fileCreatedDate,
                    'status' => '0'
                ];

                $query = [
                    'id' => $checkFile[0]['id'],
                    'status' => '1'
                ];
                $updateData = RecycleRecord::updateRecord($query, $updateRecord);
                $sqlData = RecycleRecord::addRecord((object) $newData);
            }
            $recycleRecord = RecycleRecord::getRecordByName($fileName);
            $recycleRecord = (!$recycleRecord->isEmpty()) ? $recycleRecord->toArray() : [];
            $recycleId = $recycleRecord[0];
            if (!empty($recycleId))
            {
                $data = [
                    'record_id' => $recycleId,
                    'category' => $request->category,
                    'lgross' => $request->gross_weight,
                    'ltare' => $lbstare,
                    'price' => $price,
                    'total_price' => $totalPrice,
                    'pgi' => $request->tare,
                ];
                $recordData = RecycleRecordLine::addRecord((object) $data);
            }
            return redirect()->route('recycle.first');
        }
        else
        {
            return redirect()->route('recycle.first')->with('error', 'Access Denied');
        }
    }
    
    public function editRecycleRecord(Request $request)
    {
        if (isset($request->record_id))
        {
            //get all categories
            $categories = Recycle::getAllTypeOfScrap($query= ['status' => '0']);
            $recordId = $request->record_id;
            $allRecords = RecycleRecordLine::getAllRecycleRecordLineByRecordId($recordId);
            return view('admin.recycle-first.edit', compact('allRecords', 'recordId', 'categories'));
        }
        abort('404');
    
    }

    /**
    * Ajax Request to update the recycle catageory and total price
    */
    public function updateRecycleRecord(Request $request)
    {
        if($request->ajax())
        {
            if (isset($request->action) && $request->action == 'edit_record')
            {
                $catData = Recycle::getTypeOfScrap($request);
                $tare = $this->getTarePrice($request);
                $lbstare = $request->gross_weight - $tare;
                $calulatePrice = $catData[0]['PRICE'] * $lbstare;
                $totalPrice = number_format((float) $calulatePrice, 2, '.', '');
                $price = number_format((float) $catData[0]['PRICE'], 2, '.', '');
                $query = [
                    'id' => intval($request->record_id),
                ];
                $fields = [
                    'category' => $request->category,
                    'lgross' => $request->gross_weight,
                    'ltare' => $lbstare,
                    'price' => $price,
                    'total_price' => $totalPrice,
                    'pgi' => $request->tare,
                ];
                $result = RecycleRecordLine::updateRecord($query, $fields);
                if($result)
                {
                    return response()->json(['message' => 'Record updated successfully.', 'status' => true]);
                }
                else
                {
                    return response()->json(['message' => 'something went wrong.', 'status' => false]);
                }
            }
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }

    /**
    * Ajax Request to delete the recycle catageory and total price
    */
    public function deleteRecycleRecord(Request $request, $recordId)
    {
        if($request->ajax())
        {
            $result = RecycleRecordLine::deleteRecord($recordId);
            if($result)
            {
                return response()->json(['message' => 'Record deleted successfully.', 'status' => true]);
            }
            return response()->json(['message' => 'something went wrong.', 'status' => false]);
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }
    public function recycleDownloadPdf(Request $request)
    {
        if($request->ajax())
        {
            // recycle-recorde.blade.php
            if (isset($request->file_name) && !empty($request->file_name))
            {
                $fileName = $request->file_name;
                $fileName = str_replace('xlsx', 'pdf', $fileName);
                $filePath =$this->filePath.'/'.$fileName;
                $returnPath = $this->returlPath.'/'.$fileName;
                $logo = $this->logo;
                $pdfFileData = RecycleRecord::getRecordById($request);
                $pdfFileData = (!$pdfFileData->isEmpty()) ? $pdfFileData->toArray() : [];
                $closed = ($pdfFileData[0]['status']) ? '' : Carbon::createFromFormat('l F jS,Y', $pdfFileData[0]['closed']);
                $orientation = 'landscape';
                $customPaper = array(0,0,950,950);
                $pdfData = RecycleRecordLine::getAllRecycleRecordLineByRecordId(intval($request->id));
                $pdfData = $pdfData->chunk(5);
                $pdf = PDF::loadView('admin.pdf.recycle-recorde', compact('closed', 'pdfData', 'logo'))->setPaper($customPaper, $orientation)->stream();
                $pdf->save($filePath);
                return response()->json(['url' => $returnPath, 'status' => true]);
            }
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }
}
