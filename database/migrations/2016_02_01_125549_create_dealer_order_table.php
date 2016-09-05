<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealerOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string("sns")->nullable(); //Фамилия имя отчество
            $table->string("name")->nullable(); //хз что за имя, но пусть будтет
            $table->boolean("status")->default(0);
            $table->string("ip")->nullable();
            $table->string("communication")->nullable();
            //1 - корзина 
            //2 - подтвердить заказ
            //3 - Ввести имя и фамилию
            //4 - страница завершения оформления
            //5 конец
            $table->boolean('order_step')->dafault(1);
            
            $table->timestamp("status_change_date")->nullable();
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
