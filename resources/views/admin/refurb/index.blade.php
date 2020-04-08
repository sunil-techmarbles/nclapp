@extends('layouts.appadminlayout')
@section('title', 'Refurb')
@section('content')
<div class="mte_content">
	<div class="container">
		<div id="page-head" class="noprint">
			Refurb
		</div>
		<!-- <form method="post" id="main-form" autocomplete="off"> -->
		<div class="noprint" style="text-align: center;">
			<div class='formitem'>
				<div class='form-group'>
					<label class='ttl' for='asset_num'>Asset Number <span class='req'>*</span></label><br/>
					<input type='text' value='' class='form-control' id='asset_num' name='asset_num' required='true'/>
				</div>
			</div>
		</div>
		<!-- </form> -->
		<div id="edit_form" class="noprint" style="padding-bottom: 100px">
			<div id="printsaved" style="display: none;">
				<button class="btn btn-primary" style="float: right" onclick="printSaved()" type="button">Print Saved Label</button>
			</div>
			<div id="comp_form" style="display: none;margin-right: 5px;margin-left: 5px"></div>
			<div class="tab-content" id="tab-content"></div>
			<div id="printbtn" style="display: none;">
				<button class="btn btn-default border border-primary" onclick="editProps()" type="button">Edit</button>
				<button class="btn btn-primary" style="float: right" onclick="printLabel()" type="button">Print</button>
			</div>
		</div>
		<div id="ws_form" class="noprint clearfix">
			<button type="button" id="btn-ws" class="btn btn-warning pull-right" onclick="openWS()">Bulk Wholesale</button>
		</div>	
		<div id="asset_form" style="display:none;position: relative;">
			<table class="table table-bordered">
				<tr>
					<td colspan="2">Printed by <?=@$usrname?><br/><?=date('m/d/Y')?><input type="hidden" id="asin_id" value="0"/></td>
					<td align="center" colspan="2">Asset Number<br/><svg id="product_id"></svg></td>
					<td align="center" colspan="2">Serial Number<br/><svg id="product_sern"></svg></td>
					<td align="center" colspan="2"><span id="asinttl">ASIN</span><br/><svg id="product_asin"></svg></td>
				</tr>
				<tr>
					<td colspan="2">Model: <span id="f_model"></span></td>
					<td colspan="2"><span id="f_cpu"></span></td>
					<td colspan="2">RAM: <span id="f_ram"></span></td>
					<td colspan="2">HDD: <span id="f_hdd"></span></td>
				</tr>
				<tr>
					<td colspan="3">GRADE: <span style="font-size: 20px; line-height: 20px" id="product_grade">A</span></td>
					<td colspan="2"><span id="f_technology"></span> <span class="cspecs c_webcam"><span class="f_opt" id="f_webcam">N/A</span></span></td>
					<td colspan="3"><span id="f_os_label"></span></td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<th style="width: 15%">Initials</th>
					<th colspan="7">Next Process</th>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Condition</td>
					<td colspan="6">
						<div class="frmrow c_screen"><b>Screen:</b> <span class="f_opt" id="f_Screen">N/A</span></div>
						<div class="frmrow c_case"><b>Case:</b> <span class="f_opt" id="f_Case">N/A</span></div>
						<div class="frmrow c_io"><b>Input/Output:</b> <span class="f_opt" id="f_Input_Output">N/A</span></div>
						<div class="frmrow c_functional"><b>Functional:</b> <span class="f_opt" id="f_Functional">N/A</span></div>
						<div class="frmrow c_missing"><b>Missing:</b> <span class="f_opt" id="f_Missing">N/A</span></div>
						<div class="frmrow c_other"><b>Other:</b> <span class="f_opt" id="f_Other">N/A</span></div>
						<div class="frmrow c_notes"><b>Notes:</b> <span class="f_opt" id="f_Notes">N/A</span></div>
					</td>
				</tr>
				<tr class="c_swap">
					<td rowspan="2">&nbsp;</td>
					<td rowspan="2">Replaceable parts</td>
					<td align="center">Lid Cover</td>
					<td align="center">LCD</td>
					<td align="center">Rear Cover</td>
					<td align="center">Keyboard</td>
					<td align="center">Palm Rest</td>
					<td align="center">Rubber Feet</td>
				</tr>
				<tr class="c_swap">
					<td class="replace_parts" align="center" data-cbref="Top Cover">&nbsp;</td>
					<td class="replace_parts" align="center" data-cbref="Screen">&nbsp;</td>
					<td class="replace_parts" align="center" data-cbref="Back Cover">&nbsp;</td>
					<td class="replace_parts" align="center" data-cbref="Keyboard Cover">&nbsp;</td>
					<td class="replace_parts" align="center">&nbsp;</td>
					<td class="replace_parts" align="center">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Internal cleaning</td>
					<td colspan="3">Thoroughly Clean and Dust Inside Of Machine</td>
					<td colspan="3">Replace CMOS Battery</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Configure</td>
					<td colspan="3"><span id="f_nram">No changes needed</span></td>
					<td colspan="3"><span id="f_nhdd">No changes needed</span></td>
				</tr>
				
				<tr>
					<td rowspan="4">&nbsp;</td>
					<td rowspan="4">Diagnostic / Testing</td>
					<td align="center">BIOS Flash</td>
					<td align="center">Drivers</td>
					<td align="center">Programs</td>
					<td align="center">TouchPad</td>
					<td align="center">Keyboard</td>
					<td align="center">Battery</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center">Burn</td>
					<td align="center">Screen</td>
					<td align="center">WiFi</td>
					<td align="center">Activation</td>
					<td align="center">Recovery</td>
					<td align="center">Cortana</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Exterior cleaning</td>
					<td colspan="6">Thoroughly Clean Exterior or Machine and Remove All Labels and Stickers</td>
				</tr>
				<tr class="skin">
					<td>&nbsp;</td>
					<td>Skin</td>
					<td colspan="6">Requires Skin</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Scan & COA</td>
					<td colspan="3">______ Scan</td>
					<td colspan="3">______ COA | WIN8 ______</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Final Check and Shrink/Wrap</td>
					<td colspan="3">______ Final Check</td>
					<td colspan="3">Shrink Wrap ______</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Accessories</td>
					<td colspan="6">
						
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Box Description</td>
					<td colspan="6">&nbsp;</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<td style="width: 70%">Approved By:</td>
					<td>Box Date</td>
				</tr>
				<tr>
					<td colspan="2" style="font-weight: bold">
						If the unit fails any of the steps above please note the reason in "Reported Issues" field and add it to the Wholesale Pallet.
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<td style="width: 30%">Rejected By:</td>
					<td id="f_rep_issues">Reported Issues</td>
				</tr>
			</table>
		</div>
		@include('admin.refurb.modal') 
	</div>
</div>
<script type="text/javascript">
	var process = '<?php echo json_encode($process); ?>';
</script>
@endsection