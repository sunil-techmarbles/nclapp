@extends('layouts.appadminlayout')
@section('title' , 'change Password')
@section('content')
<div class="row justify-content-center text-center">
	<table  class="table table-hover">
		<tbody>
			<tr> 
				<td class = "">
					<h3 class="float-left">Update Password</h3>
				</td>
				<td class = "float-right">
					<a class=" btn btn-primary btn-sm btn-sm border" href="{{route('users')}}" >Cancel</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="col-6">
		<form method="post" action="{{route('change.passowrd')}}">
			<input type="hidden" name="u" value="{{$userDetial->id}}">
			<input type="hidden" name="t" value="{{request()->get('t')}}">
			@csrf  
			<!-- <div class="form-group text-left"> 
				<label for="email">Email:</label>
				<input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="{{ old('email', $userDetial->email) }}"> 
				@if ($errors->has('email'))
                	<span class="text-danger">{{ $errors->first('email') }}</span>
            	@endif
			</div> -->
			@if(request()->get('t') == 1)
			<div class="form-group text-left">
				<label for="oldpwd">Old Password:</label>
				<input type="password" required class="form-control" id="oldpwd" placeholder="Old password" name="oldpassword">
				@if ($errors->has('oldpassword'))
                	<span class="text-danger">{{ $errors->first('oldpassword') }}</span>
            	@endif
			</div>
			@endif
			<div class="form-group text-left">
				<label for="pwd">Password:</label>
				<input type="password" required class="form-control" id="pwd" placeholder="New password" name="password">
				@if ($errors->has('password'))
                	<span class="text-danger">{{ $errors->first('password') }}</span>
            	@endif
			</div>
			<div class="form-group text-left">
				<label for="pwd">Confirm Password:</label> 
				<input type="password" required class="form-control" id="confirm-pwd" placeholder="Confirm password" name="confirm_password">
				@if ($errors->has('confirm_password'))
                	<span class="text-danger">{{ $errors->first('confirm_password') }}</span>
            	@endif
			</div>
			<button type="submit" class="btn btn-primary">Update Password</button>
		</form>
	</div>
</div>
@endsection