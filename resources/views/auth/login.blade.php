@extends('layouts.appmainlayout')
@section('title' , 'login')
@section('content')

@if (Session('error'))
<div class="alert alert-danger alert-dismissible">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> 
	<strong>{{ Session::get('error') }}</strong>
</div>
@endif    

<div class="row justify-content-center text-center"> 
	<div class="col-6">
		<h3 class="text-center">Sign in</h3>
		<form method="post" action="{{route('login.authenticate')}}"> 
			@csrf  
			<div class="form-group text-left">
				<label for="email">Email:</label>
				<input type="email" class="form-control" value="{{ old('email') }}" id="email" placeholder="Enter email" name="email">
				@if ($errors->has('email'))
                	<span class="text-danger">{{ $errors->first('email') }}</span>
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
				<input type="checkbox" value="1" name="rememberMe" id="rememberMe"> 
				<label for="rememberMe">Remember me</label>  
			</div>

			<button type="submit" class="btn btn-primary">Submit</button> 
		</form>
	</div> 
</div>  
@endsection 