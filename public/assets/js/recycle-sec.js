$(document).ready(function(){
    setTimeout(function(){
        var ele = '<a class="btn btn-default border border-primary dt-button delete_button"><span>Delete Selected</span></a>';
        $('.dt-buttons').append(ele);
        $('#itamg_inventory_value_processing').after('<br><br>');
        $('.first_heading').removeClass('sorting_asc');
    },1000)

    $(document).on("click",".select_all_to_delete", function(e){
        if(this.checked){
            $('.select_to_delete').each(function () {
                $(this).prop('checked', true);
            });  
        }else{

            $('.select_to_delete').each(function () {
                $(this).prop('checked', false);
            });  
        }
    });  

    $(document).on("click",".delete_button", function(e){
        e.preventDefault();
        var sList = [];
        $('.select_to_delete').each(function () {
            if(this.checked){
                sList.push($(this).val());
            }
        });

        if(sList.length > 0){
            if(confirm('are you sure?')){
                $.post('./ajax/delete_entries_multiple.php',{list:sList}, function(response){

                    swal({
                        title: "Deleted!",
                        text: "Entries deleted successfully.",
                        type: "success",
                        timer: 2000
                    });

                    loadtabledata();             
                });
            }
        }      
    });  

    function loadtabledata(){
        setTimeout(function(){
            location.reload();
        },1000);
    }

    $('.upload_data_from_files').on('click', function(data){
        $('#upload_files').modal('show');
    });

    $('.add_new_entry').on('click', function(e){
        e.preventDefault();
        $('#add_entry').modal('show');
    })  
    var ClassName = 'btn btn-default border border-primary';
    $('#itamg_inventory_value').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: ClassName },
            { extend: 'csv', className: ClassName },
            { extend: 'pdf', className: ClassName },
            { extend: 'copy', className: ClassName },
        ]
    });     

    $(document).on("click",".glyphicon-trash", function(e){
        e.preventDefault();
        var table_id = $(this).attr('data-table_id');
        $('#user_id').val(table_id);

        if(confirm('Are you sure you want to delete?')) {
            $.post('./ajax/delete_entry.php',{table_id:table_id}, function(response){
                if(response){
                    swal({
                        title: "Deleted!",
                        text: "Entry deleted successfully.",
                        type: "success",
                        timer: 2000
                    });

                    loadtabledata();

                }else{
                    swal({
                        title: "Error!",
                        text: "There is some error , try again",
                        type: "failed",
                        timer: 2000
                    });
                }
            });
        }
    });  

    $(document).on("click",".glyphicon-edit", function(e){
        e.preventDefault();
        var table_id = $(this).attr('data-table_id');
        $('#user_id').val(table_id);

        $.post('./ajax/get_entry.php',{table_id:table_id}, function(data){
            var myArray = jQuery.parseJSON(data);

            $('#model').val(myArray.entry.Model);
            $('#part').val(myArray.entry.PartNo);
            $('#brand').val(myArray.entry.Brand);
            $('#category').val(myArray.entry.Category);
            $('#notes').val(myArray.entry.Notes);
            $('#value').val(myArray.entry.Value);
            $('#status').val(myArray.entry.Status);
            $('#require_pn').val(myArray.entry.require_pn);
        });

        $('#edit_entry').modal('show');
    })

    $("#edit_entry_form").validate({
        submitHandler: function(form) {
            $.ajax({
                url: "./ajax/form_edit.php",
                type: "POST",
                data: $("#edit_entry_form").serialize(),
                success: function(response) {
                    if(response){
                        $('#edit_entry').modal('hide');
                        swal({
                            title: "Updated!",
                            text: "Record update successfully.",
                            type: "success",
                            timer: 2000
                        });

                        loadtabledata();

                    }else{
                        swal({
                            title: "Error!",
                            text: "There is some error , try again",
                            type: "failed",
                            timer: 2000
                        });
                    }
                }            
            });
        }
    });

    $("#add_entry_form").validate({
        submitHandler: function(form) {
            $.ajax({
                url: "./ajax/form_save.php",
                type: "POST",
                data: $("#add_entry_form").serialize(),
                success: function(response) {
                    if(response){
                        $('#add_entry').modal('hide');

                        swal({
                            title: "Added!",
                            text: "Record added successfully.",
                            type: "success",
                            timer: 2000
                        });

                        loadtabledata();

                    }else{
                        swal({
                            title: "Error!",
                            text: "There is some error , try again",
                            type: "failed",
                            timer: 2000
                        });
                    }
                }            
            });
        }
    });
});
