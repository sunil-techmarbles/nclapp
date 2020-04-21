@extends('layouts.appadminlayout')
@section('title', 'Failed Searches')
@section('content')
<div class="container">
	<div class="table-responsive">
		<table id="itamg_inventory_value" class="failed-search-result table table-bordered table-striped">
			<thead>
				<tr>
					<th>Model</th>
					<th>Part No</th>
					<th>Brand</th>       
					<th>Category</th>
					<th>Require PN</th>
					<th>Date Time</th>
					<th class="noExport" >Action</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
@include('admin.asset-lookup.modal',['result' => $result])
@endsection