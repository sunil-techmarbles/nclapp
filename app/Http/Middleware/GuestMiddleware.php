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
        
        // $user = Sentinel::findUserById(1);
        // Sentinel::logout($user);

        if ( Sentinel::guest() ) {  
      
            return $next( $request ) ; 
      
        } else { 

            // $user = Sentinel::findUserById(1);

            // Sentinel::logout( $user ); 
          
            //return back()->with('401', 'You have no permission to access this page.');
            return abort(401); 

        }

        return $next( $request );

    }


}
