<?php
namespace App\Http\Controllers;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

use Illuminate\Http\Request;
use App\User;

class RegisterController extends Controller
{
    public function index()
    {
        $userPreDefinedRoles = config('userconstants.roles');
        foreach ($userPreDefinedRoles as $key => $value)
        {
            $data = [
                'slug' => $key,
                'name' => $value,
            ];
            $checkRoleExist = \DB::table('roles')->where('slug',$key)->first();
            if(!$checkRoleExist)
            {
                \DB::table('roles')->insert($data);
            }
        }
        $roles = \DB::table('roles')->select('name', 'id')->get();
        return view('auth.register', compact("roles"));
    }

    public function registerAuthenticate(Request $request)
    {
        $validator = $request->validate(
            [
                'fname' => 'required|min:2|max:50',
                'lname' => 'required|min:2|max:50',
                'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'username' => 'required|string|min:8|max:20|unique:users,username,NULL,id,deleted_at,NULL',
                'password' => 'required|alphaNum|min:6|max:14|',
                'confirm_password' => 'required|min:6|max:14|same:password',
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
        $isExists = User::checkEmailExits($request->input('email'));
        if($isExists)
        {
            return redirect()->back()->with(['error' => 'That email address is already registered.']);
        }
        $userData = [
            'first_name' => $request->fname ,
            'last_name' => $request->lname ,
            'email'    => $request->email,
            'username' => $request->username,
            'password' => $request->password,
        ];
        $user = Sentinel::registerAndActivate( $userData );
        $role = Sentinel::findRoleById( $request->user_role );
        $role->users()->attach( $user );
        if($role->name != 'admin')
        {
            $user->permissions = [
                'user.admin' => false,
            ];
            $user->save();
        }
        return redirect()->route('users')->with('success', 'User created successfully.');
    }
}