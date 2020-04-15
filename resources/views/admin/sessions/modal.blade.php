<div id="userModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="user_form" enctype="multipart/form-data">
			@csrf
			<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Missing Part List</h4>
                    <button type="button" class="close float-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="table table-condensed table-hover">
                        <div class="row">
                            <div class="col-9"><b>Part Name</b></div>
                            <div class="col-3"><b>Missing</b></div>
                        </div>
                        <div class="missinglist"></div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
