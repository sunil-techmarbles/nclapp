@extends('layouts.appadminlayout')
@section('title' , 'Users List')
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

<table class="table table-hover">
	<thead>
		<th>User ID</th>
		<th>User name</th>
		<th>Email</th>
		<th>Role</th>
		<th>Action</th> 
	</thead> 
	<tbody> 
		@foreach( $users as $user )
		<tr> 
			<td>{{$user->id}}</td>
			<td>{{$user->first_name}} {{$user->last_name}} </td> 
			<td>{{$user->email}}</td> 
			<td> {{ $user->roles()->get()[0]->name }} </td>   
			<td>
				<a href = "{{route('edit.user', $user->id)}}" > <button class="btn btn-primary"> Edit </button> </a>
				<a href = "{{route('softDelete.user', $user->id)}}" ><button class="btn btn-danger"> Delete </button></a>  
			</td>
		</tr>
		@endforeach 
	</tbody>
</table>
@endsection
