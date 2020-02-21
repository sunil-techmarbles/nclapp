<?php
namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser; 

class User extends EloquentUser {


	public static function checkEmailExits( $email='' ) {
		$isCheck = false;
		$exits = self::where([ 'email' => $email ])->first(); 
		if( $exits ) { 
			$isCheck = true;
			return $isCheck;
		}

	}

	public static function getUserDetail( $id ) { 
		return $data = self::where('users.id' , $id )
		       ->join('role_users', 'role_users.user_id', '=', 'users.id')
		       ->select('*')
		       ->first();
	}


}
