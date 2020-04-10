<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use File;
use ZipArchive;
use App\WipeReport;

class CommonController extends Controller
{
	public $fileExtensions, $directories, $basePath;

	/**
     * Saving some important variables value.
     *
     * @return mixed
     */
	public function __construct()
	{
		$this->basePath  = base_path().'/public';
		$this->fileExtensions = ["csv", "pdf", "txt"];
		$this->directories = ['wipe-data' => $this->basePath.'/wipe-data/*', 'blancco/pdf-data' => $this->basePath.'/blancco/pdf-data/*'];
	}

	/**
     * for wipe report section.
     *
     * @return mixed
     */
	public function index()
	{
		return view( 'admin.wipereport.index');
	}

	/**
     * get wipe report files for a lot number.
     *
     * @return mixed
     */
	public function getWipeReportFiles(Request $request)
	{
		$response = [];
		$response['status'] = false;
		$returnFiles = [];
		$searchedAsset = $request->lotNum;
		foreach($this->directories as $key => $directory)
		{
			foreach($this->fileExtensions as $extension)
			{
				$search_path = $directory.".".$extension;
				$files = glob($search_path);
				foreach ($files as $file)
				{
					if (stripos($file, $searchedAsset) !== false)
					{
						$fileName = substr($file, strrpos($file, '/') + 1);
						$returnFiles[$fileName]['url'] = URL($key.'/'.$fileName);
						$returnFiles[$fileName]['path'] = $file;
					}
				}
			}
		}
		if (count($returnFiles) > 0)
		{
			$response['status'] = true;
			$response['files'] = $returnFiles;
		}
		return response()->json($response);
	}

	/**
     * For Exporting wipe data files.
     *
     * @return mixed
     */
	public function ExportWipeReportFiles(Request $request)
	{
		$files = $request->wipefiles;
		$zip_file = time().'-wipe-report.zip'; 
		$zip = new ZipArchive();
		$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$path = storage_path('wipereport');
		foreach ($files as $name => $file)
		{
			$relativePath = 'wipereport/' . substr($file, strlen($path) + 1);
			$zip->addFile($file, $relativePath);
		}
		$zip->close();
		return response()->download($zip_file)->deleteFileAfterSend(true);
	}

	/**
     * For PDF files count.
     *
     * @return mixed
     */
	public function getWipeReportFilesCount(Request $request)
	{
		$wipePdf = $biosPdf = $blancooPdf = $totalFiles = 0;
		if(isset($request->dates))
		{
			$dates = $request->dates;
			$datesArray = explode(" - ",$dates);
			$dateFrom = date("Y-m-d",strtotime($datesArray[0]));
			$dateTo = date("Y-m-d",strtotime($datesArray[1]))." 23:59:59";
		}
		else
		{
			$date1 = date("m/d/Y",strtotime("-1 days"))." 23:59:59";
			$dateFrom = date("Y-m-d",strtotime("-1 days"));
			$dateTo = date("Y-m-d",strtotime("-1 days"))." 23:59:59";
			$dates = date("m/d/Y",strtotime("-1 days")). " - ".$date1;
		}
		$reports = WipeReport::getWipeReportsFilesCountData($dateFrom, $dateTo);
		foreach ($reports as $key => $report)
		{
			$wipePdf += $report->wipe_data_pdf_count;
			$biosPdf += $report->bios_data_file_count;
			$blancooPdf += $report->blancco_pdf_data_count;
		}
		$totalFiles = $wipePdf+$biosPdf+$blancooPdf;
		return view( 'admin.wipereport.wipereportcount', compact('dates', 'wipePdf', 'biosPdf', 'blancooPdf', 'totalFiles'));
	}
}