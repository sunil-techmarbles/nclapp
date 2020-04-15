<div id="add_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="add_entry_form" enctype="multipart/form-data">
			@csrf
			<div class="modal-content">
				<div class="modal-body">
					<div id="modal-title" style="text-align: center;font-size: 20px;">
						<b>Add Itamg inventory</b>
					</div>
					<label>Brand</label>
					<input type="text" name="brand" id="brand" class="form-control">
					<label>Model</label>
					<input type="text" name="model" id="model" required="" class="form-control">
					<label>Part No</label>
					<input type="text" name="part" id="part" class="form-control">
					<label>Category</label>
					<select name="category" id="category" class="form-control">
					@foreach($result as $row)
						<option value="{{$row["value"]}}">{{$row["category_name"]}}</option>	
					@endforeach
					</select> 
					<label>Notes</label>
					<input type="text" name="notes" id="notes" class="form-control">
					<label>Value</label>
					<input type="text" name="value" id="value" class="form-control">
					<label>Status</label>
					<select name="status" id="status" class="form-control">
						<option value="Recycle">Recycle</option>
						<option value="Resale">Resale</option>
					</select>
					<label>Require Pn</label>
					<select name="require_pn" id="require_pn" class="form-control">
						<option value="Y">Y</option>
						<option value="N">N</option>
					</select>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="user_id" id="user_id">
					<input type="hidden" name="operation" id="operation" value="add_entry">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="upload_files" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="upload_entry_form" action="{{route('read.data.from.files')}}" enctype="multipart/form-data">
			@csrf
			<div class="modal-content">
				<div class="modal-body">
					<label>Choose CSV or Excel <a target="_blank" href="{{URL('')}}/sample-files/file.csv">Sample</a></label>
					<input accept=".csv, .xls,.xlsx" type="file" name="file" required="" class="form-control">
				</div>
				<div class="modal-footer">
					<input type="submit" name="upload_action" id="upload_action" class="btn btn-success" value="UPLOAD">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="searchModal" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="search_form" enctype="multipart/form-data">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="pop-heading">
						<label>Part NO</label>
						<input type="text" name="model1" id="model1" required="" class="form-control" />
						<br />
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" name="search_action" class="btn btn-success" value="Search">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="add_search_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="add_search_entry_form" enctype="multipart/form-data">
			@csrf
			<div class="modal-content">
				<div class="modal-body">
					<div style="text-align: center;font-size: 20px;">
						<b>Searched "part no or model" is not found. Please fill up the below form for record.</b>
					</div>
					<div class="pop-heading">
						<label>Brand</label>
						<input type="text" name="brand" id="failed_brand" class="form-control">
					</div>
					<div class="pop-heading">
						<label>Model</label>
						<input type="text" name="model" id="failed_model" required="" class="form-control">
					</div>
					<div class="pop-heading">
						<label>Part No</label>
						<input type="text" name="part" id="failed_part" class="form-control">
					</div>
					<div class="pop-heading">
						<label>Category</label>
						<select name="category" id="failed_category" class="form-control">
							@foreach($result as $row)
								<option value="{{$row["value"]}}">{{$row["category_name"]}}</option>	
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="failed_search" id="fail_search" value="true">
					<input type="submit" name="failed_action" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="cat_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="cat_entry_form" enctype="multipart/form-data">
			@csrf
			<div class="modal-content">
				<div class="modal-body">
					<label>Category Name</label>
					<input type="text" name="categoryname" required id="categoryname" class="form-control">
					<label>Value</label>
					<input type="text" name="categoryvalue" required id="categoryvalue" class="form-control">
				</div>
				<div class="modal-footer">
					<input type="hidden" name="catId" id="catId">
					<input type="hidden" name="operation" id="catoperation" value="add_cat_entry">
					<input type="submit" name="action" id="cataction" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="faildsearchemailsidentry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="faildsearchemailsidentry_form" enctype="multipart/form-data">
			@csrf
			<div class="modal-content">
				<div class="modal-body">
					<label>Email Ids</label>
					<input type="text" name="email" id="faildsearchemail"  class="form-control">
				</div>
				<div class="modal-footer">
					<input type="hidden" name="faildsearchemailsid" id="faildsearchemailsid">
					<input type="hidden" name="faildsearchemailsoperation" id="faildsearchemailsoperation">
					<input type="submit" name="action" id="faildsearchemailsaction" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>