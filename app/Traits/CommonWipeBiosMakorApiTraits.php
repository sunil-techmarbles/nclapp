<?php
namespace App\Traits;
use SimpleXMLElement;
use App\LenovoModelData;
use App\NewProcessors;

trait CommonWipeBiosMakorApiTraits
{
	public $data, $additionalData, $productName, $assetNumber, $appleData, $appleDataError;
    public $isError = false;
    public $apiData, $audit, $mainComponents , $saveDataArray, $CombinedRAM;
    public $apiDataArray;

	public function init($allDataArray, $BiosAdditionalFileContent, $productName, $assetNumber)
    {
    	$this->apiData = [];
        $this->AddCommomBiosData($allDataArray, $BiosAdditionalFileContent, $productName, $assetNumber);

        if($productName == 'Computer')
        {
        	$this->SetBiosComputerData();
        	$this->SaveDataArrayComputer();
        	$this->CreateBiosComputerXml();
        }
        elseif ($productName == 'Laptop')
        {
        	$this->SetBiosLaptopData();
        	$this->SaveBiosDataArrayLaptop();
        	$this->CreateBiosLaptopXml();
        }
        $this->apiData['xml_data'] = $this->audit->asXML();
        $this->apiData['saveDataArray'] = $this->saveDataArray;
        return $this->apiData;
    }


    public function CreateBiosLaptopXml()
    {
    	//Set Optical Drive
        $component = $this->mainComponents->addChild('component', $this->apiData['optical_drive']);
        $component->addAttribute('name', 'OpticalDrive');
        $component->addAttribute('type', 'string');

        //Set CombinedRAM
        $component = $this->mainComponents->addChild('component', $this->apiData['CombinedRAM']);
        $component->addAttribute('name', 'CombinedRAM');
        $component->addAttribute('type', 'string');

        // component Processors
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Processors');

        foreach ($this->apiData['processors'] as $processorData)
        {
			// child component Processor
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Processor');

			//Set ProcessorCount
            $component6 = $component->addChild('component', $processorData['processor_qty']);
            $component6->addAttribute('name', 'Quantity');
            $component6->addAttribute('type', 'string');

			//Set Manufacturer
            $component1 = $component->addChild('component', $processorData['processor_manufacturer']);
            $component1->addAttribute('name', 'Manufacturer');
            $component1->addAttribute('type', 'string');

			//Set ProcessorName
            $component2 = $component->addChild('component', $processorData['processor_model']);
            $component2->addAttribute('name', 'Model');
            $component2->addAttribute('type', 'string');

			//Set ProcessorType
            $component3 = $component->addChild('component', $processorData['processor_type']);
            $component3->addAttribute('name', 'Type');
            $component3->addAttribute('type', 'string');

			//Set ProcessorCore
            $component4 = $component->addChild('component', $processorData['processor_core']);
            $component4->addAttribute('name', 'Core');
            $component4->addAttribute('type', 'string');

			//Set ProcessorSpeed
            $component5 = $component->addChild('component', $processorData['processor_speed']);
            $component5->addAttribute('name', 'Speed');
            $component5->addAttribute('type', 'string');

			//Set ProcessorGeneration
            $component6 = $component->addChild('component', $processorData['processor_generation']);
            $component6->addAttribute('name', 'Generation');
            $component6->addAttribute('type', 'string');

			//Set ProcessorCodename
            $component7 = $component->addChild('component', $processorData['processor_codename']);
            $component7->addAttribute('name', 'Codename');
            $component7->addAttribute('type', 'string');

			//Set ProcessorSocket
            $component8 = $component->addChild('component', $processorData['processor_socket']);
            $component8->addAttribute('name', 'Socket');
            $component8->addAttribute('type', 'string');
        }

        if (isset($this->apiData['memory_data']))
        {
			// component Memorys
            $components = $this->audit->addChild('components');
            $components->addAttribute('name', 'Memorys');
            
            foreach ($this->apiData['memory_data'] as $memoryData)
            {
				// child component Memory
                $component = $components->addChild('components');
                $component->addAttribute('name', 'Memory');

				//Set Capacity
                $component1 = $component->addChild('component', $memoryData['capacity']);
                $component1->addAttribute('name', 'Capacity');
                $component1->addAttribute('type', 'string');

				//Set Capacity
                $component2 = $component->addChild('component', $memoryData['type']);
                $component2->addAttribute('name', 'Type');
                $component2->addAttribute('type', 'string');

				//Set Capacity
                $component3 = $component->addChild('component', $memoryData['partnumber']);
                $component3->addAttribute('name', 'PartNumber');
                $component3->addAttribute('type', 'string');

				//Set Capacity
                $component4 = $component->addChild('component', $memoryData['slots']);
                $component4->addAttribute('name', 'Slots');
                $component4->addAttribute('type', 'string');

				//Set Capacity
                $component5 = $component->addChild('component', $memoryData['max_memory']);
                $component5->addAttribute('name', 'MaximumMemoryCapacity');
                $component5->addAttribute('type', 'string');

				//Set Capacity
                $component6 = $component->addChild('component', $memoryData['speed']);
                $component6->addAttribute('name', 'Speed');
                $component6->addAttribute('type', 'string');
            }
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
            $component1 = $component->addChild('component', $videoOutput['Processor']);
            $component1->addAttribute('name', 'GraphicsProcessor');
            $component1->addAttribute('type', 'string');

			//Set Capacity
            $component2 = $component->addChild('component', $videoOutput['Ports']);
            $component2->addAttribute('name', 'AvailablePorts');
            $component2->addAttribute('type', 'string');
        }

        // component Screen/Resolution
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Screen/Resolution');

        // child component Screen/Resolution
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Screen/Resolution');

        //Set Size
        $component1 = $component->addChild('component', $this->apiData['Size']);
        $component1->addAttribute('name', 'Size');
        $component1->addAttribute('type', 'string');

        //Set Resolution
        $component2 = $component->addChild('component', $this->apiData['Resolution']);
        $component2->addAttribute('name', 'Resolution');
        $component2->addAttribute('type', 'string');

        //Set Touchscreen
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
        $component1 = $component->addChild('component', $this->apiData['battery']);
        $component1->addAttribute('name', 'Battery');
        $component1->addAttribute('type', 'string');

        //Set BatteriesStatus
        $component2 = $component->addChild('component', $this->apiData['battery_condition']);
        $component2->addAttribute('name', 'BatteryCondition');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        $component2 = $component->addChild('component', $this->apiData['Backlit_Keyboard']);
        $component2->addAttribute('name', 'BacklitKeyboard');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        $component2 = $component->addChild('component', $this->apiData['Fingerprint_Scanner']);
        $component2->addAttribute('name', 'FingerprintScanner');
        $component2->addAttribute('type', 'string');

        //Set BatteriesStatus
        $component2 = $component->addChild('component', $this->apiData['Webcam']);
        $component2->addAttribute('name', 'Webcam');
        $component2->addAttribute('type', 'string');
    }


