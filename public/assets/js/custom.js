function savePN()
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
			url: 'addpartnumber/',
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

