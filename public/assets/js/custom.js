// Remove Validation from AddUpdate Package in Inbound section  
$(document).on('change ,keyup , load, blur' , "#newPackageForm input, #newPackageForm select" , function()
{ 
	removeErrorMessage(this);
});

// This id for Inbound Section for package AddUpdate Form submit
$(document).ready(function () 
{
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

// Ajax for WipeReport section To get the report of entered Lot number. 
$(window).keydown(function (event) 
{
	if (event.keyCode == 13)
	{
		if ($("#lotNum").is(":focus"))
		{
			var lotNum = $("#lotNum").val();
			if( lotNum == '')
			{
				sweetAlertAfterResponse(status = 'error' , title = 'lot number empty', message = 'Please Enter a lot number to search files' , showbutton = true );
				return false;
			}
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': _token
				}
			});
			$.ajax({
				url: 'getwipereportfiles',
				type: 'POST', 
				data: {'lotNum':lotNum},
				dataType: 'json'
			})
			.done(function(response)
			{
				$("#wipe-report-result").html("");
				$("#downloadFiles").remove();
				if( response.status == true)
				{
					$("#selectAllFiles").prop("checked",false);
					var html = '<table><table id="wipeReportresultsFileTable" class="table"><thead>';
						html += '<tr><th><input type="checkbox" id="selectAllFiles"/>Select All</th>';
						html += '<th>File Name</th></tr></thead><tbody>';
					$.each(response.files, function (key, value) 
					{
					    html += '<tr>';
						html += '<td><input value="' + value.path + '" name="wipefiles[]" type="checkbox" class="selectSingleFile"/></td>';
						html += '<td><a target="_blank" download href="'+value.url+'">' + key + '</a></td>';
						html += '</tr>'; 
					});
						html += '</tbody></table>';
					$("#wipe-report-result").append(html);
					$( "#wipeReportresultsFileTable" ).after( '<button type="submit" class="btn btn-primary" id="downloadFiles">Download</button>' );
				}
				else if(response.status == false)
				{
					sweetAlertAfterResponse(status = 'error' , title = 'No files found', message = 'No files found for this Lot Number' , showbutton = true );
				}
			})
			.fail(function()
			{
				sweetAlertAfterResponse(status = 'error' , title = 'Oops...', message = 'Something went wrong with ajax !' , showbutton = true );
			});
		}
	}
});

// wipe report section for bulk select or unselect files to download js 
$(document).on('click', '#selectAllFiles', function ()
{
	if ($(this).is(":checked"))
	{
		$(".selectSingleFile").prop("checked", true);
	}
	else
	{
		$(".selectSingleFile").prop("checked", false);
	}
});

// for downloading Wipe Reports for a lot number in wipereport section 
$(document).on('submit', '#search-wipe-form', function (e)
{
    if ($('.selectSingleFile:checked').length < 1)
    {
        sweetAlertAfterResponse(status = 'error' , title = 'Select files', message = 'Please select some files to download !' , showbutton = true );
 		e.preventDefault();
    }
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
	// for removing validation errors 
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
	// packageModalInputs this is defined in main js file . 
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

// remove error messages
function removeErrorMessage(el)
{
	$(el).siblings('.text-danger').text('');
}
