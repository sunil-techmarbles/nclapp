<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_searches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_or_part', 255)->nullable();
            $table->string('partNo', 255)->nullable();
            $table->string('Brand', 255)->nullable();
            $table->string('Category', 255)->nullable();
            $table->string('require_pn', 255)->default('Y');
            $table->timestamp('on_datetime')->nullable();
            $table->string('is_active', 255)->default('Active');
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
        Schema::dropIfExists('failed_searches');
    }
}
