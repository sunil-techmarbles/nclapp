@extends('layouts.appmainlayout')
@section('title' , 'login')
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
		<h3 class="text-center">Sign In</h3>
		<form method="post" action="{{route('login.authenticate')}}"> 
			@csrf  
			<div class="form-group text-left">
				<label for="username">Email / Username</label>
				<input type="text" class="form-control" value="{{ old('username') }}" id="username" placeholder="Enter email or user name " name="username">
				@if ($errors->has('username'))
				<span class="text-danger">{{ $errors->first('username') }}</span>
				@endif 
			</div>
			<div class="form-group text-left">
				<label for="pwd">Password:</label>
				<input type="password" class="form-control" id="pwd" placeholder="Enter password" name="password">
				@if ($errors->has('password'))
				<span class="text-danger">{{ $errors->first('password') }}</span> 
				@endif 
			</div> 
			<div class="form-group">
				<a class="" href="{{route('forgetPassword')}}" > Lost your password?</a>
			</div> 
			<div class="form-group">
				<input type="checkbox" value="1" name="rememberMe" id="rememberMe"> 
				<label for="rememberMe">Remember me</label>  
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Log In</button> 
			</div>
		</form>
	</div> 
</div>  
@endsection 