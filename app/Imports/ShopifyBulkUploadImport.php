
<?php
namespace App\Imports;

use App\ListData;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ShopifyBulkUploadImport implements ToCollection,  WithStartRow
{
    public $data, $assetCol, $status, $cpuName;

    public function  __construct($cpuName)
	{
	    $this->cpuName = $cpuName;
	}

    public function collection(Collection $data)
    {
    	$this->data = $data[0];
        $this->assetCol = 0;
        $this->formFactorCol = 0;
        foreach ($this->data as $key => $value)
        {	
            if($value == "Asset ID")
            {
                $this->assetCol = $key;
            }
            if($value == "Form_Factor")
            {
				$this->formFactorCol = $key;
			}
        }
		if($this->assetCol >= 0 && $this->formFactorCol >= 0)
		{
			foreach ($data as $key => $value)
	    	{
	    		if($key == 0)
	    		{
	    			continue;
	    		}
			    $asset = $value[$this->assetCol];
			    $formFactor = $value[$this->formFactorCol];
			    if ($asset > 0)
			    {
			    	$data = [
						"mid" => 0,
						"technology" => $formFactor,
						"cpu" => $this->cpuName,
						"grade" => 'A',
						"asset" => $asset,
						"added_by" => Sentinel::getUser()->first_name,
						"added_on" => Carbon::now()
					];
					$result = ListData::checkRecordExist($asset);
					if(!$result)
					{
						ListData::addListDataRecord((object) $data);
					}
			    }
	    	}
		}
    }

    /**
     * @return int
    */
    public function startRow(): int
    {
        return 1;
    } 
}
