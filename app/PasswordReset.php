<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class PasswordReset extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
	    'email',		
		'token',
	];

	public static function AddForgetPasswordToken($user)
	{
    	//create a new token to be sent to the user. 
		$satus = false;
    	$passwordreset = new PasswordReset();
    	$passwordreset->email = $user->email;
		$passwordreset->token = Str::random(60);
		if($passwordreset->save())
		{
			$satus = $passwordreset;
		}
		return $satus;
	}

	public static function ValidatePasswordResetToken($token)
	{
		$token_data = self::where('token','=',$token)
						->first();
		return (isset($token_data->email) && !empty( $token_data->email)) ? true : false ;
	}

	public static function ResetUserPassword($newPassword, $token)
	{   
		$user_data =  self::where('token', '=' , $token )
							->first();
		$getusercredentials = ['login' => $user_data->email];
		$user = Sentinel::findByCredentials($getusercredentials);
		$updatepasswordcredentials = ['password' => $newPassword];
		$user = Sentinel::update($user, $updatepasswordcredentials);
		return ($user) ? true : false;
	} 

	public static function RemovePasswordResetToken($token)
	{
		return self::where('token','=',$token)
						->delete();
	}
}