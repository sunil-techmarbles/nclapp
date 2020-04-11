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
            <a href="{{route('refurb')}}"><span class="fa fa-refresh"></span></a>
            <a href="{{route('refurb')}}">Refurb</a>
        </div>  
        <div class="menu-item">
            <a href="{{route('supplies')}}"><span class="fa fa-wrench"></span></a>
            <a href="{{route('supplies')}}">Supplies</a>
        </div> 
        <div class="menu-item">
            <a href="{{route('asin')}}"><span class="fa fa-hdd-o"></span></a>
            <a href="{{route('asin')}}">ASINs</a>
        </div>
        <div class="menu-item">
            <a href="{{route('shipments')}}"><span class="fa fa-sign-out"></span></a>
            <a href="{{route('shipments')}}">Outbound</a>
        </div>
        <div class="menu-item">
            <a href="{{route('sessions')}}"><span class="fa fa-tasks"></span></a>
            <a href="{{route('sessions')}}">Sessions</a>
        </div> 
        <div class="menu-item">
            <a href="{{route('inventory')}}"><span class="fa fa-shopping-cart"></span></a>
            <a href="{{route('inventory')}}">Inventory</a>
        </div>
        <div class="menu-item">
            <a href="{{route('tracker')}}"><span class="fa fa-hourglass"></span></a>
            <a href="{{route('tracker')}}">Time Tracker</a>
        </div>
        <div class="menu-item">
            <a href="{{route('import')}}"><span class="fa fa-download"></span></a>
            <a href="{{route('import')}}">Import</a>
        </div>
        <div class="menu-item">
            <a href=""><span class="fa fa-sign-in"></span></a>
            <a href="">Asin Inventory</a>
        </div>
    </div>
</div>
@endsection