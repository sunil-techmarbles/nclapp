<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMacDataRawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mac_data_raws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Manufacturer', 255)->nullable();
            $table->string('Apple_Model_Combined', 255)->nullable();
            $table->string('Apple_Order_No', 255)->nullable();
            $table->string('EMC', 255)->nullable();
            $table->integer('Processor_Quantity')->nullable();
            $table->string('Processor_Manufacturer', 255)->nullable();
            $table->string('Processor_Type', 255)->nullable();
            $table->string('Processor_Model', 255)->nullable();
            $table->string('Processor_Core', 255)->nullable();
            $table->string('Processor_Speed', 255)->nullable();
            $table->string('Processor_Generation', 255)->nullable();
            $table->string('RAM_Type', 255)->nullable();
            $table->integer('RAM_Speed')->nullable(); 
            $table->string('MaximumRAM', 255)->nullable();
            $table->string('Motherboard_RAM', 255)->nullable();
            $table->string('RAM_Slots', 255)->nullable();
            $table->string('Video_Card', 255)->nullable();
            $table->string('Built_in_Display', 255)->nullable();
            $table->string('Native_Resolution', 255)->nullable();
            $table->string('Storage_Dimensions', 255)->nullable();
            $table->string('Storage_Interface', 255)->nullable();
            $table->string('Standard_Optical', 255)->nullable();
            $table->string('Dimensions', 255)->nullable();
            $table->string('Avg_Weight', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mac_data_raws');
    }
}
