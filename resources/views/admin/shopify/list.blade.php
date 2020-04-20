@extends('layouts.appadminlayout')
@section('title', 'Shopify Inventory')
@section('content')
<div class="container">
	<div style="position: sticky;top:0; background:white; height:100px; border-bottom:2px solid #dddddd; padding: 1px 10px;">
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
		<table class="table table-condensed table-hover table-responsive" id="main-table" style="background: white">
			<thead>
				<tr>
					<th style="white-space: nowrap">
						<input type="checkbox" class="check-all-ids" name="check-all-ids"> <span class="fa fa-arrow-down" onclick="$('#syncfilter').toggle()"></span>
						<div id="syncfilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none;z-index: 222;">
							<label for="sync_true" style="display: block">
								<input type="checkbox" onchange="tblFilter()" id="sync_true" class="sync" value="yes"/> Synchronized
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
					<th class="s_price" style="display:none">Price Diff on Shopify </th>
				</tr>
			</thead>
			<tbody>
				@foreach ($runningList as $i_key => $i)
					@php
						$list_price = checkRunlistPrice($i);
						$price_display = $list_price ? 'Available' : 'N/A';
						$images = checkImages($i);
					@endphp
					<tr style="font-weight: bold" class="nlrow" data-img="{{$images}}" data-price="{{$price_display}}" data-mdl="{{$i["model"]}}" 
						data-ff="{{ $i["technology"]}}" data-cpu="{{ $i["cpg"]}}" data-synced="{{empty($i["shopify_product_id"]) ? 'no' : 'yes'}}">
						<td>
							@if(empty($i["shopify_product_id"]) && !empty($i["mid"]))
								<input id="cb-select-{{ $i["asin"]}}" type="checkbox" name="sync-all-ids[]" value="{{ $i["asin"]}}">
							@endif
						</td>
						<td>{{$i['asin']}}</td>
						<td><span style="cursor:pointer;text-decoration: underline;" onclick="$('.asin{{$i["asin"]}}').toggle()">{{$i["model"]}}</span></td>
						<td>{{ $i["technology"] }}</td>
						<td colspan=3>{{ $i["cpg"] }} ({{$i["cpus"]}} CPUs)</td>
						<td>{{ $i["cnt"] }}</td>
						<td>
							@if($i["mid"])
								<a href="{{route('model.data.template',['pageaction' => request()->get('pageaction'),'tplid' => $i["mid"]])}}" target="_blank">Model Data</a> 
							@else
								<span style="cursor:pointer" onclick="$('#mid{{$i['asin']}}').toggle()">Specify Model</span>
								<div style="display: none;position:absolute" id="mid{{$i['asin']}}">
									<input class="form-control" type="text" id="model{{$i['asin']}}" onkeyup="getModels(this.id)"/>
								</div>
							@endif
						</td>
						<td>
							@if ($i["shopify_product_id"])
							<button class="btn btn-link sync-to-shopify" data-id="{{$i["asin"]}}">Update</button>
							@endif
						</td>
						<td>{{$images}}</td>
						<td style="text-align: center; {{ $price_display=='N/A' ? 'cursor:pointer' : '' }}" {{ $price_display=='N/A' ? 'onclick="setPrice(\''.$i["asin"].'\')"' : '' }}>{{ $price_display }}</td>
						<td>@if(isset($i["shopify_product_id"]))
							<a href="{{route("inventory", ["goto" => $i["shopify_product_id"]])}}">{{$i["shopify_product_id"]}}</a>
							@else
							''
							@endif
						</td>
						<td class="s_price" style="display:none;text-align: center;">
							@if ($i["shopify_product_id"])
								@php
									$price_data = getShopifyRunlistPrice($i);
									if ($price_data['diffrence'] != 0){
										echo 'Shopify Price: $' . $price_data['shopify_price'] . '<br>';
										echo 'Final Price: $' . $price_data['final_price'] . '<br>';
										echo 'Diffrence: $' . $price_data['diffrence'] . '<br>';
										echo '<button class="btn btn-link update-price-to-shopify" data-id="' . $i["asin"] . '">Update Price</button>';
									}
								@endphp
							@endif
						</td>
					</tr>
					@foreach ($i['items'] as $a)
						<tr style="display: none;" class="drow asin{{$i["asin"]}}">
							<td></td>
							<td colspan="3"> </td> 
							<td>{{$a["cpu"]}}</td>
							<td>{{$a["asset"]}}</td>
							<td colspan="5">{{$a["added_on"]}}</td>
							<td></td>
							<td><a href="{{route("inventory", ['pageaction' => request()->get('pageaction') ,"remove" => $a["asset"]])}}"><span class="fa fa-trash"></span></a></td>
						</tr>
					@endforeach
				@endforeach
			</tbody> 
		</table>
		{!! $runningList->appends(request()->input())->links() !!}
		<div style="text-align: right"><b>Total Count: {{$tcnt}}</b></div>
	@endif
</div>
@include('admin.shopify.modal')
@endsection
