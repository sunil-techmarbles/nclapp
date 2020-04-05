<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WipeDataPdf;

class CountWipeBiosBlancoFilesReport extends Command
{
    public $basePath , $wipeDataFilesDir, $blanccoDataFilesDir, $biosDataFilesDir, $TodayDate;
    public $validFiles = ['pdf'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WipeBiosBlanccoFiles:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command adds data to blancco, wipe, bios and wipe reports table to get the total file count in wipe report section.';

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
        $this->wipeDataFilesDir = $this->basePath . '/wipe-data';
        $this->blanccoDataFilesDir = $this->basePath . '/blancco/pdf-data';
        $this->biosDataFilesDir = $this->basePath . '/wipe-data/bios-data';
        $this->TodayDate = date('Y-m-d');

        $wipePdfFilesCount = $this->CountWipePdfDataFiles();

        echo "Total files Count Wipe-data : ". $wipePdfFilesCount;
        die("******");
    }

    /**
     * Function to count Wipe PDF files.
     *
     * @return mixed
     */
    public function CountWipePdfDataFiles()
    {
        $Filecount = 0;
        $Wipefiles = scandir($this->wipeDataFilesDir);
        foreach ($Wipefiles as $key => $Wipefile)
        {
            $ext = pathinfo($Wipefile, PATHINFO_EXTENSION);
            if(in_array($ext, $this->validFiles))
            {
                $filePath = $this->wipeDataFilesDir . '/' . $Wipefile;
                if (date ( "Y-m-d", filemtime($filePath)) >= $this->TodayDate && (!in_array($Wipefile, array('.', '..')) ))
                {
                    $Filecount++;
                    $WipeDataPdfs = WipeDataPdf::getWipeFileData($this->TodayDate);

                    pr( $WipeDataPdfs );
                    pr( $filePath );
                      die("****"); 
                }
            }
        }
    }

    
}