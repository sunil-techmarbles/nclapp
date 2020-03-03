<?php
namespace App\Exports;

use App\AsinIssue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class IssuesReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{ 
    use Exportable; 
 
    public function collection() 
    {  
        return AsinIssue::getIssuesReportFields();
    } 


    public function headings(): array
    { 
        return [
            "Asset",
            "Reported Issues",
            "Added",
        ];
    }

}