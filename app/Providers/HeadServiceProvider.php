<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View; 
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\User;  

class HeadServiceProvider extends ServiceProvider {
    
    public function boot()
    {
        View::composer('layouts.adminheader', function ($view)
        {
        	$uid = Sentinel::getUser()->id;
        	$userDetails = User::getUserDetail($uid);
            $currentUser = Sentinel::getUser()->first_name.' '.Sentinel::getUser()->last_name;
        	$role = Sentinel::findRoleById($userDetails->role_id);
            $view->with('user_role', $role->slug);
            $view->with('currentUser', $currentUser);
        });
    }
}
 