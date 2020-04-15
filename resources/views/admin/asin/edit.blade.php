@extends('layouts.appadminlayout')
@section('title', 'Edit ASIN')
@section('content')
<div class="mte_content">
	<form id="asins-validation" method="post" action="{{route('update.asin')}}">
		<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
		@csrf 
		<div>
			<table class="table" cellspacing="0" cellpadding="0" style="border: 0">
				<tbody>
					<tr>
						<td class="float-right">
							<button type="submit" class="btn btn-primary"> Update </button> 
							<a class="btn btn-default btn-sm border back-btn btn-secondary" href="{{route('asin', ['pageaction' => request()->get('pageaction')])}}">Back</a>
						</td>
						<td class="float-left">
							<h3>Edit ASIN</h3>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="width: 100%">
			<table class="table" cellspacing="0" cellpadding="10" style="width:100%; margin: 0">
				<tbody>
					<tr style="background:#eee">
						<td><b>ID</b></td>
						<td><input type="hidden" name="id" value="{{$asinDetail['id']}}">[auto increment]</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>ASIN</b></td>
						<td>
							<input type="text" required name="asin" value="{{old('asin', $asinDetail['asin'])}}" maxlength="varchar(20)" class="mte_req" id="id_1">
							@if($errors->has('asin'))
								<div class="error">{{ $errors->first('asin') }}</div>
							@endif
						</td>
						<td style="min-width:300px;">ASIN</td>
					</tr>
					<tr style="background:#eee">
						<td><b>Price</b></td>
						<td>
							<input type="text" name="price" value="{{old('price', $asinDetail['price'])}}" maxlength="double" id="price">
							@if($errors->has('price'))
								<div class="error">{{ $errors->first('price') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>Manufacturer</b></td>
						<td>
							<input required type="text" name="manufacturer" value="{{old('manufacturer', $asinDetail['manufacturer'])}}" maxlength="varchar(100)" class="mte_req" id="id_2">
							@if($errors->has('manufacturer'))
								<div class="error">{{ $errors->first('manufacturer') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#eee">
						<td><b>Model</b></td>
						<td>
							<input required type="text" name="model" value="{{old('model', $asinDetail['model'])}}" maxlength="varchar(200)" class="mte_req" id="id_3">
							@if($errors->has('model'))
								<div class="error">{{ $errors->first('model') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>Model Alias</b></td>
						<td><input type="text" name="model_alias" value="{{old('model_alias', $asinDetail['model_alias'])}}" maxlength="varchar(500)" id="model_alias"></td>
						<td style="min-width:300px;">All possible alternative spellings</td>
					</tr>
					<tr style="background:#eee">
						<td><b>Form notifications</b></td>
						<td>
							<select  name="notifications" id="notifications">
								<option @if($asinDetail['notifications'] == 1) selected @endif value="1">Yes</option>
								<option @if($asinDetail['notifications'] == 0) selected @endif value="0">No</option>
							</select>
						</td>
						<td style="min-width:300px;">Show notifiations in main form</td>
					</tr>
					<tr style="background:#fff">
						<td><b>Form Factor</b></td>
						<td>
							<input required type="text" name="form_factor" value="{{old('form_factor', $asinDetail['form_factor'])}}" maxlength="varchar(200)" class="mte_req" id="id_4">
							@if($errors->has('form_factor'))
								<div class="error">{{ $errors->first('form_factor') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#eee">
						<td><b>CPU Core</b></td>
						<td>
							<input required type="text" name="cpu_core" value="{{old('cpu_core', $asinDetail['cpu_core'])}}" maxlength="varchar(20)" class="mte_req" id="id_5">
							@if($errors->has('cpu_core'))
								<div class="error">{{ $errors->first('cpu_core') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>CPU Model</b></td>
						<td>
							<input required type="text" name="cpu_model" value="{{old('cpu_model', $asinDetail['cpu_model'])}}" maxlength="varchar(50)" class="mte_req" id="id_6">
							@if($errors->has('cpu_model'))
								<div class="error">{{ $errors->first('cpu_model') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#eee">
						<td><b>CPU Speed</b></td>
						<td>
							<input required type="text" name="cpu_speed" value="{{old('cpu_speed', $asinDetail['cpu_speed'])}}" maxlength="varchar(50)" class="mte_req" id="id_7">
							@if($errors->has('cpu_speed'))
								<div class="error">{{ $errors->first('cpu_speed') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>RAM</b></td>
						<td>
							<input required type="text" name="ram" value="{{old('ram', $asinDetail['ram'])}}" maxlength="varchar(50)" class="mte_req" id="id_8">
							@if($errors->has('ram'))
								<div class="error">{{ $errors->first('ram') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#eee">
						<td><b>HDD</b></td>
						<td>
							<input required type="text" name="hdd" value="{{old('hdd', $asinDetail['hdd'])}}" maxlength="varchar(50)" class="mte_req" id="id_9">
							@if($errors->has('hdd'))
								<div class="error">{{ $errors->first('hdd') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>OS</b></td>
						<td>
							<input required type="text" name="os" value="{{old('os', $asinDetail['os'])}}" maxlength="varchar(100)" class="mte_req" id="id_10">
							@if($errors->has('os'))
								<div class="error">{{ $errors->first('os') }}</div>
							@endif
						</td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#eee">
						<td><b>Webcam</b></td>
						<td>
							<select required name="webcam" class="mte_req" id="id_11">
								<option @if($asinDetail['webcam'] == 'Yes') selected @endif value="Yes">Yes</option>
								<option @if($asinDetail['webcam'] == 'No') selected @endif value="No">No</option>
							</select>
							@if($errors->has('webcam'))
								<div class="error">{{$errors->first('webcam')}}</div>
							@endif
						</td>
						<td style="min-width:300px;">Yes/No</td>
					</tr>
					<tr style="background:#fff">
						<td><b>Notes</b></td>
						<td><textarea name="notes" id="notes">{{old('notes', $asinDetail['notes'])}}</textarea></td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#eee">
						<td><b>Link</b></td>
						<td><input type="text" name="link" value="{{old('link', $asinDetail['link'])}}" maxlength="varchar(250)" id="link"></td>
						<td style="min-width:300px;"></td>
					</tr>
					<tr style="background:#fff">
						<td><b>Shopify Product ID</b></td>
						<td><input type="text" name="shopify_product_id" value="{{old('shopify_product_id', $asinDetail['shopify_product_id'])}}" maxlength="varchar(250)" id="shopify_product_id"></td>
						<td style="min-width:300px;"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div>
@endsection
