<nav class="navbar navbar-expand-lg">
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li>
				<a class="btn btn-info mx-1" href="{{route('search')}}">Front Search</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('failedsearch')}}">Failed Search</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('category')}}">Category</a>
			</li>
			<li>
				<a class="btn btn-info mx-1 add_new_entry" href="javascript:void(0)">Add New Entry</a>
			</li>
			<li>
				<a class="btn btn-info mx-1 upload_data_from_files" href="javascript:void(0)">Upload CSV or XLS</a>
			</li>
			<li>
				<a class="btn btn-info mx-1" href="{{route('email')}}">Report Email</a>
			</li>
		</ul>
	</div>
</nav>