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
		  
		$searchedPackages = (object) [];	

		$userDetails = User::getUserDetail( Sentinel::getUser()->id );
        $userName = $userDetails->first_name . ' ' . $userDetails->last_name;

		if( isset($request->pkg_search) && $request->pkg_search == 'Search')
		{
			$searchedPackages = Package::getPackages($request); 
		} 

		return view( 'admin.packages.index' , compact('searchedPackages', 'userName') ); 
	} 


	public function AddNewPackage(Request $request)
	{   
		$validator = Validator::make($request->all(), [
				'expected_arrival' => 'required|min:2|max:50',
				'description' => 'required|min:2|max:50',  
				'tracking_number' => 'required|unique:packages,tracking_number,'.$request->tracking_number , 
				'req_name' => 'required|min:2|max:50',  
				'carrier' => 'required|min:2|max:50',  
				'freight_ground' => 'required|min:2|max:50',  
				'qty' => 'required|min:2|max:50', 
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
			$addNewPackage = Package::AddPackage($request);
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

	
	public function CheckInPackage(Request $request)
	{ 
		$checkTrackingId = Package::CH($request->tn);


		// dd( $request );  


	}




}
