<?php
namespace App\Imports;

use App\ItamgRecycleInventory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class RecycleTwoFileImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            "Brand"  => ifnull(@$row[0]),
            "Model"   => ifnull(@$row[1]),
            "PartNo"   => ifnull(@$row[2]),
            "Category" => ifnull(@$row[3]),
            "Notes"    => ifnull(@$row[4]),
            "Value"    => ifnull(@$row[5]),
            "Status"   => ifnull(@$row[6]),
            "require_pn" => ifnull(@$row[7]),
        ];

        $result = ItamgRecycleInventory::getRecord($row[1]);
        $chk = false;
        if($result)
        {
            $chk = true;
        }
        if(!$chk && $row[0])
        {
            ItamgRecycleInventory::addRecord($data);
        }
        else
        {
            ItamgRecycleInventory::updateRecord($data, ['id' => $result->id]);
        }
        return;
    }

    /**
     * @return int
    */
    public function startRow(): int
    {
        return 2;
    }    
}
