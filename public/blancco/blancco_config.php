<?php

// for displaying errors 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting( E_ALL );  
 

// Blancco API Creds . 
define( "BLANCCO_API_USERNAME", "manish" );
define( "BLANCCO_API_PASSWORD", "M@nish7777" );    


// Makor API Creds .
define("MAKOR_API_USERNAME","RPrashad");  
define("MAKOR_API_PASSWORD","it1234");
define("MAKOR_API_URL","http://itamgapi.makor-erp.com/api/diagnostics/report"); 


// Blancco api request files folder .
define("BLANCCO_XML_REQUEST_FOLDER",'request_xmls');     


// Blancco reports data pdf folder .
define("BLANCCO_PDF_FOLDER",'pdf_data');      


// Blancco reports data xml folder .
define("BLANCCO_XML_DATA_FOLDER",'xml_data');  


// Executed files of blancco for makor api .
define("EXECUTED_MOBILE_DATA_FOLDER",'makor-processed-data/blancco-mobile-executed/');   
define("EXECUTED_MOBILE_ADDITIONAL_DATA_FOLDER",'makor-processed-data/additional-mobile-executed/');      

// additional wipe mobile data folder .
define("ADDITIONAL_MOBILE_DATA_FOLDER",'wipe-data-mobile');   


// log file path for blancco and makor . 
define("BLANCCO_API_LOG_FILE",'api_logs/blancco_errors.log');  
define("MAKOR_API_LOG_FILE",'api_logs/makor_api_errors.log');