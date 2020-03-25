@extends('layouts.appadminlayout')
@section('title', 'Recycle First')
@section('content')
<div class="container">
	<div id="page-head">Recycle</div>
    <form action="{{route('recycle.record')}}" class="form-inline mb-3" method="POST">
        @csrf
    	<div class="col-5">
	    	<div class="form-group">
		        <lable for="gross-weight"><strong>Category:</strong></lable>
		        <select class="form-control" name="category" id="category">
		            @foreach($categories as $key => $category)
		                @if($selected == $category)
		                    <option value="{{$category}}" selected>
		                    	{{$category}}
		                	</option>
		                @else
			                <option value="{{$category}}">
			                	{{$category}}
			            	</option>
		                @endif
		            @endforeach
		            <option value="custom-cat" class="custom-cat">Custom</option>
		        </select>
	    	</div>
    	</div>
    	<div class="col-3 ml-2">
	    	<div class="form-group">
		        <lable for="gross-weight"><strong>Gross Weight</strong>:</lable>
		        <input type="number" name="gross_weight" class="form-control gross-weight" required/>
	    	</div>
    	</div>
    	<div class="col-2">
	    	<div class="form-group">
		        <lable for="tare"><strong>Tare(P/G/I):</strong></lable>
		        <div class="form-check">
			        <lable class="form-check-label" for="tare"><strong>P</strong></lable>
			        <input type="radio" name="tare" value="P" class="form-check-input tare" checked/>
		        </div>
		        <div class="form-check">
			        <lable class="form-check-label" for="tare"><strong>G</strong></lable>
			        <input type="radio" name="tare" value="G" class="form-check-input tare"/>
		        </div>
		        <div class="form-check">
			        <lable class="form-check-label" for="tare"><strong>I</strong></lable>
			        <input type="radio" name="tare" value="I" class="form-check-input tare"/>
		        </div>
	    	</div>
    	</div>
    	<div class="col-1">
	        <input type="hidden" name="action" value="new_record" class="tare"/>
	        <input class="btn btn-primary" type="submit" value="Add" />
    	</div>
    </form>
    @if(!empty($recycleDataFiles))
        <table class="table table-striped recycle">
            <tr>
                <th></th>
                <th>Name</th>
                <th>Started On</th>
                <th>Closed On</th>
                <th>Status</th>
                <th>Items Count</th>
                <th></th> 
            </tr>
            @foreach($recycleDataFiles as $p)
                <tr>
                    <td>{{$p['id'] }}</td>
                    <td>
                        <strong>{{$p['name'] }}</strong>
                        <button class="btn btn-sm btn-link recycle-download" data-file_id="{{$p['id']}}" data-file_name_download="{{$p['name']}}"><strong>View</strong></button>
                        @if($p["status"] == 1)
                            <a class="btn btn-sm btn-link" target="__blank" data-file_id="{{$p['id']}}" href="{{route('edit.recycle.record', ["record_id" => $p["id"]])}}" title="Edit">
                                Edit
                            </a>
                        @endif
                    </td>
                    <td>{{$p["started"] }}</td>
                    <td>{{$p["status"] == '1' ? '' : $p["closed"] }}</td>
                    <td>{{$p["status"] == '1' ? 'Open' : 'Closed' }}</td>
                    <td>{{$p["total"] }}</td>
                    <td>
                        @if($p["status"] == 1)
                            <button class="btn btn-sm btn-link recycle-submit" data-file_id="{{$p['id']}}" data-file_name="{{$p['name']}}"><strong>Submit</strong></button>
                        @endif
                    </td>
                </tr>
                @endforeach
        </table>
    @endif
        @if(!empty($unapporovedCategories))
            <div class="heading">
                <h2>Categories</h2>
            </div>
            <div class="category-table">
                <table class="table table-striped">
                    <tr>
                        <th></th>
                        <th>Category Name <span class="glyphicon glyphicon-eye-close" onclick="$('.s_price').toggle()"></span></th>
                        <th class="s_price" style="display: none;">Price</th>
                        <th>Type</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    @foreach ($unapporovedCategories as $key => $unapporovedCategory)
                        <tr>
                            <td></td>
                            <td><strong>{{$unapporovedCategory['Type_of_Scrap']}}</strong></td>
                            <td class="s_price" style="display: none;">
                            	<strong>{{$unapporovedCategory['PRICE']}}</strong>
                            </td>
                            <td><strong>{{$unapporovedCategory['TYPE']}}</strong></td>
                            <td>
                                @if($unapporovedCategory['status'])
                                    <button class="btn btn-link recycle-approve" data-approve_name="{{$unapporovedCategory['Type_of_Scrap']}}">
                                    	<strong>Approve</strong>
                                    </button>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('edit.category.record', ["cat_name" => $unapporovedCategory['Type_of_Scrap']])}}">
                                    <img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="del_confirm('{{$unapporovedCategory['Type_of_Scrap']}}','deleterecyclecategoryrecord','Category')" data-reject_name="{{$unapporovedCategory['Type_of_Scrap']}}" class=" recycle-reject">
                                    <img src="{{URL('/assets/images/del.png')}}" class="icons" title="Delete">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
</div>
@include('admin.recycle-first.modal', ['categories' => (object)[]])
@endsection
