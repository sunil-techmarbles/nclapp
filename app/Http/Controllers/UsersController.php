<?php

namespace App\Http\Controllers;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request; 
use App\User;  


class UsersController extends Controller
{ 

	public $roles;  

	public function __Construct(){
		$this->roles = \DB::table('roles')->select( 'name' , 'id' )->get(); 
	}


	public function index() {   
		$users = User::all();   
		return view( 'admin.users.list' , compact('users') ); 
	}   

	public function edituser( $id ){    
		$user = User::getUserDetail( $id );      
		return view( 'admin.users.edit' , compact('user') )->with(['roles' => $this->roles]);  
	}  

	public function edituserHandle( $id , Request $request ) { 
		 
		$validator = $request->validate(
				[
					'fname' => 'required|min:2|max:50',
					'lname' => 'required|min:2|max:50',  
					'email' => 'required|unique:users,email,'.$id        
				], 
				[
					'fname.required' => 'First Name is required',
					'fname.min' => 'First Name must be at least 2 characters.',
					'fname.max' => 'First Name should not be greater than 50 characters.',
					'lname.required' => 'Last Name is required',
					'lname.min' => 'Last Name must be at least 2 characters.',
					'lname.max' => 'Last Name should not be greater than 50 characters.',
				]  
			); 

			try { 	  

				$user_data = [ 
				    'first_name' => $request->fname ,
				    'last_name' => $request->lname ,
				    'email'    => $request->email, 
		     	]; 
		    
		 		$user = User::findorfail( $id );    
				$user = Sentinel::update( $user, $user_data );  

				$role = Sentinel::findRoleById( $user->roles()->get()[0]->id );  
				$role->users()->detach($user);

				$role = Sentinel::findRoleById( $request->user_role );  
				$role->users()->attach( $user );  

			 	return redirect()->route('users')->with('success', 'User Updated successfully.'); 

			 } catch( Exception $error ) { 
 
			 	return redirect()->route('users')->with('success', 'Error occred Please try again.'); 
		}  

	}	

	public function DeleteUser( $id ) {  

		dd( $id );   

		// $user = Sentinel::findById( $id );
		// $user->delete(); 
		// return redirect()->route('users')->with('success', 'User Deleted successfully.'); 

	}


}
