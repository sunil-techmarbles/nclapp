<?php
function implodeSupplieEmails($array )
{
    $r = array();
    foreach ($array as $key => $value)
    {
        $r[$key] = $value->email;
    }
    return implode(',', $r);
}

function supplieEmialArray($object)
{
    $r = array();
    foreach ($object as $key => $value)
    {
        $r[$key] = $value->email;
    }
    return $r;
}

function ifnull($var, $default='')
{
    return is_null($var) ? $default : $var;
}

function explodeSupplieAsinsModels($array)
{
    $r = array();
    foreach ($array as $key => $value)
    {
        $r[$key] = $value->asin_model_id;
    }
    return $r;
}

function resultInReadableform($result)
{
    $output = [];
    foreach ($result as $key => $value)
    {
        $keys = array_keys($value->toArray());
        foreach ($keys as $k => $v) {
            if($v == 'get_supplie_asin_models')
            {
                break;
            }
            $output[$key][$v] = $value[$v];
        }
        if($value['getSupplieAsinModels'])
        {
            $AsinModels = explodeSupplieAsinsModels($value['getSupplieAsinModels']);
        }
        else
        {
            $AsinModels = [];
        }
        $output[$key]['models'] = $AsinModels;
    }
    return $output;
}

function checkImages($data)
{
    $asin = createImageAsinFromData($data);
    $allImages = glob(public_path().'/'. config('constants.finalPriceConstants.imagePathNew') . $asin . '*');
    if (empty($allImages))
    {
        return 'N/A';
    }
    else
    {
        return 'Available';
    }
}

function createImageAsinFromData($data)
{
    $asin = createAsinFromData($data);
    $asin = substr($asin, 0, strrpos($asin, '-'));
    return $asin;
}

function checkRunlistPrice($data)
{
    $searchDataArray['condition'] = config('constants.finalPriceConstants.condition');
    $searchDataArray['form_factor'] = $data['technology'];
    $searchDataArray['model'] = $data['model'];
    $searchDataArray['cpu_core'] = $data['cpu_core'];
    $searchDataArray['asin'] = $data['asin'];
    $finalPrice = new App\Http\Controllers\ShopifyController($searchDataArray);
    return $finalPrice->finalPrice;
}

function createFormFactorForNewRunlist($data)
{
    $formFactor = '';
    switch ($data['technology']) 
    {
        case "Ultra Small Form Factor":
        $formFactor = "USFF";
        break;
        case "Small Form Factor":
        $formFactor = "SFF";
        break;
        case "Notebook":
        $formFactor = "LAP";
        break;
        case "All_In_One":
        $formFactor = "AIO";
        break;
        case "Mini Tower":
        $formFactor = "MT";
        break;
        case "Desktop":
        $formFactor = "DT";
        break;
        case "Tablet_Notebook":
        $formFactor = "TAB";
        break;
        case "Tabet_Notebook":
        $formFactor = "TAB";
        break;
        case "Tiny Desktop":
        $formFactor = "TINY";
        break;
        default:
        $formFactor = '';
        break;
    }
    return $formFactor;
}

function createAsinFromData($data)
{
    if (!empty($data['model']))
    {
        $modal = trim(str_replace(array('(', ')'), '', $data['model']));
        $modal = str_replace(" ", "_", ucwords($modal));
    }
    else
    {
        $modal = '';
    }

    $formFactor = createFormFactorForNewRunlist($data);
    if (!empty($formFactor))
    {
        $formFactor = '-' . $formFactor;
    }
    else
    {
        $formFactor = '';
    }

    if (!empty($data['cpu_core']))
    {
        $cpuCore = '-' . $data['cpu_core'];
    }
    else
    {
        $cpuCore = '';
    }

    if (!empty($data['cpu_gen']))
    {
        $cpuGen = '-' . intval($data['cpu_gen']);
    }
    else
    {
        $cpuGen = '';
    }
    $asin = $modal . $formFactor . $cpuCore . $cpuGen;
    return trim($asin);
}

