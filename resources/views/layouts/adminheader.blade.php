<nav class="navbar navbar-expand-lg navbar-light bg-light">  
	<div class="collapse navbar-collapse" id="navbarSupportedContent"> 
		
		<ul class="navbar-nav mr-auto">
			<li class="nav-item {{ request()->segment(count(request()->segments())) == 'dashboard' ? 'active' : '' }}">
				<a  class="nav-link" href="{{route('dashboard')}}"> Home </a>
			</li>
			
			@if ( $user_role == 'admin' ) 
				<li class="nav-item {{ request()->segment(count(request()->segments())) == 'users' ? 'active' : '' }}">
					<a class="nav-link" href="{{route('users')}}"> Users </a> 
				</li>

			@if ( request()->segment(count(request()->segments())) == 'refurb' ) 
				<li class="nav-item">
					<a class="nav-link" href="{{route('get.coa.report')}}"> COA Report </a>  
				</li>

				<li class="nav-item">
					<a class="nav-link" href="{{route('get.issue.report')}}"> Issues Report </a> 
				</li> 
			@endif

			@if ( request()->segment(count(request()->segments())) == 'supplies' ) 
				<li class="nav-item">
					<a class="nav-link" href="{{route('asin')}}"> ASIN Database </a>  
				</li>

				<li class="nav-item">
					<a class="nav-link" href="{{route('part.lookup')}}"> Parts Lookup </a> 
				</li> 

				<li class="nav-item">
					<a class="nav-link" href="{{route('sessions')}}"> Sessions </a> 
				</li> 
			@endif

			@if ( request()->segment(count(request()->segments())) == 'asin' ) 
				<li class="nav-item">
					<a class="nav-link" href="{{route('supplies')}}"> Supplies </a> 
				</li> 

				<li class="nav-item">
					<a class="nav-link" href="{{route('part.lookup')}}"> Parts Lookup </a> 
				</li> 
			@endif 

			@if ( request()->segment(count(request()->segments())) == 'shipments' ) 
				<li class="nav-item">
					<a class="nav-link" href="{{route('asin')}}"> ASIN Database </a>  
				</li>

				<li class="nav-item">
					<a class="nav-link" href="{{route('supplies')}}"> Supplies </a> 
				</li> 

				<li class="nav-item">
					<a class="nav-link" href="{{route('sessions')}}"> Sessions </a> 
				</li>  

				<li class="nav-item">
					<a class="nav-link" href=""> Inbound </a> 
				</li> 
			@endif 

			@if ( request()->segment(count(request()->segments())) == 'sessions' || request()->segment(2) == 'asinparts' || request()->segment(count(request()->segments())) == 'partlookup'  ) 
				<li class="nav-item">
					<a class="nav-link" href="{{route('asin')}}"> ASIN Database </a>  
				</li>

				<li class="nav-item">
					<a class="nav-link" href="{{route('supplies')}}"> Supplies </a> 
				</li> 
			@endif 

			@if ( request()->segment(count(request()->segments())) == 'audit' ) 
				<li class="nav-item">
					<a class="nav-link" href="{{route('sessions')}}"> Sessions </a> 
				</li>  

				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="modal" data-target="#pnModal">Add Part Number</a><br/>
				</li>   
			@endif 
			
		@endif

		</ul> 
		<a class="nav-link" href="{{route('logout')}}"> Logout </a>   
	</div>
</nav>