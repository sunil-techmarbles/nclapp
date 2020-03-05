@extends('layouts.appadminlayout')
@section('title', 'Lookup')
@section('content')
<div class="container">
<form method="post" id="main-form" autocomplete="off">
	<div class="formitem" style="text-align: center;">
		<div class="form-group">
			<label class="ttl" for="text_1">Please enter ASIN<span class="req">*</span></label><br>
			<input type="text" value="" class="form-control" id="asset_num" name="asset_num" onkeyup="filterModels(this.value)" required="true">
		</div>
	</div>
</form>  

<table id="lookup" class="table">
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
				<tr style="cursor: pointer" class="mdlrow" data-model="@php echo strtolower( $model->asin . $model->model ); @endphp" onclick="location.href = '{{route('parts.asin', $model->id)}}'">
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
@endsection

