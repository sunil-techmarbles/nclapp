@extends('layouts.appadminlayout')
@section('title', 'Supplies')
@section('content')
<div class="abs" style="text-align: center;">
	<table class="table  record-filed" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<td>
					<a class="btn btn-primary btn-sm border" href="{{route('add.supplies')}}" >Add Record</a>
					<a class="btn btn-secondary btn-sm border" href="{{route('export.supplies')}}">Export</a>
				</td>
				<td nowrap="" style="text-align: right;">
					<form method="post" action="{{route('import.supplies')}}" enctype="multipart/form-data">
						@csrf
						<input class="supplies-flie-upload" type="file" name="impfile">
						<input type="submit" class="btn btn-secondary btn-sm border" value="Import">
					</form>
					@if($errors->has('impfile'))
						<div class="alert alert-danger">{{ $errors->first('impfile') }}</div>
					@endif
				</td>
				<td nowrap="" style="text-align: right">
					<form method="get" action="{{route('supplies')}}">
						<input class="record-form" type="text" name="s" value="{{request()->get('s')}}" placeholder=" Search">
						<select name="f" style="height:26px;border: 0;">
							@foreach($searchItemsLists as $key => $searchItem)
								<option @if(request()->get('f') == $key) selected @endif value="{{$key}}">{{$searchItem}}</option>
							@endforeach
						</select>						
						<input class="btn btn-success btn-sm border" type="submit" value="Search">
						@if(request()->get('s') || request()->get('f'))
							<a class="btn btn-dark btn-sm border" href="{{route('supplies')}}">Reset</a>
						@endif
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table id="supplies" class="table">
		<thead>
			<tr>
				<th></th>
				<th> Item ID</th>
				<th> Item Name</th>
				<th> Quantity  </th>
				<th> P/N </th>
				<th> Description</th>
				<th> Department </th>
				<th> Price </th>
				<th> Vendor </th>
				<th> Low Stock </th>
				<th> Reorder Qty </th>
				<th> Delivery Time </th>
			</tr>
		</thead>

		<tbody>
			@foreach($supplieLists as $supplie)
			<tr>
				<td nowrap="">
					<a href="javascript:void(0)" onclick="del_confirm({{$supplie->id}},'deletesupplie','Supplie')">
						<img src="{{URL('/assets/images/del.png')}}" class="icons" title="Delete">
					</a>&nbsp;&nbsp;
					<a href="{{route('edit.supplies', $supplie->id)}}">
						<img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
					</a>
				</td>
				<td>{{$supplie->id}}</td>
				<td>{{$supplie->item_name}}</td>
				<td style="padding:5px;white-space:nowrap">
					<form method="GET" action="{{route('update.qty.reorder')}}">
						<a href="javascript:void(0)" onclick="reorderItem({{$supplie->id}},{{$supplie->qty}}, 'updateqtyreorder')">
							<img src="{{URL('assets/images/cart.png')}}" alt="Reorder" title="Reorder">
						</a>&nbsp;
						<input type="hidden" name="supplieid" value="{{$supplie->id}}">
						<input type="number" style="width:70px;min-width:70px;height:20px" min="0" name="qty" value="{{$supplie->qty}}">
						<input type="image" style="width:20px;min-width:20px;height:20px; border:none" src="{{URL('assets/images/tick.png')}}" title="Save" alt="Save">
					</form>
				</td>
				<td>{{$supplie->part_num}}</td>
				<td>{{$supplie->description}}</td>
				<td>{{$supplie->dept}}</td>
				<td>{{$supplie->price}}</td>
				<td>{{$supplie->vendor}}</td>
				<td>{{$supplie->low_stock}}</td>
				<td>{{$supplie->reorder_qty}}</td>
				<td>{{$supplie->dlv_time}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
