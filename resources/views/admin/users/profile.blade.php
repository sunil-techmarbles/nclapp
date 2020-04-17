@extends('layouts.appadminlayout')
@section('title' , 'Profile')
@section('content') 
<div class="row justify-content-center text-center">
	<table  class="table table-hover">
		<tbody>
			<tr> 
				<td>
					<h3 class="float-left">Profile</h3>
				</td>
				<td class = "float-right">
					<a class=" btn btn-primary btn-sm btn-sm border" href="{{route('users')}}" >Cancel</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="col-6">
		<form method="post" action="{{route('edit.profile')}}">
			@csrf
			<input type="hidden" name="update" value="1">
			<div class="form-group text-left">
				<label for="email">First Name:</label>
				<input type="text" class="form-control" placeholder="Enter First Name" name="fname" value="{{ $profile->first_name }}" required> 
				@if ($errors->has('fname'))
				<span class="text-danger">{{ $errors->first('fname') }}</span>
				@endif
			</div>
			<div class="form-group text-left">
				<label for="email">Last Name:</label>
				<input type="text" class="form-control" placeholder="Enter Last Name" name="lname" value="{{ $profile->last_name }}" > 
				@if ($errors->has('lname'))
				<span class="text-danger">{{ $errors->first('lname') }}</span>
				@endif
			</div>
			<div class="form-group text-left"> 
				<label for="email">Email:</label>
				<input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="{{ $profile->email }}" required> 
				@if ($errors->has('email'))
				<span class="text-danger">{{ $errors->first('email') }}</span>  
				@endif
			</div>
			<div class="form-group text-left"> 
				<label for="username">User Name:</label>
				<input type="text" class="form-control" name="username" id="username" placeholder="Enter username" value="{{ $profile->username }}" required> 
				@if ($errors->has('username'))
				<span class="text-danger">{{ $errors->first('username') }}</span>  
				@endif
			</div>
			<button type="submit" class="btn btn-primary"> Update </button>
		</form>
	</div> 
</div>  
@endsection 