<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			@if(request()->segment(count(request()->segments())) != 'dashboard')
				<li class="nav-item {{request()->segment(count(request()->segments())) == 'dashboard' ? 'active' : ''}}">
					<a  class="nav-link" href="{{route('dashboard')}}">Home</a>
				</li>
			@endif
			@if($user_role == 'admin')
				@if(request()->segment(count(request()->segments())) == 'audit')
					@if(request()->get('edit'))
					<li class="nav-item">
						<a class="nav-link" href="{{route('audit',['pageaction' => request()->get('pageaction')])}}">Main Form</a>
					</li>
					@else
					<li class="nav-item">
						<a class="nav-link" href="{{route('audit',['pageaction' => request()->get('pageaction'), 'edit' => 1])}}">Search & Edit</a>
					</li>
					@endif
				@endif
				@if(request()->segment(count(request()->segments())) == 'dashboard' || request()->segment(count(request()->segments())) == 'refurbconnectdashboard' || request()->segment(count(request()->segments())) == 'itamgdashboard')
					<li class="nav-item {{ request()->segment(count(request()->segments())) == 'users' ? 'active' : '' }}">
						<a class="nav-link" href="{{route('users',['pageaction' => request()->get('pageaction')])}}">Users</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{route('manage.emails',['pageaction' => request()->get('pageaction')])}}">Manage Emails</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{route('message.log',['pageaction' => request()->get('pageaction')])}}">Message Logs</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'runninglist' || request()->segment(count(request()->segments())) == 'recycle')
					<li class="nav-item {{ request()->segment(count(request()->segments())) == 'users' ? 'active' : '' }}">
						<a class="nav-link" href="{{route('inventory',['pageaction' => request()->get('pageaction')])}}">New List</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'inventory')
					<li class="nav-item {{ request()->segment(count(request()->segments())) == 'users' ? 'active' : '' }}">
						<a class="nav-link" href="{{route('running.list',['pageaction' => request()->get('pageaction')])}}">Running List</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{route('gallery.inventory',['pageaction' => request()->get('pageaction')])}}">Gallery</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'refurb')
					<li class="nav-item">
						<a class="nav-link" href="{{route('get.coa.report',['pageaction' => request()->get('pageaction')])}}">COA Report</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{route('get.issue.report',['pageaction' => request()->get('pageaction')])}}">Issues Report</a>
					</li>
				@endif
				@if( request()->segment(count(request()->segments())) == 'supplies' || request()->segment(count(request()->segments())) == 'shipments' || request()->segment(count(request()->segments())) == 'sessions' || request()->segment(2) == 'asinparts' || request()->segment(count(request()->segments())) == 'partlookup' || request()->segment(count(request()->segments())) == 'wipereport' || request()->segment(count(request()->segments())) == 'inventory' || request()->segment(count(request()->segments())) == 'recycle' )
					<li class="nav-item">
						<a class="nav-link" href="{{route('asin',['pageaction' => request()->get('pageaction')])}}">ASIN Database</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'supplies' || request()->segment(count(request()->segments())) == 'asin')
					<li class="nav-item">
						<a class="nav-link" href="{{route('part.lookup',['pageaction' => request()->get('pageaction')])}}">Parts Lookup</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'supplies' || request()->segment(count(request()->segments())) == 'shipments' || request()->segment(count(request()->segments())) == 'audit' || request()->segment(count(request()->segments())) == 'packages' || request()->segment(count(request()->segments())) == 'wipereport' || request()->segment(count(request()->segments())) == 'inventory' || request()->segment(count(request()->segments())) == 'recycle')
					<li class="nav-item">
						<a class="nav-link" href="{{route('sessions',['pageaction' => request()->get('pageaction')])}}">Sessions</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'asin' || request()->segment(count(request()->segments())) == 'shipments' || request()->segment(count(request()->segments())) == 'sessions' || request()->segment(2) == 'asinparts' || request()->segment(count(request()->segments())) == 'partlookup' || request()->segment(count(request()->segments())) == 'wipereport' || request()->segment(count(request()->segments())) == 'inventory' || request()->segment(count(request()->segments())) == 'recycle' )
					<li class="nav-item">
						<a class="nav-link" href="{{route('supplies',['pageaction' => request()->get('pageaction')])}}">Supply</a>
					</li>
				@endif
				@if(request()->segment(count(request()->segments())) == 'audit')
					<li class="nav-item">
						<a class="nav-link" href="javascript:Void(0)" data-toggle="modal" data-target="#pnModal">Add Part Number</a>
					</li>
				@endif
				@if ( request()->segment(count(request()->segments())) == 'packages' )
					<li class="nav-item">
						<a class="nav-link" href="{{route('shipments',['pageaction' => request()->get('pageaction')])}}">Outbound</a>
					</li>
				@endif
				@if ( request()->segment(count(request()->segments())) == 'recycle' )
					<li class="nav-item">
						<a class="nav-link" href="{{route('recycle.settings',['pageaction' => request()->get('pageaction')])}}">Recycle Edit Settings </a>
					</li>
				@endif
				@if ( request()->segment(count(request()->segments())) == 'recyclesettings' )
					<li class="nav-item">
						<a class="nav-link" href="{{route('recycle.first',['pageaction' => request()->get('pageaction')])}}">Recycle</a>
					</li>
				@endif
				@if ( request()->segment(count(request()->segments())) == 'tracker' )
					<!-- @if(in_array($currentUser, config('constants.adminUsers'))) -->
					<!-- @endif -->
					@if ( request()->get('p') == 'report' )
						<li class="nav-item">
							<a class="nav-link" href="{{route('tracker',['pageaction' => request()->get('pageaction')])}}">Tracker</a>
						</li>
					@else
						<li class="nav-item">
							<a class="nav-link" href="{{route('tracker',['pageaction' => request()->get('pageaction'),'p' => 'report'])}}">Report</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="javascript: void(0)" onclick="$('#listModal').modal('show')">Actions</a>
						</li>
					@endif
				@endif
				@if ( request()->segment(count(request()->segments())) == 'wipereportfilescount' )
					<li class="nav-item">
						<a class="nav-link" href="{{route('wipereport',['pageaction' => request()->get('pageaction')])}}">Wipe Report</a>
					</li>
				@endif
			@endif
		</ul>
		<ul class="navbar-nav ml-auto">
			<li class="nav-item dropdown float-right">
				<a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<strong>{{$currentUser}}</strong>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="{{route('edit.profile')}}">Profile</a>
					<a class="dropdown-item" href="{{route('change.passowrd',['t'=>1])}}">Change Password</a>
					<a class="dropdown-item" href="{{route('logout')}}">Logout</a>
				</div>
			</li>
		</ul>
	</div>
</nav>