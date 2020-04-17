<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asin', 20);
            $table->double('price', 11, 2)->nullable();
            $table->longText('notes')->nullable();
            $table->string('manufacturer', 200);
            $table->string('model', 200);
            $table->string('model_alias', 200)->nullable();
            $table->integer('notifications')->default(1);
            $table->string('form_factor', 200);
            $table->string('cpu_core', 200);
            $table->string('cpu_model', 200);
            $table->string('cpu_speed', 200);
            $table->string('ram', 200);
            $table->string('ramtype', 200);
            $table->string('hddtype', 200);
            $table->string('hdd', 200);
            $table->string('os', 200);
            $table->string('webcam', 200);
            $table->string('link', 200)->nullable();
            $table->string('shopify_product_id', 200)->nullable();
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
        Schema::dropIfExists('asins');
    }
}
