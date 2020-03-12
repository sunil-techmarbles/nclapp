// Array of inputs for package modal for per-populating values of package
var packageModalInputs = ["order_date", "expected_arrival", "description", "qty", "value", "req_name", "tracking_number", "ref_number", "carrier", "freight_ground", "recipient", "received", "worker_id", "location" ];

// validation and form submit of AddUpdate Package 
$(document).on('change ,keyup , load, blur' , "#newPackageForm input, #newPackageForm select" , function(){ 
 	removeErrorMessage(this);
});

$(document).ready(function () {

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
			},
			value:{
				number: true
			} 
		},
		submitHandler: function() {  
			addUpdatePackage(event, 'addupdatepackage');
		}
	}); 
});


// For saving part number with modal in Autit section 
function savePN(url)
{
	var modal = $('#pnModel').val();
	var partnumber = $('#pnPn').val();
	if(!modal || !partnumber) 
	{
		sweetAlertAfterResponse( status = 'error' , title = 'Oops...', message = 'Please enter Model and Part Number !' , showbutton = true );
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
		sweetAlertAfterResponse(response.status, response.title, response.message , showbutton = true );
	})
	.fail(function()
	{
		sweetAlertAfterResponse(status = 'error' , title = 'Oops...', message = 'Something went wrong with ajax !' , showbutton = true );
	});
	$('#pnModal').modal('hide');
	$('#pnModel, #pnPn ').val('');
}


// filter modal list in partlookup section  
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


// Add new Package in InBound Section 
function newPackage()
{
	$("#newPackageForm input, #newPackageForm select").siblings('.text-danger').text('');
	$('#packageModal').find( "input" ).val('');
	$('#newPackageForm').find( "select" ).val('');
	$('#packageModalLabel').text('New Package'); 
	$('#pkg_id').val('new'); 
	$('#packageModalSubmit').text('Add Package');
	$('#packageModal').modal('show'); 
}


// Edit Existing Package in InBound Section 
function editPackage(packageTr, packageId)
{ 
	$("#newPackageForm input, #newPackageForm select").siblings('.text-danger').text('');
	$('#packageModalLabel').text('Edit Package'); 
	$('#pkg_id').val(packageId); 
	$('#packageModalSubmit').text('Update Package');
	$(packageTr).find('td').each (function(key, value) 
	{
  		var inputValue = $(this).text();
		var inputID = packageModalInputs[key]; 
  		$('#f_'+inputID).val(inputValue);
	});
	$('#packageModal').modal('show');
}


// add Update Package form submit 
function addUpdatePackage(event , url)
{
	event.preventDefault(); 
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': _token
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
			sweetAlertAfterResponse(response.status, response.title, response.message, showbutton = false ); 
			if( response.status == 'success')
			{
				location.reload();
			}
 		}
	}).fail(function() {
		sweetAlertAfterResponse(status = 'error' , title = 'Oops...', message = 'Something went wrong with ajax !', showbutton = true);
	});
}


// check in package form submit in In Bound Section 
function checkInPackage(url)
{
	var tn = $("#trackingNumber").val();
	var un = $("#userName").val();
	if (tn.length>3) 
	{
		$.get( url +"/?tn="+tn+"&un="+un, function(response)
		{
			sweetAlertAfterResponse(response.status, response.title, response.message, showbutton = true);
			$('#checkInModal').modal('hide');
			$('#trackingNumber').val('');
		});
	} 
	else
	{
		sweetAlertAfterResponse(status = 'error' , title = 'Oops...', message = 'Invalid Tracking Number !', showbutton = true);
	}
}

// sweetalert after response
function sweetAlertAfterResponse(status, title, message, showbutton)
{
	Swal.fire({
		icon: status,
		title: title,
		text: message, 
		showConfirmButton: showbutton
	});
}

// remove reeor messages
function removeErrorMessage(el)
{
	$(el).siblings('.text-danger').text('');
}
