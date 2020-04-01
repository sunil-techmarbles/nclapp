<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWipeReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wipe_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('wipe_data_pdf_count')->nullable();
            $table->integer('bios_data_file_count')->nullable();
            $table->integer('blancco_pdf_data_count')->nullable();
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
        Schema::dropIfExists('wipe_reports');
    }
}
