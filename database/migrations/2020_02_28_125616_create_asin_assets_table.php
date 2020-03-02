<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsinAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asin_assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sid');
            $table->integer('aid');
            $table->string('asset', 50);
            $table->string('added_by', 50);
            $table->string('status', 20)->default('active');    
            $table->string('run_status', 20)->default('active');
            $table->timestamp('added_on')->nullable();    
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
        Schema::dropIfExists('asin_assets');
    }
}
