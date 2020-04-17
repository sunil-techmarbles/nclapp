@extends('layouts.appadminlayout')
@section('title', 'Recycle settings')
@section('content')
<div class="container">
	<div id="page-head">Recycle Settings</div>
    <form action = "{{route('recycle.settings')}}" method="post">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="Ship-From-Address">Ship From Address:</label>
                        <textarea required="required" name="shipFromAddress" rows=5 cols=10 class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="Ship-To-Address">Ship To Address:</label>
                        <textarea required="required" name="shipToAddress" rows=5 cols=10 class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="Ship-From-Contact">Ship From Contact:</label>
                        <textarea required="required" name="shipFromContact" rows=3 cols=10 class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="Ship-To-Contact">Ship To Contact:</label>
                        <textarea required="required" name="shipToContact" rows=3 cols=10 class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="recycleSetting" value = '1'>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>
@endsection
