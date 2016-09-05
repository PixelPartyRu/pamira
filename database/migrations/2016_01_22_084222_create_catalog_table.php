<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('parent_id');
        $table->integer('level');
        $table->string('alias');
        $table->string('name');
        $table->integer('order');
        $table->string('title');
        $table->text('description');
        $table->text('keywords');
        $table->string('h1');
        $table->text('text');
        $table->text("filter");
        $table->unique('alias');
        $table->string('img');
        
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
