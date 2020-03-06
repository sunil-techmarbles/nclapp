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
	var prefix = 'admin';
	// const swalWithBootstrapButtons = Swal.mixin({
	// 	customClass: {
	// 		confirmButton: 'btn btn-success',
	// 		cancelButton: 'btn btn-danger'
	// 	},
	// 	buttonsStyling: false
	// })

	$(window).on('resize', function(){
	    setHeader();
	});
	
	function savePN()
	{
		var m = $('#pnModel').val();
		var p = $('#pnPn').val();
		if(!m || !p)
		{
			alert('Please enter Model and Part Number');
			return false;
		}
		$.get("ajax.php?action=savePN&m=" + m + "&p=" + p + "&t=" + Math.random(), function (data)
		{
			alert(data);
			$('#pnModal').modal('hide');
	    });
	}
	
	function editCapacity(cType)
	{
		$("#detailModal").modal("show");
		$(".capedit").val('');
		$("#capatype").val(cType);
		var str = $('input[data-'+cType+'capacity="1"]').val();
		if (str!="")
		{
			str = str.split("_").join("");
			console.log(str);
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
				if(astr.length==2){
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
    	if (getUrlParameter("edit")==1) edit=true;
    	setHeader();
    	$(window).keydown(function(event)
    	{
		    if(event.keyCode == 13)
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
				if($(this).val() == result) {
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
        	// $("body").css('background-image', 'none');
        	$('#page-bottom').removeClass("bottom-url");
        	alignCB();
        	$(":input[data-display]").closest(".formitem").hide();
        	$(':input[data-cb="1"]').addClass("combobox");
			$('.combobox').combobox({appendId:"_cb"});
			$("input[data-hddcapacity='1']").val(hddc);
			$("input[data-ramcapacity='1']").val(ramc);
			$('input[data-ramcapacity="1"]').parent().append('<span style="font-size:20px;cursor:pointer" onclick="editCapacity(\'ram\')" class="glyphicon glyphicon-edit"></span>');
			$('input[data-hddcapacity="1"]').parent().append('<span style="font-size:20px;cursor:pointer" onclick="editCapacity(\'hdd\')" class="glyphicon glyphicon-edit"></span>');
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
	    $("input[data-disable=1]").prop("disabled",true);
	    $.get("ajax.php?action=getPreview&" + $("#main-form").serialize() + "&t=" + Math.random(), function (data) {
			$("#preview-content").html(data);
			$("#main-form").hide();
			$("#page-logo").hide();
			$("#page-head").hide();
			$("#preview").show();
			setTimeout(refurbNotification,1000);
	    });
	}

	function showSweetAlertMessage(type, message, icon)
	{
		swalWithBootstrapButtons.fire(
    		type,
			message,
			icon
		) 
	}
	
	var selTab;
	function checkTravelerId( tabId )
	{
	    selTab = tabId; 
	    var trId = $('#text_1').val();
	    if(trId.length < 3 )
	    {	
	    	showSweetAlertMessage(type = 'error', message = 'Please enter Asset ID' , icon= 'error');
			$('input[name=radio_2]').prop('checked',false);
	    	$("#var_tab").html('');
	    	$("#reviewBtn").hide();
	    	return false;
		}      
	    var isMob = $('#radio_2_4').prop('checked');
	    if(trId.length > 0 && !isMob)
	    {
			$.get("/"+prefix+"/checktravelerid?trid=" + trId +"&t=" + Math.random(), function ( data ) { 
		        var isErr = false;
		        if ( !edit && data == "Duplicate" )
		        {
		        	showSweetAlertMessage(type = 'warning', message = 'Duplicate Entry' , icon= 'warning');
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
						showSweetAlertMessage(type = 'warning', message = 'Data files not found for entered Asset Number' , icon= 'warning');
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
   			
   			if ( data == "Missing" )
   			{
   				showSweetAlertMessage(type = 'warning', message = 'Data files not found for entered Asset Number' , icon= 'warning');		
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
	
	function hidePreview()
	{
		$("#preview").hide();
		$("#main-form").show();
		$("#page-logo").show();
	    $("#page-head").show();
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
			$.get("ajax.php?action=getFiles&tgt="+fId+"&part="+data+"&t="+Math.random(), function(data)
			{
				if(data) {
					$("#hints").html(data);
					out.show();
				}
				else {
					out.hide();
				}
			});
		}
		else
		{
			out.hide();
		}
	}
	
	function addTrId(trid,dst)
	{
		$("#text_1").val(trid);
		$("#uhint").hide();
		$.get("ajax.php?action=loadXML&trid="+trid+"&t="+Math.random(), function(data)
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
			//showTab(prodName);
			checkTravelerId(prodName);
		});		
	}
	
	function getLastInput()
	{
		var trId = $('#text_1').val();
	    if (trId.length < 3)
	    {
			alert("Please enter Asset ID");
			return false;
		}
		$.get("ajax.php?action=loadLast&t="+Math.random(), function(data)
		{
			if (data=="false")
			{
				alert("Data not found");
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
		$.get("ajax.php?action=loadModel&m="+modelId+"&t="+Math.random(), function(data)
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
				$('input[data-modelname="1"]').closest(".formitem").append('<div class="form-group"><label class="ttl">&nbsp;</label><br/><button type="button" class="btn btn-default" onclick="showModelFields()">Toggle common data</button></div>');
				if(fCnt['asin'] != '0')
				{
					asinmodels = fCnt['models'];
				}
			}
		});	
	}
	
	function refurbNotification()
	{
		var trId = $('#text_1').val();
		var modelId = $('#modelid').val();
		forRefurb = false;
		cpuname = "";
		cpuspeed = 0;
		asinmodels = [];
		calcGrade(false);
		$.get("ajax.php?action=getRefNotification&m="+modelId+"&a="+trId+"&t="+Math.random(), function(data)
		{
			var rData = JSON.parse(data);
			cpuname = rData["cpuname"];
			$('#cpuname').val(cpuname);
			asinmodels = rData["models"];
			checkRefurb();
			if(forRefurb == true && damageScore <=3 && !isBlacklisted)
			{
				alert("This item is suitable for Refurb. Please put aside");
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
			var cName = mdl.model.toLowerCase();
			var cCore = mdl.cpu_core.toLowerCase();
			var cModels = mdl.cpu_model.toLowerCase().split(',');
			var cSpeed = mdl.cpu_speed.replace('GHz','').trim();
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
	
	function showModelFields()
	{
		$(":input[data-fillmodel]").closest(".formitem").toggle();
		$(':input[data-fillmodel="1"]').closest(".formitem").css("background-color","#F5F5DC");
		$('input[data-modelname="1"]').closest(".formitem").show();
		return false;
	}
	
	function checkModelTrigger()
	{
		setTimeout(checkModel,500);
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
					$(':input[data-fillmodel="1"]').closest(".formitem").css("background-color","#F4A460");
					$('input[data-modelname="1"]').prop("readonly",true).closest(".formitem").css("background-color","none");
					alert("Template created. Please carefully fill highlighted fields and submit the form");	
				}
			}
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
			//$(".hdddynrow").hide();
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
			alert("Please set the machine aside to be manually audited");
		}
	}