<?php
namespace App\Imports;
  
use App\Supplies;
use App\SupplieEmail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
  
class SuppliesImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $currentDate = Carbon::now();
        $supplieID = ifnull(intval($row[0]));
        $data = [
            "item_name"  => ifnull($row[1]),
            "item_url"   => ifnull($row[2]),
            "qty"        => ifnull(intval($row[3])),
            "part_num"   => ifnull($row[4]),
            "description"=> ifnull($row[5]),
            "dept"       => ifnull($row[6]),
            "price"      => ifnull(floatval($row[7])),
            "vendor"     => ifnull($row[8]),
            "low_stock"  => ifnull(intval($row[9])),
            "reorder_qty"=> ifnull(intval($row[10])),
            "dlv_time"   => ifnull($row[11]),
            "bulk_options"=> ifnull($row[12]),
            "email_subj" => ifnull($row[14]),
            "email_tpl"  => ifnull($row[15]),
        ];
        
        $emailNew = false;
        if(!empty($supplieID) && Supplies::getSupplieById($supplieID))
        {
            $data['id'] = $supplieID; 
            Supplies::updateSupplieById((object) $data);
            $emailNew = true;
        }
        else
        {
            $supplieID = Supplies::addSupplies((object)$data);
        }

        if(ifnull($row[13]) != '')
        {
            $supplieEmails = array_filter(explode(',',$row[13]));
            // echo $supplieID." supplie id >".$emailNew." \n";            
            if($emailNew)
            {
                // echo 'i am here'."\n";
                SupplieEmail::deleteBulkSupplieEmail(intval($supplieID));
            }
            foreach ($supplieEmails as $key => $email)
            {
                SupplieEmail::addSupplieEmail($email, $supplieID);
            }
        }
        // Supplies::deleteBulkSupplieBelowDate($currentDate);        
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
