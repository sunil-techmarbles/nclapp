<?php
namespace App\Traits;
use App\LenovoModelData;
use App\IbmModel;
use App\NewAppleData;
use App\NewProcessors;

trait CommonWipeMakorApiTraits
{
	public $data, $additionalData, $hardwareData, $jobData, $productName, $appleData, $appleDataError;
    public $isError = false;
    public $apiData, $auditData, $mainComponents, $saveDataArray;

	public function init($wipeFileContent, $additionalFileContent, $productName, $type)
	{
        $this->AddCommomData($wipeFileContent, $additionalFileContent, $productName);

        pr( $this->apiData ); die("***");

	}


	public function AddCommomData($wipeFileContent, $additionalFileContent, $productName)
	{
		$this->data = $wipeFileContent;
        $this->additionalData = $additionalFileContent;
        $this->productName = $productName;

        //hardware data
        if (isset($this->data['Report']['Hardware']))
        {
            $this->hardwareData = $this->data['Report']['Hardware'];
        }
        else
        {
            $this->hardwareData = $this->data['Report'];
        }

        //job data
        if (isset($data['Report']['Jobs']['Job'][0]))
        {
            $this->jobData = $this->data['Report']['Jobs']['Job'][0];
        }
        else
        {
            $this->jobData = $this->data['Report']['Jobs']['Job'];
        }

        $this->SetCommonData();
        $this->SetAppleData();
        $this->SetProcessorData();
        // $this->_setHardDriveData();
        // $this->_setOpticleData();
        // $this->_setVideoOutput();
        // $this->_setMemoryData();
        // $this->_setDimensionsData();
        // $this->_setPortsData();
        // $this->_setMiscellaneousData();
        // $this->_checkModel();
        // $this->_save_data_array();
        // $this->_create_xml();
	}

