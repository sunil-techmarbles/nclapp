@extends('layouts.appadminlayout')
@section('title', 'ASIN Parts')
@section('content')
<div class="mte_content itmg-asin">
	<div class="container itmg-asin-parts">
		<input type="hidden" name="page" value="refurb"/>
		<div class="noprint" style="text-align: center;">
			<div class='formitem'>
				<div class='form-group'>
					<label class='ttl' for='text_1'>Please enter ASIN<span class='req'>*</span></label><br/>
					<input type='text' value='' class='form-control' id='asset_num' name='asset_num' onkeyup="filterModels(this.value)" required='true'/>
				</div>
			</div>
		</div>
		<div id="page-head">
			Parts for {{$asinsParts['model']}} ({{$asinsParts['asin']}})
		</div>
		<form method="GET" class="form-inline" action="{{route('parts.asin',['pageaction' => request()->get('pageaction'), 'id' => $asinsParts['id']])}}">
			<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
			<input type="hidden" name="id" value="{{$asinsParts['id']}}"/>
			<div class="w-100">
				<div class="mx-0 form-group float-right mb-2">
					<label for="qty">Quantity:</label>
					<input style="width:80px" class="form-control" type="number" name="qty" id="qty" value="{{$qty}}"/>
					<button class="btn btn-primary update-btn" type="submit">Update</button>
					<button class="btn btn-warning" name="withdraw" value="1" type="submit">Withdraw</button>
				</div>
			</div>
			<table class="table table-bordered table-striped">
				<tr>
					<th>Part Num</th>
					<th>Part Name</th>
					<th>Required Qty</th>
					<th>Available Qty</th>
					<th>Missing</th>
				</tr>
				@php $miss = 0 @endphp
				@foreach($parts as $p)
					@if($p["missing"] > 0)
						@php $miss += $p["missing"]; @endphp
					@endif
					<tr>
						<td>
							<input type="checkbox" name="ppart[]" checked="checked" id="ppart{{$p["id"]}}" value="{{$p["id"]}}">
							{{$p["part_num"]}}
						</td>
						<td>{{$p["item_name"]}}</td>
						<td>{{$p["required_qty"]}}</td>
						<td>{{$p["qty"]}}</td>
						<td>{{ $p["missing"] > 0 ? $p["missing"]: '' }}</td>
					</tr>
				@endforeach
			</table>
		</form>
		<div style="margin-bottom: 10px" class="show-hide-btn">
			<button class="btn btn-default " onclick="$('#mlookup').toggle()">Show/Hide Models</button>
			@if($miss > 0)
				<form class="w-80 form-inline float-right" action="{{route('parts.asin', ['pageaction' => request()->get('pageaction'), 'id' => $asinsParts['id']])}}" method="GET">
					<input type="hidden" name="qty" value="{{$qty}}"/>
					<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
					<input type="hidden" name="id" value="{{$asinsParts['id']}}"/>
					<button class="btn btn-warning" onclick="$(this).hide()" name="reorder" value="1" type="submit">Reorder</button>
				</form>
			@endif
		</div>
		<table id="mlookup" style="display:none;background-color: #eceef2;" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>ASIN</th>
					<th>Model</th>
					<th>Form Factor</th>
					<th>CPU</th>
					<th>RAM</th>
					<th>HDD</th>
				</tr>
			</thead>
			<tbody>
				@foreach($models as $m)
				<tr style="cursor: pointer" class="mdlrow" data-model="{{strtolower($m['asin'].$m['model'])}}" onclick="location.href = '{{route("parts.asin", ['pageaction' => request()->get('pageaction'), 'id' => $m["id"]])}}'">
					<td>{{$m['asin']}}</td>
					<td>{{$m['model']}}</td>
					<td>{{$m['form_factor']}}</td>
					<td>{{$m['cpu_core']}} {{$m['cpu_model']}} {{$m['cpu_speed']}}</td>
					<td>{{$m['ram']}}</td>
					<td>{{$m['hdd']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<div style="margin-bottom: 10px" class="show-hide-btn">
			<button class="btn btn-default" onclick="$('#all_parts').toggle()">Show/Hide Parts ({{$asinsParts['model']}} ({{$asinsParts['asin']}})
			</button>
		</div>
		<form id="all_parts" style="display:none;" method="GET" action="{{route('parts.asin', ['pageaction' => request()->get('pageaction'), 'id' => $asinsParts['id']])}}">
			<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
			<input type="hidden" name="id" value="{{$asinsParts['id']}}"/>
			<div style="height:600px;overflow: auto">
				<table style="background-color: #eceef2;" class="table table-bordered table-striped">
					<tr>
						<th>Part Number</th>
						<th>Part Name</th>
						<th>
							Department <span style="cursor: pointer" class="glyphicon glyphicon-collapse-down" onclick="$('#dfilter').toggle()"></span>
							<div id="dfilter" style="position: absolute;background:#eceef2;border:1px solid #ddd;padding:10px;display:none">
								@foreach($departments as $di => $department)
									<label for="dcb{{$di}}" style="display: block">
										<input type="checkbox" checked="checked" onchange="deptFilter()" id="dcb{{$di}}" class="dcb" value="{{htmlentities($department['dept'])}}"/> {{htmlentities($department['dept'])}}
									</label>
								@endforeach
							</div>
						</th>
						<th>Vendor</th>
					</tr>
					@foreach($allParts as $p)
					<tr class="invrow" data-dept="{{htmlentities($p['dept'])}}">
						<td>
							<input type="checkbox" name="mpart[]" id="mpart{{$p['id']}}" value="{{$p['id']}}" {{in_array($asinsParts['id'],$p['models']) ? 'checked' : '' }} >{{$p['part_num']}}</td>
						<td>{{$p['item_name']}}</td>
						<td>{{$p['dept']}}</td>
						<td>{{$p['vendor']}}</td>
					</tr>
					@endforeach
				</table>
			</div>
			<div style="margin: 10px 0; text-align: right">
				<button type="submit" value="1" name="assignasinsparts" class="btn btn-primary">Assign selected Parts</button>
			</div>
		</form>
	</div>
</div>
@endsection
