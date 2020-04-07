var items = []
$(document).ready(function()
{
	$(window).keydown(function(event)
	{
		if(event.keyCode == 13)
		{
			event.preventDefault();
			return false;
		}
	});
	// console.log(templateItems);
	if (templateItems.length > 0)
	{
		items = templateItems;
		fillData();
	}
	$('input[data-modelname="1"]').prop("readonly",true);
});

function calcGrade()
{
	damageScore = 0;
	$('input[type=checkbox]').each(function () 
	{
	    if(this.checked)
	    {
			var dKey = $(this).val()
			dKey = dKey.replace('"', '');
			if (dKey in dScores)
			{
				damageScore += dScores[dKey];
			}
		}
	});
	var gVal="";
	if (damageScore > 10)
	{
		gVal = "C";
	}
	else if (damageScore > 5)
	{
		gVal = "B";
	}
	else if (damageScore > 0)
	{
		gVal = "A";
	}
	else
	{
		gVal = "A+";
	}
	$(":radio[value='"+gVal+"']").prop('checked',true); 
}

function showTab(tabId)
{
	$.get("/"+prefix+"/gettab?tab="+tabId+"&t="+Math.random(), function (data){
    	$("#var_tab").html(data);
    	$("#reviewBtn").show();
    	setHeader();
    	$("body").css('background-image', 'none');
    	$('#page-bottom').removeClass("bottom-url");
    	alignCB();
    	$(":input[data-display]").closest(".formitem").hide();
    	if (items.length > 0)
    	{
			fillData();
			if (lastload)
			{
				$(":input[data-fillmodel]").closest(".formitem").hide();
				$('input[data-modelname="1"]').prop("readonly",true).closest(".formitem").show();
				$('input[data-modelname="1"]').closest(".formitem").append('<div class="form-group"><label class="ttl">&nbsp;</label><br/><button class="btn btn-default" onclick="showModelFields()">Toggle common data</button></div>');
			}
		}
	});
}

function frmPreview()
{
    $.get("/"+prefix+"/getpreview?" + $("#main-form").serialize() + "&t=" + Math.random(), function (data) {
       $("#preview-content").html(data);
       $("#main-form").hide();
       $("#page-logo").hide();
       $("#page-head").hide();
       $("#preview").show();
    });
}

function checkTravelerId(trId)
{
    if (!edit && trId.length > 0)
    {
		$.get("/"+prefix+"/checktravelerid?trid=" + trId +"&t=" + Math.random(), function (data) {
	        if (data=="Duplicate")
	        {
				showSweetAlertMessage(type = 'error', message = 'Duplicate Entry' , icon= 'error');
				$('#text_1').val("");
	        	$('#text_1').focus();
			}
			else
			{
				if (data=="Missing")
				{
					showSweetAlertMessage(type = 'error', message = 'Data files not found for entered Asset Number' , icon= 'error');
					$('#text_1').val("");
		        	$('#text_1').focus();
				}
			}
		});
    }
}

function addTrId(trid,dst)
{
	$("#text_1").val(trid);
	$("#uhint").hide();
	$.get("/"+prefix+"/loadxml?trid="+trid+"&t="+Math.random(), function(data)
	{
		var fCnt = JSON.parse(data);
		var prodName = fCnt['radio_2'];
		$(":input[name='radio_2'][value='" + prodName + "']").prop('checked', true);
		items = fCnt['items'];
		showTab(prodName);
	});
}

function getLastInput()
{
	$.get("/"+prefix+"/loadlast?t="+Math.random(), function(data)
	{
		if (data=="false")
		{
			showSweetAlertMessage(type = 'warning', message = 'Data not found' , icon= 'warning');
		}
		else
		{
			var fCnt = JSON.parse(data);
			var prodName = fCnt['radio_2'];
			$(":input[name='radio_2'][value='" + prodName + "']").prop('checked', true);
			for (var i = 0, len = fCnt['items'].length; i < len; i++)
			{
				if (fCnt['items'][i]['template']==1)
				{
					items.push(fCnt['items'][i]);
				}
			}
			lastload=true;
			showTab(prodName);
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
		}
		else
		{
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
			$('input[data-modelname="1"]').closest(".formitem").append('<div class="form-group"><label class="ttl">&nbsp;</label><br/><button class="btn btn-default" onclick="showModelFields()">Toggle common data</button></div>');
		}
	});
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
			$("#"+itm["id"]).val(vals[0]);
		}
	}
}