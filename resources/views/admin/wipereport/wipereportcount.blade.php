@extends('layouts.appadminlayout')
@section('title', 'Wipe Report Count')
@section('content')
<div class="container">
	<div id="page-head">
		Wipe Report
	</div>
	 <div class="row">
        <div class="col-sm-12" style="text-align: right">
            <form method="post" class="form-inline" id="wipereportcountform" action="{{route('getwipereportfilescount',['pageaction' => request()->get('pageaction')])}}">
                @csrf
                <input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
                <input type="text" style="width:200px;" class="daterange form-control" id="dates" name="dates" value="{{$dates}}">
            </form>
        </div>
    </div>
    <div class="row wipereportcount-row">
        <div class="col-sm-4">
            <h4 style="margin: 5px">Wipe Data ( PDFs ) </h4>
            <ul class="chart">
                <li>
                    Total No of files in Wipe-data folder: {{$wipePdf}}
                </li>
            </ul>
        </div>
        <div class="col-sm-4">
            <h4 style="margin: 5px">BIOS Data</h4>
            <ul class="chart">
                <li>
                    Total No of files in bios-data folder: {{$biosPdf}}
                </li>
            </ul>
        </div>
        <div class="col-sm-4">
            <h4 style="margin: 5px">Blancco PDFs data</h4>
            <ul class="chart">
                <li data-data="{{$blancooPdf}}">
                    Total No of files in blancco-data folder: {{$blancooPdf}}
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <h4>Total files</h4>
            <p><b>{{$totalFiles}}</b></p>
        </div>
    </div>
</div>
@endsection
