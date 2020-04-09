$(window).on('resize', function(){
	setHeader();
});

function editCapacity(cType)
{
	$("#detailModal").modal("show");
	$(".capedit").val('');
	$("#capatype").val(cType);
	var str = $('input[data-'+cType+'capacity="1"]').val();
	if (str!="")
	{
		str = str.split("_").join("");
		if (cType=='ram')
		{
			var astr = str.split(":");
			if (astr[1]!==undefined) str = astr[1];
		}
		var dstr = str.split(";");
		for (var i in dstr)
		{
			var k = parseInt(i)+1;
			str = dstr[i];
			var astr = str.split("x");
			if(astr.length==2)
			{
				$("#capacity"+k).val(astr[0]);
				$("#qty"+k).val(astr[1]);
			}
		}
	}
}

function saveDetails()
{
	var cType = $("#capatype").val();
	var cstr="";
	var tcap=0;
	for(var i=1;i<4;i++)
	{
		var cpc = $("#capacity"+i).val();
		var qty = $("#qty"+i).val();
		if (cpc !="" && qty !="" && qty !="0")
		{
			if (cstr!="") cstr += ";";
			cstr += cpc +"_x_"+qty;
			if (cType == 'ram')
			{
				var mp = 1;
				if (cpc.indexOf("M")>-1) mp = 1024;
				cpc = cpc.split("MB").join("");
				cpc = cpc.split("GB").join("");
				cpc = cpc.split(" ").join("");
				tcap = tcap + (parseInt(cpc) / mp)*parseInt(qty);
			}
		}
	}
	if (cstr != "")
	{
		if (cType == 'ram')
		{
			cstr = tcap+"GB:_"+cstr;
		}
		$('input[data-'+cType+'capacity="1"]').val(cstr);
		$('input[data-'+cType+'capacity="1"]').removeAttr("data-disable");
	}	
	$("#detailModal").modal("hide");
}

$( document ).ready(function()
{
	if (getUrlParameter("edit") == 1) edit=true;
	setHeader();
	$(window).keydown(function(event)
	{
		if(event.keyCode == 13 && $('#asset1').length != 1)
		{
			event.preventDefault();
			return false;
		}
	});
	$(':input[data-cb="1"]').addClass("combobox");
	$('.combobox').combobox({appendId:"_cb"});
	$('.quantity-right-plus').click(function(e)
	{  
		e.preventDefault();
		var rownum = $(this).data("row");
		var quantity = $('#qty' + rownum).val();
		if (quantity === "") quantity=0;
		$('#qty' + rownum).val(parseInt(quantity) + 1);
	});

	$('.quantity-left-minus').click(function(e)
	{
		e.preventDefault();
		var rownum = $(this).data("row");
		var quantity = $('#qty' + rownum).val();
		if (quantity === "") quantity=0;
		if (quantity>0) $('#qty' + rownum).val(parseInt(quantity) -1);
	});
});

function calcGrade(setVal)
{
	var grades_array = [];
	$('.calculate_grade').each(function (key, value)
	{
		var is_checked = $(this).is(':checked');
		if(is_checked)
		{
			grades_array.push($(this).attr('data-grade'));
		}
	});
	assign_grades(grades_array);
}

function assign_grades(grades_array)
{
	var A = grades_array.indexOf("A");
	var B = grades_array.indexOf("B");
	var C = grades_array.indexOf("C");
	var result = '';
	if(C >= 0)
	{ 
		result = 'C';
	}
	else if(B >= 0)
	{
		result = 'B';
	}
	else if (A >= 0)
	{ 
		result = 'A';
	}
	else if(A == -1 && B == -1 && C == -1)
	{ 
		result = 'A';
	}
	$('input[type="radio"]').each(function (key, value) {
		if($(this).val() == result)
		{
			$($(this)).prop("checked", true);
		}
	});
}

function showTab( tabId )
{
	$.get("/"+prefix+"/gettab?tab="+tabId+"&t="+Math.random(), function (data)
	{
		$("#var_tab").html(data);
		$("#reviewBtn").show();
		setHeader();
		$("body").css('background-image', 'none');
		$('#page-bottom').removeClass("bottom-url");
		alignCB();
		$(":input[data-display]").closest(".formitem").hide();
		$(':input[data-cb="1"]').addClass("combobox");
		$('.combobox').combobox({appendId:"_cb"});
		$("input[data-hddcapacity='1']").val(hddc);
		$("input[data-ramcapacity='1']").val(ramc);
		$('input[data-ramcapacity="1"]').parent().append('<span style="font-size:20px;cursor:pointer" onclick="editCapacity(\'ram\')" class="fa fa-edit"></span>');
		$('input[data-hddcapacity="1"]').parent().append('<span style="font-size:20px;cursor:pointer" onclick="editCapacity(\'hdd\')" class="fa fa-edit"></span>');
		if (items.length > 0)
		{
			fillData();
			if (lastload)
			{
				$(":input[data-fillmodel]").closest(".formitem").hide();
				$('input[data-modelname="1"]').prop("readonly",true).closest(".formitem").show();
				$('input[data-modelname="1"]').closest(".formitem").append('<div class="form-group"><label class="ttl">&nbsp;</label><br/><button type="button" class="btn btn-default" onclick="showModelFields()">Toggle common data</button></div>');
			}
		}
	});
}

