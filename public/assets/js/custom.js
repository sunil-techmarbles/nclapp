jQuery(document).ready(function(){
	jQuery('.users_table').DataTable();
});   

function del_confirm( id, url, text ) {   
	
	swal({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if ( willDelete ) { 
		
			$.ajax({
				url: url + '/' + id,
				type: 'GET',
				dataType: 'json'
			}) 
			.done(function( response ) {   
 
				console.log( response ); 
 
			 	// swal('Deleted!', response.message, response.status);
		 		// readProducts();
	     	})
			.fail(function(){
			 	swal('Oops...', 'Something went wrong with ajax !', 'error');
			});

			swal("Poof! Your record deleted!", {
			icon: "success",
		});
		} else {
			swal("Your record is safe!");
		}
	});
}