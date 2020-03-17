@extends('layouts.appmainlayout')
@section('title' , 'Reset Password')
@section('content')

@if (Session('error'))
<div class="alert alert-danger alert-dismissible">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> 
	<strong>{{ Session::get('error') }}</strong>
</div>
@endif   

@if (Session('success')) 
<div class="alert alert-success alert-dismissible">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<strong>{{ Session::get('success') }}</strong> 
</div>
@endif 

<div class="row justify-content-center text-center"> 
	<div class="col-6">
		<form class="form-horizontal" role="form" method="POST" action="{{ route('resetPassword' ) }}"> 
			<h1>Password Reset</h1>   
			@csrf  
			
			<div class="form-group text-left">  
				<label for="email">New Password:</label>
				<input type="password" class="form-control"  id="newpassword" placeholder="Enter Password" name="newpassword">
				@if ($errors->has('newpassword'))
					<span class="text-danger">{{ $errors->first('newpassword') }}</span> 
				@endif 
			</div>  
			
			<div class="form-group text-left">    
				<label for="email">Confirm Password:</label>
				<input type="password" class="form-control" id="confirmpassword" placeholder="Re-Enter Password" name="confirmpassword"> 
				@if ($errors->has('confirmpassword'))
					<span class="text-danger">{{ $errors->first('confirmpassword') }}</span>
				@endif 
			</div>

			<input type="hidden" class="form-control" value="{{ $token }}" placeholder="Re-Enter Password" name="token"> 

			<div class="form-group">
				<button type="submit" class="btn btn-primary">Reset Password</button>   
			</div>
		</form>
	</div> 
</div>   
@endsection 