function frmPreview()
{
	showLoader();
	$("input[data-disable=1]").prop("disabled",true);
	$.get("/"+prefix+"/getpreview?" + $("#main-form").serialize() + "&t=" + Math.random(), function (data) {
		hideLoader();
		$("#preview-content").html(data);
		$("#main-form").hide();
		$("#page-logo").hide();
		$("#page-head").hide();
		$("#preview").show();
		setTimeout(refurbNotification,1000);
	});
}

var selTab;
function checkTravelerId( tabId )
{
	showLoader();
	selTab = tabId;
	var trId = $('#text_1').val();
	if(!$.isNumeric(trId))
	{
		hideLoader();
		showSweetAlertMessage(type = 'Error', message = 'Please add assest number' , icon= 'error');
		return false;		
	}
	if(trId.length < 3 )
	{
		hideLoader();
		showSweetAlertMessage(type = 'Error', message = 'Please enter Asset ID' , icon= 'error');
		$('input[name=radio_2]').prop('checked',false);
		$("#var_tab").html('');
		$("#reviewBtn").hide();
		return false;
	}      
	var isMob = $('#radio_2_4').prop('checked');
	if(trId.length > 0 && !isMob)
	{
		$.get("/"+prefix+"/checktravelerid?trid=" + trId +"&t=" + Math.random(), function ( data ) { 
			hideLoader();
			var isErr = false;
			if ( !edit && data == "Duplicate" )
			{
				showSweetAlertMessage(type = 'Warning', message = 'Duplicate Entry' , icon= 'warning');
				$('#text_1').val("");
				$('#text_1').focus();
				isErr = true;
				$('input[name=radio_2]').prop('checked',false);
				$("#var_tab").html('');
				$("#reviewBtn").hide();
			}
			else
			{
				if (data=="Missing")
				{
					showSweetAlertMessage(type = 'Error', message = 'Data files not found for entered Asset Number' , icon= 'error');
					$('#text_1').val("");
					$('#text_1').focus();
					isErr = true;
					$('input[name=radio_2]').prop('checked',false);
					$("#var_tab").html('');
					$("#reviewBtn").hide();
				}
				if (data.substring(0,4)=="Data")
				{
					var da=data.split("|");
					hddc = da[2];
					ramc = da[1];
					if(!edit)
					{
						$("input[data-hddcapacity='1']").val(hddc);
						$("input[data-ramcapacity='1']").val(ramc);
					}
					cpuname  = da[3];
					if(!isNaN(da[4])) cpuspeed = parseFloat(da[4]);
				}
				if ( !isErr ) showTab(selTab);
			} 
		});
	}
	else if(isMob)
	{ 
		CheckTravelerIdForMobile( trId , selTab );
	}
}

function CheckTravelerIdForMobile ( trId , selTab )
{  
	var isErr = false;
	$.get("/"+prefix+"/checktraveleridformobile?trid=" + trId +"&t=" + Math.random(), function ( data ) {
		hideLoader();
		if (data == "Missing")
		{
			showSweetAlertMessage(type = 'Warning', message = 'Data files not found for entered Asset Number' , icon= 'warning');
			$('#text_1').val("");
			$('#text_1').focus();
			isErr = true;   
			$('input[name=radio_2]').prop('checked',false);
			$("#var_tab").html('');
			$("#reviewBtn").hide();
		}
		else
		{
			showTab( selTab );
		} 
	});
}

function noDisable( iId )
{
	$("#"+iId).removeAttr("data-disable");
}

function addTrId(trid,dst)
{
	$("#text_1").val(trid);
	$("#uhint").hide();
	$.get("/"+prefix+"/loadxml?trid="+trid+"&t="+Math.random(), function(data)
	{
		var fCnt = JSON.parse(data);
		var prodName = fCnt['radio_2'];
		if (fCnt.model !== undefined)
		{
			$("#modelid").val(fCnt.model);
			asinmodels = fCnt['models'];
		}
		$(":input[name='radio_2'][value='" + prodName + "']").prop('checked', true);
		items = fCnt['items'];
		checkTravelerId(prodName);
	});
}

function getLastInput()
{
	var trId = $('#text_1').val();
	if (trId.length < 3)
	{
		showSweetAlertMessage(type = 'Error', message = 'Please enter Asset ID' , icon= 'error');
		return false;
	}
	$.get("/"+prefix+"/loadlast?t="+Math.random(), function(data)
	{
		if (data=="false")
		{
			showSweetAlertMessage(type = 'Error', message = 'Data not found' , icon= 'error');
		}
		else
		{
			var fCnt = JSON.parse(data);
			var prodName = fCnt['radio_2'];
			if (fCnt.model !== undefined)
			{
				$("#modelid").val(fCnt.model);
				asinmodels = fCnt['models'];
			}
			$(":input[name='radio_2'][value='" + prodName + "']").prop('checked', true);
			for (var i = 0, len = fCnt['items'].length; i < len; i++)
			{
				if (fCnt['items'][i]['template']==1)
				{
					items.push(fCnt['items'][i]);
				}
			}
			lastload=true;
			checkTravelerId(prodName);
		}
	});
}

function getModelData(modelId,tId)
{
	modelSet = true;
	$("#uhint").hide();
	$.get("/"+prefix+"/loadmodel?m="+modelId+"&t="+Math.random(), function(data)
	{
		if (data=="false")
		{			
			$("#"+tId).val($("#model"+modelId).text());
			$('#modelid').val('0');
		}
		else
		{
			$('#modelid').val(modelId);
			var fCnt = JSON.parse(data);
			for (var i = 0, len = fCnt['items'].length; i < len; i++)
			{
				if (fCnt['items'][i]['fillmodel']==1)
				{
					items.push(fCnt['items'][i]);
				}
			}
			fillData();
			$(":input[data-fillmodel]").closest(".formitem").hide();
			$('input[data-modelname="1"]').prop("readonly",true).closest(".formitem").show();
			$('input[data-modelname="1"]').closest(".formitem").append('<div class="form-group"><label class="ttl">&nbsp;</label><br/><button type="button" class="btn btn-secondary" onclick="showModelFields()">Toggle common data</button></div>');
			if(fCnt['asin'] != '0')
			{
				asinmodels = fCnt['models'];
			}
		}
	});
}

function refurbNotification()
{
	showLoader();
	var trId = $('#text_1').val();
	var modelId = $('#modelid').val();
	forRefurb = false;
	cpuname = "";
	cpuspeed = 0;
	asinmodels = [];
	calcGrade(false);
	$.get("/"+prefix+"/getrefnotification?m="+modelId+"&a="+trId+"&t="+Math.random(), function(data)
	{
		hideLoader();
		var rData = JSON.parse(data);
		cpuname = rData["cpuname"];
		$('#cpuname').val(cpuname);
		asinmodels = rData["models"];
		checkRefurb();
		if(forRefurb == true && damageScore <=3 && !isBlacklisted)
		{
			alert('This item is suitable for Refurb. Please put aside');
			$('#refurb').val('1');
		}
		else
		{
			$('#refurb').val('0');
		}
		$("#frmSubmitBtn").prop('disabled',false);
	});
}

function checkRefurb()
{
	asinid = 0;
	var wl = ['e6420','e6430']; //lowercase!
	for (var c in asinmodels)
	{
		var mdl = asinmodels[c];
		var cName = (mdl.model != null || mdl.model != undefined) ? mdl.model.toLowerCase() : '';
		var cCore = (mdl.cpu_core != null || mdl.cpu_core != undefined) ? mdl.cpu_core.toLowerCase() : '';
		var cModels = (mdl.cpu_model != null || mdl.cpu_model != undefined) ? mdl.cpu_model.toLowerCase().split(',') : [];
		var cSpeed = (mdl.cpu_speed != null || mdl.cpu_speed != undefined) ? mdl.cpu_speed.replace('GHz','').trim() : '';
		var mdlId = mdl.id;
		var speedMatch = true;
		var isWhitelisted = false;
		for(var w in wl)
		{
			if(cName.indexOf(wl[w]) !== -1)
			{
				isBlacklisted = false;
				damageScore = 0;
				isWhitelisted = true;
			}
		}
		for(var m in cModels)
		{
			var cModel = cModels[m];
			if (speedMatch && cpuname.indexOf(cCore) !== -1  && (isWhitelisted || cpuname.indexOf(cModel) !== -1))
			{
				forRefurb = true;
				asinid = mdlId;
			}
		}
	}
	$('#asinid').val(asinid);
}

function fillData()
{
	for (i = 0; i < items.length; ++i)
	{
		var itm = items[i];
		var vals = itm["value"];
		if (itm["new"] != "")
		{
			$("#"+itm["id"]+"_new").val(itm["new"]);
			$("#"+itm["id"]).val("Other:");
			$("#"+itm["id"]+"_newitm").prop('checked', true);
		}
		if (itm["type"] == "mult" || itm["type"] == "radio")
		{
			for (j = 0; j < vals.length; ++j)
			{
				$(":input[name='"+itm["id"]+"'][value='" + vals[j] + "']").prop('checked', true);
				$(":input[name='"+itm["id"]+"[]'][value='" + vals[j] + "']").prop('checked', true);
			}
		}
		else
		{
			$("#"+itm["id"]+"_cb").val(vals[0]);
			$("#"+itm["id"]).val(vals[0]);
			$(":input[name='"+itm["id"]+"']").val(vals[0]);
		}
	}
	calcGrade();
}