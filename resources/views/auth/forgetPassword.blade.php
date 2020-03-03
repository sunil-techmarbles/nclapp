@extends('layouts.appmainlayout')
@section('title' , 'login')
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
		<form class="form-horizontal" role="form" method="POST" action="{{route('sendPasswordResetEmail')}}"> 
			<h1>Send Password Reset Email</h1> 
			@csrf  
			<div class="form-group text-left">  
				<label for="email">Email:</label>
				<input type="text" class="form-control" value="{{ old('email') }}" id="email" placeholder="Enter email" name="email">
				@if ($errors->has('email'))
				<span class="text-danger">{{ $errors->first('email') }}</span>
				@endif 
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Submit</button> 
			</div>
		</form>
	</div> 
</div>  
@endsection 