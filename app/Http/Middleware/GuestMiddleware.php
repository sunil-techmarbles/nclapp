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
        if ( Sentinel::guest() ) {  
            return $next( $request ) ; 
        } else { 
            return abort(401); 
        }
        return $next( $request );

    }


}
