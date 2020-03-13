<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\MessageLog; 
use Config;

class Blancco extends Command
{
    public $basePath;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blancco:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blancco api for getting data of mobiles and create pdf and xml files for makor api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->basePath  = base_path().'/public/blancco/'; 
        $this->GetAllDataFileBlancco();
    }

    public function GetAllDataFileBlancco()
    {
        $format = "xml";
        $requestFilePath = $this->basePath . "request_xmls/all-reports.xml";
        $requestFileName = 'all-reports.xml';
        $this->blancooCurlRequest($requestFilePath, $requestFileName, 'fulldata', $format, 'data', 'all');
        echo "all data";
    }

    public function blancooCurlRequest($requestFilePath , $requestFileName , $type , $format , $returnFileName , $reportUuid)
    {
        $url = "https://cloud.blancco.com:443/rest-service/report/export/". $format;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_USERPWD, Config::get('blancco.blanccoApiCredential.apiUsername').':'.Config::get('blancco.blanccoApiCredential.apiPassword'));
        $fields = [
            'xmlRequest' => new \CurlFile( $requestFilePath, 'application/xml', $requestFileName )
        ];
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 300 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: multipart/form-data' ) );
        $result = curl_exec( $ch );
        if ( curl_errno( $ch ) )
        {
            $errTxt = $reportUuid . ' --> ERROR -> ' . curl_errno( $ch ) . ': ' . curl_error( $ch );
            MessageLog::AddBlanccoErrorLog($errTxt);
        }
        else
        {
            $httpcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if( $httpcode == 200 )
            {
                if( $type == 'fulldata' )
                {
                    $xmlFile = $this->basePath . "xml_data/".$returnFileName .'.xml';
                    $this->WrightBlancoDataFile($xmlFile, $result);
                }
                else if ( $type == "singlepdf" ) 
                {
                    $pdfFile = $this->basePath . "pdf_data/".$returnFileName .'.xml';
                    $this->WrightBlancoDataFile($pdfFile, $result);
                }
                else if ( $type == "singlexml" ) 
                {
                    $xmlFile = $this->basePath . "xml_data/".$returnFileName .'.xml';
                    $this->WrightBlancoDataFile($xmlFile, $result);
                }
            }
            else
            {
                $errTxt =  $reportUuid." --> The user has sent too many requests in a given amount of time. HTTP CODE " . $httpcode;
                MessageLog::AddBlanccoErrorLog($errTxt);
           }
       }
       curl_close( $ch );
   }

   public function WrightBlancoDataFile($file, $result)
   {
       $dataFile = fopen ( $file,'w' );
       fwrite ( $dataFile, $result );
       fclose ( $dataFile );
       return true; 
   }
}