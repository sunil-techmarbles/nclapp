<?php
namespace App\Traits;
use SimpleXMLElement;
use App\LenovoModelData;
use App\IbmModel;
use App\NewAppleData;
use App\NewProcessors;

trait CommonWipeMakorApiTraits
{
	public $data, $additionalData, $hardwareData, $jobData, $productName, $appleData, $appleDataError;
    public $isError = false;
    public $apiData, $audit, $mainComponents, $saveDataArray;

	public function init($wipeFileContent, $additionalFileContent, $productName, $type)
	{
        $this->AddCommomData($wipeFileContent, $additionalFileContent, $productName);

   
        if($type == 'Computer')
        {
            $this->SetGraphic();
            $this->CreateComputerXml();
        }
        elseif ($type == 'Server')
        {
            $this->SetData();
            $this->SetRaidController();
            $this->SetBatterystatus();
            $this->CreateServerXml();
        }
        elseif ($type == 'Laptop')
        {
            $this->SetBatteryStatusLaptop();
            $this->SetResolution();
            $this->CreateLaptopXml();
        }
        elseif ($type == 'All_In_One')
        {
            $this->SetAllInOneData();
            $this->CreateAllInOneXml();

        }
        elseif ($type == 'Makor_Apple')
        {
            die("((");
            $this->apple_table_data = getMakorAppleDataFromTable($this->hardware_data);
            $this->_save_apple_data_array();
            $this->_setProcessorData();
            $this->_setHardDriveData();
            $this->_setResolution();
            $this->_setMemoryData();
            $this->_setDimensionsData();
            $this->_setBatterystatus();
            $this->_setNewAppledata();
            $this->_create_xml();
            // $this->CreateMakorAppleXml();
        }
   

        $this->apiData['xml_data'] = $this->audit->asXML();

        return $this->apiData['xml_data'];
	}


    public function CreateAllInOneXml()
    {
         //Set Webcam
        if (is_array($this->apiData['webcam']))
        {
            $this->apiData['webcam'] = $this->apiData['webcam'][0];
        }
        $component = $this->mainComponents->addChild('component', $this->apiData['webcam']);
        $component->addAttribute('name', 'Webcam');
        $component->addAttribute('type', 'string');
    }


    public function SetAllInOneData()
    {
         //if exist additional data 
        if (isset($this->additionalData['Webcam']))
        {
            $this->apiData['webcam'] = $this->additionalData['Webcam'];
        }
        elseif (isset($this->additionalData['Peripherials']['Webcam']))
        {
            $this->apiData['webcam'] = $this->additionalData['Peripherials']['Webcam'];
        }
        else
        {
            $this->apiData['webcam'] = "N/A";
        }

        //if exist additional data 
        if (isset($this->additionalData['Size']))
        {
            $this->apiData['screen_resolution']['size'] = $this->additionalData['Size'];
        }
        else
        {
            $this->apiData['screen_resolution']['size'] = "N/A";
        }

        //if exist additional data 
        if (isset($this->additionalData['Size']))
        {
            $this->apiData['screen_resolution']['resolution_and_touch'] = "not get";
        }
        else
        {
            $this->apiData['screen_resolution']['resolution_and_touch'] = "N/A";
        }
    }

    public function CreateLaptopXml()
    {
        // component Screen/Resolution
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Screen/Resolution');

        // child component Screen/Resolution
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Screen/Resolution');

        //Set Type
        if (is_array($this->apiData['Size']))
        {
            $this->apiData['Size'] = $this->apiData['Size'][0];
        }
        $component1 = $component->addChild('component', $this->apiData['Size']);
        $component1->addAttribute('name', 'Size');
        $component1->addAttribute('type', 'string');

        //Set BatteriesStatus
        if (is_array($this->apiData['Resolution']))
        {
            $this->apiData['Resolution'] = $this->apiData['Resolution'][0];
        }
        $component2 = $component->addChild('component', $this->apiData['Resolution']);
        $component2->addAttribute('name', 'Resolution');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        if (is_array($this->apiData['Touchscreen']))
        {
            $this->apiData['Touchscreen'] = $this->apiData['Touchscreen'][0];
        }
        $component3 = $component->addChild('component', $this->apiData['Touchscreen']);
        $component3->addAttribute('name', 'Touchscreen');
        $component3->addAttribute('type', 'string');


        // component Batteries
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Peripherals');
        // child component Battery
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Peripherals');

        //Set Type
        if (is_array($this->apiData['battery']))
        {
            $this->apiData['battery'] = $this->apiData['battery'][0];
        }
        $component1 = $component->addChild('component', $this->apiData['battery']);
        $component1->addAttribute('name', 'Battery');
        $component1->addAttribute('type', 'string');

        //Set BatteriesStatus
        if (is_array($this->apiData['battery_condition']))
        {
            $this->apiData['battery_condition'] = $this->apiData['battery_condition'][0];
        }
        $component2 = $component->addChild('component', $this->apiData['battery_condition']);
        $component2->addAttribute('name', 'BatteryCondition');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        if (is_array($this->apiData['Backlit_Keyboard']))
        {
            $this->apiData['Backlit_Keyboard'] = $this->apiData['Backlit_Keyboard'][0];
        }
        $component2 = $component->addChild('component', $this->apiData['Backlit_Keyboard']);
        $component2->addAttribute('name', 'BacklitKeyboard');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        if (is_array($this->apiData['Fingerprint_Scanner']))
        {
            $this->apiData['Fingerprint_Scanner'] = $this->apiData['Fingerprint_Scanner'][0];
        }
        $component2 = $component->addChild('component', $this->apiData['Fingerprint_Scanner']);
        $component2->addAttribute('name', 'FingerprintScanner');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        if (is_array($this->apiData['Webcam']))
        {
            $this->apiData['Webcam'] = $this->apiData['Webcam'][0];
        }
        $component2 = $component->addChild('component', $this->apiData['Webcam']);
        $component2->addAttribute('name', 'Webcam');
        $component2->addAttribute('type', 'string');
    }

    public function SetResolution()
    {
        if (isset($this->additionalData['Screen']['Size']))
        {
            $this->apiData['Size'] = $this->additionalData['Screen']['Size'];
        }
        else
        {
            $this->apiData['Size'] = "N/A";
        }

        if (isset($this->additionalData['Screen']['Resolution']))
        {
            $this->apiData['Resolution'] = $this->additionalData['Screen']['Resolution'];
        }
        else
        {
            $this->apiData['Resolution'] = "N/A";
        }

        if (isset($this->additionalData['Screen']['Touchscreen']))
        {
            $this->apiData['Touchscreen'] = $this->additionalData['Screen']['Touchscreen'];
        }
        else
        {
            $this->apiData['Touchscreen'] = "N/A";
        }
    }

    public function SetBatteryStatusLaptop()
    {
        $batteryCondition = "";
        
        if (isset($this->additionalData['Peripherials']['Battery_and_Power']))
        {
            $batteryCondition = $this->additionalData['Peripherials']['Battery_and_Power'];
        }

        if ($batteryCondition == "Battery_Yes")
        {
            $battery = "Battery_Yes";
        }
        elseif ($batteryCondition == "Battery_No")
        {
            $battery = "Battery_No";
        }
        elseif ($batteryCondition == "Extended_Battery_Yes")
        {
            $battery = "Extended_Battery_Yes";
        }
        else
        {
            $battery = "N/A";
        }

        $this->apiData['battery'] = $battery;

        if (isset($this->additionalData['Peripherials']['Battery_Condition']))
        {
            $this->apiData['battery_condition'] = $this->additionalData['Peripherials']['Battery_Condition'];
        }
        else
        {
            $this->apiData['battery_condition'] = "N/A";
        }

        if (isset($this->additionalData['Peripherials']['Backlit_Keyboard']))
        {
            $this->apiData['Backlit_Keyboard'] = $this->additionalData['Peripherials']['Backlit_Keyboard'];
        }
        else
        {
            $this->apiData['Backlit_Keyboard'] = "N/A";
        }

        if (isset($this->additionalData['Peripherials']['Fingerprint_Scanner']))
        {
            $this->apiData['Fingerprint_Scanner'] = $this->additionalData['Peripherials']['Fingerprint_Scanner'];
        }
        else
        {
            $this->apiData['Fingerprint_Scanner'] = "N/A";
        }

        if (isset($this->additionalData['Peripherials']['Webcam']))
        {
            $this->apiData['Webcam'] = $this->additionalData['Peripherials']['Webcam'];
        }
        else
        {
            $this->apiData['Webcam'] = "N/A";
        }
    }

