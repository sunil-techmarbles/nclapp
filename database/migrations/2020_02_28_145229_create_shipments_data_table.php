<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sid');  
            $table->integer('aid'); 
            $table->integer('win8_activated');   
            $table->string('asset', 255);   
            $table->string('sn', 255);
            $table->string('old_coa', 255);
            $table->string('new_coa', 255);
            $table->string('added_by', 255);
            $table->timestamp('added_on')->nullable();
            $table->string('status', 255)->default('active');   
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
        Schema::dropIfExists('shipments_data');
    }
}
