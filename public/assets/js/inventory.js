function getModels(fId)
{
	var inp=$("#"+fId);
	var out=$("#uhint");
	var data=encodeURIComponent(inp.val());
	var position = inp.position();
	if (data.length>1)
	{
		out.insertAfter(inp);
		$.get("/"+prefix+"/getmodels?tgt="+fId+"&part="+data+"&tab=&tech=&t="+Math.random(), function(data)
		{
			if(data)
			{
				$("#hints").html(data);
				modelSet = false;
				out.show()
			}
			else
			{
				out.hide();
			}
		});
	}
	else
	{
		out.hide();
	}
}

function getModelData(mid,asin)
{
	$("#uhint").hide();
	$.get(""+prefix+"/setmodelid?mid="+mid+"&asin="+asin+"&t="+Math.random(), function(data)
	{
		if(data=='OK')
		{
			location.reload();
		}
		else
		{
			alert(data);
		}
	});
}

function setPrice(asin)
{
	$('#set_price').val(asin);
	$('#priceModal').modal('show');
}

function toggleAll(f)
{
	var c = $('#'+f+'_all').prop('checked');
	$('.'+f).prop('checked',c);
	tblFilter();
}

function tblFilter()
{
	$('.nlrow').hide();
	$('.drow').hide();
	var cpus = [];
	var ffs = [];
	var mdls = [];
	var imgs = [];
	var prcs = [];
	var syncs = [];
	$('.img').each(function(){
		if($(this).prop('checked'))
		{
			imgs.push($(this).val());
		}
	});
	$('.sync').each(function(){
		if($(this).prop('checked'))
		{
			syncs.push($(this).val());
		}
	});
	$('.price').each(function(){
		if($(this).prop('checked'))
		{
			prcs.push($(this).val());
		}
	});
	$('.mdl').each(function(){
		if($(this).prop('checked'))
		{
			mdls.push($(this).val());
		}
	});
	$('.ff').each(function(){
		if($(this).prop('checked'))
		{
			ffs.push($(this).val());
		}
	});
	$('.cpu').each(function(){
		if($(this).prop('checked'))
		{
			cpus.push($(this).val());
		}
	});
	$('.nlrow').each(function()
	{
		var m = $(this).data('mdl');
		var c = $(this).data('cpu');
		var f = $(this).data('ff');
		var p = $(this).data('price');
		var i = $(this).data('img');
		var s = $(this).data('synced');
		if(cpus.indexOf(c)>=0 && ffs.indexOf(f)>=0 && mdls.indexOf(m)>=0 && prcs.indexOf(p)>=0 && imgs.indexOf(i)>=0 && syncs.indexOf(s)>=0)
		{
			$(this).show();
		}
	});
}

$(document).ready(function ()
{
	$("#sync-all-to-shopify").click(function () {
		var ids = [];
		$.each($("input[name='sync-all-ids[]']:checked"), function () {
			ids.push($(this).val());
		});
		if(ids.length > 0)
		{
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "/"+prefix+"/syncalltoshopify",
				data: {
	                ids: ids,// < note use of 'this' here
	                _token: _token, 
	            },
	            beforeSend: function () {
	            	$("#sync-all-to-shopify").attr("disabled", "disabled");
	            },
	            complete: function () {
	            	$("#sync-all-to-shopify").removeAttr("disabled");
	            },
	            success: function (result) {
	            	console.log(result);
	            	// alert(result);
	            	// location.reload(true);
	            }
	        });
		}
		else
		{
			showSweetAlertMessage(type = 'error', message = 'Please select atleast one data' , icon= 'error');
		}
	});

	$(".check-all-ids").click(function () {
		$("input[type=checkbox]").prop('checked', $(this).prop('checked'));
	});

	$(".sync-to-shopify").click(function (e) {
		e.preventDefault();
		var id = $(this).data('id');
		var that = $(this);
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "shopify_new_list/shopify_product.php",
			data: {
                id: id, // < note use of 'this' here
            },
            beforeSend: function () {
            	that.attr("disabled", "disabled");
            },
            complete: function () {
            	that.removeAttr("disabled");
            },
            success: function (result) {
            	alert(result);
            	location.reload(true);
            }
        });
	});
	$(".update-price-to-shopify").click(function (e) {
		e.preventDefault();
		var id = $(this).data('id');
		var that = $(this);
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "shopify_new_list/shopify_price_update.php",
			data: {
                id: id, // < note use of 'this' here
            },
            beforeSend: function () {
            	that.attr("disabled", "disabled");
            },
            complete: function () {
            	that.removeAttr("disabled");
            },
            success: function (result) {
            	alert(result);
            	location.reload(true);
            }
        });
	});
	tblFilter();
});