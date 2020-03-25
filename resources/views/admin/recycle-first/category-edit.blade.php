@extends('layouts.appadminlayout')
@section('title', 'Recycle First')
@section('content')
<div class="container">
    <div id="page-head">Update Category</div>
    <form action="{{route('update.category.record')}}" method="post" class="category_update_table">
    	@csrf
        <div class="form-group">
            <input type="hidden" name="action" value="update_category" />
            <input type="hidden" name="old_name" value="{{ $categoryData[0]['Type_of_Scrap'] }}" />
            <input type="hidden" name="cat_id" value="{{ $categoryData[0]['id'] }}" />
            <label><strong>Category </strong></label> 
            <input class="form-control" type="text" name="Type_of_Scrap" value="{{$categoryData[0]['Type_of_Scrap']}}" required/>
        </div>
        <div class="form-group">
            <label><strong>Price </strong></label>
            <input class="form-control" type="number" name="PRICE" value="{{$categoryData[0]["PRICE"] == 'NULL' ? 0 : $categoryData[0]["PRICE"]}}" step='0.01' />
        </div>
        <div class="form-group">
            <label><strong>Type </strong></label>
            <select class="form-control" name="TYPE">
                <option value="none">none</option>
                <option value="debt" {{$categoryData[0]["TYPE"] == 'debt' ? 'Selected' : ''}} >debt</option>
                <option value="credit" {{$categoryData[0]["TYPE"] == 'credit' ? 'Selected' : ''}} >credit</option>
            </select>
        </div>
        <div class="form-group">
            <label><strong>Status </strong></label>
            <select class="form-control" name="status">
                <option value="0" {{$categoryData[0]["status"] == 0 ? 'Selected' : '' }} >Enabled</option>
                <option value="1" {{$categoryData[0]["status"] == 1 ? 'Selected' : '' }} >Disabled</option>
            </select>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-success" name="submit" value="Update"/>
            <a href="{{route('recycle.first')}}" class="btn btn-default border">Cancel</a>
        </div>
    </form>
</div>
@endsection