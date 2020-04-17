@extends('layouts.appadminlayout')
@section('title' , 'Users List')
@section('content')
<table  class="table table-hover">
	<tbody>
		<tr>
			<td class = "float-right">
				<a class="float-right btn btn-primary btn-sm btn-sm border" href="{{route('user.add')}}" >Add User</a>
			</td>
		</tr>
	</tbody>
</table>
<table id="users_table" class="table table-hover">
	<thead>
		<th></th> 
		<th>User ID</th>
		<th>User name</th>
		<th>Email</th>
		<th>User Name</th>
		<th>Status</th>
		<th>Role</th>
	</thead>
	<tbody>
		@foreach($users as $user)
		<tr>
			<td nowrap="">
					<a href="javascript:void(0)" 
					onclick="del_confirm({{$user->id}} ,'DeleteUser','User')">
						<img src="{{URL('/assets/images/del.png')}}" class="icons" title="Delete">
					</a>
					<a href="{{route('edit.user', $user->id )}}">
						<img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
					</a>
					<a href="{{route('change.passowrd',['u' => $user->id])}}" class="mt-1 " title="Change Passowrd">
						<img src="{{URL('/assets/images/shield.png')}}" class="icons" title="Change Passowrd">
					</a>
				</td>
			<td>{{$user->id}}</td>
			<td>{{$user->first_name}} {{$user->last_name}} </td>
			<td>{{$user->email}}</td>
			<td>{{$user->username}}</td>
			<td>
				<span class="{{userVerifiedClass($user->verified)}}">{{userVerifiedString($user->verified)}}
				</span>
				@if(!$user->verified)
					<a href="javascript:void(0)" onclick="verifyuser({{$user->id}},{{$user->verified}},'verifyuser')">
						<i class="fa fa-check" aria-hidden="true"></i>
					</a>
				@endif
			</td>
			<td>{{$user->role}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endsection
