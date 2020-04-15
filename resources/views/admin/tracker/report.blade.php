@extends('layouts.appadminlayout')
@section('title', 'Tracker Report')
@section('content')
<div class="container reporttracker">
	<input type="hidden" name="reporttracker" value="report_tracker"/>
	<div id="page-head" class="noprint">Time Tracker Report</div>
	<div class="row">
		<div class="col-sm-12" style="text-align: right">
			<form class="tracker-form" method="get" class="form-inline" id="statsform" action="{{route('tracker')}}">
				<div class="tracker-tab  m-1">
					<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
				<input type="hidden" name="p" value="report"/>
					<select name="user" class="form-control" id="user" onchange="doFilter()">
						<option disabled selected value> Select User</option>
						@foreach($userList as $key => $user)
							<option @if(request()->get('user') == htmlentities($user, ENT_QUOTES)) selected @endif value="{{htmlentities($user, ENT_QUOTES)}}">{{$user}}</option>
						@endforeach
					</select>
				</div>
				<div class="tracker-tab  m-1">
					<select name="activity" class="form-control" id="activity" onchange="doFilter()">
						<option disabled selected value>Select Activity</option>
						@foreach($actionList as $key => $action)
							<option @if(request()->get('activity') == htmlentities($action, ENT_QUOTES)) selected @endif value="{{htmlentities($action, ENT_QUOTES)}}">{{$action}}</option>
						@endforeach
					</select>
				</div>
				<div class="tracker-tab  m-1">
					<input type="text" style="display: inline-block;" class="form-control" id="dates" name="dates" value="{{request()->get('dates')}}" />
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<h4 style="margin: 5px">Actions performed </h4>
			<ul class="dbchart act-chart" style="padding: 5px">
				<?php foreach ($actions as $uname => $data): ?>
					<p style="font-size: 14px;font-weight:bold;margin-top: 10px;margin-bottom: 0"><?= $uname ?></p>
					<?php foreach ($data as $aname => $val): ?>
						<li data-data="<?= $val ?>">
							<?= $aname ?>: <?= number_format($val, 0, ".", ",") ?>
						</li>
					<?php endforeach ?>
				<?php endforeach ?>
			</ul>
		</div>
		<div class="col-sm-4">
			<h4 style="margin: 5px">Hours worked</h4>
			<ul class="dbchart dact-chart" style="padding: 5px">
				<?php foreach ($dactions as $uname => $data): ?>
					<p style="font-size: 14px;font-weight:bold;margin-top: 10px;margin-bottom: 0"><?= $uname ?></p>
					<?php foreach ($data as $aname => $val): ?>
						<li data-data="<?= $val ?>">
							<?= $aname ?>: <?= number_format($val / 3600, 1, ".", ",") ?>
						</li>
					<?php endforeach ?>
				<?php endforeach ?>
			</ul>
		</div>
		<div class="col-sm-4">
			<h4 style="margin: 5px">Avg Min per Activity</h4>
			<ul class="dbchart aact-chart" style="padding: 5px">
				<?php foreach ($dactions as $uname => $data): ?>
					<p style="font-size: 14px;font-weight:bold;margin-top: 10px;margin-bottom: 0"><?= $uname ?></p>
					<?php foreach ($data as $aname => $val): ?>
						<?php $param = round($val / $actions[$uname][$aname]); ?>
						<li data-data="<?= $param ?>">
							<?= $aname ?>: <?= gmdate("i:s", $param); ?>
						</li>
					<?php endforeach ?>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<h4 style="margin: 5px">Total Actions performed</h4>
			<ul class="dbchart usr-chart" style="padding: 5px">
				<?php foreach ($users as $uname => $val): ?>
					<li data-data="<?= $val ?>">
						<?= $uname ?>: <?= number_format($val, 0, ".", ",") ?>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		<div class="col-sm-4">
			<h4 style="margin: 5px">Total Hours worked</h4>
			<ul class="dbchart dusr-chart" style="padding: 5px">
				<?php foreach ($dusers as $uname => $val): ?>
					<li data-data="<?= $val ?>">
						<?= $uname ?>: <?= number_format($val / 3600, 1, ".", ",") ?>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		<div class="col-sm-4">
			<h4 style="margin: 5px">Avg. Min per Activity</h4>
			<ul class="dbchart ausr-chart" style="padding: 5px">
				<?php foreach ($dusers as $uname => $val): ?>
					<?php $param = round($val / $users[$uname]); ?>
					<li data-data="<?= $param ?>">
						<?= $uname ?>: <?= gmdate("i:s", $param) ?>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<h4>Grand Total</h4>
			<p style="font-size: 1.4em"><b><?= number_format($total) ?> actions</b> 
				<!--(<?= number_format($daily, 1) ?>/day, <?= number_format($weekly, 1) ?>/week, <?= number_format($monthly, 1) ?>/month )-->
			</p>
		</div>
		<div class="col-sm-4">
			<h4>&nbsp;</h4>
			<p style="font-size: 1.4em"><b><?= number_format($totalHours, 1) ?> hours</b> 
				<!--(<?= number_format($ddaily, 1) ?>/day, <?= number_format($dweekly, 1) ?>/week, <?= number_format($dmonthly, 1) ?>/month )-->
			</p>
		</div>
	</div>
</div>
@endsection
