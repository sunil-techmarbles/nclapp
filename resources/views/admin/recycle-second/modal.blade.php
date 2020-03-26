<div id="edit_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="edit_entry_form" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-body">
					<label>Brand</label>
					<input type="text" name="brand" id="brand" class="form-control">
					<label>Model</label>
					<input type="text" name="model" id="model" readonly="" class="form-control">
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
					<input type="hidden" name="operation" id="operation">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="add_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="add_entry_form" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-body">
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
					<input type="hidden" name="operation" id="operation">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="upload_files" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="add_entry_form" action="readdatafromfiles.php" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-body">
					<label>Choose CSV or Excel <a target="_blank" href="./sample/file.csv">Sample</a></label>
					<input accept=".csv, .xls,.xlsx" type="file" name="file" id="model" required="" class="form-control">
				</div>
				<div class="modal-footer">
					<input type="hidden" name="user_id" id="user_id">
					<input type="hidden" name="operation" id="operation">
					<input type="submit" name="action" id="action" class="btn btn-success" value="UPLOAD">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="searchModal" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="search_form" enctype="multipart/form-data">
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
					<input type="submit" name="action" class="btn btn-success" value="Search">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="add_search_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="add_search_entry_form" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-body">
					<div style="text-align: center;font-size: 20px;">
						<b>Searched "part no or model" is not found. Please fill up the below form for record.</b>
					</div>
					<div class="pop-heading">
						<label>Brand</label>
						<input type="text" name="brand" id="brand" class="form-control">
					</div>
					<div class="pop-heading">
						<label>Model</label>
						<input type="text" name="model" id="model" required="" class="form-control">
					</div>
					<div class="pop-heading">
						<label>Part No</label>
						<input type="text" name="part" id="part" class="form-control">
					</div>
					<div class="pop-heading">
						<label>Category</label>
						<select name="category" id="category" class="form-control">
							@foreach($result as $row)
								<option value="{{$row["value"]}}">{{$row["category_name"]}}</option>	
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="user_id" id="user_id">
					<input type="hidden" name="operation" id="operation">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Save">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>