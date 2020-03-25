<div class="modal fade" id="catModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert-error"></div>
                <form action="" class="form-horizontal" id="add_category" method="POST" autocomplete="off">
                    <lable><strong>Category Name</strong></lable><br><br>
                    <input type="text" class="form-control category_name" name="category_name" maxlength="255" required><br>
                    <button type="submit" class="btn btn-primary add_new_category">Add Category</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit-record-model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog dialog-term-condition" role="document">
        <div class="modal-content content-term-condition">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Record</h4>
                <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body body-term-condition"> 
                <form class="modal-form" id="edit-record-form">
                    <input type="hidden" class="record_id" name="record_id" />
                    <input type="hidden" class="action" name="action" value="edit_record" />
                    <div class="form-group">
                        <lable><strong>Category</strong></lable>
                        <select name="category" class="category form-control">
                            @foreach ($categories as $key => $category)
                                <option value="{{$category}}" selected>{{$category}}</option>
                            @endforeach 
                        </select>
                    </div>
                    <div class="form-group">
                        <lable><strong>Gross Weight</strong></lable>
                        <input type="number" name="gross_weight" class="gross-weight form-control" value=""/>
                    </div>
                    <div class="form-group">
                        <lable><strong>Tare(P/G/I)</strong></lable>
                        <div class="form-control">
                            <strong>P</strong>&nbsp;
                            <input type="radio" name="tare" value="P" class="tare" />&nbsp;&nbsp;&nbsp;
                            <strong>G</strong>&nbsp;
                            <input type="radio" name="tare" value="G" class="tare"/>&nbsp;&nbsp;&nbsp;
                            <strong>I</strong>&nbsp;
                            <input type="radio" name="tare" value="I" class="tare"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
