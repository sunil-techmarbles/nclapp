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
    $allImages = glob(IMAGE_PATH_NEW . $asin . '*');
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
    $searchDataArray['condition'] = CONDITION;
    $searchDataArray['form_factor'] = $data['technology'];
    $searchDataArray['model'] = $data['model'];
    $searchDataArray['cpu_core'] = $data['cpu_core'];
    $searchDataArray['asin'] = $data['asin'];
    $finalPrice = new ShopifyController($searchDataArray);
    return $finalPrice;
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