<?php

require_once __DIR__ . '/function.php';

$format = "xml";
$request_filePath = __DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . "/all-reports.xml";
$request_file_name = BLANCCO_XML_REQUEST_FOLDER . '/all-reports.xml';

TMdoCurlRequest($request_filePath, $request_file_name, 'fulldata', $format, 'data', 'all');
// echo "Successfully Full reports file created.";  
include __DIR__ . '/sabre_xml/vendor/autoload.php';

$report_allowed_states = ['Successful', 'Failed'];

$log_file = __DIR__ . '/' . BLANCCO_API_LOG_FILE;
$blancco_all_reports_file_path = __DIR__ . "/" . BLANCCO_XML_DATA_FOLDER . "/data.xml";

$single_pdf_report_request_filePath = __DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . "/single-report-pdf.xml";
$single_pdf_report_request_filename = BLANCCO_XML_REQUEST_FOLDER . '/single-report-pdf.xml';

$single_xml_report_request_filePath = __DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . "/single-report-xml.xml";
$single_xml_report_request_filename = BLANCCO_XML_REQUEST_FOLDER . '/single-report-xml.xml';

$single_pdf_report_request_filePath_updated = __DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . "/updated_pdf_request.xml";
$single_pdf_report_request_filename_updated = BLANCCO_XML_REQUEST_FOLDER . '/updated_pdf_request.xml';

$single_xml_report_request_filePath_updated = __DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . "/updated_xml_request.xml";
$single_xml_report_request_filename_updated = BLANCCO_XML_REQUEST_FOLDER . '/updated_xml_request.xml';

// loop through all the reports in this single file to create single - single pdf and xml report file  
$reports = TMgetXmlFileContent($blancco_all_reports_file_path);

foreach ($reports as $key => $report) {

    $reportUuid = $state = $serial = $lot_number = $asset_id = $file_name = '';
    $blancco_report_data = $report['value'][0]['value'];
    $blancco_user_data = $report['value'][1]['value'][0]['value'];
    foreach ($blancco_report_data as $key => $data) {
        if ($data['name'] == '{}description') {
            $reportUuid = $data['value'][0]['value'];
        }
        if ($data['name'] == '{}blancco_erasure_report') {
            $blancco_erasure_report_data = $data['value'][0]['value'][0]['value'];
            if (is_array($blancco_erasure_report_data)) {
                foreach ($blancco_erasure_report_data as $key => $state_data) {
                    if ($state_data['attributes']['name'] == 'state') {
                        $state = $state_data['value'];
                    }
                }
            }
        }
        if ($data['name'] == '{}blancco_hardware_report') {
            $blancco_hardware_report_data = $data['value'][0]['value'];
            if (is_array($blancco_hardware_report_data)) {
                foreach ($blancco_hardware_report_data as $key => $serial_data) {
                    if ($serial_data['attributes']['name'] == 'serial') {
                        $serial = $serial_data['value'];
                    }
                }
            }
        }
    }



    if (in_array($state, $report_allowed_states)) {


        foreach ($blancco_user_data as $key => $user_data) {
            if ($user_data['attributes']['name'] == 'Asset ID') {
                $asset_id = $user_data['value'];
            } elseif ($user_data['attributes']['name'] == 'Lot Number') {
                $lot_number = $user_data['value'];
            }
        }
        if (!empty($serial) && !empty($asset_id) && !empty($lot_number)) {
            $file_name = $lot_number . "-" . $asset_id . "-" . $serial;
            $searches = ['{{id}}'];
            $replacements = [$reportUuid];

            // for getting pdf file of single report 
            $xml_pdf = simplexml_load_file($single_pdf_report_request_filePath);
            $newXml_pdf = simplexml_load_string(str_replace($searches, $replacements, $xml_pdf->asXml()));
            $newXml_pdf->asXml(__DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . '/updated_pdf_request.xml');

            TMdoCurlRequest($single_pdf_report_request_filePath_updated, $single_pdf_report_request_filename_updated, 'singlepdf', "pdf", $file_name, $reportUuid);

            // for getting xml file of single report 
            $xml = simplexml_load_file($single_xml_report_request_filePath);
            $newXml = simplexml_load_string(str_replace($searches, $replacements, $xml->asXml()));
            $newXml->asXml(__DIR__ . "/" . BLANCCO_XML_REQUEST_FOLDER . '/updated_xml_request.xml');

            TMdoCurlRequest($single_xml_report_request_filePath_updated, $single_xml_report_request_filename_updated, 'singlexml', "xml", $file_name, $reportUuid);
        } else {
            if (empty($serial)) {
                $txt = $reportUuid . " -- Serial Number not found for this Report Uuid";
                file_put_contents($log_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            if (empty($asset_id)) {
                $txt = $reportUuid . " -- Asset Id not found for this Report Uuid";
                file_put_contents($log_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            if (empty($lot_number)) {
                $txt = $reportUuid . " -- Lot Number not found for this Report Uuid";
                file_put_contents($log_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
    } else {
        if (!empty($state)) {
            $txt = $reportUuid . " --Invalid State value for this Uuid. State : " . $state;
            file_put_contents($log_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            $txt = $reportUuid . " -- State not found for this report Uuid";
            file_put_contents($log_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}

die('done');
