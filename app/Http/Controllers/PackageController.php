<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;

class PackageController extends Controller
{

	public function index(Request $request)
	{
		return view('admin.packages.index');
	} 


	public function AddNewPackage(Request $request)
	{ 
		$addNewPackage = Package::AddUpdatePackage($request);

		if($addNewPackage)
		{
			$response['status']  = 'success';
			$response['title']  = 'Package Added';
			$response['message'] = 'New Package Added Successfully'; 
		}
		else
		{
			$response['status']  = 'error';
			$response['title']  = 'Unable to add';
			$response['message'] = 'Unable to add Package, Please try again'; 
		}

		return response()->json($response); 

	}

}
