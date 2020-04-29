$(document).ready(function(){
	$.extend( $.fn.dataTable.defaults, {
	    language: {
	        processing: "Loading. Please wait..."
	    },
	 
	});
	$('#supplies-list').DataTable({
        processing : true,
        serverSide : true,
        ajax : {
            url:'/'+prefix+'/supplies?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        columns: [
        	{ 'data': 'action' },
        	{ 'data': 'id' },
        	{ 'data': 'item_name' },
        	{
            	mRender: function (data, type, row) {
            	var appendToResult = '<form method="GET" action="'+row.updateurl+'">';
            	appendToResult = appendToResult.concat('<a href="javascript:void(0)" onclick="reorderItem('+row.id+','+row.qty+','+row.updateqtyreorder+')"><img src="'+row.cartimageurl+'" alt="Reorder" title="Reorder"></a>&nbsp;');
				appendToResult = appendToResult.concat('<input type="hidden" name="pageaction" id="pageaction" value="'+row.pageaction+'"/>');
				appendToResult = appendToResult.concat('<input type="hidden" name="supplieid" value="'+row.id+'">');
				appendToResult = appendToResult.concat('<input type="number" style="width:70px;min-width:70px;height:20px" min="0" name="qty" value="'+row.qty+'">');
				appendToResult = appendToResult.concat('<input type="image" style="width:20px;min-width:20px;height:20px; border:none" src="'+row.tickimageurl+'" title="Save" alt="Save">');
				appendToResult = appendToResult.concat('</form>');
                return appendToResult;
            }},
            { 'data': 'part_num' },
			{ 'data': 'description' },
			{ 'data': 'dept' },
			{ 'data': 'price' },
			{ 'data': 'vendor' },
			{ 'data': 'low_stock' },
			{ 'data': 'reorder_qty' },
			{ 'data': 'dlv_time' },
        ],        
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });

    $('#asins-list').DataTable({
        processing : true,
        serverSide : true,
        ajax : {
            url:'/'+prefix+'/asin?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        columns: [
        	{ 'data': 'action' },
        	{ 'data': 'id' },
        	{
            	mRender: function (data, type, row) {
	            if(row.asinlink)
	            {
	            	return '<a href="'+row.link+'" target="_blank">'+row.asin+'</a>';
	            }
	            else
	            {
	            	return '<a href="https://www.amazon.com/dp/'+row.asin+'?ref=myi_title_dp" target="_blank">'+row.asin+'</a>';	
	            }
            }},
			{ 'data': 'price' },
			{ 'data': 'manufacturer' },
			{ 'data': 'notifications' },
			{ 'data': 'form_factor' },
			{ 'data': 'cpu_core' },
			{ 'data': 'cpu_model' },
			{ 'data': 'cpu_speed' },
			{ 'data': 'ram' },
			{ 'data': 'hdd' },
			{ 'data': 'os' },
			{ 'data': 'webcam' },
        ],        
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });

    $('#users-table').DataTable({
        processing : true,
        serverSide : true,
        ajax : {
            url:'/'+prefix+'/users?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        columns: [
        	{ 'data': 'action' },
        	{ 'data': 'id' },
			{ 'data': 'name' },
			{ 'data': 'email' },
			{ 'data': 'username' },
        	{
            	mRender: function (data, type, row) {
        		var html = '<span class="'+row.verifiedclass+'">'+row.verifiedtext+'</span>';
	            if(row.verifiedcheck)
	            {
        			html = html.concat('<a href="javascript:void(0)" title="Verified User" onclick="verifyuser('+row.id+','+row.verified+','+row.verifyuser+')"><i class="fa fa-check" aria-hidden="true"></i></a>')
	            }
	            return html;
            }},
			{ 'data': 'role' },
        ],        
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });

    var itamgShopifyInventoryIist = $('#main-table').DataTable({
    	responsive: true,
		autoWidth: false,
        processing : true,
        serverSide : true,        
        order: [[ 1, "desc" ]],
        ajax : {
            url:'/'+prefix+'/inventory?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        "rowCallback": function( row, data ) {
        	let shopifyProductId = (data.shopify_product_id) ? 'no' : 'yes';
        	$(row).css('font-weight','bold');
        	// $(row).addClass("nlrow");
        	$(row).addClass(data.asin);
        	$(row).attr("data-img",data.images);
        	$(row).attr("data-mdl",data.model);
        	$(row).attr("data-price",data.price_display);
        	$(row).attr("data-ff",data.technology);
        	$(row).attr("data-cpu",data.cpg);
        	$(row).attr("data-synced",shopifyProductId);
		},
		columnDefs: [
		{
			targets: 0,
			searchable: false,
			orderable: false,
			defaultContent: "",
			render: function (full, type, data, meta){
				var html = '';
				if(data.shopify_product_id == '' && data.mid != '')
				{
					html = '<input id="cb-select-'+data.asin+'" type="checkbox" name="sync-all-ids[]" value="'+data.asin+'">';
				}
				return html;
			}
		},
		{
			targets: 1,
			render: function (full, type, data, meta){
				return data.asin
			}
		},
		{
			targets: 2,
			searchable:false,
			orderable:false,
			defaultContent: "",
			render: function (full, type, data, meta){
				var itamgasinclass = "'.asin"+data.asin+"'";
				var html = '<a href="javascript:void(0);" onclick="$('+itamgasinclass+').toggle()">'+data.model+'</a>';
				return html;
			},
		},
		{
			targets: 3,
			searchable: false,
			orderable: false,
			render: function (full, type, data, meta){
				return data.technology;
			}
		},
		{
			targets: 4,
			searchable: false,
			orderable: false,
			render: function (full, type, data, meta){
				var html = data.cpg+' ('+data.cpus+' CPUs)';
				return html;
			},
		},
		
		{
			targets: 7,
			render: function (full, type, data, meta){
				return data.cnt;
			}
		},
		{
			targets: 8,
			render: function (full, type, data, meta){
				var itamgasinclass = "'#mid"+data.asin+"'";
				var html = '<span style="cursor:pointer" onclick="$('+itamgasinclass+').toggle()">Specify Model</span><div style="display: none;position:absolute" id="mid'+data.asin+'"><input class="form-control" type="text" id="model'+data.asin+'" onkeyup="getModels(this.id)"/></div>';
				if(data.isMid)
				{
					html = '<a href="'+data.templateUrl+'" target="_blank">Model Data</a>';
				}
				return html;
			}
		},
		{
			targets: 9,
			render: function (full, type, data, meta){
				var html = '';
				if (data.shopify_product_id)
				{
					html = '<button class="btn btn-link sync-to-shopify" data-id="'+data.asin+'">Update</button>';
				}
				return html;
			}
		},
		{
			targets: 10,
			searchable: false,
			orderable: false,
			render: function (full, type, data, meta){
				var html = data.images;					
				return html;
			}
		},
		{
			targets: 11,
			searchable:false,
			orderable:false,
			render: function(full, type, data, meta){
				var html = data.price_display;
				if(data.price_display == 'N/A')
				{
        			html = '<a style="cursor:pointer" href="javascript:void(0);" onclick="setPrice(\''+data.asin+'\')">'+data.price_display+'</a>';
				}
				return html;
			}
		},
		{
			targets: 12,
			searchable: false,
			orderable: false,
			render: function (full, type, data, meta){
				var html = '';
				if (data.shopify_product_id)
				{
					html = '<a href="'+data.inventoryUrl+'">'+data.shopify_product_id+'</a>';
				}
				return html;				
			}
		},
		{
			targets: 13,
			searchable:false,
			orderable:false,
			className: 's_price',
			vissible: false,
			render: function (full, type, data, meta){
				var html = '';
				if (data.checkItamgPriceDiff)
				{
					if (data.itamgPriceDiff['diffrence'] != 0)
					{
						html = '<span>Shopify Price: $'+data.itamgPriceDiff['shopify_price']+'</span><span>Final Price: $'+data.itamgPriceDiff['final_price']+'</span><span>Diffrence: $'+data.itamgPriceDiff['diffrence']+'</span><button class="btn btn-link update-price-to-shopify" data-id="'+data.asin+'">Update Price</button>';
					}
				}
				return html;
			}
		},
		{
			targets: [5,6],
			searchable: false,
			orderable:false,
			render: function (data, type, row, meta){
				return '';
			}
		},
		],
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });

	var itamgRunningIist = $('#itamg-running-list').DataTable({
		responsive: true,
		autoWidth: false,
        processing : true,        
        serverSide : true,        
        order: [[ 1, "desc" ]],
        ajax : {
            url:'/'+prefix+'/runninglist?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        rowCallback: function( row, data ){
        	$(row).addClass(data.aid);
		},
		columnDefs: [
			{
				targets: 0,
				searchable:false,
			    orderable:false,
			    defaultContent: "",
				render: function (data, type, row, meta){
					var html = '';
	        		if(row.shopify_product_id == '' && row.mid != '')
		        	{
						html = '<input id="cb-select-'+row.asin+'" type="checkbox" name="sync-all-ids[]" value="'+row.asin+'">';
		        	}
		            return html;
		        },
			},
			{
				targets: 1,
				defaultContent: "",
				render: function (data, type, row, meta){
					var itamgasinclass = "'.asin"+row.aid+"'";					
            		var html = '<a href="javascript:void(0);" onclick="$('+itamgasinclass+').toggle()">'+row.asin+'</a>';
		            return html;
		        },
			},
			{
				targets: 2,
				render: function (data, type, row, meta){
		            return row.model;
		        }
			},
			{
				targets: 3,
				render: function (data, type, row, meta){
					return row.form_factor;
		        }
			},
			{
				targets: 4,
				render: function (data, type, row, meta){
					var html = row.cpu_core+row.cpu_model+' CPU @ '+row.cpu_speed;
		            return html;
		        }
			},
			{
				targets: 5,
				render: function (data, type, row, meta){
		            return row.price;
		        }
			},
			{
				targets: 6,
				render: function (data, type, row, meta){
		            return '';
		        }
			},
			{
				targets: 7,
				render: function (data, type, row, meta){
		            return '';
		        }
			},
			{
				targets: 8,
				render: function (data, type, row, meta){
		            return row.cnt;
		        }
			},
			{
				targets: 9,
				render: function (data, type, row, meta) {
					var html = '';
					if (row.shopify_product_id)
					{
						html = '<button class="btn btn-link sync-to-shopify" data-id="'+row.asin+'">Update</button>';
					}
					return html;
				}
			},
			{
				targets: 10,
				render: function (data, type, row, meta){
		            return row.images;
		        }
			},
			{
				targets: 11,
				render: function (data, type, row, meta){
		            return row.shopify_product_id;
		        }
			},
			{
				targets: 12,
				render: function (data, type, row, meta){
		            var html = '';
					if (row.checkItamgPriceDiff)
					{
						if (row.itamgPriceDiff['diffrence'] != 0)
						{
							html = '<span>Shopify Price: $'+row.itamgPriceDiff['shopify_price']+'</span><span>Final Price: $'+row.itamgPriceDiff['final_price']+'</span><span>Diffrence: $'+row.itamgPriceDiff['diffrence']+'</span><button class="btn btn-link update-price-to-shopify" data-id="'+row.asin+'">Update Price</button>';
						}
					}
					return html;
		        }
			},
		],        
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });
	
	itamgRunningIist.on( 'xhr', function () {
	    var json = itamgRunningIist.ajax.json();
		if(json.data.length > 0)
		{
			setTimeout(function(){
				$.each(json.data, function( index, value ) {
					var asinItem = value.items;
					var asinClass = value.aid;
					asinRunnListItems(asinItem,parentEl='tbody',asinClass)
				});
			}, 1000);
		}
	});

	itamgShopifyInventoryIist.on( 'xhr', function () {
	    var json = itamgShopifyInventoryIist.ajax.json();
	    if(json.data.length > 0)
		{
			setTimeout(function(){
				$('.s_price').css('display','none');
				$.each(json.data, function( index, value ) {
					var asinItem = value.items;
					var asinClass = value.asin;
					asinShopifyInventoryIist(asinItem,parentEl='tbody',asinClass)
				});
			}, 1000);
		}
	});

	$('#supplies, #asins, #message-logs').DataTable({dom: 'lipBf',aoColumnDefs: itamgSort,
        "lengthMenu": itamgLength,});

	$('.shipment-list, .shipment-asin-list').DataTable
	({
		"order": [[ 0, "desc" ]],
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});

	$('.sessions-list, .sessions-asins-part-list').DataTable
	({
		"order": [[ 0, "desc" ]],
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});

	var asinInventryTableItmg = $('#asinInventryTable-Itmg').DataTable({
		responsive: true,
		autoWidth: false,
        processing : true,
        serverSide : true,             
        order: [[ 1, "desc" ]],
        ajax : {
            url:'/'+prefix+'/asininventry?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        rowCallback: function( row, data ){
        	$(row).addClass(data.aid);
		},
		columnDefs: [
			{
				targets: 0,
				defaultContent: "",
				render: function (data, type, row, meta){
					var cc = "'.asset"+row.aid+"'";
					var html = '<a href="javascript:void(0);" onclick="$('+cc+').toggle();">'+row.asin+'</a>';
		            return html;
		        },
			},
			{
				targets: 1,
				render: function (data, type, row, meta){
		            return row.model;
		        }
			},
			{
				targets: 2,
				render: function (data, type, row, meta){
					return row.form_factor;
		        }
			},
			{
				targets: 3,
				render: function (data, type, row, meta){
					var html = row.cpu_core+row.cpu_model+' CPU @ '+row.cpu_speed;
		            return html;
		        }
			},
			{
				targets: 4,
				render: function (data, type, row, meta){
		            return row.price;
		        }
			},
			{
				targets: 5,
				render: function (data, type, row, meta){
		            return row.cnt;
		        }
			},
		],        
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });
	
	asinInventryTableItmg.on( 'xhr', function () {
	    var json = asinInventryTableItmg.ajax.json();
		if(json.data.length > 0)
		{
			setTimeout(function(){
				$.each(json.data, function( index, value ) {
					var asinItem = value.assets;
					var asinClass = value.aid;
					asinInventoryItems(asinItem,parentEl='tbody',asinClass)
				});
			}, 1000);
		}
	});

	var partLookups = $('#lookup').DataTable({
		responsive: true,
		autoWidth: false, 
        processing : true,
        serverSide : true,             
        order: [[ 1, "desc" ]],
        ajax : {
            url:'/'+prefix+'/partlookup?pageaction='+pageaction+'&dtable='+dItamgTable,
            type:"POST"
        },
        rowCallback: function( row, data ){
        	$(row).css('cursor','pointer');
        	$(row).addClass("partslookup-asin");
        	$(row).addClass(data.asin);
        	$(row).addClass('asin'+data.id);
        	$(row).attr("data-model",data.asin.toLowerCase()+data.model.toLowerCase());
        	$(row).attr("data-url",data.url);
		},
		columnDefs: [
			{
				targets: 0,
				defaultContent: "",
				render: function (data, type, row, meta){
					var html = '<a href="javascript:void(0);" onclick="location.href = '+"'"+row.url+"'"+'">'+row.asin+'</a>';
		            return html;
		        },
			},
			{
				targets: 1,
				render: function (data, type, row, meta){
		            return row.model;
		        }
			},
			{
				targets: 2,
				render: function (data, type, row, meta){
					return row.form_factor;
		        }
			},
			{
				targets: 3,
				render: function (data, type, row, meta){
					return row.cpu_core+row.cpu_model+row.cpu_speed;
		        }
			},
			{
				targets: 4,
				render: function (data, type, row, meta){
		            return row.ram;
		        }
			},
			{
				targets: 5,
				render: function (data, type, row, meta){
		            return row.hdd;
		        }
			},
		],        
        aoColumnDefs: itamgSort,
        lengthMenu: itamgLength,
    });
})

