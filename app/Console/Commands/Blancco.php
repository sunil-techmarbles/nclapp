<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageLog;
use File;
use Config;
use App\Traits\TMXmlToArrayTraits;

class Blancco extends Command
{
    use TMXmlToArrayTraits;

    public $basePath;

    public $report_allowed_states = ['Successful', 'Failed'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blancco:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blancco api for getting data of mobiles and create pdf and xml files for makor api';

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
     * Execute the console command .
     *
     * @return mixed
     */ 
    public function handle()
    {
        $this->basePath  = base_path().'/public/blancco/';
        $this->GetAllDataFileBlancco();
        $this->GetSingleXmlAndPdfFIlesBlancco();
    }

    /**
     *  Function for reading data.xml file to array to create single pdf and xml blancco request. 
     *
     * @return mixed
     */
    public function GetSingleXmlAndPdfFIlesBlancco()
    {
        $dataFileBlancco = $this->basePath . 'xml-data/data.xml';
        if(File::exists($dataFileBlancco))
        {
            $blanccoFileContent = file_get_contents($dataFileBlancco);
            try
            {
                // createArray in TMXmlToArrayTraits for parsing xml data
                $blanccoFullData = $this->createArray($blanccoFileContent);
                $i = 0;
                foreach ($blanccoFullData['root']['report'] as $blanccoData)
                {
                    $reportUuid = $state = $serial = $lotNumber = $assetId = '';
                    if( isset($blanccoData['blancco_data']['description']['document_id']) && !empty($blanccoData['blancco_data']['description']['document_id']))
                    {
                        $reportUuid = $blanccoData['blancco_data']['description']['document_id'];

                        // for getting state form blancco_erasure_report
                        $blanccoErasureReportData = $blanccoData['blancco_data']['blancco_erasure_report']['entries']['entries']['entry'];
                        $state = $this->GetBlanccoVariable($blanccoErasureReportData, $variableToGet='state', $reportUuid);
                      
                        // for getting serial form blancco_hardware_report
                        $blanccoHardwareReportData = $blanccoData['blancco_data']['blancco_hardware_report']['entries'][0]['entry'];
                        $serial = $this->GetBlanccoVariable($blanccoHardwareReportData, $variableToGet='serial', $reportUuid);

                        // for getting lot number and asset id
                        $blanccoUserReportData = $blanccoData['user_data']['entries']['entry'];
                        $lotNumber = $this->GetBlanccoVariable($blanccoUserReportData, $variableToGet='Lot Number', $reportUuid);
                        $assetId = $this->GetBlanccoVariable($blanccoUserReportData, $variableToGet='Asset ID', $reportUuid);

                        if (in_array($state, $this->report_allowed_states) && !empty($serial) && !empty($lotNumber) && !empty($assetId))
                        {
                        }
                        else
                        {
                            if(!in_array($state, $this->report_allowed_states))
                            {
                                 $message = $reportUuid . " --Invalid State value for this Uuid. State : " . $state;
                                 MessageLog::addLogMessageRecord($message,$type="blancco", $status="failure");
                            }
                        }
                    }
                    else
                    {
                        MessageLog::addLogMessageRecord($message='reportUuid Not Exist',$type="blancco", $status="failure");
                    }
                }
                echo $i; die;
            }
            catch (\Execption $e)
            {
                echo $e->getMessage().' '. $e->getCode();
            }
        }
    }

    /**
     *  Function for getting blancco Variable.
     *
     * @return mixed
     */
    public function GetBlanccoVariable($blanccoReportData, $variableToGet, $reportUuid)
    {
        $result = '';
        if (is_array($blanccoReportData))
        {
            foreach ($blanccoReportData as $key => $data)
            {
                if ($data['@attributes']['name'] == $variableToGet)
                {
                        $result = $data['@value'];
                }
            }
        }
        if(empty($result))
        {
            $message = $variableToGet . ' Not found for '.$reportUuid.' this reportUuid';
            MessageLog::addLogMessageRecord($message,$type="blancco", $status="failure");
        }
        return $result;
    }

    /**
     *  Function for getting all data from blancco.
     *
     * @return mixed
     */
    public function GetAllDataFileBlancco()
    {
        $format = "xml";
        $requestFilePath = $this->basePath . "request-xmls/all-reports.xml";
        $requestFileName = 'all-reports.xml';
        $this->blancooCurlRequest($requestFilePath, $requestFileName, 'fulldata', $format, 'data', 'all');
    }

    /**
     *  Function for doing curl request to blancco that contain request xml file and return result in xml,pdf format.
     *
     * @return mixed
     */
    public function blancooCurlRequest($requestFilePath , $requestFileName , $type , $format , $returnFileName , $reportUuid)
    {
        $url = "https://cloud.blancco.com:443/rest-service/report/export/". $format;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, Config::get('blancco.blanccoApiCredential.apiUsername').':'.Config::get('blancco.blanccoApiCredential.apiPassword'));
        $fields = [
        'xmlRequest' => new \CurlFile($requestFilePath, 'application/xml', $requestFileName)
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            $errTxt = $reportUuid . ' --> ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
            MessageLog::addLogMessageRecord($errTxt, $type="blancco", $status="failure");
        }
        else
        {
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpcode == 200)
            {
                $this->handleBlanccoCurlResponse($result, $type, $returnFileName);
            }
            else
            {
                $errTxt =  $reportUuid." --> ".curl_error($ch)." ".$httpcode;
                MessageLog::addLogMessageRecord($errTxt,$type="blancco", $status="failure");
            }
        }
        curl_close($ch);
    }

   /**
     *  Function for handling response of blancco curl request.
     *
     * @return mixed
     */
   public function handleBlanccoCurlResponse($result, $type, $returnFileName)
   {
        if($type == 'fulldata')
        {
            $xmlFile = $this->basePath . "xml-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($xmlFile, $result); 
            $successTxt =  'All data file Created';
        }
        else if ($type == "singlepdf")
        {
            $pdfFile = $this->basePath . "pdf-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($pdfFile, $result);
            $successTxt =  $reportUuid . ' PDF file created for this reportUuid';
        }
        else if ($type == "singlexml")
        {
            $xmlFile = $this->basePath . "xml-data/".$returnFileName .'.xml';
            $this->WriteBlancoDataFile($xmlFile, $result);
            $successTxt =  $reportUuid . ' XML file created for this reportUuid';
        }
        MessageLog::addLogMessageRecord($successTxt, $type="blancco", $status="success");
    }

   /**
     *  Function for getting all data from blancco.
     *
     * @return mixed
     */
   public function WriteBlancoDataFile($file, $result)
   {
        $dataFile = fopen ($file,'w');
        fwrite ($dataFile, $result);
        fclose ($dataFile);
        return true;
    }
}