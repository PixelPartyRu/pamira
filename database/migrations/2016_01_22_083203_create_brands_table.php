<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias'); //имя для формирования пути
            $table->string('name'); 
            $table->string('title'); 
            $table->string('img'); 
            $table->boolean('main_page'); //отображения не главной странице
            $table->integer('order'); //Порядок
            $table->text('keywords'); //Порядок
            $table->text('preview'); //анонс
            $table->text('description1'); //Описание 1
            $table->text('description2'); //Описание 2
            $table->text('forcart');
            $table->boolean('visible_forcart');
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
