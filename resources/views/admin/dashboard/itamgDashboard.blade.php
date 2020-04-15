@extends('layouts.appadminlayout')
@section('title', 'Dashboard')
@section('content')
<div class="abs" style="text-align: center;">
    <div style="text-align: center;font-size: 30px;font-weight: bold;color:#107878;">
        WE’RE SAVING THE PLANET
    </div>
    <div style="text-align: center;font-size: 50px;font-weight: bold;color:#139999">
        ONE COMPUTER AT A TIME
    </div>
    <div class="pt-2" style="max-width: 700px;margin: auto;">
        <div class="menu-item">
            <a href="{{route('audit',['pageaction' => $action])}}"><span class="fa fa-refresh"></span></a>
            <a href="{{route('audit',['pageaction' => $action])}}">Audit</a>
        </div>  
        <!-- <div class="menu-item">
            <a href="{{route('sessions',['pageaction' => $action])}}"><span class="fa fa-tasks"></span></a>
            <a href="{{route('sessions',['pageaction' => $action])}}">Sessions</a>
        </div>  -->
        <div class="menu-item">
            <a href="{{route('import',['pageaction' => $action])}}"><span class="fa fa-download"></span></a>
            <a href="{{route('import',['pageaction' => $action])}}">Import</a>
        </div>
        <div class="menu-item">
            <a href="{{route('recycle.first',['pageaction' => $action])}}"><span class="fa fa-refresh"></span></a>
            <a href="{{route('recycle.first',['pageaction' => $action])}}">Recycle Trailer</a>
        </div>
         <div class="menu-item">
            <a href="{{route('search',['pageaction' => $action])}}"><span class="fa fa-refresh"></span></a>
            <a href="{{route('search',['pageaction' => $action])}}">Asset Lookup</a>
        </div>
        <div class="menu-item">
             <a href="{{route('wipereport',['pageaction' => $action])}}"><span class="fa fa-file-excel-o"></span></a>
            <a href="{{route('wipereport',['pageaction' => $action])}}">Wipe Report</a>
        </div>
    </div>
</div>
@endsection