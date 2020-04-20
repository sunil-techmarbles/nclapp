@extends('layouts.appadminlayout')
@section('title', 'ASINs')
@section('content')
<div class="abs" style="text-align: center;">
	<table class="table record-filed" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<td>
					<a class="btn btn-primary btn-sm border" href="{{route('add.asins', ['pageaction' => request()->get('pageaction')])}}" >Add Record
					</a>
				</td>
				<td nowrap="" style="text-align: right">
					<form method="get" action="{{route('asin')}}">
						<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
						<input type="text" name="s" value="{{request()->get('s')}}" placeholder=" Search">
						<select name="f" style="height:26px;border: 0;">
							@foreach($searchItemsLists as $key => $searchItem)
								<option value="{{$key}}">{{$searchItem}}</option>
							@endforeach
						</select> 
						<input class="btn btn-success btn-sm border" type="submit" value="Search">
						@if(request()->get('s') || request()->get('f'))
							<a class="btn btn-dark btn-sm border" href="{{route('asin', ['pageaction' => request()->get('pageaction')])}}">Reset</a>
						@endif
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table id="asins-list" class="table">
		<thead>
			<tr>
				<th></th>
				<th>ID</th>
				<th>ASIN</th>
				<th>Price</th>
				<th>Manufacturer</th>
				<th>Notif.</th>
				<th>Form Factor</th>
				<th>CPU Core</th>
				<th>CPU Model</th>
				<th>CPU Speed</th>
				<th>RAM</th>
				<th>HDD</th>
				<th>OS</th>
				<th>Webcam</th>
			</tr>
		</thead>
		<tbody>
			@foreach($asinLists as $asin)
				<tr>
					<td nowrap="">
						<a href="javascript:void(0)" onclick="del_confirm({{$asin->id}},'deleteasin','ASINs')">
							<img src="{{URL('/assets/images/del.png')}}" class="icons" title="Delete">
						</a>&nbsp;&nbsp;
						<a href="{{route('edit.asin',['pageaction' => request()->get('pageaction'), 'id' => $asin->id])}}">
							<img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
						</a>
						<a href="{{route('parts.asin',['pageaction' => request()->get('pageaction'), 'id' => $asin->id])}}" title="Parts List"><img src="{{URL('/assets/images/tools.png')}}" class="icons" title="Parts"></a>
					</td>
					<td>{{$asin->id}}</td>
					<td>
						@if(!empty($asin->link))
							<a href="{{$asin->link}}" target="_blank">{{$asin->asin}}</a>
						@else
							<a href="https://www.amazon.com/dp/{{$asin->asin}}?ref=myi_title_dp" target="_blank">{{$asin->asin}}</a>
						@endif
					</td>
					<td>{{$asin->price}}</td>
					<td>{{$asin->manufacturer}}</td>
					<td>{{$asin->notifications}}</td>
					<td>{{$asin->form_factor}}</td>
					<td>{{$asin->cpu_core}}</td>
					<td>{{$asin->cpu_model}}</td>
					<td>{{$asin->cpu_speed}}</td>
					<td>{{$asin->ram}}</td>
					<td>{{$asin->hdd}}</td>
					<td>{{$asin->os}}</td>
					<td>{{$asin->webcam}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	@if($asinLists->count() == 0)
		<p><center><strong>Nothing found</strong></center></p>
	@endif
	{!! $asinLists->appends(request()->input())->links() !!}
</div>
@endsection
