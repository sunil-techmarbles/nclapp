@extends('layouts.appadminlayout')
@section('title', 'Tracker')
@section('content')
<div class="container">
	<div class="row mt-2">
		<div class="col-md-11">
			<select class="form-control form-control-lg" id="act" name="act" style="font-size: 1.5em; display: inline-block;" onchange="setActivity()">
				<option value="">Please Select</option>
				@foreach($actions as $key => $action)
					<option value="{{htmlentities($action, ENT_QUOTES)}}">{{$action}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-md-1">
			<span style="color:#139999; font-size: 3em;cursor: pointer;" class="fa fa-repeat float-right"></span>
		</div>
	</div>
	<div class="row" style="font-size:2em"></div>
	<div class="row pt-2 mb-2" style="color:#139999; font-size: 3em;cursor: pointer;display: flex; justify-content: space-between;">
		<div class="col-md-5">
			<span id="playPause" class="fa fa-pause" onclick = "ppTime()"></span>
		</div>
		<div class="col-md-2">
			<span class="mx-2" style="font-size: 0.5em;">
				<input id="multiitems" type="checkbox"/> <label for="multiitems">Multi-Items</label>
			</span> 
		</div>
		<div class="col-md-5">
			<span class="fa fa-stop float-right" onclick = "stopTime()"></span>
		</div>
	</div> 
	<div class="row">
		<div class="col-sm-12" style="text-align: center;cursor: pointer;" id="timecnt">
			<span style="color:#139999;width: 250px;height: 250px;display: block;border: 20px solid;margin: auto;border-radius: 125px;" >
				<span id="startbtn" style="font-size: 13em;margin:23px" class="fa fa-play" onclick = "startStop()"></span>
				<div id="timer"  style="font-size: 4em;margin-top:72px;font-weight: bold;display:none" onclick = "startStop()">
					<span id="time">0:00:00<span class="ms">.0</span></span><br>
					<span style="display: block;font-size:12px;margin-top: -10px;font-weight: normal;">Tap to restart</span>
				</div>
			</span>
		</div>
	</div>
</div>
@include('admin.tracker.modal',['actions'  => $actions])
@endsection