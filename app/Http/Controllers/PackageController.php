<?php

namespace App\Http\Controllers;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Package; 
use Validator;
use App\User; 

class PackageController extends Controller
{


	public function index(Request $request)
	{
		$searchedPackages = [];

		$userDetails = User::getUserDetail( Sentinel::getUser()->id );
		$userName = $userDetails->first_name;

		if( isset($request->pkg_search) && $request->pkg_search == 'Search')
		{
			$searchedPackages = Package::getPackages($request); 
		} 
		return view( 'admin.packages.index' , compact('searchedPackages', 'userName') ); 
	}

	public function AddUpdatePackage(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'expected_arrival' => 'required|min:2|max:50',
			'description' => 'required|min:2|max:50',  
			'req_name' => 'required|min:2|max:50',  
			'carrier' => 'required|min:2|max:50',  
			'freight_ground' => 'required|min:2|max:50',  
			'qty' => 'required|numeric|min:1|max:100', 
			'order_date' => 'required|min:2|max:50', 
		]);
 
		if ($validator->fails()) 
		{
			$response['validation']  = 'errors';
			$response['messages'] =  $validator->errors();
			return response()->json($response); 
		}
		else 
		{
			if($request->pkg_id == 'new')
			{
				$validator = Validator::make($request->all(), [
					'tracking_number' => 'required|unique:packages,tracking_number',
				]);
				if ($validator->fails())
				{
					$response['validation']  = 'errors';
					$response['messages'] =  $validator->errors();
					return response()->json($response);
				}
				Package::AddPackageDetails($request); 	
				$response['status']  = 'success';
				$response['title']  = 'Package Added';
				$response['message'] = 'New Package Added Successfully'; 
			}
			else
			{
				$id = intval($request->pkg_id);
				$validator = Validator::make($request->all(), [
					'tracking_number' => 'required|unique:packages,tracking_number,'.$id,
				]);
				if ($validator->fails())
				{
					$response['validation']  = 'errors';
					$response['messages'] =  $validator->errors();
					return response()->json($response); 
				} 
				Package::UpdatePackageDetails($request);  
				$response['status']  = 'success';
				$response['title']  = 'Package Update';
				$response['message'] = 'Package Update Successfully'; 
			} 
			return response()->json($response);
		}
	}

	public function CheckInPackage(Request $request)
	{
		$checkTrackingId = Package::CheckIfPackageExist($request->tn);
		
		if($checkTrackingId){ 
			$update = Package::UpdatePackageRecived($request); 
			if($update)
			{
				$response['status']  = 'success';
				$response['title']  = 'Package Updated';
				$response['message'] = 'Package Recived Successfully updated'; 
			}
			else
			{
				$response['status']  = 'error';
				$response['title']  = 'Some Error occurred';
				$response['message'] = 'Please try again';
			}
		}
		else
		{
			$response['status']  = 'error';
			$response['title']  = 'Package Not Exist';
			$response['message'] = 'Package not found with this tracking number , Please try again With correct tracking number'; 
		}
		return response()->json($response); 
	}
}
