<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use App\Traits\CommonWipeMakorApiTraits;
use SimpleXMLElement;

class WipeMakor extends Command
{
    use CommonWipeMakorApiTraits;
     public $basePath, $wipeDataDir , $wipeAdditionalDataDir, $wipeExecutedFileDir, $wipeAdditionalExecutedDir, $wipeResponseFileDIr;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WipeMakor:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Wipe report data to Makpor';

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
        $this->wipeDataDir = $this->basePath . "/wipe-data";
        $this->wipeAdditionalDataDir = $this->basePath . "/wipe-data-additional";
        $this->wipeExecutedFileDir = $this->basePath . "/makor-processed-data/wipe-data";
        $this->wipeAdditionalExecutedDir = $this->basePath . "/makor-processed-data/additional";
        $this->wipeResponseFileDIr = $this->basePath . "/wipe-data2";

        $this->createMakorRequestFromWipeData();
        die("Wipe Makor api done");
    }

    /**
     *  Function for reading all the xml files form Wipe data and create makor request.
     *
     * @return mixed
     */
    public function createMakorRequestFromWipeData()
    {
        // get all XML files from wipe data directory
        $wipeDataFiles = getDirectoryFiles($this->wipeDataDir);
        if( !empty($wipeDataFiles))
        {
            foreach ($wipeDataFiles as $key => $wipeDataFile)
            {
                $wipeDataFilePath = $this->wipeDataDir . "/" . $wipeDataFile;
                try
                {
                    //read XML file
                    $wipeFileContent = getXMLContent($wipeDataFilePath);
                    
                    //check if XML is valid
                    if (false === $wipeFileContent)
                    {
                        $error = 'Invalid XML file > ' . $wipeDataFilePath;
                        MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                        continue; //skip file on error
                    }

                    @list($orderTid, $travelerId, $serialNumber) = explode("-", pathinfo($wipeDataFile, PATHINFO_FILENAME));
                    if (isset($wipeFileContent['Report']) && !empty($wipeFileContent['Report']))
                    {
                        // get job data 
                        if (isset($wipeFileContent['Report']['Jobs']['Job'][0]))
                        {
                            $jobData = $wipeFileContent['Report']['Jobs']['Job'][0];
                        }
                        else
                        { 
                            $jobData = $wipeFileContent['Report']['Jobs']['Job'];
                        }
                        
                        // get hardware data 
                        if (isset($wipeFileContent['Report']['Hardware']))
                        {
                            $hardwareData = $wipeFileContent['Report']['Hardware'];
                        } 
                        else
                        {
                            $hardwareData = $wipeFileContent['Report'];
                        }
                        
                        $assetTag = getJobUserData($jobData['UserFields']['UserField'], 2);
                        
                        $additionalDataFile = $this->wipeAdditionalDataDir . "/" . $assetTag . ".xml";
                        if(!File::exists($additionalDataFile))
                        {
                            $error = 'No Additional data file found for' . $wipeDataFile . ".";
                            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                            continue;
                        }
                        
                        $additionalFileContent = getXMLContent($additionalDataFile);

                        if (!isset($jobData['UserFields']['UserField']))
                        {
                            $error = "WIPE DATA FILE > " . $wipeDataFile . " > ProductName at UserField Index 5 not found so aborting this record.";
                            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                            continue;
                        }

                        if (strpos(strtolower($hardwareData['ComputerVendor']), "apple") !== false)
                        {
                            $productName = getJobUserData($jobData['UserFields']['UserField'], 4);
                        }
                        else
                        {
                            $productName = getJobUserData($jobData['UserFields']['UserField'], 5);
                        }

                        if (isset($additionalFileContent['Product_Name']) && !empty($additionalFileContent['Product_Name']))
                        {
                            $productName = $additionalFileContent['Product_Name'];
                        }

                        if (empty($productName))
                        {
                            $error = "WIPE DATA FILE > " . $wipeDataFile . " > ProductName is empty so skipping this file.";
                            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                            continue;
                        }

                        if (strtolower($productName) == strtolower('Apple - Laptop'))
                        {
                            $productName = 'Laptop';
                        } 
                        elseif (strtolower($productName) == strtolower('Apple - Tower'))
                        {
                            $productName = 'Computer';
                        } 
                        elseif (strtolower($productName) == strtolower('Apple - All In One'))
                        {
                            $productName = 'All_In_One';
                        }
                        
                        if (strpos(strtolower($hardwareData['ComputerVendor']), "apple") !== false)
                        {
                            $productName = 'Makor_Apple';
                        }

                        switch ($productName)
                        {
                            case 'Computer':
                            $ApidataObject = $this->init($wipeFileContent, $additionalFileContent, $productName, 'Computer');
                            break;
                            case 'Server':
                            $ApidataObject = $this->init($wipeFileContent, $additionalFileContent, $productName, 'Server');
                            break;
                            case 'Laptop':
                            $ApidataObject = $this->init($wipeFileContent, $additionalFileContent, $productName, 'Laptop');
                            break;
                            case 'All_In_One':
                            $ApidataObject = $this->init($wipeFileContent, $additionalFileContent, $productName, 'All_In_One');
                            break;
                            case 'Makor_Apple':
                            $ApidataObject = $this->init($wipeFileContent, $additionalFileContent, $productName, 'Makor_Apple');
                            break;
                            default:
                            $error = 'No Class Found for file ' . $wipeDataFile . ". Valid Classes are Computer, Server, Laptop & All_In_One";
                            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                            continue;
                            break;
                        }

                        $WipeMakorResponse = $this->WipeMakorAPIRequest($ApidataObject, $assetTag);
                        
                        if ($WipeMakorResponse == 200)
                        {
                            $destinationWipeReportExecutedFile = $this->wipeExecutedFileDir . '/' . $wipeDataFile;
                            rename($wipeDataFilePath, $destinationWipeReportExecutedFile);

                            $destinationAdditionalWipeExecutedFile = $this->wipeAdditionalExecutedDir . '/' . $assetTag . ".xml";
                            rename($additionalDataFile, $destinationAdditionalWipeExecutedFile); 

                            $wipeResponseFile = $this->wipeResponseFileDIr .'/' . $assetTag . '.xml';
                            $this->CreateWipeReportXmlResponseFIle($wipeResponseFile, $ApidataObject);

                            $success = 'Wipe Makor API Successfull for  ' . $wipeDataFile;
                            MessageLog::addLogMessageRecord($success,$type="WipeMakor", $status="success");
                        }
                        elseif ($WipeMakorResponse == 400)
                        {
                            $error = 'Makor ERP system received the message, but was not able to process the report for ' . $wipeDataFile;
                            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                            continue;
                        }
                        else
                        {
                            $error = 'Unknown API Error for ' . $wipeDataFile;
                            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                            continue;
                        }
                    }
                }
                catch (\Execption $e)
                {
                    $message = $e->getMessage().' '. $e->getCode() . " " . $wipeDataFile;
                    MessageLog::addLogMessageRecord($message,$type="WipeMakor", $status="failure");
                    continue;
                }
            }
        }
        else
        {
            $error = $this->wipeDataDir . " doesn't contain any files.";
            MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
        }
    }

    /**
     *  Function for Creating Wipe report Response file.
     *
     * @return mixed
     */
    public function CreateWipeReportXmlResponseFIle($wipeResponseFile, $ApidataObject)
    {
        $xml = new SimpleXMLElement('<data></data>');
                        
        $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Model']);
        $component->addAttribute('name', 'Model');

        $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Serial']);
        $component->addAttribute('name', 'Serial');

        $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Combined_RAM']);
        $component->addAttribute('name', 'Combined_RAM');

        $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Combined_HD']);
        $component->addAttribute('name', 'Combined_HD');
                        
        if (isset($ApidataObject['saveDataArray']['Motherboard_RAM']))
        {
            $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Motherboard_RAM']);
            $component->addAttribute('name', 'Motherboard_RAM');
        }

        if (isset($ApidataObject['saveDataArray']['RAM_Slots']))
        {
            $component = $xml->addChild('component', $ApidataObject['saveDataArray']['RAM_Slots']);
            $component->addAttribute('name', 'RAM_Slots');
        }
                        
        if (isset($ApidataObject['saveDataArray']['Storage_Dimensions'])) {
            $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Storage_Dimensions']);
            $component->addAttribute('name', 'Storage_Dimensions');
        }

        if (isset($ApidataObject['saveDataArray']['Storage_Interface']))
        {
            $component = $xml->addChild('component', $ApidataObject['saveDataArray']['Storage_Interface']);
            $component->addAttribute('name', 'Storage_Interface');
        }

        if (isset($ApidataObject['saveDataArray']['EMC']))
        {
            $component = $xml->addChild('component', $ApidataObject['saveDataArray']['EMC']);
            $component->addAttribute('name', 'EMC');
        }
                        
        $component = $xml->addChild('component', $ApidataObject['saveDataArray']['ProcessorModel_Speed']);
        $component->addAttribute('name', 'ProcessorModel_Speed');
                        
        foreach ($ApidataObject['saveDataArray']['MemoryType_Speed'] as $key => $MemoryTypeSpeed) {
            $component = $xml->addChild('component', $MemoryTypeSpeed);
            $component->addAttribute('name', 'MemoryType_Speed');
        }

        foreach ($ApidataObject['saveDataArray']['HardDriveType_Interface'] as $key => $HardDriveTypeInterface) {
            $component = $xml->addChild('component', $HardDriveTypeInterface);
            $component->addAttribute('name', 'HardDriveType_Interface');
        }

        $xml->asXML($wipeResponseFile);
    }

     /**
     *  Function for API request to Makor of Wipe data.
     *
     * @return mixed
     */
    public function WipeMakorAPIRequest($WipeMakorApiRequestDataXml, $assetId)
    {
        $MakorRequestjson = array();
        $MakorRequestjson['asset_id'] = $assetId;
        $MakorRequestjson['asset_report']['report'] = base64_encode($WipeMakorApiRequestDataXml['xml_data']);
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
}