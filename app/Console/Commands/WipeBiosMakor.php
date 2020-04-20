<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use App\Traits\CommonWipeBiosMakorApiTraits;
use Config;
use SimpleXMLElement;
use Illuminate\Support\Facades\Mail;


class WipeBiosMakor extends Command
{
    use CommonWipeBiosMakorApiTraits;
    public $basePath, $wipeBiosDataDir, $wipeBiosAdditionalDataDir, $wipeBiosExecutedFileDir, $wipeBiosAdditionalExecutedDir, $wipeBiosResponseFileDIr, $WipeBiosMakorRequestFileDir, $executedFiles;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WipeBiosMakor:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Bios Wipe report data to Makpor';

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
        // $subject = 'WipeBiosMakor:api '. date('Y-m-d h:i:s');
        // $emailsToSend = "sunil.techmarbles@gmail.com";
        // Mail::raw('Test Crons for WipeBiosMakor:api', function($m) use ( $subject, $emailsToSend)
        // {
        //         $m->to( $emailsToSend )->subject($subject);
        // });
        $this->basePath  = base_path().'/public';   
        $this->wipeBiosDataDir = $this->basePath . "/wipe-data/bios-data";
        $this->wipeBiosAdditionalDataDir = $this->basePath . "/wipe-data-additional";
        $this->wipeBiosExecutedFileDir = $this->basePath . "/makor-processed-data/bios-data";
        $this->wipeBiosAdditionalExecutedDir = $this->basePath . "/makor-processed-data/additional";
        $this->wipeBiosResponseFileDIr = $this->basePath . "/wipe-data2/bios-data";
        $this->WipeBiosMakorRequestFileDir = $this->basePath . "/makor-request/bios-makor-request";
        $this->createMakorRequestFromWipeBiosData();
        die( $this->executedFiles . " files Successfully exectuted for Wipe Bios Makor api");
    }

    /**
     *  Function for reading all the xml files form Wipe Bios data and create makor request.
     *
     * @return mixed
     */
    public function createMakorRequestFromWipeBiosData()
    {
        $this->executedFiles = 0;
        // get all XML files from wipe Bios data directory
        $wipeBiosDataFiles = getDirectoryFiles($this->wipeBiosDataDir);
        if( !empty($wipeBiosDataFiles))
        {
            foreach ($wipeBiosDataFiles as $key => $wipeBiosDataFile)
            {
                $wipeBiosDataFilePath = $this->wipeBiosDataDir . "/" . $wipeBiosDataFile;
                try
                {
                    //read XML file
                    $wipeBiosFileContent = getXMLContent($wipeBiosDataFilePath);

                    //check if XML is valid
                    if (false === $wipeBiosFileContent)
                    {
                        $error = 'Invalid XML file > ' . $wipeBiosDataFile;
                        MessageLog::addLogMessageRecord($error, $type="WipeBiosMakor", $status="failure");
                        continue; //skip file on error
                    }

                    $assetNumber = str_replace('.xml', '', $wipeBiosDataFile);

                    $BiosAdditionalDataFile = $this->wipeBiosAdditionalDataDir . "/" . $assetNumber . ".xml";
                    if(!File::exists($BiosAdditionalDataFile))
                    {
                        $error = 'No Additional data file found for' . $wipeBiosDataFile . ".";
                        MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
                        continue;
                    }
                        
                    $BiosAdditionalFileContent = getXMLContent($BiosAdditionalDataFile);

                    if (isset($BiosAdditionalFileContent['Product_Name']) && !empty($BiosAdditionalFileContent['Product_Name']))
                    {
                        $productName = $BiosAdditionalFileContent['Product_Name'];
                    }
                    else
                    {
                        $error = "WIPE DATA FILE > " . $wipeBiosDataFile . " > ProductName is empty so skipping this file.";
                        MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
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

                    $allDataArray = $wipeBiosFileContent['node'];

                    switch ($productName) {
                        case 'Computer':
                            $apiDataObject = $this->init($allDataArray, $BiosAdditionalFileContent, $productName, $assetNumber);
                            break;
                        case 'Laptop':
                            $apiDataObject = $this->init($allDataArray, $BiosAdditionalFileContent, $productName, $assetNumber);
                            break;
                        default:
                            $error = 'No Class Found for file ' . $wipe_data_file . ". Valid Classes are Computer, Server, Laptop & All_In_One";
                            MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
                            continue;
                            break;
                    }

                    if (!isset($apiDataObject['xml_data']) && !empty($apiDataObject['xml_data']))
                    {
                        $error = 'Invalid XML file for Wipe Bios Makor Api , enable to convert > ' . $wipeBiosDataFile;
                        MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
                        continue;
                    }

                    $WipeBiosMakorResponse = $this->BiosWipeMakorAPIRequest($apiDataObject, $assetNumber);

                    if ($WipeBiosMakorResponse == 200)
                    {
                        $this->executedFiles++;

                        $destinationBiosWipeReportExecutedFile = $this->wipeBiosExecutedFileDir . '/' . $wipeBiosDataFile;
                        rename($wipeBiosDataFilePath, $destinationBiosWipeReportExecutedFile);

                        $destinationBiosAdditionalWipeExecutedFile = $this->wipeBiosAdditionalExecutedDir . '/' . $assetNumber . ".xml";
                        rename($BiosAdditionalDataFile, $destinationBiosAdditionalWipeExecutedFile);

                        $RequestFile = $this->WipeBiosMakorRequestFileDir .'/' . $assetNumber . ".xml";
                        WriteDataFile($RequestFile, $apiDataObject['xml_data']);

                        $wipeBiosResponseFile = $this->wipeBiosResponseFileDIr .'/' . $assetNumber . '.xml';
                        $this->CreateBiosWipeReportXmlResponseFIle($wipeBiosResponseFile, $apiDataObject);

                        $success = 'Wipe Bios Makor API Successfull for wipe data  ' . $wipeBiosDataFile;
                        MessageLog::addLogMessageRecord($success,$type="WipeBiosMakor", $status="success");
                    }
                    elseif ($WipeBiosMakorResponse == 400)
                    {
                        $error = 'Makor ERP system received the message, but was not able to process the report for ' . $wipeBiosDataFile;
                        MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
                        continue;
                    }
                    else
                    {
                        $error = 'Unknown API Error for ' . $wipeBiosDataFile;
                        MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
                        continue;
                    }
                }
                catch (\Execption $e)
                {
                    $message = $e->getMessage().' '. $e->getCode() . " " . $wipeBiosDataFile;
                    MessageLog::addLogMessageRecord($message,$type="WipeBiosMakor", $status="failure");
                    continue;
                }
            }
        }
        else
        {
            $error = $this->wipeBiosDataDir . " doesn't contain any files.";
            MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
        }
    }

    /**
     *  Function for Creating Wipe Bios report Response file.
     *
     * @return mixed
     */
    public function CreateBiosWipeReportXmlResponseFIle($wipeBiosResponseFile, $ApidataObject)
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
        $component = $xml->addChild('component', $ApidataObject['saveDataArray']['ProcessorModel_Speed']);
        $component->addAttribute('name', 'ProcessorModel_Speed');
        
        if( isset( $ApidataObject['saveDataArray']['MemoryType_Speed'] ))
        {
            foreach ($ApidataObject['saveDataArray']['MemoryType_Speed'] as $key => $MemoryTypeSpeed)
            {
                $component = $xml->addChild('component', $MemoryTypeSpeed);
                $component->addAttribute('name', 'MemoryType_Speed');
            }
        }
        $component = $xml->addChild('component', 'No HD');
        $component->addAttribute('name', 'HardDriveType_Interface');
        $xml->asXML($wipeBiosResponseFile);
    }


     /**
     *  Function for API request to Makor of Wipe bios data.
     *
     * @return mixed
     */
    public function BiosWipeMakorAPIRequest($apiDataObject, $assetId)
    {
        $MakorRequestjson = array();
        $MakorRequestjson['asset_id'] = $assetId;
        $MakorRequestjson['asset_report']['report'] = base64_encode($apiDataObject['xml_data']);
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