<nav class="navbar navbar-expand-lg">
	<h3 align="center"><strong>@yield('title')</strong></h3>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li>
				<a class="btn btn-info mx-1" href="{{route('search')}}">Asset lookup</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('recycle.second')}}">Admin Access</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('failedsearch')}}">Failed Search</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('category')}}">Category</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('email')}}">Report Email</a>
			</li>
		</ul>
	</div>
</nav>
	