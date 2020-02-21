<?php 
	if ( $user = Sentinel::getUser() ) { 
		$slug = Sentinel::getUser()->roles()->first()->slug;  
	?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">  
	
	<div class="collapse navbar-collapse" id="navbarSupportedContent"> 
		
		<ul class="navbar-nav mr-auto">
			<li class="nav-item {{ request()->segment(count(request()->segments())) == 'dashboard' ? 'active' : '' }}">
				<a  class="nav-link" href="{{route('dashboard')}}"> Home </a>
			</li>

			<?php if ( $slug == 'admin') {  ?>
			
			<li class="nav-item {{ request()->segment(count(request()->segments())) == 'users' ? 'active' : '' }}">
				<a class="nav-link" href="{{route('users')}}"> Users </a>
			</li>
			
			<li class="nav-item {{ request()->segment(count(request()->segments())) == 'register' ? 'active' : '' }}">
				<a class="nav-link"  href="{{route('user.register')}}"> Add User </a> 
			</li>

			<?php  } ?>
		</ul> 
		<a class="nav-link" href="{{route('logout')}}"> Logout </a>   
	</div>
</nav>
<?php }  ?>