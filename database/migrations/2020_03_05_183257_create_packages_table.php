<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('worker_id', 255)->nullable(); 
            $table->double('value', 8, 2)->nullable(); 
            $table->string('location', 255)->nullable(); 
            $table->string('carrier', 255); 
            $table->string('freight_ground', 255); 
            $table->string('received', 255)->default('N'); 
            $table->string('recipient', 255)->nullable(); 
            $table->string('qty', 255); 
            $table->string('description', 255); 
            $table->string('req_name', 255); 
            $table->string('tracking_number', 255); 
            $table->string('ref_number', 255)->nullable(); 
            $table->timestamp('expected_arrival')->nullable();
            $table->timestamp('order_date')->nullable();
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
        Schema::dropIfExists('packages');
    }
}
