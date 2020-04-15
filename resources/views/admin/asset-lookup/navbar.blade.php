<nav class="navbar navbar-expand-lg">
	<h3 align="center"><strong>@yield('title')</strong></h3>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li>
				<a class="btn btn-info mx-1" href="{{route('search',['pageaction' => request()->get('pageaction')])}}">Asset lookup</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('recycle.second',['pageaction' => request()->get('pageaction')])}}">Admin Access</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('failedsearch',['pageaction' => request()->get('pageaction')])}}">Failed Search</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('category',['pageaction' => request()->get('pageaction')])}}">Category</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('email',['pageaction' => request()->get('pageaction')])}}">Report Email</a>
			</li>
		</ul>
	</div>
</nav>
	