function pr($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function getApiDataForPrice($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    if (!$response)
    {
        return false;
    }
    return $shopifyData = json_decode($response, 1);
}

function getShopifyRunlistPrice($data)
{
    $productsurl = config('constants.finalPriceConstants.shopifyBaseUrl') . "/admin/api/2019-04/products/" . $data['shopify_product_id']. ".json";
    $shopifyProductData = getApiDataForPrice($productsurl);
    $shopifyPrice = (isset($shopifyProductData['product'])) ?  $shopifyProductData['product']['variants'][0]['price'] : 0 ;
    $searchDataArray['condition'] = config('constants.finalPriceConstants.condition');
    $searchDataArray['form_factor'] = $data['technology'];
    $searchDataArray['model'] = $data['model'];
    $searchDataArray['cpu_core'] = $data['cpu_core'];
    $searchDataArray['asin'] = $data['asin'];
    $finalPrice = new App\Http\Controllers\ShopifyController($searchDataArray);
    $price = array();
    $price['shopify_price'] = $shopifyPrice;
    $price['final_price'] = $finalPrice->finalPrice;
    $diffrence = $shopifyPrice - $finalPrice->finalPrice;
    $price['diffrence'] = $diffrence;
    return $price;
}

function getProcessorGenration($cpuModel)
{
    $processerGen = substr($cpuModel, 0, 1);
    switch ($processerGen) {
        case 1:
        $processerGen = "1st Gen";
        break;
        case 2:
        $processerGen = "2nd Gen";
        break;
        case 3:
        $processerGen = "3rd Gen";
        break;
        default:
        $processerGen = $processerGen . "th Gen";
        break;
    }
    return $processerGen;
}

function getProcessorModel($model)
{
    $modelArray = explode(',', $model);
    $model = min($modelArray);
    return $model;
}

function getManufacturerForNewRunlistdata($series)
{
    $series = strtolower($series);
    $manuFacturer = config('constants.finalPriceConstants.manuFacturer');
    if (isset($manuFacturer[$series]))
    {
        return $manuFacturer[$series];
    }
    else
    {
        return '';
    }
}

function postApiData($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    if (!$response)
    {
        return false;
    }
    return $shopifyData = json_decode($response, 1);
}

function putApiData($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    if (!$response)
    {
        return false;
    }
    return $shopifyData = json_decode($response, 1);
}

function createImageName($name)
{
    $name = trim(strtolower($name));
    $name = str_replace(" ", "-", $name);
    return $name;
}

function correctAsinForImages($asin)
{
    $asin = substr($asin, 0, strrpos($asin, '-'));
    return $asin;
}

function getApiData($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    $response = curl_exec($ch);
    if (!$response)
    {
        return false;
    }
    return $shopifyData = json_decode($response, 1);
}

function getDirectoryFiles($directory)
{
    $scanned_directory = array();

    foreach (new DirectoryIterator($directory) as $file) {
        if ($file->isFile()) {
            if (strpos($file->getFilename(), ".xml")) {
                $scanned_directory[] = $file->getFilename();
            }
        }
    }
    return $scanned_directory;
}

function convert_bytes_to_specified($bytes, $to, $decimalPlaces = 0) 
{ 
    $formulas = array(
        'K' => number_format( $bytes / 1000, $decimalPlaces ), 
        'M' => number_format( $bytes / 1000000, $decimalPlaces ),
        'G' => number_format( $bytes / 1000000000, $decimalPlaces )
        );
    return isset($formulas[$to]) ? $formulas[$to] : 0;
}

function WriteDataFile($file, $result)
{
    $dataFile = fopen ($file,'w');
    fwrite ($dataFile, $result);
    fclose ($dataFile);
}

function getXMLContent($xmlFilePath)
{
    //file content
    $fileContent = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode(file_get_contents($xmlFilePath)));

    try{
             //load xml
            $fileContentObject = simplexml_load_string($fileContent, 'SimpleXmlElement', LIBXML_NOERROR + LIBXML_ERR_FATAL + LIBXML_ERR_NONE);
            //check if XML is valid
            if (false === $fileContentObject) {
                return false;
            }
            //converting to array
            $fileContentArray = json_decode(json_encode($fileContentObject), 1);
            return $fileContentArray;

        }   catch( Exception $e )
        {
            // pr( $e); die("**");
        }

}


