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
            <a href="{{route('dashboard.itamg')}}">
                <img src="{{URL('/assets/images/logo_itamg.png')}}" class="icons" title="itamg">
            </a>
            <a href="{{route('dashboard.itamg')}}">ITAMG</a>
        </div>   
        <div class="menu-item">
            <a href="{{route('dashboard.refurbconnect')}}">
                <img src="{{URL('/assets/images/rc-logo-vertical.png')}}" class="icons" title="refurbconnect">
            </a>
            <a href="{{route('dashboard.refurbconnect')}}">RefurbConnect</a>
        </div>
    </div>
</div>
@endsection