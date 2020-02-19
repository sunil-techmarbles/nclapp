<?php

namespace App\Http\Controllers;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

use Illuminate\Http\Request;
use App\User;

class RegisterController extends Controller
{ 
   
   public function index() {  

   		$roles = \DB::table('roles')->select( 'name' , 'id' )->get();  
   		return view('auth.register' , compact("roles"))->with('pageTitle',  "Add User");    
 
   }	 

   	public function registerAuthenticate( Request $request ) {    

      	$validator = $request->validate(
	      	[
	         	'fname' => 'required|min:2|max:50',
	            'lname' => 'required|min:2|max:50',            
	            'email' => 'required|email|unique:users',
	            'password' => 'required|min:6',                 
	            'confirm_password' => 'required|min:6|max:20|same:password',
	    	],
	    	[
	            'fname.required' => 'First Name is required',
	            'fname.min' => 'First Name must be at least 2 characters.',
	            'fname.max' => 'First Name should not be greater than 50 characters.',
				'lname.required' => 'Last Name is required',
	            'lname.min' => 'Last Name must be at least 2 characters.',
	            'lname.max' => 'Last Name should not be greater than 50 characters.',
	            'email.unique' => 'That email address is already registered.',
	        ] 
    	);

      	$email = $request->input('email'); 	  

        $isExists = User::checkEmailExits( $email );    

        if( $isExists ) {
            return redirect()->back()->with(['error' => 'That email address is already registered.']);
        }
  
      	$user_data = [
		    'first_name' => $request->fname ,
		    'last_name' => $request->lname ,
		    'email'    => $request->email,
		    'password' => $request->password,  
     	]; 
 
        $user = Sentinel::registerAndActivate( $user_data ); 
		$role = Sentinel::findRoleById( $request->user_role );
		$role->users()->attach( $user );   

		if(  $role->name  != 'admin') { 
			
			$user->permissions = [
    			'user.admin' => false,
			]; 

			$user->save(); 
		} 
     	return back()->with('success', 'User created successfully.'); 
   } 
 
}
