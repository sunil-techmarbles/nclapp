function getModels(fId)
{
	console.log("i am here");
	var inp = $("#"+fId);
	var out = $("#uhint");
	var data = encodeURIComponent(inp.val());
	var position = inp.position();
	if (data.length>1)
	{
		out.insertAfter(inp);
		$.get("/"+prefix+"/getmodels?tgt="+fId+"&part="+data+"&tab=&tech=&t="+Math.random(), function(data)
		{
			console.log(data);

			// if(data)
			// {
			// 	$("#hints").html(data);
			// 	modelSet = false;
			// 	out.show()
			// }
			// else
			// {
			// 	out.hide();
			// }
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
			showSweetAlertMessage('error', data, 'error');
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
	var newRunList = ($("input[name='reunlistsyns']").length > 0) ? true : false;
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
	                newRunList: newRunList
	            },
	            beforeSend: function () {
	            	showLoader();
	            	$("#sync-all-to-shopify").attr("disabled", "disabled");
	            },
	            complete: function () {
	            	hideLoader();
	            	$("#sync-all-to-shopify").removeAttr("disabled");
	            },
	            success: function (result) {
	            	hideLoader();
	            	let icon = (result.status) ? 'success' : 'error';
	            	showSweetAlertMessage(type = icon, message = result['message'] , icon = icon);
	            	if(result.status)
	            	{
	            		location.reload(true);
	            	}
	            },
	            error: function(xhr, status, error){
	            	hideLoader();
					showSweetAlertMessage(type = 'error', message = 'something went wrong with your request' , icon = 'error');
				}
	        });
		}
		else
		{
			showSweetAlertMessage(type = 'error', message = 'Please select atleast one data' , icon= 'error');
		}
	});

	$(".check-all-ids").click(function ()
	{
		$("input[type=checkbox]").prop('checked', $(this).prop('checked'));
	});

	$(".sync-to-shopify").click(function (e) {
		e.preventDefault();
		var ids = [];
		var that = $(this);
		ids.push(that.data('id'));
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/"+prefix+"/updatetoshopify",
			data: {
                ids: ids, // < note use of 'this' here
                _token: _token,
                newRunList:newRunList,
            },
            beforeSend: function () {
            	showLoader();
            	that.attr("disabled", "disabled");
            },
            complete: function () {
            	hideLoader();
            	that.removeAttr("disabled");
            },
            success: function (result){
            	hideLoader();
            	let icon = (result.status) ? 'success' : 'error';
            	showSweetAlertMessage(type = icon, message = result['message'] , icon = icon);
            	if(result.status)
            	{
            		location.reload(true);
            	}
            },
            error: function(xhr, status, error){
            	hideLoader();
				showSweetAlertMessage(type = 'error', message = 'something went wrong with your request' , icon = 'error');
			}
        });
	});
	$(".update-price-to-shopify").click(function (e) {
		e.preventDefault();
		var id = $(this).data('id');
		var that = $(this);
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/"+prefix+"/updatepricetoshopify",
			data: {
                id: id, // < note use of 'this' here
                _token: _token,
                newRunList:newRunList,
            },
            beforeSend: function () {
            	showLoader();
            	that.attr("disabled", "disabled");
            },
            complete: function () {
            	hideLoader();
            	that.removeAttr("disabled");
            },
            success: function (result) {
            	hideLoader();
            	let icon = (result.status) ? 'success' : 'error';
            	showSweetAlertMessage(type = icon, message = result['message'] , icon = icon);
            	if(result.status)
            	{
            		location.reload(true);
            	}
            },
            error: function(xhr, status, error){
            	hideLoader();
				showSweetAlertMessage(type = 'error', message = 'something went wrong with your request' , icon = 'error');
			}
        });
	});
	tblFilter();
});