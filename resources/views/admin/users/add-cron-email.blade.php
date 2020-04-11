@extends('layouts.appadminlayout')
@section('title' , 'Add Cron Emails')
@section('content')
<div class="row justify-content-center text-center">
	<table  class="table table-hover">
		<tbody>
			<tr> 
				<td>
					<h3 class="float-left">Add Cron Emails</h3>
				</td>
				<td class = "float-right">
					<a class=" btn btn-primary btn-sm btn-sm border" href="{{route('manage.emails')}}" >Cancel</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="col-6">
		<form method="post" action="{{route('manage.emails')}}">   
			@csrf  
			<div class="form-group text-left">
				<label for="email">Email Type</label>
				<input type="text" class="form-control" value="{{$name}}" readonly>
				<input type="hidden" name="cronjobname" value="{{request()->get('t')}}" readonly>
			</div>
			<div class="form-group text-left">
				<label for="sel-bs">User Emails</label>
				<select id="sel-bs" class="form-control input-large" name="cronjob[]" multiple data-live-search="true">
					<option value="" disabled>--Select option--</option>
					@foreach($userEmails as $key => $userEmail)
						<option @if(in_array($key,$result)) selected @endif value="{{$key}}">{{$userEmail}}</option>
					@endforeach
				</select>
			</div>
			<button type="submit" class="btn btn-primary"> Save </button>
		</form>
	</div> 
</div>
@endsection
