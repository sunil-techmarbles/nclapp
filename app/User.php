<?php
namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser; 
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

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

}
