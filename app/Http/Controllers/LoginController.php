<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Validator;
use Redirect; 
use Illuminate\Validation\Rule;
use App\User;    
use App\PasswordReset;

class LoginController extends Controller  { 

    public function index() { 
        return view('auth.login');  
    } 

    public function forgetPassword(){ 
        return view('auth.forgetPassword'); 
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

    public function sendPasswordResetEmail( Request $request ) {     
        $validator = $request->validate(
            [ 
            'email'    => 'required|email|exists:users,email' 
            ],  
            [
            'email.exists' => 'User not found with this email',
            ]
            );   

        $email = $request->email; 
        $user = User::checkEmailExits( $email );        

        if( $user ) {       

            $resetEmailSent = $this->CreateTokenForResetPassword( $user );   

            if( $resetEmailSent ) { 
                return redirect()->back()->with(['success' => 'Password Reset Email has been sent.']);
            } else {
                return redirect()->route('forgetPassword')->with(['error' => 'Please try again.']);
            }
        } else {
            return redirect()->back()->with(['error' => 'User not found with this email.']);
        } 

    } 


    public function CreateTokenForResetPassword( $user ) { 

        $token_data = PasswordReset::AddForgetPasswordToken( $user );    
        
        if(!empty($token_data->token))
        {              
            $reset_url = url( 'ResetPasswordForm', $token_data->token ); 
            $this->sendEmail( $user , $reset_url ); 
            return true;  
        }  
        else
        {  
            return false;
        } 
    }  


    public function sendEmail( $user , $reset_url  ) {

        $subject = "Reset Password Mail";
        $email = $user->email;  
        $body = "";
 
        Mail::raw( "Reset password " . $reset_url , function ( $m ) use ( $subject ,  $email ) { 
             $m->to(  $email ) 
            ->subject($subject);    
        });      
    } 


    public function resetPasswordForm( $token ) {           

        $checktoken = PasswordReset::ValidatePasswordResetToken( $token );  

        if($checktoken)
        {   
            return view( 'auth.resetPassword' , compact('token') );   
        } 
        else 
        {  
            PasswordReset::RemovePasswordResetToken( $token );
            return redirect()->route('forgetPassword')->with(['error' => 'Token Has been expired , Please try again.']); 
        } 
    }  

    public function resetPassword( Request $request  ) {  

        $validator = $request->validate(
            [ 
                'newpassword'    => 'required|min:6' ,
                'confirmpassword' => 'required|min:6|max:20|same:newpassword'
            ] 
            );       

        $newpassword = $request->newpassword;  

        $resetUserPassword = PasswordReset::ResetUserPassword( $request->newpassword , $request->token );   

        PasswordReset::RemovePasswordResetToken( $request->token );    

        if($resetUserPassword) 
        {    
            return redirect('login')->with(['success' => 'Password Reset Successfully , Please login.']);   
        } 
        else 
        { 
            return redirect('login')->with(['error' => 'Some error Occured Please try again.']);
        }

    }

}