function getJobUserData($jobUserFields, $attributeIndex)
{
    foreach ($jobUserFields as $key => $jobUserField)
    {
        if (isset($jobUserField['@attributes']['index']) && $jobUserField['@attributes']['index'] == $attributeIndex)
        {
            return trim($jobUserField['Value']);
        }
    }
}

function MHzToGHz($speed)
{
    //check if in MHz
    $get_ex = explode(" ", html_entity_decode($speed));
    if (isset($get_ex[1]) && $get_ex[1] == 'MHz')
    {
        $convrt_GHz = $get_ex[0] / 1000;
        $speed = round($convrt_GHz, 1);
        $speed = $speed + 0;
        $speed = $speed . " GHz";
    }
    elseif (strpos($speed, 'MHz'))
    {
        $spd = chop($speed, 'MHz');
        $convrt_GHz = $spd / 1000;
        $speed = round($convrt_GHz, 1);
        $speed = $speed + 0;
        $speed = $speed . " GHz";
    }
    else
    {
        $speed = $speed;
    }
    return $speed;
}

//Coustomise  data
function getCustomizeData($data)
{
    $i = 0;
    if (isset($data[0]))
    {
        foreach ($data as $key => $value)
        {
            $customData[$i] = $value;
            $i++;
        }
    }
    else
    {
        $customData[$i] = $data;
    }
    return $customData;
}

function getHDDInterfaceOld($devices)
{
    $devicesByInterfaces = array();
    $interfaceCounter = array();
    if (isset($devices['Device'][0]))
    {
        foreach ($devices['Device'] as $key => $device)
        {
            if (isset($device['Interface']) && !empty($device['Interface']))
            {

                if (isset($devicesByInterfaces[$device['Interface']]))
                {
                    $currentCount = count($devicesByInterfaces[$device['Interface']]) + 1;
                }
                else
                {
                    $currentCount = 1;
                }
                $interfaceCounter[$device['Interface']] = $currentCount;
                $devicesByInterfaces[$device['Interface']][] = 1;
            }
        }
    }
    else
    {
        if (isset($devices['Device']['Interface']) && !empty($devices['Device']['Interface']))
        {
            return $devices['Device']['Interface'];
        }
    }

    if (!empty($devicesByInterfaces)) {
        return array_search(max($interfaceCounter), $interfaceCounter);
    } else {
        return;
    }
}

function getMakorSmartAttribute($jobOperationData)
{
    $smartData = "";
    if (isset($jobOperationData['@attributes']))
    {
        if (isset($jobOperationData['@attributes']['DeviceIndex']) && $jobOperationData['@attributes']['DeviceIndex'] == 1)
        {
            foreach ($jobOperationData['PreWipeSmartAttributes']['SMARTAttribute'] as $key => $value)
            {
                if ($value['ID'] == 9)
                {
                    $smartData = $value['RawValue'];
                    break;
                }
            }
        }
    }
    return $smartData;
}

function getJobOprationServiceParfrmed($jobUserFields, $attributeIndex)
{
    foreach ($jobUserFields as $key => $jobUserField)
    {
        if (isset($jobUserField['@attributes']['DeviceIndex']) && $jobUserField['@attributes']['DeviceIndex'] == $attributeIndex)
        {
            return trim($jobUserField['Method']);
        }
    }
}

