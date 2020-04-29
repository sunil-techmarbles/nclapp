<div class="modal fade" id="brModal" tabindex="-1" role="dialog" aria-labelledby="brModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" id="bulk-addform" enctype="multipart/form-data" action="{{route('inventory')}}">
				<input type="hidden" name="pageaction" id="pageaction1" value="{{request()->get('pageaction')}}"/>
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title" id="brModalLabel">Bulk Remove Items</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="bulk_remove" value="1"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="datafile">Data File</label>
                                <input type="file" id="datafile" name="datafile" class="form-control"/>
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
<div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="priceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" id="addform" enctype="multipart/form-data" action="{{route('inventory')}}">
				<input type="hidden" name="pageaction" id="pageaction2" value="{{request()->get('pageaction')}}"/>
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title" id="priceModalLabel">Set Custom Price</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="set_price" id="set_price" value=""/>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="setprice">Enter Price</label>
                                <input type="number" step="0.01" id="setprice" name="setprice" class="form-control"/>
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
<div id="uhint">
    <div class="close-link">
        <span style="cursor:pointer; font-weight: bold;" onclick="$('#uhint').hide()">[X]</span>
    </div>
    <div id="hints">&nbsp;</div>
</div>