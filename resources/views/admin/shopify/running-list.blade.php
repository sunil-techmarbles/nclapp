@extends('layouts.appadminlayout')
@section('title', 'Running List')
@section('content')
<div class="container">
    @if (!empty($runningList))
	<div class="col-12 mb-3">
		<h3>
			<span class="float-left">Running List</span>
			<span class="float-right">UPC Remaining: @if(is_array($upcCount)) {{@$upcCount[0]["count"]}} @endif</span>
		</h3>
	</div>
	<div class="row mx-0">
		<a class="btn btn-info ml-2 float-right" href="{{route('runninglist.csv',['csv' => '1'])}}">Export</a>
		<button type="button" class="btn btn-info ml-2 float-right" id="sync-all-to-shopify">Sync to Shopify</button>
		<input type="hidden" name="reunlistsyns" value="true">
	</div>
        <table class="table table-striped table-condensed table-hover">
            <tr>
                <th><input type="checkbox" class="all_asin_sync" name="all_asin_sync"></th>
                <th>ASIN</th>
                <th>Model</th>
                <th>Form Factor</th>
                <th>CPU</th>
                <th>Price</th>
                <th>Asset</th>
                <th>Added On</th>
                <th>Count</th>
                <th>Update to Shopify</th>
                <th>Images</th>
                <th>Shopify Product ID</th>
                <th>Price Diff on Shopify </th>
            </tr>
            @foreach ($runningList as $i)
                <tr style="font-weight: bold">
                    <td>
                        @if (empty($i["shopify_product_id"]))
                            <input id="cb-select-{{$i["asin"]}}" type="checkbox" name="sync-all-ids[]" value="{{$i["asin"]}}">
                        @endif
                    </td>
                    <td><a href="javascript:void(0)" onclick="$('.asin{{$i["aid"]}}').toggle()">{{$i["asin"]}}</a></td>
                    <td>{{$i["model"]}}</td>
                    <td>{{$i["form_factor"]}}</td>
                    <td>{{$i["cpu_core"]}} {{$i["cpu_model"]}} CPU @ <?= htmlspecialchars($i["cpu_speed"]) ?></td>
                    <td>{{$i["price"]}}</td>
                    <td colspan=2>&nbsp;</td>
                    <td>{{$i["cnt"]}}</td>
                    <td>
                        @if ($i["shopify_product_id"])
                            <button class="btn btn-link sync-to-shopify" data-asin="{{$i["asin"]}}">Update</button>
                        @endif
                    </td>
                    <td>
                        @php
                        $allImages = glob($asinImages.'/'. $i["asin"] . '*');
                        if (empty($allImages)) { @endphp
                            N/A
                        @php } else { @endphp
                            Available
                        @php } @endphp
                    </td>
                    <td>{{$i["shopify_product_id"]}}</td>
                    <td style="text-align: center;">
                        @if ($i["shopify_product_id"])
                            @if ($i["priceData"] && $i["priceData"]['diffrence'] != 0)
                                Shopify Price: $ {{$i["priceData"]['shopify_price']}} <br>
                                Final Price: $  {{$i["priceData"]['final_price']}} <br>
                                Diffrence: $  {{$i["priceData"]['diffrence']}} <br>
                                <button class="btn btn-link update-price-to-shopify" data-asin="{{$i["asin"]}}">Update Price</button>
                            @endif
                        @endif
                    </td>
                </tr>
                @foreach ($i['items'] as $a)
                    <tr style="display: none;" class="asin{{$i["aid"] }}">
                        <td>{{ $a["asin"] }}</td>
                        <td>{{ $a["model"] }}</td>
                        <td>{{ $a["form_factor"] }}</td>
                        <td>{{ $a["cpu_core"]}} {{$a["cpu_model"] }} CPU @ {{htmlspecialchars($a["cpu_speed"])}}</td>
                        <td>{{ number_format($a["price"], 2) }}</td>
                        <td>{{ $a["asset"] }}</td>
                        <td>{{ $a["added_on"] }}</td>
                        <td><a href="index.php?page=runlist&remove=$a["asset"] &t=time() "><span class="fa fa-trash"></span></a></td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    @endif
    <div style="text-align: right">
        <b>Total Count: {{$tcnt}}</b>
    </div>
</div>
@endsection
