<?php
namespace App\Exports;

use App\Supplies;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SuppliesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $supplieWithEmails = [];

        $abc = Supplies::getExportResult();

        foreach ($abc as $key => $value) {
            $keys = array_keys($value->toArray());
            foreach ($keys as $k => $v) {
                if($v == 'get_supplie_emails')
                {
                    break;
                }
                $supplieWithEmails[$key][$v] = $value[$v];
            }
            if($value['getSupplieEmails'])
            {
                $emails = implodeSupplieEmails($value['getSupplieEmails']);
            }
            else{
                $emails = '';
            }
            $supplieWithEmails[$key]['emails'] = $emails;
        }        
        return collect($supplieWithEmails);
    }

    public function headings(): array
    {
        return [
            "Item ID",
            "Name",
            "URL",
            "Quantity",
            "Part Num",
            "Description",
            "Department",
            "Price",
            "Vendor",
            "Low Stock",
            "Reorder Qty",
            "Delivery Time",
            "Bulk Opts",
            "Subject",
            "Template",
            "Emails",
        ];
    }

}