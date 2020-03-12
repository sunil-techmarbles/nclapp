<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../new_runlist_helper.php");

$db = DB::init();

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $sql = "select d.asin, count(d.asin) as cnt, max(d.mid) as mid,
		d.technology, d.model, d.cpu_core, d.cpu_model, d.cpu_gen, d.shopify_product_id
 		from tech_list_data d 
 		where d.status='active' and d.run_status='active' and d.shopify_product_id != '' and d.asin = '" . $_POST['id'] . "' 
 		group by d.asin, d.technology, d.model, d.cpu_core, d.cpu_gen, d.shopify_product_id  LIMIT 1";
} else {
    die('Permission Denied.');
}

$all_running_list = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (!empty($all_running_list)) {
    $baseurl = SHOPIFY_BASE_URL;
    //initiate logger class
    $logger = new Error_Logger();

    //set error log file path 
    $log_file_path = 'error-logs/shopify-api.log';
    $log_path = '../error-logs/shopify-api.log';

    if (!file_exists($log_path)) {
        if (!is_writable(__DIR__ . "/error-logs/")) {
            echo 'Please create this file' . $log_path;
            return;
        }
        touch($log_file_path);
    }

    $logger->lfile($log_file_path);
    $return_error = '';
//    foreach ($all_running_list as $key => $running_list) {
    $running_list = $all_running_list[0];
    $running_list['condition'] = CONDITION;
    $running_list['form_factor'] = $running_list['technology'];

    $price = productPriceCalculation($db, $running_list);

    if ($price == 0 || $price == 0.00) {
        $price = 299.99;
    }
    $variant_data['variant'] = [
        "price" => $price,
    ];

    $return_error[] = updateShopifyProductNewRunListPrice($db, SHOPIFY_BASE_URL, $running_list, $variant_data);
//    }
    pr($return_error);
} else {
    echo 'No Data Found for this ASIN';
}
