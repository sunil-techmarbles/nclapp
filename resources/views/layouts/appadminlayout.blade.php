<!DOCTYPE html> 
<html lang="en"> 
	<head> 
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>
			{{ config('app.name') }} - @yield('title')
		</title>
		<link href="{{URL('assets/css/bootstrap/bootstrap.min.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/bootstrap/font-awesome.min.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/admin-style.css')}}" rel="stylesheet">
		<link href="{{URL('assets/css/jquery.dataTables.min.css')}}" rel="stylesheet">
		<link rel="shortcut icon" href="{{URL('assets/favicon.ico')}}">
	</head>   

	<body> 
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
		<div> 
	</body>     
	<script type="text/javascript" src = "{{URL('assets/js/bootstrap/jquery.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/jquery.validate.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/jquery.dataTables.min.js')}}" ></script> 
	<script type="text/javascript" src="{{URL('assets/js/sweetAlert/sweetalert.min.js')}}"></script>  
	<script type="text/javascript" src = "{{URL('assets/js/admin-custom.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/custom.js')}}" ></script> 
</html>  