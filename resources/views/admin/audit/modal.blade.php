<div class="modal fade" id="pnModal" tabindex="-1" role="dialog" aria-labelledby="pnModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="display:inline-block" class="modal-title" id="pnModalLabel">Add Part Number</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">X</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="pnModel">Model</label>
							<input type="text" id="pnModel" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="pnPn">Part Number</label>
							<input type="text" id="pnPn" class="form-control"/>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-right" onclick="savePN('addpartnumber')">Save</button> 
			</div>
		</div>
	</div>
</div>