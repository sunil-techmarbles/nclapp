<?php

function getManufacturerForNewRunlistdata($db, $series) {
    $series = strtolower($series);
    $manufacturer = array(
        'dell' => 'Dell',
        'inspiron' => 'Dell',
        'latitude' => 'Dell',
        'optiplex' => 'Dell',
        'precision' => 'Dell',
        'vostro' => 'Dell',
        'xps' => 'Dell',
        'hp' => 'HP',
        'elite' => 'HP',
        'elitebook' => 'HP',
        'elitedesk' => 'HP',
        'probook' => 'HP',
        'prodesk' => 'HP',
        'touchsmart' => 'HP',
        'workstation' => 'HP',
        'z200' => 'HP',
        'z210' => 'HP',
        'z400' => 'HP',
        'z440' => 'HP',
        'z820' => 'HP',
        'compaq' => 'HP',
        'lenovo' => 'Lenovo',
        'thinkcentre' => 'Lenovo',
        'thinkpad' => 'Lenovo',
        'apple' => 'Apple',
        'macbook' => 'Apple',
        'imac' => 'Apple',
        'macpro' => 'Apple',
    );
    
    if (isset($manufacturer[$series])) {
        return $manufacturer[$series];
    } else {
        return '';
    }
}
