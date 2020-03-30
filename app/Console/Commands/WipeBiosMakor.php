<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use App\Traits\CommonWipeBiosMakorApiTraits;

class WipeBiosMakor extends Command
{
    use CommonWipeBiosMakorApiTraits;
    public $basePath, $wipeBiosDataDir, $wipeBiosAdditionalDataDir;

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
        $this->basePath  = base_path().'/public';
        $this->wipeBiosDataDir = $this->basePath . "/wipe-data/bios-data";
        $this->wipeBiosAdditionalDataDir = $this->basePath . "/wipe-data-additional";

        $this->createMakorRequestFromWipeBiosData();
        die("Wipe Bios Makor api done");
    }

    /**
     *  Function for reading all the xml files form Wipe Bios data and create makor request.
     *
     * @return mixed
     */
    public function createMakorRequestFromWipeBiosData()
    {
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
                        // $error = 'No Additional data file found for' . $wipeBiosDataFile . ".";
                        // MessageLog::addLogMessageRecord($error,$type="WipeBiosMakor", $status="failure");
                        continue;
                    }
                        
                    $BiosAdditionalFileContent = getXMLContent($BiosAdditionalDataFile);

                    if (isset($BiosAdditionalFileContent['Product_Name']) && !empty($BiosAdditionalFileContent['Product_Name'])) {
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

                    pr($apiDataObject );
                    die;

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
}