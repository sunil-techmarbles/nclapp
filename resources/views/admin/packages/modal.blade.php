	<!-- Check in modal  --> 
	<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="checkModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title" id="checkModalLabel">Check-In Package</h3>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="checkNumber">Please scan or enter the Tracking Number</label>
						<input class="form-control" type="text" id="checkNumber"/>
					</div>

					<div class="form-group">
						<label for="userName">Please enter the responsible User Name</label>
						<input class="form-control" type="text" id="userName" value=""/>
					</div> 

				</div> 
				<div class="modal-footer">
					<button type="button" onclick="checkPackage()" class="btn btn-primary">Submit</button>
				</div>
			</div>
		</div>
	</div>


	<!-- New package / Edit package modal  -->
	<div class="modal fade" id="asinModal" tabindex="-1" role="dialog" aria-labelledby="asinModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form method="post" enctype="multipart/form-data" id="newPackageForm" autocomplete="off" onsubmit="return addNewPackage(event, this, 'addnewpackage')"> 
					<div class="modal-header">
						<h3 class="modal-title" id="asinModalLabel">New Package</h3> 
					</div>
					<div class="modal-body">

						<div style="margin-bottom: 10px">
							<input type="hidden" name="pkg_id" id="pkg_id" value=""/>
							
							<div class="row" style="margin-bottom:5px"> 
								<div class="col-sm-3">
									<div class="form-group-sm">	
										<label for="f_expected_arrival" style="margin:0">Expected Arrival:</label><span class="req">*</span>
										<input class="form-control datepicker" type="text" name="expected_arrival" id="f_expected_arrival"/>
									</div>
								</div> 

								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_description" style="margin:0">Description:</label><span class="req">*</span>
										<input class="form-control" type="text" name="description" id="f_description"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_req_name" style="margin:0">Request Name:</label><span class="req">*</span>
										<input class="form-control" type="text" name="req_name" id="f_req_name"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_tracking_number" style="margin:0">Tracking Number:</label><span class="req">*</span>
										<input class="form-control" type="text" name="tracking_number" id="f_tracking_number"/>
									</div>
								</div>
							</div>
							
							<div class="row" style="margin-bottom:5px">
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_order_date" style="margin:0">Order Date:</label><span class="req">*</span>
										<input class="form-control datepicker" type="text" name="order_date" id="f_order_date"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_ref_number" style="margin:0">Ref. Number:</label>
										<input class="form-control" type="text" name="ref_number" id="f_ref_number"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_carrier" style="margin:0">Carrier:</label><span class="req">*</span>
										<input class="form-control" type="text" name="carrier" id="f_carrier"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_freight_ground" style="margin:0">Freight/Ground:</label><span class="req">*</span>
										<select class="form-control" name="freight_ground" id="f_freight_ground">
											<option value="">Select</option>
											<option value="Freight">Freight</option>
											<option value="Ground">Ground</option>
										</select>
									</div>
								</div>
							</div>
							
							<div class="row" style="margin-bottom:5px">
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_recipient" style="margin:0">Recipient or Dept:</label>
										<input class="form-control" type="text" name="recipient" id="f_recipient"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_worker_id" style="margin:0">Warehouse Worker ID:</label>
										<input class="form-control" type="text" name="worker_id" id="f_worker_id"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_location" style="margin:0">Location:</label>
										<input class="form-control" type="text" name="location" id="f_location"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_received" style="margin:0">Received?</label>
										<select class="form-control" name="received" id="f_received">
											<option value="">Select</option>
											<option value="Y">Yes</option>
											<option value="N" selected="selected">No</option>
										</select>
									</div>
								</div>
							</div>
							
							<div class="row" style="margin-bottom:5px">
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_qty" style="margin:0">Quantity:</label><span class="req">*</span>
										<input class="form-control" type="text" name="qty" id="f_qty"/>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group-sm">
										<label for="f_value" style="margin:0">Value:</label>
										<input class="form-control" type="text" name="value" id="f_value"/>
									</div>
								</div> 
							</div>

						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>