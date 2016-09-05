<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharesParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('shares_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('salon');
            $table->string('adress');
            $table->string('password');
            $table->string('email');
            $table->string('type');
            $table->integer('region_id');
            $table->rememberToken();
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
