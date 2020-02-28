<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsinIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asin_issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asset', 255);  
            $table->string('sn', 255); 
            $table->string('issue', 255);    
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
        Schema::dropIfExists('asin_issues');
    }
}
