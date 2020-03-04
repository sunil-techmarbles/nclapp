jQuery(document).on('click', '.addPartNumber', function(e) {
	e.preventDefault();  
	
	Swal.fire({ 
		title: 'Add Part Number',
		html: '<input type="text" class="swal2-input" id="modal" placeholder="Enter Modal" name="modal">' +
		'<input type="text" class="swal2-input" id="partnumber" placeholder="Enter Part Number" name="partnumber">',
		confirmButtonText: 'Save',
		showCancelButton: true, 
		preConfirm: () => {  
			let modal = Swal.getPopup().querySelector('#modal').value;
			let partnumber = Swal.getPopup().querySelector('#partnumber').value;
			
			if (modal === '' || partnumber === '') {
				Swal.showValidationMessage('Modal or Part Number is empty')
			} 
		} 
	})
	.then(( result ) => {      

		if(result.value) 
		{
			
			let modal = Swal.getPopup().querySelector('#modal').value;
			let partnumber = Swal.getPopup().querySelector('#partnumber').value;

			$.ajax({
				url: 'addpartnumber/',
				type: 'GET', 
				data: {'modal':modal, 'partnumber':partnumber},
				dataType: 'json' 
			}) 
			.done(function(response)
			{  

				if( response.status == 'success')
				{ 

					Swal.fire({
						icon: 'success',
						title: 'Added',
						text: 'Part Number has been added successfully',
					})

				}
				else 
				{ 

					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Something went wrong, Please try again!',
					})

				}

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
	});

}); 