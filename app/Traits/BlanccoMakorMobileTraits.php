<?php
namespace App\Traits;
use SimpleXMLElement;

trait BlanccoMakorMobileTraits
{
    public static function createMakorMobileXmlData($apiData)
    {
        // creating object of AuditXMLElement
        $audit = new SimpleXMLElement('<audit></audit>');

        // component System
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'System');

        // Set AssetTag
        $component = $components->addChild('component', $apiData['assetId'] );
        $component->addAttribute('name', 'Asset#');
        $component->addAttribute('type', 'string');

        // Set Class
        $component = $components->addChild('component', $apiData['serial']);
        $component->addAttribute('name', 'Serial');
        $component->addAttribute('type', 'string');

        // Set Serial
        $component = $components->addChild('component', $apiData['manufacturer']);
        $component->addAttribute('name', 'Manufacturer');
        $component->addAttribute('type', 'string');

        // Set manufacturer
        $component = $components->addChild('component', $apiData['model']);
        $component->addAttribute('name', 'Model');
        $component->addAttribute('type', 'string');
 
        // Set Model
        $component = $components->addChild('component', $apiData['modelNumber']);
        $component->addAttribute('name', 'Model#');
        $component->addAttribute('type', 'string');

        // Set Customer Asset
        $component = $components->addChild('component', $apiData['customerAssetTag']);
        $component->addAttribute('name', 'CustomerAsset#');
        $component->addAttribute('type', 'string');

        // Set Weight
        $component = $components->addChild('component', $apiData['weight']);
        $component->addAttribute('name', 'ItemNetWeight');
        $component->addAttribute('type', 'string');

        // Set Grade
        $component = $components->addChild('component', $apiData['pallet']);
        $component->addAttribute('name', 'Pallet');
        $component->addAttribute('type', 'string');

        // Set Grade
        $component = $components->addChild('component', $apiData['grade']);
        $component->addAttribute('name', 'Grade');
        $component->addAttribute('type', 'string');

        // set NextProcess
        $component = $components->addChild('component', $apiData['nextProcess']);
        $component->addAttribute('name', 'NextProcess');
        $component->addAttribute('type', 'string');

        // set ComplianceLabel
        $component = $components->addChild('component', $apiData['complianceLabel']);
        $component->addAttribute('name', 'ComplianceLabel');
        $component->addAttribute('type', 'string');

        // Set Condition 
        $component = $components->addChild('component', $apiData['condition']);
        $component->addAttribute('name', 'Condition');
        $component->addAttribute('type', 'string');
        
        // Set Color
        $component = $components->addChild('component', $apiData['color']);
        $component->addAttribute('name', 'Color');
        $component->addAttribute('type', 'string');

