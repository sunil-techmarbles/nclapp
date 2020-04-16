<?php
namespace App\Http\Controllers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\User;
use App\UserCronJob;

class UsersController extends Controller
{
	public $roles;

	public function __Construct()
	{
		$this->roles = \DB::table('roles')->select( 'name' , 'id' )->get();
	}

	public function index()
	{
		$users = User::all();
		foreach ($users as $key => $value) {
			$role = Sentinel::findById($value->id)->roles()->get();
			foreach ($role as $rolekey => $rolevalue) {
				$users[$key]['role'] = isset($rolevalue->slug) ? $rolevalue->slug : 'N/A';
			}
		}
		return view('admin.users.list', compact('users'));
	}

	public function verifyUser(Request $request)
	{
		$userid = intval($request->userid);
		$status = $request->status;
		switch ($status) {
			case 0:
				$s = 1;
				$m = 'verified';
				$t = '';
				break;
			case 1:
				$s = 0;
				$m = 'unverified';
				$t = '';
				break;
		}
        $result = User::changeUserStatus($userid,$s);        
        if ($result)
        {
        	$userDetail = User::getUserDetail($userid);
        	$subject = 'Verified User';
        	$data['name'] = $userDetail['username'];
        	$email = $userDetail['email'];
        	Mail::send('admin.emails.verifyemail', $data, function ($m) use ($subject, $email) {
                $m->to($email)->subject($subject);
            });
            $response['status']  = true;
            $response['message'] = 'user '.$m.' successfully';
        }
        else
        {
            $response['status']  = false;
            $response['message'] = 'Something went wrong';
        }
        if($request->type)
        {
        	$icon  = ($response['status']) ? 'success' : 'error';
        	return redirect()->route('login.view')->with($icon,$response['message']);
        }
        else
        {
        	return response()->json($response);
        }
	}

	public function edituser($Userid)
	{
		$cronjobs = config('cronjob.cronJobList');
		$user = User::getUserDetail($Userid);
		if($user)
		{
			return view('admin.users.edit', compact('user', 'cronjobs'))->with(['roles' => $this->roles]);
		}
		abort('404');
	}

	public function manageEmail(Request $request)
    {
    	$cronjobTypes = config('cronjob.cronJobType');
    	if($request->isMethod('post'))
    	{
    		$cronjobname = $request->cronjobname;
			if($request->cronjob)
			{
				$cronjobs = $request->cronjob;
				UserCronJob::deleteRecord($cronjobname);
				foreach ($cronjobs as $key => $value)
				{
					$data = [
					'user_id' => $value,
					'cron_job' => $cronjobname,
					'status' => 1,
					];
					UserCronJob::addRecord($data);
				}
	    	}
	    	return redirect()->route('manage.emails')->with('success', 'Record added successfully ');
	   	}
	   	else
	   	{
	   		if($request->a)
	   		{
	   			$action = $request->a;
	   			switch ($action)
	   			{
	   				case 'add':
	   				$view = 'add-cron-email';
	   				break;
	   				case 'view':
	   				$view = 'view-cron-email';
	   				break;
	   			}
	   			$name = $cronjobTypes[$request->t];
	   			$result = UserCronJob::getCronJobName($request->t);
	   			$result = $result->toArray();
	   			$userEmails = User::getAllUserEmails();
	   			return view('admin.users.'.$view, compact('result','name','userEmails'));
	   		}
	   		else
	   		{
	   			return view('admin.users.manage-email', compact('cronjobTypes'));
	   		}
	   	}
    }

	public function edituserHandle(Request $request, $Userid)
	{
		$validator = $request->validate([
			'fname' => 'required|min:2|max:50',
			'lname' => 'required|min:2|max:50',
			'email' => 'required|unique:users,email,'.$Userid.',id,deleted_at,NULL',
			'username' => 'required|unique:users,username,'.$Userid.',id,deleted_at,NULL',
		],[
			'fname.required' => 'First Name is required',
			'fname.min' => 'First Name must be at least 2 characters.',
			'fname.max' => 'First Name should not be greater than 50 characters.',
			'lname.required' => 'Last Name is required',
			'lname.min' => 'Last Name must be at least 2 characters.',
			'lname.max' => 'Last Name should not be greater than 50 characters.',
		]);

		try {
			$userData = [
			    'first_name' => $request->fname ,
			    'last_name'  => $request->lname ,
			    'email'      => $request->email,
			    'username'   => $request->username,
	     	];
	 		$user = User::findorfail($Userid);
			$user = Sentinel::update($user, $userData);
			$role = Sentinel::findRoleById( $user->roles()->get()[0]->id );
			$role->users()->detach($user);
			$role = Sentinel::findRoleById( $request->user_role );
			$role->users()->attach( $user );
		 	return redirect()->route('users')->with('success', 'User Updated successfully.');
		}
		catch(Exception $error)
		{
		 	return redirect()->route('users')->with('success', 'Error occred Please try again.');
		}
	}

	public function changePassowrd(Request $request)
	{
		$userId = $request->u;
		$userDetial = User::findorfail($userId);
		if ($request->isMethod('post'))
		{
			$validator = $request->validate(
	      	[
	            'password' => 'required|alphaNum|min:6|max:14|',
	            'confirm_password' => 'required|min:6|max:20|same:password',
	    	]);
	    	$user = Sentinel::findUserById($userId);
	        Sentinel::update($user, array('password' => $request->password));
	        Sentinel::logout($user);
	        return redirect()->route('users')->with('success', 'Password updated successfully.');
		}
		else
		{
			return view('admin.users.changepassword', compact('userDetial'));
		}
		abort('404');
	}

	public function DeleteUser(Request $request, $UserID)
	{
	  	$uid = intval($UserID);
        $result = User::deleteUserByID($uid);
        if ($result)
        {
            $response['status'] = 'success';
            $response['message'] = 'User deleted successfully';
        }
        else
        {
            $response['status'] = 'error';
            $response['message'] = 'Unable to delete user';
        }
        return response()->json($response);
	}
}