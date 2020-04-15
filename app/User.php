<?php
namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends EloquentUser {

	protected static function boot()
	{
	    parent::boot();
	    static::deleting(function($usersList){
	        $usersList->userCronJobList()->delete();
	    });
	}
	use SoftDeletes; 
	protected $dates = ['deleted_at'];
	
	protected $fillable = [
        'email',
        'username',
        'password',
        'last_name',
        'first_name',
        'permissions',
    ];

    protected $loginNames = ['email', 'username'];

	public static function checkEmailExits( $email='' )
	{
		$exits = self::where([ 'email' => $email ])->first();
		return ( $exits ) ? true : false ;
	}

	public static function getUserEmailByUserId($value)
	{
		return self::where(['id' => $value])
			->pluck('email')
			->first();
	}

	public static function getUserDetail($id)
	{
		return self::where('users.id' , $id )
			->join('role_users', 'role_users.user_id', '=', 'users.id')
			->select('*')
			->first();
	}

	public static function getAllUserEmails()
	{
		return self::pluck('email','id');
	}
	
	public static function deleteUserByID($uid)
	{
		$user = self::find($uid);
		return ($user->delete()) ? true : false;
	}

	public function userCronJobList()
	{
		return $this->hasMany('App\UserCronJob');
	}
}