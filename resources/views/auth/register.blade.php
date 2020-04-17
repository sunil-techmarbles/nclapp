@extends('layouts.appadminlayout')
@section('title' , 'Add User')
@section('content') 

<div class="row justify-content-center text-center">
	<table  class="table table-hover">
		<tbody>
			<tr> 
				<td class = "">
					<h3 class="float-left">Add User</h3>
				</td>
				<td class = "float-right">
					<a class=" btn btn-primary btn-sm btn-sm border" href="{{route('users')}}" >Cancel</a>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="col-6">
		<form method="post" action="{{route('register.registerAuthenticate')}}">  
			@csrf
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
			<div class="form-group text-left"> 
				<label for="sel1">Role:</label>
				<select class="form-control" name="user_role">  
					 @foreach($roles as $role)
      					<option value="{{$role->id}}">{{$role->name}}</option>
    				 @endforeach 
				</select> 
			</div>
			<button type="submit" class="btn btn-primary"> Save </button> 
		</form>
	</div> 
</div>  
@endsection 