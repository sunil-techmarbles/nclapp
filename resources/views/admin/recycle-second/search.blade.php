@extends('layouts.appadminlayout')
@section('title', 'Recycle Search')
@section('content')
<div class="container box">
	<div class="box-cen">
		<h3 align="center"><strong>Search Part-no or Model</strong></h3>
		<!-- The form -->
		<div class="row mx-0 form-group">
		<div class="col-10 search-form">
			<input type="text" class="form-control" placeholder="Search.." name="search" id="searchtext">
		</div>
		<div class="col-2">
			<button type="submit" class="btn btn-success" id="search">Search</button>
			<a href="{{route('recycle.second')}}" class="btn btn-info float-right">Back</a>
		</div>
		</div>
	</div>
	<h3 align="center"><strong></strong>
		<p class="searchresult" style="display: none;"></p>
	</h3>
</div>
@include('admin.recycle-second.modal',['result' => $result]);
@endsection