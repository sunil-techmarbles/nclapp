@extends('layouts.appadminlayout')
@section('title', 'Asin Inventry')
@section('content')
<div class="container">
	<div id="page-head" class="noprint">
		Remove Asset Id 
	</div>
	<ul class="wipeReportNav">
		<li><a class="btn btn-info" href="{{route('asininventry',['pageaction' => request()->get('pageaction')])}}">Asin Inventory</a></li>
	</ul>
	<div class="modal-header">
		<h3 class="modal-title" id="asinModalLabel">Add Asset Id's to Remove From Session</h3>
	</div>
	<form action = "{{route('asininventry.removeasset',['pageaction' => request()->get('pageaction')])}}" method="post">
	<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
		@csrf
		<div class="modal-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label for="asset1">Asset Numbers (one per line)</label>
						<textarea id="asset1" required="required" name="assetIds" rows=10 class="form-control"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="submit" id="removeasset" class="btn btn-primary">Remove Assets</button>
		</div>
	</form>
</div>
@endsection
