@extends('layouts.appadminlayout')
@section('title', 'Audit')
@section('content')

<div class="container">
	<div>
		<button style="margin-top:10px;margin-left: 50px" class="btn btn-info" data-toggle="modal" data-target="#checkModal">Check 	In</button>
		<button style="float:right;margin-top:10px" class="btn btn-primary" onclick="newPackage()">New Package</button>
	</div>
	<div id="page-head">
		Packages
	</div>
	<form method="post" enctype="multipart/form-data" autocomplete="off" action="">
		<div style="margin-bottom: 10px">
			<input type="hidden" name="page" value="packages"/>
			<input type="hidden" name="search" value="1"/>
			<div class="row" style="margin-bottom:5px">
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="expected_arrival" style="margin:0">Expected Arrival:</label>
						<input class="form-control daterange" type="text" name="expected_arrival" id="expected_arrival"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="description" style="margin:0">Description:</label>
						<input class="form-control" type="text" name="description" id="description"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="req_name" style="margin:0">Request Name:</label>
						<input class="form-control" type="text" name="req_name" id="req_name"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="tracking_number" style="margin:0">Tracking Number:</label>
						<input class="form-control" type="text" name="tracking_number" id="tracking_number"/>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom:5px">
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="order_date" style="margin:0">Order Date:</label>
						<input class="form-control daterange" type="text" name="order_date" id="order_date"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="ref_number" style="margin:0">Ref. Number:</label>
						<input class="form-control" type="text" name="ref_number" id="ref_number"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="carrier" style="margin:0">Carrier:</label>
						<input class="form-control" type="text" name="carrier" id="carrier"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="freight_ground" style="margin:0">Freight/Ground:</label>
						<select class="form-control" name="freight_ground" id="freight_ground">
							<option value="">Select</option>
							<option value="Freight">Freight</option>
							<option value="Ground">Ground</option>
						</select>
					</div>
				</div>
				
			</div>
			<div class="row" style="margin-bottom:5px">
				<div class="col-sm-3">
					<div class="form-group-sm">
						<label for="location" style="margin:0">Location:</label>
						<input class="form-control" type="text" name="location" id="location"/>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group-sm">
						<label for="recipient" style="margin:0">Recipient or Dept:</label>
						<input class="form-control" type="text" name="recipient" id="recipient"/>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group-sm">
						<label for="received" style="margin:0">Received?</label>
						<select class="form-control" name="received" id="received">
							<option value="">Select</option>
							<option value="Y">Yes</option>
							<option value="N">No</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group-sm">
						<label for="worker_id" style="margin:0">Warehouse Worker ID:</label>
						<input class="form-control" type="text" name="worker_id" id="worker_id"/>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group-sm">
						<input class="btn btn-warning" type="reset" value="Reset" style="margin-top:15px;"/>
						<input class="btn btn-primary" type="submit" name="btn_search" id="btn_search" value="Search" style="margin-top:15px;float:right"/>
					</div>
				</div>
			</div>
		</div>
	</form>  
	@include('admin.packages.modal')
</div>
@endsection  