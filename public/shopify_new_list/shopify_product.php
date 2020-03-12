<?php

require_once("../new_runlist_helper.php");
require_once("manufacturer.php");

$db = DB::init();

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "select d.*, count(d.id) as cnt,d.id as list_id, 
		a.tab,a.technology,a.id
 		from tech_list_data d inner join tech_form_models a on d.mid = a.id 
 		where d.status='active' and d.run_status='active' and d.model != 'none' and d.id > 0 and d.asin = '" . $id . "'
 		group by d.id LIMIT 1";

    $all_running_list = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($all_running_list)) {
        //initiate logger class
        $logger = new Error_Logger();

        //set error log file path 
        $log_file_path = __DIR__ . '/../error-logs/shopify-api.log';
        $log_path = 'error-logs/shopify-api.log';

        if (!file_exists($log_file_path)) {
            if (!is_writable(__DIR__ . "/../error-logs/")) {
                echo 'Please create this file ' . $log_path;
                return;
            }
            touch($log_file_path);
        }

        $logger->lfile($log_file_path);
        $running_list = $all_running_list[0];
//        foreach ($all_running_list as $key => $running_list) {
        $asin = createAsinFromData($running_list);
        $image_asin = createImageAsinFromData($running_list);

        $all_images = glob('../' . IMAGE_PATH_NEW . $image_asin . '*');
        if (empty($all_images)) {
            $error = "Can't sync product.Reason: No images found for ASIN " . $image_asin;
            $return_error[] = $error;
            $logger->lwrite($error);
            print_r($return_error);
            die;
//                continue;
        }
        $insert_data_array = getAdditionalDataForNewRunlist($running_list['id'], $db);

        $insert_data_array['processer_gen'] = getProcessorGenration($running_list['cpu_model']);
        $running_list['cpu_model'] = getProcessorModel($running_list['cpu_model']);
        $running_list['condition'] = CONDITION;
        $model_data = explode(' ', $running_list['model']);
        $insert_data_array['series'] = $model_data[0];
        $running_list['form_factor'] = $running_list['technology'];
        $running_list['asin'] = $asin;
        $running_list['manufacturer'] = getManufacturerForNewRunlistdata($db, $model_data[0]);
        $running_list['list_id'] = $running_list['list_id'];
        $price = productPriceCalculation($db, $running_list);

        $apple_data = getShopifyAppleDataFromTable($db, $running_list);
        if (!empty($apple_data)) {
            $insert_data_array['product_class'] = 'Apple';
            $running_list['manufacturer'] = $apple_data['Manufacturer'];
        }

        switch ($insert_data_array['product_class']) {
            case 'Computer':
                $data_object = new Shopify_Computer($running_list, $insert_data_array, $insert_data_array['product_class']);
                break;
            case 'Laptop':
                $data_object = new Shopify_Laptop($running_list, $insert_data_array, $insert_data_array['product_class']);
                break;
            case 'All_In_One':
                $data_object = new Shopify_Laptop($running_list, $insert_data_array, $insert_data_array['product_class']);
                break;
            case 'Printer':
//            $data_object = new Shopify_Printer($running_list, $insert_data_array, $available_port, $processer_gen, $series);
//            break;
            case 'Apple':
                $data_object = new Shopify_Apple_Laptop($running_list, $insert_data_array, $apple_data, $insert_data_array['product_class']);
                break;
            default:
                $error = "Can't sync product. Reason: Class " . $insert_data_array['product_class'] . " not found for asin " . $asin . ". Valid classes are Computer & Laptop";
                $return_error[] = $error;
                $logger->lwrite($error);
                continue;
                break;
        }

        $data = $data_object->data;
//print_r($data);die; 
        $bar_code = getBarCode($asin, $db);

        if ($price == 0 || $price == 0.00) {
            $price = 299.99;
        }

        $variant_data['variant'] = [
            "price" => $price,
            "sku" => strtolower($asin),
            "inventory_management" => "shopify",
            "barcode" => $bar_code,
            "weight" => $insert_data_array['weight'],
        ];

        if (empty($running_list['shopify_product_id']) || $running_list['shopify_product_id'] == 0) {
            $return_error[] = createShopifyNewRunlistProduct($db, $data, $running_list, $variant_data);
        } else {
            $data['product']['id'] = $running_list['shopify_product_id'];
            $productsurl = SHOPIFY_BASE_URL . "/admin/api/2019-04/products/" . $running_list['shopify_product_id'] . ".json";
            $shopify_get_product = getApiData($productsurl);
            if (isset($shopify_get_product['errors']) && strtolower($shopify_get_product['errors']) == "not found") {
                $return_error[] = createShopifyNewRunlistProduct($db, $data, $running_list, $variant_data);
            } else {
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                    $return_error[] = updateShopifyNewRunlistProduct($db, $data, $running_list, $variant_data);
                }
            }
        }
//        }
    } else {
        $return_error = 'Record not found.';
    }

    print_r($return_error);
} else {
    die('Permission Denied.');
}
