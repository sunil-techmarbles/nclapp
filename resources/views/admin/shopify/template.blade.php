@extends('layouts.appadminlayout')
@section('title', 'Model Template')
@section('content')
<div class="container">
	<div id="page-head">
		Template Edit
	</div>
	<form method="post" id="main-form" autocomplete="off">
		<input type="hidden" name="page" value="tplproc"/>
		<input type="hidden" name="tplid" value="{{@$tplid}}"/>
		<div id="var_tab">
			{!! @$output !!}
		</div>
		<button id="submitBtn" type="submit" class="btn btn-default border border-secondary">Submit</button>
		<div></div>
	</form>
</div>

<script type="text/javascript">
	var items = '<?php echo json_encode($items); ?>';
	var dScores = JSON.parse('<?php echo json_encode(config('constants.damageScores')); ?>');
</script>
@endsection