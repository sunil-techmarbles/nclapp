<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppleDataNew extends Model
{
	public static function getMakorAppleManufacturerModel($model, $processorModel)
	{
		pr($model) ;
		pr($processorModel );die("**");


	 //    $query = "SELECT * FROM apple_data_new WHERE Model LIKE '%" . $model . "%'";
	 //    $mysqli_query = $mysqli->query($query);

	 //    if ($mysqli_query->num_rows > 1) {
	 //        foreach ($mysqli_query->rows as $key => $value) {
	 //            if (strtolower($processor_model) == strtolower($value['Processor_Model'])) {
	 //                $data = $value;
	 //                break;
	 //            } else {
	 //                $data = "DUPLICATES";
	 //            }
	 //        }
	 //        return $data;
	 //    } elseif ($mysqli_query->num_rows == 1) {
	 //        return $mysqli_query->row;
	 //    }
	 //    return false;
		// }

}
