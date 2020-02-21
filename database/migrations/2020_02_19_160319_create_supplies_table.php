<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item_name', 200);
            $table->string('item_url', 500)->nullable();
            $table->integer('qty');
            $table->string('part_num', 100);
            $table->longText('description')->nullable();
            $table->string('dept', 100);
            $table->double('price', 11, 2);
            $table->string('vendor', 200);
            $table->integer('low_stock');
            $table->integer('reorder_qty');
            $table->string('dlv_time', 500);
            $table->longText('bulk_options')->nullable();
            $table->string('email_subj', 500);
            $table->longText('email_tpl');
            $table->timestamp('email_sent');
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
        Schema::dropIfExists('supplies');
    }
}
