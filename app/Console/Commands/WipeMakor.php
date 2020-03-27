<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use App\Traits\CommonWipeMakorApiTraits;

 
class WipeMakor extends Command
{
    use CommonWipeMakorApiTraits;
    public $wipeDataDir , $wipeAdditionalDataDir;

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
                
                //read XML file
                $wipeFileContent = getXMLContent($wipeDataFilePath);
                
                //check if XML is valid
                if (false === $wipeFileContent)
                {
                    // $error = 'Invalid XML file > ' . $wipeDataFilePath;
                    // MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
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
                        // $error = 'No Additional data file found for' . $wipeDataFile . ".";
                        // MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
                        continue;
                    }
                    
                    $additionalFileContent = getXMLContent($additionalDataFile);

                    if (!isset($jobData['UserFields']['UserField']))
                    {
                        // $error = "WIPE DATA FILE > " . $wipeDataFile . " > ProductName at UserField Index 5 not found so aborting this record.";
                        // MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
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
                        // $error = "WIPE DATA FILE > " . $wipeDataFile . " > ProductName is empty so skipping this file.";
                        // MessageLog::addLogMessageRecord($error,$type="WipeMakor", $status="failure");
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

                    if( $productName == 'Laptop' )
                    {
                        // pr( $wipeDataFilePath );
                        // pr( $additionalDataFile );
                        // pr($productName  ); die;
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
                        pr( $ApidataObject );die;
                    }
                }
            }
        }
    }
}