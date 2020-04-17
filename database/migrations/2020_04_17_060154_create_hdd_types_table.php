<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHddTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hdd_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hdd_type_name', 255)->nullable();
			$table->string('hdd_type_value', 255)->nullable();
			$table->integer('status')->default(1);
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
        Schema::dropIfExists('hdd_types');
    }
}
