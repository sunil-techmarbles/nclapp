@extends('layouts.appadminlayout')
@section('title', 'Wipe Report')
@section('content')
<div class="container">
    <div id="page-head" class="noprint">
        Wipe Report
    </div>
    <ul class="wipeReportNav">
      <li> <a href="{{route('getwipereportfilescount',['pageaction' => request()->get('pageaction')])}}" class="btn btn-info">Reporting</a></li>
   </ul>
        <div class="noprint text-center">
            <div class='formitem'>
                <div class='form-group'>
                    <label class='ttl' for='asset_num'>Lot Number
                        <span class='req'>*(Hit "Enter Key" to fetch files)</span>
                    </label>
                    <input type='text' value='' class='form-control' id='lotNum' name='lotNum'/>
                </div>
            </div>
        </div>
        <div id="wipe-report-result">
        </div>
        <div id="wipe-advance-report-result">
        </div>
</div>
@include('admin.wipereport.modal')
@endsection