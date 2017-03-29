<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCataologsAddAssortment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ignore_cats = [ 'мелкая бытовая техника' ];

        foreach(App\Catalog::all() as $catalog) {
            if(in_array(mb_strtolower($catalog->name, 'utf8'), $ignore_cats)) continue;

            $filters = explode('|', $catalog->filter);
            if(!in_array('assortment', $filters)) {
                $filters[] = 'assortment';
                $catalog->filter = implode('|', $filters);
                $catalog->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach(App\Catalog::all() as $catalog) {
            $filters = explode('|', $catalog->filter);
            if(false !== ($pos = array_search('assortment', $filters))) {
                unset($filters[$pos]);
                $catalog->filter = implode('|', $filters);
                $catalog->save();
            }
        }
    }
}
