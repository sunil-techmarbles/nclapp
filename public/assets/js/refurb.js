
$(document).ready(function()
{
	$(document).keydown(function(event)
	{
	    if(event.keyCode == 13)
	    {
	      	if($("#asset_num").is(":focus"))
	      	{
				event.preventDefault();
				getAssetData('asset_num');
				return true;
			}
			else if ($("#ws_list").is(":focus"))
			{
				return true;
			}
			else
			{
				event.preventDefault();
				return "avbb";
			}
	    }
	});
});

function addCOA()
{
	$('#coa_sess').val(adata.asset);
	$('#old_coa').val(old_coa);
	$('#new_coa').val(new_coa);
	$('#win8').prop('checked',false);
	if(win8==1) $('#win8').prop('checked',true);
	if(old_coa != "" || new_coa != ""){
      $(".message1").html("");
	}
	checkWin8();
	$('#detailModal').modal('show');
}

function setNonAsin()
{
	var onum = prompt('Please enter the Order Number','');
	if(onum) {
		JsBarcode("#product_asin", onum,{height:50,width:1,fontSize:10});
	} else {
		JsBarcode("#product_asin", '000000',{height:50,width:1,fontSize:10});
	}
	$('#asinttl').text('ORDER');
	$('#asinModal').modal('hide');
}

function saveCoa()
{
	old_coa = $('#old_coa').val();
	new_coa = $('#new_coa').val();
	win8 = ($('#win8').prop('checked')) ? 1 : 0;
	if(old_coa == "" || new_coa == "")
	{
      	$(".message1").html("Please Input COA Values");
	}
	else
	{
		$.post("/"+prefix+"/savecoa",
				{_token:_token, asset: assetNumber, old_coa: old_coa, new_coa: new_coa, win8: win8, asin: adata.asin_id}
			).done(function( response ) {
			var icon = (response.status) ? 'success' : 'error';
			swalWithBootstrapButtons.fire( 
				response.type,
				response.message ,
				icon
			)
	    	$('#detailModal').modal('hide');
		});
	}
}

