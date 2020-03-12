<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyPricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_pricings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('Asset_ID')->nullable();
            $table->string('SerialNumber', 50)->nullable();
            $table->string('Class', 50)->nullable();
            $table->string('Brand', 50)->nullable();
            $table->string('Model', 50)->nullable();
            $table->string('Model_Number', 50)->nullable();
            $table->string('Form_Factor', 50)->nullable();
            $table->string('Processor', 50)->nullable();
            $table->string('RAM', 50)->nullable();
            $table->string('Memory_Type', 50)->nullable();
            $table->string('Memory_Speed', 50)->nullable();
            $table->string('Hard_Drive', 50)->nullable();
            $table->string('HD_Interface', 50)->nullable();
            $table->string('HD_Type', 50)->nullable();
            $table->string('Condition', 50)->nullable();
            $table->decimal('Price', 15,  2)->nullable()->default(0.00);
            $table->decimal('Final_Price', 15,  2)->nullable()->default(0.00);
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
        Schema::dropIfExists('shopify_pricings');
    }
}
