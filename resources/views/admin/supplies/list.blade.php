@extends('layouts.appadminlayout')
@section('title', 'Supplies')
@section('content')
<div class="abs" style="text-align: center;">
	<table id="supplies" class="table" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<td>
					<a class="btn btn-default btn-sm border" href="{{route('add.supplies')}}" &quot;index.php?&amp;start=0&amp;ad=&amp;sort=&amp;s=&amp;f=&amp;mte_a=new&quot;" >Add Record</a>
					<a class="btn btn-default btn-sm border" href="{{route('export.supplies')}}" onclick="window.location=&quot;export.php&quot;">Export</a>
				</td>
				<td nowrap="" style="text-align: right">
					<form method="post" action="{{route('import.supplies')}}" enctype="multipart/form-data">
						<input type="file" name="impfile">
						<input type="submit" class="btn btn-default btn-sm border" value="Import">
					</form>
				</td>
				<td nowrap="" style="text-align: right">
					<form method="get" action="{{route('supplies')}}">
						<input type="text" name="s" value="" placeholder=" Search">
						<select name="f" style="height:26px;border: 0;">
							@foreach($searchItemsLists as $key => $searchItem)
								<option value="{{$key}}">{{$searchItem}}</option>
							@endforeach
						</select> 
						<input class="btn btn-default btn-sm border" type="submit" value="Search">
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="table">
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
					<a href="javascript:void(0)" onclick="del_confirm({{$supplie->id}})">
						<img src="{{URL('/assets/images/del.png')}}" class="icons" title="Delete">
					</a>&nbsp;&nbsp;
					<a href="{{route('edit.supplies', $supplie->id)}}">
						<img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
					</a>
				</td>
				<td>{{$supplie->id}}</td>
				<td>{{$supplie->item_name}}</td>
				<td>{{$supplie->qty}}</td>
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
