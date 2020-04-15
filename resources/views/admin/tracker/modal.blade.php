<div class="modal fade" id="listModal" tabindex="-1" role="dialog" aria-labelledby="listModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" id="addform" action="{{route('tracker')}}">
				<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
                @csrf
                <input type="hidden" name="a" value="add_action"/>
                <div class="modal-header">
                    <h3 class="modal-title" id="listModalLabel">Edit Actions</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="actions">Actions (one per line)</label>
                                <textarea id="actions" required="required" name="actions" rows=10 class="form-control">@php echo implode(PHP_EOL,$actions); @endphp</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
