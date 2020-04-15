@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('bulksession'))
<div class="alert-warning alert-block">
	<div class="bulk-session-itamg">{!! $message !!}</div>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	Please check the form below for errors
</div>
@endif

@if(request()->get('redirect') == 'true')
	<div class="alert alert-warning alert-block">
		You will be redirected to the 
		    <strong>
				<a class="it-amg-redirect" href="{{route('audit', ['pageaction' => request()->get('pageaction')])}}">main page</a>
			</strong>
		shortly
	</div>
@endif