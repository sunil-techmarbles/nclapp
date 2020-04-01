@extends('layouts.appadminlayout')
@section('title', 'Tracker Report')
@section('content')
<div class="container shopify-product-table">
    <div id="page-head" class="noprint">Time Tracker Report</div>
    <form action="" method="POST" class="search-table" enctype="multipart/form-data">
        <?php
        if (isset($_POST['model']) && empty($shopify_price_data)) {
            echo '<p class="message"> No Data Found.</p><br>';
        }
        ?>
        <lable for="model"><b>Model: </b></lable>
        <input type="text" name="model" placeholder="Model" value="<?php echo $model; ?>" required/>
        <lable for="form_factor"><b>Form Factor: </b></lable>
        <input type="text" name="form_factor" placeholder="Form Factor" value="<?php echo $form_factor; ?>" required/>
        <lable for="processor"><b>Processor: </b></lable>
        <input type="text" name="processor" placeholder="Processor" value="<?php echo $processor; ?>" required/>
        <lable for="processor"><b>Condition: </b></lable>
        <input type="text" name="condition" placeholder="Condition" value="<?php echo $condition; ?>" required/>
        <input type="submit"/>
        <?php
        if (!empty($shopify_price_data)) {
            echo '<br><br><h2>Final Price : $<span class="show_final_price">' . $shopify_price_data[0]['Final_Price'] . '</span></h2>';
            echo '<input class="show_all" type="button" value="Show All Records"><br><br>';
            ?>
            <input type="hidden" class="final_price" value="<?php echo $shopify_price_data[0]['Final_Price']; ?>">
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
            <?php
        }
        ?>
    </form>
    <?php if (!empty($shopify_price_data)) { ?>
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
                    <?php foreach ($shopify_price_data as $key => $price_data) { ?>
                        <tr>
                            <td><?php echo $price_data['Asset_ID']; ?></td>
                            <td><?php echo $price_data['Model']; ?></td>
                            <td><?php echo $price_data['Form_Factor']; ?></td>
                            <td><?php echo $price_data['Processor']; ?></td>
                            <td><?php echo $price_data['Condition']; ?></td>
                            <td><?php echo $price_data['SerialNumber']; ?></td>
                            <td><?php echo $price_data['Class']; ?></td>
                            <td><?php echo $price_data['Brand']; ?></td>
                            <td><?php echo $price_data['Model_Number']; ?></td>
                            <td><?php echo $price_data['RAM']; ?></td>
                            <td><?php echo $price_data['Memory_Type']; ?></td>
                            <td><?php echo $price_data['Memory_Speed']; ?></td>
                            <td><?php echo $price_data['Hard_Drive']; ?></td>
                            <td><?php echo $price_data['HD_Interface']; ?></td>
                            <td><?php echo $price_data['HD_Type']; ?></td>
                            <td>$<?php echo $price_data['Price']; ?></td>
                            <td>$<?php echo $price_data['Final_Price']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <h2>Upload file to update</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <?php
        if ($msg) {
            echo '<p class="message">' . $msg . '</p>';
        }
        ?>
        <input type="file" name="file" />
        <input type="submit"/>
        <p>Note: Please upload .xlsx file to update Shopify Pricing Table</p>
    </form>
</div>
@endsection