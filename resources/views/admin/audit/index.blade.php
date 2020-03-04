@extends('layouts.appadminlayout')
@section('title', 'Audit')
@section('content')

<div id="page-head">
	Audit Data Entry
	<span class="glyphicon glyphicon-repeat" style="cursor: pointer" onclick="getLastInput()"></span>
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
				@php echo $output @endphp
			</div> 
		</form>  
	</div>
</div>
@endsection