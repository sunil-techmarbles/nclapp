<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>{{ config('app.name') }} - @yield('title')</title>
	<link href="{{URL('assets/css/bootstrap/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{URL('assets/css/bootstrap/font-awesome.min.css')}}" rel="stylesheet">
	<link href="{{URL('assets/css/style.css')}}" rel="stylesheet">
	<link rel="shortcut icon" href="{{URL('assets/favicon.ico')}}">
</head>
	<body>
		<div id = "main_content">
			@include('layouts.header')
			<div class="container">
				<div id="page-logo">
					<img src="{{URL('assets/images/logo_itamg.png')}}" id="img-logo" alt="ITAMG">
				</div>
				@yield('content')
			</div>
			@include('layouts.footer') 
		<div> 
	</body> 
	<script type="text/javascript" src = "{{URL('assets/js/jquery.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/bootstrap/bootstrap.min.js')}}" ></script>
</html>