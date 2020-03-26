<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Mail;
use App\Traits\CommenRecycleTraits;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Redirect;
use Config;
use File;
use PDF;
use App\Recycle;
use App\RecycleRecord;
use App\RecycleRecordLine;
use App\ItamgRecycleInventory;

class RecycleController extends Controller
{
    use CommenRecycleTraits;
	public $basePath, $current, $wipeData2, $filePath, $logo, $returlPath, $rootPath;

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
        $this->rootPath = $this->basePath.'/recycle/files';
        $this->wipeData2 = $this->basePath.'/wipe-data2';
        $this->filePath = $this->basePath.'/recycle/files/pdf';
        $this->returlPath = '/recycle/files/pdf';
        $this->logo = url('/').'/recycle/logo.jpg';
    }
    /**
 	* Method recycleSecondIndex use for Recycle 2 
 	*/
    public function recycleSecondIndex(Request $request)
  	{
        $itamgRecycleInventors = ItamgRecycleInventory::getAllRecord();
  		return view('admin.recycle-second.list', compact('itamgRecycleInventors'));
  	}
    /**
    * Method recycleSecondSearch use for Recycle 2 search
    */

    public function recycleSecondSearch(Request $request)
    {
        return view('admin.recycle-second.search');
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

    public function deleteRecycleCategoryRecord(Request $request, $categoryName)
    {
        if($request->ajax())
        {
            $result = Recycle::deleteRecord(trim($categoryName));
            if($result)
            {
                return response()->json(['message' => 'Category deleted successfully.', 'status' => true]);
            }
            return response()->json(['message' => 'something went wrong.', 'status' => false]);
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }

    /**
    * Ajax Request to approve category when approved by admin
    */
    public function approveRecycleCategoryRecord(Request $request)
    {
        if($request->ajax())
        {
            $result = Recycle::approveCategoryRecord(trim($request->approve_cat_name));
            if($result)
            {
                return response()->json(['message' => 'Category Approved successfully.', 'status' => true]);
            }
            return response()->json(['message' => 'something went wrong.', 'status' => false]);
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }

    public function submitRecycleCategoryRecord(Request $request)
    {
        if($request->ajax())
        {
            if (isset($request->action) && $request->action == 'submit')
            {

                $sampleFile = $this->rootPath."/sample.xlsx";
                $sampleCategoryFile = $this->rootPath."/sample_category.xlsx";
                $recycleDate = Carbon::now()->format('l F jS,Y');
                $fileClosedDate = Carbon::now()->format('m-d-Y');
                $recycleFilePath = $this->rootPath."/processed/Recycle_BOL".$fileClosedDate.'.xlsx';
                $categoriesFilePath = $this->rootPath."/categories/Recycle_Invoice_".$fileClosedDate.".xlsx";
                $recordId = $request->id;
                $xlsxData = RecycleRecordLine::getAllRecycleRecordLineByRecordId(intval($recordId));
                if (!empty($xlsxData))
                {
                    $this->init($xlsxData, $type='first', $sampleFile, $recycleFilePath);
                }
                $recordData = RecycleRecordLine::getRecordGroupByCat($recordId);
                if (!empty($recordData))
                {
                    $this->init($recordData, $type='secound', $sampleCategoryFile, $categoriesFilePath);
                }
                $rename = "Recycle_BOL".$this->current. '.xlsx';
                $dbDate = $this->current;
                $query = [
                    'id' => $recordId,
                ];

                $fields = [
                    'name' => $rename,
                    'closed' => $dbDate,
                    'status' => '0'
                ];
                RecycleRecord::updateRecord($query, $fields);
                $emails = config('constants.recycleReportMailAddress');
                $message = "Hi,
                            The Recycle shipment have been created.
                            Please find the attachment. ";
                $subject = 'Recycle Shipment Detail';
                
                $files[] = ['url' => $recycleFilePath, 'name' => "Recycle_BOL".$fileClosedDate.'.xlsx'];
                $files[] = ['url' => $categoriesFilePath, 'name'=> "Recycle_Invoice_".$fileClosedDate.".xlsx"];
                Mail::raw($message, function ($m) use ($subject, $emails, $files) {
                    $m->to($emails)->subject($subject);
                    foreach($files as $file) {
                        $m->attach($file['url'], array(
                            'as' => $file['name'],
                            'mime' => 'xlsx')
                        );
                    }
                });
                return response()->json(['message' => 'Submitted successfully.', 'status' => true]);
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
                $filePath = $this->filePath.'/'.$fileName;
                $returnPath = $this->returlPath.'/'.$fileName;
                $logo = $this->logo;
                $pdfFileData = RecycleRecord::getRecordById($request);
                $pdfFileData = (!$pdfFileData->isEmpty()) ? $pdfFileData->toArray() : [];
                $closed = ($pdfFileData[0]['status']) ? '' : Carbon::createFromFormat('l F jS,Y', $pdfFileData[0]['closed']);
                $pdfData = RecycleRecordLine::getAllRecycleRecordLineByRecordId(intval($request->id));
                $html = view('admin.pdf.recycle-recorde', compact('closed', 'pdfData', 'logo'))->render();
                $this->createPDF($html, $request->file_name, $filePath);
                return response()->json(['url' => $filePath, 'status' => true]);
            }
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }

    public function addNewCategoryRecord(Request $request)
    {
        if($request->ajax())
        {
            if (isset($request->action) && $request->action == 'new_cat')
            {
                if (empty($request->category_name) || empty(trim($request->category_name)))
                {
                    return response()->json(['message' => 'Category name required.', 'status' => false]);
                }
                else
                {
                    $query = [
                        'Type_of_Scrap' => trim($request->category_name)
                    ];
                    $data = [
                        'Type_of_Scrap' => trim($request->category_name), 'status' => '1'
                    ];
                    $checkIfExist = Recycle::getAllTypeOfScrap($query);
                    if (count($checkIfExist->toArray()) > 0)
                    {
                        return response()->json(['message' => 'Category already exists.', 'status' => false]);
                    }
                    else
                    {
                        $result = Recycle::addRecord((object) $data);
                        if($result)
                        {
                            return response()->json(['message' => 'Your category is successfully added. Wait for admin approval.', 'status' => true]);
                        }
                    }
                }
            }
        }
        else
        {
            return response()->json(['message' => 'something went wrong with ajax request', 'status' => false]);
        }
    }

    public function editCategoryRecord(Request $request)
    {
        if (isset($request->cat_name) && !empty($request->cat_name))
        {
            $data = ['category' => $request->cat_name];
            $categoryData = Recycle::getTypeOfScrap((object) $data);
            $categoryData = (!$categoryData->isEmpty()) ? $categoryData->toArray() : [];
            return view('admin.recycle-first.category-edit', compact('categoryData'));
        }
        abort('404');
    }

    public function updateCategoryRecord(Request $request)
    {

        if (isset($request->action) && $request->action == 'update_category')
        {
            if (empty($request->Type_of_Scrap) || empty(trim($request->Type_of_Scrap)))
            {
                return redirect()->back()->with('error', 'category name required');
            }
            $query = [
                'id' => $request->cat_id
            ];
            $checkIfExist = Recycle::getAllTypeOfScrap($query);
            if (count($checkIfExist->toArray()) > 0)
            {
                $data = [
                    'PRICE' => $request->PRICE,
                    'TYPE' => $request->TYPE,
                    'Type_of_Scrap' => trim($request->Type_of_Scrap),
                    'status' => $request->status
                ];
                $query = [
                    'id' => $request->cat_id
                ];
                $result = Recycle::updateRecord($data, $query);
                if($result)
                {
                    return redirect()->back()->with('success', 'Category updated successfully');
                }
            }
            else
            {
                return redirect()->back()->with('error', 'Something went wrong');
            }
        }
    }
}