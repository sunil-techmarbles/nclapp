@extends('layouts.appadminlayout')
@section('title', 'Running List')
@section('content')
<div class="container">
    <div class="row">
    @if (!empty($runningList))
	<div class="col-12 mb-3">
		<h3>
			<span class="float-left">Running List</span>
			<span class="float-right">UPC Remaining: @if(is_array($upcCount)) {{@$upcCount[0]["count"]}} @endif</span>
		</h3>
	</div>
</div>
	<div class="row pb-4">
		<div class="col-sm-12">
            <a class="btn btn-info ml-2 float-right" href="{{route('runninglist.csv',['pageaction' => request()->get('pageaction'),'csv' => '1'])}}">Export</a>
            <button type="button" class="btn btn-info ml-2 float-right" id="sync-all-to-shopify">Sync to Shopify</button>
            <input type="hidden" name="reunlistsyns" value="true">      
        </div>
	</div>
        <table style="background: white;" id="itamg-running-list" class="itamg-running-list table table table-bordered table-striped table-responsive">
            <thead>
                <tr>
                    <th><input type="checkbox" class="check-all-ids" name="all_asin_sync"></th>
                    <th>ASIN</th>
                    <th>Model</th>
                    <th>Form Factor</th>
                    <th>CPU</th>
                    <th>Price</th>
                    <th>Asset</th>
                    <th>Added On</th>
                    <th>Count</th>
                    <th>Update to Shopify</th>
                    <th>Images</th>
                    <th>Shopify Product ID</th>
                    <th>Price Diff on Shopify </th>
                </tr>
            </thead>            
        </table>
    @endif
    <div style="text-align: right">
        <b>Total Count: {{$tcnt}}</b>
    </div>
</div>
@endsection