function getMakorServiecsQueueStatus($jobOperationDataArray, $productName)
{
    $smartData = "";
    if ($productName == "Server")
    {
        foreach ($jobOperationDataArray as $key => $jobOperationData)
        {
            if (isset($jobOperationData['@attributes']))
            {
                if (isset($jobOperationData['@attributes']['DeviceIndex']) && $jobOperationData['@attributes']['DeviceIndex'] == 1)
                {
                    $actionresult = $jobOperationData['ActionResult'];

                    if (strtolower($actionresult) == "failure")
                    {
                        $smartData = "Failure";
                    }
                    elseif (strtolower($actionresult) == "success")
                    {
                        $smartData = "Success";
                    }
                }
            }
        }
    }
    else
    {
        if (isset($jobOperationDataArray['@attributes']))
        {
            if (isset($jobOperationDataArray['@attributes']['DeviceIndex']) && $jobOperationDataArray['@attributes']['DeviceIndex'] == 1)
            {
                $actionresult = $jobOperationDataArray['ActionResult'];
                if (strtolower($actionresult) == "failure")
                {
                    $smartData = "Failure";
                }
                elseif (strtolower($actionresult) == "success")
                {
                    $smartData = "Success";
                }
            }
        }
    }

    return $smartData;
}

//this checks fr 
function getHDDInterface($devices)
{
    $deviceInterface = "";

    if (empty($devices) || !isset($devices['Device']))
    {
        return $deviceInterface;
    }

    if (isset($devices['Device'][0]))
    {
        foreach ($devices['Device'] as $key => $device)
        {
            $device = $device;
            break;
        }
    }
    else
    {
        $device = $devices['Device'];
    }

    if (isset($device['IsSSD']) && $device['IsSSD'] == 'Yes')
    {
        $deviceInterface = "SSD";
    }
    elseif (!isset($device['IsSSD']) || (isset($device['IsSSD']) && $device['IsSSD'] == 'No'))
    {
        $device['Gigabytes'] = (int) $device['Gigabytes'];

        if ($device['Gigabytes'] == '120')
        {
            $deviceInterface = "SSD";
        }
        elseif ($device['Gigabytes'] == '128') 
        {
            $deviceInterface = "SSD";
        }
        elseif ($device['Gigabytes'] == '240')
        {
            $deviceInterface = "SSD";
        }
        elseif ($device['Gigabytes'] == '256')
        {
            $deviceInterface = "SSD";
        }
        elseif ($device['Gigabytes'] == '512')
        {
            $deviceInterface = "SSD";
        }
        else
        {
            $deviceInterface = "SATA";
        }
    }
    return $deviceInterface;
}

function MBToGB($speed)
{
    //check if in MHz
    $getEx = explode(" ", $speed);
    if (isset($getEx[1]) && $getEx[1] == 'MB')
    {
        $convrtGb = $getEx[0] / 1000;
        $speed = round($convrtGb, 0) . "GB";
    }
    else
    {
        $speed = $speed;
    }
    return $speed;
}

function getRAMString($RAM) 
{
    //covert to GB
    if (strpos($RAM['TotalCapacity'], 'GiB') || strpos($RAM['TotalCapacity'], 'GB'))
    {
        $ramString = (int) $RAM['TotalCapacity'];
    }
    else
    {
        $ramString = (int) $RAM['TotalCapacity'] / 1024;
    }
    $ramString .= "GB:";
    $ramSizes = array();
    if (isset($RAM['Stick'][0]))
    {
        foreach ($RAM['Stick'] as $key => $stick)
        {
            if (strpos(strtolower($stick['Capacity']), 'gb'))
            {
                $stickCapacity = (int) $stick['Capacity'];
            }
            else
            {
                $stickCapacity = (int) $stick['Capacity'] / 1024;
            }
            $stickCapacityGb = $stickCapacity . "GB";
            $ramSizes[$stickCapacityGb][] = $key;
        }
    }
    elseif (isset($RAM['Stick']))
    {
        if (strpos(strtolower($RAM['Stick']['Capacity']), 'gb'))
        {
            $stickCapacity = (int) $RAM['Stick']['Capacity'];
        }
        else
        {
            $stickCapacity = (int) $RAM['Stick']['Capacity'] / 1024;
        }
        $stickCapacityGb = $stickCapacity . "GB";
        $ramSizes[$stickCapacityGb][] = 1;
    }
    elseif (isset($RAM['TotalCapacity']))
    {
        $ramSizes[$RAM['TotalCapacity']][] = 1;
        $count = 1;
        $totalRamSizes = count($ramSizes);
        foreach ($ramSizes as $key => $ramSize)
        {
            if ($count == 1) 
            {
                $ramString .= "_";
            }

            $ramString .= $key . "_x_" . count($ramSize);
            if ($count < $totalRamSizes)
            {
                $ramString .= ";";
            }
            $count++;
        }
        $ramString = str_replace("GiB", "GB", $ramString);
        return $ramString;
    }
}

