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