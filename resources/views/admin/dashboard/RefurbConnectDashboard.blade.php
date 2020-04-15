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
            <a href="{{route('refurb',['pageaction' => $action])}}"><span class="fa fa-refresh"></span></a>
            <a href="{{route('refurb',['pageaction' => $action])}}">Refurb</a>
        </div>  
        <div class="menu-item">
            <a href="{{route('supplies',['pageaction' => $action])}}"><span class="fa fa-wrench"></span></a>
            <a href="{{route('supplies',['pageaction' => $action])}}">Supplies</a>
        </div> 
        <div class="menu-item">
            <a href="{{route('asin',['pageaction' => $action])}}"><span class="fa fa-hdd-o"></span></a>
            <a href="{{route('asin',['pageaction' => $action])}}">ASINs</a>
        </div>
        <div class="menu-item">
            <a href="{{route('shipments',['pageaction' => $action])}}"><span class="fa fa-sign-out"></span></a>
            <a href="{{route('shipments',['pageaction' => $action])}}">Outbound</a>
        </div>
        <div class="menu-item">
            <a href="{{route('sessions',['pageaction' => $action])}}"><span class="fa fa-tasks"></span></a>
            <a href="{{route('sessions',['pageaction' => $action])}}">Sessions</a>
        </div> 
        <div class="menu-item">
            <a href="{{route('inventory',['pageaction' => $action])}}"><span class="fa fa-shopping-cart"></span></a>
            <a href="{{route('inventory',['pageaction' => $action])}}">Inventory</a>
        </div>
        <div class="menu-item">
            <a href="{{route('tracker',['pageaction' => $action])}}"><span class="fa fa-hourglass"></span></a>
            <a href="{{route('tracker',['pageaction' => $action])}}">Time Tracker</a>
        </div>
        <div class="menu-item">
            <a href="{{route('import',['pageaction' => $action])}}"><span class="fa fa-download"></span></a>
            <a href="{{route('import',['pageaction' => $action])}}">Import</a>
        </div>
        <div class="menu-item">
            <a href="{{route('asininventry',['pageaction' => $action])}}"><span class="fa fa-sign-in"></span></a>
            <a href="{{route('asininventry',['pageaction' => $action])}}">Asin Inventory</a>
        </div>
    </div>
</div>
@endsection