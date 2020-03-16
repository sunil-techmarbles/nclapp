<!DOCTYPE html> 
<html lang="en"> 
	<head> 
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<title>
			{{ config('app.name') }} - @yield('title')
		</title>
		<link href="{{URL('assets/css/bootstrap/bootstrap.min.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/bootstrap/font-awesome.min.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/admin-style.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/dataTables/jquery.dataTables.min.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/datepicker/datepicker.min.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/datepicker/daterangepicker.css')}}" rel="stylesheet"> 

		<link rel="shortcut icon" href="{{URL('assets/favicon.ico')}}">
	</head>   
	<style type="text/css">
		.loader {
			display: none;
			position: fixed;
			left: 50%;
			top: 50%;
			width: 100%;
			height: 100%;
			opacity: 1;
			z-index: 9999;
		}
		body.loader-opacity{
			opacity: 0.5;
		}
	</style>
	<body>
		<div class="loader">
			<img src="{{URL('assets/images/loader.gif')}}">
		</div>
		<div id="main_content">
			<div class="container">
				@include('layouts.adminheader')
				<div id="page-logo">
					<a href="{{URL('/')}}">
						<img src="{{URL('assets/images/logo_itamg.png')}}" id="img-logo" alt="ITAMG">
					</a>
				</div>
				@include('admin.message')
				@yield('content')
			</div>
			@include('layouts.adminfooter') 
		</div>
	</body>     
	<script type="text/javascript" src = "{{URL('assets/js/jquery.min.js')}}" ></script> 
	<script type="text/javascript" src = "{{URL('assets/js/datepicker/moment.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/datepicker/datepicker.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/bootstrap/bootstrap-combobox.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/datepicker/daterangepicker.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/jquery.validate.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/bootstrap/bootstrap.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/dataTables/jquery.dataTables.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/JsBarcode/JsBarcode.all.min.js')}}" ></script> 
	<script type="text/javascript" src = "{{URL('assets/js/sweetAlert/sweetalert.min.js')}}"></script>  
	<script type="text/javascript" src = "{{URL('assets/js/main.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/refurb.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/audit.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/inventory.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/custom.js')}}" ></script>  
</html>