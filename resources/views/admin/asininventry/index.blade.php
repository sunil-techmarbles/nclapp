@extends('layouts.appadminlayout')
@section('title', 'Asin Inventry')
@section('content')
<div class="container">
	<div id="page-head" class="noprint">
		Asin Inventory
	</div>
	<div class="wipeReportNav">
		<a class="btn btn-info" href="{{route('asininventry.removeasset')}}">Remove Assets</a>
		<a class="btn btn-info" href="{{route('asininventry.exportinventry')}}">Export To Excel</a>
	</div>
	<h3>Items In session</h3>
	<table class="table table-striped table-condensed table-hover" id="asinInventryTable-Itmg">
		<thead>
			<th>ASIN</th>
			<th>Model</th>
			<th>Form Factor</th>
			<th>CPU</th>
			<th>Price</th>
			<th>Count</th>
		</thead>
		<tbody>
			@foreach($items as $i)
			<tr>
				<td><a href="javascript:void(0)" onclick="$('.assets{{$i->aid}}').toggle();">{{$i->asin}}</a></td>
				<td>{{$i->model}}</td>
				<td>{{$i->form_factor}}</td>
				<td>{{$i->cpu_core}} {{$i->cpu_model}} CPU @ {{$i->cpu_speed}}</td>
				<td>{{$i->price}}</td>
				<td>{{$i->cnt}}</td>
			</tr>
			@if ( !empty($assets['asin'.$i->aid]) )
					<tr class="assets{{$i->aid}}" style="display: none">
						<td colspan="6">
							@if ( !empty($assets['asin'.$i->aid]['active']) )
								<b>Asset Numbers:</b> 
								@foreach($assets['asin'.$i->aid]['active'] as $itm)
									{{$itm}},
								@endforeach
								<br/>
							@endif
						</td>
					</tr> 
					@endif
					@endforeach
				</tbody>
			</table>
		</div>
		@endsection