@extends('layouts.appadminlayout')
@section('title', 'Shopify Inventory')
@section('content')
<div class="container">
	<div style="position: sticky;top:0; background:white; height:100px; border-bottom:2px solid #dddddd; padding: 1px 10px; z-index: 9999;">
		<div class="mb-3">
			<h3>
				<span class="float-left pb-1">New Running List</span>
				<span class="float-right pb-1">UPC Remaining: @if(is_array($upcCount)) {{@$upcCount[0]["count"]}} @endif</span>
			</h3>
		</div>
		<form method="post" class="form-inline row w-100 m-0" action="{{route('inventory',['shopifysubmit' => 'true'])}}" enctype="multipart/form-data">
			<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
			@csrf
			<div class="col-6 float-left p-0">
				<div class="form-group">
					<input class="form-control" accept=".csv,.xls,.xlsx" type="file" name="bulk_data" id="bulk_data"/>
					<input type="submit" value="Bulk Upload" class="btn btn-warning" name="bulk_upload"/>
				</div>
			</div>
			<div class="col-6 float-right p-0">
				<a class="btn btn-info ml-2 float-right" href="{{route('inventory.csv',['csv' => '1'])}}">Export</a>
				<button type="button" class="btn btn-warning ml-2 float-right" data-toggle="modal" data-target="#brModal">Bulk Remove</button>
				<button type="button" class="btn btn-info ml-2 float-right" id="sync-all-to-shopify">Sync to Shopify</button>
			</div>
		</form>
	</div>
	@if (!empty($runningList))
		<table class="main-table table table-bordered table-striped table-responsive" id="main-table" style="background: white">
			<thead>
				<tr>
					<th style="white-space: nowrap">
						<input type="checkbox" class="check-all-ids" name="check-all-ids"> <span class="fa fa-arrow-down" onclick="$('#syncfilter').toggle()"></span>
						<div id="syncfilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none;z-index: 222;">
							<label for="sync_all" style="display: block">
								<input type="checkbox" checked="checked" onchange="toggleAll('sync')" id="sync_all" value="1"/> Toggle All
							</label>
							<label for="sync_true" style="display: block">
								<input type="checkbox" checked="checked" onchange="tblFilter()" id="sync_true" class="sync" value="yes"/> Synchronized
							</label>
							<label for="sync_false" style="display: block">
								<input type="checkbox" checked="checked" onchange="tblFilter()" id="sync_false" class="sync" value="no"/> Not Synchronized
							</label>
						</div>	
					</th>
					<th>ASIN/SKU</th>
					<th style="white-space: nowrap">
						Model <span style="cursor: pointer" class="fa fa-arrow-down" onclick="$('#mfilter').toggle()"></span>
						<div id="mfilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none">
							<label for="mdl_all" style="display: block">
								<input type="checkbox" checked="checked" onchange="toggleAll('mdl')" id="mdl_all" value="1"/> Toggle All
							</label>
							@foreach($mdlList as $itmid=>$itm)
								<label for="mdl{{$itmid}}" style="display: block">
									<input type="checkbox" checked="checked" onchange="tblFilter()" id="mdl{{$itmid}}" class="mdl" value="{{htmlentities($itm)}}"/> {{htmlentities($itm)}}
								</label>
							@endforeach 
						</div>
					</th>
					<th style="white-space: nowrap">
						Form Factor  <span style="cursor: pointer" class="fa fa-arrow-down" onclick="$('#ffilter').toggle()"></span>
						<div id="ffilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none">
							<label for="ff_all" style="display: block">
								<input type="checkbox" checked="checked" onchange="toggleAll('ff')" id="ff_all" value="1"/> Toggle All
							</label>
							@foreach($ffList as $itmid=>$itm)
								<label for="ff{{$itmid}}" style="display: block">
									<input type="checkbox" checked="checked" onchange="tblFilter()" id="ff{{$itmid}}" class="ff" value="{{htmlentities($itm)}}"/> {{htmlentities($itm)}}
								</label>
							@endforeach 
						</div>
					</th>
					<th style="white-space: nowrap">
						CPU <span style="cursor: pointer" class="fa fa-arrow-down" onclick="$('#cfilter').toggle()"></span>
						<div id="cfilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none">
							<label for="cpu_all" style="display: block">
								<input type="checkbox" checked="checked" onchange="toggleAll('cpu')" id="cpu_all" value="1"/> Toggle All
							</label>
							@foreach($cpuList as $itmid=>$itm)
								<label for="cpu{{$itmid}}" style="display: block">
									<input type="checkbox" checked="checked" onchange="tblFilter()" id="cpu{{$itmid}}" class="cpu" value="{{htmlentities($itm)}}"/> {{htmlentities($itm)}}
								</label>
							@endforeach 
						</div>
					</th>
					<th>Asset</th>
					<th>Added</th>
					<th>Count</th>
					<th>&nbsp;</th>
					<th>Update to Shopify</th>
					<th style="white-space: nowrap">Images <span style="cursor: pointer" class="fa fa-arrow-down" onclick="$('#imgfilter').toggle()"></span>
						<div id="imgfilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none">
							<label for="img_all" style="display: block">
								<input type="checkbox" checked="checked" onchange="toggleAll('img')" id="img_all" value="1"/> Toggle All
							</label>
							<label for="imgavl" style="display: block">
								<input type="checkbox" checked="checked" onchange="tblFilter()" id="imgavl" class="img" value="Available"/> Available
							</label>
							<label for="imgna" style="display: block">
								<input type="checkbox" checked="checked" onchange="tblFilter()" id="imgna" class="img" value="N/A"/> N/A
							</label>
						</div>	
					</th>
					<th style="white-space: nowrap">Price <span style="cursor: pointer" class="fa fa-arrow-down" onclick="$('#pricefilter').toggle()"></span>
						<div id="pricefilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none">
							<label for="price_all" style="display: block">
								<input type="checkbox" checked="checked" onchange="toggleAll('price')" id="price_all" value="1"/> Toggle All
							</label>
							<label for="priceavl" style="display: block">
								<input type="checkbox" checked="checked" onchange="tblFilter()" id="priceavl" class="price" value="Available"/> Available
							</label>
							<label for="pricena" style="display: block">
								<input type="checkbox" checked="checked" onchange="tblFilter()" id="pricena" class="price" value="N/A"/> N/A
							</label>
						</div>	
					</th>
					<th>Shopify Product ID <span class="fa fa-eye-slash " onclick="$('.s_price').toggle()"></span></th>
					<th>Price Diff on Shopify </th>
				</tr>
			</thead>
		</table>
		<div style="text-align: right"><b>Total Count: {{$tcnt}}</b></div>
	@endif
</div>
@include('admin.shopify.modal')
@endsection