    public function CreateServerXml()
    {
         //Set HardDriveDimension
        if (is_array($this->apiData['hard_drive_dimension']))
        {
            $this->apiData['hard_drive_dimension'] = $this->apiData['hard_drive_dimension'][0];
        }
        $component = $this->mainComponents->addChild('component', $this->apiData['hard_drive_dimension']);
        $component->addAttribute('name', 'HardDriveDimension');
        $component->addAttribute('type', 'string');

        //Set StorageController
        if (is_array($this->apiData['storage_controller']))
        {
            $this->apiData['storage_controller'] = $this->apiData['storage_controller'][0];
        }
        $component = $this->mainComponents->addChild('component', $this->apiData['storage_controller']);
        $component->addAttribute('name', 'StorageController');
        $component->addAttribute('type', 'string');

        // component Batteries
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Batteries');
        
        foreach ($this->apiData['Batteries'] as $batteryData)
        {
            // child component Battery
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Battery');

            //Set Type
            if (is_array($batteryData['type']))
            {
                $batteryData['type'] = $batteryData['type'][0];
            }
            $component1 = $component->addChild('component', $batteryData['type']);
            $component1->addAttribute('name', 'Type');
            $component1->addAttribute('type', 'string');

            //Set BatteriesStatus
            if (is_array($batteryData['battery_condition']))
            {
                $batteryData['battery_condition'] = $batteryData['battery_condition'][0];
            }
            $component2 = $component->addChild('component', $batteryData['battery_condition']);
            $component2->addAttribute('name', 'BatteriesStatus');
            $component2->addAttribute('type', 'string');

            //Set Wattage
            if (is_array($batteryData['wattage']))
            {
                $batteryData['wattage'] = $batteryData['wattage'][0];
            }
            $component3 = $component->addChild('component', $batteryData['wattage']);
            $component3->addAttribute('name', 'Wattage');
            $component3->addAttribute('type', 'string');

            //Set Quantity
            if (is_array($batteryData['quantity']))
            {
                $batteryData['quantity'] = $batteryData['quantity'][0];
            }
            $component4 = $component->addChild('component', $batteryData['quantity']);
            $component4->addAttribute('name', 'Quantity');
            $component4->addAttribute('type', 'string');

            //Set Partnumber
            if (is_array($batteryData['part_number']))
            {
                $batteryData['part_number'] = $batteryData['part_number'][0];
            }
            $component5 = $component->addChild('component', $batteryData['part_number']);
            $component5->addAttribute('name', 'PartNumber');
            $component5->addAttribute('type', 'string');
        }
    }


    public function SetBatterystatus()
    {
        if (isset($this->additionalData['Battery_and_Power']))
        {
            if (is_array($this->additionalData['Battery_and_Power']))
            {
                $batteryCondition = $this->additionalData['Battery_and_Power']['Power__Supply_QTY'] . "_x_" . $this->additionalData['Battery_and_Power']['Power_Supply_Type'];
            }
            else
            {
                $batteryCondition = $this->additionalData['Battery_and_Power'];
            }
        }
        else
        {
            $batteryCondition = "N/A";
        }
        if (isset($this->hardwareData['Batteries']['Battery'][0]))
        {
            foreach ($this->hardwareData['Batteries']['Battery'] as $key => $batteryData)
            {
                $this->apiData['Batteries'][$key]['type'] = $batteryData['Type'];
                $this->apiData['Batteries'][$key]['battery_condition'] = $batteryCondition;
                if (isset($this->additionalData['Battery_and_Power']['Power_Supply_Type']))
                {
                    $this->apiData['Batteries'][$key]['wattage'] = $this->additionalData['Battery_and_Power']['Power_Supply_Type'];
                }
                else
                {
                    $this->apiData['Batteries'][$key]['wattage'] = "";
                }
                if (isset($this->additionalData['Battery_and_Power']['Power__Supply_QTY']))
                {
                    $this->apiData['Batteries'][$key]['quantitu'] = $this->additionalData['Battery_and_Power']['Power__Supply_QTY'];
                }
                else
                {
                    $this->apiData['Batteries'][$key]['quantity'] = "";
                }
                if (isset($this->additionalData['Battery_and_Power']['Power_Supply_Notes']))
                {
                    $this->apiData['Batteries'][$key]['part_number'] = $this->additionalData['Battery_and_Power']['Power_Supply_Notes'];
                }
                else
                {
                    $this->apiData['Batteries'][$key]['part_number'] = "";
                }
            }
        }
        else
        {
            if (isset($this->hardwareData['Batteries']['Battery']['Type']))
            {
                $this->apiData['Batteries'][0]['type'] = $this->hardwareData['Batteries']['Battery']['Type'];
            }
            else
            {
                $this->apiData['Batteries'][0]['type'] = "N/A";
            }
            $this->apiData['Batteries'][0]['battery_condition'] = $batteryCondition;
            if (isset($this->additionalData['Battery_and_Power']['Power_Supply_Type']))
            {
                $this->apiData['Batteries'][0]['wattage'] = $this->additionalData['Battery_and_Power']['Power_Supply_Type'];
            }
            else
            {
                $this->apiData['Batteries'][0]['wattage'] = "";
            }
            if (isset($this->additionalData['Battery_and_Power']['Power__Supply_QTY']))
            {
                $this->apiData['Batteries'][0]['quantity'] = $this->additionalData['Battery_and_Power']['Power__Supply_QTY'];
            }
            else
            {
                $this->apiData['Batteries'][0]['quantity'] = "";
            }
            if (isset($this->additionalData['Battery_and_Power']['Power_Supply_Notes']))
            {
                $this->apiData['Batteries'][0]['part_number'] = $this->additionalData['Battery_and_Power']['Power_Supply_Notes'];
            }
            else
            {
                $this->apiData['Batteries'][0]['part_number'] = "";
            }
        }
    }

    public function SetRaidController()
    {
        if (isset($this->hardwareData['StorageControllers']['StorageController'][0]))
        {
            foreach ($this->hardwareData['StorageControllers']['StorageController'] as $key => $StorageController)
            {
                $this->apiData['storage_controller'] = $StorageController['Product'];
                break;
            }
        }
        elseif (isset($this->hardwareData['StorageControllers']['Product']))
        {
            $this->apiData['storage_controller'] = $this->hardwareData['StorageControllers']['Product'];
        }
        else
        {
            $this->apiData['storage_controller'] = "N/A";
        }
    }

    public function SetData()
    {
        if (isset($this->additionalData['Hard_Drive_Dimension_Slot']))
        {
            if (is_array($this->additionalData['Hard_Drive_Dimension_Slot']))
            {
                $this->apiData['hard_drive_dimension'] = $this->additionalData['Hard_Drive_Dimension_Slot']['Hard_Drive_Dimension'] . "_x_" . $this->additionalData['Hard_Drive_Dimension_Slot']['HD_Slot_Qty'];
            }
            else
            {
                $this->apiData['hard_drive_dimension'] = $this->additionalData['Hard_Drive_Dimension_Slot'];
            }
        }
        elseif (isset($this->additionalData['Components']['Hard_Drive_Dimension']) && isset($this->additionalData['Components']['HD_Slot_Qty']))
        {
            $this->apiData['hard_drive_dimension'] = str_replace('"', '', $this->additionalData['Components']['Hard_Drive_Dimension']) . "_x_" . $this->additionalData['Components']['HD_Slot_Qty'];
        }
        else
        {
            $this->apiData['hard_drive_dimension'] = "N/A";
        }

    }

