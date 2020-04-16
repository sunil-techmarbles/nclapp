@extends('layouts.appadminlayout')
@section('title', 'Gallery')
@section('content')
<div class="container itamg-gallery">
	<div class="row">
		<div class="col-md-12">
			<form method="post" action="{{route('gallery.inventory',['a'=>'add'])}}" enctype="multipart/form-data" id="image-upload">
				@csrf
					<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
					<input type="file" class="dropify" multiple name="invgallery[]" data-allowed-file-extensions="png jpeg jpg" />
					<input type="submit" class="btn btn-primary mt-2 float-right" name="a" value="Upload">
			</form>
			<div class="demo-gallery mt-3">
	            <ul id="lightgallery" class="list-unstyled row">
	            	@foreach($allImages as $key => $value)
		                <li class="col-xs-6 col-sm-4 col-md-3" data-responsive="{{$value['url']}} 375, {{$value['url']}} 480, {{$value['url']}} 800" data-src="{{$value['url']}}" >
		                    <a href="{{$value['url']}}">
		                        <img class="img-responsive" src="{{$value['url']}}" alt="{{$value['name']}}">
		                    </a>
		                </li>
	            	@endforeach
	            </ul>
	        </div>
		</div>
	</div>
</div>
@endsection