function getAssetData(fId)
{
	var data = encodeURIComponent($("#asset_num").val());
	if (data.length > 3)
	{
		$.get("/"+prefix+"/getasset?asset="+data+"&t="+Math.random(), function(response)
		{
			if(response.status)
			{
				forceWS = false;
				adata = response.result;
				device = adata["radio_2"];
				$('#asinttl').text('ASIN');
				$("#ws_form").hide();
				$("#asin_id").val(adata.asin_id);
				$("#tab-headers").html('');
				$("#tab-content").html('');
				$("#product_grade").text('');
				$("#f_rep_issues").text('');
				$(".replace_parts").text('');
				$("#f_nhdd").text('No changes needed');
				$("#f_nram").text('No changes needed');
				$(".laptop-only").hide();
				$(".conf-only").hide();
				$(".c_swap").hide();
				$(".specsram").hide();
				$(".specshdd").hide();
				$(".specsos").hide();
				$(".bondo").hide();
				$(".skin").hide();
				$(".paint").hide();
				$("#printbtn").hide();
				$("#printsaved").hide();
				$("#comp_form").html('').hide();
				// $("body").css('background-image', 'none');
				$(".f_opt").text('N/A');
				$(".frmrow").hide();
				old_coa = adata.old_coa;
				new_coa = adata.new_coa;
				win8 = adata.win8;
				adata["descr"]={};
				$("#mspecs").text(adata.Model+', '+adata.CPU+', '+adata.RAM+', '+adata.HDD);
				$("#f_model").text(adata.Model);
				$("#f_cpu").text(adata.CPU);
				$("#f_ram").text(adata.upd_ram);
				if(adata.upd_ram != adata.RAM && adata.upd_ram != "")
				{
					$("#f_nram").text('RAM: ⇒ '+adata.upd_ram);
					$(".specs").show();
					$(".specsram").show();
				}
				$("#f_hdd").text(adata.upd_hdd);
				if(adata.upd_hdd != adata.HDD && adata.upd_hdd!="")
				{
					$(".specs").show();
					$(".specshdd").show();
					$("#f_nhdd").text('HDD: ⇒ '+adata.upd_hdd);
				}
				for (var i in adata["items"])
				{
					var itm = adata["items"][i];
					if(itm.key=="Asset_Number")
					{
						if(adata["Serial"]=="") adata["Serial"] = '000000';
						JsBarcode("#product_id", itm.value[0],{height:50,width:1,fontSize:10});	
						JsBarcode("#product_asin", adata["asin"],{height:50,width:1,fontSize:10});	
						JsBarcode("#product_sern", adata["Serial"],{height:50,width:1,fontSize:10});	
						assetNumber = itm.value[0];
					} 
					else if (itm.key=="OS_Label")
					{
						$("#f_os_label").text(itm.value[0]);	
						if(itm.value[0]!=adata["upd_os"] && adata["upd_os"] !="")
						{
							$("#f_nos_label").text('⇒ ' + adata["upd_os"]);
							$(".specsos").show();
						}
						$(".c_os_label").show();
						if(itm.value[0].indexOf('Windows 8')>-1) $(".activate").show();
					}
					else if (itm.key=="Technology")
					{
						$("#f_technology").text(itm.value[0]);	
						$(".c_technology").show();
						if(itm.value[0] == 'Notebook')
						{
							$(".c_webcam").show();
							$("#f_webcam").text('No Webcam');	
						}
					}
					else if (itm.key=="Webcam")
					{
						$(".c_webcam").show();
						if (itm.value[0] == "Yes")
						{
							$("#f_webcam").text('Webcam');
						}
					} 
					else if (itm.key=="Technology")
					{
						$("#f_technology").text(itm.value[0]);	
						$(".c_technology").show();
					}
					else if (itm.key=="Fingerprint_Scanner")
					{
						$("#f_fingerprint").text(itm.value[0]);	
						$(".c_fingerprint").show();
					}
					else if (itm.key=="Battery_and_Power")
					{
						$("#f_battery").text(itm.value[0]);	
						$(".c_battery").show();
					}
					else if (itm.key=="Battery_Condition")
					{
						$("#f_battery_cond").text(itm.value[0]);
						$(".c_battery_cond").show();	
					}
					else if(itm.type=="mult")
					{
                       	$("#f_"+itm.key+"").text(itm.value.join("; "));
						$(".c_"+itm.key+"").show();
						adata.descr[itm.key]=itm.value; 
						$(".frmrow").show();
				 	}
				}
				adata.descr["swap"]=[];
				var datakeys = response.result.items;
				var akeysA = [];
				var obj = {};
				for(var x = 0; x < datakeys.length; x++)
				{
					if(datakeys[x].type == 'mult')
					{
						if( datakeys[x].key != 'Available_Video_Ports')
						{
						    obj[datakeys[x].key] = datakeys[x].key;
							akeysA.push(obj);
						}
					}
				}
				obj['Reported_Issues'] = 'Reported_Issues';
				var akeys = akeysA[0];
				adata["conf"].push({
					key: "Reported_Issues",
					options: []
				});
				for (var i in adata["conf"])
				{
					var itm = adata["conf"][i];
					var ka = akeys[itm.key];
                    if(akeys[itm.key] == undefined)
                    {
	                    $("#tab-content").append('<div id="edit_'+itm.key+'" class="tab-pane '+(i==0?'in active':'')+'"><h3>'+itm.key+'</h3></div>'); 
						for (var j in itm.options)
						{
							var opt = itm.options[j];
							var cb='<div class="cb-cnt noprint"><label class="btn" for="'+itm.key+'_'+j+'"><input class="edit_'+itm.key+'" type="checkbox"  value="'+opt+'" id="'+itm.key+'_'+j+'"> <span>'+opt+'</span></label></div>';
							$("#edit_"+itm.key).append(cb);
						}
						if(i==0)
						{
							$("#edit_"+itm.key).append('<div><button class="btn btn-primary" id="btnSetAsin" style="display:none" onclick="changeASIN()" type="button">Change ASIN</button> <button class="btn btn-primary" onclick="addCOA()" type="button">COA Info</button> <button class="btn btn-warning" onclick="setWS()" type="button">To Wholesale</button> <button style="float:right" class="btn btn-primary" onclick="nextTab('+i+')" type="button">Next</button></div>');
						}
						else if (itm.key == "Reported_Issues")
						{
							$("#edit_"+itm.key).append('<textarea class="form-control" style="margin-bottom:10px" rows="5" id="rep_issues"></textarea>');
							$("#edit_"+itm.key).append('<div style="text-align:right"><button class="btn btn-primary" onclick="prevTab('+i+')" type="button">Back</button> <button class="btn btn-primary" onclick="setDescriptions()" type="button">Submit</button></div>');
						}
						else
						{
							$("#edit_"+itm.key).append('<div><button class="btn btn-primary" onclick="addCOA()" type="button">COA Info</button> <button class="btn btn-warning" onclick="setWS()" type="button">To Wholesale</button> <span style="float:right"><button class="btn btn-primary" onclick="prevTab('+i+')" type="button">Back</button>&nbsp;<button class="btn btn-primary" onclick="nextTab('+i+')" type="button">Next</button></span></div>');
						}
                    }
                    else
                    {
						$("#tab-content").append('<div id="edit_'+ka+'" class="tab-pane '+(i==0?'in active':'')+'"><h3>'+itm.key+'</h3></div>'); 
						for (var j in itm.options)
						{
							var opt = itm.options[j];
							var ck = (adata.descr[ka] != undefined || adata.descr[ka] != null) ? (adata.descr[ka].indexOf(opt)>=0?' checked':'') : '';
							var cb='<div class="cb-cnt noprint"><label class="btn" for="'+ka+'_'+j+'"><input class="edit_'+ka+'" type="checkbox" '+ck+' value="'+opt+'" id="'+ka+'_'+j+'"> <span>'+opt+'</span></label></div>';
							$("#edit_"+ka).append(cb);
						}
						if (itm.key == "Other" && (device=="Computer" || device=="All_In_One" || device=="Apple_All_In_One" || device=="Apple_Tower"))
						{
							j++;
						} 
						if(i==0)
						{
							$("#edit_"+ka).append('<div><button class="btn btn-primary" id="btnSetAsin" style="display:none" onclick="changeASIN()" type="button">Change ASIN</button> <button class="btn btn-primary" onclick="addCOA()" type="button">COA Info</button> <button class="btn btn-warning" onclick="setWS()" type="button">To Wholesale</button> <button style="float:right" class="btn btn-primary" onclick="nextTab('+i+')" type="button">Next</button></div>');
						}
						else if (itm.key == "Reported_Issues")
						{
							$("#edit_"+ka).append('<textarea class="form-control" style="margin-bottom:10px" rows="5" id="rep_issues"></textarea>');
							$("#edit_"+ka).append('<div style="text-align:right"><button class="btn btn-primary" onclick="prevTab('+i+')" type="button">Back</button> <button class="btn btn-primary" onclick="setDescriptions()" type="button">Submit</button></div>');
						}
						else
						{
							$("#edit_"+ka).append('<div><button class="btn btn-primary" onclick="addCOA()" type="button">COA Info</button> <button class="btn btn-warning" onclick="setWS()" type="button">To Wholesale</button> <span style="float:right"><button class="btn btn-primary" onclick="prevTab('+i+')" type="button">Back</button>&nbsp;<button class="btn btn-primary" onclick="nextTab('+i+')" type="button">Next</button></span></div>');
						}
					}
				}		
				alignCB();
				setChange(); 
				if(adata.asins.length == 0)
				{
					showSweetAlertMessage('error', 'No ASIN found!', 'error');
				} 
				else if(adata.asin_match == 'partial')
				{
					changeASIN();
				}
				if(adata.asin_match == 'saved') $("#btnSetAsin").show();
				if (adata.print != '') $("#printsaved").show();
				if (adata.pdf != '')
				{
					$('#comp_form').html(adata.pdf).show();
				}
			}
			else
			{
				Swal.fire({
					  icon: 'error',
					  title: 'Oops...',
					  text: 'Nothing found!',
				})
			}
		});
	}
	else
	{
		Swal.fire({
					  icon: 'warning',
					  title: 'Oops...',
					  text: 'Please add asset number!',
				})
		$("#asset_form").hide(); 
		$("#tab-headers").html('');
		$("#tab-content").html('');
	}
}