function getMakorRAMType($RAM)
{
    $ramType = "";
    if (isset($RAM['Type']))
    {
        $ramType = $RAM['Type'];
    }
    elseif (isset($RAM['Stick']))
    {
        $ramType = $RAM['Stick']['Type'];
    }
    elseif (isset($RAM['Description']))
    {
        if (strpos($RAM['Description'], 'DDR4') !== FALSE)
        {
            $ramType = 'DDR4';
        }
        elseif (strpos($RAM['Description'], 'DDR3') !== FALSE)
        {
            $ramType = 'DDR3';
        }
        elseif (strpos($RAM['Description'], 'DDR2') !== FALSE)
        {
            $ramType = 'DDR2';
        }
        elseif (strpos($RAM['Description'], 'DDR') !== FALSE)
        {
            $ramType = 'DDR';
        }
    }
    return $ramType;
}

function getMakorRAMSpeed($RAM)
{
    if (isset($RAM['Type']) && isset($RAM['Speed']))
    {
        $ramType = $RAM['Type'];
        $ramSpeed = $RAM['Speed'];
    }
    elseif (isset($RAM['Stick']))
    {
        $ramType = $RAM['Stick']['Type'];
        $ramSpeed = $RAM['Stick']['Speed'];
    }
    elseif (isset($RAM['Description']))
    {
        if (strpos($RAM['Description'], 'DDR4') !== FALSE)
        {
            $ramType = 'DDR4';
        }
        elseif (strpos($RAM['Description'], 'DDR3') !== FALSE)
        {
            $ramType = 'DDR3';
        }
        elseif (strpos($RAM['Description'], 'DDR2') !== FALSE)
        {
            $ramType = 'DDR2';
        }
        elseif (strpos($RAM['Description'], 'DDR') !== FALSE)
        {
            $ramType = 'DDR';
        }

        if (isset($ramType))
        {
            preg_match('/(\d+)\s?MHz/i', $RAM['Description'], $matches);
            if (!empty($matches) && isset($matches[1]))
            {
                $ramSpeed = $matches[1];
            }
        }
    }

    if (isset($ramType) && !empty($ramType) && isset($ramSpeed) && !empty($ramSpeed))
    {
        $ramSpeed = preg_replace("/[^0-9]/", "", $ramSpeed);
        $ramSpeed = trim($ramSpeed);
        if ($ramType == 'DDR')
        {
            if ($ramSpeed >= 200 && $ramSpeed <= 250)
            {
                return '1600';
            }
            elseif ($ramSpeed >= 266 && $ramSpeed <= 290)
            {
                return '2100';
            }
            elseif ($ramSpeed >= 300 && $ramSpeed <= 325)
            {
                return '2400';
            }
            elseif ($ramSpeed >= 333 && $ramSpeed <= 350)
            {
                return '2700';
            }
            elseif ($ramSpeed >= 400 && $ramSpeed <= 450)
            {
                return '3200';
            }
            elseif ($ramSpeed == 667)
            {
                return '5300';
            }
            elseif ($ramSpeed == 533)
            {
                return '4200';
            }
        } 
        elseif ($ramType == 'DDR2')
        {
            if ($ramSpeed >= 400 && $ramSpeed <= 500)
            {
                return '3200';
            }
            elseif ($ramSpeed >= 533 && $ramSpeed <= 600)
            {
                return '4200';
            }
            elseif ($ramSpeed >= 667 && $ramSpeed <= 700)
            {
                return '5300';
            }
            elseif ($ramSpeed >= 800 && $ramSpeed <= 900)
            {
                return '6400';
            }
            elseif ($ramSpeed >= 1066 && $ramSpeed <= 1100)
            {
                return '8500';
            }
        }
        elseif ($ramType == 'DDR3')
        {
            if ($ramSpeed >= 800 && $ramSpeed <= 900)
            {
                return '6400';
            }
            elseif ($ramSpeed >= 1066 && $ramSpeed <= 1300)
            {
                return '8500';
            }
            elseif ($ramSpeed >= 1333 && $ramSpeed <= 1500)
            {
                return '10600';
            }
            elseif ($ramSpeed >= 1600 && $ramSpeed <= 1800)
            {
                return '12800';
            }
            elseif ($ramSpeed >= 1866 && $ramSpeed <= 2000)
            {
                return '14900';
            }
            elseif ($ramSpeed >= 2133 && $ramSpeed <= 2200)
            {
                return '17000';
            }
            elseif ($ramSpeed == 1067)
            {
                return '8500';
            }
            elseif ($ramSpeed == 1333)
            {
                return '10600';
            }
        }
        elseif ($ramType == 'DDR4')
        {
            if ($ramSpeed >= 2133 && $ramSpeed <= 2300)
            {
                return '17000';
            }
            elseif ($ramSpeed >= 2400 && $ramSpeed <= 2500)
            {
                return '19200';
            }
            elseif ($ramSpeed >= 2666 && $ramSpeed <= 2700)
            {
                return '20800';
            }
            elseif ($ramSpeed >= 2800 && $ramSpeed <= 2900)
            {
                return '22400';
            }
            elseif ($ramSpeed >= 3000 && $ramSpeed <= 3100)
            {
                return '24000';
            }
            elseif ($ramSpeed >= 3200 && $ramSpeed <= 3300)
            {
                return '25600';
            }
        }
    }
    return '';
}

