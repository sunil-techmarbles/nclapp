<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use App\Traits\TMXmlToArrayTraits;
use App\Traits\BlanccoMakorMobileTraits;
use Illuminate\Support\Facades\Mail;

class BlanccoMakorApi extends Command
{
    use TMXmlToArrayTraits;
    use BlanccoMakorMobileTraits;

    public $basePath, $blanccoXmlDataDir, $blanccoAdditionMobileDataDir, $blanccoMakorRequestFileDir, $executedFiles;
    public $blanccoMakorProcessedDataDir;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BlanccoMakor:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read Xml files for blancco api, create request form all data and additional data after than do the Makor Api Request.';

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
        // $subject = 'BlanccoMakor:api '. date('Y-m-d h:i:s');
        // $emailsToSend = "sunil.techmarbles@gmail.com";
        // Mail::raw('Test Crons for BlanccoMakor:api', function($m) use ( $subject, $emailsToSend)
        // {
        //         $m->to( $emailsToSend )->subject($subject);
        // });
        $this->basePath  = base_path().'/public';
        $this->blanccoXmlDataDir = $this->basePath . "/blancco/xml-data/";
        $this->blanccoAdditionMobileDataDir = $this->basePath . "/wipe-data-mobile";
        $this->blanccoMakorRequestFileDir = $this->basePath . "/blancco/makor-requests/";
        $this->blanccoMakorProcessedDataDir = $this->basePath . "/makor-processed-data";
 
        // read all the xml files form blancco and create makor request.
        $this->createMakorRequestFromBlanccoData();

