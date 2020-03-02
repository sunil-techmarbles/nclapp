@extends('layouts.appadminlayout')
@section('title', 'Shipment')
@section('content')
<div class="mte_content">
	<div class="container">
		<form method="get" id="main-form" autocomplete="off">
			<input type="hidden" name="page" value="shipments"/>
			<div class="noprint" style="text-align: center;">
				<div class='formitem'>
					<div class='form-group'>
						<label class='ttl' for='asset'>Please scan the Asset Number to add items to current Shipment</label><br/>
						<input type='text' value='' class='form-control' id='asset' name='asset' onkeyup="filterModels(this.value)" required='true'/>
					</div>
				</div>
			</div>
		</form>
		<form method="post" class="form-inline" action="{{route('add.shipment')}}" style="max-height: 250px;overflow: auto;">
			@csrf
			<div  class="w-100" style="text-align: right; margin-bottom: 10px">
				<div class="form-group float-right">
					<label for="qty">Shipment Name:</label>
					<input style="width:160px" class="form-control" type="text" name="session_name" id="session_name"/>
					<button class="btn btn-warning" name="new_session" value="1" type="submit">New Shipment</button>
				</div>
			</div>	
		</form>
		<table id="shipment" class="table">
			<thead> 
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Started On</th>
					<th>Closed On</th>
					<th>Status</th>
					<th>Items Count</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			@foreach($shipments as $p)
				<tr>
					<td>{{$p["id"]}}</td>
					<td>
						<a href="{{route('shipments', ['s' => $p['id']])}}">
							{{$p["name"]}}
						</a>
					</td>
					<td>{{$p["started_on"]}}</td>
					<td>{{$p["status"] == 'open' ? '' : $p["updated_on"] }}</td>
					<td>{{$p["status"]}}</td>
					<td>{{$p["count"]}}</td>
					<td>@if($p["status"] == 'open')
							<span style="cursor:pointer" onclick="$(\'#asinModal\').modal(\'toggle\')" class="glyphicon glyphicon-plus">
							</span>
						@else
							''
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		
		@if(!empty($asins))
			<h3>Items for Shipment {{$shipmentName}}</h3>
			<table id="shipment-asin" class="table">
				<thead>
					<tr>
						<th>ASIN</th>
						<th>Model</th>
						<th>Form Factor</th>
						<th>CPU</th>
						<th>Price</th>
						<th>Count</th>
						<th>Asset</th>
						<th>S/N</th>
						<th>COA</th>
						<th>Added</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@foreach($asins as $i)
						<tr style="font-weight: bold">
							<td><a href="#" onclick="$('.asin{{$i["aid"]}}').toggle()">{{$i["asin"]}}</a></td>
							<td>{{$i["model"]}}</td>
							<td>{{$i["form_factor"]}}</td>
							<td>{{$i["cpu_core"]}} {{$i["cpu_model"]}} CPU @ {{htmlspecialchars($i["cpu_speed"])}}</td>
							<td>{{number_format($i["price"],2)}}</td>
							<td>{{$i["cnt"]}}</td>
							<td colspan="5">&nbsp;</td>
						</tr>
						@foreach($i['items'] as $a)
							<tr style="display: none;" class="asin<?=$i["aid"]?>">
								<td>{{$a["asin"]}}</td>
								<td>{{$a["model"]}}</td>
								<td>{{$a["form_factor"]}}</td>
								<td>{{$a["cpu_core"]}} {{$a["cpu_model"]}} CPU @ {{htmlspecialchars($a["cpu_speed"])}}</td>
								<td>{{number_format($a["price"],2)}}</td>
								<td>&nbsp;</td>
								<td>{{$a["asset"]}}</td>
								<td>{{$a["sn"]}}</td>
								<td>{{($a["win8_activated"] ? 'WIN8 Activated' : $a["new_coa"])}}</td>
								<td>{{$a["added_on"]}}</td>
								<td><a href="
									{{route('shipments', ['s' => $sess, 'remove' => $a['asset']])}}
									"><span class="glyphicon glyphicon-trash"></span></a></td>
									}
							</tr>
						@endforeach
					@endforeach
				</tbody>
			</table>
		@endif
	</div>
	@include('admin.shipment.modal') 
</div>
@endsection