	public function SetCommonData()
	{
		//Asset Tag
        if (strpos(strtolower($this->hardwareData['ComputerVendor']), "apple") !== false)
        {
            $assetData = getJobUserData($this->jobData['UserFields']['UserField'], 1);
        }
        else
        {
            $assetData = getJobUserData($this->jobData['UserFields']['UserField'], 2);
        }

        if (!empty($assetData))
        {
            $assetTag = explode("-", $assetData);
            if (isset($assetTag[1]))
            {
                $this->apiData['asset_tag'] = $assetTag[1];
            }
            else
            {
                $this->apiData['asset_tag'] = $assetTag[0];
            }
        }


        if (isset($this->additionalData['Customer_Asset_Tag']))
        {
            $this->apiData['customer_asset_tag'] = $this->additionalData['Customer_Asset_Tag'];
        }
        else
        {
            $this->apiData['customer_asset_tag'] = "N/A";
        }

        //Product Serial
        $this->apiData['serial'] = $this->hardwareData['ComputerSerial'];

        //product class
        $this->apiData['Class'] = $this->productName;

        //Product Manufacturer
        $this->apiData['manufacturer'] = $this->hardwareData['ComputerVendor'];
        
        if (strpos(strtolower($this->hardwareData['ComputerVendor']), "apple") !== false)
        {
            $this->apiData['manufacturer'] = "Apple";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "dell inc.") !== false)
        {
            $this->apiData['manufacturer'] = "Dell";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "hewlett-packard") !== false)
        {
            $this->apiData['manufacturer'] = "HP";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "panasonic coropration") !== false)
        {
            $this->apiData['manufacturer'] = "Panasonic";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "amd") !== false)
        {
            $this->apiData['manufacturer'] = "AMD";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "lenovo") !== false)
        {
            $this->apiData['manufacturer'] = "Lenovo";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "asustek computer inc.") !== false)
        {
            $this->apiData['manufacturer'] = "Asus";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "asustek computer inc") !== false)
        {
            $this->apiData['manufacturer'] = "Asus";
        }
        else if (strpos(strtolower($this->hardwareData['ComputerVendor']), "intel") !== false)
        {
            $this->apiData['manufacturer'] = "Intel";
        }

        //product ChassisType
        $this->apiData['chassistype'] = $this->hardwareData['ChassisType'];

         //model
        if (strtolower(trim($this->hardwareData['ComputerVendor'])) == "lenovo")
        {
            $modelString = LenovoModelData::getLenovoManufacturerModel($this->hardwareData['ComputerModel']);
        }
        elseif (strtolower(trim($this->hardwareData['ComputerVendor'])) == "ibm" || (isset($this->hardwareData['Manufacturer']) && strtolower(trim($this->hardwareData['Manufacturer'])) == "ibm"))
        {
            $modelString = IbmModel::getIBMManufacturerModel($this->hardwareData['ComputerModel']);
        }
        elseif (strtolower(trim($this->hardwareData['ComputerVendor'])) == "panasonic" || strtolower(trim($this->hardwareData['ComputerVendor'])) == "panasonic corporation")
        {
            $modelString = substr($this->hardwareData['ComputerModel'], 0, 5);
        }
        else
        {
            $modelString = trim($this->hardwareData['ComputerModel']);
        }

        //get model from string HP
        $modelString = str_replace($this->apiData['manufacturer'], "", $modelString);
        $this->apiData['model'] = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $modelString)));

        //set weight
        if (isset($this->additionalData['Weight']))
        {
            $this->apiData['weight'] = $this->additionalData['Weight'];
            $this->apiData['weight_base'] = "LB";
        }
        else
        {
            $this->apiData['weight'] = "N/A";
            $this->apiData['weight_base'] = "N/A";
        }

        //grade
        if (isset($this->additionalData['Grade']))
        {
            $this->apiData['grade'] = $this->additionalData['Grade'];
        }
        else
        {
            $this->apiData['grade'] = "N/A";
        }

        // Next process 
        $this->apiData['next_process'] = "Resale";

        //Compliance Label
        $this->apiData['compliance_label'] = "Tested for Key Functions,  R2/Ready for Resale";

        //condition
        $this->apiData['condition'] = "Tested Working";

        // part number
        if (strtolower(trim($this->hardwareData['ComputerVendor'])) == "lenovo")
        {
            $this->apiData['model#'] = $this->hardwareData['ComputerModel'];
        }
        elseif (strtolower(trim($this->hardwareData['ComputerVendor'])) == "ibm" || (isset($this->hardwareData['Manufacturer']) && strtolower(trim($this->hardwareData['Manufacturer'])) == "ibm"))
        {
            $serialNumber = $this->hardwareData['ComputerModel'];
            if (!empty($serialNumber))
            {
                $new = array_reverse(explode(" ", $serialNumber));
                if (isset($new[0]))
                {
                    $new = str_replace('-[', '', $new[0]);
                    $serialNumber = str_replace(']-', '', $new);
                }
            }
            $this->apiData['model#'] = $serialNumber;
        }
        elseif (isset($this->additionalData['Part_Number']))
        {
            $this->apiData['model#'] = $this->additionalData['Part_Number'];
        }
        else
        {
            $this->apiData['model#'] = "";
        }

        // color
        if (isset($this->additionalData['Color']))
        {
            $this->apiData['color'] = $this->additionalData['Color'];
        }
        else
        {
            $this->apiData['color'] = "N/A";
        }

         //OS Level
        if ($this->productName == "Server")
        {
            $this->apiData['oprating_system'] = "N/A";
        }
        elseif (isset($this->additionalData['OS_Label']))
        {
            $this->apiData['oprating_system'] = $this->additionalData['OS_Label'];
        }
        else
        {
            $this->apiData['oprating_system'] = "N/A";
        }

        // form factor
        if (isset($this->additionalData['Technology']))
        {
            $this->apiData['form_factor'] = $this->additionalData['Technology'];
        }
        else
        {
            $this->apiData['form_factor'] = "N/A";
        }
	}


	public function SetAppleData()
	{
		//Apple data array
        $this->appleData = array();
        unset($this->appleDataError );
        $appleModelModified = '';
        
        if (strpos(strtolower($this->hardwareData['ComputerVendor']), "apple") !== false)
        {
        	$appleProcessor = explode(" ", $this->hardwareData['Processors']['Processor']['Name']);
        	if (isset($this->hardwareData['Processors']['Processor'][0]))
        	{
                $appleProcessor = explode(" ", $this->hardwareData['Processors']['Processor'][0]['Name']);
                $appleProcessorModel = $appleProcessor[2];
            }
            else
            {
                $appleProcessor = explode(" ", $this->hardwareData['Processors']['Processor']['Name']);
                $appleProcessorModel = $appleProcessor[2];
            }

           	$appleDataResp = NewAppleData::getMakorAppleManufacturerModel($this->hardwareData['ComputerModel'], $appleProcessorModel);

           	if ($appleDataResp !== FALSE && !is_array($appleDataResp) && $appleDataResp == 'DUPLICATES')
           	{
                $this->appleDataError = "Multiple Models Manually Check";
            }
            else
            {
                $this->appleData = $appleDataResp;
            }

            $appleModelModified = trim(preg_replace('/[0-9,]/', "", $this->hardwareData['ComputerModel']));

            if (!empty($this->appleData) && is_array($this->appleData))
            {
                $modelString = trim($this->appleData['Model']);
                $modelString = str_replace($this->apiData['manufacturer'], "", $modelString);
                $this->apiData['model'] = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $modelString)));
            }
            else
            {
                $this->apiData['model'] = $this->apiData['model'];
            }

            if (!empty($this->appleData['Model#']))
            {
                $this->apiData['model#'] = $this->appleData['Model#'];
            }
        }
        $this->SetTechnologyData($appleModelModified);
	}


	public function SetTechnologyData($appleModelModified)
	{
        if (isset($this->additionalData['Technology']))
        {
            $this->apiData['form_factor'] = $this->additionalData['Technology'];
        }
        elseif (isset($appleModelModified))
        {
            if ($appleModelModified == 'MacBookPro')
            {
                $this->apiData['form_factor'] = "Notebook";
            }
            elseif ($appleModelModified == 'PowerMac')
            {
                $this->apiData['form_factor'] = "Tower";
            }
            elseif ($appleModelModified == 'MacBook')
            {
                $this->apiData['form_factor'] = "Notebook";
            }
            elseif ($appleModelModified == 'MacPro')
            {
                $this->apiData['form_factor'] = "Tower";
            }
            elseif ($appleModelModified == 'MacBookAir')
            {
                $this->apiData['form_factor'] = "Notebook";
            }
            elseif ($appleModelModified == 'PowerBook')
            {
                $this->apiData['form_factor'] = "Notebook";
            }
            elseif ($appleModelModified == 'iMac')
            {
                $this->apiData['form_factor'] = "All In One";
            }
            elseif ($appleModelModified == 'Macmini')
            {
                $this->apiData['form_factor'] = "Ultra Small Form Factor";
            }
            else {
                $this->apiData['form_factor'] = "";
            }
        }
        else
        {
            $this->apiData['form_factor'] = "";
        }
    }

    
    public function SetProcessorData()
    {
    	if (!empty($this->appleData))
    	{
    		$this->apiData['processors'][0]['processor_manufacturer'] = $this->appleData['Processor_Manufacturer'];
            $this->apiData['processors'][0]['processor_type'] = $this->appleData['Processor_Type'];
            $this->apiData['processors'][0]['processor_model'] = $this->appleData['Processor_Model'];
            $this->apiData['processors'][0]['processor_core'] = $this->appleData['Processor_Core'];
            $this->apiData['processors'][0]['processor_generation'] = $this->appleData['Processor_Generation'];
            $this->apiData['processors'][0]['processor_codename'] = $this->appleData['Processor_Codename'];
            $this->apiData['processors'][0]['processor_socket'] = $this->appleData['Processor_Socket'];
            if (!empty($this->appleData['Processor_Socket']))
            {
                $speed = MHzToGHz($this->appleData['Processor_Socket']);
                $speed = '';
            }
            else
            {
                $speed = "";
            }
            $this->apiData['processors'][0]['processor_speed'] = $speed;
            $this->apiData['processors'][0]['processor_qty'] = $this->appleData['Processor_Qty'];
    	}
    	else
    	{
            $processorData = getCustomizeData($this->hardwareData['Processors']['Processor']);
            $key = 0;

            if (strpos($processorData[0]['Vendor'], 'Intel') !== false) {
                $processorData[0]['Vendor'] = "Intel";
            }

            $processorName = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $processorData[0]['Name'])));
            $model = explode(" ", $processorName);
            $sqlData = $this->getProcessorSqlData($model, $processorName);

            if (isset($sqlData['Manufacturer']) && $sqlData['Manufacturer'] != "NULL")
            {
                $this->apiData['processors'][$key]['processor_manufacturer'] = $sqlData['Manufacturer'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_manufacturer'] = $processorData[0]['Vendor'];
            }

            if (isset($sqlData['Type']) && $sqlData['Type'] != "NULL")
            {
                $this->apiData['processors'][$key]['processor_type'] = $sqlData['Type'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_type'] = 'N/A';
            }

            if ($this->productName == "Server")
            {
                if (isset($sqlData['Model']) && !empty($sqlData['Model']))
                {
                    $this->apiData['processors'][$key]['processor_model'] = $sqlData['Model'];
                }
                else
                {
                    $this->apiData['processors'][$key]['processor_model'] = $model[3];
                }
            }
            elseif (isset($sqlData['Model']) && !empty($sqlData['Model']))
            {
                $this->apiData['processors'][$key]['processor_model'] = $sqlData['Model'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_model'] = $model[2];
            }

            if (isset($sqlData['Cores']) && $sqlData['Cores'] != "NULL")
            {
                $this->apiData['processors'][$key]['processor_core'] = $sqlData['Cores'];
            }
            else
            {
                NewProcessors::updateProcessor($model[2], $processorData[0]['NumCores']);
                $this->apiData['processors'][$key]['processor_core'] = $processorData[0]['NumCores'];
            }

            if (isset($sqlData['Generation']) && $sqlData['Generation'] != "NULL")
            {
                $this->apiData['processors'][$key]['processor_generation'] = $sqlData['Generation'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_generation'] = "";
            }

            if (isset($sqlData['Codename']) && $sqlData['Codename'] != "NULL")
            {
                $this->apiData['processors'][$key]['processor_codename'] = $sqlData['Codename'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_codename'] = "";
            }

            if (isset($sqlData['Socket']) && $sqlData['Socket'] != "NULL")
            {
                $this->apiData['processors'][$key]['processor_socket'] = $sqlData['Socket'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_socket'] = "";
            }

            if (isset($sqlData['Clock']) && $sqlData['Clock'] != "NULL")
            {
                $speed = MHzToGHz($sqlData['Clock']);
            }
            else
            {
                $speed = MHzToGHz($processorData[0]['Speed']);
            }
            
            $this->apiData['processors'][$key]['processor_speed'] = $speed;

            if (isset($this->additionalData['Components']['CPU_Count']) && !empty($this->additionalData['Components']['CPU_Count']))
            {
                $this->apiData['processors'][$key]['processor_qty'] = $this->additionalData['Components']['CPU_Count'];
            }
            else
            {
                $this->apiData['processors'][$key]['processor_qty'] = count($this->hardwareData['Processors']);
            }
    	}
    }

    public function getProcessorSqlData($model, $processorName)
    {
        $sqlData = '';
        if ($this->productName == "Server")
        {
            $modelStr = $model[4];
            if ($modelStr[0] == 'v' && is_numeric($modelStr[1]))
            {
                $searchModel = $model[3] . $model[4];
            }
            else
            {
                $searchModel = $model[3];
            }
            $sqlData = NewProcessors::getProcessorData($searchModel);
        }
        else
        {
            $modelStr = $model[2];
            if ($modelStr[0] == 'v' && is_numeric($modelStr[1]))
            {
                $searchModel = $model[2] . $model[3];
            }
            else
            {
                $searchModel = $model[2];
            }
            $sqlData = NewProcessors::getProcessorData($searchModel);
        }
        if (empty($sqlData))
        {
            $sqlData = NewProcessors::getMissedProcessorData($model[2], $model[5]);
        }
        if (empty($sqlData) && strpos($processorName, 'Atom') !== false)
        {
            $modelStr = $model[4];
            
            if ($modelStr[0] == 'v' && is_numeric($modelStr[1]))
            {
                $searchModel = $model[3] . $model[4];
            }
            else
            {
                $searchModel = $model[3];
            }
            $sqlData = NewProcessors::getProcessorData($searchModel);
        }
        return $sqlData;
    }
}