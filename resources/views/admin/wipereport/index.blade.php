@extends('layouts.appadminlayout')
@section('title', 'Wipe Report')
@section('content')
<div class="container">
    <div id="page-head" class="noprint">
        Wipe Report
    </div>
    <form method="post" id="search-wipe-form" autocomplete="off" action="{{route('exportwipereportfiles')}}">
        @csrf
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
    </form>
</div>
@endsection