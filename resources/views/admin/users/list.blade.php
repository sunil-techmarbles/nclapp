@extends('layouts.appadminlayout')
@section('title' , 'Users List')
@section('content') 

<table  class="table table-hover"> 
	<tbody>
		<tr>
			<td class = "float-right">
				<a class="float-right btn btn-primary btn-sm btn-sm border" href="{{route('user.register')}}" >Add User</a>
			</td>
		</tr>
	</tbody>
</table>

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
				<small><a href = "{{route('edit.user', $user->id)}}" > <button class="btn btn-primary btn-sm"> Edit </button> </a></small>
				<small><a href = "{{route('softDelete.user', $user->id)}}" ><button class="btn btn-danger btn-sm"> Delete </button></a> </small>
			</td>
		</tr>
		@endforeach 
	</tbody>
</table>
@endsection