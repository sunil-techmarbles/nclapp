<?php

use Illuminate\Database\Seeder;
use App\FormsConfig;
class FormsConfigDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {	
    	$formConfig = config()->get('constants.formConfig');
    	foreach ($formConfig as $key => $value)
    	{
           	FormsConfig::addRecord($value);
    	}
    }
}
