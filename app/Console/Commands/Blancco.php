<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use Orchestra\Parser\Xml\Facade as XmlParser;

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
     * Execute the console command .
     *
     * @return mixed
     */ 
    public function handle()
    {
        $this->basePath  = base_path().'/public/blancco/';
        // $this->GetAllDataFileBlancco();
        $this->GetSingleXmlAndPdfFIlesBlancco();
    }

    /**
     *  Function for reading data.xml file to array to create single pdf and xml blancco request. 
     *
     * @return mixed
     */
    public function GetSingleXmlAndPdfFIlesBlancco()
    {
        $dataFile = $this->basePath . 'xml-data/data.xml';
        if(File::exists($dataFile))
        {
            $fileContent = file_get_contents($dataFile);
            try
            {
           
            }
            catch (\Execption $e)
            {
                echo $e->getMessage().' '. $e->getCode();
            }
        }
    }

    /**
     *  Function for getting all data from blancco.
     *
     * @return mixed
     */
    public function GetAllDataFileBlancco()
    {
        $format = "xml";
        $requestFilePath = $this->basePath . "request-xmls/all-reports.xml";
        $requestFileName = 'all-reports.xml';
        $this->blancooCurlRequest($requestFilePath, $requestFileName, 'fulldata', $format, 'data', 'all');
    }

    /**
     *  Function for doing curl request to blancco that contain request xml file and return result in xml,pdf format.
     *
     * @return mixed
     */
    public function blancooCurlRequest($requestFilePath , $requestFileName , $type , $format , $returnFileName , $reportUuid)
    {
        $url = "https://cloud.blancco.com:443/rest-service/report/export/". $format;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, Config::get('blancco.blanccoApiCredential.apiUsername').':'.Config::get('blancco.blanccoApiCredential.apiPassword'));
        $fields = [
        'xmlRequest' => new \CurlFile($requestFilePath, 'application/xml', $requestFileName)
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            $errTxt = $reportUuid . ' --> ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
            MessageLog::addLogMessageRecord($errTxt, $type="blancco", $status="failure");
        }
        else
        {
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpcode == 200)
            {
                $this->handleBlanccoCurlResponse($result, $type, $returnFileName);
            }
            else
            {
                $errTxt =  $reportUuid." --> ".curl_error($ch)." ".$httpcode;
                MessageLog::addLogMessageRecord($errTxt,$type="blancco", $status="failure");
            }
        }
        curl_close($ch);
    }

   /**
     *  Function for handling response of blancco curl request.
     *
     * @return mixed
     */
   public function handleBlanccoCurlResponse($result, $type, $returnFileName)
   {
        if($type == 'fulldata')
        {
            $xmlFile = $this->basePath . "xml-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($xmlFile, $result); 
            $successTxt =  'All data file Created';
        }
        else if ($type == "singlepdf")
        {
            $pdfFile = $this->basePath . "pdf-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($pdfFile, $result);
            $successTxt =  $reportUuid . ' PDF file created for this reportUuid';
        }
        else if ($type == "singlexml")
        {
            $xmlFile = $this->basePath . "xml-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($xmlFile, $result);
            $successTxt =  $reportUuid . ' XML file created for this reportUuid';
        }
        MessageLog::addLogMessageRecord($successTxt, $type="blancco", $status="success");
    }

   /**
     *  Function for getting all data from blancco.
     *
     * @return mixed
     */
   public function WriteBlancoDataFile($file, $result)
   {
        $dataFile = fopen ($file,'w');
        fwrite ($dataFile, $result);
        fclose ($dataFile);
        return true;
    }
}