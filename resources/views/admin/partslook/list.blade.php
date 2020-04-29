@extends('layouts.appadminlayout')
@section('title', 'Lookup')
@section('content')
<div class="container">
	<h3 align="center"><strong>Inventory Lookup</strong></h3>
	<form method="post" id="main-form" autocomplete="off">
		<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
		<div class="formitem" style="text-align: center;">
			<div class="form-group">
				<label class="ttl" for="text_1">Please enter ASIN<span class="req">*</span></label><br>
				<input type="text" value="" class="form-control" id="asset_num" name="asset_num" onkeyup="filterModels(this.value)" required="true">
			</div>
		</div>
	</form>
	<table id="lookup" class="table lookup table-bordered table-striped">
		<thead>
			<th>ASIN</th>
			<th>Model</th>
			<th>Form Factor</th>
			<th>CPU</th>
			<th>RAM</th>
			<th>HDD</th>
		</thead>
	</table>
</div>
@endsection

