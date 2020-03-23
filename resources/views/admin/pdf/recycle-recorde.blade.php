<table cellpadding="0" cellspacing="0" width="700" style="margin: 0 auto;">
	<tr>
		<td style="font-size:16px;text-align: center;"><img src="{{$logo}}" alt="logo" align="center" height="70"></td>
		<td>
			<table style="width: 100%;">
				<tr style="border: 1px solid #ccc; display: block; border-bottom: 0px; border-spacing: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; width:200px; padding: 5px;">Invoice #</td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-spacing: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; width:200px; padding: 5px;">
						Date: {{$closed}}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table style="width: 100%;">
				<tr style="border: 1px solid #ccc; display: block; border-bottom: 0px; border-spacing: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;"><strong>Ship From:</strong></td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-spacing: 0; border-bottom: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;">IT Asset Management Group<br>
						110 Bi County Bld, Suite 106<br>
						Farmingdale, NY, 11735
					</td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-bottom: 0px; border-spacing: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;"><strong>Contact</strong></td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-spacing: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;">Kamal Kaur
						Office: 516-284-8568<br>
						kamal@itamg.com
					</td>
				</tr>
			</table>
		</td>
		<td>
			<table style="width: 100%;">
				<tr style="border: 1px solid #ccc; display: block; border-bottom: 0px; border-spacing: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;"><strong>Ship To:</strong></td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-spacing: 0; border-bottom: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;">EcoTech Management Inc <br>935 Lincoln Avenue <br>Holbrook, NY 11741
					</td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-bottom: 0px; border-spacing: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;"><strong>Contact</strong></td>
				</tr>
				<tr style="border: 1px solid #ccc; display: block; border-spacing: 0; border-right: 0;">
					<td style="font-size:16px;border: 1px solid #ccc; display: block; text-align: center; padding: 5px;">Nat VariscoOffice: <br>631-567-2727
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="700" style="margin: 10px auto 0; border: 1px solid #ccc; border-bottom:0px;">
	<tr style="text-align: center; display:block; width:100%;">
	  <td style="font-size:16px;text-align: center;padding:10px;display:block;"><strong>ITAMG - RECYCLE SHIPMENT BOL</strong></td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="700" style="margin: 0px auto; border: 1px solid #ccc;">
	@foreach ($pdfData as $key => $datas)
		<tr style="background: #f00; display: block; width: 100%;">
			<td style="font-size:16px;padding:5px; text-align: left; width: 100px;">Pallet</th>
			<td style="font-size:16px;padding:5px; text-align: left; width: 200px;">Scrap Category</th>
			<td style="font-size:16px;padding:5px; text-align: left; width: 80px;">Lbs. Gross</th>
			<td style="font-size:16px;padding:5px; text-align: left; width: 80px;">Lbs. Tare</th>
			<td style="font-size:16px;padding:5px; text-align: left; width: 80px;">Price/Lb.</th>
			<td style="font-size:16px;padding:5px; text-align: left; width: 80px;">Total Price</th>
			<td style="font-size:16px;padding:5px; text-align: left; width: 80px;">P/G/I</th>
		</tr>
    	@foreach ($datas as $key => $data)
	        <tr style="display: block; width: 100%;">
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 100px;">{{ $key+1 }}</td>
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 200px;">{{ $data['category'] }}</td>
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 80px;">{{ $data['lgross'] }}</td>
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 80px;">{{ $data['ltare'] }}</td>
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 80px;">{{ $data['price'] }}</td>
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 80px;">{{ $data['total_price'] }}</td>
				<td style="font-size:16px;padding: 5px;border: 1px solid #ccc; width: 80px;">{{ $data['pgi'] }}</td>
			</tr>
		@endforeach
	@endforeach
</table>
<table cellpadding="0" cellspacing="0" width="250" style="margin: 10px 0 0; border: 1px solid #ccc;">
	<tr style="border-bottom: 1px solid #ccc; display: block; width: 100%;">
		<td style="font-size:16px;padding: 5px;width:80px;">Carrier:</td>
		<td style="font-size:16px;padding: 5px;">DPB Trucking</td>
	</tr>
	<tr style=" display: block; width: 100%;">
		<td style="font-size:16px;padding: 5px;width:80px;">Contact</td>
		<td style="font-size:16px;padding: 5px;">631-831-0232</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="700" style="margin: 0 auto;">
	<tr style="display: table-row;">
		<td style="font-size:16px;padding-top: 40px;"><span style="border-top: 1px solid #000; display: block; text-align: center; padding-top: 5px;">Driver Signature</span></td>
		<td style="font-size:16px;padding-top: 40px; float:right;display: table-cell; text-align:right;"><span style="width:170px; border-top: 1px solid #000; display: block; text-align: center; padding-top: 5px;">Date</span></td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="700" style="margin: 0 auto;">
	<tr style="display: table-row;">
		<td style="font-size:16px;padding-top: 40px;"><span style="border-top: 1px solid #000; display: block; text-align: center; padding-top: 5px;">Driver Signature</span></td>
		<td style="font-size:16px;padding-top: 40px; float:right;display: table-cell; text-align:right;"><span style="width:170px; border-top: 1px solid #000; display: block; text-align: center; padding-top: 5px;">Date</span></td>
	</tr>
</table>';