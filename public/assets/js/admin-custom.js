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

function reorderItem(iid, dqty, url) {
	var qty = prompt('Please enter reorder quantity (default is ' + dqty +')', dqty);
	if (qty == null || qty == '') {
		return false;
	} else {
		$.ajax({
			url: url,
			type: 'GET',
			data: {supplieid: iid, quantity: qty},
			dataType: 'json'
		})
		.done(function(response){
			console.log(response)
		 	swal('Deleted!', response.message, response.status);
     	})
		.fail(function(){
		 	swal('Oops...', 'Something went wrong with ajax !', 'error');
		});
	}
}

function getAssetData(fId)
{
	var asin=$('#asset_num').val();
	if (asin.length >= 10) {
		$.get("ajax.php?action=getASIN&asin="+asin+"&t="+Math.random(), function(data) {
			if(data=='0') {
				alert('ASIN not found. Please check and try again');
			} else {
				location.href = 'index.php?page=parts&model='+data;
			}
		});
	}
}

function filterModels(str){
	if (str.length > 2) {
		$('.mdlrow').hide();
		$("tr[data-model*='" + str.toLowerCase() +"']" ).show();
	} else {
		$('.mdlrow').show();
	}
}

function deptFilter() {
	$('.invrow').hide();
	$('.dcb').each(function(){
		if($(this).prop('checked')) {
			$(".invrow[data-dept='" + $(this).val() +"']").show();
		}
	});
}
		
function del_confirm(id, url, text) {
	swal({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) {
			$.ajax({
				url: url+'/'+id,
				type: 'GET',
				dataType: 'json'
			})
			.done(function(response){
				console.log(response)
			 	swal('Deleted!', response.message, response.status);
	     	})
			.fail(function(){
			 	swal('Oops...', 'Something went wrong with ajax !', 'error');
			});
			setTimeout(function(){location.reload();}, 2000);
		} else {
			swal("Your record is safe!");
		}
	}); 
}