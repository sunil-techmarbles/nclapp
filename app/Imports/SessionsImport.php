<?php
namespace App\Imports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
  
class SessionsImport implements ToCollection, WithStartRow
{
    public $data;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $data)
    {   
        $this->data = $data->transform(function ($row) {
            $cpudata = explode("-", ifnull(@$row[18]));
            return [
                'rcheck' => ifnull(@$row[1]),
                'asset' => ifnull(@$row[1]),
                'manuf' => ifnull(@$row[5]),
                'model' => ifnull(@$row[6]),
                'serial' => ifnull(@$row[2]),
                'cpuModel' => ifnull(@$row[18]),
                'cpuSpeed' => ifnull(@$row[19]),
                'ram' => ifnull($row[20]),
                'cpudata' => ifnull($cpudata),
                'cpuCore' => ifnull(strtolower(@$cpudata[0])),
                'cpuMdl' => ifnull(strtolower(@$cpudata[1])),
            ];
        });
    }

    /**
     * @return int
    */
    public function startRow(): int
    {
        return 2;
    }    
}
