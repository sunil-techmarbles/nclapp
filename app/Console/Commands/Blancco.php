<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use App\Traits\TMXmlToArrayTraits;

class Blancco extends Command
{
    use TMXmlToArrayTraits;

    public $basePath, $AllDatarequestFilePath, $singleReportRequestFilePath, $singleReportRequestFilePathUpdated;
    public $blanccoXmlDataDir, $blanccoAdditionMobileDataDir;
    public $reportAllowedStates = ['Successful', 'Failed'];

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
    protected $description = 'Blancco api for getting data of mobiles and create pdf and xml files and do makor api request';

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
        $this->basePath  = base_path().'/public';
        $this->AllDatarequestFilePath = $this->basePath . "/blancco/request-xmls/all-reports.xml";
        $this->singleReportRequestFilePath = $this->basePath . "/blancco/request-xmls/single-report.xml";
        $this->singleReportRequestFilePathUpdated = $this->basePath . "/blancco/request-xmls/updated-request.xml";
        $this->blanccoXmlDataDir = $this->basePath . "/blancco/xml-data/";
        $this->blanccoAdditionMobileDataDir = $this->basePath . "/wipe-data-mobile";
        
        // get data form blancco in data.xml file
        $this->GetAllDataFileBlancco();

        // parse data form data.xml and create single single xml and pdf form all data
        $this->GetSingleXmlAndPdfFIlesBlancco();

        // read all the xml files form blancco and create makor request.
        $this->createMakorRequestFromBlanccoData();
        
