var prefix = 'admin';
var _token = $('meta[name="csrf-token"]').attr('content');
var cbWidth=0;
var reqFld = [];
var items = [];
var edit = false;
var isBlacklisted = false;
var damageScore=0;
var forRefurb = false;
var modelSet=false;
var lastload=false;
var hddc = "";
var ramc = "";
var cpuname = "N/A";
var cpuspeed = 0;
var asinid = 0;
var asinmodels = [];
var device="";
var asins=[];
var adata;
var assetNumber;
var forceWS = false;
var old_coa = "";
var new_coa = "";
var win8 = 0;

// Array of inputs for package modal for per-populating values of package
var packageModalInputs = ["order_date", "expected_arrival", "description", "qty", "value", "req_name", "tracking_number", "ref_number", "carrier", "freight_ground", "recipient", "received", "worker_id", "location" ];


const swalWithBootstrapButtons = Swal.mixin({
	customClass: {
		confirmButton: 'btn btn-success',
		cancelButton: 'btn btn-danger'
	},
	buttonsStyling: false
})

function showLoader()
{
	$('body').addClass('loader-opacity')
	$('.loader').show();
}

function hideLoader()
{
	$('body').removeClass('loader-opacity')
	$('.loader').hide();
}

function showSweetAlertMessage(type, message, icon)
{
	swalWithBootstrapButtons.fire(
		type,
		message,
		icon
	) 
}

function setHeader()
{
	var lh = $("#page-logo").height();
	var hh = $("#page-head").height();
	var fh = $("#main-form").height();
	var bh = $("#page-bottom").height();
	var wh = $(window).height();
	var ch = Math.round((wh-fh-lh-hh-bh) / 6);
	if (ch>0)
	{
		$("#page-logo").css('margin-bottom',ch+'px');
		$("#page-logo").css('margin-top',ch+'px');
		$("#page-head").css('margin-bottom',ch+'px');
	}
	else
	{
		$("#page-logo").css('margin-bottom','5px');
		$("#page-logo").css('margin-top','5px');
		$("#page-head").css('margin-bottom','5px');
	}
}

function alignCB()
{
	$('.cb-cnt').each(function (index, value)
	{
	  	var w = $(this).width();
	  	if (w > cbWidth) cbWidth = w; 
	});
	$('.cb-cnt').width(cbWidth);
}

function frmSubmit()
{
	$("#main-form").submit();
}

function checkModelTrigger()
{
	setTimeout(checkModel,500);
}

function setHDD(fId)
{
	var cVal = $('#'+fId).val();
	var cTab = $('input[name="radio_2"]:checked').val();
	var cParent = $('#'+fId).closest(".formitem");
	var subFields = $('*[data-subfield="Updated HDD"]');
	//console.log(subFields.length);
	var nParent = $("#hdd-controls");
	if (!nParent.length)
	{
		nParent = $( "<div id='hdd-controls' class='formitem'></div>" ).insertAfter( cParent );
		for (i = 1; i <= 4; i++)
		{
			var cRow = $( "<div id='hddrow" + i + "' class='hdddynrow' style='display:block'></div>" ).appendTo(nParent);
			var rowFields = $('*[data-hddrow="' + i +'"]');
			rowFields.closest(".form-group").appendTo(cRow);
			if (i<4) $( "<span style='font-size:1.5em; margin-left:10px; cursor:pointer'><a onclick='showRow(\"hddrow\","+(i+1)+")'><img src='img/plus.png' alt='add'></a></span>" ).appendTo(rowFields.closest(".form-group").last());
			if (i==1)
			{
				$("label", rowFields.closest(".form-group")).append("&nbsp;<span class='req'>*</span>");
				var fWidth = rowFields.closest(".form-group").first().width();
				var sWidth = rowFields.closest(".form-group").eq(1).width();
				console.log(fWidth); 
			}
			else
			{
				$("label", rowFields.closest(".form-group")).hide();
				rowFields.closest(".form-group").first().width(fWidth);
				rowFields.closest(".form-group").eq(1).width(sWidth);
			}	
			if (rowFields.first().val()=="") cRow.hide();
		}
		$("#hddrow1").show();
	}
	
	if (cVal == 'Removed' || (cTab=='Server' && (cVal == 'Updated' || cVal == 'Unchanged')))
	{
		nParent.show();
		var vFields = $('*[data-subfield="Updated HDD"]:visible');
		vFields.prop('required',true);
	}
	else
	{
		nParent.hide();
		subFields.prop('required',false);
	}
}

