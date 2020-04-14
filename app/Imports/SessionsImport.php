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
            $cpudata = explode("-", ifnull(@$row[25]));
            return [
                'rcheck' => ifnull(@$row[8]),
                'asset' => ifnull(@$row[8]),
                'form_factor' => ifnull(@$row[11]),
                'manuf' => ifnull(@$row[12]),
                'model' => ifnull(@$row[13]),
                'serial' => ifnull(@$row[9]),
                'cpuModel' => ifnull(@$row[25]),
                'cpuSpeed' => ifnull(@$row[26]),
                'ram' => ifnull($row[27]),
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
