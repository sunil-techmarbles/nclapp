@extends('layouts.appadminlayout')
@section('title', 'Add Supply')
@section('content')
<div class="mte_content">
	<div style="width: 100%">
		<form id="supplie" method="post" action="{{route('store.supplies')}}">   
			@csrf 
			<table class="table" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td class="float-right">
							<button type="submit" class="btn btn-primary"> Save </button> 
							<a class="btn btn-default btn-sm border" href="{{route('supplies')}}">Back</a>
						</td>
						<td class="float-left">
							<h3>Add Supply</h3>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table" cellspacing="0" cellpadding="10" style="width:100%; margin: 0">
				<tbody>
					<tr style="background:#eee">
						<td><b>Item ID</b></td>
						<td><input type="hidden" name="id" value="">[auto increment]</td>
						<td style="min-width:300px;"></td>
					</tr>

					<tr style="background:#fff">
						<td><b>Item Name</b></td>
						<td>
							<input type="text" value="{{ old('item_name') }}" required name="item_name" value="" maxlength="varchar(200)" class="mte_req" id="id_1">
							@if($errors->has('firstname'))
							<div class="error">{{ $errors->first('item_name') }}</div>
							@endif
						</td>
						<td style="min-width:300px;">[item_name]</td>
					</tr>

					<tr style="background:#eee">
						<td><b>URL</b></td>
						<td><input type="text" value="{{ old('item_url') }}" name="item_url" value="" maxlength="varchar(500)" id="item_url"></td>
						<td style="min-width:300px;">[item_url]</td>
					</tr>

					<tr style="background:#fff">
						<td><b>Quantity</b></td>
						<td>
							<input type="text" value="{{ old('qty') }}" required name="qty" value="" maxlength="int(11)" class="mte_req" id="id_2">
							@if($errors->has('qty'))
								<div class="error">{{ $errors->first('qty') }}</div>
							@endif
						</td>
						<td style="min-width:300px;">[qty]</td>
					</tr>

					<tr style="background:#eee">
						<td><b>P/N</b></td>
						<td>
							<input type="text" value="{{ old('part_num') }}" required name="part_num" value="" maxlength="varchar(100)" class="mte_req" id="id_3">
							@if($errors->has('part_num'))
								<div class="error">{{ $errors->first('part_num') }}</div>
							@endif
						</td>
						<td style="min-width:300px;">[part_num]</td>
					</tr>

					<tr style="background:#fff">
						<td><b>Description</b></td>
						<td><textarea name="description" id="description">{{ old('description') }}</textarea></td>
						<td style="min-width:300px;">[description]</td>
					</tr>

					<tr style="background:#eee">
						<td><b>Applicable Models</b></td>
						<td>
							<div style="max-height:250px;overflow:auto">
								@foreach($models as $model)
									<label style="display:block">
										<input name="applicable_models[]" type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="{{$model->id}}">
										{{$model->asin}} {{$model->model}} {{$model->form_factor}} 
									</label>
								@endforeach						
							</div>
						</td>
						<td style="min-width:300px;">Select all applicable Models</td>
					</tr>

					<tr style="background:#fff">
					<td><b>Department</b></td>
					<td>
						<input required value="{{ old('dept') }}" type="text" name="dept" value="" maxlength="varchar(100)" class="mte_req" id="id_4">
						@if($errors->has('dept'))
					 		<div class="error">{{ $errors->first('dept') }}</div>
					 	@endif
					 </td>
					<td style="min-width:300px;">[dept]</td>
					</tr>

					<tr style="background:#eee">
					<td><b>Price</b></td>
					<td>
						<input required value="{{ old('price') }}" type="text" name="price" value="" maxlength="double" class="mte_req" id="id_5">
						@if($errors->has('price'))
					 		<div class="error">{{ $errors->first('price') }}</div>
					 	@endif
					 </td>
					<td style="min-width:300px;">[price]</td>
					</tr>

					<tr style="background:#fff">
					<td><b>Vendor</b></td>
					<td>
						<input required value="{{ old('vendor') }}" type="text" name="vendor" value="" maxlength="varchar(200)" class="mte_req" id="id_6">
						@if($errors->has('vendor'))
					 		<div class="error">{{ $errors->first('vendor') }}</div>
					 	@endif
					 </td>
					<td style="min-width:300px;">[vendor]</td>
					</tr>

					<tr style="background:#eee">
					<td><b>Low Stock</b></td>
					<td>
						<input required value="{{ old('low_stock') }}" type="text" name="low_stock" value="" maxlength="int(11)" class="mte_req" id="id_7">
						@if($errors->has('low_stock'))
					 		<div class="error">{{ $errors->first('low_stock') }}</div>
					 	@endif
					 </td>
					<td style="min-width:300px;">[low_stock] Threschold at which the notification will be sent</td>
					</tr>

					<tr style="background:#fff">
					<td><b>Reorder Qty</b></td>
					<td>
						<input required value="{{ old('reorder_qty') }}" type="text" name="reorder_qty" value="" maxlength="int(11)" class="mte_req" id="id_8">
						@if($errors->has('reorder_qty'))
					 		<div class="error">{{ $errors->first('reorder_qty') }}</div>
					 	@endif
					</td>
					<td style="min-width:300px;">[reorder_qty]</td>
					</tr>

					<tr style="background:#eee">
					<td><b>Delivery Time</b></td>
					<td><input type="text" value="{{ old('dlv_time') }}" name="dlv_time" value="" maxlength="varchar(500)" id="dlv_time"></td>
					<td style="min-width:300px;">[dlv_time]</td>
					</tr>

					<tr style="background:#fff">
					<td><b>Bulk Options</b></td>
					<td><textarea name="bulk_options" id="bulk_options">{{ old('bulk_options') }}</textarea></td>
					<td style="min-width:300px;">[bulk_options]</td>
					</tr>

					<tr style="background:#eee">
						<td><b>Emails</b></td>
						<td><input type="text" name="email" value="" maxlength="varchar(500)" id="emails">
						@foreach($adminEmails as $emails)
							<label style="display:block">
							<input name="emails[]" type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="email_list" value="{{$emails}}">{{$emails}}</label><label style="display:block">
						@endforeach
						</td>
						<td style="min-width:300px;">Emails for notifications separated by comma</td>
					</tr>

					<tr style="background:#fff">
						<td><b>Subject</b></td>
						<td><input type="text" name="email_subj" value="Running Low On An Item! - Reorder Request" maxlength="varchar(500)" id="email_subj"></td>
						<td style="min-width:300px;"></td>
					</tr>

					<tr style="background:#eee">
						<td><b>Email Template</b></td>
						<td><textarea name="email_tpl" id="email_tpl">{{$emailTemplate}}</textarea></td>
						<td style="min-width:300px;">You can use variable names listed above</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
@endsection