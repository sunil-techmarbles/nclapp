@extends('layouts.appadminlayout')
@section('title' , 'View Cron Emails')
@section('content')
<div class="row justify-content-center text-center show-itamg-crons">
	<table  class="table table-hover">
		<tbody>
			<tr> 
				<td>
					<h3 class="float-left">View Cron Emails</h3>
				</td>
				<td class = "float-right">
					<a class=" btn btn-primary btn-sm btn-sm border" href="{{route('manage.emails')}}" >Cancel</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="col-6">
		<div class="form-group text-left">
			<label for="email">Email Type</label>
			<input type="text" class="form-control" value="{{$name}}" readonly>
			<input type="hidden" name="cronjobname" value="{{request()->get('t')}}">
		</div>
		<div class="form-group text-left">
			<label for="show-bs">User Emails</label>
			<ul id="show-bs" class="">
				@if(count($result) > 0)
				@foreach($result as $key => $userEmail)
					<li>{{\App\User::getUserEmailByUserId($userEmail)}}</li>
				@endforeach
				@else
					<li>Nothing Found</li>
				@endif
			</ul>
		</div>
	</div> 
</div>
@endsection