        die( $this->executedFiles . " files Successfully exectuted for Blancco Makor api");
    }

    /**
     *  Function for reading all the xml files form blancco and create makor request. 
     *
     * @return mixed
     */
    public function createMakorRequestFromBlanccoData()
    {
        $this->executedFiles = 0;
        $blanccoXmlFiles = getDirectoryFiles($this->blanccoXmlDataDir);
        if( is_array($blanccoXmlFiles) && !empty($blanccoXmlFiles))
        {
            foreach ($blanccoXmlFiles as $key => $blanccoXmlFile)
            {
              
                $apiDataSendToMakor = [];
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

                            $apiDataSendToMakor['assetId'] = $assetId;
                            $apiDataSendToMakor['serial'] = $serial;

                            // variables form blancco report data
                            $manufacturer = $itemModel = $modelNumber = $model = $modelOne = $internalModelRegion = $internalModel = $color = $battery = $hdSerial = '';
                            $hdCapacity = $hdCapacityInBytes = $hdServicesPerformed = $serviceQueueStatus = $osType = $osVersion = $simStatus = $hdRemoved = '';
                            $MDMStatus = $FMIPStatus = $blacklistStatus = $graylistStatus = $releaseYear = '';

                             // variables from additional data
                            $customerAssetTag = $itemNetWeight = $grade = $type = $chargingPort = $displaySize = $displayResolution = '';
                            $displayTouchScreen = $notes = $other = '';
                            $cosmetic = $missing = $functional = $screen = $case = $inputOutput = '';

                            // variables that are fixed
                            $apiDataSendToMakor['pallet'] = '1-DefaultPallet-I'; 
                            $apiDataSendToMakor['nextProcess'] = 'Resale';
                            $apiDataSendToMakor['complianceLabel'] = 'Tested for Key Functions,  R2/Ready for Resale';
                            $apiDataSendToMakor['condition'] = 'Tested Working'; 
                            $apiDataSendToMakor['hdManufacturer'] = 'Apple';
                            $apiDataSendToMakor['hdModel'] = 'N/A'; 
                            $apiDataSendToMakor['hdPartNumber'] = 'N/A';
                            $apiDataSendToMakor['hdInterface'] = 'Properitary'; 
                            $apiDataSendToMakor['hdPowerOnHours'] = 'N/A';
                            $apiDataSendToMakor['hdType'] = 'Onboard'; 
                            $carrierValue = 'N/A';

                            // data get form Hardware data.
                            $blanccoHardwareReportData = $blanccoData['root']['report']['blancco_data']['blancco_hardware_report'];
                            $allBlanccoHardwareReportData = $this->getAllBlanccoHardwareDataArray($blanccoHardwareReportData);
                            
                            $manufacturer = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='manufacturer', $reportUuid);
                            $itemModel = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='name', $reportUuid);
                            $itemModel = (!strpos($itemModel, "GSM")) ? $itemModel : trim(substr($itemModel, 0, strpos($itemModel, "GSM")));
                            $modelNumber = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='a_model_number', $reportUuid);
                            $internalModel = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='internal_model', $reportUuid);
                            $internalModelRegion = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='region', $reportUuid);
                            $hdCapacityInBytes = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='capacity', $reportUuid);
                            $carrier = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='initial_carrier', $reportUuid);

                            $apiDataSendToMakor['manufacturer'] = substr($manufacturer, 0, strpos($manufacturer, ","));
                            $apiDataSendToMakor['model'] = $itemModel.' (' .$modelNumber. ')';
                            $apiDataSendToMakor['modelNumber'] = $internalModel . $internalModelRegion;
                            $apiDataSendToMakor['color'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='device_color', $reportUuid);
                            $apiDataSendToMakor['battery'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='battery_serial', $reportUuid);
                            $apiDataSendToMakor['hdSerial'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='imei', $reportUuid);
                            $apiDataSendToMakor['carrier'] = ( !empty($carrier) ) ? $carrier : $carrierValue;
                            $apiDataSendToMakor['simStatus'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='prolog_simlock', $reportUuid);
                            $apiDataSendToMakor['MDMStatus'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='mdm_status', $reportUuid);
                            $apiDataSendToMakor['FMIPStatus'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='find_my_iphone', $reportUuid);
                            $apiDataSendToMakor['blacklistStatus'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='prolog_blacklisted', $reportUuid);
                            $apiDataSendToMakor['graylistStatus'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='prolog_graylisted', $reportUuid);
                            $apiDataSendToMakor['releaseYear'] = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='manufacturing_date', $reportUuid);
                            $apiDataSendToMakor['hdCapacity'] = convert_bytes_to_specified($hdCapacityInBytes, 'G') .'GB';

                            // data get form Erasure data.
                            $blanccoErasureReportData = $blanccoData['root']['report']['blancco_data']['blancco_erasure_report']['entries']['entries']['entry'];
                            $apiDataSendToMakor['hdServicesPerformed'] = $this->getBlanccoVariable($blanccoErasureReportData, $variableToGet='erasure_standard_name', $reportUuid);
                            $apiDataSendToMakor['serviceQueueStatus'] = $this->getBlanccoVariable($blanccoErasureReportData, $variableToGet='state', $reportUuid);
                            $apiDataSendToMakor['hdRemoved'] = ($serviceQueueStatus == 'Successful') ? 'No' : 'Yes';

                            // data get form Software data.
                            $blanccoSoftWareReportData = $blanccoData['root']['report']['blancco_data']['blancco_software_report']['entries']['entry'];
                            $apiDataSendToMakor['osType'] = $this->getBlanccoVariable($blanccoSoftWareReportData, $variableToGet='name', $reportUuid);
                            $apiDataSendToMakor['osVersion'] = $this->getBlanccoVariable($blanccoSoftWareReportData, $variableToGet='version', $reportUuid);

                            // data get form addit ional blancco data file
                            $blanccoAdditionalFileContent = file_get_contents($additionalMobileDataFile);
                            $blanccoAdditionalData = $this->createArray($blanccoAdditionalFileContent);
                            
                            $apiDataSendToMakor['customerAssetTag'] = (isset($blanccoAdditionalData['data']['Customer_Asset_Tag'])) ? $blanccoAdditionalData['data']['Customer_Asset_Tag'] : '';
                            $apiDataSendToMakor['weight'] = (isset($blanccoAdditionalData['data']['Weight'])) ? $blanccoAdditionalData['data']['Weight'] : '';
                            $apiDataSendToMakor['grade'] = (isset($blanccoAdditionalData['data']['Grade'])) ? $blanccoAdditionalData['data']['Grade'] : '';
                            $apiDataSendToMakor['type'] = (isset($blanccoAdditionalData['data']['Technology'])) ? str_replace('_', ' ', $blanccoAdditionalData['data']['Technology']) : '';
                            $apiDataSendToMakor['chargingPort'] = (isset($blanccoAdditionalData['data']['Charging_Port'])) ? $blanccoAdditionalData['data']['Charging_Port'] : '';
                            $apiDataSendToMakor['displaySize'] = (isset($blanccoAdditionalData['data']['Screen']['Size'])) ? $blanccoAdditionalData['data']['Screen']['Size'] : '';
                            $apiDataSendToMakor['displayResolution'] = (isset($blanccoAdditionalData['data']['Screen']['Resolution'])) ? $blanccoAdditionalData['data']['Screen']['Resolution'] : '';
                            $apiDataSendToMakor['displayTouchScreen'] = (isset($blanccoAdditionalData['data']['Screen']['Touchscreen'])) ? $blanccoAdditionalData['data']['Screen']['Touchscreen'] : '';
                            $apiDataSendToMakor['notes'] = (isset($blanccoAdditionalData['data']['Description']['Notes'])) ? $blanccoAdditionalData['data']['Description']['Notes'] : '';
                            $apiDataSendToMakor['other'] = (isset($blanccoAdditionalData['data']['Description']['Other'])) ? $blanccoAdditionalData['data']['Description']['Other'] : '';
                            $apiDataSendToMakor['cosmetic'] = (isset($blanccoAdditionalData['data']['Description']['Cosmetic'])) ? $blanccoAdditionalData['data']['Description']['Cosmetic'] : '';
                            $apiDataSendToMakor['missing'] = (isset($blanccoAdditionalData['data']['Description']['Missing'])) ? $blanccoAdditionalData['data']['Description']['Missing'] : '';
                            $apiDataSendToMakor['functional'] = (isset($blanccoAdditionalData['data']['Description']['Functional'])) ? $blanccoAdditionalData['data']['Description']['Functional'] : '';
                            $apiDataSendToMakor['screen'] = (isset($blanccoAdditionalData['data']['Description']['Screen'])) ? $blanccoAdditionalData['data']['Description']['Screen'] : '';
                            $apiDataSendToMakor['case'] = (isset($blanccoAdditionalData['data']['Description']['Case'])) ? $blanccoAdditionalData['data']['Description']['Case'] : '';
                            $apiDataSendToMakor['inputOutput'] = (isset($blanccoAdditionalData['data']['Description']['Input_Output'])) ? $blanccoAdditionalData['data']['Description']['Input_Output'] : '';

                            $MakorMobileApiRequestDataXml = $this->createMakorMobileXmlData($apiDataSendToMakor);
                            $MakorResponse = $this->blanccoMakorAPIRequest($MakorMobileApiRequestDataXml, $assetId);

                            if($MakorResponse == 200)
                            {
                                $this->executedFiles++;

                                $RequestFile = $this->blanccoMakorRequestFileDir . $assetId . ".xml";
                                WriteDataFile($RequestFile, $MakorMobileApiRequestDataXml);

                                $destinationBlanccoMobileExecutedFile = $this->blanccoMakorProcessedDataDir . '/blancco-mobile-executed/' . $blanccoXmlFile;
                                rename($blanccoXmlFilePath, $destinationBlanccoMobileExecutedFile);

                                $destinationAdditionalMobileExecutedFile = $this->blanccoMakorProcessedDataDir . '/additional-mobile-executed/' . $assetId . ".xml";
                                rename($additionalMobileDataFile, $destinationAdditionalMobileExecutedFile);
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
                        $message = $e->getMessage().' '. $e->getCode() . " " . $blanccoXmlFile;
                        MessageLog::addLogMessageRecord($message,$type="blanccoMakor", $status="failure");
                        continue;
                    }
                }
            }
        }
    }

    /**
     *  Function for API request to Makor of blancco data. 
     *
     * @return mixed
     */
    public function blanccoMakorAPIRequest($MakorMobileApiRequestDataXml, $assetId)
    {
        $MakorRequestjson = array();
        $MakorRequestjson['asset_id'] = $assetId;
        $MakorRequestjson['asset_report']['report'] = base64_encode($MakorMobileApiRequestDataXml);
        $MakorRequestApiData = json_encode($MakorRequestjson);

        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, Config::get('makor.makorApiCredential.apiUrl'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $MakorRequestApiData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, Config::get('makor.makorApiCredential.apiUsername').":".Config::get('makor.makorApiCredential.apiPassword'));
        $response = curl_exec( $ch );
        $responseCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
        return $responseCode;
    }

    /**
     *  Function for getting all the hardware report data in a single array to get variables. 
     *
     * @return mixed
     */
    public function getAllBlanccoHardwareDataArray($blanccoHardwareReportData)
    {
        $allBlanccoHardwareReport = [];
        if(is_array($blanccoHardwareReportData) && isset($blanccoHardwareReportData['entries']))
        {
            foreach ($blanccoHardwareReportData['entries'] as $blanccoHardwareReport)
            {
                if( isset($blanccoHardwareReport['entries']) && !empty($blanccoHardwareReport['entries']))
                {
                    if( isset($blanccoHardwareReport['entries']['entry']) && !empty($blanccoHardwareReport['entries']['entry']))
                    {
                        foreach ($blanccoHardwareReport['entries']['entry'] as $hardwareReport)
                        {
                            $allBlanccoHardwareReport[] = $hardwareReport;
                        }
                    }
                    else
                    {
                        foreach ($blanccoHardwareReport['entries'] as $hardwareReport)
                        {
                            if(isset($hardwareReport['entry']) && !empty( $hardwareReport['entry']))
                            {
                                foreach ($hardwareReport['entry'] as $hdReportValues)
                                {
                                    $allBlanccoHardwareReport[] = $hdReportValues;
                                }
                            }
                        }
                    }
                }
                else if (isset( $blanccoHardwareReport['entry']))
                {
                    foreach ($blanccoHardwareReport['entry'] as $key => $hardwareReport)
                    {
                        $allBlanccoHardwareReport[] = $hardwareReport;
                    }
                }
            }
        }
        return $allBlanccoHardwareReport;
    }

    /**
     *  Function for getting blancco Variable.
     *
     * @return mixed
     */
    public function getBlanccoVariable($blanccoReportData, $variableToGet, $reportUuid)
    {
        $result = '';
        if (is_array($blanccoReportData))
        {
            foreach ($blanccoReportData as $key => $data)
            {
                if ($data['@attributes']['name'] == $variableToGet)
                {
                    $result = trim($data['@value']);
                }
            }
        }
        if(empty($result))
        {
            $message = $variableToGet . ' Not found for '.$reportUuid.' this reportUuid';
            MessageLog::addLogMessageRecord($message,$type="blanccoMakor", $status="failure");
        }
        return $result;
    }
}