function setRAM(fId)
{
	var cVal = $('#'+fId).val();
	var cTab = $('input[name="radio_2"]:checked').val();
	var cParent = $('#'+fId).closest(".formitem");
	var subFields = $('*[data-subfield="Updated RAM"]');
	//console.log(subFields.length);
	var nParent = $("#ram-controls");
	if (!nParent.length)
	{
		nParent = $( "<div id='ram-controls' class='formitem'></div>" ).insertAfter( cParent );
		for (i = 1; i <= 4; i++)
		{
			var cRow = $( "<div id='ramrow" + i + "' class='ramdynrow' style='display:block'></div>" ).appendTo(nParent);
			var rowFields = $('*[data-ramrow="' + i +'"]');
			rowFields.closest(".form-group").appendTo(cRow);
			if (i<4) $( "<span style='font-size:1.5em; margin-left:10px; cursor:pointer'><a onclick='showRow(\"ramrow\","+(i+1)+")'><img src='img/plus.png' alt='add'></a></span>" ).appendTo(rowFields.closest(".form-group").last());
			if (i==1)
			{
				$("label", rowFields.closest(".form-group")).append("&nbsp;<span class='req'>*</span>");
				var fWidth = rowFields.closest(".form-group").first().width();
				var sWidth = rowFields.closest(".form-group").eq(1).width();
				console.log(fWidth); 
			}
			else
			{
				$("label", rowFields.closest(".form-group")).hide();
				rowFields.closest(".form-group").first().width(fWidth);
				rowFields.closest(".form-group").eq(1).width(sWidth);
			}	
			if (rowFields.first().val()=="") cRow.hide();
		}
		//$(".ramdynrow").hide();
		$("#ramrow1").show();
	}
	
	if (cVal == 'Removed' || (cTab=='Server' && (cVal == 'Updated' || cVal == 'Unchanged')))
	{
		nParent.show();
		var vFields = $('*[data-subfield="Updated RAM"]:visible');
		vFields.prop('required',true);
	}
	else
	{
		nParent.hide();
		subFields.prop('required',false);
	}
}

function showRow(rtype,rnum)
{
	var rowFields = $('*[data-' + rtype + '="' + rnum +'"]');
	$("#"+rtype + rnum).show();
	rowFields.prop('required',true); 
}

function detectSize(fId)
{
	fVal = $('#'+fId).val();
	sLabel = $('#'+fId+'_lbl');
	var sUnit = "TB";
	if(!sLabel.length)
	{
		sLabel = $( "<span style='font-size:1.5em; margin-left:5px' id='"+fId+"_lbl'>&nbsp;</span>" ).insertAfter( '#'+fId );
	}
	if(fVal > 50)  sUnit = "GB";
	sLabel.text(sUnit);
}

function detectRamSize(fId)
{
	fVal = $('#'+fId).val();
	sLabel = $('#'+fId+'_lbl');
	var sUnit = "GB";
	if(!sLabel.length)
	{
		sLabel = $( "<span style='font-size:1.5em; margin-left:5px' id='"+fId+"_lbl'>&nbsp;</span>" ).insertAfter( '#'+fId );
	}
	sLabel.text(sUnit);
}

function setAudit(rId)
{
	var cVal = $('#'+rId).val();
	var reasonFldId = $('#'+rId).data('ref');
	var cArr = rId.split("_");
	var prId = parseInt(cArr[1])+1;
	if (cVal == 'Yes')
	{
		var pId = '#radio_' + prId + '_1';
		$(pId).prop( "checked", true )
		$("#"+reasonFldId).closest(".formitem").hide();
		$("#"+reasonFldId).prop('required',false);
	}
	else
	{
		var pId = '#radio_' + prId + '_0';
		$(pId).prop( "checked", true )
		$("#"+reasonFldId).prop('required',true);
		$("#"+reasonFldId).closest(".formitem").show();
	}
}

