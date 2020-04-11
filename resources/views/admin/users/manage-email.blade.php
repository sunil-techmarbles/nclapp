@extends('layouts.appadminlayout')
@section('title' , 'Manage Emails')
@section('content')
<div class="container">
	<table id="emils-type-table" class="table table-hover">
		<thead>
			<th></th>
			<th></th>
			<th></th>
			<th colspan="2">Emails Type</th>
		</thead>
		<tbody>
			@foreach($cronjobTypes as $key => $cronjob)
			<tr>
				<td nowrap="">
					<a href="{{route('manage.emails',['t' => $key, 'a' => 'add' ] )}}">
						<img src="{{URL('/assets/images/plus.png')}}" class="icons" title="Add">
					</a>
				</td>
				<td></td>
				<td></td>
				<td colspan="2">{{$cronjob}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection