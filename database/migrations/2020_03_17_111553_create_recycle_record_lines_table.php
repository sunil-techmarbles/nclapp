<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecycleRecordLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycle_record_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('record_id')->nullable();
            $table->integer('pallet')->nullable();
            $table->string('category', 255)->nullable();
            $table->string('lgross', 255)->nullable();
            $table->string('ltare', 255)->nullable();
            $table->decimal('price', 15, 4)->nullable();
            $table->decimal('total_price', 15, 4)->nullable();
            $table->string('pgi', 255)->nullable();
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
        Schema::dropIfExists('recycle_record_lines');
    }
}