function setRTS(rId)
{
	var cVal = $('#'+rId).val();
	var reasonFldId = $('#'+rId).data('ref');
	var cArr = rId.split("_");
	var prId = parseInt(cArr[1])-1;
	if (cVal == 'Yes')
	{
		var pId = '#radio_' + prId + '_1';
		$(pId).prop( "checked", true );
		$("#"+reasonFldId).closest(".formitem").show();
		$("#"+reasonFldId).prop('required',true);
	}
	else
	{
		var pId = '#radio_' + prId + '_0';
		$(pId).prop( "checked", true );
		$("#"+reasonFldId).prop('required',false);
		$("#"+reasonFldId).closest(".formitem").hide();
	}
}

function getUrlParameter(sParam)
{
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++)
    {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam)
        {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

function setAside(itmId)
{
	if ($("#"+itmId).val().length > 0)
	{
		showSweetAlertMessage(type = 'error', message = 'Please set the machine aside to be manually audited' , icon= 'error');
	}
}

function showModelFields()
{
	$(":input[data-fillmodel]").closest(".formitem").toggle();
	// $(':input[data-fillmodel="1"]').closest(".formitem").css("background-color","#F5F5DC");
	$(':input[data-fillmodel="1"]').closest(".formitem");
	$('input[data-modelname="1"]').closest(".formitem").show();
	return false;
}

function setVideo(hasCrd)
{
	if (hasCrd == "Yes")
	{ 
		$(':input[data-video="1"]').closest(".formitem").show();
	}
	else
	{
		$(':input[data-video="1"]').closest(".formitem").hide();
	}
}

function getModels(fId)
{
	var inp=$("#"+fId);
	var out=$("#uhint");
	var cTab = $('input[name="radio_2"]:checked').val();
	var cTech = encodeURIComponent($('input[data-technology="1"]:checked').val());
	var data=encodeURIComponent(inp.val());
	var position = inp.position();
	if (data.length>1)
	{
		var h = inp.height();
		var x=position.left;
		var y=position.top+h+10;
		out.css(({left:x,top:y}));
		$.get("/"+prefix+"/getmodels?tgt="+fId+"&part="+data+"&tab="+cTab+"&tech="+cTech+"&t="+Math.random(), function(data)
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

function checkModel()
{
	if (!modelSet)
	{
		var modelName = encodeURIComponent($('input[data-modelname="1"]').val());
		var cTab  = $('input[name="radio_2"]:checked').val();
		var cTech = encodeURIComponent($('input[data-technology="1"]:checked').val());
		if (modelName.length>1)
		{
			var q = confirm("This model name is not in the list. Do you want to create new template?");
			if (q == true)
			{
				modelSet = true; 
				$('#main-form').append('<input type="hidden" name="addModel" value="1"/>');
				// $(':input[data-fillmodel="1"]').closest(".formitem").css("background-color","#F4A460");
				$(':input[data-fillmodel="1"]').closest(".formitem");
				// $('input[data-modelname="1"]').prop("readonly",true).closest(".formitem").css("background-color","none");
				$('input[data-modelname="1"]').prop("readonly",true).closest(".formitem");
				showSweetAlertMessage(type = 'error', message = 'Template created. Please carefully fill highlighted fields and submit the form' , icon= 'error');
			}
		}
	}
} 

function showHint(fId)
{
	var inp=$("#"+fId);
	var out=$("#uhint");
	var data=encodeURIComponent(inp.val());
	var position = inp.position();
	if (edit && data.length>2)
	{
		var h = inp.height();
		var x=position.left;
		var y=position.top+h+10;
		out.css(({left:x,top:y}));
		$.get("/"+prefix+"/getfiles?tgt="+fId+"&part="+data+"&t="+Math.random(), function(data)
		{
			if(data)
			{
				$("#hints").html(data);
				out.show();
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

function CheckRequired()
{
    $("input[data-disable=1]").prop("disabled",true);
    reqFld = [];
    var formValid = true;
    $(':input').each(function()
    { 
		var inpVal = "";
		var inpValNew = "";
		var inpType = $(this).attr('type');
		var inpName = $(this).attr('name');
		if($(this).prop('required') && inpName !== undefined)
		{
			if (reqFld.indexOf(inpName) < 0)
			{
				reqFld.push(inpName);
				if (inpType=="radio" || inpType=="checkbox")
				{
					inpVal = $('input[name="' + inpName + '"]:checked').val();
				}
				else
				{
					inpVal = $(this).val();
				}
				var newName = inpName.replace("[]", "") + '_new';
				inpValNew = $('input[name="' + newName + '"]').val();
				if (inpVal == null) inpVal = "";
				if (inpValNew == null) inpValNew = "";
				if (inpVal == "Other:")
				{
					inpVal = ""
					$('input[name=' + newName + ']').prop('required',true);
				}
				else
				{
					$('input[name=' + newName + ']').prop('required',false);
				}
				if (inpVal === "" && inpValNew === "") formValid = false;
			}
		} 
	});
    if (formValid)
    {
        frmPreview();
		return true;
    }
    else
    {
		$(':input[required]').closest(".formitem").show();
		$("#submitBtn").click();
        return false;
	}
}

function hidePreview()
{
	$("#preview").hide();
	$("#main-form").show();
	$("#page-logo").show();
    $("#page-head").show();
}

function getAssetData(fId)
{
	var asin = $('#asset_num').val();
	if (asin.length >= 10)
	{
		$.get("/"+prefix+"/getasin?asin=" + asin + "&t=" + Math.random(), function (data) {
			if (data == '0')
			{
				showSweetAlertMessage(type = 'error', message = 'ASIN not found. Please check and try again' , icon= 'error');
			}
			else
			{
				location.href = 'index.php?page=parts&model=' + data;
			}
		});
	}
}

$(document).ready(function()
{
	if($('.it-amg-redirect').length > 0)
	{
		setTimeout(
			function(){
				location.href =  $('.it-amg-redirect').attr('href');
			}
			,3000);
	}
	$('#asset').focus();
	$(document).keydown(function(event)
	{
		if(event.keyCode == 13) 
		{
			if($("#asset").is(":focus"))
			{
				event.preventDefault();
				if ($("#asset").val().length > 3) $('#main-form').submit();
				return true;
			}
			else
			{
				return true;
			}
		}
	});
}); 

$(document).ready(function()
{
	$('#supplies, #asins, #users_table').DataTable();
	
	$('#shipment').DataTable
	({
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});
	
	$('#shipment-asin, #sessions, #sessions-asins, #sessions-asins-part, #package-table').DataTable
	({
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});

	$('#lookup').DataTable
	({
		"searching": false,
		"bPaginate": false, 
	}); 

	if($('#supplie').length > 0)
	{
		$( "#supplie" ).validate
		({
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
		$(".email_list").on('change', function(argument)
		{
			earr=[];
			$('.email_list').each(function()
			{
				if($(this).prop('checked')) earr.push($(this).val());
			});
			$('input[name=email]').val(earr.join(','));
		});
	}
})

function reorderItem(iid, dqty, url) 
{
	var qty = prompt('Please enter reorder quantity (default is ' + dqty +')', dqty);
	if (qty == null || qty == '') 
	{
		return false;
	} 
	else 
	{
		$.ajax
		({
			url: url,
			type: 'GET',
			data: {supplieid: iid, quantity: qty},
			dataType: 'json'
		})
		.done(function(response)
		{
			swalWithBootstrapButtons.fire
			( 
				'Deleted!',
				response.message ,
				response.status
			) 
		}) 
		.fail(function()
		{
			Swal.fire
			({
				icon: 'error',
				title: 'Oops...',
				text: 'Something went wrong with ajax !',
			})
		});
	}
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

function deptFilter()
{
	$('.invrow').hide();
	$('.dcb').each(function()
	{
		if($(this).prop('checked')) 
		{
			$(".invrow[data-dept='" + $(this).val() +"']").show();
		}
	});
}

function del_confirm(id,url,text)
{
	swalWithBootstrapButtons.fire
	({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Yes, delete it!',
		cancelButtonText: 'No, cancel!',
		reverseButtons: true
	})
	.then((result) => { 
		
		if (result.value) 
		{  
			$.ajax({
				url: url+'/'+id,
				type: 'GET',
				dataType: 'json'
			})
			.done(function(response)
			{ 
				console.log(response)
				swalWithBootstrapButtons.fire( 
					'Deleted!',
					response.message ,
					response.status
				) 
			})
			.fail(function()
			{
				Swal.fire({
					  icon: 'error',
					  title: 'Oops...',
					  text: 'Something went wrong with ajax !',
				})

			});
			setTimeout(function(){location.reload();}, 2000);
		}
		else if (result.dismiss === Swal.DismissReason.cancel ) 
		{
			swalWithBootstrapButtons.fire(
				'Cancelled',
				'Your record is safe :)',
				'error'
			)
		} 
		
	})
}