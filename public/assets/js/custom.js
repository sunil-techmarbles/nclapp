$(document).ready(function () { 
	$("#newPackageForm input").keyup(function(){
		$(this).siblings('.text-danger').text('');   
	});  

	$(" #newPackageForm select, #newPackageForm input ").change(function(){
		$(this).siblings('.text-danger').text('');   
	});

	$('.datepicker').datepicker({format: "yyyy-mm-dd"}); 
	$(".daterange").daterangepicker({
		opens: 'left',
		locale: {
			format: 'YYYY-MM-DD'
		},
		autoUpdateInput: false
	}).on('apply.daterangepicker', function(ev, picker){
		picker.element.val(picker.startDate.format(picker.locale.format)+' - '+picker.endDate.format(picker.locale.format));
	});

	$('#newPackageForm').validate({
		rules: { 
			expected_arrival: {
				required: true
			},
			description: {
				required: true,
			},
			req_name: {
				required: true,
			},
			tracking_number: {
				required: true,
			},
			order_date: {
				required: true,
			},
			carrier: {
				required: true,
			},
			freight_ground: {
				required: true,
			},
			qty:{
				required: true,
				number: true
			}
		},
		submitHandler: function() {  
			addNewPackage(event, 'addnewpackage');
		}
	}); 

});

function savePN(url)
{
	var modal = $('#pnModel').val();
	var partnumber = $('#pnPn').val();

	if(!modal || !partnumber) 
	{
		Swal.fire
		({
			icon: 'error',
			title: 'Oops...',
			text: 'Please enter Model and Part Number !',
		})
		return false;
	} 

	$.ajax({
		url: url + '/',
		type: 'GET', 
		data: {'modal':modal, 'partnumber':partnumber},
		dataType: 'json' 
	}) 
	.done(function(response)
	{
		sweetAlertAfterResponse(response);
	}) 
	.fail(function()
	{
		Swal.fire({
			icon: 'error',
			title: 'Oops...',
			text: 'Something went wrong with ajax !',
		});
	});
	$('#pnModal').modal('hide'); 
	$('#pnModel, #pnPn ').val(''); 
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


function newPackage() 
{
	$('#pkg_id').val('new'); 
	$('#asinModalLabel').text('New Package');
	$('#asinModal').modal('show');
}


function addNewPackage(event , url)
{
	event.preventDefault(); 

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});  

	$.ajax({ 
		url: url, 
		type: 'POST', 
		data: $('#newPackageForm').serialize(),
		dataType: 'json'
	}).done(function(response) { 

		if( response.validation == 'errors' ) 
		{  
			$.each( response.messages , function( key, value ) 
			{
				$( "input[name='"+key+"']" ).siblings('.text-danger').text( value[0] );
				$( "select[name='"+key+"']" ).siblings('.text-danger').text( value[0] ); 
			});
		} 
		else
		{
			sweetAlertAfterResponse(response); 
			if( response.status == 'success')
			{
				location.reload();
			}
		}
		
	}).fail(function() { 
		
		Swal.fire({
			icon: 'error',
			title: 'Oops...',
			text: 'Something went wrong with ajax !',
		});
		
	});
} 


function checkInPackage(url)
{ 
	var tn = $("#checkNumber").val();
	var un = $("#userName").val();

	if (tn.length>3) { 



	} 
	else 
	{
		Swal.fire({
			icon: 'error',
			title: 'Oops...',
			text: 'Invalid Tracking Number !!',
		});
	}

}


function sweetAlertAfterResponse(response)
{ 
	Swal.fire({
		icon: response.status,
		title: response.title,
		text: response.message, 
		showConfirmButton: false
	});
}



