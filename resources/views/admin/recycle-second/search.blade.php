@extends('layouts.appadminlayout')
@section('title', 'Recycle Search')
@section('content')
<div class="container box">
	<div class="box-cen">
		<h3 align="center"><strong>Search Part-no or Model</strong></h3>
		<!-- The form -->
		<form>
  			<div class="row mx-0 form-group">
				<div class="col-10 search-form">
					<input type="text" class="form-control" placeholder="Search.." name="search" id="searchtext">
				</div>
				<div class="col-2">
					<button type="submit" class="btn btn-success" id="search">Search</button>
					<a href="{{route('recycle.second')}}" class="btn btn-info float-right">Back</a>
				</div>
  			</div>
  		</form>
	</div>
	<h3 align="center"><strong></strong>
		<p class="searchresult" style="display: none;"></p>
	</h3>
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
<div id="add_entry" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="add_entry_form" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-body">
					<div style="text-align: center;font-size: 20px;"><b>Searched "part no or model" is not found. Please fill up the below form for record.</b></div>
					<br>
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

							<?php   include('database_connection.php');
							$statement = $connect->prepare("SELECT * FROM category");
							$statement->execute();
							$result = $statement->fetchAll();
							foreach($result as $row)
								{?>

									<option value="<?php echo $row["value"]; ?>"><?php echo $row["category_name"]; ?></option>

									<br />
								<?php } ?>
							</select>  </div>
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
@endsection