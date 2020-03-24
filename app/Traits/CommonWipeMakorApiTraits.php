<?php
namespace App\Traits;

trait CommonWipeMakorApiTraits
{
	public $data, $additionalData, $hardwareData, $jobData, $productName, $appleData, $appleDataError;
    public $isError = false;
    public $apiData, $auditData, $mainComponents, $saveDataArray;

	public function init($wipeFileContent, $additionalFileContent, $productName, $type)
	{
        $this->AddCommomData($wipeFileContent, $additionalFileContent, $productName);

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
        
        // $this->_setAppleData();
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

        pr( $this->apiData ); die("test");

	}
}