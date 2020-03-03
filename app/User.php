<?php
namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser; 
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 

class User extends EloquentUser {

	use SoftDeletes; 

	protected $dates = ['deleted_at'];

	public static function checkEmailExits( $email='' ) {
		$isCheck = false;
		$exits = self::where([ 'email' => $email ])->first(); 
		if( $exits ) { 
			return $exits;
		}     
		return $isCheck;  
	} 

	public static function getUserDetail( $id ) { 
		return $data = self::where('users.id' , $id )
		->join('role_users', 'role_users.user_id', '=', 'users.id')
		->select('*')
		->first();
	}
	
	public static function deleteUserByID( $uid ) {
		$result = false;
		$user = self::find($uid);
		if($user->delete())
		{
			$result = true;
		}

		return $result;
	}

	public static function AddForgetPasswordToken( $user ) { 
    	//create a new token to be sent to the user.  
		return DB::table('password_reset')->insert([ 
					'user_id' => $user->id,
					'email' => $user->email, 
					'token' => Str::random(60),
					'created_at' => now()
				]);   
	} 

	public static function GetForgetPasswordUrl( $user ) {
		$tokenData = DB::table('password_reset') 
					->where( array( 'email' => $user->email , 'user_id' => $user->id  ) )->first(); 
		if( isset( $tokenData->token  )  && !empty( $tokenData->token  )) {
			return 	$tokenData->token; 
		} else { 
			return 	false; 
		} 
	} 

	public static function ValidatePasswordResetToken( $token ){ 
		$token_data = DB::table('password_reset')
						->where('token','=',$token)
	        			// ->where( 'created_at','>', Carbon::now()->subHours(2) )
						->first(); 

		if( isset( $token_data->email ) && !empty( $token_data->email ) ) {
			return true ; 
		} else {  
			return false ; 
		}  
	}  

	public static function ResetUserPassword( $newPassword , $token ) { 

		$user_data =   DB::table('password_reset')
							->where('token', '=' , $token )  
	        				// ->where( 'created_at','>', Carbon::now()->subHours(2) )
							->first();   

		$user = Sentinel::findById( $user_data->user_id );

		$credentials = [
			'password' => $newPassword ,
		]; 

		$user = Sentinel::update( $user, $credentials ); 

		if(  $user ) {  
			return true ;  
		}

		return false ; 

	}

	public static function RemovePasswordResetToken( $token ){

		return DB::table('password_reset') 
						->where('token','=',$token)
						->delete(); 

	}








}
