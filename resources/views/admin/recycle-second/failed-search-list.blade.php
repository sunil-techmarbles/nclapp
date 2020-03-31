@extends('layouts.appadminlayout')
@section('title', 'Failed Searches')
@section('content')
<div class="container">
	<h3 align="center"><strong>Failed Searches</strong></h3>
	<div class="table-responsive">
		<a href="{{route('recycle.second')}}" class="btn btn-default border border-success float-right mb-2">Back</a>
		<table id="itamg_inventory_value" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>Model</th>
					<th>Part No</th>
					<th>Brand</th>       
					<th>Category</th>
					<th>Require PN</th>
					<th>Date Time</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($failedSearches as $key => $failedSearch)
				<tr>
					<td>{{$failedSearch->model_or_part}}</td>
					<td>{{$failedSearch->partNo}}</td>
					<td>{{$failedSearch->Brand}}</td>
					<td>{{$failedSearch->Category}}</td>
					<td>{{$failedSearch->require_pn}}</td>
					<td>{{$failedSearch->on_datetime}}</td>
					<td>
                        <a href="javascript:void(0)" class="update" data-table_id="{{$failedSearch->id}}">
                            <img src="{{URL('/assets/images/edit.png')}}" class="icons"  title="Edit">
                        </a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@include('admin.recycle-second.modal',['result' => $result]);
@endsection