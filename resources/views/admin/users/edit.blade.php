@extends('layouts.appadminlayout')
@section('title' , 'Edit User')
@section('content') 

<div class="row justify-content-center text-center">
	<div class="col-6">
		<h3 class="text-center">Edit User</h3>
		<form method="post" action="{{route('edit.edituserHandle' , $user->id )}}">   
			@csrf  
			<div class="form-group text-left">
				<label for="email">First Name:</label>
				<input type="text" class="form-control" placeholder="Enter First Name" name="fname" value="{{ $user->first_name }}"> 
				@if ($errors->has('fname'))
                	<span class="text-danger">{{ $errors->first('fname') }}</span>
            	@endif
			</div>

			<div class="form-group text-left">
				<label for="email">Last Name:</label>
				<input type="text" class="form-control" placeholder="Enter Last Name" name="lname" value="{{ $user->first_name }}" > 
				@if ($errors->has('lname'))
                	<span class="text-danger">{{ $errors->first('lname') }}</span>
            	@endif
			</div>

			<div class="form-group text-left"> 
				<label for="email">Email:</label>
				<input type="email" readonly class="form-control" id="email" placeholder="Enter email" name="email" value="{{ $user->email }}"> 
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
      						<option {{ $user->roles()->get()[0]->name == $role->name ? 'selected="selected"' : '' }} value="{{$role->id}}"> {{$role->name}} </option> 
    				 @endforeach 
				</select> 
			</div> 

			<button type="submit" class="btn btn-primary"> Edit </button> 
		</form>
	</div> 
</div>  
@endsection 