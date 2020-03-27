@extends('layouts.appadminlayout')
@section('title', 'Recycle Second')
@section('content')
<div class="container">
	<h3 align="center"><strong>ITAMG INVENTORY</strong></h3>
	<div class="table-responsive">
		<table id="itamg_inventory_value" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="first_heading"><input type="checkbox" class="select_all_to_delete" name="select_all_to_delete"></th>
					<th>Brand</th>  
					<th>Model</th>
					<th>Part No</th>
					<th>Category</th>
					<th>Notes</th>
					<th>Value</th>
					<th>Status</th>
					<th>Require PN</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($itamgRecycleInventors as $key => $itamgRecycleInventor)
				<tr>
					<td><input type="checkbox" class="select_to_delete" name="select_to_delete" value="{{$itamgRecycleInventor->id}}"></td>
					<td>{{$itamgRecycleInventor->Brand}}</td>
					<td>{{$itamgRecycleInventor->Model}}</td>
					<td>{{$itamgRecycleInventor->PartNo}}</td>
					<td>{{$itamgRecycleInventor->Category}}</td>
					<td>{{$itamgRecycleInventor->Notes}}</td>
					<td>{{$itamgRecycleInventor->Value}}</td>
					<td>{{$itamgRecycleInventor->Status}}</td>
					<td>{{$itamgRecycleInventor->require_pn}}</td>
					<td>
						<!--{{route('edit.category.record', ["cat_name" => $itamgRecycleInventor->id])}} -->
                        <a href="javascript:void(0)" class="edit_entry_link" data-table_id="{{$itamgRecycleInventor->id}}">
                            <img src="{{URL('/assets/images/edit.png')}}" class="icons"  title="Edit">
                        </a>
                        <a href="javascript:void(0)" class="delete_entry_link" onclick="del_confirm({{$itamgRecycleInventor->id}},'deleterecycletwo','Recycle Inventory')" data-table_id="{{$itamgRecycleInventor->id}}">
                            <img src="{{URL('/assets/images/del.png')}}" class="icons"  title="Delete">
                        </a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@include('admin.recycle-second.modal',['result' => $result]);
@endsection