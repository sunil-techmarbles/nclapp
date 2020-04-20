$(document).ready(function(){
    var categoryDelete = ($("input[name='recycletwopage']").val() == 'category') ? 'true' : 'false';
    $(document).on("click",".select_all_to_delete", function(e){
        if(this.checked)
        {
            $('.select_to_delete').each(function () {
                $(this).prop('checked', true);
            });
        }
        else
        {
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
                    showLoader();
                    $.ajax({
                        url:"/"+prefix+"/multrecycleinvtdelete",
                        type: 'POST',
                        data: {ids:sList, type: categoryDelete},
                        dataType: 'json'
                    })
                    .done(function(response)
                    {
                        hideLoader();
                        var status = (response.status) ? 'success' : 'error';
                        showSweetAlertMessage(status, response.message, status);
                    }).fail(function(){
                        hideLoader();
                        showSweetAlertMessage('error', 'Something went wrong with ajax !', 'error');
                    });
                    setTimeout(function(){location.reload();}, 2000);
                }
                else if (result.dismiss === Swal.DismissReason.cancel ) 
                {
                    showSweetAlertMessage('warning','Your record is safe :)','warning');
                } 
            })
        }
        else
        {
            showSweetAlertMessage('warning', 'Please select record to delete' ,'warning');
        }
           
    });

    $(document).on('click', '.upload_data_from_files', function(data){
        $('#upload_files').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on('click', '.add_new_entry', function(e){
        if($("input[name='recycletwopage']").val() != 'category')
        {
            $('#add_entry_form input[type="text"]').val('');
            $('#add_entry_form input[name="operation"]').val('add_entry');
            $('#action').val("Save");
            $('#add_entry').modal({
                backdrop: 'static',
                keyboard: false
            });
        }
        else
        {
            $('#cat_entry input[type="text"]').val('');
            $('#cat_entry input[name="operation"]').val('add_cat_entry');
            $('#cataction').val("Save");
            $('#cat_entry').modal({
                backdrop: 'static',
                keyboard: false
            });
        } 
    })

    $(document).on('click', '.cat_entry', function(e){
      
    })

    var ClassName = 'mx-1 btn btn-xs btn-default border border-primary';
    $('#itamg_inventory_value').DataTable({
        dom: 'lipBf',
        buttons: [
            { extend: 'excel', className: ClassName, 
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            { extend: 'csv', className: ClassName,
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            { extend: 'pdf', className: ClassName,
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            { extend: 'copy', className: ClassName,
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
        ],
        aoColumnDefs: [{
            bSortable: false,
            aTargets: [ 0 ]
        }],
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
    });

    $(document).on("click",".edit_faildsearch_address", function(e){
        showLoader();
        e.preventDefault();
        var table_type = $(this).attr('data-table_type');
        $('#user_id').val(table_type);
        $.get("/"+recyclePrefix+"/getfaildsearchemails?type="+table_type, function(result){
            hideLoader();
            if(result.status)
            {
                $('#faildsearchemail').val(result.data.email);
                $('#faildsearchemailsid').val(result.data.type);
                $('#faildsearchemailsoperation').val("update_entry");
                $('#faildsearchemailsaction').val("Update");
                $('#faildsearchemailsidentry').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            }
            else
            {
                showSweetAlertMessage(type = 'error', message = 'something went wrong', icon = 'error');
            }
        }).fail(function (jqXHR, textStatus, error){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        });
    })

    $("#faildsearchemailsidentry_form").validate({
        submitHandler: function(form) {
            $.ajax({
                url: "/"+recyclePrefix+"/getfaildsearchemails",
                type: "POST",
                data: $("#faildsearchemailsidentry_form").serialize(),
                 beforeSend: function (){
                showLoader();
                },
                success: function(result){
                    hideLoader();
                    console.log(result)
                    var status = (result.status) ? 'success' : 'error';
                    showSweetAlertMessage(type = status, message = result.message , icon = status);
                    if(result.status)
                    {
                        location.reload();
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
    });

    $(document).on("click",".edit_entry_link", function(e){
        showLoader();
        e.preventDefault();
        var id = $(this).attr('data-table_id');
        $.get("/"+recyclePrefix+"/getrecordeedit?inventoryid="+id, function(response){
            hideLoader();
            console.log(response);
            if(response.status)
            {
                $('#user_id').val(id);
                $('#modal-title b').text('Edit Itamg inventory')
                $('#model').val(response.data.Model);
                $('#part').val(response.data.PartNo);
                $('#brand').val(response.data.Brand);
                $('#category').val(response.data.Category);
                $('#notes').val(response.data.Notes);
                $('#value').val(response.data.Value);
                $('#status').val(response.data.Status);
                $('#require_pn').val(response.data.require_pn);
                $('#operation').val("update_entry");
                $('#action').val("Update");
                $('#add_entry').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            }
            else
            {
                showSweetAlertMessage(type = 'error', message = 'something went wrong', icon = 'error');   
            }
        }).fail(function (jqXHR, textStatus, error){
            hideLoader();
            showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
        });
    })
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

$(document).on('click', "#search", function(){
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
                $('#user_id').val(id);
                $('#model').val(result.data.model);
                $('#part').val(result.data.part);
                $('#brand').val(result.data.brand);
                $('#category').val(result.data.category);
                $('#require_pn').val(result.data.require_pn);
                $('#action').val("Add");
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
                var status = (result.status) ? 'success' : 'error';
                showSweetAlertMessage(type  = status, message = result.message , icon = status);
                if(result.status)
                {
                    location.reload();
                }
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
$("#cat_entry_form").validate({
    submitHandler: function(form) {
        $.ajax({
            url: "/"+recyclePrefix+"/addrecyclecategory",
            type: "POST",
            data: $("#cat_entry_form").serialize(),
            beforeSend: function (){
                showLoader();
            },
            success: function(result)
            {
                hideLoader();
                var status = (result.status) ? 'success' : 'error';
                showSweetAlertMessage(type  = status, message = result.message , icon = status);
                if(result.status)
                {
                    location.reload();
                }
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

$(document).on('click', '.edit_cat_link', function(e){
    showLoader();
    e.preventDefault();
    var id = $(this).attr('data-table_id');
    $.get("/"+recyclePrefix+"/getcatrecordeedit?categoryid="+id, function(response){
        hideLoader();
        console.log(response);
        if(response.status)
        {
            $('#catId').val(id);
            $('#categoryname').val(response.data.category_name);
            $('#categoryvalue').val(response.data.value);
            $('#catoperation').val("update_cat_entry");
            $('#cataction').val("Update");
            $('#cat_entry').modal({
                backdrop: 'static',
                keyboard: false
            });
        }
        else
        {
            showSweetAlertMessage(type = 'error', message = 'something went wrong', icon = 'error');   
        }
    }).fail(function (jqXHR, textStatus, error){
        hideLoader();
        showSweetAlertMessage(type = 'error', message = 'something went wrong with ajax request' , icon= 'error');
    });
});