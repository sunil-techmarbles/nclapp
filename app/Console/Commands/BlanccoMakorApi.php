<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use App\Traits\TMXmlToArrayTraits;

class BlanccoMakorApi extends Command
{
    use TMXmlToArrayTraits;

    public $basePath, $blanccoXmlDataDir, $blanccoAdditionMobileDataDir;
    
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
        $this->basePath  = base_path().'/public';
        $this->blanccoXmlDataDir = $this->basePath . "/blancco/xml-data/";
        $this->blanccoAdditionMobileDataDir = $this->basePath . "/wipe-data-mobile";

        // read all the xml files form blancco and create makor request.
        $this->createMakorRequestFromBlanccoData();

        die("Blancco Makor api done");
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
                                // $message = $reportUuid . ' --> No Additional xml data file found for this Asset Id : ' . $assetId;
                                // MessageLog::addLogMessageRecord($message,$type="blanccoMakor", $status="failure");
                                continue;
                            }

                            // variables form blancco report data
                            $manufacturer = $itemModel = $modelNumber = $model = $modelOne = $internalModelRegion = $internalModel = $color = $battery = $hdSerial = '';
                            $hdCapacity = $hdCapacityInBytes = $hdServicesPerformed = $serviceQueueStatus = $osType = $osVersion = $simStatus = $hdRemoved = '';
                            $MDMStatus = $FMIPStatus = $blacklistStatus = $graylistStatus = $releaseYear = '';

                             // variables from additional data
                            $customerAssetTag = $itemNetWeight = $grade = $type = $chargingPort = $displaySize = $displayResolution = '';
                            $displayTouchScreen = $notes = $other = '';
                            $cosmetic = $missing = $functional = $screen = $case = $inputOutput = '';

                            // variables that are fixed
                            $pallet = '1-DefaultPallet-I'; $nextProcess = 'Resale';
                            $complianceLabel = 'Tested for Key Functions,  R2/Ready for Resale';
                            $condition = 'Tested Working'; $hdManufacturer = 'Apple';
                            $hdModel = 'N/A'; $hdPartNumber = 'N/A';
                            $hdInterface = 'Properitary'; $hdPowerOnHours = 'N/A';
                            $hdType = 'Onboard'; $carrier = 'N/A';

                            // data get form Hardware data.
                            $blanccoHardwareReportData = $blanccoData['root']['report']['blancco_data']['blancco_hardware_report'];
                            $allBlanccoHardwareReportData = $this->getAllBlanccoHardwareDataArray($blanccoHardwareReportData);
                            
                            $manufacturer = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='manufacturer', $reportUuid);
                            
                            $itemModel = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='name', $reportUuid);
                            $itemModel = (!strpos($itemModel, "GSM")) ? $itemModel : substr($itemModel, 0, strpos($itemModel, "GSM"));
                            $modelNumber = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='a_model_number', $reportUuid);

                            $internalModel = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='internal_model', $reportUuid);
                            $internalModelRegion = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='region', $reportUuid);

                            $hdCapacityInBytes = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='capacity', $reportUuid);

                            $manufacturer = substr($manufacturer, 0, strpos($manufacturer, ","));
                            $model = $itemModel.' (' .$modelNumber. ')';
                            $modelOne = $internalModel . $internalModelRegion;
                            $color = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='device_color', $reportUuid);
                            $battery = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='battery_serial', $reportUuid);
                            $hdSerial = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='imei', $reportUuid);
                            $carrier = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='initial_carrier', $reportUuid);
                            $simStatus = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='prolog_simlock', $reportUuid);
                            $MDMStatus = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='mdm_status', $reportUuid);
                            $FMIPStatus = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='find_my_iphone', $reportUuid);
                            $blacklistStatus = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='prolog_blacklisted', $reportUuid);
                            $graylistStatus = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='prolog_graylisted', $reportUuid);
                            $releaseYear = $this->getBlanccoVariable($allBlanccoHardwareReportData, $variableToGet='manufacturing_date', $reportUuid);
                            $hdCapacity = convert_bytes_to_specified($hdCapacityInBytes, 'G');

                            // data get form Erasure data.
                            $blanccoErasureReportData = $blanccoData['root']['report']['blancco_data']['blancco_erasure_report']['entries']['entries']['entry'];
                            $hdServicesPerformed = $this->getBlanccoVariable($blanccoErasureReportData, $variableToGet='erasure_standard_name', $reportUuid);
                            $serviceQueueStatus = $this->getBlanccoVariable($blanccoErasureReportData, $variableToGet='state', $reportUuid);
                            $hdRemoved = ($serviceQueueStatus == 'Successful') ? 'No' : 'Yes';

                            // data get form Software data.
                            $blanccoSoftWareReportData = $blanccoData['root']['report']['blancco_data']['blancco_software_report']['entries']['entry'];
                            $osType = $this->getBlanccoVariable($blanccoSoftWareReportData, $variableToGet='name', $reportUuid);
                            $osVersion = $this->getBlanccoVariable($blanccoSoftWareReportData, $variableToGet='version', $reportUuid);

                            // data get form additional blancco data file 
                            $blanccoAdditionalFileContent = file_get_contents($additionalMobileDataFile);
                            $blanccoAdditionalData = $this->createArray($blanccoAdditionalFileContent);
                            
                            $customerAssetTag = (isset($blanccoAdditionalData['data']['Customer_Asset_Tag'])) ? $blanccoAdditionalData['data']['Customer_Asset_Tag'] : '';
                            $itemNetWeight = (isset($blanccoAdditionalData['data']['Weight'])) ? $blanccoAdditionalData['data']['Weight'] : '';
                            $grade = (isset($blanccoAdditionalData['data']['Grade'])) ? $blanccoAdditionalData['data']['Grade'] : '';
                            $type = (isset($blanccoAdditionalData['data']['Technology'])) ? $blanccoAdditionalData['data']['Technology'] : '';
                            $chargingPort = (isset($blanccoAdditionalData['data']['Charging_Port'])) ? $blanccoAdditionalData['data']['Charging_Port'] : '';
                            $displaySize = (isset($blanccoAdditionalData['data']['Screen']['Size'])) ? $blanccoAdditionalData['data']['Screen']['Size'] : '';
                            $displayResolution = (isset($blanccoAdditionalData['data']['Screen']['Resolution'])) ? $blanccoAdditionalData['data']['Screen']['Resolution'] : '';
                            $displayTouchScreen = (isset($blanccoAdditionalData['data']['Screen']['Touchscreen'])) ? $blanccoAdditionalData['data']['Screen']['Touchscreen'] : '';

                            $notes = (isset($blanccoAdditionalData['data']['Description']['Notes'])) ? $blanccoAdditionalData['data']['Description']['Notes'] : ''; 
                            $other = (isset($blanccoAdditionalData['data']['Description']['Other'])) ? $blanccoAdditionalData['data']['Description']['Other'] : '';
                            $cosmetic = (isset($blanccoAdditionalData['data']['Description']['Cosmetic'])) ? $blanccoAdditionalData['data']['Description']['Cosmetic'] : ''; 
                            $missing = (isset($blanccoAdditionalData['data']['Description']['Missing'])) ? $blanccoAdditionalData['data']['Description']['Missing'] : '';
                            $functional = (isset($blanccoAdditionalData['data']['Description']['Functional'])) ? $blanccoAdditionalData['data']['Description']['Functional'] : '';
                            $screen = (isset($blanccoAdditionalData['data']['Description']['Screen'])) ? $blanccoAdditionalData['data']['Description']['Screen'] : '';
                            $case = (isset($blanccoAdditionalData['data']['Description']['Case'])) ? $blanccoAdditionalData['data']['Description']['Case'] : '';
                            $inputOutput = (isset($blanccoAdditionalData['data']['Description']['Input_Output'])) ? $blanccoAdditionalData['data']['Description']['Input_Output'] : '';

                            pr( $blanccoAdditionalData );
                            
                            pr( $notes );
                            pr( $other );
                            pr( $cosmetic );
                            pr( $missing );
                            pr( $functional );
                            pr( $screen );
                            pr( $case );
                            pr( $inputOutput );
                            die;
                            
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