<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoaReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coa_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asset', 50);  
            $table->string('sn', 50); 
            $table->string('old_coa', 255);   
            $table->string('new_coa', 255);   
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
        Schema::dropIfExists('coa_reports');
    }
}