function asinShopifyInventoryIist(argument,el,asinClass)
{
	var html = '';
	$.each(argument, function( index, value ) {
		html = html.concat('<tr style="display: none;" class="drow asin'+asinClass+'">');
	 	html = html.concat('<td></td>');
	  	html = html.concat('<td></td>');
	  	html = html.concat('<td></td>');
	  	html = html.concat('<td data-a="cpu">'+value["cpu"]+'</td>');
	  	html = html.concat('<td data-a="asset">'+value["asset"]+'</td>');
		html = html.concat('<td data-a="added_on">'+value["added_on"]+'</td>');
		html = html.concat('<td></td>');
		html = html.concat('<td data-a="asset"><a href="/admin/inventory?pageaction=refurbconnect&remove='+value["asset"]+'"><span class="fa fa-trash"></span></a></td>');		
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('</tr>');
	});
	$(html).insertAfter(el+' tr.'+asinClass);
}

function asinRunnListItems(argument,el,asinClass)
{
	var html = '';
	$.each(argument, function( index, value ) {
	  	html = html.concat('<tr style="display: none;" class="drow asin'+asinClass+'">');
	  	html = html.concat('<td></td>');
	  	html = html.concat('<td>'+value["asin"]+'</td>');
	  	html = html.concat('<td>'+value["model"]+'</td>');
	  	html = html.concat('<td>'+value["form_factor"]+'</td>');
	  	html = html.concat('<td>'+value["cpu_core"]+value["cpu_model"]+' CPU @ '+value["cpu_speed"]+'</td>');
	  	html = html.concat('<td>'+value["price"]+'</td>');
	  	html = html.concat('<td>'+value["asset"]+'</td>');
	  	html = html.concat('<td>'+value["added_on"]+'</td>');
	  	html = html.concat('<td></td>');
		html = html.concat('<td><a onclick="return confirm("Are you sure want to delete this?");" href="/admin/runninglist?pageaction=refurbconnect&remove='+value['asset']+'"><span class="fa fa-trash"></span></a></td>');
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('<td></td>');
		html = html.concat('</tr>');
	});
	$(html).insertAfter(el+' tr.'+asinClass);
}

function asinInventoryItems(argument,el,asinClass)
{
	var html = '';
	html = html.concat('<tr class="asset'+asinClass+'" style="display: none;">');
  	html = html.concat('<td colspan="6"><b>Asset Numbers:</b>'+argument.toString()+'</td>');
	html = html.concat('</tr>');
	$(html).insertAfter(el+' tr.'+asinClass);
}

$(document).on('click','.partslookup-asin', function (argument) {
	const parseResult = new DOMParser().parseFromString($(this).data('url'), "text/html");
	const parsedUrl = parseResult.documentElement.textContent;
	window.location.href = parsedUrl;
})
