$(document).ready(function(){
	$('#asset').focus();
	$(document).keydown(function(event){
		if(event.keyCode == 13) {
			if($("#asset").is(":focus"))
			{
				event.preventDefault();
				if ($("#asset").val().length > 3) $('#main-form').submit();
				return true;
			}
			else
			{
				return true;
			}
		}
	});
});

$(document).ready(function()
{
	$('#supplies, #asins, #users_table ').DataTable();
	
	$('#shipment').DataTable({
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});
	
	$('#shipment-asin, #sessions, #sessions-asins, #sessions-asins-part').DataTable({
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});

	$('#lookup').DataTable({
		"searching": false,
		"bPaginate": false, 
	}); 

	if($('#supplie').length > 0)
	{
		$( "#supplie" ).validate({
			rules: {
				qty: {
					digits: true
				},
				low_stock: {
					digits: true
				},
				reorder_qty: {
					digits: true
				},
			}
		});		
	}

	if($('#asins-validation').length > 0)
	{
		$( "#asins-validation" ).validate();	
	}

	if($('.alert').length > 0)
	{
		setTimeout(function(){ $('.alert').hide()}, 3000);
	}

	if($(".email_list").length > 0)
	{
		var earr=[];
		$(".email_list").on('change', function(argument) {
			earr=[];
			$('.email_list').each(function(){
				if($(this).prop('checked')) earr.push($(this).val());
			});
			$('input[name=email]').val(earr.join(','));
		});
	}
})

function reorderItem(iid, dqty, url) 
{
	var qty = prompt('Please enter reorder quantity (default is ' + dqty +')', dqty);
	if (qty == null || qty == '') 
	{
		return false;
	} 
	else 
	{
		$.ajax({
			url: url,
			type: 'GET',
			data: {supplieid: iid, quantity: qty},
			dataType: 'json'
		})
		.done(function(response)
		{
			swalWithBootstrapButtons.fire( 
					'Deleted!',
					response.message ,
					response.status
			) 
		}) 
		.fail(function()
		{
			Swal.fire({
					  icon: 'error',
					  title: 'Oops...',
					  text: 'Something went wrong with ajax !',
				})
		});
	}
}

function getAssetData(fId)
{
	var asin=$('#asset_num').val();
	if (asin.length >= 10) 
	{
		$.get("ajax.php?action=getASIN&asin="+asin+"&t="+Math.random(), function(data) 
		{
			if(data=='0') 
			{
				alert('ASIN not found. Please check and try again');
			} 
			else 
			{
				location.href = 'index.php?page=parts&model='+data;
			}
		});
	}
}

function filterModels(str)
{
	if (str.length > 2) 
	{
		$('.mdlrow').hide();
		$("tr[data-model*='" + str.toLowerCase() +"']" ).show();
	} 
	else 
	{
		$('.mdlrow').show();
	}
}

function deptFilter()
{
	$('.invrow').hide();
	$('.dcb').each(function()
	{
		if($(this).prop('checked')) 
		{
			$(".invrow[data-dept='" + $(this).val() +"']").show();
		}
	});
}

function del_confirm(id,url,text)
{

	const swalWithBootstrapButtons = Swal.mixin({
		customClass: {
			confirmButton: 'btn btn-success',
			cancelButton: 'btn btn-danger'
		},
		buttonsStyling: true
	})

	swalWithBootstrapButtons.fire({
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
				url: url+'/'+id,
				type: 'GET',
				dataType: 'json'
			})
			.done(function(response)
			{ 
				console.log(response)
				swalWithBootstrapButtons.fire( 
					'Deleted!',
					response.message ,
					response.status
				) 
			})
			.fail(function()
			{
				Swal.fire({
					  icon: 'error',
					  title: 'Oops...',
					  text: 'Something went wrong with ajax !',
				})

			});
			setTimeout(function(){location.reload();}, 2000);
		}
		else if (result.dismiss === Swal.DismissReason.cancel ) 
		{
			swalWithBootstrapButtons.fire(
				'Cancelled',
				'Your record is safe :)',
				'error'
			)
		} 
		
	})
}


function getAssetData(fId)
{
	var asin=$('#asset_num').val();
	if (asin.length >= 10)
	{
		$.get("ajax.php?action=getASIN&asin="+asin+"&t="+Math.random(), function(data) 
		{
			if(data=='0')
			{
				alert('ASIN not found. Please check and try again');
			}
			else
			{
				location.href = 'index.php?page=parts&model='+data;
			}
		});
	}
}

function setBulkAsin(sid)
{
	var asset = sid.replace("asset","");
	var aid = $('#'+sid).val();
	if(aid !='')
	{
		$.get("ajax.php?action=setBulkAsin&aid="+aid+"&asset="+asset, function(data) {
			$('#'+data).parent().hide();
		}); 
	}
}