function getMakorActionResult($jobOperationDataArray, $productName)
{
    $smartData = "";
    if ($productName == "Server")
    {
        foreach ($jobOperationDataArray as $key => $jobOperationData)
        {
            if (isset($jobOperationData['@attributes']))
            {
                if (isset($jobOperationData['@attributes']['DeviceIndex']) && $jobOperationData['@attributes']['DeviceIndex'] == 1)
                {
                    $smartData = $jobOperationData['ActionResult'];
                }
            }
        }
    }
    else
    {
        if (isset($jobOperationDataArray['@attributes']))
        {
            if (isset($jobOperationDataArray['@attributes']['DeviceIndex']) && $jobOperationDataArray['@attributes']['DeviceIndex'] == 1)
            {
                $smartData = $jobOperationDataArray['ActionResult'];
            }
        }
    }
    return $smartData;
}

function getHDDString($devices)
{
    $devicesBySizes = array();
    if (isset($devices['Device'][0])) 
    {
        foreach ($devices['Device'] as $key => $device)
        {
            if ($device['Gigabytes'] > 999)
            {
                $device['Gigabytes'] = round($device['Gigabytes'] / 1000, 0);
                $devicesBySizes[$device['Gigabytes'] . "TB"][] = $key;
            }
            else
            {
                $device['Gigabytes'] = round($device['Gigabytes'], 0);
                $devicesBySizes[$device['Gigabytes'] . "GB"][] = $key;
            }
        }
    }
    else
    {
        if ($devices['Device']['Gigabytes'] > 999)
        {
            $devices['Device']['Gigabytes'] = round($devices['Device']['Gigabytes'] / 1000, 0);
            $devicesBySizes[$devices['Device']['Gigabytes'] . "TB"][] = 1;
        }
        else
        {
            $devices['Device']['Gigabytes'] = round($devices['Device']['Gigabytes'], 0);
            $devicesBySizes[$devices['Device']['Gigabytes'] . "GB"][] = 1;
        }
    }
    $hddString = "";
    $count = 1;
    $totalDevicesBySizes = count($devicesBySizes);
    foreach ($devicesBySizes as $key => $deviceSize)
    {
        $hddString .= $key . "_x_" . count($deviceSize);
        if ($count < $totalDevicesBySizes)
        {
            $hddString .= ";";
        }
        $count++;
    }
    return $hddString;
}

