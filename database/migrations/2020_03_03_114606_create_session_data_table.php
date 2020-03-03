<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sid');  
            $table->integer('aid'); 
            $table->string('asset', 255);   
            $table->string('added_by', 255);
            $table->timestamp('added_on')->nullable();
            $table->string('status', 255)->default('active');   
            $table->string('run_status', 255)->default('active');   
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
        Schema::dropIfExists('session_data');
    }
}
