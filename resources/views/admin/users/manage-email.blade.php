@extends('layouts.appadminlayout')
@section('title' , 'Manage Emails')
@section('content')
<div class="container">
	<table id="emils-type-table" class="table table-hover">
		<thead>
			<th>Action</th>
			<th>Type</th>
		</thead>
		<tbody>
			@foreach($cronjobTypes as $key => $cronjob)
			<tr>
				<td>
					<small>
						<a class="btn btn-xs btn-default border border-primary" href="{{route('manage.emails',['t' => $key, 'a' => 'add'])}}">
							Add
						</a>
					</small>
					<small>
						<a class="btn btn-xs btn-default border border-primary" href="{{route('manage.emails',['t' => $key, 'a' => 'view'])}}">
							View
						</a>
					</small>
				</td>
				<td><a href="{{route('manage.emails',['t' => $key, 'a' => 'add' ] )}}">
				{{$cronjob}}
				</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection