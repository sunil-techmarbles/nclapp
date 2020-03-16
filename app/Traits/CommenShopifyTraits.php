<?php
namespace App\Traits;

trait CommenShopifyTraits
{
	public $data, $runningList, $insertDataArray, $appleData, $title;
	private $definedDescs = [
		'tagline_blurb' => [
			'Big performance, compact design',
			'Iconic design, ultra-modern features',
			'Big business, small package',
			'A sleek superpower on your desk',
			'Latest generation of robust performance',
			'Powerful and light on its feet',
			'Visually stunning performance',
			'Performance and portability in one package',
			'Speed of light performance for any project',
			'The portable performance and reliability your businesses needs',
			'Feature-rich, portable powerhouse in a business-class of its own',
			'Small and light with unrivaled features and performance',
			'All-inclusive design with the tools your business needs',
			'Extra power, unprecedented performance',
			'Take your computing experience to a whole new level',
			'A sexy, amazingly powerful machine that performs as good as it looks'
		],
		'processer_blurb' => [
			'With Intel Core CPUs that supercharge performance',
			'With speeds that leave previous generations of this iconic machine in the dust',
			"Satisfies your business\'s need for speed",
			'Better performance when using processor-heavy applications',
			'Do what you do faster with Intel Core processors on board',
			'Faster than ever before, from launching apps to higher-level computing',
			'Speed through your to-do list',
			'The performance you need to tackle your desktop to-do list',
			'Extra power, unprecedented performance'
		],
		'hard_drive_blurb' => [
			'With quality storage, you enjoy powerful, quick performance when opening files and applications',
			'Upgradeable to an SSD drive for quicker launching and loading.',
			'The budget-friendlier hard drive is perfect for both high capacity and high performance',
			'The performance to read data lightning-fast, the affordability to fit any budget',
			'Power up and load at the speed of light with insane read and write speeds.',
			'Capacious and affordable, with an impressive amount of storage for your data',
			'Split-second load time, nimble navigation',
			'With the snappy hard drive, you get big storage that moves',
			'Maximum space at maximum velocity',
			'Faster, more efficient, and roomier—from boot to launch',
			'Extra storage, unprecedented performance',
			'Absurdly fast storage, and lots of it'
		],
		'memory_blurb' => [
			'Stunning capacity to store your data and run the apps you use every day',
			'Extra power, unprecedented performance',
			'Powerful performance, unprecedented power',
			'Run apps faster with up to 16GB of memory',
			'Faster performance, from the smallest file to the most professional software',
			'Customizable options available to meet your requirements of a highperforming system'
		],
	];

	private $staticText = "At RefurbConnect we do not take our Microsoft Authorized Refurbisher designation lightly. That means before putting the “certified refurbished” stamp on this {{Model}}, our Certified technicians lovingly restored the device by following the comprehensive RefurbConnect 15-Step Refurbishing Process. In accordance with strict environmental standards, we ensured that this {{Mfg}} {{Model}} refurbished Computer was cosmetically and functionally perfect, before packaging it, along with all OEM accessories and literature, in an innovative custom box, ready to ship to you. Like all of our products, this device carries a minimum 90-day warranty, so you can feel as confident in your refurbished purchase as we do. RefurbConnect: Like new, but greener.Tested for Key Functions,  R2/Ready for Resale.";

	private $extendedText = 'Extended Warranty Options<br>Looking for longer-term peace of mind? We offer all of our customers the opportunity to purchase One-, Two- and Three-Year Extended Warranties. All of these warranties come with the same coverage as the standard One-Year Warranty outlined above (in the Taking Care of Business section).';

	private $takingCareText = "Taking Care of Business<br>Our business customers benefit from a standard One-Year Warranty at no extra cost. Just like under the 90-Day Limited Warranty, if this device is damaged during delivery, or there's something wrong with the hardware we installed, we will repair or replace it. This warranty also covers any issue resulting from the operating system installed by RefurbConnect. We don't cover any software or issues resulting from software installed by the customer. If within 1 year of delivery your device isn't working right and hardware (or the software we installed) is the problem, RefurbConnect will, at our discretion: replace the device or repair the device or provide the necessary parts to repair the device or replace the device with a comparable machine. To make sure the workflow keeps on flowing, we also offer business clients standard Advance Replacement, which means that if there is an issue, your replacement device is on its way to you immediately—no need to wait until we have confirmed your return. Once you receive the replacement unit, simply put the faulty device in the packaging the replacement came in, and stick on the return label we emailed. Once that's done, just let the RefurbConnect team know and we'll even schedule a pick-up. It couldn't be easier.";

	private $replacementText = "90-Day Limited Replacement Warranty<br>If this device is damaged during delivery, or the hardware we installed is not performing as it should, we got you covered and will repair or replace it. Simple as that. This warranty also covers any issue resulting from the operating system installed by RefurbConnect, such as Microsoft Windows. We don't cover any software or issues resulting from software installed by the customer. If within 90 days of delivery your device isn't working right and hardware (or the software we installed) is the problem, RefurbConnect will, at our discretion: replace the device or repair the device or provide the necessary parts to repair the device or replace the device with a comparable machine.";

	private $helpText = "We help you get your work done by getting you the best IT equipment available. Whether it's a refurbished laptop, PC, smartphone, tablet, or printer, our comprehensively trained and A+ certified technicians restore all equipment to its original manufacturer's specs, ensuring at every step that the client device performs to the highest standard. <br>Every machine we restore starts as a pre-owned device sourced from responsible, globally-minded businesses, and goes through our comprehensive in-house refurbishing process. By strictly adhering to environmental protocols in reusing, recycling, and refurbishing our equipment, we help minimize IT pollution—as do our clients.";

	public function init($runningList, $insertDataArray, $appleData, $productType, $type)
	{
		$this->runningList = $runningList;
		$this->insertDataArray = $insertDataArray;
		$this->data = [];
		if($type == 'apple')
		{
			$this->appleData = $appleData;
	        if (strtolower($this->runningList['manufacturer']) == 'hp')
	        {
	            $this->runningList['manufacturer'] = 'HP';
	        }
	        else
	        {
	            $this->runningList['manufacturer'] = ucwords(strtolower($this->runningList['manufacturer']));
	        }
	        $series = $this->appleSeries();
	        $year = $this->appleYear();
	        $emcYear = $this->emcAppleYear();
		}
		$Processor = "Intel Core " . $this->runningList['cpu_core'] . " " . $this->insertDataArray['processer_gen'];
		switch ($type)
		{
			case 'computer':
				$tags = "Series: " . ucwords(strtolower($this->insertDataArray['series'])) . ",Product Type: " . ucwords(strtolower($this->runningList['form_factor'])) . ",Processor: " . ucwords(strtolower($Processor)) . ",Operating System: Windows,Model: " . ucwords(strtolower($this->runningList['model'])) . ",Condition: Refurbished,Brand: " . ucwords(strtolower($this->runningList['manufacturer']));
				break;
			case 'apple':
				$tags = "Series: " . $series . ",Screen Size: " . ucwords(strtolower(trim($this->insertDataArray['screen_size']))) . ",Release Year: " . $year . ",EMC: " . $emc_year . ",Product Type: " . ucwords(strtolower($this->runningList['form_factor'])) . ",Processor: " . ucwords(strtolower($Processor)) . ",Operating System: Most Recent Compatible Mac OS X,Model: " . ucwords(strtolower($this->runningList['model'])) . ",Condition: Refurbished,Brand: " . $this->runningList['manufacturer'];
				break;
			case 'laptop':
				$tags = "Series: " . ucwords(strtolower($this->insertDataArray['series'])) . ",Product Type: " . ucwords(strtolower($this->runningList['form_factor'])). ",Screen Size: " . $this->insertDataArray['screen_size'] . ",Processor: " . ucwords(strtolower($Processor)) . ",Operating System: Windows,Model: " . ucwords(strtolower($this->runningList['model'])) . ",Condition: Refurbished,Brand: " . ucwords(strtolower($this->runningList['manufacturer']));
				break;
			default:
				$tags = "";
				break;
		}
		$data['product'] = [
			'title' => $this->getTitle($type),
			'body_html' => $this->getDescription($type),
			'vendor' => $this->runningList['manufacturer'],
			'product_type' => $productType,
			'tags' => $tags,
		];
		return $this->data = $data;
	}

	private function appleModelCombined()
	{
        $appleModelCombined = $this->appleData['Apple_Model_Combined'];
        $appleModelCombined = substr($appleModelCombined, 0, strpos($appleModelCombined, "("));
        $appleModelCombinedNew = preg_replace('/[0-9]+/', '', trim($appleModelCombined));
        $appleModelCombinedNew = str_replace(',', '', $appleModelCombinedNew);
        $appleModelCombinedNew = str_replace($appleModelCombinedNew, $appleModelCombinedNew . ' ', $appleModelCombined);
        return $appleModelCombinedNew;
    }

    private function appleSeries()
    {
        $appleModelCombined = $this->appleData['Apple_Model_Combined'];
        $appleModelCombined = substr($appleModelCombined, 0, strpos($appleModelCombined, "("));
        $appleModelCombinedNew = preg_replace('/[0-9]+/', '', trim($appleModelCombined));
        $appleModelCombinedNew = str_replace(',', '', $appleModelCombinedNew);
        return $appleModelCombinedNew;
    }

    private function appleYear()
    {
        $array = explode('-', $this->appleData['Other_Data']);
        $yearText = end($array);
        $yearNumber = preg_replace("/[^0-9]{1,4}/", '', $yearText);
        $year = substr($yearNumber, 0, 5);
        return $year;
    }

    private function emcAppleYear()
    {
        $array = explode('-', $this->appleData['EMC']);
        $yearText = end($array);
        $yearNumber = preg_replace("/[^0-9]{1,4}/", '', $yearText);
        $year = substr($yearNumber, 0, 5);
        return $year;
    }

	
	private function getTitle($type)
	{
		switch ($type)
		{
			case 'computer':
				$removeText = array(' non-vPro ', ' DT ', ' CMT ', ' SFF ', ' USDT ', ' sff ', ' DM ', ' TWR ', ' MT ');
				$title = $this->runningList['manufacturer'] . " " . $this->runningList['model'] . " Intel Core " . $this->runningList['cpu_core'] . " " . $this->insertDataArray['processer_gen'] . " " . $this->runningList['form_factor'] . "";
				break;
			case 'apple':
				$appleModelCombined = $this->appleModelCombined();
        		$year = $this->Apple_Year();
        		$removeText = array(' non-vPro ', ' DT ', ' CMT ', ' SFF ', ' USDT ', ' sff ', ' DM ', ' TWR ', ' MT ');
        		$title = $this->runninglist['manufacturer'] . " " . ucwords(strtolower(trim($this->insertDataArray['screen_size']))) . " " . trim($appleModelCombined) . " " . trim($this->appleData['Apple_Order_No']) . " Intel Core " . ucwords(strtolower(trim($this->runninglist['cpu_core']))) . " " . trim($this->insertDataArray['processer_gen']);
				break;
			case 'laptop':
				$removeText = array(' non-vPro ', ' DT ', ' CMT ', ' SFF ', ' USDT ', ' sff ', ' DM ', ' TWR ', ' MT ', ' AIO ');
				$title = $this->runningList['manufacturer'] . " " . $this->runningList['model'] . " " . $this->insertDataArray['screen_size'] . " Intel Core " . $this->runningList['cpu_core'] . " " . $this->insertDataArray['processer_gen'] . " " . $this->runningList['form_factor'];
				break;
			default:
				$tags = "";
				break;
		}
		if($type == 'apple')
		{
			if (!empty($year))
			{
	            $title = $title . " - " . $year;
	        }
		}
		foreach ($removeText as $remove) 
		{
			if (strpos($title, $remove) !== FALSE)
			{
				$title = str_replace($title, '', $title);
			}
		}

		if($type == 'apple')
		{
			$this->title = $title;
        	return $this->title;
		}
		else
		{
			return ucwords(strtolower(trim($title)));
		}
	}

	private function getDescription($type)
	{
		$descriptionRandom = array();
		foreach ($this->definedDescs as $key => $descriptionData) 
		{
            $arrayCount = count($descriptionData); //4
            $randomKey = rand(0, $arrayCount - 1); //0,3 -> 1, 0, 3
            $convertedString = "";
            $string = $descriptionData[$randomKey];
            if (preg_match_all('/{{+(.*?)}}/', $string, $matches))
            {
            	if (!empty($matches[1]))
            	{
            		$convertedString = $string;
            		foreach ($matches[1] as $matchKey)
            		{
            			if (isset($this->insertDataArray[$matchKey]) && !empty($this->insertDataArray[$matchKey])) 
            			{
            				$convertedString = str_replace("{{" . $matchKey . "}}", $this->insertDataArray[$matchKey], $convertedString);
            			}
            		}
            	}
            }

            if (!empty($convertedString))
            {
            	$descriptionSingle = $convertedString;
            }
            else
            {
            	$descriptionSingle = $string;
            }
            if ($key == 'tagline_blurb')
            {
            	$tagline = $descriptionSingle;
            }
            else
            {
            	$descriptionRandom[$key] = $descriptionSingle;
            }
        }

        switch ($type)
        {
        	case 'computer':
	        	$descHtml = $this->getComputerDescriptionHTML();
	        	break;
        	case 'apple':
				$descHtml = $this->getAppleDescriptionHTML();
        		break;
        	case 'laptop':
	        	$descHtml = $this->getLaptopDescriptionHTML();
	        	break;
        	default:
	        	$descHtml = "";
	        	break;
        }
        $descHtml = str_replace("{{tagline_blurb}}", $tagline, $descHtml);
        $descHtml = str_replace("{{combined_blurb}}", implode(".", $descriptionRandom), $descHtml);
        foreach ($descriptionRandom as $key => $descriptionRandomSingle)
        {
        	$descHtml = str_replace("{{" . $key . "}}", $descriptionRandomSingle, $descHtml);
        }

        return $descHtml;
    }
    
    private function getComputerDescriptionHTML()
    {
    	$staticText = str_replace("{{Model}}", $this->insertDataArray['model'], $this->staticText);
    	$staticText = str_replace("{{Mfg}}", $this->runningList['manufacturer'], $staticText);
    	return "<p><span class='a-list-item'>---</span></p><h5><span>Overview </span></h5>"
    	. "<ul><li>{{tagline_blurb}}</li>"
    	. "<li>{{combined_blurb}}</li>"
    	. "<p style='text-align: left;'> </p>"
    	. "<li>" . $staticText . "</li></ul>"
    	. "<h5><span class='a-list-item'>Specifications</span></h5>"
    	. "<ul><li>Form Factor: " . $this->runningList['form_factor'] . "</li>"
    	. "<li>Processor: Intel Core " . $this->runningList['cpu_core'] . " " . $this->insertDataArray['processer_gen'] . "</li>"
    	. "<li>RAM: Customizable</li>"
    	. "<li>Hard Drive: Customizable</li>"
    	. "<li>Graphic Processor: " . $this->insertDataArray['graphics_processor'] . "</li>"
    	. "<li>Operating System: Customizable</li>"
    	. "<li>Available Ports: " . $this->insertDataArray['available_port'] . "</li>"
    	. "<li>Available Video Ports: " . $this->insertDataArray['available_vedio_port'] . "</li>"
    	. "<li>Dimensions: " . $this->insertDataArray['width'] . " X " . $this->insertDataArray['length'] . " X " . $this->insertDataArray['height'] . " inches</li>"
    	. "<li>Weight: " . $this->insertDataArray['weight'] . "</li>"
    	. "<li>Tested for Key Functions,  R2/Ready for Resale</li></ul>"
    	. "<h5>In The Box</h5>"
    	. "<ul>"
    	. "<li>Your perfect, like-new (but greener) refurbished " . $this->runningList['model'] . "</li>"
    	. "<li>All standard manufacturer's literature, such as documentation and setup manual</li>"
    	. "<li>Windows 10 Certificate of Authenticity (COA) and License Key (if applicable)</li>"
    	. "<li>AC Power Cable</li>"
    	. "<li>Standard manufacturer's accessories, such as keyboard and mouse</li>"
    	. "</ul>"
    	. "<h5>Warranty</h5>"
    	. "<ul>"
    	. "<li>We stand behind every piece of refurbished equipment we deliver and offer some of the most comprehensive warranties in the business—so you can be as confident in our products as we are.</li>"
    	. "<li>30-Day Money-Back Guarantee<br>If for any reason you are not 100% satisfied with this device, send it back to us within 30 days and we'll issue a full refund. Guaranteed, no questions. We get it. It happens.</li>"
    	. "<li>".$this->replacementText."</li>"
    	. "<li>".$this->takingCareText."</li>"
    	. "<li>".$this->extendedText."</li>"
    	. "</ul>"
    	. "<strong>Extended warranty includes:</strong>"
    	. "<ul><li>Advanced replacement</li>"
    	. "<li>Repair - Parts and labor</li>"
    	. "<li>Replacement</li></ul>"
    	. "<h5>What we do</h5>"
    	. "<p>".$this->helpText."</p>   
    	<p>ASIN : " . $this->runningList['asin'] . "</p>";
    }

    private function getLaptopDescriptionHTML()
    {
    	$staticText = str_replace("{{Model}}", $this->insertDataArray['model'], $this->staticText);
    	$staticText = str_replace("{{Mfg}}", $this->runningList['manufacturer'], $staticText);
    	return "<p><span class='a-list-item'>---</span></p><h5><span>Overview </span></h5>"
    	. "<ul><li>{{tagline_blurb}}</li>"
    	. "<li>{{combined_blurb}}</li>"
    	. "<p style='text-align: left;'> </p>"
    	. "<li>" . $staticText . "</li></ul>"
    	. "<h5><span class='a-list-item'>Specifications</span></h5>"
    	. "<ul><li>Form Factor: " . $this->runningList['form_factor'] . "</li>"
    	. "<li>Processor: Intel Core " . $this->runningList['cpu_core'] . " " . $this->insertDataArray['processer_gen'] . "</li>"
    	. "<li>RAM: Customizable</li>"
    	. "<li>Available RAM Slots " . $this->insertDataArray['Memory_Slots'] . "</li>"
    	. "<li>Max RAM Capacity " . $this->insertDataArray['Max_Memory_Capacity'] . "</li>"
    	. "<li>Hard Drive: Customizable</li>"
    	. "<li>Graphic Processor: " . $this->insertDataArray['graphics_processor'] . "</li>"
    	. "<li>Operating System: Customizable</li>"
    	. "<li>Available Ports: " . $this->insertDataArray['available_port'] . "</li>"
    	. "<li>Available Video Ports: " . $this->insertDataArray['available_vedio_port'] . "</li>"
    	. "<li>Screen Size: " . $this->insertDataArray['screen_size'] . "</li>"
    	. "<li>Screen Resolution: " . $this->insertDataArray['screen_res'] . "</li>"
    	. "<li>Dimensions: " . $this->insertDataArray['width'] . " X " . $this->insertDataArray['length'] . " X " . $this->insertDataArray['height'] . " inches</li>"
    	. "<li>Weight: " . $this->insertDataArray['weight'] . "</li>"
    	. "<li>Color: " . $this->insertDataArray['color'] . "</li>"
    	. "<li>Tested for Key Functions,  R2/Ready for Resale</li></ul>"
    	. "<h5>In The Box</h5>"
    	. "<ul>"
    	. "<li>Your perfect, like-new (but greener) refurbished " . $this->runningList['model'] . "</li>"
    	. "<li>Tested for Key Functions,  R2/Ready for Resale</li>"
    	. "<li>All standard manufacturer's literature, such as documentation and setup manual</li>"
    	. "<li>Windows 10 Certificate of Authenticity (COA) and License Key (if applicable)</li>"
    	. "<li>AC Power Cable</li>"
    	. "<li>Standard manufacturer's accessories, such as keyboard and mouse</li>"
    	. "</ul>"
    	. "<h5>Warranty</h5>"
    	. "<ul>"
    	. "<li>We stand behind every piece of refurbished equipment we deliver and offer some of the most comprehensive warranties in the business-so you can be as confident in our products as we are.</li>"
    	. "<li>30-Day Money-Back Guarantee<br>If for any reason you are not 100% satisfied with this device, send it back to us within 30 days and we'll issue a full refund. Guaranteed, no questions. We get it. It happens.</li>"
    	. "<li>".$this->replacementText."</li>"
    	. "<li>".$this->takingCareText."</li>"
    	. "<li>".$this->extendedText."</li>"
    	. "</ul>"
    	. "<strong>Extended warranty includes:</strong>"
    	. "<ul><li>Advanced replacement</li>"
    	. "<li>Repair - Parts and labor</li>"
    	. "<li>Replacement</li></ul>"
    	. "<h5>What we do</h5>"
    	. "<p>".$this->helpText."</p>"
    	. "<p>ASIN : " . $this->runningList['asin'] . "</p>";
    }

