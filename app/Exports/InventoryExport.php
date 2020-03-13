<?php
namespace App\Exports;

use App\ListData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $data = ListData::getInventoryExportResult();
        return collect($data->toArray());
    }

    public function headings(): array
    {
        return [
            "Asset",
            "Model",
            "Form Factor",
            "CPU",
            "Added"
        ];
    }

}