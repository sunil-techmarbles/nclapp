@extends('layouts.appadminlayout')
@section('title', 'Session')
@section('content')
<div class="mte_content">
	<div class="container">
		<form method="post" class="form-inline" enctype="multipart/form-data" action="{{route('sessions')}}" style="max-height: 250px;overflow: auto;">
			@csrf
			<div class="w-100 row mb-3 mt-3">
				<div class="col-md-6">
					<div class="form-group">
						<input class="form-control" type="file" name="bulk_data" id="bulk_data"/>
						<input type="submit" value="Bulk Upload" class="btn btn-warning" name="bulk_upload"/>
					</div>					
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<input class="form-control" placeholder="Session Name" type="text" name="session_name" id="session_name"/>
						<button class="btn btn-warning" name="new_session" value="1" type="submit">New Session</button>
					</div>
				</div>
			</div>
		</form>
		<table id="sessions" class="table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Started On</th>
					<th>Closed On</th>
					<th>Status</th>
					<th>Items Count</th>
				</tr>
			</thead>
			<tbody>
				@foreach($sessions as $p)
					<tr>
						<td>{{$p["id"]}}</td>
						<td><a href="{{route('sessions', ['s' => $p['id']])}}">{{$p["name"]}}</a></td>
						<td>{{$p["started_on"]}}</td>
						<td>{{$p["status"] == 'open' ? '' : $p["updated_on"]}}</td>
						<td>{{$p["status"]}}</td>
						<td>{{$p["count"]}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		@if(!empty($items))
			<h3>Items for session {{$sessionName}}</h3>
			<table id="sessions-asins" class="table">
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
								<td colspan="6">
									@if(!empty($assets['asin'.$i['aid']]['active']))
										<b>Asset Numbers {{$i["asin"]}}:</b> 
										@foreach($assets['asin'.$i['aid']]['active'] as $itm)
											<a href="{{route('sessions', [
												's' => request()->get('s'),
												'remove' => $itm

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
												'restore' => $itm

											])}}">{{$itm}}</a>&nbsp;
										@endforeach
										(click to restore)
									@endif
								</td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
							</tr>
						@endif
					@endforeach
				</tbody>
			</table>
		@endif
		
		@if(!empty($parts))
			<h3>Required Parts</h3>
			<form method="post" action="{{route('sessions')}}">
				@csrf
				<table id="sessions-asins-part" class="table">
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
									'reorder' => 1

								])}}" onclick="$(this).hide()" class="btn btn-warning">Reorder</a>
						@endif
					</div>					
				</div>
			</form>
		@endif
	</div>
</div>
@endsection