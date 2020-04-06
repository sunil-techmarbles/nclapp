<?php
namespace App\Exports;

use App\CoaReport;
use App\Asin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class CoaReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $allcoadata = CoaReport::getCoaReportFields();
        foreach ($allcoadata as $key => $coadata)
        {
        	if($coadata->old_coa == 'WIN8 Activated')
            {
				$allcoadata[$key]->old_coa = '';
				$allcoadata[$key]->win8 = 'Activated';
			}
            else
            {
				$allcoadata[$key]->win8 = '';
			}
			$allcoadata[$key]->manufacture = '';
			if(is_readable( $this->refurbAssetDataPath .'/'.$coadata['asset'].'.json'))
            {
				$adata = json_decode(file_get_contents($this->refurbAssetDataPath .'/'.$coadata['asset'].'.json'));
				$allcoadata[$key]->cpu = empty($adata->CPU)?'':$adata->CPU;
				$allcoadata[$key]->model = empty($adata->Model)?'':$adata->Model;
				if(!empty($adata->asin_id))
                {
					$asinData = Asin::getAsinById($adata->asin_id);
					$allcoadata[$key]->manufacture = $asinData['manufacturer'];
				}
			}
            else
            {
				$allcoadata[$key]->cpu = '';
				$allcoadata[$key]->model = '';
			}
        }
        return $allcoadata;
    }

    public function headings(): array
    {
        return ["Asset", "S/N", "Old COA", "New COA", "WIN8", "Manufacturer", "CPU", "Model", "Added" ];
    }
}