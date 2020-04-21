@extends('layouts.appadminlayout')
@section('title', 'Category')
@section('content')
<div class="container">
	<input type="hidden" name="recycletwopage" value="category">
	<div class="table-responsive">
		<table id="itamg_inventory_value" class="catageory-list-result table table-bordered table-striped">
			<thead>
				<tr>
					<th class="first_heading"><input type="checkbox" class="select_all_to_delete" name="select_all_to_delete"></th>
					<th>Category Name</th>
					<th>Value</th>       
					<th>Action</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
@include('admin.asset-lookup.modal',['result' => []])
@endsection