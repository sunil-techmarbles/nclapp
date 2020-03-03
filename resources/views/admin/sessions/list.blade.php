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
						<label for="session_name">Session Name:</label>
						<input class="form-control" type="text" name="session_name" id="session_name"/>
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
						<td><a href="index.php?page=sessions&s=<?=$p["id"]?>">{{$p["name"]}}</a></td>
						<td>{{$p["started_on"]}}</td>
						<td>{{$p["status"] == 'open' ? '' : $p["updated_on"]}}</td>
						<td>{{$p["status"]}}</td>
						<td>{{$p["count"]}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		@if(!empty($items))
			<h3>Items for session {{$sess_name}}</h3>
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
							<td><a href="#" onclick="$('.assets{{$i['aid']}}').toggle();">{{$i["asin"]}}</a></td>
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
										<b>Asset Numbers:</b> 
										@foreach($assets['asin'.$i['aid']]['active'] as $itm)
											<a href="index.php?page=sessions&s=<?=$sess?>&remove=<?=$itm?>&t=<?=time()?>">{{$itm}}</a>&nbsp;
										@endforeach
										(click to remove)<br/>
									@endif
									@if(!empty($assets['asin'.$i['aid']]['removed']))
										<b>Asset Numbers:</b> 
										@foreach($assets['asin'.$i['aid']]['removed'] as $itm)
											<a href="index.php?page=sessions&s=<?=$sess?>&restore=<?=$itm?>&t=<?=time()?>">{{$itm}}</a>&nbsp;
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
		
		@if(!empty($parts))
			<h3>Required Parts</h3>
			<form method="post" class="form-inline" action="index.php">
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
						@foreach($parts as $p)
							<tr>
								<td>
									<input type="checkbox" name="ppart[]" checked="checked" id="ppart{{$p["id"]}}" value="{{$p["id"]}}">
									{{$p["part_num"]}}
								</td>
								<td>{{$p["item_name"]}}</td>
								<td>{{$p["required_qty"]}}</td>
								<td>{{$p["qty"]}}</td>
								<td>{{$p["missing"] > 0 ? $p["missing"] : '&nbsp;'}}</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<div style="text-align: right; margin-bottom: 10px">
					<input type="hidden" name="page" value="sessions"/>
					<input type="hidden" name="s" value="<?=$sess?>"/>
					<div class="form-group">
						<button class="btn btn-danger" name="withdraw" value="1" type="submit">Withdraw</button>
					</div>
					@if($miss>0)
						<a style="float:right" href="index.php?page=sessions&s=<?=$sess?>&reorder=1&t=<?=time()?>" onclick="$(this).hide()" class="btn btn-warning">Reorder</a>
					@endif
				</div>
			</form>
			<div style="margin-bottom: 10px"></div>
		@endif
	</div>
</div>
@endsection