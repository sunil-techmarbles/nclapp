<?php
namespace App\Exports;

use App\SessionData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AsinInventryExport implements FromCollection, WithHeadings, ShouldAutoSize
{ 
    use Exportable;
    public $refurbAssetDataPath , $basePath;

    public function __construct()
    {
    	$this->basePath = base_path().'/public';
    	$this->refurbAssetDataPath = $this->basePath.'/refurb-asset-data';
    }  

    public function collection()
    {
        $allInventrydata = [];
        $getReportData = SessionData::getAsinInventrySectionData();
        $assts = SessionData::getSessionData();
        $assets = [];
        foreach($assts as $a)
        {
            if(!empty($a['asset']))
            {
                if(!isset($assets['asin'.$a['aid']])) $assets['asin'.$a['aid']] = ["active"=>[],"removed"=>[]];
                $assets['asin'.$a['aid']][$a['status']][] = $a['asset'];
            }
        }
        foreach ($getReportData as $key => $data)
        {
            $cpu = $data['cpu_core'].$data['cpu_model']." CPU @".$data['cpu_speed'];
            $asstId = "";
            foreach( $assets['asin'.$data['aid']]['active'] as $itm )
            {
                $asstId = $itm ;
                $allInventrydata[] = array( $asstId, $data['model'], $data['form_factor'], $cpu , $data['asin'], $data['added_on']);                      
            }
            $cpu = '';
        }
        return collect([(object) $allInventrydata] );
    }

    public function headings(): array
    {
        return ["Asset Id","Model","Form Factor","CPU","ASIN","Added date" ];
    }
}