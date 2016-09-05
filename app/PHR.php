<?php

//Связь товара и характеристики

namespace App;

use Illuminate\Database\Eloquent\Model;

class PHR extends Model {

    protected $table = 'product_haracteristic_relation';
    protected $fillable = ['product_id', 'haracteristic_id'];

    public function ph() {
        return $this->belongsTo("App\PH", "haracteristic_id", "id");
    }

    public function getHName() {
        return $this->ph->name;
    }

    public function product() {
        return $this->belongsTo("App\Product", "product_id", "id");
    }

    //возвращает товары, которые имеют характеристики( id ), переданные в массиве $ph_array

    public static function getProductsByHaracteristicIds($ph_array) {
        // var_dump($ph_array);
        $data = self::whereIn("haracteristic_id", $ph_array)->get();
        $ids = array();
        foreach ($data as $d) {
            $ids[] = $d->product->id;
        }
        // var_dump($ids);
        return Product::whereIn("id", $ids);
    }

    //возвращает товары, которые имеют характеристикой( id ) 
    public static function getProductsByHaracteristicId($id) {
        //dd(self::where("haracteristic_id", $id)->get());
        $id = intval($id);
        $data = self::where("haracteristic_id", $id)->get();
        $ids = array();
        foreach ($data as $d) {
        if(!is_null($d->product)) {
            $ids[] = $d->product->id;
        }
           
            // d($d->product->id);
        }
        //  dd($ids);
        return Product::whereIn("id", $ids);
    }
    
        //возвращает товары, которые имеют характеристикой( id ) 
    public static function getProductsIdsByHaracteristicId($id,$catalog_id) {
        //dd(self::where("haracteristic_id", $id)->get());
        $id = intval($id);
        $data = self::where("haracteristic_id", $id)->get();
        $ids = array();
        foreach ($data as $d) {
            if (!is_null($d->product) && $d->product->catalog_id == $catalog_id) {
                $ids[] = $d->product->id;
            }

            // d($d->product->id);
        }
        //  dd($ids);
        return $ids;
    }

    //Кеширование связи
    public function caching() {
        
        if(!is_null($this->product->catalog)) {
        $cache_ob = $this->product->catalog->getCacheObject();
        $cache_ob->update_cache($this->haracteristic_id,"h");
        }
        
        
    }
    
    public static function boot() {
        parent::boot();

        static::created(function($phr) {
           // $phr->name = $phr->ph->name;

        });

        static::updated(function($phr) {
             //$phr->name = $phr->ph->name;
        });

        static::deleting(function($phr) {
           // d("deleting");
           // d($phr);

            
             
                $phr->caching();
            
        });
    }

}
