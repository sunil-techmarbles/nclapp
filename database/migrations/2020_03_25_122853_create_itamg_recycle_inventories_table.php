<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItamgRecycleInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itamg_recycle_inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Brand', 255)->nullable();
            $table->string('Model', 255)->nullable();
            $table->integer('PartNo');
            $table->string('Category', 255)->nullable();
            $table->string('Notes', 255)->nullable();
            $table->string('Value', 255)->nullable();
            $table->string('Status', 255)->nullable();
            $table->string('require_pn', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itamg_recycle_inventories');
    }
}
