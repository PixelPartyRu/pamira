<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('name');
            $table->string('alias');
            $table->string('article'); //Арктикул
            $table->integer("catalog_id");
            $table->text("haracteristic");
            $table->string("img");
            $table->string("img2");
            $table->string("img3");
            $table->string("img4");

            $table->integer("brand_id");

            $table->string('country');
            $table->string('code_1c');
            
            
            $table->integer("stock"); //акция
            $table->decimal('cost', 12, 2);
            $table->decimal('cost_trade', 12, 2);
            $table->boolean("sales_leader");
            $table->boolean("viewcost");
            $table->boolean("viewcost_nonauth");
            $table->boolean("view_filter");
            $table->boolean("moderated");
            $table->boolean("in_main_page");
            $table->timestamps();
            
            //$table->timestamps("created_at");
            //$table->timestamps("updated_at");
            
           
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