    public function SetGraphic()
    {
        if (isset($this->additionalData['Ports']['Has_Video_Card']) && strtolower($this->additionalData['Ports']['Has_Video_Card']) == "yes")
        {
            if (isset($this->additionalData['Ports']['Graphics_Card_Output']))
            {
                $this->apiData["GraphicsCardOutput"] = $this->additionalData['Ports']['Graphics_Card_Output'];
            }
            else
            {
                $this->apiData["GraphicsCardOutput"] = "N/A";
            }
            if (isset($this->additionalData['Ports']['Graphics_Card']))
            {
                $this->apiData["GraphicsCard"] = $this->additionalData['Ports']['Graphics_Card'];
            }
            else
            {
                $this->apiData["GraphicsCard"] = "N/A";
            }
        }
        else
        {
            $this->apiData["GraphicsCardOutput"] = "N/A";
            $this->apiData["GraphicsCard"] = "N/A";
        }
    }

    public function CreateComputerXml()
    {
         // component GraphicCard
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'GraphicCard');
        // child component GraphicCard
        $component = $components->addChild('components');
        $component->addAttribute('name', 'GraphicCard');

        //Set GraphicsCardOutput
        $component1 = $component->addChild('component', $this->apiData['GraphicsCardOutput']);
        $component1->addAttribute('name', 'GraphicsCardOutput');
        $component1->addAttribute('type', 'string');

        //Set GraphicsCard
        $component2 = $component->addChild('component', $this->apiData['GraphicsCard']);
        $component2->addAttribute('name', 'GraphicsCard');
        $component2->addAttribute('type', 'string');
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
        $this->SetHardDriveData();
        $this->SetOpticleData();
        $this->SetVideoOutput();
        $this->SetMemoryData();
        $this->SetDimensionsData();
        $this->SetPortsData();
        $this->SetMiscellaneousData();
        $this->CheckModel();
        $this->SaveDataArray();
        $this->CreateCommonXml();
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
        elseif (isset($this->additionalData['Part_Number']) && !empty($this->additionalData['Part_Number']))
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


    public function SetHardDriveData()
    {
        $this->apiData['hard_drive'] = array();
        
        //coustomise hard drive data
        $hardDrives = getCustomizeData($this->hardwareData['Devices']['Device']);

        foreach ($hardDrives as $key => $hardDriveData)
        {
            if (isset($hardDriveData['Vendor']) && !empty($hardDriveData['Vendor']))
            {
                if (is_array($hardDriveData['Vendor']))
                {
                    $this->apiData['hard_drive'][$key]['manufacturer'] = array_values($hardDriveData['Vendor'])[0];
                }
                else
                {
                    $this->apiData['hard_drive'][$key]['manufacturer'] = $hardDriveData['Vendor'];
                }
            }
            else
            {
                $this->apiData['hard_drive'][$key]['manufacturer'] = '';
            }

            if (isset($hardDriveData['Vendor']) && !empty($hardDriveData['Vendor']))
            {
                $hardDriveData['Product'] = str_ireplace(array($hardDriveData['Vendor']), array(""), $hardDriveData['Product']);
            }
            else
            {
                $hardDriveData['Product'] = $hardDriveData['Product'];
            }

            //set hard drive model
            $this->apiData['hard_drive'][$key]['model'] = $hardDriveData['Product'];

            //set hard drive part number
            if ($this->productName == "Computer" || $this->productName == "Laptop")
            {
                $this->apiData['hard_drive'][$key]['part_number'] = "N/A";
            }
            elseif (isset($this->additionalData['Hard_Drive_P/N ']))
            {
                $this->apiData['hard_drive'][$key]['part_number'] = $this->additionalData['Hard_Drive_P/N '];
            }
            else
            {
                $this->apiData['hard_drive'][$key]['part_number'] = "N/A";
            }

            //set hard drive serial
            $this->apiData['hard_drive'][$key]['serial'] = $hardDriveData['Serial'];

             //set hard drive capacity
            if (isset($hardDriveData['Gigabytes']) && $hardDriveData['Gigabytes'] > 999)
            {
                $Gigabytes = round($hardDriveData['Gigabytes'] / 1000, 0);
                $this->apiData['hard_drive'][$key]['capacity'] = $Gigabytes . "TB";
            }
            else
            {
                $Gigabytes = round($hardDriveData['Gigabytes'], 0);
                $this->apiData['hard_drive'][$key]['capacity'] = $Gigabytes . "GB";
            }

            //set hard drive interface
            if (isset($this->additionalData['Updated_HDD']) && strtolower($this->additionalData['Updated_HDD']) == 'yes' && isset($this->additionalData['Updated_HDD_Type']))
            {
                $this->apiData['hard_drive'][$key]['interface'] = $this->additionalData['Updated_HDD_Type'];
            }
            else
            {
                if ($this->apiData['hard_drive'][$key]['capacity'] == "No_HD")
                {
                    $this->apiData['hard_drive'][$key]['interface'] = "N/A";
                }
                else
                {
                    $this->apiData['hard_drive'][$key]['interface'] = getHDDInterface($this->hardwareData['Devices']);
                }
            }

            if ($this->productName == "Server") {
                $this->apiData['hard_drive'][$key]['interface'] = "SAS";
            }

            //set hard drive power hours
            $this->apiData['hard_drive'][$key]['power_hours'] = getMakorSmartAttribute($this->jobData['Operation']);

             //set hard drive service performed
            $serviceParfrmedData = getJobOprationServiceParfrmed($this->jobData['Operation'], 1);

            if (!empty($serviceParfrmedData))
            {
                $this->apiData['hard_drive'][$key]['service_parfrmed'] = $serviceParfrmedData;
            }
            else
            {
                $this->apiData['hard_drive'][$key]['service_parfrmed'] = $this->jobData['Operation']['Method'];
            }

             //set hard drive removed
            $this->apiData['hard_drive'][$key]['removed'] = "No";

            //set hard drive size
            if (isset($this->additionalData['Components']['HD_Size']))
            {
                $this->apiData['hard_drive'][$key]['size'] = $this->additionalData['Components']['HD_Size'];
            }
            elseif (isset($this->additionalData['Components']['Hard_Drive_Dimension']))
            {
                $this->apiData['hard_drive'][$key]['size'] = $this->additionalData['Components']['Hard_Drive_Dimension'];
            }
            else
            {
                $this->apiData['hard_drive'][$key]['size'] = 'N/A';
            }

            //set hard drive service queue status
            $this->apiData['hard_drive'][$key]['service_queue_status'] = getMakorServiecsQueueStatus($this->jobData['Operation'], $this->productName);
        }
    }


    public function SetOpticleData()
    {
        if ($this->productName == "Computer" || $this->productName == "Server" || $this->productName == "Laptop" || $this->productName == "All_In_One")
        {
            $this->apiData['optical_drive'] = "";

            //optical manufacturer
            if (isset($this->hardwareData['OpticalDrives']['OpticalDrive'][0]['Product']))
            {
                $opticalDrive = $this->hardwareData['OpticalDrives']['OpticalDrive'][0]['Product'];
            }
            elseif (isset($this->hardwareData['OpticalDrives']['OpticalDrive']['Product']))
            {
                $opticalDrive = $this->hardwareData['OpticalDrives']['OpticalDrive']['Product'];
            }

            if (!empty($opticalDrive))
            {
                $opticalArray = explode(" ", $opticalDrive);
                $opticalArrayCount = count($opticalArray);
                if ($opticalArrayCount > 1)
                {
                    if ($opticalArrayCount > 2)
                    {
                        $opticalDrive = $opticalArray[0] . " " . $opticalArray[1];
                    }
                    else
                    {
                        $opticalDrive = $opticalArray[0];
                    }
                }
            }

            if (isset($this->additionalData['Components']['Optical_Drive']) && !empty($this->additionalData['Components']['Optical_Drive']))
            {
                if (strtolower($this->additionalData['Components']['Optical_Drive']) == "yes")
                {
                    $opticalDrive = "CDDVDW";
                }
                else
                {
                    $opticalDrive = "";
                }
            }
            else
            {
                $opticalDrive = "";
            }

            if (!empty($opticalDrive))
            {
                $this->apiData['optical_drive'] = $opticalDrive;
            }
            else
            {
                $this->apiData['optical_drive'] = "";
            }

        }
        else if ($this->productName == "Storage_Array")
        {
            $this->apiData['optical_drive'] = "N/A";
        }
    }

