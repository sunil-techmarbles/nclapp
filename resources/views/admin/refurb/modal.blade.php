<div class="modal fade bd-example-modal-lg" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="display:inline-block" class="modal-title" id="detModalLabel">COA Config</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="coa_sess" name="coa_sess" value="0"/>
				<input type="hidden" name="page" value="shipments"/>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="old_coa">Old COA</label>
							<input type="text" id="old_coa" required="required" name="old_coa" class="form-control"/>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="new_coa">New COA</label>
							<input type="text" id="new_coa" required="required" name="new_coa" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="message" style="color: red;"></div>
				<div class="message1" style="color: red;"></div>
				<div class="row">
					<div class="col-sm-12">
						<button type="button" onclick="saveCoa()" class="btn btn-primary pull-right">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="asinModal" tabindex="-1" role="dialog" aria-labelledby="asinModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="asinModalLabel">Model Selection</h3>
			</div>
			<div class="modal-body">
				<h4>Current Specs: <span id="mspecs"></span></h4>
				<div><b>Multiple or partial matches found. Please select appropriate model from ASIN lookup</b></div>
				<table class="table table-striped table-condensed table-hover" id="asintable"></table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" onclick="setNonAsin()">Non-Asin</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="wsModal" tabindex="-1" role="dialog" aria-labelledby="wsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="wsModalLabel">Bulk Wholesale</h3>
			</div>
			<div class="modal-body">
				<h4>Scan Asset Numbers (one per line): <span id="mspecs"></span></h4>
				<textarea id="ws_list" class="form-control" rows="10"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="setWholesale()">Submit</button>
			</div>
		</div>
	</div>
</div>