        // component Type
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'Mobile Device');

        // component Type
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Mobile Device');
 
        // Set Type
        $component1 = $components->addChild('component', $apiData['type']);
        $component1->addAttribute('name', 'Type');
        $component1->addAttribute('type', 'string');
        
        // component Hard Drive
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'Hard_Drives');
        
        // child component Screen/Resolution
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Hard_Drive');

        // Set HD Manufacturer 
        $component1 = $component->addChild('component', $apiData['hdManufacturer']);
        $component1->addAttribute('name', 'Manufacturer');
        $component1->addAttribute('type', 'string');

        // Set HD Model  
        $component2 = $component->addChild('component', $apiData['hdModel']);
        $component2->addAttribute('name', 'Model');
        $component2->addAttribute('type', 'string');

        // Set HD Part Number    
        $component3 = $component->addChild('component', $apiData['hdPartNumber']);
        $component3->addAttribute('name', 'PartNumber');
        $component3->addAttribute('type', 'string');

        // Set HD HD Serial#    
        $component4 = $component->addChild('component', $apiData['hdSerial']);
        $component4->addAttribute('name', 'Serial#');
        $component4->addAttribute('type', 'string');

        // Set HD HD Serial#    
        $component5 = $component->addChild('component', $apiData['hdCapacity']);
        $component5->addAttribute('name', 'Capacity');
        $component5->addAttribute('type', 'string');

        // Set HD HD Interface#    
        $component6 = $component->addChild('component', $apiData['hdInterface']);
        $component6->addAttribute('name', 'Interface');
        $component6->addAttribute('type', 'string');

        // Set HD HD Interface#    
        $component7 = $component->addChild('component', $apiData['hdPowerOnHours']);
        $component7->addAttribute('name', 'PowerOnHours');
        $component7->addAttribute('type', 'string');

        // Set HD Services Performed#    
        $component8 = $component->addChild('component', $apiData['hdServicesPerformed']);
        $component8->addAttribute('name', 'HDServicesPerformed');
        $component8->addAttribute('type', 'string');

        // Set HD Services Performed#    
        $component9 = $component->addChild('component', $apiData['hdRemoved']);
        $component9->addAttribute('name', 'Removed');
        $component9->addAttribute('type', 'string');

        // Set HD Type#    
        $component10 = $component->addChild('component', $apiData['hdType']);
        $component10->addAttribute('name', 'Type');
        $component10->addAttribute('type', 'string');

        // Set Service Queue Status#    
        $component11 = $component->addChild('component', $apiData['serviceQueueStatus']);
        $component11->addAttribute('name', 'ServiceQueueStatus');
        $component11->addAttribute('type', 'string');
        
        // component KeySpecs
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'Key Specs');
        
        // child component Screen/Resolution
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Key Specs');

        // Set Service OS Type#    
        $component1 = $component->addChild('component', $apiData['osType']);
        $component1->addAttribute('name', 'OSType');
        $component1->addAttribute('type', 'string');

        //Set Service OS Type#    
        $component2 = $component->addChild('component', $apiData['osVersion']);
        $component2->addAttribute('name', 'OSVersion');
        $component2->addAttribute('type', 'string');

        //Set Charging Port 
        $component3 = $component->addChild('component', $apiData['chargingPort']);
        $component3->addAttribute('name', 'ChargingPort');
        $component3->addAttribute('type', 'string');

        //Set Battery 
        $component4 = $component->addChild('component', $apiData['battery']);
        $component4->addAttribute('name', 'Battery');
        $component4->addAttribute('type', 'string'); 
 
        // component Conectivity
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'Connectivity');
        
        // child component Screen/Resolution
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Connectivity');

        //Set Service Carrier#
        $component1 = $component->addChild('component', preg_replace('/[^a-zA-Z0-9_ -]/s','', $apiData['carrier']));
        $component1->addAttribute('name', 'Carrier');
        $component1->addAttribute('type', 'string');

        //Set Service Sim Status#
        $component2 = $component->addChild('component', $apiData['simStatus']);
        $component2->addAttribute('name', 'SimStatus');
        $component2->addAttribute('type', 'string');

        //Set Service MDM Status#    
        $component3 = $component->addChild('component', $apiData['MDMStatus']);
        $component3->addAttribute('name', 'MDMStatus');
        $component3->addAttribute('type', 'string');

        //Set Service FMIP Status#    
        $component4 = $component->addChild('component', $apiData['FMIPStatus']);
        $component4->addAttribute('name', 'FMIPStatus');
        $component4->addAttribute('type', 'string');

        //Set Service FMIP Status#    
        $component5 = $component->addChild('component', $apiData['blacklistStatus']);
        $component5->addAttribute('name', 'BlacklistStatus');
        $component5->addAttribute('type', 'string');

        //Set Service Graylist Status#
        $component6 = $component->addChild('component', $apiData['graylistStatus']);
        $component6->addAttribute('name', 'GraylistStatus');
        $component6->addAttribute('type', 'string');

        //Set Service Release Year#
        $component7 = $component->addChild('component', $apiData['releaseYear']);
        $component7->addAttribute('name', 'ReleaseYear');
        $component7->addAttribute('type', 'string');

        // component Screen/Resolution
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'Screen/Resolution');
        
        // child component Screen/Resolution
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Screen/Resolution');

        //Set Service Display Size#    
        $component1 = $component->addChild('component', $apiData['displaySize']);
        $component1->addAttribute('name', 'Size');
        $component1->addAttribute('type', 'string');
 
        // Set Service Display Size#
        $component2 = $component->addChild('component', $apiData['displayResolution']);
        $component2->addAttribute('name', 'Resolution');
        $component2->addAttribute('type', 'string');

        //Set Service Display Touchscreen#
        $component3 = $component->addChild('component', $apiData['displayTouchScreen']);
        $component3->addAttribute('name', 'Touchscreen');
        $component3->addAttribute('type', 'string');
        
        // component  Miscellaneous
        $components = $audit->addChild('components');
        $components->addAttribute('name', 'Miscellaneous');

        // child component  Miscellaneous
        $component = $components->addChild('components');
        $component->addAttribute('name', 'Miscellaneous');

        // Set Service Release Year#
        $notes = ( is_array( $apiData['notes'] ) ) ? $apiData['notes'][0] : $apiData['notes'];
        $component1 = $component->addChild('component', $notes);
        $component1->addAttribute('name', 'Notes');
        $component1->addAttribute('type', 'string');

        // Set Service Release Year#
		$other = ( is_array( $apiData['other'] ) ) ? $apiData['other'][0] : $apiData['other'];
        $component2 = $component->addChild('component', $other);
        $component2->addAttribute('name', 'Other');
        $component2->addAttribute('type', 'string');

        // Set Service Input/Output
        $inputOutput = ( is_array( $apiData['inputOutput'] ) ) ? $apiData['inputOutput'][0] : $apiData['inputOutput'];
        $component3 = $component->addChild('component',  $inputOutput );
        $component3->addAttribute('name', 'Input/ Output');
        $component3->addAttribute('type', 'string');

        // Set Service Cosemtic    
        $cosmetic = ( is_array( $apiData['cosmetic'] ) ) ? $apiData['cosmetic'][0] : $apiData['cosmetic'];
        $component4 = $component->addChild('component', $cosmetic );
        $component4->addAttribute('name', 'Cosemtic');
        $component4->addAttribute('type', 'string');

        // Set Service Missing  
        $missing = ( is_array( $apiData['missing'] ) ) ? $apiData['missing'][0] : $apiData['missing'];
        $component5 = $component->addChild('component', $missing );
        $component5->addAttribute('name', 'Missing');
        $component5->addAttribute('type', 'string');

        // Set Service Functional  
        $functional = ( is_array( $apiData['functional'] ) ) ? $apiData['functional'][0] : $apiData['functional'];
        $component6 = $component->addChild('component', $functional );
        $component6->addAttribute('name', 'Functional');
        $component6->addAttribute('type', 'string');

        // Set Service Screen
        $screen = ( is_array( $apiData['screen'] ) ) ? $apiData['screen'][0] : $apiData['screen'];
        $component7 = $component->addChild('component', $screen );
        $component7->addAttribute('name', 'Screen');
        $component7->addAttribute('type', 'string');

        //Set Service Case     
        $case = ( is_array( $apiData['case'] ) ) ? $apiData['case'][0] : $apiData['case'];   
        $component8 = $component->addChild('component', $case );   
        $component8->addAttribute('name', 'Case');  
        $component8->addAttribute('type', 'string');    

        return $audit->asXML();
    }
}