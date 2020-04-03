@extends('layouts.appadminlayout')
@section('title', 'Wipe Report Count')
@section('content')
<div class="container">
	<div id="page-head" class="noprint">
		Wipe Report
	</div>
	 <div class="row">
        <div class="col-sm-12" style="text-align: right">
            <form method="post" class="form-inline" id="statsform" action="index.php">
                @csrf
                <input type="text" style="display: inline-block;width:200px;margin-right: 15px;" class="daterange form-control" id="dates" name="dates" value="04/02/2020 - 04/02/2020">
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <h4 style="margin: 5px">Wipe Data ( PDFs ) </h4>
            <ul class="dbchart act-chart">
                <li data-data="">
                    Total No of files in Wipe-data folder:
                </li>
            </ul>
        </div>
        <div class="col-sm-4">
            <h4 style="margin: 5px">BIOS Data</h4>
            <ul class="dbchart dact-chart">
                <li data-data="">
                    Total No of files in bios-data folder:
                </li>
            </ul>
        </div>
        <div class="col-sm-4">
            <h4 style="margin: 5px">Blancco pdf_data</h4>
            <ul class="dbchart aact-chart">
                <li data-data="">
                    Total No of files in blancco-data folder:
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <h4>Total files</h4>
            <p><b></b></p>
        </div>
    </div>
</div>
@endsection