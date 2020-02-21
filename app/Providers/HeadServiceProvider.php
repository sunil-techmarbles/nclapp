<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View; 
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;


class HeadServiceProvider extends ServiceProvider
{
    public function boot()  
    {  
        
        View::composer('layouts.adminheader', function ($view) {
            $view->with( 'user', Sentinel::getUser() ); 
        });  

    }
} 
 