@extends('layouts.appadminlayout')
@section('title', 'Category')
@section('content')
<div class="container">
	<h3 align="center"><strong>Category</strong></h3>
	<input type="hidden" name="recycletwopage" value="category">
	<div class="table-responsive">
		<a href="{{route('recycle.second')}}" class="btn btn-default border border-success float-right mb-2">Back</a>
		<table id="itamg_inventory_value" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="first_heading"><input type="checkbox" class="select_all_to_delete" name="select_all_to_delete"></th>
					<th>Category Name</th>
					<th>Value</th>       
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($categories as $key => $category)
				<tr>
					<td>
						<input type="checkbox" class="select_to_delete" name="select_to_delete" value="{{$category->id}}">
					</td>
					<td>{{$category->category_name}}</td>
					<td>{{$category->value}}</td>
					<td>
                        <a href="javascript:void(0)" class="edit_cat_link" data-table_id="{{$category->id}}">
                            <img src="{{URL('/assets/images/edit.png')}}" class="icons"  title="Edit">
                        </a>
                        <a href="javascript:void(0)" class="delete_cat_link" onclick="del_confirm({{$category->id}},'deleterecycletwocategory','Recycle Inventory Category')" data-table_id="{{$category->id}}">
                            <img src="{{URL('/assets/images/del.png')}}" class="icons"  title="Delete">
                        </a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@include('admin.recycle-second.modal',['result' => []]);
@endsection