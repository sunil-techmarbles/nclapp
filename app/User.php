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
}