    public function SaveBiosDataArrayLaptop()
    {
        $this->saveDataArray['Model'] = $this->apiData['model'];
    	$this->saveDataArray['Serial'] = $this->apiData['serial'];
        $this->saveDataArray['Combined_RAM'] = $this->apiData['CombinedRAM'];
        $this->saveDataArray['Combined_HD'] = $this->apiData['CombinedHD'];
        
        if(isset( $this->apiData['memory_data']))
        {
            foreach ($this->apiData['memory_data'] as $key => $memoryValue)
            {
                $this->saveDataArray['MemoryType_Speed'][$key] = $memoryValue['type'] . '_' . $memoryValue['speed'];
            }
        }

        $this->saveDataArray['ProcessorModel_Speed'] = $this->apiData['processors'][0]['processor_model'] . '_' . $this->apiData['processors'][0]['processor_speed'];
    }

    public function SetBiosLaptopData()
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

        $batteryCondition = "";
        
        if (isset($this->additionalData['Peripherials']['Battery_and_Power']))
        {
            $batteryCondition = $this->additionalData['Peripherials']['Battery_and_Power'];
        }

        if ($batteryCondition == "Battery_Yes")
        {
            $battery = "Yes";
        }
        elseif ($batteryCondition == "Battery_No")
        {
            $battery = "No";
        }
        elseif ($batteryCondition == "Extended_Battery_Yes")
        {
            $battery = "Yes_Present";
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

        $this->apiDataArray = getBiosLaptopData($this->data);

        if (isset($this->apiDataArray['memory']))
        {
            foreach ($this->apiDataArray['memory'] as $memoryData)
            {
                if ($memoryData['@attributes']['id'] == "memory" && $memoryData['@attributes']['class'] == "memory")
                {
                    foreach ($memoryData['node'] as $key => $memory)
                    {
                        if (!isset($memory['product']) || $memory['product'] == "[Empty]" && $memory['vendor'] == "[Empty]")
                        {
                            continue;
                        }

                        if (strpos($memory['description'], "DDR ") !== false)
                        {
                            $ramType = "DDR";
                        }
                        elseif (strpos($memory['description'], "DDR2 ") !== false)
                        {
                            $ramType = "DDR2";
                        }
                        elseif (strpos($memory['description'], "DDR3 ") !== false)
                        {
                            $ramType = "DDR3";
                        }
                        elseif (strpos($memory['description'], "DDR4 ") !== false)
                        {
                            $ramType = "DDR4";
                        }
                        else
                        {
                            $ramType = "N/A";
                        }

                        if ($ramType == "N/A") {
                            continue;
                        }

                        $capacity = BToGB($memory['size']);
                        $this->CombinedRAM[$key] = $capacity;

                        $this->apiData['memory_data'][$key]['capacity'] = $capacity . "GB";
                        $this->apiData['memory_data'][$key]['partnumber'] = $memory['product'];

                        $this->CombinedRAM[$key] = $capacity;
                        $this->apiData['memory_data'][$key]['type'] = $ramType;
                        $this->apiData['memory_data'][$key]['speed'] = HzToMHz($memory['clock']);
                        
                        if (isset($this->additionalData['Components']['Memory_Slots']))
                        {
                            $this->apiData['memory_data'][$key]['slots'] = $this->additionalData['Components']['Memory_Slots'];
                        }
                        else
                        {
                            $this->apiData['memory_data'][$key]['slots'] = "N/A";
                        }
                        if (isset($this->additionalData['Components']['Max_Memory_Capacity']))
                        {
                            $this->apiData['memory_data'][$key]['max_memory'] = $this->additionalData['Components']['Max_Memory_Capacity'];
                        }
                        else
                        {
                            $this->apiData['memory_data'][$key]['max_memory'] = "N/A";
                        }
                    }
                }
            }
        }

        if (isset($this->apiDataArray['processor']))
        {
            $processorData = $this->apiDataArray['processor'][0];
            if (isset($processorData['product']))
            {
                $key = 0;
                $apiProcessorData = explode(" ", $processorData['product']);
                $arrayCount = count($apiProcessorData);
                $speed = $arrayCount - 1;

                if (strpos($processorData['product'], "Intel") !== false)
                {
                    $proManufacturer = "Intel";
                }
                elseif (strpos($processorData['product'], "AMD") !== false)
                {
                    $proManufacturer = "AMD";
                }
                elseif (strpos($processorData['product'], "Dell Inc.") !== false)
                {
                    $proManufacturer = "Dell";
                }
                elseif (strpos($processorData['product'], "Hewlett-Packard") !== false)
                {
                    $proManufacturer = "HP";
                }
                else
                {
                    $proManufacturer = "N/A";
                }

                $sqlData = NewProcessors::getProcessorData($apiProcessorData['2']);

                if (isset($sqlData['Manufacturer']) && $sqlData['Manufacturer'] != "NULL")
                {
                    $this->apiData['processors'][$key]['processor_manufacturer'] = $sqlData['Manufacturer'];
                }
                else
                {
                    $this->apiData['processors'][$key]['processor_manufacturer'] = $proManufacturer;
                }
                if (isset($sqlData['Type']) && $sqlData['Type'] != "NULL")
                {
                    $this->apiData['processors'][$key]['processor_type'] = $sqlData['Type'];
                }
                else
                {
                    $this->apiData['processors'][$key]['processor_type'] = 'N/A';
                }

                $this->apiData['processors'][$key]['processor_model'] = $apiProcessorData['2'];

                if (isset($sqlData['Cores']) && $sqlData['Cores'] != "NULL")
                {
                    $this->apiData['processors'][$key]['processor_core'] = $sqlData['Cores'];
                }
                else
                {
                    $cores = getCore($processorData['configuration']['setting']);
                    NewProcessors::updateProcessor($apiProcessorData['2'], $cores);
                    $this->apiData['processors'][$key]['processor_core'] = $cores;
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
                    $speed = $apiProcessorData[$speed];
                }
                $this->apiData['processors'][$key]['processor_speed'] = $speed;
				$this->apiData['processors'][$key]['processor_qty'] = 1;
            }
        }

        if (isset($this->additionalData['Components']['Optical_Drive']) && !empty($this->additionalData['Components']['Optical_Drive']))
        {
            if (strtolower($this->additionalData['Components']['Optical_Drive']) == 'no')
            {
                $opticalDrive = 'No_Optical';
            }
            elseif (strtolower($this->additionalData['Components']['Optical_Drive']) == 'n/a')
            {
                $opticalDrive = 'N/A';
            }
            elseif (strtolower($this->additionalData['Components']['Optical_Drive']) == 'ultrabay battery')
            {
                $opticalDrive = 'Ultrabay Battery';
            }
        }

        if (!empty($opticalDrive))
        {
            $this->apiData['optical_drive'] = $opticalDrive;
        }
        else
        {
            if (isset($this->apiDataArray['storage']))
            {
                $this->apiData['optical_drive'] = getOpticle($this->apiDataArray['storage']);
            }
            else
            {
                $this->apiData['optical_drive'] = "";
            }
        }

        // adaptor
        if (isset($this->apiDataArray['bridge'][0]['node'][0]['product']))
        {
            //select first adaptor
            $videoOutput = str_replace(" ", "_", $this->apiDataArray['bridge'][0]['node'][0]['product']);
        }

        if (isset($this->additionalData['Ports']['Available_Video_Ports']))
        {
            $this->apiData['Video_Outputs'][0]['Processor'] = $videoOutput;
            if (is_array($this->additionalData['Ports']['Available_Video_Ports']))
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
            $this->apiData['Video_Outputs'][0]['Processor'] = $videoOutput;
            $this->apiData['Video_Outputs'][0]['Ports'] = "N/A";
        }

        if (isset($this->apiData['CombinedRAM']))
        {
            $this->apiData['CombinedRAM'] = getMakorRAMString($this->CombinedRAM);
        }
        else
        {
            $this->apiData['CombinedRAM'] = "";
        }
    }


