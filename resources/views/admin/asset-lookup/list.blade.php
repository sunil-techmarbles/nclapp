@extends('layouts.appadminlayout')
@section('title', 'Asset Lookup')
@section('content')
<div class="container">
	<div class="table-responsive">
		<table id="itamg_inventory_value" class="asset-lookup-list table table-responsive table-bordered table-striped">
			<thead>
				<tr>
					<th class="first_heading noExport"><input type="checkbox" class="select_all_to_delete" name="select_all_to_delete"></th>
					<th>Brand</th>  
					<th>Model</th>
					<th>Part No</th>
					<th>Category</th>
					<th>Notes</th>
					<th>Value</th>
					<th>Status</th>
					<th>Require PN</th>
					<th class="noExport">Action</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
@include('admin.asset-lookup.modal',['result' => $result])
@endsection
