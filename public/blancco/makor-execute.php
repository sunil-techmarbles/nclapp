<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/function.php';
require_once '../helper-functions.php';
require_once '../mobile.php';

$log_file = __DIR__ . '/' . MAKOR_API_LOG_FILE;
$blancco_additional_data_path = "../" . ADDITIONAL_MOBILE_DATA_FOLDER . "/";
$blancco_data_dir = __DIR__ . "/" . BLANCCO_XML_DATA_FOLDER . "/";

// get all XML files from blancco data directory .
$blancoo_xml_files = getDirectoryFiles($blancco_data_dir);

// loop through all the xml reports files . 
foreach ($blancoo_xml_files as $key => $blancoo_xml_file) {

    if (substr($blancoo_xml_file, 0, 4) != "data") {

        $additional_mobile_data_file = $reportUuid = $lot_number = '';

        // variables form blancco report data .   
        $asset_id = $serial = $manufacturer = $model = $model_number = $internal_model = $color = $battery = $hd_serial = '';
        $hd_capacity = $hd_services_performed = $service_queue_status = $os_type = $os_version = $sim_status = '';
        $MDM_status = $FMIP_status = $blacklist_status = $graylist_status = $release_year = '';
        $carrier = 'N/A';

        // variables from additional data . 
        $customer_asset_tag = $item_net_weight = $grade = $type = $charging_port = $display_size = $display_resolution = '';
        $display_touchscreen = $notes = $other = '';
        $cosmetic = $missing = $functional = $screen = $case = $input_output = [];

        // variables that are fixed .  
        $pallet = '1-DefaultPallet-I';
        $next_process = 'Resale';
        $compliance_label = 'Tested for Key Functions,  R2/Ready for Resale';
        $condition = 'Tested Working';
        $hd_manufacturer = 'Apple';
        $hd_model = 'N/A';
        $hd_part_number = 'N/A';
        $hd_interface = 'Properitary';
        $hd_power_on_hours = 'N/A';
        $hd_type = 'Onboard';

        // pr( $blancoo_xml_file );     	      

        $lot_number_asset_id_serial = pathinfo($blancoo_xml_file, PATHINFO_FILENAME);
        @list( $lot_number, $asset_id, $serial ) = explode("-", $lot_number_asset_id_serial);

        $path = __DIR__ . "/" . BLANCCO_XML_DATA_FOLDER . "/" . $blancoo_xml_file;

        $blanccodata = TMgetXmlFileContent($path);

        $blancco_report_data = $blanccodata[0]['value'][0];

        if ($blancco_report_data['name'] != '{}blancco_data') {
            $error_text = 'No xml data found for this Asset Id : ' . $asset_id;
            file_put_contents($log_file, $error_text . PHP_EOL, FILE_APPEND | LOCK_EX);
            continue;
        }

        $mainBlanccoData = $blancco_report_data['value'];

        if (!empty($mainBlanccoData) && is_array($mainBlanccoData)) {
            foreach ($mainBlanccoData as $key => $data) {

                if ($data['name'] == '{}description') {
                    $reportUuid = $data['value'][0]['value'];
                }

                if ($data['name'] == '{}blancco_hardware_report') {
                    $blancco_hardware_report_data = $data['value'];

                    foreach ($blancco_hardware_report_data as $key => $hardware_report_data) {
                        $BlanccoHardwareReport = $hardware_report_data['value'];

                        if ($BlanccoHardwareReport[0]['name'] == '{}entries') {

                            foreach ($BlanccoHardwareReport as $key => $HardwareReport) {
                                $HardwareReports = $HardwareReport['value'];
                                foreach ($HardwareReports as $key => $SingleHardwareReport) {
                                    // for getting capiacity
                                    if ($SingleHardwareReport['attributes']['name'] == 'capacity') {
                                        $hd_capacity_in_bytes = $SingleHardwareReport['value'];
                                    }
                                }
                            }
                        } else {

                            if (is_array($BlanccoHardwareReport) && !empty($BlanccoHardwareReport)) {

                                foreach ($BlanccoHardwareReport as $key => $HardwareReport) {

                                    // for getting manufacturer   
                                    if ($HardwareReport['attributes']['name'] == 'manufacturer') {
                                        $manufacturer = $HardwareReport['value'];
                                        $manufacturer = substr($manufacturer, 0, strpos($manufacturer, ","));
                                    }

                                    // for getting model 
                                    if ($HardwareReport['attributes']['name'] == 'market_name') {
                                        if (!strpos($HardwareReport['value'], "GSM")) {
                                            $item_model = $HardwareReport['value'];
                                        } else {
                                            $item_model = substr($HardwareReport['value'], 0, strpos($HardwareReport['value'], "GSM"));
                                        }
                                    }

                                    if ($HardwareReport['attributes']['name'] == 'a_model_number') {
                                        $model_number = $HardwareReport['value'];
                                    }


                                    // for getting internal model 
                                    if ($HardwareReport['attributes']['name'] == 'internal_model') {
                                        $internal_model = $HardwareReport['value'];
                                    }

                                    if ($HardwareReport['attributes']['name'] == 'region') {
                                        $internal_model_region = $HardwareReport['value'];
                                    }

                                    // for getting color  
                                    if ($HardwareReport['attributes']['name'] == 'device_color') {
                                        $color = $HardwareReport['value'];
                                    }

                                    // for getting battery   
                                    if ($HardwareReport['attributes']['name'] == 'battery_serial') {
                                        $battery = $HardwareReport['value'];
                                    }

                                    // for getting imei 
                                    if ($HardwareReport['attributes']['name'] == 'imei') {
                                        $hd_serial = $HardwareReport['value'];
                                    }

                                    // for getting initial Carrier 
                                    if ($HardwareReport['attributes']['name'] == 'initial_carrier') {
                                        $carrier = $HardwareReport['value'];
                                    }

                                    // for getting prolog_simlock  
                                    if ($HardwareReport['attributes']['name'] == 'prolog_simlock') {
                                        $sim_status = $HardwareReport['value'];
                                    }

                                    // for getting mdm_status  
                                    if ($HardwareReport['attributes']['name'] == 'mdm_status') {
                                        $MDM_status = $HardwareReport['value'];
                                    }

                                    // for getting FMIP Status  
                                    if ($HardwareReport['attributes']['name'] == 'find_my_iphone') {
                                        $FMIP_status = $HardwareReport['value'];
                                    }

                                    // for getting Blacklist Status  
                                    if ($HardwareReport['attributes']['name'] == 'prolog_blacklisted') {
                                        $blacklist_status = $HardwareReport['value'];
                                    }

                                    // for getting Graylist Status  
                                    if ($HardwareReport['attributes']['name'] == 'prolog_graylisted') {
                                        $graylist_status = $HardwareReport['value'];
                                    }

                                    // for getting Release Year  
                                    if ($HardwareReport['attributes']['name'] == 'manufacturing_date') {
                                        $release_year = substr($HardwareReport['value'], 0, 4);
                                    }
                                }
                            }
                        }
                    }
                }

                // getting blancco erasure report data  
                if ($data['name'] == '{}blancco_erasure_report') {
                    $blancco_erasure_report_data = $data['value'];

                    foreach ($blancco_erasure_report_data as $key => $erasure_report_data) {
                        $BlanccoErasureReport = $erasure_report_data['value'];
                        if ($BlanccoErasureReport[0]['name'] == '{}entries') {
                            foreach ($BlanccoErasureReport as $key => $ErasureReport) {
                                $ErasureReports = $ErasureReport['value'];


                                foreach ($ErasureReports as $key => $SingleErasureReport) {

                                    // for getting HD Services Performed  
                                    if ($SingleErasureReport['attributes']['name'] == 'erasure_standard_name') {
                                        $hd_services_performed = $SingleErasureReport['value'];
                                    }

                                    // for getting HD Services Performed  
                                    if ($SingleErasureReport['attributes']['name'] == 'state') {
                                        $service_queue_status = $SingleErasureReport['value'];
                                    }
                                }
                            }
                        }
                    }
                }


                // getting software report data  . 
                if ($data['name'] == '{}blancco_software_report') {
                    $blancco_software_report_data = $data['value'];
                    foreach ($blancco_software_report_data as $key => $software_report_data) {
                        $BlanccoSoftwareReports = $software_report_data['value'];
                        foreach ($BlanccoSoftwareReports as $key => $BlanccoSoftwareReport) {
                            // for getting OS type  
                            if ($BlanccoSoftwareReport['attributes']['name'] == 'name') {
                                $os_type = $BlanccoSoftwareReport['value'];
                            }

                            // for getting OS version
                            if ($BlanccoSoftwareReport['attributes']['name'] == 'version') {
                                $os_version = $BlanccoSoftwareReport['value'];
                            }
                        }
                    }
                }
            }
        }


        $model = trim($item_model) . ' (' . $model_number . ')';
        $model1 = trim($internal_model) . trim($internal_model_region);

        $hd_capacity = TMisa_convert_bytes_to_specified($hd_capacity_in_bytes, 'G');

        // additional wipe mobile data .. 
        $additional_mobile_data_file = $blancco_additional_data_path . $asset_id . ".xml";

        if (file_exists($additional_mobile_data_file)) {
            $additional_mobile_data = TMgetXmlFileContent($additional_mobile_data_file);
        } else {
            $error_text = $reportUuid . ' --> No Additional xml data file found for this Asset Id : ' . $asset_id;
            file_put_contents($log_file, $error_text . PHP_EOL, FILE_APPEND | LOCK_EX);
            continue;
        }

        if (is_array($additional_mobile_data)) {
            foreach ($additional_mobile_data as $key => $additional_data) {

                // for getting Customer Asset Tag 
                if ($additional_data['name'] == '{}Customer_Asset_Tag') {
                    $customer_asset_tag = $additional_data['value'];
                }

                // for getting Item Net Weight 
                if ($additional_data['name'] == '{}Weight') {
                    $item_net_weight = $additional_data['value'];
                }

                // for getting Grade  
                if ($additional_data['name'] == '{}Grade') {
                    $grade = $additional_data['value'];
                }

                // for getting Type 
                if ($additional_data['name'] == '{}Technology') {
                    $type = $additional_data['value'];
                }

                // for getting Charging Port 
                if ($additional_data['name'] == '{}Charging_Port') {
                    $charging_port = $additional_data['value'];
                }

                // for getting display_size and resolution  
                if ($additional_data['name'] == '{}Screen') {
                    $screen_data = $additional_data['value'];
                    foreach ($screen_data as $screen_d) {
                        if ($screen_d['name'] == '{}Size') {
                            $display_size = $screen_d['value'];
                        }
                        if ($screen_d['name'] == '{}Resolution') {
                            $display_resolution = $screen_d['value'];
                        }
                        if ($screen_d['name'] == '{}Touchscreen') {
                            $display_touchscreen = $screen_d['value'];
                        }
                    }
                }

                // for getting cosmetic  , input/output , Missing , Functional , Screen , Case  
                if ($additional_data['name'] == '{}Description') {
                    $descriptions_data = $additional_data['value'];

                    foreach ($descriptions_data as $description_data) {

                        if ($description_data['name'] == '{}Input_Output') {
                            $input_output[] = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Cosmetic') {
                            $cosmetic[] = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Missing') {
                            $missing[] = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Functional') {
                            $functional[] = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Screen') {
                            $screen[] = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Case') {
                            $case[] = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Notes') {
                            $notes = $description_data['value'];
                        }

                        if ($description_data['name'] == '{}Other') {
                            $other = $description_data['value'];
                        }
                    }
                }
            }
        }

        $hd_removed = ( $service_queue_status == 'Successful' ) ? 'No' : 'Yes';

        $api_data = [
            'asset_id' => $asset_id,
            'serial' => $serial,
            'manufacturer' => $manufacturer,
            'model' => $model,
            'model_number' => $model1,
            'customer_asset_tag' => $customer_asset_tag,
            'weight' => $item_net_weight,
            'pallet' => $pallet,
            'grade' => $grade,
            'next_process' => $next_process,
            'compliance_label' => $compliance_label,
            'type' => str_replace('_', ' ', $type),
            'condition' => $condition,
            'color' => $color,
            'charging_port' => $charging_port,
            'battery' => $battery,
            'hd_manufacturer' => $hd_manufacturer,
            'hd_model' => $hd_model,
            'hd_part_number' => $hd_part_number,
            'hd_serial' => $hd_serial,
            'hd_capacity' => $hd_capacity . 'GB',
            'hd_interface' => $hd_interface,
            'hd_power_on_hours' => $hd_power_on_hours,
            'hd_services_performed' => $hd_services_performed,
            'hd_removed' => $hd_removed,
            'hd_type' => $hd_type,
            'service_queue_status' => $service_queue_status,
            'os_type' => $os_type,
            'os_version' => $os_version,
            'display_size' => $display_size,
            'display_resolution' => $display_resolution,
            'display_touchscreen' => $display_touchscreen,
            'carrier' => $carrier,
            'sim_status' => $sim_status,
            'MDM_status' => $MDM_status,
            'FMIP_status' => $FMIP_status,
            'blacklist_status' => $blacklist_status,
            'graylist_status' => $graylist_status,
            'release_year' => $release_year,
            'notes' => $notes,
            'other' => $other,
            'input_output' => $input_output,
            'cosmetic' => $cosmetic,
            'missing' => $missing,
            'functional' => $functional,
            'screen' => $screen,
            'case' => $case,
        ];

        $api_data_object = new Mobile($api_data, 'mobile');

        $xml_file = __DIR__ . '/makormobileexecute.xml';
        $dataxml = fopen($xml_file, 'w');
        fwrite($dataxml, $api_data_object->xml_data);
        fclose($dataxml);

        $json_data = array();
        $json_data['asset_id'] = $asset_id;
        $json_data['asset_report']['report'] = base64_encode($api_data_object->xml_data);
        $api_data = json_encode($json_data);

        $makors = TMMakorAPIRequest($api_data);

        if ($makors == 200) {
            if (!file_exists(__DIR__ . '/makor_requests')) {
                mkdir(__DIR__ . '/makor_requests', 0777, true);
            }

            $makor_executed_file_path = __DIR__ . '/makor_requests/' . $asset_id . '.xml';

            //Save makor executed files
            $dataxml = fopen($makor_executed_file_path, 'w');
            fwrite($dataxml, $api_data_object->xml_data);
            fclose($dataxml);

            // move the files that are executed 
            $destination_mobile_executed_file = "../" . EXECUTED_MOBILE_DATA_FOLDER . $blancoo_xml_file;
            rename($path, $destination_mobile_executed_file);

            // move the files that are executed 
            $destination__additional_mobile_executed_file = "../" . EXECUTED_MOBILE_ADDITIONAL_DATA_FOLDER . $asset_id . ".xml";

            rename($additional_mobile_data_file, $destination__additional_mobile_executed_file);
        }
    }
}

die("");



