@extends('layouts.appadminlayout')
@section('title', 'Recycle Search')
@section('content')
<div class="container box assest-lookup-search">
	<div class="admin-access-Itamg row">
		<div class="col text-center">
			<a class="btn btn-info mx-1" href="{{route('recycle.second', ['pageaction' => request()->get('pageaction')])}}">Admin Access</a>
		</div>
	</div>
	<div class="box-cen">
		<h3 align="center"><strong>Search Part-no or Model</strong></h3>
		<div class="row mx-0 form-group">
		<div class="col-10 search-form">
			<input type="text" class="form-control" placeholder="Search.." name="search" id="searchtext">
		</div>
		<div class="col-2">
			<button type="submit" class="btn btn-success" id="search">Search</button>
		</div>
		</div>
	</div>
	<h3 align="center"><strong></strong>
		<p class="searchresult" style="display: none;"></p>
	</h3>
</div>
@include('admin.asset-lookup.modal',['result' => $result]);
@endsection
