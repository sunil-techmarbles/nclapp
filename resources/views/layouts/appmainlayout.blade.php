<!DOCTYPE html> 
<html lang="en"> 

<head> 
	<link href="{{URL('assets/css/bootstrap/bootstrap.min.css')}}" rel="stylesheet">

	<link href="{{URL('assets/css/style.css')}}" rel="stylesheet"> 
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

	<script type="text/javascript" src = "{{URL('assets/js/bootstrap/jquery.min.js')}}" ></script>
	<script type="text/javascript" src = "{{URL('assets/js/bootstrap/bootstrap.min.js')}}" ></script>

</html>  