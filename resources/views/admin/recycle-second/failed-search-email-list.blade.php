@extends('layouts.appadminlayout')
@section('title', 'Failed Search Email')
@section('content')
<div class="container">
	<h3 align="center"><strong>Failed Search Report Emails</strong></h3>
	<div class="table-responsive">
		<h6 align="center"><strong>If you want to get Search Result Report access on many email, please add another email by putting , (comma)</strong></h6>
		<a href="{{route('recycle.second')}}" class="btn btn-default border border-success float-right mb-2">Back</a>
		<table id="itamg_inventory_value" class="table table-bordered table-striped">
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
@include('admin.recycle-second.modal',['result' => []]);
@endsection