function getBiosData($data)
{
    $apiDataArray = [];
    if (isset($data['node']['node']))
    {
        $data = $data['node']['node'];
        foreach ($data as $key => $value)
        {
            $apiDataArray[$value['@attributes']['class']][] = $value;
        }
    }
    elseif (isset($data['node'][0]['node']))
    {
        $data = $data['node'][0]['node'];
        foreach ($data as $key => $value)
        {
            $apiDataArray[$value['@attributes']['class']][] = $value;
        }
    }
    else
    {
        $data = $data['node'];
        foreach ($data as $key => $value)
        {
            $apiDataArray[$value['@attributes']['class']][] = $value;
        }
    }
    return $apiDataArray;
}

function BToGB($speed)
{
    $convrt_GB = $speed * 0.000000001;
    $speed = round($convrt_GB, 0);
    return $speed;
}

function HzToMHz($speed)
{
    //check if in MHz
    $convrt_GHz = $speed * 0.000001;
    $speed = round($convrt_GHz, 0) . "MHz";
    return $speed;
}

function getBiosRAMSpeed($ramType, $ramSpeed)
{
    if (isset($ramType) && !empty($ramType) && isset($ramSpeed) && !empty($ramSpeed))
    {
        $ramSpeed = preg_replace("/[^0-9]/", "", $ramSpeed);
        $ramSpeed = trim($ramSpeed);
        if ($ramType == 'DDR')
        {
            if ($ramSpeed >= 200 && $ramSpeed <= 250)
            {
                return '1600';
            }
            elseif ($ramSpeed >= 266 && $ramSpeed <= 290)
            {
                return '2100';
            }
            elseif ($ramSpeed >= 300 && $ramSpeed <= 325)
            {
                return '2400';
            }
            elseif ($ramSpeed >= 333 && $ramSpeed <= 350)
            {
                return '2700';
            }
            elseif ($ramSpeed >= 400 && $ramSpeed <= 450)
            {
                return '3200';
            }
            elseif ($ramSpeed == 667)
            {
                return '5300';
            }
            elseif ($ramSpeed == 533)
            {
                return '4200';
            }
        }
        elseif ($ramType == 'DDR2')
        {
            if ($ramSpeed >= 400 && $ramSpeed <= 500)
            {
                return '3200';
            }
            elseif ($ramSpeed >= 533 && $ramSpeed <= 600)
            {
                return '4200';
            }
            elseif ($ramSpeed >= 667 && $ramSpeed <= 700)
            {
                return '5300';
            }
            elseif ($ramSpeed >= 800 && $ramSpeed <= 900)
            {
                return '6400';
            }
            elseif ($ramSpeed >= 1066 && $ramSpeed <= 1100)
            {
                return '8500';
            }
        }
        elseif ($ramType == 'DDR3')
        {
            if ($ramSpeed >= 800 && $ramSpeed <= 900)
            {
                return '6400';
            }
            elseif ($ramSpeed >= 1066 && $ramSpeed <= 1300)
            {
                return '8500';
            }
            elseif ($ramSpeed >= 1333 && $ramSpeed <= 1500)
            {
                return '10600';
            }
            elseif ($ramSpeed >= 1600 && $ramSpeed <= 1800)
            {
                return '12800';
            }
            elseif ($ramSpeed >= 1866 && $ramSpeed <= 2000)
            {
                return '14900';
            }
            elseif ($ramSpeed >= 2133 && $ramSpeed <= 2200)
            {
                return '17000';
            }
            elseif ($ramSpeed == 1067)
            {
                return '8500';
            }
            elseif ($ramSpeed == 1333)
            {
                return '10600';
            }
        }
        elseif ($ramType == 'DDR4')
        {
            if ($ramSpeed >= 2133 && $ramSpeed <= 2300)
            {
                return '17000';
            }
            elseif ($ramSpeed >= 2400 && $ramSpeed <= 2500)
            {
                return '19200';
            }
            elseif ($ramSpeed >= 2666 && $ramSpeed <= 2700)
            {
                return '20800';
            }
            elseif ($ramSpeed >= 2800 && $ramSpeed <= 2900)
            {
                return '22400';
            }
            elseif ($ramSpeed >= 3000 && $ramSpeed <= 3100)
            {
                return '24000';
            }
            elseif ($ramSpeed >= 3200 && $ramSpeed <= 3300)
            {
                return '25600';
            }
        }
        elseif ($ramType == 'LPDDR3')
        {
            if ($ramSpeed >= 800 && $ramSpeed <= 900)
            {
                return 'PC3-6400';
            }
            elseif ($ramSpeed >= 1066 && $ramSpeed <= 1300)
            {
                return 'PC3-8500';
            }
            elseif ($ramSpeed >= 1333 && $ramSpeed <= 1500)
            {
                return 'PC3-10600';
            }
            elseif ($ramSpeed >= 1600 && $ramSpeed <= 1800)
            {
                return 'PC3-12800';
            }
            elseif ($ramSpeed >= 1867 && $ramSpeed <= 2000)
            {
                return 'PC3-14900';
            }
            elseif ($ramSpeed >= 2133 && $ramSpeed <= 2200)
            {
                return 'PC3-17000';
            }
            elseif ($ramSpeed == 1067)
            {
                return 'PC3-8500';
            }
            elseif ($ramSpeed == 1333)
            {
                return 'PC3-10600';
            }
        }
    }
    return '';
}

