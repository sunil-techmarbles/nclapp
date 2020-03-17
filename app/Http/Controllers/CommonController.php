<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use File;
use ZipArchive;

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
}