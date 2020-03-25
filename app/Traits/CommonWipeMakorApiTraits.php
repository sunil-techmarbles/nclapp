<?php
namespace App\Traits;
use App\LenovoModelData;
use App\IbmModel;

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
        // $this->_setProcessorData();
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
        	
        }


	}

}