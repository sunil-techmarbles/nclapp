@extends('layouts.appadminlayout')
@section('title', 'Session')
@section('content')
<div class="mte_content">
	<div class="container">
		<form method="post" class="form-inline" enctype="multipart/form-data" action="{{route('sessions')}}" style="max-height: 250px;overflow: auto;">
			<input type="hidden" name="pageaction" id="pageaction" value="{{(request()->get('pageaction')) ? request()->get('pageaction') : $pageaction}}"/>
			@csrf
			<div class="w-100 row mb-3 mt-3">
				<div class="col-md-7 mr-0">
					<div class="form-group">
						<input class="form-control" type="file" accept=".csv,.xls,.xlsx" name="session_file" id="bulk_data"/>
						<button class="btn btn-warning ml-1" name="bulk_upload" value="1" type="submit">Bulk Upload</button>
						<a target="_blank" class="btn btn-warning ml-1" href="{{URL('/sample-files/sample.xlsx')}}">Sample</a>
					</div>					
				</div>
				<div class="col-md-5">
					<div class="form-group float-right">
						<input class="form-control" placeholder="Session Name" type="text" name="session_name" id="session_name"/>
						<button class="btn btn-warning ml-2" name="new_session" value="1" type="submit">New Session</button>
					</div>
				</div>
			</div>
		</form>
		<div class="shipments-table">
		<table id="sessions-list" class="sessions-list table table-bordered table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Started On</th>
					<th>Closed On</th>
					<th>Status</th>
					<th>Items Count</th>
					<th>Missing parts</th>
				</tr>
			</thead>
			<tbody>
				@foreach($sessions as $p)
					<tr>
						<td>{{$p["id"]}}</td>
						<td><a href="{{route('sessions', ['pageaction' => request()->get('pageaction'),'s' => $p['id']])}}">{{$p["name"]}}</a></td>
						<td>{{$p["started_on"]}}</td>
						<td>{{$p["status"] == 'open' ? '' : $p["updated_on"]}}</td>
						<td>{{$p["status"]}}</td>
						<td>{{$p["count"]}}</td>
						<td>
							<button type="button" name="update" id="{{$p["id"]}}" class="btn btn-warning btn-sm session-update">Missing Parts</button>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
		@if(!empty($items) && $items->count() > 0)
			<h3>Items for session {{$sessionName}}</h3>
			<table id="sessions-asins-list" class="sessions-asins-list table table-bordered table-striped">
				<thead>
					<tr>
						<th>ASIN</th>
						<th>Model</th>
						<th>Form Factor</th>
						<th>CPU</th>
						<th>Price</th>
						<th>Count</th>
					</tr>
				</thead>
				<tbody>
					@foreach($items as $i)
						<tr>
							<td><a href="javascript:void(0);" onclick="$('.assets{{$i['aid']}}').toggle();">{{$i["asin"]}}</a></td>
							<td>{{$i["model"]}}</td>
							<td>{{$i["form_factor"]}}</td>
							<td>{{$i["cpu_core"]}} {{$i["cpu_model"]}} CPU @ {{htmlspecialchars($i["cpu_speed"])}}</td>
							<td>{{$i["price"]}}</td>
							<td>{{$i["cnt"]}}</td>
						</tr>
						@if (!empty($assets['asin'.$i['aid']]))
							<tr class="assets{{$i['aid']}}" style="display: none">
								<td colspan="6" align="center">
									@if(!empty($assets['asin'.$i['aid']]['active']))
										<b>Asset Numbers {{$i["asin"]}}:</b> 
										@foreach($assets['asin'.$i['aid']]['active'] as $itm)
											<a href="{{route('sessions', [
												's' => request()->get('s'),
												'remove' => $itm,
												'pageaction' => request()->get('pageaction')
											])}}">
										{{$itm}}</a>&nbsp;
										@endforeach
										(click to remove)<br/>
									@endif
									@if(!empty($assets['asin'.$i['aid']]['removed']))
										<b>Asset Numbers {{$i["asin"]}}:</b> 
										@foreach($assets['asin'.$i['aid']]['removed'] as $itm)
											<a href="{{route('sessions', [
												's' => request()->get('s'),
												'restore' => $itm,
												'pageaction' => request()->get('pageaction')
											])}}">{{$itm}}</a>&nbsp;
										@endforeach
										(click to restore)
									@endif
								</td>								
							</tr>
						@endif
					@endforeach
				</tbody>
			</table>
		@endif
		
		@if(!empty($parts) && $parts->count() > 0)
			<h3>Required Parts</h3>
			<form method="post" action="{{route('sessions')}}">
				<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
				@csrf
				<table id="sessions-asins-part-list" class=" sessions-asins-part-list table table-bordered table-striped">
					<thead>
						<tr>
							<th>Part Num</th>
							<th>Part Name</th>
							<th>Required Qty</th>
							<th>Available Qty</th>
							<th>Missing</th>
						</tr>
					</thead>
					<tbody>
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
								<td>{{$p["missing"] > 0 ? $p["missing"] : ''}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="mb-3">
					<input type="hidden" name="page" value="sessions"/>
					<input type="hidden" name="s" value="{{request()->get('s')}}"/>
					<div class="form-group">
						<button class="btn btn-danger" name="withdraw" value="1" type="submit">Withdraw</button>
						@if(@$miss>0)
							<a href="{{route('sessions', [
									's' => request()->get('s'),
									'reorder' => 1,
									'pageaction' => request()->get('pageaction')
								])}}" onclick="$(this).hide()" class="btn btn-warning">Reorder</a>
						@endif
					</div>					
				</div>
			</form>
		@endif
	</div>
</div>
@include('admin.sessions.modal')
@endsection