        die("all files created successfully");
    }

     /**
     *  Function for reading all the xml files form blancco and create makor request. 
     *
     * @return mixed
     */
    public function createMakorRequestFromBlanccoData()
    {
        $blanccoXmlFiles = getDirectoryFiles($this->blanccoXmlDataDir);
        if( is_array($blanccoXmlFiles) && !empty($blanccoXmlFiles))
        {
            foreach ($blanccoXmlFiles as $key => $blanccoXmlFile)
            {
                if (substr($blanccoXmlFile, 0, 4) != "data")
                {
                    $blanccoXmlFilePath = $this->blanccoXmlDataDir . $blanccoXmlFile;
                    $blanccoFileContent = file_get_contents($blanccoXmlFilePath);
                    try
                    {
                        $blanccoData = $this->createArray($blanccoFileContent);
                        if(isset($blanccoData['root']['report']['blancco_data']['description']['document_id']) && !empty($blanccoData['root']['report']['blancco_data']['description']['document_id']))
                        {
                            $reportUuid = $blanccoData['root']['report']['blancco_data']['description']['document_id'];

                            $lotNumber = $assetId = $serial = '';
                            $lotNumber_assetId_serial = pathinfo($blanccoXmlFile, PATHINFO_FILENAME);
                            @list($lotNumber, $assetId, $serial) = explode("-", $lotNumber_assetId_serial);
                            
                            $additionalMobileDataFile = $this->blanccoAdditionMobileDataDir .'/' . $assetId . ".xml";
                            if(!File::exists($additionalMobileDataFile))
                            {
                                $message = $reportUuid . ' --> No Additional xml data file found for this Asset Id : ' . $assetId;
                                MessageLog::addLogMessageRecord($message,$type="blanccoMakor", $status="failure");
                                continue;
                            }

                            // variables form blancco report data .   
                            $manufacturer = $model = $modelNumber = $internalModel = $color = $battery = $hdSerial = '';
                            $hdCapacity = $hdServicesPerformed = $serviceQueueStatus = $osType = $osVersion = $simStatus = '';
                            $MDMStatus = $FMIPStatus = $blacklistStatus = $graylistStatus = $releaseYear = '';

                             // variables from additional data . 
                            $customerAssetTag = $itemNetWeight = $grade = $type = $chargingPort = $displaySize = $displayResolution = '';
                            $displayTouchScreen = $notes = $other = '';
                            $cosmetic = $missing = $functional = $screen = $case = $inputOutput = [];
                           
                            // variables that are fixed .  
                            $pallet = '1-DefaultPallet-I'; $nextProcess = 'Resale';
                            $complianceLabel = 'Tested for Key Functions,  R2/Ready for Resale';
                            $condition = 'Tested Working'; $hdManufacturer = 'Apple';
                            $hdModel = 'N/A'; $hdPartNumber = 'N/A';
                            $hdInterface = 'Properitary'; $hdPowerOnHours = 'N/A';
                            $hdType = 'Onboard'; $carrier = 'N/A';

                            $blanccoHardwareReportData = $blanccoData['root']['report']['blancco_data']['blancco_hardware_report']['entries'];

                            foreach ($blanccoHardwareReportData as $key => $blanccoHardwareReport) {
                                    pr($blanccoHardwareReportData);
                                    
                            }
                        }
                        else
                        {
                            $message = 'No xml data found in this file '.$blanccoXmlFile;
                            MessageLog::addLogMessageRecord($message,$type="blanccoMakor", $status="failure");
                            continue;
                        }
                    }
                    catch (\Execption $e)
                    {
                        $message = $e->getMessage().' '. $e->getCode();
                        MessageLog::addLogMessageRecord($message,$type="blanccoMakor", $status="failure");
                    }
                   
                }
            }
        }
    }

    /**
     *  Function for reading data.xml file to array to create single pdf and xml blancco request.
     *
     * @return mixed
     */
    public function GetSingleXmlAndPdfFIlesBlancco()
    {
        $dataFileBlancco = $this->basePath . '/blancco/xml-data/data.xml';
        if(File::exists($dataFileBlancco))
        {
            $blanccoFileContent = file_get_contents($dataFileBlancco);
            try
            {
                // createArray in TMXmlToArrayTraits for parsing xml data
                $blanccoFullData = $this->createArray($blanccoFileContent);
                foreach ($blanccoFullData['root']['report'] as $blanccoData)
                {
                    $reportUuid = $state = $serial = $lotNumber = $assetId = '';
                    if( isset($blanccoData['blancco_data']['description']['document_id']) && !empty($blanccoData['blancco_data']['description']['document_id']))
                    {
                            $reportUuid = $blanccoData['blancco_data']['description']['document_id'];
                       
                            // for getting state form blancco_erasure_report
                            $blanccoErasureReportData = $blanccoData['blancco_data']['blancco_erasure_report']['entries']['entries']['entry'];
                            $state = $this->GetBlanccoVariable($blanccoErasureReportData, $variableToGet='state', $reportUuid);

                            // for getting serial form blancco_hardware_report
                            $blanccoHardwareReportData = $blanccoData['blancco_data']['blancco_hardware_report']['entries'][0]['entry'];
                            $serial = $this->GetBlanccoVariable($blanccoHardwareReportData, $variableToGet='serial', $reportUuid);

                            // for getting lot number and asset id
                            $blanccoUserReportData = $blanccoData['user_data']['entries']['entry'];
                            $lotNumber = $this->GetBlanccoVariable($blanccoUserReportData, $variableToGet='Lot Number', $reportUuid);
                            $assetId = $this->GetBlanccoVariable($blanccoUserReportData, $variableToGet='Asset ID', $reportUuid);

                            if (in_array($state, $this->reportAllowedStates) && !empty($serial) && !empty($lotNumber) && !empty($assetId))
                            {
                                // getting single pdf file form blancco of spectfic reportUuid
                                $this->GetSingleFileOfReportUuid($serial, $lotNumber, $assetId, $reportUuid, $format="pdf", $type="singlepdf");

                                // getting single xml file form blancco of spectfic reportUuid
                                $this->GetSingleFileOfReportUuid($serial, $lotNumber, $assetId, $reportUuid, $format="xml", $type="singlexml");
                            }
                            else
                            {
                                if(!in_array($state, $this->reportAllowedStates))
                                {
                                   $message = $reportUuid . " --Invalid State value for this Uuid. State : " . $state;
                                   MessageLog::addLogMessageRecord($message,$type="blancco", $status="failure");
                               }
                           }
                    }
                    else
                    {
                        MessageLog::addLogMessageRecord($message='reportUuid Not Exist',$type="blancco", $status="failure");
                    }

                }
            }
            catch (\Execption $e)
            {
                $message = $e->getMessage().' '. $e->getCode();
                MessageLog::addLogMessageRecord($message,$type="blancco", $status="failure");
            }
        }
        return true;
    }

    /**
     *  Function for single pdf file form blancco of spectfic reportUuid .
     *
     * @return mixed
     */
    public function GetSingleFileOfReportUuid($serial, $lotNumber, $assetId, $reportUuid, $format, $type)
    {
        $returnfileName = $lotNumber . "-" . $assetId . "-" . $serial;
        $searches = ['{{id}}'];
        $replacements = [$reportUuid];

        // for getting file of single report Uuid
        $xml_pdf = simplexml_load_file($this->singleReportRequestFilePath);
        $newXml_pdf = simplexml_load_string(str_replace($searches, $replacements, $xml_pdf->asXml()));
        $newXml_pdf->asXml($this->singleReportRequestFilePathUpdated);

        $this->blancooCurlRequest($this->singleReportRequestFilePathUpdated, $requestFileName='updated-request.xml', $type, $format, $returnfileName, $reportUuid);
    }

    /**
     *  Function for getting blancco Variable.
     *
     * @return mixed
     */
    public function GetBlanccoVariable($blanccoReportData, $variableToGet, $reportUuid)
    {
        $result = '';
        if (is_array($blanccoReportData))
        {
            foreach ($blanccoReportData as $key => $data)
            {
                if ($data['@attributes']['name'] == $variableToGet)
                {
                    $result = $data['@value'];
                }
            }
        }
        if(empty($result))
        {
            $message = $variableToGet . ' Not found for '.$reportUuid.' this reportUuid';
            MessageLog::addLogMessageRecord($message,$type="blancco", $status="failure");
        }
        return $result;
    }

    /**
     *  Function for getting all data from blancco.
     *
     * @return mixed
     */
    public function GetAllDataFileBlancco()
    {
        $this->blancooCurlRequest($this->AllDatarequestFilePath, $requestFileName='all-reports.xml', $type='fulldata', $format='xml', $returnFileName='data', $reportUuid='all');
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
                $this->handleBlanccoCurlResponse($result, $type, $returnFileName, $reportUuid);
            }
            else
            {
                $errTxt =  $reportUuid." --> ".curl_error($ch)." ".$httpcode . " To Many Request";
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
   public function handleBlanccoCurlResponse($result, $type, $returnFileName, $reportUuid)
   {
        if($type == 'fulldata')
        {
            $xmlFile = $this->basePath . "/blancco/xml-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($xmlFile, $result); 
            $successTxt =  'All data file Created';
        }
        else if ($type == "singlepdf")
        {
            $pdfFile = $this->basePath . "/blancco/pdf-data/".$returnFileName .'.pdf';
            $this->WriteBlancoDataFile($pdfFile, $result);
            $successTxt =  $reportUuid . ' PDF file created for this reportUuid';
        }
        else if ($type == "singlexml")
        {
            $xmlFile = $this->basePath . "/blancco/xml-data/".$returnFileName .'.xml';
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
    }
}