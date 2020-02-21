@extends('layouts.appadminlayout')
@section('title', 'Add Supplies')
@section('content')
<div class="mte_content">
	<div style="width: 100%">
		<table class="table" cellspacing="0" cellpadding="10" style="width:100%; margin: 0">
			<tbody>
				<tr style="background:#eee">
					<td><b>Item ID</b></td>
					<td><input type="hidden" name="id" value="">[auto increment]</td>
					<td style="min-width:300px;"></td>
				</tr>

				<tr style="background:#fff">
					<td><b>Item Name</b></td>
					<td><input type="text" name="item_name" value="" maxlength="varchar(200)" class="mte_req" id="id_1"></td>
					<td style="min-width:300px;">[item_name]</td>
				</tr>

				<tr style="background:#eee">
					<td><b>URL</b></td>
					<td><input type="text" name="item_url" value="" maxlength="varchar(500)" id="item_url"></td>
					<td style="min-width:300px;">[item_url]</td>
				</tr>

				<tr style="background:#fff">
					<td><b>Quantity</b></td>
					<td><input type="text" name="qty" value="" maxlength="int(11)" class="mte_req" id="id_2"></td>
					<td style="min-width:300px;">[qty]</td>
				</tr>

				<tr style="background:#eee">
					<td><b>P/N</b></td>
					<td><input type="text" name="part_num" value="" maxlength="varchar(100)" class="mte_req" id="id_3"></td>
					<td style="min-width:300px;">[part_num]</td>
				</tr>

				<tr style="background:#fff">
					<td><b>Description</b></td>
					<td><textarea name="description" id="description"></textarea></td>
					<td style="min-width:300px;">[description]</td>
				</tr>

				<tr style="background:#eee">
					<td><b>Applicable Models</b></td>
					<td>
						<div style="max-height:250px;overflow:auto">
							<input type="hidden" name="applicable_models" value="" id="applicable_models"><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="104"> B06Y3H72QB  EliteBook 840 G1(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="76"> B01HSJ2O0Y Compaq 8200 Elite(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="74"> B01CV9G1BO Compaq Elite 8300 (Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="80"> B01GU6HLH2 Elite 6300(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="75"> B01HSJ2O0Y Elite 8200(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="47"> B01HSJO9D4 Elite 8200 (Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="3"> B01LY0JRKV Elite 8300(Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="35"> B01CV9G1BO Elite 8300(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="102"> B07FCTD4H2 EliteBook 820 G1(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="105"> B07FCWX4GT EliteBook 820 G2(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="67"> B01H2FKH94 EliteBook 840(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="70"> B01M0BTKYE EliteBook 8470p(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="68"> B074VF7XJX EliteDesk 800 G1(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="94"> B0733P5MKM EliteDesk 800 G1(Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="95"> B07F9X6WDQ EliteDesk 800 G1(Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="97"> B07CXTGCL4 EliteDesk 800 G1(Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="98"> B07F16YSRD EliteDesk 800 G1(Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="12"> B01FWJZ60C Latitude E6420(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="71"> B079P8759M Latitude E6420(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="56"> B00VKL0I96 Latitude E6420(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="14"> B01GI6EX4I Latitude E6430(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="15"> B01M293O5P Latitude E6430(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="69"> B01LX4PE0S Latitude E6430(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="5"> B074SHYFV3 Latitude E6440(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="90"> B07C1DK3SS Latitude E6440(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="8"> B07116R1GY Latitude E7240(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="111"> B07116R1GY Latitude E7240(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="112"> B07DW4PS8B Latitude E7250(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="113"> B07GCS55LR Latitude E7250(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="114"> B07FDHGH2R Latitude E7250(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="87"> B0799HPMFT Latitude E7440(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="89"> B071F49Q2P Latitude E7440(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="103"> B076DKFYF9 Latitude E7450(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="91"> B074YJQPNX Optiplex 3010(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="106"> B075WYN1JL Optiplex 3020(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="83"> B01LOSQWBC Optiplex 390(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="120"> B0773X69FQ Optiplex 5040(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="10"> B01HSDJFFM Optiplex 7010(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="34"> B01LZLPO1H Optiplex 7010(Desktop)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="92"> B079QB4P6S Optiplex 7010(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="110"> B073R2DK2C Optiplex 7010(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="73"> B07CTT3LK2 Optiplex 7020(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="99"> B078WGWZ1X Optiplex 9010(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="30"> B07CTSFCRR Optiplex 9020(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="88"> B076X6QRTD Optiplex 9020(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="65"> B01HTVADHW Optiplex 990(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="66"> B076DFQN2P Optiplex 990(Desktop)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="82"> B077MSZ9P3 ProDesk 600 G1(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="100"> B07N1TJZBV ProDesk 600 G2(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="107"> B01C9GV646 Surface Book(Tablet)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="54"> B00QQTDLG4 Surface Pro 2(Tablet)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="28"> B01M29NJHT Surface Pro 3(Tablet)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="39"> B00TUJ7WQW Surface Pro 3(Tablet)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="49"> B00VU1XDDO Surface Pro 3(Tablet)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="62"> B07BFNDPJ5 Surface Pro 4(Tablet)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="108"> B07NJ7P1BS Surface Pro 4(Tablet_Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="85"> 0 Template(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="86"> 0 Template(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="93"> 0 Template(Ultra Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="123"> test test(test)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="72"> B077N73MKG THINKCENTRE M700(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="40"> B01M0XWPI5 ThinkCentre M72e(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="31"> B00FNQMZFY ThinkCentre M73(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="32"> B07798GCN7 ThinkCentre M73(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="46"> B073SJQW4B ThinkCentre M73 (Tiny Desktop)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="43"> B076VTTHSJ ThinkCentre M83 (Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="79"> B0786HSGW7 ThinkCentre M91p(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="84"> B07RRBKVM3 ThinkCentre M91p(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="81"> B078X9G6LB ThinkCentre M92p(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="42"> B076VMW9D6 ThinkCentre M93P(Small Form Factor)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="101"> B07DVLGPS6 ThinkCentre M93p(Tiny Desktop)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="77"> B00W4AMWCI Thinkpad T420(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="1"> B01LXCLDRI Thinkpad T430(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="52"> B07MKR39LS ThinkPad T430S(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="51"> B07CTTW8HR ThinkPad T440(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="55"> B073WVRDJT ThinkPad T440(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="53"> B07HHCGJSY ThinkPad T450S(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="115"> B07CSN5TQN Thinkpad T560(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="45"> B06VSNTJJP ThinkPad X1 Carbon 2nd Gen(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="48"> B07NDYDK7B ThinkPad X1 Carbon 3rd Gen(Notebook)</label><label style="display:block">
							<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="model_list" value="78"> B07CNCD16X Thinkpad X1 Carbon 4th Gen(Notebook)</label>
							<script>
								var marr=[];
								$('.model_list').change(function(){
								marr=[];
								$('.model_list').each(function(){
									if($(this).prop('checked')) marr.push($(this).val());
								});
								$('input[name=applicable_models]').val(marr.join(','));
								});
							</script>
						</div>
					</td>
					<td style="min-width:300px;">Select all applicable Models</td>
				</tr>

				<tr style="background:#fff">
				<td><b>Department</b></td>
				<td><input type="text" name="dept" value="" maxlength="varchar(100)" class="mte_req" id="id_4"></td>
				<td style="min-width:300px;">[dept]</td>
				</tr>

				<tr style="background:#eee">
				<td><b>Price</b></td>
				<td><input type="text" name="price" value="" maxlength="double" class="mte_req" id="id_5"></td>
				<td style="min-width:300px;">[price]</td>
				</tr>

				<tr style="background:#fff">
				<td><b>Vendor</b></td>
				<td><input type="text" name="vendor" value="" maxlength="varchar(200)" class="mte_req" id="id_6"></td>
				<td style="min-width:300px;">[vendor]</td>
				</tr>

				<tr style="background:#eee">
				<td><b>Low Stock</b></td>
				<td><input type="text" name="low_stock" value="" maxlength="int(11)" class="mte_req" id="id_7"></td>
				<td style="min-width:300px;">[low_stock] Threschold at which the notification will be sent</td>
				</tr>

				<tr style="background:#fff">
				<td><b>Reorder Qty</b></td>
				<td><input type="text" name="reorder_qty" value="" maxlength="int(11)" class="mte_req" id="id_8"></td>
				<td style="min-width:300px;">[reorder_qty]</td>
				</tr>

				<tr style="background:#eee">
				<td><b>Delivery Time</b></td>
				<td><input type="text" name="dlv_time" value="" maxlength="varchar(500)" id="dlv_time"></td>
				<td style="min-width:300px;">[dlv_time]</td>
				</tr>

				<tr style="background:#fff">
				<td><b>Bulk Options</b></td>
				<td><textarea name="bulk_options" id="bulk_options"></textarea></td>
				<td style="min-width:300px;">[bulk_options]</td>
				</tr>

				<tr style="background:#eee">
					<td><b>Emails</b></td>
					<td><input type="text" name="emails" value="" maxlength="varchar(500)" id="emails"><label style="display:block">
						<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="email_list" value="richy@itamg.com"> richy@itamg.com</label><label style="display:block">
						<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="email_list" value="randy@itamg.com"> randy@itamg.com</label><label style="display:block">
						<input type="checkbox" style="width: 16px;height: 16px; min-width: 20px;" class="email_list" value="kamal@itamg.com"> kamal@itamg.com</label>
						<script>
							var earr=[];
							$('.email_list').change(function(){
								earr=[];
								$('.email_list').each(function(){
									if($(this).prop('checked')) earr.push($(this).val());
								});
								$('input[name=emails]').val(earr.join(','));
							});
						</script>
					</td>
					<td style="min-width:300px;">Emails for notifications separated by comma</td>
				</tr>

				<tr style="background:#fff">
					<td><b>Subject</b></td>
					<td><input type="text" name="email_subj" value="Running Low On An Item! - Reorder Request" maxlength="varchar(500)" id="email_subj"></td>
					<td style="min-width:300px;"></td>
				</tr>

				<tr style="background:#eee">
					<td><b>Email Template</b></td>
					<td><textarea name="email_tpl" id="email_tpl">Hi, 
						We are running low on item: 
						[item_name] 
						Part Number: [part_num]
						Current Qty: [qty]

						We get this item from Vendor: [vendor]. Suggested Reorder Quantity is: [reorder_qty]. This item usually ships: [dlv_time]. 

						Please Reorder As Soon As Possible. 
					Thanks!</textarea></td>
					<td style="min-width:300px;">You can use variable names listed above</td>
				</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="mte_new_rec" value="1">
	<input type="hidden" name="mte_a" value="save">
</div>
@endsection