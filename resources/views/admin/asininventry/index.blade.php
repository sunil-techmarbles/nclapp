@extends('layouts.appadminlayout')
@section('title', 'Asin Inventry')
@section('content')
<div class="container">
	<div id="page-head" class="noprint">
		Asin Inventory
	</div>
	<div class="wipeReportNav">
		<a class="btn btn-info" href="{{route('asininventry.removeasset',['pageaction' => request()->get('pageaction')])}}">Remove Assets</a>
		<a class="btn btn-info" href="{{route('asininventry.exportinventry',['pageaction' => request()->get('pageaction')])}}">Export To Excel</a>
	</div>
	<h3>Items In session</h3>
	<table class="asinInventryTable-Itmg wrap table table-bordered table-striped" id="asinInventryTable-Itmg">
		<thead>
			<th>ASIN</th>
			<th>Model</th>
			<th>Form Factor</th>
			<th>CPU</th>
			<th>Price</th>
			<th>Count</th>
		</thead>
	</table>
</div>
@endsection
