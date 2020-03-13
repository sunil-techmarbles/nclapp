<?php
namespace App\Imports;
use App\ListData;
use App\SessionData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FirstSheetImport implements ToCollection,  WithStartRow
{
	public $data, $assetCol, $status;

    public function collection(Collection $data)
    {
    	$this->data = $data[0];
        $this->assetCol = 0;
        $this->status = 'removed';
        foreach ($this->data as $key => $value)
        {	
            if($value == "Asset ID")
            {
                $this->assetCol = $key;
            }
        	# code...
        }
        $asset = [];
    	foreach ($data as $key => $value)
    	{
    		if($key == 0)
    		{
    			continue;
    		}
		    $asset[] = $value[$this->assetCol];
		    ListData::updateRunStatus($this->assetCol, $this->status);
    	}
        SessionData::updateRecordRunStatus($asset, $this->status);
    }

    /**
     * @return int
    */
    public function startRow(): int
    {
        return 1;
    }  
}