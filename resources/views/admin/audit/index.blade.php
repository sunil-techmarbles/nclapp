@extends('layouts.appadminlayout')
@section('title', 'Audit')
@section('content')
<div class="container">
	<div id="page-head">
		Audit Data Entry
		<span class="fa fa-repeat" style="cursor: pointer" onclick="getLastInput()"></span>
	</div>

	<div class="row justify-content-center text-center">
		<div class="col-6">
			<form method="post" id="main-form" action="">    
				@csrf  
				<input type="hidden" name="page" value="proc"/>
				<input type="hidden" name="asinid" id="asinid" value="0"/>
				<input type="hidden" name="refurb" id="refurb" value="0"/>
				<input type="hidden" name="modelid" id="modelid" value="0"/>
				<input type="hidden" name="cpuname" id="cpuname" value=""/>
				<input type="hidden" name="grade" id="grade" value=""/> 

				<div class="form-initial" style="text-align: center;">
					{!!$output!!}
				</div> 
			</form>  
		</div>
	</div>
</div>
@include('admin.audit.modal')
@endsection