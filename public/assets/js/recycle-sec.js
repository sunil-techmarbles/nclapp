$(window).on('load', function (){
    setTimeout(function(){
        var ele = '<a class="btn btn-default border mr-1 border-primary add_new_entry" href="javascript:void(0)">Add</a><a class="btn btn-default border mr-1 border-primary upload_data_from_files" href="javascript:void(0)">Upload CSV or XLS</a><a href="javascript:void(0)" class="btn btn-default border border-primary dt-button delete_button"><span>Delete Selected</span></a>';
        $('.dt-buttons').append(ele);
        $('#itamg_inventory_value_processing').after('<br><br>');
        $('.first_heading').removeClass('sorting_asc');
    },300)
});

$(document).ready(function(){
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
        if(sList.length > 0)
        {
            swalWithBootstrapButtons.fire
            ({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            })
            .then((result) => { 
                
                if (result.value) 
                {  
                    $.ajax({
                        url:"/"+prefix+"/multrecycleinvtdelete",
                        type: 'POST',
                        data: {ids:sList},
                        dataType: 'json'
                    })
                    .done(function(response)
                    {
                        var status = (response.status) ? 'success' : 'error';
                        showSweetAlertMessage(type = status, message = response.message , icon= status);
                    })
                    .fail(function()
                    {
                        showSweetAlertMessage(type = 'error', message = 'Something went wrong with ajax !' , icon= 'error');
                    });
                    setTimeout(function(){location.reload();}, 2000);
                }
                else if (result.dismiss === Swal.DismissReason.cancel ) 
                {
                    showSweetAlertMessage(type = 'warning', message = 'Your record is safe :)' , icon= 'warning');
                } 
            })
        }
        else
        {
            showSweetAlertMessage(type = 'warning', message = 'Please select record to delete' , icon= 'warning');
        }
           
    });  

    function loadtabledata(){
        setTimeout(function(){
            location.reload();
        },1000);
    }

    $(document).on('click', '.upload_data_from_files', function(data){
        $('#upload_files').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on('click', '.add_new_entry', function(e){
        $('#add_entry').modal({
            backdrop: 'static',
            keyboard: false
        });
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
});

function commenAjaxOnSearchResponse(argument){
    $.ajax({
        type: "POST",
        url: "/"+recyclePrefix+"/search",
        data: {
            search: $("#searchtext").val(),
            type: 'second',
        },
        beforeSend: function (){
            showLoader();
        },
        success: function(result){
            hideLoader();
            if(result.value == "We didn't find this Part-no or Model in database")
            {
                $('#add_search_entry').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            }
            else
            {
                $(".searchresult").html(result.value);
                if(argument != '')
                {
                    $(".searchresult").css("display", argument);
                }
            }
        },
        error: function(result){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        }
    }).fail(function (jqXHR, textStatus, error){
        hideLoader();
        showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
    });
}

$( "#search" ).click(function(){
    $.ajax({
        type: "POST",
        url: "/"+recyclePrefix+"/search",
        data: {
            search: $("#searchtext").val(),
            type: "first",
        },
        beforeSend: function (){
            showLoader();
        },
        success: function(result){
            hideLoader();
            console.log(result);
            if(result.value == "Y")
            {
                $('#searchModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                commenAjaxOnSearchResponse(q1='');
            }
            else
            {
                commenAjaxOnSearchResponse(q1="block");
            }
        },
        error: function(result){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        }
    }).fail(function (jqXHR, textStatus, error){
        hideLoader();
        showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
    });
});

$("#add_search_entry_form").validate({
    submitHandler: function(form){
        $.ajax({
            url: "/"+recyclePrefix+"/search",
            type: "POST",
            data: $("#add_search_entry_form").serialize(),
            beforeSend: function (){
                showLoader();
            },
            success: function(result){
                hideLoader();
                $('#add_search_entry').modal('hide');
                var status = (result.status) ? 'success' : 'error';
                showSweetAlertMessage(type = status, message = result.message , icon = status);
            }            
        }).fail(function (jqXHR, textStatus, error){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        });
    }
});

$("#search_form").validate({
    submitHandler: function(form){
        $.ajax({
            type: "POST",
            url: "/"+recyclePrefix+"/search",
            data: {
                search: $("#searchtext").val(),
                search1: $("#model1").val(),
                type: "third",
            },
            beforeSend: function (){
                showLoader();
            },
            success: function(result){
                hideLoader();
                $(".searchresult").html(result.value);
                $(".searchresult").css("display", "block");
                $('#searchModal').modal('hide');
            },
            error: function(result){
                hideLoader();
                showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
            }
        }).fail(function (jqXHR, textStatus, error){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        });
    }
});

$(document).on('click', '.update', function(){
    var id = $(this).data("table_id");
    $.ajax({
        method:"POST",
        url: "/"+recyclePrefix+"/failedsearch",
        data:{id:id},
        dataType:"json",
        beforeSend: function (){
            showLoader();
        },
        success:function(result)
        {
            hideLoader();
            if(result.status)
            {
                $('#add_entry').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#add_entry #user_id').val(id);
                $('#add_entry #model').val(result.data.model);
                $('#add_entry .modal-title').text("Add Itamg inventory");
                $('#add_entry #part').val(result.data.part);
                $('#add_entry #brand').val(result.data.brand);
                $('#add_entry #category').val(result.data.category);
                $('#add_entry #require_pn').val(result.data.require_pn);
                $('#add_entry #action').val("Add");
                $('#add_entry #operation').val("Edit");
            }
            else
            {
                showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
            }
        },
        error: function(result){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        }
    }).fail(function (jqXHR, textStatus, error){
        hideLoader();
        showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
    });
});

$(document).on('submit', '#add_entry_form', function(event){
    event.preventDefault();
    var errorFlag = true;
    if($(this).find('#model').val() == '')
    {
        alert('Model field required');
        errorFlag = false;
    }
    
    if($(this).find('#part').val() == '')
    {
        alert('Part number field required');
        errorFlag = false;
    }
    
    if(errorFlag)
    {
        $.ajax({
            url: "/"+recyclePrefix+"/addinventory",
            method: 'POST',
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function (){
                showLoader();
            },
            success: function(result)
            {
                hideLoader();
                // $('#user_form')[0].reset();
                // $('#userModal').modal('hide');
                var status = (result.status) ? 'success' : 'error';
                showSweetAlertMessage(type  = status, message = result.message , icon = status);
                location.reload();
            },
            error: function(result){
                hideLoader();
                showSweetAlertMessage(type = 'error', message = 'something went wrong' , icon= 'error');
            }
        }).fail(function (jqXHR, textStatus, error){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        });
    }
});