<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecycleRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycle_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('items');
            $table->timestamp('started')->nullable();
            $table->timestamp('closed')->nullable();
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
        Schema::dropIfExists('recycle_records');
    }
}
