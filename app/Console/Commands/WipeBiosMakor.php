<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;


class WipeBiosMakor extends Command
{
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
                      pr( $BiosAdditionalFileContent );die;





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