<div class="modal fade" id="asinModal" tabindex="-1" role="dialog" aria-labelledby="asinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" id="addform" action="{{route('shipments')}}">
				<input type="hidden" name="pageaction" id="pageaction" value="{{request()->get('pageaction')}}"/>
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title" id="asinModalLabel">Add Items to Shipment</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="page" value="shipments"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="asin">ASIN</label>
                                <input type="text" required="required" id="asin" name="asin" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="asset1">Asset Numbers (one per line)</label>
                                <textarea id="asset1" required="required" name="asset1" rows=10 class="form-control"></textarea>
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
