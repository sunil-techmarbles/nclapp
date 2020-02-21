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
            $table->double('price', 11, 2);
            $table->longText('notes');
            $table->string('manufacturer', 200);
            $table->string('model', 200);
            $table->string('model_alias', 200);
            $table->integer('notifications');
            $table->string('form_factor', 200);
            $table->string('cpu_core', 200);
            $table->string('cpu_model', 200);
            $table->string('cpu_speed', 200);
            $table->string('ram', 200);
            $table->string('hdd', 200);
            $table->string('os', 200);
            $table->string('webcam', 200);
            $table->string('link', 200);
            $table->string('shopify_product_id', 200);
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