function changeASIN()
{
	asins = adata.asins;
	$("#asintable").html('');
	$("#asintable").append('<tr><th>ASIN</th><th>Model</th><th>CPU</th><th>RAM</th><th>HDD</th><th>Price</th></tr>');
	for (var i in asins)
	{
		var a = asins[i];
		$("#asintable").append('<tr class="asinrow" onclick="setAsin('+i+')" id="asinr_'+i+'"></tr>');
		$("#asinr_"+i).append('<td>'+a["asin"]+'</td>');
		$("#asinr_"+i).append('<td>'+a["model"]+'</td>');
		$("#asinr_"+i).append('<td>'+a["cpu_core"]+' '+a["cpu_model"]+' '+a["cpu_speed"]+'</td>');
		$("#asinr_"+i).append('<td>'+a["ram"]+'</td>');
		$("#asinr_"+i).append('<td>'+a["hdd"]+'</td>');
		$("#asinr_"+i).append('<td>'+parseFloat(a["price"]).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>');
	}
	$("#asinModal").modal("show");
}

function setAsin(aId)
{
	var a = asins[aId];
	adata.asin_id = a.id;
	
	JsBarcode("#product_asin", a["asin"],{height:50,width:1,fontSize:10});
	$("#f_nram").text('RAM: ⇒ '+a["ram"]);
	$("#f_ram").text(a["ram"]);
	$("#f_nhdd").text('HDD: ⇒ '+a["hdd"]);
	$("#f_hdd").text(a["hdd"]);
	$("#f_nos_label").text('⇒ ' + a["os"]);
	$("#f_os_label").text(a["os"]);
	
	if(a["ram"] != adata.RAM) {
		
		$(".specs").show();
		$(".specsram").show();
	}
	if(a["hdd"] != adata.HDD)
	{
		$(".specs").show();
		$(".specshdd").show();
		
	}
	if($("#f_os_label").text()!=a["os"])
	{
		$(".specsos").show();
	}
	$("#asinModal").modal("hide");
	$("#btnSetAsin").show();

	$.post("/"+prefix+"/saveasin", {_token:_token, aid: a.id, asset: assetNumber}, function(response){
		var icon = (response.status) ? 'success' : 'error';
		swalWithBootstrapButtons.fire( 
					response.type,
					response.message ,
					icon
				) 
	    return false;
	});
}

function editProps()
{
	$('.tab-pane:eq(0)').addClass('in active');
	$("#printbtn").hide();
}

function nextTab(tabId)
{
	var nTab = parseInt(tabId) + 1;
	$("#printsaved").hide();
	$(".tab-pane:eq("+tabId+")").removeClass("in active");
	$(".tab-pane:eq("+nTab+")").addClass("in active");
	alignTab();
}

function prevTab(tabId)
{
	var nTab = parseInt(tabId) -1;
	$("#printsaved").hide();
	$(".tab-pane:eq("+tabId+")").removeClass("in active");
	$(".tab-pane:eq("+nTab+")").addClass("in active");
	alignTab();
}

var dfields = [];
function setDescriptions()
{
	dfields=[];
	$(".tab-pane").removeClass("in active");
	$("#printbtn").show();
	$("#asset_form").show();
	var ri = $("#rep_issues").val();
	$("#f_rep_issues").text('Reported issues: ' + ri);
	
	if(ri !='')
	{
		$.post(
			"/"+prefix+"/saveissue",
			{_token:_token, asset:assetNumber, sn:adata.Serial, issue:ri},
			function(response)
			{
				var icon = (response.status) ? 'success' : 'error';
				swalWithBootstrapButtons.fire( 
					response.type,
					response.message ,
					icon
				) 
			}
		);
	}
	$('input[type=checkbox]').each(function()
	{
		if($(this).prop("checked")) dfields.push($(this).val());
	});
	if (device=="Laptop" || device=="Apple_Laptop")
	{
		dfields.push("is_laptop");
		$(".laptop-only").show();
	}
	setProcessOptions();
}

function setWholesale()
{
	var a = $('#ws_list').val();
	$.post("/"+prefix+"/setwholesale", {a: a, _token:_token,}, function(response)
	{
	    var icon = (response.status) ? 'success' : 'error';
	    swalWithBootstrapButtons.fire(  
					response.type,
					response.message , 
					icon
				) 
		$('#ws_list').val('');
		$('#wsModal').modal('hide');
	});
}

function setWS()
{
	$.get("/"+prefix+"/setwholesale?a="+assetNumber+"&t="+Math.random(), function(response)
	{
		var icon = (response.status) ? 'success' : 'error';
		swalWithBootstrapButtons.fire( 
					response.type,
					response.message ,
					icon
				) 
	});
	forceWS = true;
	$(".tab-pane").removeClass("in active");
	var c = $('.tab-pane').length - 1;
	$(".tab-pane:eq("+c+")").addClass("in active");
}

function openWS()
{
	$('#ws_list').val('');
	$('#wsModal').modal('show');
}

function setProcessOptions()
{
	$(".col-sm-3").hide();
	$(".col-sm-3").css("background","none");
	$(".bondo").hide();
	$(".paint").hide();
	$(".skin").hide();
	for (var i in dfields)
	{
		var fld = dfields[i];
		if (process.hasOwnProperty(fld))
		{
			var option = process[fld]["option"];
			if (option != "" && option != "direct")
			{
				$("#"+option).closest(".form-group").show();
			}
		}
	}
	setNextProcess();
}

function setNextProcess()
{
	var nextProcess = [];
	if (forceWS) nextProcess.push('Add to Wholesale Pallet');
	var assetGrade = "A";
	var need_selection = false;
	for (var i in dfields)
	{
		var fld = dfields[i];
		if (process.hasOwnProperty(fld))
		{
			var option = process[fld]["option"];
			if (option == "direct")
			{
				if (nextProcess.indexOf(process[fld]["value"])<0 && process[fld]["value"]!="") nextProcess.push(process[fld]["value"]);
			}
			else if(option != "")
			{
				var sel = $("#"+option).val();
				if (sel == "")
				{
					$("#"+option).closest(".form-group").css("background","orange");
					need_selection = true;
				}
				else
				{
					$("#"+option).closest(".form-group").css("background","none");
					if (nextProcess.indexOf(process[fld][sel])<0 && process[fld][sel]!="") nextProcess.push(process[fld][sel]);
				}
			}
		}
	}
	if(nextProcess.length>0 && !need_selection)
	{
		$("#f_next_proc").html('');
		if(nextProcess.indexOf('Needs to be Skinned')>=0)
		{
			$(".skin").show();
		}
		if(nextProcess.indexOf('Requires Bondo')>=0)
		{
			assetGrade = 'B';
			$(".bondo").show();
		}
		if (device=="Computer" || device=="All_In_One" || device=="Apple_All_In_One" || device=="Apple_Tower")
		{
			$(".paint").hide();
		}
		else if (nextProcess.indexOf('Requires Paint')>=0)
		{
			assetGrade = 'B';
			$(".paint").show();
		}	
		if (nextProcess.indexOf('Add to Holding Pallet')>=0) assetGrade = 'C';
		if (nextProcess.indexOf('Add to Wholesale Pallet')>=0)
		{
			assetGrade = 'W';
			alert('Add to Wholesale Pallet');//Add to Wholesale Pallet
		}
		$("#product_grade").text(assetGrade);
	}
	else
	{
		$("#f_next_proc").html('');
	}
}

function checkWin8()
{
	$.post("/"+prefix+"/checkcoa", { _token:_token, asset: assetNumber, old_coa: old_coa, new_coa: new_coa, win8: win8, asin: adata.asin_id}, function(response){
		// console.log(response);
		if(response.status)
		{
			var icon = (response.status) ? 'success' : 'error';
			swalWithBootstrapButtons.fire( 
					response.type,
					response.message ,
					icon
				) 
		}
	});
}

var cbWidth=0;
function alignCB()
{
	$('.cb-cnt').each(function (index, value)
	{ 
	  	$(this).width('auto');
	  	var w = $(this).width();
	  	if (w > cbWidth) cbWidth = w; 
	});
	$('.cb-cnt').width(cbWidth);
}

function alignTab()
{
	setTimeout(alignCB,500);
}

var cv=[];
function setChange()
{
	$('input[type=checkbox]').change(function()
	{
		var cl= $(this).attr("class");
		if (cl != 'win8')
		{
			cv=[];
			$("."+cl).each(function()
			{
				if($(this).prop('checked')) cv.push($(this).val());
			});
			if (cv.length>0)
			{
				var out = cv.join("; ");
				$("#f_"+cl.replace('edit_','')).text(out);
				$(".c_"+cl.replace('edit_','')).show();
				if (cl.replace('edit_','') == 'swap')
				{
					$(".replace_parts").text('');
					for (var j in cv)
					{
						$(".replace_parts[data-cbref='" + cv[j] + "']").text('X');
					}
				}
			}
			else
			{
				$(".c_"+cl.replace('edit_','')).hide();
			}
		}
	});
}

function printLabel()
{
	savePrint();
	window.print();
	return false;
}

function savePrint()
{
	var data = $("#asset_form").html();
	$.post("/"+prefix+"/saveprint", { _token:_token, print: data, asset: assetNumber}, function(response){
    	return false;
	});
}

function printSaved()
{
	$("#asset_form").html(adata.print);
	$("#asset_form").show();
	$(".tab-pane").removeClass("in active");
	window.print();
	return false;
}