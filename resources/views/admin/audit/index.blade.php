@extends('layouts.appadminlayout')
@section('title', 'Audit')
@section('content')
<div class="container itmg-audit">
	<div id="page-head">
		Audit Data Entry
		<span class="fa fa-repeat" style="cursor: pointer" onclick="getLastInput()"></span>
		@if(request()->get('edit'))
			<h3>Search & Edit</h3>
		@endif
	</div>
	<div class="row justify-content-center text-center">
		<div class="col-12">
			<form method="post" id="main-form" action="{{route('store.audit.record')}}">    
				@csrf
				<input type="hidden" name="asinid" id="asinid" value="0"/>
				<input type="hidden" name="refurb" id="refurb" value="0"/>
				<input type="hidden" name="modelid" id="modelid" value="0"/>
				<input type="hidden" name="cpuname" id="cpuname" value=""/>
				<input type="hidden" name="grade" id="grade" value=""/> 
				<div class="form-initial" style="text-align: center;">{!!$output!!}</div>
				<div id="uhint">
					<div class="close-link">
						<span style="cursor:pointer; font-weight: bold;" onclick="$('#uhint').hide()">[X]</span>
					</div>
					<div id="hints">&nbsp;</div>
				</div>
				<div id="var_tab"></div>
				<button style="display: none;" id="reviewBtn" type="button" class="btn btn-primary" onclick="CheckRequired()">Review</button>
				<button style="display: none;" id="submitBtn" type="submit" class="btn btn-success">Submit</button>
				<div>&nbsp;</div>
			</form>
			<div id="preview">
				<h3>Review Data Entered</h3>
				<div id="preview-content"></div>
			</div> 
		</div>
	</div>
</div>
@include('admin.audit.modal')
<script type="text/javascript">
	var dScores = '<?php echo json_encode($damageScores); ?>';
	var ref_BL = '<?php echo json_encode($refurbBlacklist); ?>';
</script>
@endsection