    public function SetVideoOutput()
    {
        $videoOutput = "";

        // adaptor
        if ($this->productName == "Computer" || $this->productName == "Server")
        {
            $videoOutput = "Integrated Onboard Video";
        }
        elseif ($this->productName == "Laptop" || $this->productName == "All_In_One")
        {
            if (isset($this->hardwareData['DisplayAdapters']['DisplayAdapter'][0]))
            {
                //select first adaptor
                $videoOutput = str_replace(" ", "_", $this->hardwareData['DisplayAdapters']['DisplayAdapter'][0]['Product']);
            }
            else
            {
                $videoOutput = str_replace(" ", "_", $this->hardwareData['DisplayAdapters']['DisplayAdapter']['Product']);
            }
        }

        if (!empty($this->appleData['Video_Card']))
        {
            if (!empty($videoOutput))
            {
                $videoOutput = $videoOutput . ";" . $this->appleData['Video_Card'];
            }
            else
            {
                $videoOutput = $this->appleData['Video_Card'];
            }
        }

        pr( $this->additionalData['Components']  ); 

        if (isset($this->additionalData['Components']['Graphics_Processor']))
        {
            if (is_array($this->additionalData['Components']['Graphics_Processor']) && isset( $this->additionalData['Components']['Graphics_Processor'][0] ))
            {
                $this->apiData['Video_Outputs'][0]['Processor'] = implode(", ", $this->additionalData['Components']['Graphics_Processor']);

            }
            else
            {
                $this->apiData['Video_Outputs'][0]['Processor'] = $this->additionalData['Components']['Graphics_Processor'];
            }
        }
        else
        {
            $this->apiData['Video_Outputs'][0]['Processor'] = $videoOutput;
        }

          pr( $this->additionalData['Ports']  );  die;

        if (isset($this->additionalData['Ports']['Available_Video_Ports']))
        {
            if (is_array($this->additionalData['Ports']['Available_Video_Ports']) && isset( $this->additionalData['Ports']['Available_Video_Ports'][0] ) && !empty( $this->additionalData['Ports']['Available_Video_Ports'][0] ) )
            {
                $this->apiData['Video_Outputs'][0]['Ports'] = implode(", ", $this->additionalData['Ports']['Available_Video_Ports']);
            }
            else
            {
                $this->apiData['Video_Outputs'][0]['Ports'] = $this->additionalData['Ports']['Available_Video_Ports'];
            }
        }
        else
        {
            $this->apiData['Video_Outputs'][0]['Ports'] = "N/A";
        }


    }

    public function SetMemoryData()
    {
        if (isset($this->hardwareData['RAM']['Stick']))
        {
            $ramsData = getCustomizeData($this->hardwareData['RAM']['Stick']);
        }
        else
        {
            $ramsData = getCustomizeData($this->hardwareData['RAM']);
        }

        foreach ($ramsData as $key => $ramData)
        {
            if (isset($ramData['Capacity']))
            {
                $capacity = MBToGB($ramData['Capacity']);
            }
            else
            {
                $capacity = getRAMString($this->hardwareData['RAM']);
            }
            
            $this->apiData['memory'][$key]['capacity'] = $capacity;

            $this->apiData['memory'][$key]['type'] = getMakorRAMType($ramData);
            $this->apiData['memory'][$key]['speed'] = getMakorRAMSpeed($ramData, $this->apiData['memory'][$key]['type']);

            if (isset($this->additionalData['Components']['Memory_Slots']) && !empty($this->additionalData['Components']['Memory_Slots']))
            {
                $this->apiData['memory'][$key]['slots'] = $this->additionalData['Components']['Memory_Slots'];
            }
            else
            {
                $this->apiData['memory'][$key]['slots'] = "N/A";
            }

            if (isset($this->additionalData['RAM_P_N']) && !empty( $this->additionalData['RAM_P_N'] ))
            {
                $this->apiData['memory'][$key]['partnumber'] = $this->additionalData['RAM_P_N'];
            }
            elseif (isset($ramData['PartNumber']) && !empty( $ramData['PartNumber']))
            {
                $this->apiData['memory'][$key]['partnumber'] = $ramData['PartNumber'];
            }
            else
            {
                $this->apiData['memory'][$key]['partnumber'] = "N/A";
            }

            if (isset($this->additionalData['Components']['Max_Memory_Capacity']) && !empty($this->additionalData['Components']['Max_Memory_Capacity']))
            {
                $this->apiData['memory'][$key]['max_memory'] = $this->additionalData['Components']['Max_Memory_Capacity'];
            }
            else
            {
                $this->apiData['memory'][$key]['max_memory'] = "N/A";
            }
        }
    }

    public function SetDimensionsData()
    {
        if (isset($this->additionalData['Dimensions']['Height']) && !empty( $this->additionalData['Dimensions']['Height'] ))
        {
            $this->apiData['height'] = $this->additionalData['Dimensions']['Height'];
        }
        else
        {
            $this->apiData['height'] = "N/A";
        }
        if (isset($this->additionalData['Dimensions']['Width']) && !empty( $this->additionalData['Dimensions']['Width'] ))
        {
            $this->apiData['width'] = $this->additionalData['Dimensions']['Width'];
        }
        else
        {
            $this->apiData['width'] = "N/A";
        }
        if (isset($this->additionalData['Dimensions']['Length']) && !empty( $this->additionalData['Dimensions']['Length'] ))
        {
            $this->apiData['length'] = $this->additionalData['Dimensions']['Length'];
        }
        else
        {
            $this->apiData['length'] = "N/A";
        }
    }

    public function SetPortsData()
    {
        if (isset($this->additionalData['Ports']['RJ_45']) && !empty( $this->additionalData['Ports']['RJ_45'] ) )
        {
            $this->apiData['RJ-45'] = $this->additionalData['Ports']['RJ_45'];
        }
        else
        {
            $this->apiData['RJ-45'] = "N/A";
        }
        if (isset($this->additionalData['Ports']['USB_2_0_Ports']) && !empty( $this->additionalData['Ports']['USB_2_0_Ports'] ))
        {
            $this->apiData['USB2.0'] = $this->additionalData['Ports']['USB_2_0_Ports'];
        }
        else
        {
            $this->apiData['USB2.0'] = "N/A";
        }
        if (isset($this->additionalData['Ports']['USB_3_0_Ports']) && !empty($this->additionalData['Ports']['USB_3_0_Ports']))
        {
            $this->apiData['USB3.0'] = $this->additionalData['Ports']['USB_3_0_Ports'];
        }
        else
        {
            $this->apiData['USB3.0'] = "N/A";
        }
        if (isset($this->additionalData['Ports']['USB_C_Ports']) && !empty($this->additionalData['Ports']['USB_C_Ports'])) 
        {
            $this->apiData['USB-C'] = $this->additionalData['Ports']['USB_C_Ports'];
        }
        else
        {
            $this->apiData['USB-C'] = "N/A";
        }
        if (isset($this->additionalData['Ports']['SD_Card_Reader']) && !empty($this->additionalData['Ports']['SD_Card_Reader']))
        {
            $this->apiData['SD_card_reader'] = $this->additionalData['Ports']['SD_Card_Reader'];
        }
        else
        {
            $this->apiData['SD_card_reader'] = "N/A";
        }
        if (isset($this->additionalData['Ports']['Headphone_Jack']) && !empty($this->additionalData['Ports']['Headphone_Jack']))
        {
            $this->apiData['Headphone_Jack'] = $this->additionalData['Ports']['Headphone_Jack'];
        }
        else
        {
            $this->apiData['Headphone_Jack'] = "N/A";
        }
        if (isset($this->additionalData['Ports']['Microphone_Jack']) && !empty( $this->additionalData['Ports']['Microphone_Jack'] ))
        {
            $this->apiData['Microphone_Jack'] = $this->additionalData['Ports']['Microphone_Jack'];
        }
        else
        {
            $this->apiData['Microphone_Jack'] = "N/A";
        }
    }

