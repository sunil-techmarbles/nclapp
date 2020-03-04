@extends('layouts.appadminlayout')
@section('title', 'Lookup')
@section('content')

	<table id="lookup" class="table table-hover">
		<thead>
			<th>ASIN</th>
			<th>Model</th>
			<th>Form Factor</th>
			<th>CPU</th>
			<th>RAM</th>
			<th>HDD</th>
		<thead>
		<tbody>
			@foreach( $models as $model )
			<tr style="cursor: pointer" class="mdlrow" data-model="" onclick="">
				<td>{{ $model->asin }} </td>
				<td>{{ $model->model }}</td>
				<td>{{ $model->form_factor }}</td>
				<td>{{ $model->cpu_core }} {{ $model->cpu_model }} {{ $model->cpu_speed }}</td>
				<td>{{ $model->ram }}</td>
				<td>{{ $model->hdd }}</td>
			</tr>
			@endforeach 
		</tbody>
	</table>
</div>
@endsection;