<html>
<head>
	<title>Session Info</title>
	<style>
		table {
			border-spacing: 0;
			border-collapse: collapse;
			border:1px solid #aaa;
		}
		td {
			padding: 5px;
			border:1px solid #aaa;
		}
		th {
			padding: 5px;
			border:1px solid #aaa;
			background-color: #ddd;
		}
	</style>
</head>
<body style="font-family: Arial,Sans-serif">
	<?php if(!empty($summary)): ?>
	<h3>Summary for shipment <?=$name?></h3>
	<table>
		<tr>
			<th>ASIN</th>
			<th>Model</th>
			<th>Form Factor</th>
			<th>CPU</th>
			<th>Price</th>
			<th>Count</th>
		</tr>
		<?php $tqty = 0; ?>
		<?php foreach($summary as $i): ?>
		<?php $tqty += $i["cnt"]; ?>
		<tr>
			<td><?=$i["asin"]?></td>
			<td><?=$i["model"]?></td>
			<td><?=$i["form_factor"]?></td>
			<td><?=$i["cpu_core"]?> <?=$i["cpu_model"]?> CPU @ <?=htmlspecialchars($i["cpu_speed"])?></td>
			<td><?=number_format($i["price"],2)?></td>
			<td><?=$i["cnt"]?></td>
		</tr>
		<?php endforeach ?>
	</table>
	<?php endif ?>
	<p>
		<b>Total Quantity Shipped: <?=$tqty?></b>
	</p>
</body>
</html>