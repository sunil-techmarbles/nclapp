$(document).ready(function(){
	$('.users_table').DataTable();
}); 

jQuery( document ).on( 'click' , '.deleteUser' , function( e ){
	e.preventDefault();
	
	swal("Are you sure You want to delete this user.")
	.then((value) => {


			var user_id = jQuery(this).data('user_id');

			console.log( ); 

	   			// swal('ok user deleted now');
	});
 
});