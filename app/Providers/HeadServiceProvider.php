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
            $currentUser = Sentinel::getUser()->first_name;
            $role = Sentinel::findRoleById($userDetails->role_id);
            $user = Sentinel::getUser();
            $adminAccess = false;
            if ($user->inRole('admin'))
            {
                $adminAccess = true;
            }
            $view->with('user_role', $role->slug);
            $view->with('currentUser', $currentUser);
            $view->with('adminAccess', $adminAccess);
        });

        View::composer('layouts.appadminlayout', function ($view)
        {
            $redirect = URL('/');
            $logo = URL('assets/images/logo_itamg.png');
            $title = 'ITAMG';
            $user = Sentinel::getUser();
            $adminAccess = false;
            if ($user->inRole('admin'))
            {
                $adminAccess = true;
            }
            if(request()->get('pageaction') == 'itamgconnect'||
            request()->segment(count(request()->segments())) == 'itamgdashboard'){
                $redirect = route('dashboard.itamg',['pageaction'=>request()->get('pageaction')]);
                $title = 'ITAMG';
                $logo = URL('assets/images/logo_itamg.png');
            }
            else if(request()->get('pageaction') == 'refurbconnect'||
            request()->segment(count(request()->segments())) == 'refurbconnectdashboard'){
                $redirect = route('dashboard.refurbconnect',['pageaction'=>request()->get('pageaction')]);
                $logo = URL('assets/images/rc-logo-vertical.png');
                $title = 'Refurb Connect';
            }
            $view->with('redirect', $redirect);
            $view->with('logo', $logo);
            $view->with('title', $title);
            $view->with('adminAccess', $adminAccess);
        });
    }
}