<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpuCoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpu_cores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cpu_core_name', 255)->nullable();
			$table->string('cpu_core_value', 255)->nullable();
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
        Schema::dropIfExists('cpu_cores');
    }
}
