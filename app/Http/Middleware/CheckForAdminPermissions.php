<?php

namespace App\Http\Middleware;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

use Closure;

class CheckForAdminPermissions
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
            if ( $user = Sentinel::check() ) { 
                $slug = Sentinel::getUser()->roles()->first()->slug;  
                if ($slug == 'admin') {  
                    return $next( $request ) ;     
                } else { 
                    return redirect()->route('dashboard')->with('error', 'You have no permission to access this page.'); 
                }
            } else {
                return redirect()->route('dashboard')->with('error', 'You have no permission to access this page.'); 
            } 
        return $next($request);
    }  

} 
