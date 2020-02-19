@extends('layouts.appmainlayout')
@section('content')

@if ($errors->any())
<div class="alert alert-danger">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<div class="row justify-content-center text-center">
	<div class="col-6">
		<h3 class="text-center">Sign in</h3>
		<form method="post" action="{{route('login.authenticate')}}"> 
			@csrf  
			<div class="form-group text-left">
				<label for="email">Email:</label>
				<input type="email" class="form-control" id="email" placeholder="Enter email" name="email"> 
			</div>
			<div class="form-group text-left">
				<label for="pwd">Password:</label>
				<input type="password" class="form-control" id="pwd" placeholder="Enter password" name="password">
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