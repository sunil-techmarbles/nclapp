@extends('layouts.appadminlayout')
@section('title', 'Audit')
@section('content')

<div class="container">
	<div id="page-head">Packages</div>
	<div class=" w-100 mb-3 float-right">
		<button class="btn btn-info" data-toggle="modal" data-target="#checkInModal">Check In</button>
		<button class="btn btn-primary" onclick="newPackage()">New Package</button>
	</div>
	<form method="GET" enctype="multipart/form-data" autocomplete="off" action="{{route('packages')}}"> 
		<div style="margin-bottom: 10px">
			<div class="row" style="margin-bottom:5px">
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="expected_arrival" style="margin:0">Expected Arrival:</label>
						<input class="form-control daterange" type="text" value="{{ Request::get('expected_arrival') }}" name="expected_arrival" id="expected_arrival"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="description" style="margin:0">Description:</label>
						<input class="form-control" type="text" name="description" value="{{ Request::get('description') }}" id="description"/>
					</div>
				</div> 
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="req_name" style="margin:0">Request Name:</label>
						<input class="form-control" type="text" name="req_name" value="{{ Request::get('req_name') }}"  id="req_name"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="tracking_number" style="margin:0">Tracking Number:</label>
						<input class="form-control" type="text"  name="tracking_number" id="tracking_number" value="{{ Request::get('tracking_number') }}"/>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom:5px">
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="order_date" style="margin:0">Order Date:</label>
						<input class="form-control daterange" type="text" value="{{ Request::get('order_date') }}" name="order_date" id="order_date"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="ref_number" style="margin:0">Ref. Number:</label>
						<input class="form-control" type="text" value="{{ Request::get('ref_number') }}" name="ref_number" id="ref_number"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="carrier" style="margin:0">Carrier:</label>
						<input class="form-control" type="text" value="{{ Request::get('carrier') }}" name="carrier" id="carrier"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="freight_ground" style="margin:0">Freight/Ground:</label>
						<select class="form-control" name="freight_ground" id="freight_ground">
							<option value="" >Select</option>
							<option {{ (Request::get('freight_ground') == 'Freight') ? 'selected' : '' }} value="Freight">Freight</option>
							<option {{ (Request::get('freight_ground') == 'Ground') ? 'selected' : '' }} value="Ground">Ground</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom:5px">
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="location" style="margin:0">Location:</label>
						<input class="form-control" type="text" name="location" value="{{ Request::get('location') }}" id="location"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="recipient" style="margin:0">Recipient or Dept:</label>
						<input class="form-control" type="text" value="{{ Request::get('recipient') }}" name="recipient" id="recipient"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="received" style="margin:0">Received?</label>
						<select class="form-control" name="received" id="received">
							<option value="">Select</option>
							<option {{ (Request::get('received') == 'Y') ? 'selected' : '' }} value="Y">Yes</option>
							<option {{ (Request::get('received') == 'N') ? 'selected' : '' }} value="N">No</option>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="worker_id" style="margin:0">Warehouse Worker ID:</label>
						<input class="form-control" type="text" value="{{ Request::get('worker_id') }}" name="worker_id" id="worker_id"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="w-100 float-right">
					<div class="form-group-sm float-right mt-3">
						<a href="{{route('packages')}}" class="btn btn-warning" >Reset</a>
						<input class="btn btn-primary" type="submit" name="pkg_search" id="pkg_search" value="Search"/>
					</div>
				</div> 
			</div>
		</div>
	</form>
	@if (!empty($searchedPackages))
		<h3>Search Results</h3>
		<table class="table" id="package-table">
			<thead>
				<tr>
					<th>Order Date</th>
					<th>Exp. Arrival</th>
					<th>Description</th>
					<th>Qty</th>
					<th>Value</th>
					<th>Request Name</th>
					<th>Tracking #</th>
					<th>Ref #</th>
					<th>Carrier</th>
					<th>F/G</th>
					<th>Recipient</th>
					<th>Recvd</th>
					<th>Worker ID</th>
					<th>location</th>
				</tr>
			</thead>
			<tbody>
				@foreach($searchedPackages as $package)
					@if (!empty($package->id))
						<tr style="cursor: pointer" onclick="editPackage( this , {{$package->id}})">
							<td>{{$package->order_date}}</td>
							<td>{{$package->expected_arrival}}</td>
							<td>{{$package->description}}</td>
							<td>{{$package->qty}}</td>
							<td>{{$package->value}}</td>
							<td>{{$package->req_name}}</td>
							<td>{{$package->tracking_number}}</td>
							<td>{{$package->ref_number}}</td>
							<td>{{$package->carrier}}</td>
							<td>{{$package->freight_ground}}</td>
							<td>{{$package->recipient}}</td>
							<td>{{$package->received}}</td>
							<td>{{$package->worker_id}}</td>
							<td>{{$package->location}}</td>
						</tr>
					@endif
				@endforeach
			</tbody>
		</table>
	@endif
</div>
@include('admin.packages.modal')
@endsection  