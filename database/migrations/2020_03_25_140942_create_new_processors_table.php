<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewProcessorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_processors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Manufacturer',255)->nullable();
            $table->string('Name',255)->nullable();
            $table->string('Type',255)->nullable();
            $table->string('Model',255)->nullable();
            $table->integer('Generation')->nullable();
            $table->string('Codename',255)->nullable();
            $table->integer('Cores')->nullable();
            $table->integer('Threads')->nullable();
            $table->string('Socket',255)->nullable();
            $table->string('Process',255)->nullable();
            $table->string('Clock',255)->nullable();
            $table->string('Multi',255)->nullable();
            $table->string('Cache_L1_L2_L3',255)->nullable();
            $table->string('TDP',255)->nullable();
            $table->string('Released',255)->nullable();
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
        Schema::dropIfExists('new_processors');
    }
}
