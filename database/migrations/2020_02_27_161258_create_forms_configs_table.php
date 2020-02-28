<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tab', 100);
            $table->integer('tab_order'); 
            $table->string('grp', 100);
            $table->string('question', 200);  
            $table->string('qtype', 100);  
            $table->integer('allow_new')->default(0); 
            $table->string('config', 500);
            $table->string('required')->default(0);  
            $table->string('sort', 100);   
            $table->string('default_val', 100);
            $table->enum('is_active',['Yes','No']);    
            $table->string('options', 5000);  
            $table->string('grades', 255)->nullable();   
            $table->string('xml_grp', 255)->nullable();
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
        Schema::dropIfExists('forms_configs');
    }
}