function getCore($data)
{
    foreach ($data as $key => $value)
    {
        if ($value['@attributes']['id'] == 'cores')
        {
            $cores = $value['@attributes']['value'];
            break;
        }
    }
    return $cores;
}

function getOpticle($data)
{
    foreach ($data as $value)
    {
        if (($value['@attributes']['id'] == 'scsi:1' && $value['@attributes']['class'] == 'storage') || ($value['@attributes']['id'] == 'scsi' && $value['@attributes']['class'] == 'storage'))
        {
            if ($value['node']['@attributes']['id'] == "cdrom" && $value['node']['@attributes']['class'] == "disk")
            {
                $opticle = $value['node']['product'];
                break;
            }
        }
    }
    return $opticle;
}

function getMakorRAMString($ram)
{
    $ramString = "";
    foreach ($ram as $key => $value)
    {
        $stickCapacity = (int) $value;
        $stickCapacityGB = $stickCapacity . "GB";
        $ramSizes[$stickCapacityGB][] = $key;
        $ramString += (int) $value;
    }
    $ramString .= "GB:";
    $count = 1;
    $totalRamSizes = count($ramSizes);
    foreach ($ramSizes as $key => $ramSize)
    {
        if ($count == 1)
        {
            $ramString .= "_";
        }
        $ramString .= $key . "_x_" . count($ramSize);
        if ($count < $totalRamSizes)
        {
            $ramString .= ";";
        }
        $count++;
    }
    return $ramString;
}

function getBiosLaptopData($data)
{
    $apiDataArray = [];
    if (isset($data['node']['node']))
    {
        $data = $data['node']['node'];
        foreach ($data as $key => $value)
        {
            $apiDataArray[$value['@attributes']['class']][] = $value;
        }
        return $apiDataArray;
    }
    else
    {
        $data = $data['node'];
        foreach ($data as $key => $value)
        {
            $apiDataArray1[$value['@attributes']['class']][] = $value;
        }
    }
    foreach ($apiDataArray1['bus'] as $key => $value)
    {
        foreach ($value['node'] as $key => $value)
        {
            $apiDataArray[$value['@attributes']['class']][] = $value;
        }
    }
    return $apiDataArray;
}

function recycleTwocategoryName($id)
{
    $value = 'N/A';
    if($id)
    {
        $value = App\Category::getCategoryName(intval($id));
    }
    return $value;
}