<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductHaracteristicRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_haracteristic_relation', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('product_id');
            $table->integer('haracteristic_id');
            $table->enum('type', array('article', 'page'))->default('article');
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
        //
    }
}
