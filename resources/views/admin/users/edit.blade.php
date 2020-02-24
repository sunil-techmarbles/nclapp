@extends('layouts.appadminlayout')
@section('title' , 'Edit User')
@section('content') 

<div class="row justify-content-center text-center">

	<table  class="table table-hover">
		<tbody>
			<tr> 
				<td class = "">
					<h3 class="float-left">Edit User</h3>
				</td>
				<td class = "float-right">
					<a class=" btn btn-primary btn-sm btn-sm border" href="{{route('users')}}" >Cancel</a>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="col-6">
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
				<input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="{{ $user->email }}"> 
				@if ($errors->has('email'))
				<span class="text-danger">{{ $errors->first('email') }}</span>  
				@endif
			</div>   

			<div class="form-group text-left">
				<label for="sel1">Role:</label>
				<select class="form-control" name="user_role">  
					@foreach($roles as $role)
					<option {{ $user->role_id == $role->id ? 'selected="selected"' : '' }} value="{{$role->id}}"> {{$role->name}} </option> 
					@endforeach 
				</select>  
			</div> 

			<button type="submit" class="btn btn-primary"> Update </button>
		</form>
	</div> 
</div>  
@endsection 