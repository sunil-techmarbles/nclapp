<?php
namespace App\Exports;

use App\SessionData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RunningListExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $data = SessionData::getRunningListExport();
        return collect($data->toArray());
    }

    public function headings(): array
    {
        return [
            "Asset",
            "Model",
            "Form Factor",
            "Price",
            "ASIN",
            "Added",
            "CPU"
        ];
    }

}