<?php
namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends EloquentUser {
	use SoftDeletes; 
	protected $dates = ['deleted_at'];

	public static function checkEmailExits( $email='' )
	{
		$exits = self::where([ 'email' => $email ])->first();
		return ( $exits ) ? true : false ;
	}

	public static function getUserDetail($id)
	{
		return $data = self::where('users.id' , $id )
		->join('role_users', 'role_users.user_id', '=', 'users.id')
		->select('*')
		->first();
	}
	
	public static function deleteUserByID($uid)
	{
		$user = self::find($uid);
		return ($user->delete()) ? true : false;
	}
}