    public function CreateBiosComputerXml()
    {
    	 //Set Optical Drive
        $component = $this->mainComponents->addChild('component', $this->apiData['optical_drive']);
        $component->addAttribute('name', 'OpticalDrive');
        $component->addAttribute('type', 'string');

        //Set CombinedRAM
        $component = $this->mainComponents->addChild('component', $this->apiData['CombinedRAM']);
        $component->addAttribute('name', 'CombinedRAM');
        $component->addAttribute('type', 'string');

        // component Processors
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Processors');

        foreach ($this->apiData['processors'] as $processorData)
        {
        	// child component Processor
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Processor');

        	//Set ProcessorCount
            $component6 = $component->addChild('component', $processorData['processor_qty']);
            $component6->addAttribute('name', 'Quantity');
            $component6->addAttribute('type', 'string');

			//Set Manufacturer
            $component1 = $component->addChild('component', $processorData['processor_manufacturer']);
            $component1->addAttribute('name', 'Manufacturer');
            $component1->addAttribute('type', 'string');

			//Set ProcessorName
            $component2 = $component->addChild('component', $processorData['processor_model']);
            $component2->addAttribute('name', 'Model');
            $component2->addAttribute('type', 'string');

			//Set ProcessorType
            $component3 = $component->addChild('component', $processorData['processor_type']);
            $component3->addAttribute('name', 'Type');
            $component3->addAttribute('type', 'string');

			//Set ProcessorCore
            $component4 = $component->addChild('component', $processorData['processor_core']);
            $component4->addAttribute('name', 'Core');
            $component4->addAttribute('type', 'string');

			//Set ProcessorSpeed
            $component5 = $component->addChild('component', $processorData['processor_speed']);
            $component5->addAttribute('name', 'Speed');
            $component5->addAttribute('type', 'string');

			//Set ProcessorGeneration
            $component6 = $component->addChild('component', $processorData['processor_generation']);
            $component6->addAttribute('name', 'Generation');
            $component6->addAttribute('type', 'string');

			//Set processorData
            $component7 = $component->addChild('component', $processorData['processor_codename']);
            $component7->addAttribute('name', 'Codename');
            $component7->addAttribute('type', 'string');

			//Set ProcessorSocket
            $component8 = $component->addChild('component', $processorData['processor_socket']);
            $component8->addAttribute('name', 'Socket');
            $component8->addAttribute('type', 'string');
        }

		// component Memorys
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Memorys');

        foreach ($this->apiData['memory_data'] as $memoryData) {
			
			// child component Memory
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Memory');

			//Set Capacity
            $component1 = $component->addChild('component', $memoryData['capacity']);
            $component1->addAttribute('name', 'Capacity');
            $component1->addAttribute('type', 'string');

			//Set Type
            $component2 = $component->addChild('component', $memoryData['type']);
            $component2->addAttribute('name', 'Type');
            $component2->addAttribute('type', 'string');

			//Set PartNumber
            $component3 = $component->addChild('component', $memoryData['partnumber']);
            $component3->addAttribute('name', 'PartNumber');
            $component3->addAttribute('type', 'string');

			//Set Slots
            $component4 = $component->addChild('component', $memoryData['slots']);
            $component4->addAttribute('name', 'Slots');
            $component4->addAttribute('type', 'string');

			//Set MaximumMemoryCapacity
            $component5 = $component->addChild('component', $memoryData['max_memory']);
            $component5->addAttribute('name', 'MaximumMemoryCapacity');
            $component5->addAttribute('type', 'string');

			//Set Speed
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
            $component1 = $component->addChild('component', $videoOutput['Processor']);
            $component1->addAttribute('name', 'GraphicsProcessor');
            $component1->addAttribute('type', 'string');

			//Set Capacity
            $component2 = $component->addChild('component', $videoOutput['Ports']);
            $component2->addAttribute('name', 'AvailablePorts');
            $component2->addAttribute('type', 'string');
        }

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


    public function SaveDataArrayComputer()
    {
    	$this->saveDataArray['Model'] = $this->apiData['model'];
        $this->saveDataArray['Serial'] = $this->apiData['serial'];
        $this->saveDataArray['Combined_RAM'] = $this->apiData['CombinedRAM'];
        $this->saveDataArray['Combined_HD'] = $this->apiData['CombinedHD'];
        if(isset($this->apiData['memory_data']))
        {
            foreach ($this->apiData['memory_data'] as $key => $memorValue)
            {
                $this->saveDataArray['MemoryType_Speed'][$key] = $memorValue['type'] . '_' . $memorValue['speed'];
            }
        }
        $this->saveDataArray['ProcessorModel_Speed'] = $this->apiData['processors'][0]['processor_model'] . '_' . $this->apiData['processors'][0]['processor_speed'];
    }


    public function SetBiosComputerData()
    {
    	$this->apiDataArray = getBiosData($this->data);
        if (isset($this->apiDataArray['memory']))
        {
            foreach ($this->apiDataArray['memory'] as $memoryData)
            {
                if ($memoryData['@attributes']['id'] == "memory" && $memoryData['@attributes']['class'] == "memory")
                {
                    foreach ($memoryData['node'] as $key => $memory)
                    {
                        if (!isset($memory['product']) || $memory['product'] == "[Empty]" && $memory['vendor'] == "[Empty]")
                        {
                            continue;
                        }

                        if (strpos($memory['description'], "DDR ") !== false)
                        {
                            $ramType = "DDR";
                        }
                        elseif (strpos($memory['description'], "DDR2 ") !== false)
                        {
                            $ramType = "DDR2";
                        }
                        elseif (strpos($memory['description'], "DDR3 ") !== false)
                        {
                            $ramType = "DDR3";
                        }
                        elseif (strpos($memory['description'], "DDR4 ") !== false)
                        {
                            $ramType = "DDR4";
                        }
                        else {
                            $ramType = "N/A";
                        }

                        if ($ramType == "N/A")
                        {
                            continue;
                        }

                        $capacity = BToGB($memory['size']);
                        $this->CombinedRAM[$key] = $capacity;
                        
                        $this->apiData['memory_data'][$key]['capacity'] = $capacity . "GB";
                        $this->apiData['memory_data'][$key]['partnumber'] = $memory['product'];
                        $this->apiData['memory_data'][$key]['type'] = $ramType;

                        $clock = HzToMHz($memory['clock']);
                        $ramSpeed = getBiosRAMSpeed($ramType, $clock);

                        $this->apiData['memory_data'][$key]['speed'] = $ramSpeed;
                        
                        if (isset($this->additionalData['Components']['Memory_Slots']))
                        {
                            $this->apiData['memory_data'][$key]['slots'] = $this->additionalData['Components']['Memory_Slots'];
                        }
                        else
                        {
                            $this->apiData['memory_data'][$key]['slots'] = "N/A";
                        }

                        if (isset($this->additionalData['Components']['Max_Memory_Capacity']))
                        {
                            $this->apiData['memory_data'][$key]['max_memory'] = $this->additionalData['Components']['Max_Memory_Capacity'];
                        }
                        else
                        {
                            $this->apiData['memory_data'][$key]['max_memory'] = "N/A";
                        }
                    }
                }
            }
        }

        if (isset($this->apiDataArray['processor']))
        {
            $processorData = $this->apiDataArray['processor'][0];
            if (isset($processorData['product']))
            {
                $key = 0;
                $apiProcessorData = explode(" ", $processorData['product']);
                $arrayCount = count($apiProcessorData);
                $speed = $arrayCount - 1;
                if (strpos($processorData['product'], "Intel") !== false)
                {
                    $proManufacturer = "Intel";
                }
                elseif (strpos($processorData['product'], "AMD") !== false)
                {
                    $proManufacturer = "AMD";
                }
                elseif (strpos($processorData['product'], "Dell Inc.") !== false)
                {
                    $proManufacturer = "Dell";
                }
                elseif (strpos($processorData['product'], "Hewlett-Packard") !== false)
                {
                    $proManufacturer = "HP";
                }
                else
                {
                    $proManufacturer = "N/A";
                }

                $sqlData = NewProcessors::getProcessorData($apiProcessorData['2']);

                if (isset($sqlData['Manufacturer']) && $sqlData['Manufacturer'] != "NULL")
                {
                    $this->apiData['processors'][$key]['processor_manufacturer'] = $sqlData['Manufacturer'];
                }
                else
                {
                    $this->apiData['processors'][$key]['processor_manufacturer'] = $proManufacturer;
                }
                
                if (isset($sqlData['Type']) && $sqlData['Type'] != "NULL")
                {
                    $this->apiData['processors'][$key]['processor_type'] = $sqlData['Type'];
                }
                else
                {
                    $this->apiData['processors'][$key]['processor_type'] = 'N/A';
                }

                $this->apiData['processors'][$key]['processor_model'] = $apiProcessorData['2'];

                if (isset($sqlData['Cores']) && $sqlData['Cores'] != "NULL")
                {
                    $this->apiData['processors'][$key]['processor_core'] = $sqlData['Cores'];
                }
                else
                {
                    $cores = getCore($processorData['configuration']['setting']);
                    NewProcessors::updateProcessor($apiProcessorData['2'], $cores);
                    $this->apiData['processors'][$key]['processor_core'] = $cores;
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
                    $speed = $apiProcessorData[$speed];
                }

                $this->apiData['processors'][$key]['processor_speed'] = $speed;

                if (isset($this->productName) && (strtolower($this->productName) == "computer" || strtolower($this->productName) == "laptop"))
                {
                    $this->apiData['processors'][$key]['processor_qty'] = 1;
                }
                else
                {
                    if (isset($this->additionalData['CPU_Count']))
                    {
                        $this->apiData['processors'][$key]['processor_qty'] = $this->additionalData['CPU_Count'];
                    }
                    else
                    {
                        $this->apiData['processors'][$key]['processor_qty'] = 1;
                    }
                }
            }
        }

        if (isset($this->additionalData['Components']['Optical_Drive']) && !empty($this->additionalData['Components']['Optical_Drive']))
        {
            if (strtolower($this->additionalData['Components']['Optical_Drive']) == 'no')
            {
                $opticalDrive = 'No_Optical';
            }
            elseif (strtolower($this->additionalData['Components']['Optical_Drive']) == 'n/a')
            {
                $opticalDrive = 'N/A';
            }
            elseif (strtolower($this->additionalData['Components']['Optical_Drive']) == 'ultrabay battery')
            {
                $opticalDrive = 'Ultrabay Battery';
            }
        }

        if (!empty($opticalDrive))
        {
            $this->apiData['optical_drive'] = $opticalDrive;
        }
        else
        {
            if (isset($this->apiDataArray['storage']))
            {
                $this->apiData['optical_drive'] = getOpticle($this->apiDataArray['storage']);
            }
            else
            {
                $this->apiData['optical_drive'] = "";
            }
        }

        if (isset($this->additionalData['Ports']['Has_Video_Card']) && strtolower($this->additionalData['Ports']['Has_Video_Card']) == "yes")
        {
            if (isset($this->additionalData['Ports']['Graphics_Card_Output']))
            {
                if (is_array($this->additionalData['Ports']['Graphics_Card_Output']))
                {
                    $this->apiData["GraphicsCardOutput"] = implode(",", $this->additionalData['Ports']['Graphics_Card_Output']);
                }
                else
                {
                    $this->apiData["GraphicsCardOutput"] = $this->additionalData['Ports']['Graphics_Card_Output'];
                }
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

        // adaptor
        $videoOutput = "Integrated Onboard Video";

        if (isset($this->additionalData['Ports']['Available_Video_Ports']))
        {
            $this->apiData['Video_Outputs'][0]['Processor'] = $videoOutput;
            if (is_array($this->additionalData['Ports']['Available_Video_Ports']))
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
            $this->apiData['Video_Outputs'][0]['Processor'] = $videoOutput;
            $this->apiData['Video_Outputs'][0]['Ports'] = "N/A";
        }
        $this->apiData['CombinedRAM'] = getMakorRAMString($this->CombinedRAM);
    }

    public function AddCommomBiosData($allDataArray, $BiosAdditionalFileContent, $productName, $assetNumber)
    {
    	$this->data = $allDataArray;
        $this->additionalData = $BiosAdditionalFileContent;
        $this->productName = $productName;
        $this->assetNumber = $assetNumber;

        $this->SetCommonData();
        $this->SetAdditionalData();
        $this->SetVideoOutput();
        $this->CheckModel();
        $this->CreateXml();
    }

    public function SetCommonData()
    {
    	if (strpos($this->data['vendor'], "Intel") !== false)
    	{
            $this->apiData['manufacturer'] = "Intel";
        }
        elseif (strpos($this->data['vendor'], "AMD") !== false)
        {
            $this->apiData['manufacturer'] = "AMD";
        }
        elseif (strpos($this->data['vendor'], "Dell Inc.") !== false)
        {
            $this->apiData['manufacturer'] = "Dell";
        }
        elseif (strpos($this->data['vendor'], "Hewlett-Packard") !== false)
        {
            $this->apiData['manufacturer'] = "HP";
        }
        elseif (strpos($this->data['vendor'], "Lenovo") !== false)
        {
            $this->apiData['manufacturer'] = "Lenovo";
        }
        elseif (strpos(strtolower($this->data['vendor']), "panasonic coropration") !== false)
        {
            $this->apiData['manufacturer'] = "Panasonic";
        }
        else
        {
            $this->apiData['manufacturer'] = $this->data['vendor'];
        }

        $model = explode("(", $this->data['product']);

        $this->apiData['model'] = trim($model[0]);

        $this->apiData['model#'] = "";
        if (strtolower($this->apiData['manufacturer']) == "lenovo")
        {
            $this->apiData['model'] = LenovoModelData::getLenovoManufacturerModel(trim($model[0]));
            $this->apiData['model#'] = trim($model[0]);
        }

        if (strtolower($this->apiData['manufacturer']) == "hp")
        {
            $this->apiData['model'] = trim($model[0]);
            if (isset($this->additionalData['model#']))
            {
                $this->apiData['model#'] = $this->additionalData['model#'];
            }
        }

        $this->apiData['asset_tag'] = $this->assetNumber;
        $this->apiData['serial'] = $this->data['serial'];
    }


    public function SetAdditionalData()
    {
    	if (isset($this->additionalData['Customer_Asset_Tag']))
    	{
            $this->apiData['customer_asset_tag'] = $this->additionalData['Customer_Asset_Tag'];
        }
        else
        {
            $this->apiData['customer_asset_tag'] = "N/A";
        }

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

        if (isset($this->additionalData['Technology']))
        {
            $this->apiData['form_factor'] = $this->additionalData['Technology'];
        }
        else
        {
            $this->apiData['form_factor'] = "N/A";
        }

        if (isset($this->additionalData['Notes']))
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
            $this->apiData['Cosmetic'] = $cosemtic;
        }
        else
        {
            $this->apiData['Cosmetic'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Input_Output']))
        {
            $this->apiData['Input/Output'] = implode(",", $this->additionalData['Description']['Input_Output']);
        }
        else
        {
            $this->apiData['Input/Output'] = "N/A";
        }

        if (isset($this->additionalData['Description']['Other']))
        {
            $this->apiData['Other'] = $this->additionalData['Description']['Other'];
        }
        else
        {
            $this->apiData['Other'] = "N/A";
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

        if (isset($this->additionalData['Dimensions']['Height']))
        {
            $this->apiData['height'] = $this->additionalData['Dimensions']['Height'];
        }
        else
        {
            $this->apiData['height'] = "N/A";
        }

        if (isset($this->additionalData['Dimensions']['Width']))
        {
            $this->apiData['width'] = $this->additionalData['Dimensions']['Width'];
        }
        else
        {
            $this->apiData['width'] = "N/A";
        }

        if (isset($this->additionalData['Dimensions']['Length']))
        {
            $this->apiData['length'] = $this->additionalData['Dimensions']['Length'];
        }
        else
        {
            $this->apiData['length'] = "N/A";
        }

        if (isset($this->additionalData['Combined_HD']))
        {
            $this->apiData['CombinedHD'] = $this->additionalData['Combined_HD'];
        }
        else
        {
            $this->apiData['CombinedHD'] = "No_HD";
        }

        if (isset($this->additionalData['Combined_HD_P/N']))
        {
            $this->apiData['CombinedHDP/N'] = $this->additionalData['Combined_HD_P/N'];
        }
        else
        {
            $this->apiData['CombinedHDP/N'] = "N/A";
        }

        if (isset($this->additionalData['Combined_RAM_P/N']))
        {
            $this->apiData['CombinedRAMP/N'] = $this->additionalData['Combined_RAM_P/N'];
        }
        else
        {
            $this->apiData['CombinedRAMP/N'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['RJ_45']))
        {
            $this->apiData['RJ-45'] = $this->additionalData['Ports']['RJ_45'];
        }
        else
        {
            $this->apiData['RJ-45'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['USB_2_0_Ports']))
        {
            $this->apiData['USB2.0'] = $this->additionalData['Ports']['USB_2_0_Ports'];
        }
        else
        {
            $this->apiData['USB2.0'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['USB_3_0_Ports']))
        {
            $this->apiData['USB3.0'] = $this->additionalData['Ports']['USB_3_0_Ports'];
        }
        else
        {
            $this->apiData['USB3.0'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['USB_C_Ports']))
        {
            $this->apiData['USB-C'] = $this->additionalData['Ports']['USB_C_Ports'];
        }
        else
        {
            $this->apiData['USB-C'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['SD_Card_Reader']))
        {
            $this->apiData['SD_card_reader'] = $this->additionalData['Ports']['SD_Card_Reader'];
        }
        else
        {
            $this->apiData['SD_card_reader'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['Headphone_Jack']))
        {
            $this->apiData['Headphone_Jack'] = $this->additionalData['Ports']['Headphone_Jack'];
        }
        else
        {
            $this->apiData['Headphone_Jack'] = "N/A";
        }

        if (isset($this->additionalData['Ports']['Microphone_Jack']))
        {
            $this->apiData['Microphone_Jack'] = $this->additionalData['Ports']['Microphone_Jack'];
        }
        else
        {
            $this->apiData['Microphone_Jack'] = "N/A";
        }
    }

    public function SetVideoOutput()
    {
    	if (isset($this->additionalData['Components']['Graphics_Processor']))
    	{
            if (is_array($this->additionalData['Components']['Graphics_Processor']))
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
            $this->apiData['Video_Outputs'][0]['Processor'] = $video_output;
        }

        if (isset($this->additionalData['Ports']['Available_Video_Ports']))
        {
            if (is_array($this->additionalData['Ports']['Available_Video_Ports']))
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

    public function CheckModel()
    {
    	if (isset($this->apiData['model']) && !empty($this->apiData['model']))
    	{
            $modelString = str_replace($this->apiData['manufacturer'], "", $this->apiData['model']);
            $modelString = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $modelString)));
            $modelString = str_replace('SFF', "", $modelString);
            $modelString = str_replace('Workstation', "", $modelString);
            $modelString = str_replace('PC', "", $modelString);
            $this->apiData['model'] = $modelString;
        }
    }

    public function CreateXml()
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
        $component = $components->addChild('component', $this->apiData['asset_tag']);
        $component->addAttribute('name', 'Asset');
        $component->addAttribute('type', 'string');

		//Set Class
        $component = $components->addChild('component', $this->productName);
        $component->addAttribute('name', 'Class');
        $component->addAttribute('type', 'string');

		//Set Serial
        $component = $components->addChild('component', $this->apiData['serial']);
        $component->addAttribute('name', 'Serial');
        $component->addAttribute('type', 'string');

		//Set manufacturer
        $component = $components->addChild('component', $this->apiData['manufacturer']);
        $component->addAttribute('name', 'Manufacturer');
        $component->addAttribute('type', 'string');

		//Set Model
        $component = $components->addChild('component', $this->apiData['model']);
        $component->addAttribute('name', 'Model');
        $component->addAttribute('type', 'string');

		//Set Model#
        $component = $components->addChild('component', $this->apiData['model#']);
        $component->addAttribute('name', 'Model#');
        $component->addAttribute('type', 'string');

		//Set Customer Asset
        $component = $components->addChild('component', $this->apiData['customer_asset_tag']);
        $component->addAttribute('name', 'CustomerAsset#');
        $component->addAttribute('type', 'string');

		//Set Weight
        $component = $components->addChild('component', $this->apiData['weight']);
        $component->addAttribute('name', 'ItemNetWeight');
        $component->addAttribute('type', 'string');

		//Set Grade
        $component = $components->addChild('component', $this->apiData['grade']);
        $component->addAttribute('name', 'Grade');
        $component->addAttribute('type', 'string');

		//set NextProcess
        $component = $components->addChild('component', $this->apiData['next_process']);
        $component->addAttribute('name', 'NextProcess');
        $component->addAttribute('type', 'string');

		//set ComplianceLabel
        $component = $components->addChild('component', $this->apiData['compliance_label']);
        $component->addAttribute('name', 'ComplianceLabel');
        $component->addAttribute('type', 'string');

		//Set Condition
        $component = $components->addChild('component', $this->apiData['condition']);
        $component->addAttribute('name', 'Condition');
        $component->addAttribute('type', 'string');

		//Set FormFactor
        $component = $components->addChild('component', $this->apiData['form_factor']);
        $component->addAttribute('name', 'FormFactor');
        $component->addAttribute('type', 'string');

		//Set Color
        $component = $components->addChild('component', $this->apiData['color']);
        $component->addAttribute('name', 'Color');
        $component->addAttribute('type', 'string');

		//set OperatingSystem
        $component = $components->addChild('component', $this->apiData['oprating_system']);
        $component->addAttribute('name', 'OperatingSystem');
        $component->addAttribute('type', 'string');

		//Set RJ-45
        $component = $components->addChild('component', $this->apiData['RJ-45']);
        $component->addAttribute('name', 'RJ-45');
        $component->addAttribute('type', 'string');

		//Set USB 2.0
        $component = $components->addChild('component', $this->apiData['USB2.0']);
        $component->addAttribute('name', 'USB2.0');
        $component->addAttribute('type', 'string');

		//Set USB 3.0
        $component = $components->addChild('component', $this->apiData['USB3.0']);
        $component->addAttribute('name', 'USB3.0');
        $component->addAttribute('type', 'string');

		//Set USB-C
        $component = $components->addChild('component', $this->apiData['USB-C']);
        $component->addAttribute('name', 'USB-C');
        $component->addAttribute('type', 'string');

		//Set SD Card Reader
        $component = $components->addChild('component', $this->apiData['SD_card_reader']);
        $component->addAttribute('name', 'SDCardReader');
        $component->addAttribute('type', 'string');

		//Set Headphone Jack
        $component = $components->addChild('component', $this->apiData['Headphone_Jack']);
        $component->addAttribute('name', 'HeadphoneJack');
        $component->addAttribute('type', 'string');

		//Set Microphone Jack
        $component = $components->addChild('component', $this->apiData['Microphone_Jack']);
        $component->addAttribute('name', 'MicrophoneJack');
        $component->addAttribute('type', 'string');

		//Set Height
        $component = $components->addChild('component', $this->apiData['height']);
        $component->addAttribute('name', 'Height');
        $component->addAttribute('type', 'string');

		//Set Width
        $component = $components->addChild('component', $this->apiData['width']);
        $component->addAttribute('name', 'Width');
        $component->addAttribute('type', 'string');

		//Set Length
        $component = $components->addChild('component', $this->apiData['length']);
        $component->addAttribute('name', 'Length');
        $component->addAttribute('type', 'string');

		//Set CombinedHDP/N
        $component = $components->addChild('component', $this->apiData['CombinedHDP/N']);
        $component->addAttribute('name', 'CombinedHDP/N');
        $component->addAttribute('type', 'string');

        //Set CombinedHDP/N
        $component = $components->addChild('component', $this->apiData['CombinedHD']);
        $component->addAttribute('name', 'CombinedHD');
        $component->addAttribute('type', 'string');

		//Set CombinedRAMP/N
        $component = $components->addChild('component', $this->apiData['CombinedRAMP/N']);
        $component->addAttribute('name', 'CombinedRAMP/N');
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

        // component  Miscellaneous
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Miscellaneous');

		// child component  Miscellaneous
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Miscellaneous');

		//Set Case
        $component1 = $component->addChild('component', $this->apiData['Case']);
        $component1->addAttribute('name', 'Case');
        $component1->addAttribute('type', 'string');

		//Set Screen
        $component2 = $component->addChild('component', $this->apiData['Screen']);
        $component2->addAttribute('name', 'Screen');
        $component2->addAttribute('type', 'string');

		//set Missing
        $component3 = $component->addChild('component', $this->apiData['Missing']);
        $component3->addAttribute('name', 'Missing');
        $component3->addAttribute('type', 'string');

		//Set Cosemtic
        $component4 = $component->addChild('component', $this->apiData['Cosmetic']);
        $component4->addAttribute('name', 'Cosmetic');
        $component4->addAttribute('type', 'string');

		//Set Input/Output 
        $component5 = $component->addChild('component', $this->apiData['Input/Output']);
        $component5->addAttribute('name', 'Input/Output');
        $component5->addAttribute('type', 'string');

		//Set Other
        $component6 = $component->addChild('component', $this->apiData['Other']);
        $component6->addAttribute('name', 'Other');
        $component6->addAttribute('type', 'string');

		//Set note
        $component7 = $component->addChild('component', $this->apiData['notes']);
        $component7->addAttribute('name', 'Notes');
        $component7->addAttribute('type', 'string');

		//Set Functional
        $component8 = $component->addChild('component', $this->apiData['functional']);
        $component8->addAttribute('name', 'Functional');
        $component8->addAttribute('type', 'string');

        // component  Video Outputs
        $components = $this->audit->addChild('components');
        $components->addAttribute('name', 'Video Outputs');
        
        foreach ($this->apiData['Video_Outputs'] as $videoOutput)
        {
            // child component  Video Outputs
            $component = $components->addChild('components');
            $component->addAttribute('name', 'Video Output');

            //Set Capacity
            $component1 = $component->addChild('component', $videoOutput['Processor']);
            $component1->addAttribute('name', 'GraphicsProcessor');
            $component1->addAttribute('type', 'string');

            //Set Capacity
            $component2 = $component->addChild('component', $videoOutput['Ports']);
            $component2->addAttribute('name', 'AvailablePorts');
            $component2->addAttribute('type', 'string');
        }
    }
}