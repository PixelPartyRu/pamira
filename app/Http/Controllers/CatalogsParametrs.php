<?php
namespace App\Http\Controllers;
use App\Catalog;
use Illuminate\Support\Facades\Redis;
use App\PH;


class CatalogsParametrs extends Controller {
    
public function method($method) {
    
    $this->$method();
    
}
    public function catalog1() {
        
        $catalog = Catalog::find(1);
        $products = $catalog->products;
        $redis = Redis::connection();
        
        $products->each(function($product) use($redis) {
            $old = json_decode($redis->get("old:".$product->id)) ;
            d($old);
        });

        
    }
        public function catalog2() {
        
    }
        public function catalog3() {
        
    }
        public function catalog4() {
        
    }
        public function catalog5() {
            
       $catalog = Catalog::find(5);
       d($catalog->name);
        $products = $catalog->products;
        $redis = Redis::connection();

        $products->each(function($product) use($redis) {
            $old = json_decode($redis->get("old:" . $product->id));
            d($old->old_ob->attrs);
            
            
            //size
            
            
            
            
            
        });
    }
    
    public function products() {
        $redis = Redis::connection();
        $p_keys = $redis->keys('product:*');
        foreach($p_keys as $product) {

            $product = json_decode($redis->get($product));
            $old = json_decode($redis->get("old:" . $product->id));
            $attrs = $old->old_ob->attrs;

            if(isset($attrs->size)){
                $product_ph = (array)json_decode($redis->get("product_ph:" . $product->id));
                $new_ph = array();
                foreach($product_ph as $v) {
                    $new_ph[$v->name] = $v;
                }
                unset($product_ph);
                
                if(!isset($new_ph["size"])) {
                    
                }
                
                
                
                
            }
        }
    }

    
}