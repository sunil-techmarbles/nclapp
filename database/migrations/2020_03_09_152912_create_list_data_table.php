<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sid')->default(0);
            $table->integer('mid')->nullable();
            $table->string('model',255)->nullable();
            $table->string('technology',255)->nullable();
            $table->string('asin',255)->nullable();
            $table->string('asset',255)->nullable();
            $table->string('grade',255)->nullable();
            $table->string('cpu',255)->nullable();
            $table->string('cpu_core',255)->nullable();
            $table->string('cpu_model',255)->nullable();
            $table->string('cpu_gen',255)->nullable();
            $table->string('cpu_speed',255)->nullable();
            $table->string('shopify_product_id',255)->nullable();
            $table->string('added_by', 255)->nullable();
            $table->string('status', 255)->default('active');
            $table->string('run_status', 255)->default('active');
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
        Schema::dropIfExists('list_data');
    }
}
