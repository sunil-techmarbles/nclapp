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
                            
                            pr($blanccoHardwareReportData); die;
                            
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

}
