@extends('layouts.appadminlayout')
@section('title', 'Import')
@section('content')
<div class="container shopify-product-table">
    <div id="page-head" class="noprint">Import</div>
    <form action="{{route('import')}}" method="GET" class="search-table" enctype="multipart/form-data">
        @if (request()->get('model') && empty($shopify_priceData))
            echo '<p class="message"> No Data Found.</p><br>';
        @endif
        <div class="row" style="margin-bottom:5px">
            <div class="col-sm-3">
                <div class="form-group-sm">
                    <input type="text" class="form-control" name="model" placeholder="Model" value="{{request()->get('model')}}" required/>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group-sm">
                    <input type="text" class="form-control" name="form_factor" placeholder="Form Factor" value="{{request()->get('form_factor')}}" required/>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group-sm">
                    <input type="text" class="form-control" name="processor" placeholder="Processor" value="{{request()->get('processor')}}" required/>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group-sm">
                    <input type="text" class="form-control" name="condition" placeholder="Condition" value="{{request()->get('condition')}}" required/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="w-100 float-right">
                <div class="form-group-sm float-right mt-3">
                    <a href="{{route('import')}}" class="btn btn-warning">Reset</a>
                    <input class="btn btn-success" type="submit" value="Search"/>
                </div>
            </div> 
        </div>
        @if(!empty($shopifyPriceData))
            <h2>Final Price : $<span class="show_final_price">{{$shopifyPriceData[0]['Final_Price']}}</span></h2>
            <input class="show_all" type="button" value="Show All Records">
            <input type="hidden" class="final_price" value="{{$shopifyPriceData[0]['Final_Price']}}">
            <select name="properties[Hard Drive]" class="hard_drive">
                <option value="0">-- Choose Hard Drive --</option>
                <option value="0" data-option_value_key="0">320GB SATA</option>
                <option value="20" data-option_value_key="1">500GB SATA [+$20.00]</option>
                <option value="25" data-option_value_key="2">1TB SATA [+$25.00]</option>
                <option value="30" data-option_value_key="3">128GB SSD [+$30.00]</option>
                <option value="35" data-option_value_key="4">256GB SSD [+$35.00]</option>
                <option value="50" data-option_value_key="5">512GB SSD [+$50.00]</option>
            </select>
            <select name="properties[Memory]" class="memory">
                <option value="0">-- Choose Memory --</option>
                <option value="0" data-option_value_key="0">4GB</option>
                <option value="20" data-option_value_key="1">8GB [+$20.00]</option>
                <option value="40" data-option_value_key="2">16GB [+$40.00]</option>
            </select>
            <select name="properties[Operating System]" class="operating_system">
                <option value="0">-- Choose Operating System --</option>
                <option value="0" data-option_value_key="0">No Operating System Needed</option>
                <option value="19.19" data-option_value_key="1">Windows 10 Home 64-Bit [+$19.99]</option>
                <option value="39.99" data-option_value_key="2">Windows 10 Pro 64-Bit [+$39.99]</option>
            </select>
            <select name="properties[Software]" class="software">
                <option value="0">-- Choose Software --</option>
                <option value="0" data-option_value_key="0">No Additional Software</option>
                <option value="49.99" data-option_value_key="1">Microsoft Office 365 - 2019 [+$49.99]</option>
            </select>
            <select name="properties[Warranty]" class="warranty">
                <option value="0">-- Choose Warranty --</option>
                <option value="0" data-option_value_key="0">90 Day Standard Warranty</option>
                <option value="29.99" data-option_value_key="1">One Year - Support &amp; Maintenance [+$29.99]</option>
                <option value="49.99" data-option_value_key="2">Two Year - Support &amp; Maintenance [+$49.99]</option>
                <option value="74.99" data-option_value_key="3">Three Year - Support &amp; Maintenance [+$74.99]</option>
            </select>
            <select name="properties[Accessories]" class="accessories">
                <option value="0">-- Choose Accessories --</option>
                <option value="0" data-option_value_key="0">No Accessories Needed</option>
                <option value="199.99" data-option_value_key="1">Refurbished HP Laserjet Desktop Printer [+$199.99]</option>
            </select>
       @endif
    </form>
    @if (!empty($shopifyPriceData))
        <div class="show_all_record" style="display: none">
            <h2> Shopify product data </h2>
            <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Model</th>
                        <th>Form Factor</th>
                        <th>Processor</th>
                        <th>Condition</th>
                        <th>Serial Number</th>
                        <th>Class</th>
                        <th>Brand</th>
                        <th>Model Number</th>
                        <th>RAM</th>
                        <th>Memory Type</th>
                        <th>Memory Speed</th>
                        <th>Hard Drive</th>
                        <th>HD Interface</th>
                        <th>HD Type</th>
                        <th>Price</th>
                        <th>Final Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shopifyPriceData as $key => $priceData)
                        <tr>
                            <td>{{$priceData['Asset_ID']}}</td>
                            <td>{{$priceData['Model']}}</td>
                            <td>{{$priceData['Form_Factor']}}</td>
                            <td>{{$priceData['Processor']}}</td>
                            <td>{{$priceData['Condition']}}</td>
                            <td>{{$priceData['SerialNumber']}}</td>
                            <td>{{$priceData['Class']}}</td>
                            <td>{{$priceData['Brand']}}</td>
                            <td>{{$priceData['Model_Number']}}</td>
                            <td>{{$priceData['RAM']}}</td>
                            <td>{{$priceData['Memory_Type']}}</td>
                            <td>{{$priceData['Memory_Speed']}}</td>
                            <td>{{$priceData['Hard_Drive']}}</td>
                            <td>{{$priceData['HD_Interface']}}</td>
                            <td>{{$priceData['HD_Type']}}</td>
                            <td>${{$priceData['Price']}}</td>
                            <td>${{$priceData['Final_Price']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <h2>Upload file to update</h2>
    <p>Note: Please upload .xlsx file to update Shopify Pricing Table</p>
    <form action="" method="POST" enctype="multipart/form-data">
        @if ($message)
            <p class="message">{{$message}}</p>
        @endif
        <div class="row" style="margin-bottom:5px">
            <div class="col-sm-10">
                <div class="form-group-sm">
                    <input placeholder="" class="form-control" type="file" name="file" />
                </div>
            </div>
             <div class="col-sm-2">
                <div class="form-group-sm">
                    <input class="btn btn-success" type="submit"/>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection