<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WipeDataPdf;
use App\BiosData;
use App\BlanccoPdf;
use App\WipeReport;

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
        $wipeBiosFilesCount = $this->CountWipeBiosDataFiles();
        $blanccoPdfFilesCount = $this->CountBlanccoPdfDataFiles();
        $this->InsertDataInWipeReportMainTable($wipePdfFilesCount, $wipeBiosFilesCount, $blanccoPdfFilesCount);
        echo "Total files Count Wipe-data : ". $wipePdfFilesCount;
        echo "<br>";
        echo "Total files Count Bios-data : ". $wipeBiosFilesCount;
        echo "<br>";
        echo "Total files Count Blancco Pdf-data : ". $blanccoPdfFilesCount;
        die("******");
    }


    /**
     * Function to Insert data to wipe report table .
     *
     * @return mixed
     */
    public function InsertDataInWipeReportMainTable($wipePdfFilesCount, $wipeBiosFilesCount, $blanccoPdfFilesCount)
    {
        $WipeReportFilesData = WipeReport::getWipeReportsFilesData( $this->TodayDate );
        if($WipeReportFilesData->count() > 0)
        {
            foreach ($WipeReportFilesData as $key => $WipeReportFileData)
            {
               if($wipePdfFilesCount > $WipeReportFileData->wipe_data_pdf_count)
               {
                    $c = $wipePdfFilesCount;
               }
               else
               {
                    $c = $WipeReportFileData->wipe_data_pdf_count;
               }

               if($wipeBiosFilesCount > $WipeReportFileData->bios_data_file_count)
               {
                    $c1 = $wipeBiosFilesCount;
               }
               else
               {
                    $c1 = $WipeReportFileData->bios_data_file_count;
               }

               if($blanccoPdfFilesCount > $WipeReportFileData->blancco_pdf_data_count)
               {
                    $c2 = $blanccoPdfFilesCount;
               }
               else
               {
                    $c2 = $WipeReportFileData->blancco_pdf_data_count;
               }
            }
            WipeReport::UpdateWipeReportsFileData($c, $c1, $c2, $this->TodayDate);
        }
        else
        {
            WipeReport::InsertWipeReportsFileData($wipePdfFilesCount, $wipeBiosFilesCount, $blanccoPdfFilesCount, $this->TodayDate);
        }
    }


    /**
     * Function to count Blancco PDF files added on that day .
     *
     * @return mixed
     */
    public function CountBlanccoPdfDataFiles()
    {
        $Filecount = 0;
        $blanccoPdfFiles = scandir($this->blanccoDataFilesDir);
        foreach ($blanccoPdfFiles as $key => $blanccoPdfFile)
        {
            $filePath = $this->blanccoDataFilesDir . '/' . $blanccoPdfFile;
            if (date ( "Y-m-d", filemtime($filePath)) >= $this->TodayDate && (!in_array($blanccoPdfFile, array('.', '..')) ))
            {
                $Filecount++;
                $BlanccoDataPdfs = BlanccoPdf::getBlanccoFileData( $this->TodayDate );
                if($BlanccoDataPdfs->count() > 0)
                {
                }
                else
                {
                    BlanccoPdf::InsertBlanccoFileData($blanccoPdfFile, $this->TodayDate);
                }
            }
        }
        return  $Filecount;
    }


    /**
     * Function to count Wipe Bios data files added on that day .
     *
     * @return mixed
     */
    public function CountWipeBiosDataFiles()
    {
        $Filecount = 0;
        $WipeBiosfiles = scandir($this->biosDataFilesDir);
        foreach ($WipeBiosfiles as $key => $WipeBiosfile)
        {
            $filePath = $this->biosDataFilesDir . '/' . $WipeBiosfile;
            if (date ( "Y-m-d", filemtime($filePath)) >= $this->TodayDate && (!in_array($WipeBiosfile, array('.', '..')) ))
            {
                $Filecount++;
                $WipeBiosDataFiles = BiosData::getWipeBiosFileData( $this->TodayDate );
                if($WipeBiosDataFiles->count() > 0)
                {
                }
                else
                {
                    BiosData::InsertWipeBiosFileData($WipeBiosfile, $this->TodayDate);
                }
            }
        }
        return $Filecount;
    }


    /**
     * Function to count Wipe PDF files added on that day.
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
                    $WipeDataPdfs = WipeDataPdf::getWipeFileData( $this->TodayDate );
                    if($WipeDataPdfs->count() > 0)
                    {
                    }
                    else
                    {
                        WipeDataPdf::InsertWipeFileData($Wipefile, $this->TodayDate);
                    }
                }
            }
        }
        return $Filecount;
    }
}