<?php 

require_once __DIR__.'/blancco_config.php';     
include __DIR__.'/sabre_xml/vendor/autoload.php';    

// curl request for Blancco api 
function TMdoCurlRequest( $request_filePath , $request_file_name , $type , $format , $return_file_name , $reportUuid ) {  
	$log_file = __DIR__.'/' . BLANCCO_API_LOG_FILE ;     
	$url = "https://cloud.blancco.com:443/rest-service/report/export/". $format;   
	$ch = curl_init();        
	curl_setopt( $ch, CURLOPT_URL, $url );  
	curl_setopt( $ch, CURLOPT_USERPWD, BLANCCO_API_USERNAME.':'.BLANCCO_API_PASSWORD );  
	$fields = [ 
		'xmlRequest' => new \CurlFile( $request_filePath, 'application/xml', $request_file_name )  
	]; 
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields ); 
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );   
	curl_setopt( $ch, CURLOPT_TIMEOUT, 300 ); 
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: multipart/form-data' ) ); 

	$result = curl_exec( $ch );   
	if ( curl_errno( $ch ) ) { 
		$txt = $reportUuid . ' --> ERROR -> ' . curl_errno( $ch ) . ': ' . curl_error( $ch ); 
		file_put_contents( $log_file  , $txt.PHP_EOL , FILE_APPEND | LOCK_EX );       
	} else {   
		$httpcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );   
		if( $httpcode == 200 ) {
			if( $type == 'fulldata' ) {    
				$xml_file = __DIR__.'/' . BLANCCO_XML_DATA_FOLDER . '/'.$return_file_name .'.xml'; 
				$dataxml = fopen ( $xml_file,'w' );  
				fwrite ( $dataxml, $result );
				fclose ( $dataxml ); 
			} else if ( $type == "singlepdf" ) { 
				$pdf_file = __DIR__.'/' . BLANCCO_PDF_FOLDER . '/'.$return_file_name .'.pdf';    
				$datapdf = fopen ($pdf_file,'w');  
				fwrite ( $datapdf, $result);
				fclose ($datapdf);   
			} else if ( $type == "singlexml" ) {  
				$xml_file = __DIR__.'/' . BLANCCO_XML_DATA_FOLDER . '/'.$return_file_name .'.xml';      
				$dataxml = fopen ( $xml_file,'w' );   
				fwrite ( $dataxml, $result );
				fclose ( $dataxml );     
			}
		} else {  
			$txt =  $reportUuid." --> The user has sent too many requests in a given amount of time. HTTP CODE " . $httpcode;
			file_put_contents( $log_file  , $txt . PHP_EOL , FILE_APPEND | LOCK_EX ); 
		} 
	} 
	curl_close( $ch );     
}


// get xml file to array using sabre xml reader library . 
function TMgetXmlFileContent( $filepath ) {   
	if( file_exists ( $filepath ) ) { 
		$xmlstring = file_get_contents( $filepath );     
		$service = new Sabre\Xml\Service();  
		$blanccodata = $service->parse( $xmlstring );  
		return $blanccodata; 
	} 
}     


// for converting bytes to K , M , G byte . 
function TMisa_convert_bytes_to_specified( $bytes, $to, $decimal_places = 0 ) { 
	$formulas = array(
		'K' => number_format( $bytes / 1000, $decimal_places ), 
		'M' => number_format( $bytes / 1000000, $decimal_places ),
		'G' => number_format( $bytes / 1000000000, $decimal_places )
	);
	return isset($formulas[$to]) ? $formulas[$to] : 0;
} 


function TMMakorAPIRequest( $api_data ) {
        //setting the curl parameters. 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_URL, MAKOR_API_URL );
	curl_setopt($ch, CURLOPT_POSTFIELDS, $api_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, MAKOR_API_USERNAME . ":" . MAKOR_API_PASSWORD ); 
	$response = curl_exec( $ch );  
	$response_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE ); 
	curl_close( $ch ); 
	return $response_code; 
}


// for printing data to debug 
function pr( $data ) {
	echo "<pre>";
	print_r( $data ); 
	echo "</pre>";
} 



