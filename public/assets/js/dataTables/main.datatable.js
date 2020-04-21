$(document).ready(function(){
	$('#supplies-list').DataTable({
        dom: 'lipBf',
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
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
        "lengthMenu": itamgLength,
    });

    $('#asins-list').DataTable({
        dom: 'lipBf',
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
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
        "lengthMenu": itamgLength,
    });

    $('#users-table').DataTable({
        dom: 'lipBf',
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
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
        			html = html.concat('<a href="javascript:void(0)" onclick="verifyuser('+row.id+','+row.verified+','+row.verifyuser+')"><i class="fa fa-check" aria-hidden="true"></i></a>')
	            }
	            return html;
            }},
			{ 'data': 'role' },
        ],        
        aoColumnDefs: itamgSort,
        "lengthMenu": itamgLength,
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

	$('.sessions-list, .sessions-asins-part-list, .sessions-asins-list').DataTable
	({
		"order": [[ 0, "desc" ]],
		"searching": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});
})