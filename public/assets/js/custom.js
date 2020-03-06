function savePN(url)
{
	console.log( url );  

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
		Swal.fire({
			icon: response.status,
			title: response.title,
			text: response.message,
		})
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
	// for (var i in sv) {
	// 	$('#f_'+i).val('');
	// }
	$('#asinModal').modal('show');
} 


function addNewPackage(event, form, url)
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
		data: $(form).serialize(),
		dataType: 'json'
	}).done(function(response) {
	
		console.log( response ); 
		
	}).fail(function() {

		
	});
}

$(document).ready(function () {

	$('.datepicker').datepicker({format: "yyyy-mm-dd"});

	// $(".daterange").daterangepicker({
	// 	    opens: 'left',
	// 	    locale: {
	//             format: 'YYYY-MM-DD'
	//         }
	// }); 

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
			}
		}
	});
});



