<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppleDataNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apple_data_news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Case_Type',255)->nullable();
            $table->string('Model',255)->nullable();
            $table->string('Model#',255)->nullable();
            $table->string('EMC',255)->nullable();
            $table->string('Family',255)->nullable();
            $table->string('Processor_Qty',255)->nullable();
            $table->string('Processor_Manufacturer',255)->nullable();
            $table->string('Processor_Type',255)->nullable();
            $table->string('Processor_Model',255)->nullable();
            $table->string('Processor_Core',255)->nullable();
            $table->string('Processor_Speed',255)->nullable();
            $table->integer('Processor_Generation')->nullable();
            $table->string('Processor_Socket',255)->nullable();
            $table->string('Processor_Codename',255)->nullable();
            $table->string('Standard_Optical',255)->nullable();
            $table->string('Video_Card',255)->nullable();
            $table->string('RAM_Slots',255)->nullable();
            $table->string('Built_in_Display',255)->nullable();
            $table->string('Native_Resolution',255)->nullable();
            $table->string('Storage_Dimensions',255)->nullable();
            $table->string('Column_21',255)->nullable();
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
        Schema::dropIfExists('apple_data_news');
    }
}
