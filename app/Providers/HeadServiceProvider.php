<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View; 
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\User;  


class HeadServiceProvider extends ServiceProvider {
    
    public function boot()   {  
        View::composer('layouts.adminheader', function ($view) {
        	$uid = Sentinel::getUser()->id;
        	$user_details = User::getUserDetail( $uid );     
        	$role = Sentinel::findRoleById( $user_details->role_id  );  
            $view->with( 'user_role', $role->slug ); 
        });  
    }
    
} 
 