  	private function getAppleDescriptionHTML()
  	{
	    $staticText = str_replace("{{Model}}", $this->insertDataArray['model'], $this->staticText);
	    $staticText = str_replace("{{Mfg}}", $this->runningList['manufacturer'], $staticText);
	    return "<p><span class='a-list-item'>---</span></p><h5><span>Overview </span></h5>"
	            . "<p>" . $this->title . "</p>"
	            . "<ul><li>{{tagline_blurb}}</li>"
	            . "<li>{{combined_blurb}}</li>"
	            . "<p style='text-align: left;'> </p>"
	            . "<li>" . $staticText . "</li></ul>"
	            . "<h5><span class='a-list-item'>Specifications</span></h5>"
	            . "<p>" . $this->title . "</p>"
	            . "<ul><li>Form Factor: " . $this->runningList['form_factor'] . "</li>"
	            . "<li>Color: " . $this->insertDataArray['color'] . "</li>"
	            . "<li>Apple Order Number: " . $this->appleData['Apple_Order_No'] . "</li>"
	            . "<li>Apple Model: " . $this->appleData['Apple_Model_Combined'] . "</li>"
	            . "<li>Processor: Intel Core " . $this->runningList['cpu_core'] . " " . $this->insertDataArray['processer_gen'] . "</li>"
	            . "<li>RAM: Customizable</li>"
	            . "<li>Available Memory Slots: " . $this->appleData['MaximumRAM'] . "</li>"
	            . "<li>Maximum Memory Capacity: " . $this->appleData['RAM_Slots'] . "</li>"
	            . "<li>Hard Drive: Customizable</li>"
	            . "<li>Screen Size:  " . $this->appleData['Built_in_Display'] . "</li>"
	            . "<li>Resolution:  " . $this->appleData['Native_Resolution'] . "</li>"
	            . "<li>Graphic Processor: " . $this->appleData['Video_Card'] . "</li>"
	            . "<li>Operating System: Most Recent Compatible Mac OS X</li>"
	            . "<li>Available Ports: " . $this->insertDataArray['available_port'] . "</li>"
	            . "<li>Available Video Ports: " . $this->insertDataArray['available_vedio_port'] . "</li>"
	            . "<li>Dimensions: " . $this->insertDataArray['width'] . " X " . $this->insertDataArray['length'] . " X " . $this->insertDataArray['height'] . " inches</li>"
	            . "<li>Weight: " . $this->insertDataArray['weight'] . "</li>"
	            . "<li>Tested for Key Functions,  R2/Ready for Resale</li></ul>"
	            . "<h5>In The Box</h5>"
	            . "<ul>"
	            . "<li>Your perfect, like-new (but greener) refurbished " . $this->runningList['model'] . "</li>"
	            . "<li>All standard manufacturer’s literature, such as documentation and setup manual</li>"
	            . "<li>Windows 10 Certificate of Authenticity (COA) and License Key (if applicable)</li>"
	            . "<li>AC Power Cable</li>"
	            . "<li>Standard manufacturer’s accessories, such as keyboard and mouse</li>"
	            . "</ul>"
	            . "<h5>Warranty</h5>"
	            . "<ul>"
	            . "<li>We stand behind every piece of refurbished equipment we deliver and offer some of the most comprehensive warranties in the business—so you can be as confident in our products as we are.</li>"
	            . "<li>30-Day Money-Back Guarantee<br>If for any reason you are not 100% satisfied with this device, send it back to us within 30 days and we’ll issue a full refund. Guaranteed, no questions. We get it. It happens.</li>"
	            . "<li>".$this->replacementText."</li>"
		    	. "<li>".$this->takingCareText."</li>"
		    	. "<li>".$this->extendedText."</li>"
	            . "</ul>"
	            . "<strong>Extended warranty includes:</strong>"
	            . "<ul><li>Advanced replacement</li>"
	            . "<li>Repair - Parts and labor</li>"
	            . "<li>Replacement</li></ul>"
	            . "<h5>What we do</h5>"
	            . "<p>".$this->helpText."</p> 
			<p>ASIN : " . $this->runningList['asin'] . "</p>";
	}
}
