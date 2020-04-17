@extends('layouts.appmainlayout')
@section('title' , 'Register')
@section('content')

@if (Session('error'))
<div class="alert alert-danger alert-dismissible">
	<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> 
	<strong>{{ Session::get('error') }}</strong>
</div>
@endif   

@if (Session('success'))
<div class="alert alert-success alert-dismissible">
	<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> 
	<strong>{{ Session::get('success') }}</strong>
</div> 
@endif     

<div class="row justify-content-center text-center"> 
	<div class="col-6">
		<h3 class="text-center">Sign Up</h3>
		<form method="post" action="{{route('register.registerAuthenticate')}}">  
			@csrf
			<input type="hidden" name="type" value="new">
			<div class="form-group text-left">
				<label for="email">First Name:</label>
				<input type="text" class="form-control" placeholder="Enter First Name" name="fname" value="{{ old('fname') }}" required> 
				@if ($errors->has('fname'))
                	<span class="text-danger">{{ $errors->first('fname') }}</span>
            	@endif
			</div>
			<div class="form-group text-left">
				<label for="email">Last Name:</label>
				<input type="text" class="form-control" placeholder="Enter Last Name" name="lname" value="{{ old('lname') }}" > 
				@if ($errors->has('lname'))
                	<span class="text-danger">{{ $errors->first('lname') }}</span>
            	@endif
			</div>
			<div class="form-group text-left"> 
				<label for="email">Email:</label>
				<input type="email" required class="form-control" id="email" placeholder="Enter email" name="email" value="{{ old('email') }}"> 
				@if ($errors->has('email'))
                	<span class="text-danger">{{ $errors->first('email') }}</span>
            	@endif
			</div>
			<div class="form-group text-left"> 
				<label for="username">User Name:</label>
				<input type="text" required class="form-control" id="username" placeholder="Enter username" name="username" value="{{ old('username') }}"> 
				@if ($errors->has('username'))
                	<span class="text-danger">{{ $errors->first('username') }}</span>
            	@endif
			</div>
			<div class="form-group text-left">
				<label for="pwd">Password:</label>
				<input type="password" required class="form-control" id="pwd" placeholder="Enter password" name="password">
				@if ($errors->has('password'))
                	<span class="text-danger">{{ $errors->first('password') }}</span> 
            	@endif
			</div>
			<div class="form-group text-left">
				<label for="pwd">Confirm Password:</label> 
				<input type="password" class="form-control" id="pwd" placeholder="Enter password" name="confirm_password">
				@if ($errors->has('confirm_password'))
                	<span class="text-danger">{{ $errors->first('confirm_password') }}</span> 
            	@endif
			</div>
			<button type="submit" class="btn btn-primary"> Sign Up </button>
			<a class="btn btn-primary" href="{{route('login.view')}}">Sign In</a>
		</form>
	</div> 
</div>  
@endsection 