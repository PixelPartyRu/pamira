<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string("name");
            $table->string("img");
            $table->text("text");
            $table->text("alias");
            $table->text("rubric");

            $table->enum('type', array('article', 'page'));

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
        Schema::table('content', function (Blueprint $table) {
            //
        });
    }
}