    public function SetMiscellaneousData()
    {
        if (isset($this->appleData['EMC']) && !empty($this->appleData['EMC']) && $this->appleData['EMC'] != "EMC None")
        {
            $this->apiData['notes'] = $this->appleData['EMC'];
        }
        elseif (isset($this->additionalData['Notes']))
        {
            $this->apiData['notes'] = $this->additionalData['Notes'];
        }
        else
        {
            $this->apiData['notes'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Case']))
        {
            if (is_array($this->additionalData['Description']['Case']))
            {
                $case = implode(",", $this->additionalData['Description']['Case']);
            }
            else
            {
                $case = $this->additionalData['Description']['Case'];
            }
            $this->apiData['Case'] = $case;
        }
        else
        {
            $this->apiData['Case'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Screen']))
        {
            if (is_array($this->additionalData['Description']['Screen']))
            {
                $screen = implode(",", $this->additionalData['Description']['Screen']);
            }
            else
            {
                $screen = $this->additionalData['Description']['Screen'];
            }
            $this->apiData['Screen'] = $screen;
        }
        else
        {
            $this->apiData['Screen'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Missing']))
        {
            if (is_array($this->additionalData['Description']['Missing']))
            {
                $missing = implode(",", $this->additionalData['Description']['Missing']);
            }
            else
            {
                $missing = $this->additionalData['Description']['Missing'];
            }
            $this->apiData['Missing'] = $missing;
        }
        else
        {
            $this->apiData['Missing'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Cosmetic']))
        {
            if (is_array($this->additionalData['Description']['Cosmetic']))
            {
                $cosemtic = implode(",", $this->additionalData['Description']['Cosmetic']);
            }
            else
            {
                $cosemtic = $this->additionalData['Description']['Cosmetic'];
            }
            $this->apiData['Cosemtic'] = $cosemtic;
        }
        else
        {
            $this->apiData['Cosemtic'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Input_Output']))
        {
            $this->apiData['Input/Output'] = $this->additionalData['Description']['Input_Output'];
        }
        else
        {
            $this->apiData['Input/Output'] = "N/A";
        }

        if (isset($this->appleData['Family']) && !empty($this->appleData['Family']) && $this->appleData['Family'] != "N/A")
        {
            $this->apiData['Other'] = $this->appleData['Family'];
        }
        elseif (isset($this->additionalData['Description']['Other']))
        {
            $this->apiData['Other'] = $this->additionalData['Description']['Other'];
        }
        else
        {
            $this->apiData['Other'] = "N/A";
        }

        if (isset($this->additionalData['Combined_HD_P/N']) && strtolower($this->apiData['Class']) == "server")
        {
            $this->apiData['CombinedHDP/N'] = $this->additionalData['Combined_HD_P/N'];
        }
        else
        {
            $this->apiData['CombinedHDP/N'] = "N/A";
        }

        if (isset($this->additionalData['Combined_RAM_P/N']) && strtolower($this->apiData['Class']) == "server")
        {
            $this->apiData['CombinedRAMP/N'] = $this->additionalData['Combined_RAM_P/N'];
        }
        else
        {
            $this->apiData['CombinedRAMP/N'] = "N/A";
        }

        // RAM Details
        if ($this->productName == "Storage_Array")
        {
            $this->apiData['CombinedRAM'] = "N/A";
        }
        else
        {
            $this->apiData['CombinedRAM'] = getRAMString($this->hardwareData['RAM']);
        }

        if (empty($GET['disable_lenovo_ram']))
        {
            if (strtolower(trim($this->hardwareData['ComputerVendor'])) == "lenovo" && $this->productName == 'Laptop' && strpos($this->apiData['model'], 'THINKPAD X1 CARBON') !== FALSE)
            {
                $this->apiData['CombinedRAM'] = "8GB_x_1";
            }
        }

        if (isset($this->additionalData['RAM']) && !empty($this->additionalData['RAM']))
        {
            if (strtolower($this->additionalData['RAM']) == 'updated')
            {
                $this->apiData['CombinedRAM'] = 'No_RAM';
            }
        }

        if (isset($this->additionalData['Hard_Drive']) && !empty($this->additionalData['Hard_Drive']))
        {
            if (strtolower($this->additionalData['Hard_Drive']) == 'updated' || strtolower($this->additionalData['Hard_Drive']) == 'shred')
            {
                $this->apiData['CombinedRAM'] = 'No_HD';
            }
        }

        if (isset($this->hardwareData['Devices']['Device']['Gigabytes']) && $this->hardwareData['Devices']['Device']['Gigabytes'] <= 10)
        {
            $this->apiData['CombinedHD'] = "No_HD";
        }
        else
        {
            //HARD DISK details
            $this->apiData['CombinedHD'] = getHDDString($this->hardwareData['Devices']);
        }

        $UpdatedHardDrive = getMakorActionResult($this->jobData['Operation'], $this->productName);

        if ($UpdatedHardDrive == "Success")
        {
            $this->apiData['UpdatedHardDrive'] = $this->apiData['CombinedHD'];
        }
        else
        {
            $this->apiData['UpdatedHardDrive'] = "No_HD";
        }

        if (isset($this->additionalData['Description']['Functional']))
        {
            if (is_array($this->additionalData['Description']['Functional']))
            {
                $functional = implode(",", $this->additionalData['Description']['Functional']);
            }
            else
            {
                $functional = $this->additionalData['Description']['Functional'];
            }
            $this->apiData['functional'] = $functional;
        }
        else
        {
            $this->apiData['functional'] = "N/A";
        }
    }

    public function CheckModel()
    {
        if (isset($this->apiData['model']))
        {
            $modelString = str_replace($this->apiData['manufacturer'], "", $this->apiData['model']);
            $modelString = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $modelString)));
            $modelString = str_replace('SFF', "", $modelString);
            $modelString = str_replace('Workstation', "", $modelString);
            $modelString = str_replace('PC', "", $modelString);
            $this->apiData['model'] = $modelString;
        }
    }

    public function SaveDataArray()
    {
        $this->saveDataArray['Model'] = $this->apiData['model'];
        $this->saveDataArray['Serial'] = $this->apiData['serial'];
        $this->saveDataArray['Combined_RAM'] = $this->apiData['CombinedRAM'];
        $this->saveDataArray['Combined_HD'] = $this->apiData['CombinedHD'];
        if (isset($this->apiData['memory'])) {
            foreach ($this->apiData['memory'] as $key => $memoryValue) {
                $this->saveDataArray['MemoryType_Speed'][$key] = $memoryValue['type'] . '_' . $memoryValue['speed'];
            }
        } else {
            $this->saveDataArray['MemoryType_Speed'][0] = '';
        }
        if (isset($this->api_data['hard_drive'])) {
            foreach ($this->api_data['hard_drive'] as $key => $hardDriveValue) {
                $this->saveDataArray['HardDriveType_Interface'][$key] = $hardDriveValue['size'] . '_' . $hardDriveValue['interface'];
            }
        } else {
            $this->saveDataArray['HardDriveType_Interface'][0] = '';
        }
        if (isset($this->apiData['processors'])) {
            $this->saveDataArray['ProcessorModel_Speed'] = $this->apiData['processors'][0]['processor_model'] . '_' . $this->apiData['processors'][0]['processor_speed'];
        } else {
            $this->saveDataArray['ProcessorModel_Speed'][0] = '';
        }
    }

    public function CreateCommonXml()
    {
        // creating object of AuditXMLElement
        $this->audit = new SimpleXMLElement('<audit></audit>');

        // component System
        $this->mainComponents = $this->audit->addChild('components');
        $this->mainComponents->addAttribute('name', 'System');
        $components = $this->mainComponents;

        //Set AssetTag
        $component = $components->addChild('component', "1-DefaultPallet-I");
        $component->addAttribute('name', 'Pallet');
        $component->addAttribute('type', 'string');

        //Set AssetTag
        if (is_array($this->apiData['asset_tag'])) {
            $this->apiData['asset_tag'] = $this->apiData['asset_tag'][0];
        }
        $component = $components->addChild('component', $this->apiData['asset_tag']);
        $component->addAttribute('name', 'Asset');
        $component->addAttribute('type', 'string');

        //Set Class
        if (is_array($this->apiData['Class'])) {
            $this->apiData['Class'] = $this->apiData['Class'][0];
        }
        $component = $components->addChild('component', $this->apiData['Class']);
        $component->addAttribute('name', 'Class');
        $component->addAttribute('type', 'string');

        //Set Serial
        if (is_array($this->apiData['serial'])) {
            $this->apiData['serial'] = $this->apiData['serial'][0];
        }
        $component = $components->addChild('component', $this->apiData['serial']);
        $component->addAttribute('name', 'Serial');
        $component->addAttribute('type', 'string');

        //Set manufacturer
        if (is_array($this->apiData['manufacturer'])) {
            $this->apiData['manufacturer'] = $this->apiData['manufacturer'][0];
        }
        $component = $components->addChild('component', $this->apiData['manufacturer']);
        $component->addAttribute('name', 'Manufacturer');
        $component->addAttribute('type', 'string');

        //Set Model
        if (is_array($this->apiData['model'])) {
            $this->apiData['model'] = $this->apiData['model'][0];
        }
        $component = $components->addChild('component', $this->apiData['model']);
        $component->addAttribute('name', 'Model');
        $component->addAttribute('type', 'string');

        //Set Model#
        if (is_array($this->apiData['model#'])) {
            $this->apiData['model#'] = $this->apiData['model#'][0];
        }
        $component = $components->addChild('component', $this->apiData['model#']);
        $component->addAttribute('name', 'Model#');
        $component->addAttribute('type', 'string');

        //Set Customer Asset
        if (is_array($this->apiData['customer_asset_tag'])) {
            $this->apiData['customer_asset_tag'] = $this->apiData['customer_asset_tag'][0];
        }
        $component = $components->addChild('component', $this->apiData['customer_asset_tag']);
        $component->addAttribute('name', 'CustomerAsset#');
        $component->addAttribute('type', 'string');

        //Set Weight
        if (is_array($this->apiData['weight'])) {
            $this->apiData['weight'] = $this->apiData['weight'][0];
        }
        $component = $components->addChild('component', $this->apiData['weight']);
        $component->addAttribute('name', 'ItemNetWeight');
        $component->addAttribute('type', 'string');

        //Set Grade
        if (is_array($this->apiData['grade'])) {
            $this->apiData['grade'] = $this->apiData['grade'][0];
        }
        $component = $components->addChild('component', $this->apiData['grade']);
        $component->addAttribute('name', 'Grade');
        $component->addAttribute('type', 'string');

        //set NextProcess
        if (is_array($this->apiData['next_process'])) {
            $this->apiData['next_process'] = $this->apiData['next_process'][0];
        }
        $component = $components->addChild('component', $this->apiData['next_process']);
        $component->addAttribute('name', 'NextProcess');
        $component->addAttribute('type', 'string');

        //set ComplianceLabel
        if (is_array($this->apiData['compliance_label'])) {
            $this->apiData['compliance_label'] = $this->apiData['compliance_label'][0];
        }
        $component = $components->addChild('component', $this->apiData['compliance_label']);
        $component->addAttribute('name', 'ComplianceLabel');
        $component->addAttribute('type', 'string');

        //Set Condition
        if (is_array($this->apiData['condition'])) {
            $this->apiData['condition'] = $this->apiData['condition'][0];
        }
        $component = $components->addChild('component', $this->apiData['condition']);
        $component->addAttribute('name', 'Condition');
        $component->addAttribute('type', 'string');


        //Set FormFactor
        if (is_array($this->apiData['form_factor'])) {
            $this->apiData['form_factor'] = $this->apiData['form_factor'][0];
        }
        $component = $components->addChild('component', $this->apiData['form_factor']);
        $component->addAttribute('name', 'FormFactor');
        $component->addAttribute('type', 'string');

        //Set Color
        if (is_array($this->apiData['color'])) {
            $this->apiData['color'] = $this->apiData['color'][0];
        }
        $component = $components->addChild('component', $this->apiData['color']);
        $component->addAttribute('name', 'Color');
        $component->addAttribute('type', 'string');

        //set OperatingSystem
        if (is_array($this->apiData['oprating_system'])) {
            $this->apiData['oprating_system'] = $this->apiData['oprating_system'][0];
        }
        $component = $components->addChild('component', $this->apiData['oprating_system']);
        $component->addAttribute('name', 'OperatingSystem');
        $component->addAttribute('type', 'string');

        //Set Optical Drive
        if (is_array($this->apiData['optical_drive'])) {
            $this->apiData['optical_drive'] = $this->apiData['optical_drive'][0];
        }
        $component = $components->addChild('component', $this->apiData['optical_drive']);
        $component->addAttribute('name', 'OpticalDrive');
        $component->addAttribute('type', 'string');

        //Set RJ-45
        if (is_array($this->apiData['RJ-45'])) {
            $this->apiData['RJ-45'] = $this->apiData['RJ-45'][0];
        }
        $component = $components->addChild('component', $this->apiData['RJ-45']);
        $component->addAttribute('name', 'RJ-45');
        $component->addAttribute('type', 'string');

        //Set USB 2.0
        if (is_array($this->apiData['USB2.0'])) {
            $this->apiData['USB2.0'] = $this->apiData['USB2.0'][0];
        }
        $component = $components->addChild('component', $this->apiData['USB2.0']);
        $component->addAttribute('name', 'USB2.0');
        $component->addAttribute('type', 'string');

        //Set USB 3.0
        if (is_array($this->apiData['USB3.0'])) {
            $this->apiData['USB3.0'] = $this->apiData['USB3.0'][0];
        }
        $component = $components->addChild('component', $this->apiData['USB3.0']);
        $component->addAttribute('name', 'USB3.0');
        $component->addAttribute('type', 'string');

        //Set USB-C
        if (is_array($this->apiData['USB-C'])) {
            $this->apiData['USB-C'] = $this->apiData['USB-C'][0];
        }
        $component = $components->addChild('component', $this->apiData['USB-C']);
        $component->addAttribute('name', 'USB-C');
        $component->addAttribute('type', 'string');

        //Set SD Card Reader
        if (is_array($this->apiData['SD_card_reader'])) {
            $this->apiData['SD_card_reader'] = $this->apiData['SD_card_reader'][0];
        }
        $component = $components->addChild('component', $this->apiData['SD_card_reader']);
        $component->addAttribute('name', 'SDCardReader');
        $component->addAttribute('type', 'string');

        //Set Headphone Jack
        if (is_array($this->apiData['Headphone_Jack'])) {
            $this->apiData['Headphone_Jack'] = $this->apiData['Headphone_Jack'][0];
        }
        $component = $components->addChild('component', $this->apiData['Headphone_Jack']);
        $component->addAttribute('name', 'HeadphoneJack');
        $component->addAttribute('type', 'string');

        //Set Microphone Jack
        if (is_array($this->apiData['Microphone_Jack'])) {
            $this->apiData['Microphone_Jack'] = $this->apiData['Microphone_Jack'][0];
        }
        $component = $components->addChild('component', $this->apiData['Microphone_Jack']);
        $component->addAttribute('name', 'MicrophoneJack');
        $component->addAttribute('type', 'string');

        //Set Height
        if (is_array($this->apiData['height'])) {
            $this->apiData['height'] = $this->apiData['height'][0];
        }
        $component = $components->addChild('component', $this->apiData['height']);
        $component->addAttribute('name', 'Height');
        $component->addAttribute('type', 'string');

        //Set Width
        if (is_array($this->apiData['width'])) {
            $this->apiData['width'] = $this->apiData['width'][0];
        }
        $component = $components->addChild('component', $this->apiData['width']);
        $component->addAttribute('name', 'Width');
        $component->addAttribute('type', 'string');

        //Set Length
        if (is_array($this->apiData['length'])) {
            $this->apiData['length'] = $this->apiData['length'][0];
        }
        $component = $components->addChild('component', $this->apiData['length']);
        $component->addAttribute('name', 'Length');
        $component->addAttribute('type', 'string');

        //Set CombinedHD
        if (is_array($this->apiData['CombinedHD'])) {
            $this->apiData['CombinedHD'] = $this->apiData['CombinedHD'][0];
        }
        $component = $components->addChild('component', $this->apiData['CombinedHD']);
        $component->addAttribute('name', 'CombinedHD');
        $component->addAttribute('type', 'string');

        //Set CombinedHDP/N
        if (is_array($this->apiData['CombinedHDP/N'])) {
            $this->apiData['CombinedHDP/N'] = $this->apiData['CombinedHDP/N'][0];
        }
        $component = $components->addChild('component', $this->apiData['CombinedHDP/N']);
        $component->addAttribute('name', 'CombinedHDP/N');
        $component->addAttribute('type', 'string');

        //Set CombinedRAM
        if (is_array($this->apiData['CombinedRAM'])) {
            $this->apiData['CombinedRAM'] = $this->apiData['CombinedRAM'][0];
        }
        $component = $components->addChild('component', $this->apiData['CombinedRAM']);
        $component->addAttribute('name', 'CombinedRAM');
        $component->addAttribute('type', 'string');

        //Set CombinedRAMP/N
        if (is_array($this->apiData['CombinedRAMP/N'])) {
            $this->apiData['CombinedRAMP/N'] = $this->apiData['CombinedRAMP/N'][0];
        }
        $component = $components->addChild('component', $this->apiData['CombinedRAMP/N']);
        $component->addAttribute('name', 'CombinedRAMP/N');
        $component->addAttribute('type', 'string');

        //Set CombinedRAMP/N
        if (is_array($this->apiData['UpdatedHardDrive'])) {
            $this->apiData['UpdatedHardDrive'] = $this->apiData['UpdatedHardDrive'][0];
        }
        $component = $components->addChild('component', $this->apiData['UpdatedHardDrive']);
        $component->addAttribute('name', 'UpdatedHardDrive');
        $component->addAttribute('type', 'string');

        //Set Motherboard
        $component = $components->addChild('component', 'Passed');
        $component->addAttribute('name', 'Motherboard');
        $component->addAttribute('type', 'string');

        //Set Processor
        $component = $components->addChild('component', 'Passed');
        $component->addAttribute('name', 'Processor');
        $component->addAttribute('type', 'string');

        //Set Memory
        $component = $components->addChild('component', 'Passed');
        $component->addAttribute('name', 'Memory');
        $component->addAttribute('type', 'string');

        //Set Power
        $component = $components->addChild('component', 'Passed');
        $component->addAttribute('name', 'Power');
        $component->addAttribute('type', 'string');

        
        // component Processors
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Processors');

        foreach ($this->apiData['processors'] as $processorData)
        {
            // child component Processor
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Processor');

            //Set Manufacturer
            if (is_array($processorData['processor_manufacturer']))
            {
                $processorData['processor_manufacturer'] = $processorData['processor_manufacturer'][0];
            }
            $component1 = $component->addChild('component', $processorData['processor_manufacturer']);
            $component1->addAttribute('name', 'Manufacturer');
            $component1->addAttribute('type', 'string');

            //Set ProcessorName
            if (is_array($processorData['processor_model']))
            {
                $processorData['processor_model'] = $processorData['processor_model'][0];
            }
            $component2 = $component->addChild('component', $processorData['processor_model']);
            $component2->addAttribute('name', 'Model');
            $component2->addAttribute('type', 'string');

            //Set ProcessorType
            if (is_array($processorData['processor_type']))
            {
                $processorData['processor_type'] = $processorData['processor_type'][0];
            }
            $component3 = $component->addChild('component', $processorData['processor_type']);
            $component3->addAttribute('name', 'Type');
            $component3->addAttribute('type', 'string');

            //Set ProcessorCore
            if (is_array($processorData['processor_core']))
            {
                $processorData['processor_core'] = $processorData['processor_core'][0];
            }
            $component4 = $component->addChild('component', $processorData['processor_core']);
            $component4->addAttribute('name', 'Core');
            $component4->addAttribute('type', 'string');

            //Set ProcessorSpeed
            if (is_array($processorData['processor_speed']))
            {
                $processorData['processor_speed'] = $processorData['processor_speed'][0];
            }
            $component5 = $component->addChild('component', $processorData['processor_speed']);
            $component5->addAttribute('name', 'Speed');
            $component5->addAttribute('type', 'string');

            //Set ProcessorGeneration
            if (is_array($processorData['processor_generation']))
            {
                $processorData['processor_generation'] = $processorData['processor_generation'][0];
            }
            $component6 = $component->addChild('component', $processorData['processor_generation']);
            $component6->addAttribute('name', 'Generation');
            $component6->addAttribute('type', 'string');

            //Set ProcessorCodename
            if (is_array($processorData['processor_codename']))
            {
                $processorData['processor_codename'] = $processorData['processor_codename'][0];
            }
            $component7 = $component->addChild('component', $processorData['processor_codename']);
            $component7->addAttribute('name', 'Codename');
            $component7->addAttribute('type', 'string');

            //Set ProcessorSocket
            if (is_array($processorData['processor_socket'])) {
                $processorData['processor_socket'] = $processorData['processor_socket'][0];
            }
            $component8 = $component->addChild('component', $processorData['processor_socket']);
            $component8->addAttribute('name', 'Socket');
            $component8->addAttribute('type', 'string');

            //Set ProcessorCount
            if (is_array($processorData['processor_qty'])) 
            {
                $processorData['processor_qty'] = $processorData['processor_qty'][0];
            }
            $component9 = $component->addChild('component', $processorData['processor_qty']);
            $component9->addAttribute('name', 'Quantity');
            $component9->addAttribute('type', 'string');
        }

        // component Hard Drive
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Hard_Drives');

        foreach ($this->apiData['hard_drive'] as $hardDriveData)
        {
            // child component Hard Drive
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Hard_Drive');

            //Set Vendor
            if (is_array($hardDriveData['manufacturer'])) {
                $hardDriveData['manufacturer'] = $hardDriveData['manufacturer'][0];
            }
            $component1 = $component->addChild('component', $hardDriveData['manufacturer']);
            $component1->addAttribute('name', 'Manufacturer');
            $component1->addAttribute('type', 'string');

            //Set Model
            if (is_array($hardDriveData['model'])) {
                $hardDriveData['model'] = $hardDriveData['model'][0];
            }
            $component2 = $component->addChild('component', $hardDriveData['model']);
            $component2->addAttribute('name', 'Model');
            $component2->addAttribute('type', 'string');


            if (is_array($hardDriveData['part_number'])) {
                $hardDriveData['part_number'] = $hardDriveData['part_number'][0];
            }
            $component3 = $component->addChild('component', $hardDriveData['part_number']);
            $component3->addAttribute('name', 'PartNumber');
            $component3->addAttribute('type', 'string');

            //Set Serial
            if (is_array($hardDriveData['serial'])) {
                $hardDriveData['serial'] = $hardDriveData['serial'][0];
            }
            $component4 = $component->addChild('component', $hardDriveData['serial']);
            $component4->addAttribute('name', 'Serial#');
            $component4->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($hardDriveData['capacity'])) {
                $hardDriveData['capacity'] = $hardDriveData['capacity'][0];
            }
            $component5 = $component->addChild('component', $hardDriveData['capacity']);
            $component5->addAttribute('name', 'Capacity');
            $component5->addAttribute('type', 'string');

            //Set Interface
            if (is_array($hardDriveData['interface'])) {
                $hardDriveData['interface'] = $hardDriveData['interface'][0];
            }
            $component6 = $component->addChild('component', $hardDriveData['interface']);
            $component6->addAttribute('name', 'Interface');
            $component6->addAttribute('type', 'string');

            //Set Interface
            if (is_array($hardDriveData['power_hours'])) {
                $hardDriveData['power_hours'] = $hardDriveData['power_hours'][0];
            }
            $component7 = $component->addChild('component', $hardDriveData['power_hours']);
            $component7->addAttribute('name', 'PoweronHours');
            $component7->addAttribute('type', 'string');

            //Set Vendor
            if (is_array($hardDriveData['service_parfrmed'])) {
                $hardDriveData['service_parfrmed'] = $hardDriveData['service_parfrmed'][0];
            }
            $component8 = $component->addChild('component', $hardDriveData['service_parfrmed']);
            $component8->addAttribute('name', 'HDServicesPerformed');
            $component8->addAttribute('type', 'string');

            //Set Vendor
            if (is_array($hardDriveData['removed'])) {
                $hardDriveData['removed'] = $hardDriveData['removed'][0];
            }
            $component9 = $component->addChild('component', $hardDriveData['removed']);
            $component9->addAttribute('name', 'Removed');
            $component9->addAttribute('type', 'string');

            //Set Interface
            if (is_array($hardDriveData['size'])) {
                $hardDriveData['size'] = $hardDriveData['size'][0];
            }
            $component10 = $component->addChild('component', $hardDriveData['size']);
            $component10->addAttribute('name', 'Size');
            $component10->addAttribute('type', 'string');

            //Set Interface
            if (is_array($hardDriveData['service_queue_status'])) {
                $hardDriveData['service_queue_status'] = $hardDriveData['service_queue_status'][0];
            }
            $component11 = $component->addChild('component', $hardDriveData['service_queue_status']);
            $component11->addAttribute('name', 'ServiceQueueStatus');
            $component11->addAttribute('type', 'string');
        }

        // component Memorys
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Memorys');

        foreach ($this->apiData['memory'] as $memoryData)
        {
            // child component Memory
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Memory');

            //Set Capacity
            if (is_array($memoryData['capacity'])) {
                $memoryData['capacity'] = $memoryData['capacity'][0];
            }
            $component1 = $component->addChild('component', $memoryData['capacity']);
            $component1->addAttribute('name', 'Capacity');
            $component1->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($memoryData['type'])) {
                $memoryData['type'] = $memoryData['type'][0];
            }
            $component2 = $component->addChild('component', $memoryData['type']);
            $component2->addAttribute('name', 'Type');
            $component2->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($memoryData['partnumber'])) {
                $memoryData['partnumber'] = $memoryData['partnumber'][0];
            }
            $component3 = $component->addChild('component', $memoryData['partnumber']);
            $component3->addAttribute('name', 'PartNumber');
            $component3->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($memoryData['slots'])) {
                $memoryData['slots'] = $memoryData['slots'][0];
            }
            $component4 = $component->addChild('component', $memoryData['slots']);
            $component4->addAttribute('name', 'Slots');
            $component4->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($memoryData['max_memory'])) {
                $memoryData['max_memory'] = $memoryData['max_memory'][0];
            }
            $component5 = $component->addChild('component', $memoryData['max_memory']);
            $component5->addAttribute('name', 'MaximumMemoryCapacity');
            $component5->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($memoryData['speed'])) {
                $memoryData['speed'] = $memoryData['speed'][0];
            }
            $component6 = $component->addChild('component', $memoryData['speed']);
            $component6->addAttribute('name', 'Speed');
            $component6->addAttribute('type', 'string');
        }

        // component  Video Outputs
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Video Outputs');

        foreach ($this->apiData['Video_Outputs'] as $videoOutput)
        {
            // child component  Video Outputs
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Video Output');

            //Set Capacity
            if (is_array($videoOutput['Processor'])) {
                $videoOutput['Processor'] = $videoOutput['Processor'][0];
            }
            $component1 = $component->addChild('component', $videoOutput['Processor']);
            $component1->addAttribute('name', 'GraphicsProcessor');
            $component1->addAttribute('type', 'string');

            //Set Capacity
            if (is_array($videoOutput['Ports'])) {
                $videoOutput['Ports'] = $videoOutput['Ports'][0];
            }
            $component2 = $component->addChild('component', $videoOutput['Ports']);
            $component2->addAttribute('name', 'AvailablePorts');
            $component2->addAttribute('type', 'string');

        }

        // component  Miscellaneous
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Miscellaneous');

        // child component  Miscellaneous
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Miscellaneous');

        //Set Case
        if (is_array($this->apiData['Case'])) {
            $this->apiData['Case'] = $this->apiData['Case'][0];
        }
        $component1 = $component->addChild('component', $this->apiData['Case']);
        $component1->addAttribute('name', 'Case');
        $component1->addAttribute('type', 'string');

        //Set Screen
        if (is_array($this->apiData['Screen'])) {
            $this->apiData['Screen'] = $this->apiData['Screen'][0];
        }
        $component2 = $component->addChild('component', $this->apiData['Screen']);
        $component2->addAttribute('name', 'Screen');
        $component2->addAttribute('type', 'string');

        //set Missing
        if (is_array($this->apiData['Missing'])) {
            $this->apiData['Missing'] = $this->apiData['Missing'][0];
        }
        $component3 = $component->addChild('component', $this->apiData['Missing']);
        $component3->addAttribute('name', 'Missing');
        $component3->addAttribute('type', 'string');

        //Set Cosemtic
        if (is_array($this->apiData['Cosemtic'])) {
            $this->apiData['Cosemtic'] = $this->apiData['Cosemtic'][0];
        }
        $component4 = $component->addChild('component', $this->apiData['Cosemtic']);
        $component4->addAttribute('name', 'Cosemtic');
        $component4->addAttribute('type', 'string');

        //Set Input/Output
        if (is_array($this->apiData['Input/Output'])) {
            $this->apiData['Input/Output'] = $this->apiData['Input/Output'][0];
        }
        $component5 = $component->addChild('component', $this->apiData['Input/Output']);
        $component5->addAttribute('name', 'Input/Output');
        $component5->addAttribute('type', 'string');

        //Set Other
        if (is_array($this->apiData['Other'])) {
            $this->apiData['Other'] = $this->apiData['Other'][0];
        }
        $component6 = $component->addChild('component', $this->apiData['Other']);
        $component6->addAttribute('name', 'Other');
        $component6->addAttribute('type', 'string');

        //Set note
        if (is_array($this->apiData['notes'])) {
            $this->apiData['notes'] = $this->apiData['notes'][0];
        }
        $component7 = $component->addChild('component', $this->apiData['notes']);
        $component7->addAttribute('name', 'Notes');
        $component7->addAttribute('type', 'string');

        //Set CombinedRAMP/N
        if (is_array($this->apiData['functional'])) {
            $this->apiData['asset_tag'] = $this->apiData['functional'][0];
        }
        $component8 = $component->addChild('component', $this->apiData['functional']);
        $component8->addAttribute('name', 'Functional');
        $component8->addAttribute('type', 'string');
    }
}