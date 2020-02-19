@extends('layouts.appmainlayout')
@section('title' , 'Add User')
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
		<h3 class="text-center">Register User</h3>
		<form method="post" action="{{route('register.registerAuthenticate')}}">  
			@csrf  
			
			<div class="form-group text-left">
				<label for="email">First Name:</label>
				<input type="text" class="form-control" placeholder="Enter First Name" name="fname" value="{{ old('fname') }}"> 
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
				<input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="{{ old('email') }}"> 
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

			<div class="form-group text-left">
				<label for="pwd">Confirm Password:</label> 
				<input type="password" class="form-control" id="pwd" placeholder="Enter password" name="confirm_password">
				@if ($errors->has('confirm_password'))
                	<span class="text-danger">{{ $errors->first('confirm_password') }}</span> 
            	@endif
			</div>

			<div class="form-group text-left"> 
				<label for="sel1">Role:</label>
				<select class="form-control" name="user_role">  
					 @foreach($roles as $role)
      					<option value="{{$role->id}}">{{$role->name}}</option>
    				 @endforeach 
				</select> 
			</div> 

			<button type="submit" class="btn btn-primary"> Register </button> 
		</form>
	</div> 
</div>  
@endsection 