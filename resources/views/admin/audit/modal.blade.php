<div class="modal fade add-part-num" id="pnModal" tabindex="-1" role="dialog" aria-labelledby="pnModalLabel" aria-hidden="true">
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
			<div class="modal-footer ">
				<button type="button" class="btn btn-default pull-right save" onclick="savePN('addpartnumber')">Save</button> 
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-lg" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="display:inline-block" class="modal-title" id="detModalLabel">Edit Capacity</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				@for($i=1;$i<4;$i++)
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="capacity<?=$i?>">Capacity {{$i}}</label>
							<input type="text" id="capacity<?=$i?>" class="form-control capedit"/>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="qty<?=$i?>">Count</label>
							<div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="quantity-left-minus btn btn-danger btn-number"  data-type="minus" data-row="{{$i}}" data-field="">
                                      <span class="fa fa-minus"></span>
                                    </button>
                                </span>
                                <input type="text" id="qty<?=$i?>" class="form-control input-number capedit" min="1" max="1000">
                                <span class="input-group-btn">
                                    <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus" data-row="{{$i}}" data-field="">
                                        <span class="fa fa-plus"></span>
                                    </button>
                                </span>
                            </div>
						</div>
					</div>
				</div>
				<?php endfor ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary pull-right" onclick="saveDetails()">Save</button>
				<input type="hidden" id="capatype" value=""/>
			</div>
		</div>
	</div>
</div>