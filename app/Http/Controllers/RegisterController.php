<?php
namespace App\Http\Controllers;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\User;
use App\UserCronJob;

class RegisterController extends Controller
{
    public function index(Request $request)
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
        if($request->t == 'new')
        {
            return view('auth.new-register');
        }
        else
        {
            $routeName = $request->route()->getName();
            if($routeName == 'register')
            {
                return redirect()->route('register', ['t' => 'new']);
            }
            else
            {
                return view('auth.register', compact("roles"));
            }
        }
    }

    public function registerAuthenticate(Request $request)
    {
        $validator = $request->validate([
            'fname' => 'required|min:2|max:50',
            'lname' => 'required|min:2|max:50',
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'username' => 'required|string|min:8|max:20|unique:users,username,NULL,id,deleted_at,NULL',
            'password' => 'required|alphaNum|min:6|max:14|',
            'confirm_password' => 'required|min:6|max:14|same:password',
        ],[
            'fname.required' => 'First Name is required',
            'fname.min' => 'First Name must be at least 2 characters.',
            'fname.max' => 'First Name should not be greater than 50 characters.',
            'lname.required' => 'Last Name is required',
            'lname.min' => 'Last Name must be at least 2 characters.',
            'lname.max' => 'Last Name should not be greater than 50 characters.',
            'email.unique' => 'That email address is already registered.',
        ]);
        $isExists = User::checkEmailExits($request->input('email'));
        if($isExists)
        {
            return redirect()->back()->with(['error' => 'That email address is already registered.']);
        }
        $userData = [
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email'    => $request->email,
            'username' => $request->username,
            'password' => $request->password,
            'verified' => 1
        ];
        if($request->type == 'new')
        {
            $checkRoleExist = \DB::table('roles')->where('slug','user')->first();
            $request->merge(['user_role' => $checkRoleExist->id]);
            $userData['verified'] = 0;
        }
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
        if($request->type == 'new')
        {            
            $e_mails = [];
            $emails = UserCronJob::getCronJobUserEmails('newUser');
            if($emails->count() > 0)
            {
                foreach ($emails as $key => $value) {
                    $e_mails[] = $value->email;
                }
            }
            $emails = ($emails->count() > 0) ? $e_mails : config('constants.userConfirmAdminEmail')
            $subject = 'New User';
            $data = [
                'name' => $request->fname.''.$request->lname,
                'email' => $request->email,
                'username' => $request->username,
                'link' => route('verify.user',['type' => 'email', 'userid'=> $user->id, 'status'=> $user->verified]),
            ];
            Mail::send('admin.emails.newuser', $data, function ($m) use ($subject, $emails) {
                $m->to($emails)->subject($subject);
            });
            return redirect()->back()->with('success', 'Register successfully');
        }
        else
        {
            return redirect()->route('users')->with('success', 'User created successfully.');
        }
    }
}