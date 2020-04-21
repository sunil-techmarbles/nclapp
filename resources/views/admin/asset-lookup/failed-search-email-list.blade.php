@extends('layouts.appadminlayout')
@section('title', 'Failed Search Report Email')
@section('content')
<div class="container">
	<div class="table-responsive">
		<h6 align="center"><strong>If you want to get Search Result Report access on many email, please add another email by putting , (comma)</strong></h6>
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Emails</th>
					<th>Email Type</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($emails as $key => $email)
				<tr>
					<td>{{$key+1}}</td>
					<td>{{$email->email}}</td>
					<td>{{$email->type}}</td>
					<td>
                        <a href="javascript:void(0)" class="edit_faildsearch_address" data-table_type="{{$email->type}}">
                            <img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
                        </a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@include('admin.asset-lookup.modal',['result' => []]);
@endsection