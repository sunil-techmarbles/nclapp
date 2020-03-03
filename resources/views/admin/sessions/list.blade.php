@extends('layouts.appadminlayout')
@section('title', 'Session')
@section('content')
<div class="mte_content">
	<div class="container">
		<div id="page-head">
			Sessions
		</div>
		<form method="post" class="form-inline" enctype="multipart/form-data" action="index.php" style="max-height: 250px;overflow: auto;">
			<div style="width:100%; margin-bottom: 10px">
				<input type="hidden" name="page" value="sessions"/>
				<div class="form-group">
					<input class="form-control" type="file" name="bulk_data" id="bulk_data"/>
					<input type="submit" value="Bulk Upload" class="btn btn-warning" name="bulk_upload"/>
				</div>
				<div class="form-group" style="float:right">
					<label for="qty">Session Name:</label>
					<input style="width:160px" class="form-control" type="text" name="session_name" id="session_name"/>
					<button class="btn btn-warning" name="new_session" value="1" type="submit">New Session</button>
				</div>
			</div>
			<table id="sessions" class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Started On</th>
						<th>Closed On</th>
						<th>Status</th>
						<th>Items Count</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($sessions as $p): ?>
						<tr>
							<td><?=$p["id"]?></td>
							<td><a href="index.php?page=sessions&s=<?=$p["id"]?>"><?=$p["name"]?></a></td>
							<td><?=$p["started_on"]?></td>
							<td><?=$p["status"]=='open'?'':$p["updated_on"]?></td>
							<td><?=$p["status"]?></td>
							<td><?=$p["count"]?></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</form>
		<?php if(!empty($items)): ?>
			<h3>Items for session <?=$sess_name?></h3>
			<table id="sessions-asins" class="table">
				<thead>
					<tr>
						<th>ASIN</th>
						<th>Model</th>
						<th>Form Factor</th>
						<th>CPU</th>
						<th>Price</th>
						<th>Count</th>
					</tr>					
				</thead>
				<tbody>	
					<?php foreach($items as $i): ?>
						<tr>
							<td><a href="#" onclick="$('.assets<?=$i['aid']?>').toggle();"><?=$i["asin"]?></a></td>
							<td><?=$i["model"]?></td>
							<td><?=$i["form_factor"]?></td>
							<td><?=$i["cpu_core"]?> <?=$i["cpu_model"]?> CPU @ <?=htmlspecialchars($i["cpu_speed"])?></td>
							<td><?=$i["price"]?></td>
							<td><?=$i["cnt"]?></td>
						</tr>
						<?php if (!empty($assets['asin'.$i['aid']])): ?>
							<tr class="assets<?=$i['aid']?>" style="display: none">
								<td colspan="6">
									<?php if(!empty($assets['asin'.$i['aid']]['active'])): ?>
										<b>Asset Numbers:</b> 
										<?php foreach($assets['asin'.$i['aid']]['active'] as $itm): ?>
											<a href="index.php?page=sessions&s=<?=$sess?>&remove=<?=$itm?>&t=<?=time()?>"><?=$itm?></a>&nbsp;
										<?php endforeach?>
										(click to remove)<br/>
									<?php endif ?>
									<?php if(!empty($assets['asin'.$i['aid']]['removed'])): ?>
										<b>Asset Numbers:</b> 
										<?php foreach($assets['asin'.$i['aid']]['removed'] as $itm): ?>
											<a href="index.php?page=sessions&s=<?=$sess?>&restore=<?=$itm?>&t=<?=time()?>"><?=$itm?></a>&nbsp;
										<?php endforeach?>
										(click to restore)
									<?php endif ?>
								</td>
							</tr>
						<?php endif ?>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
		
		<?php if(!empty($parts)): ?>
			<h3>Required Parts</h3>
			<form method="post" class="form-inline" action="index.php">
				<table class="table table-striped table-condensed table-hover">
					<tr>
						<th>Part Num</th>
						<th>Part Name</th>
						<th>Required Qty</th>
						<th>Available Qty</th>
						<th>Missing</th>
					</tr>
					<?php foreach($parts as $p): ?>
						<tr>
							<td>
								<input type="checkbox" name="ppart[]" checked="checked" id="ppart<?=$p["id"]?>" value="<?=$p["id"]?>">
								<?=$p["part_num"]?>
							</td>
							<td><?=$p["item_name"]?></td>
							<td><?=$p["required_qty"]?></td>
							<td><?=$p["qty"]?></td>
							<td><?=$p["missing"]>0?$p["missing"]:'&nbsp;'?></td>
						</tr>
					<?php endforeach ?>
				</table>
				<div style="text-align: right; margin-bottom: 10px">
					<input type="hidden" name="page" value="sessions"/>
					<input type="hidden" name="s" value="<?=$sess?>"/>
					<div class="form-group">
						<button class="btn btn-danger" name="withdraw" value="1" type="submit">Withdraw</button>
					</div>
					<?php if($miss>0): ?>
						<a style="float:right" href="index.php?page=sessions&s=<?=$sess?>&reorder=1&t=<?=time()?>" onclick="$(this).hide()" class="btn btn-warning">Reorder</a>
					<?php endif ?>
				</div>
			</form>
			<div style="margin-bottom: 10px">
			</div>
		<?php endif ?>
	</div>
</div>
@endsection