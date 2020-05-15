@extends('layouts.appadminlayout')
@section('title' , 'General Settings')
@section('content')
<div class="row">
	<div class="col-12">
		<form class="itamg-setting" method="post" action="{{route('save.setting')}}">   
			@csrf
			<p class="border-bottom border-secondary my-3">
				<i class="fa fa-podcast" aria-hidden="true"></i>
				Cron Job Setting
			</p>
			<div class="itamg-form">
				<ul class="list-group list-group-flush">
				@foreach($cronJob as $key => $value)
					<li class="list-group-item">						
						<span class="itamg-center">{{$value}}</span>
						<label class="switch">
							<input type="checkbox" name="cronjob[{{$key}}][]" class="success">
							<div class="slider round">
								<span class="on">ON</span><span class="off">OFF</span>
							</div>
						</label>
					</li>
				@endforeach
				</ul>
			</div>
			<div class="float-right mt-2">
				<button type="submit" class="btn btn-primary"> Save </button>
				<a href="{{route('dashboard')}}" class="btn btn-default"> Cancel </a>
			</div>
		</form>
	</div>
</div>
@endsection