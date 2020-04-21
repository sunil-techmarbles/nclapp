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
<table id="users-table" class="table table-bordered table-striped">
	<thead>
		<th></th> 
		<th>User ID</th>
		<th>User name</th>
		<th>Email</th>
		<th>User Name</th>
		<th>Status</th>
		<th>Role</th>
	</thead>
</table>
@endsection
