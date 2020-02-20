<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;


class GuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {  

        if( \Route::current()->uri()  == 'register' ) { 
            
            if ( $user = Sentinel::check() ) { 
                $slug = Sentinel::getUser()->roles()->first()->slug;  

                if ($slug == 'admin') { 
                    return $next( $request ) ;   
                } else {
                     return redirect()->back()->with(['error' => 'You have no permission to access this page.']);  
                }
            } else { 
                return redirect()->back()->with(['error' => 'You have no permission to access this page.']); 
            } 

        } else if (  \Route::current()->uri()  == 'admin/dashboard' ) {   

            if ( Sentinel::guest() ) {    
                return redirect()->route('login.view')->with(['error' => 'You have no permission to access this page.']); 
            } 

        } else {

            if ( Sentinel::guest() ) {    
           
                // user not logged in ..
                return $next( $request ) ;  
      
            } else {   

                $user = Sentinel::getUser();  
                $slug = Sentinel::getUser()->roles()->first()->slug;  

                if ($slug == 'admin') {  
                   return redirect()->route('dashboard'); 
                } else { 

                  return redirect()->back()->with(['error' => 'You have no permission to access this page.']);  
      
                }
    
            }

        } 
        return $next( $request );
    }

}
