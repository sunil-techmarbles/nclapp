<h3 align="center"><strong>@yield('title')</strong></h3>
<nav class="navbar navbar-expand-lg">
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">
			<li>
				<a class="btn btn-info btn-xs mx-1" href="{{route('search',['pageaction' => request()->get('pageaction')])}}">Asset lookup</a>
			</li>
			<li>
				<a class="btn btn-info btn-xs mx-1" href="{{route('recycle.second',['pageaction' => request()->get('pageaction')])}}">Admin Access</a>
			</li>
			<li>
				<a class="btn btn-info btn-xs mx-1" href="{{route('failedsearch',['pageaction' => request()->get('pageaction'), 'a' => 1])}}">Failed Search</a>
			</li>
			<li>
				<a class="btn btn-info btn-xs mx-1" href="{{route('category',['pageaction' => request()->get('pageaction'), 'cat' => 1])}}">Category</a>
			</li>
			<li>
				<a class="btn btn-info btn-xs mx-1" href="{{route('email',['pageaction' => request()->get('pageaction'), 'a' => 1])}}">Report Email</a>
			</li>
			@if(!request()->get('a'))
				<li>
					<a class="btn btn-info btn-xs mx-1 add_new_entry" href="javascript:void(0)">Add</a>
				</li>
				<li>
					<a class="btn btn-info btn-xs mx-1 dt-button delete_button" href="javascript:void(0)"><span>Delete Selected</span></a>
				</li>
				@if(!request()->get('cat'))
					<li>
						<a class="btn btn-info btn-xs mx-1 upload_data_from_files" href="javascript:void(0)">Upload CSV or XLS</a>
					</li>
				@endif
			@endif
		</ul>
	</div>
</nav>
	
