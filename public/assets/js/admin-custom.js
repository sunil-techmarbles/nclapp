$(document).ready(function(){
	$('#supplies').DataTable();
	$('#asins').DataTable();

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
				price: {

				}
			}
		});		
	}


	if($('.alert').length > 0)
	{
		setTimeout(function(){ $('.alert').hide()}, 3000);
	}
})