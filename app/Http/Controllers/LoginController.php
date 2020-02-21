<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Validator;
use Redirect;

class LoginController extends Controller  { 

    public function index() { 
        return view('auth.login'); 
    } 

    public function loginAuthenticate( Request $request ) { 
        $validator = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|alphaNum'
        ]);
        $rememberMe = ( $request->rememberMe == '1' ) ? true : false ;  
        try {  
            if ( Sentinel::authenticate( $request->all() , $rememberMe ) ) {
                return redirect()->route('dashboard');      
            } else {   
                return redirect()->back()->with(['error' => 'Wrong Credentials']); 
            } 
        } catch (ThrottlingException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        } catch (NotActivatedException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect('/');   
    } 

    public function logout( Request $request ) {  
        Sentinel::logout();
        return redirect('/');
    }

}
