<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Catalog;
use App\Product;
use App\Http\Controllers\Controller;
use App\PH;
use App\PHR;
use Illuminate\Support\Facades\Redis;
use App\Filter_cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Loader\CsvFileLoader;
use Symfony\Component\Translation\Tests\Loader\CsvFileLoaderTest;
use Symfony\Component\Translation\Dumper\CsvFileDumper;
use App\Brand;
use Illuminate\Support\Facades\Response;

class TestController extends Controller {
        public function test($method,$param = null) {
        if(!is_null($param)){
            return $this->$method($param);
        }
        return $this->$method();
        
        }
        public function null_cat() {
            
            $data["products"] = Product::where("catalog_id",0)->get();
            return view("nullcat",$data);
        }
        public function set_cat($id) {
            
            $product = Product::find($id);
           // d($product);
           // d($product->name);
            
            setlocale(LC_ALL, 'ru_RU.UTF-8');
            $name = trim(preg_replace('/[^А-Яа-я ]/ui', '', $product->name));
            $name = $this->get2words($name);
            $pr = Product::where("catalog_id","!=",0)->where("name","like","%$name%")->get();
            
            $cat_pr_array = array();
            $cat_count_array = array();

            $pr->each(function($el) use(&$cat_pr_array,&$cat_count_array){
                //if($el->catalog_id == 0) var_dump("0 catalof");
                if(isset($cat_count_array[$el->catalog_id])) $cat_count_array[$el->catalog_id] = $cat_count_array[$el->catalog_id] + 1;
                else{
                   $cat_count_array[$el->catalog_id] = 1; 
                }

                
                $cat_pr_array[] = $el->catalog_id;
     
                
            });
            
            $max_count_catalog = !empty($cat_count_array)?$this->get_max_array($cat_count_array):0;



            
            $barnd_catalogs  = $product->brand->getCatalogs();
            $ph_catalogs = $product->uniqPhCatalog();

            $no_empty_arr = array();
            if(!empty($barnd_catalogs)) $no_empty_arr[] = $barnd_catalogs;
            if(!empty($ph_catalogs)) $no_empty_arr[] = $ph_catalogs;
            if(!empty($cat_pr_array)) $no_empty_arr[] = $cat_pr_array;
            $result_catalog = !empty($no_empty_arr)?array_shift($no_empty_arr):array();
            if(!empty($result_catalog)) {
            foreach($no_empty_arr as $arr) {
                $result_catalog = array_intersect($result_catalog,$arr);
            }
            }
            
            
            
            

            if(count($result_catalog) == 1) {
                $catalog = array_shift($result_catalog);
                $product->catalog_id = $catalog;
                $product->save();
                return Response::json( Catalog::find($catalog)->name );
            }
            else {
                if($max_count_catalog == 0) return Response::json( "<span style='color:red;'>Неопределен</span>" );
                $product->catalog_id = $max_count_catalog;
                $product->save();

                
                
                return Response::json( Catalog::find($max_count_catalog)->name );
            }
            
            

            
            

        }
        
        public function get2words($word){
            $new_word = "";
            $word_arr = explode(" ",$word);
            if(count($word_arr) > 1) {
                $new_word = $word_arr[0]." ".$word_arr[1];
            }
            return $new_word;
            
        }
        public function get_max_array($arr) {
            $save_arr = $arr;
        $max = array_shift($save_arr);
        $index_max = 0;
        foreach ($arr as $k => $a) {
            if ($a > $max) {
                $index_max = $k;
                $max = $a;
            }
            if ($a == $max) {
                $index_max = $k;
                $max = $a;
            }
        }
        return $index_max;
    }

    public function getonecatalog() {
        $catalog = Catalog::all();
        $res = 0;
        foreach ($catalog as $cat) {
            $res = DB::table("product_haracteristic")->where("catalogs", "like", "|<" . $cat->id . ">")->get();
           // d($res);
            foreach ($res as $p) {
                $ph = PH::find($p->id);
                $products = $ph->phr()->whereHas("product", function($q) {

                    $q->where('catalog_id', 0);
                })->take(1)->get();
                $cat = $ph->getCatalogs()[1];
                d($cat);
                foreach($products as $p) {
                    $p->product->catalog_id = $cat;
                    $p->product->save();
                }
                
                
                
            }
        }
    }

    public function getnext() {
        
        $products_phr = PHR::whereHas("product", function($q) {

                    $q->where('catalog_id', 0);
                })->get();
                
        foreach($products_phr as $k=>&$phr) {
            $prod = $phr->product;
            
            $barnd_catalogs  = $prod->brand->getCatalogs();
            $ph_catalogs = $prod->uniqPhCatalog();
            $result_catalog = array_intersect($barnd_catalogs, $ph_catalogs);
            
            $res_arr = array();
            foreach($result_catalog as $res) {
                
                
                $catalog = Catalog::find($res);
                $prod_keys = $prod->getPHsNamesKeys();
                $catalog_kyes = $catalog->getAFTypesArray();

                
                foreach($prod_keys as $k=>$v) {
                    if(in_array($v,$catalog_kyes )) {
                        unset($prod_keys[$k]);
                    }
                }
                if(count($prod_keys) == 0) {
                    $res_arr[] = $res;
                }
          
                //
               
                
                

                
            }
            
            if(count($res_arr) == 1) {
                $prod->catalog_id = $res_arr[0];
                $prod->save();
            }
            

            
            
            

        }        
                
        
    }
    public function catalog_count() {
        $ph = PH::get_with_catalog_count(2);
        d($ph);
        
    }
    public function delete_ph() {

        $name = PH::lists("name")->toArray();
        $name = array_unique($name);
        $str = "";
        foreach($name as $n){
            $str.= ","."'$n'";
        }
        d($str);
    }

    public function getnullcatalog() {
            $products =  Product::where("catalog_id",0)->get();
            print "<pre>";

            $all_attrs = array();
            $old_info = array();
            $products->each(function($product) use(&$all_attrs,&$old_info) {
             
              //  var_dump($product->migration_info->get_old_catalogs());
                $old = $product->migration_info->get_old_catalogs();
                //var_dump($old);
                $attrs = array();
                foreach($old->ph as $k=>$v) {
                   $attrs[] = $k; 
                }
                $all_attrs = array_merge($attrs,$all_attrs);
                $old->product = $product;
                $old_info[] = $old;
              //  d($old);
                
                
                

            });
            $all_attrs = array_unique($all_attrs);
          //  var_dump($all_attrs);
          //  print "</pre>";
            $data["old"] = $old_info;
            $data["attrs"] = $all_attrs;
         //   d($old_info);
            
            return view("nullcatalog",$data);
    }
        
        public function tt($param = null) {
            
//            $data = Product::whereHas("phr.ph", function($q) {
//
//            $q->where('id', 50);
//        })->count();
//        
//        d($data);
            $data = \App\Migration_info::get_old_products_by_hid("type",58);
            d($data);
                        $data = \App\Migration_info::get_old_products_by_hid("type",60);
            d($data);
    }
        
        public function test_catalog($catalog_id) {
        
        $catalog = Catalog::find( intval($catalog_id) );
        $ac = $catalog->getAccessFiltersArrayFormat();
     //   $c2 = $catalog->getAccessFiltersArrayTestFormat();
     //   d($c2);
        d($ac);
     //   d(key($ac)); 
        $syb = "1234567890abcdefghijklmnopqrstuvwxyz";
        d($syb[20]);
        $count = 

        $pwdgen = new CPasswordGenerator(5, 5, "01234");
        $string_array = array();
        while ($pwdgen->getNext($pwd)) {
            $string_array[] = $pwd;
        }
        
        foreach($string_array as &$string) {
            $str_ar = array();
            for($i = 0; $i < strlen($string); $i++) {
                $str_ar[] = intval($string[$i]);
            }
            $str_ar = array_unique($str_ar);
            asort($str_ar);
            
            $string = implode("",$str_ar);
            
        }
        $string_array = array_unique($string_array);
        d($string_array);

    }
    
    public function test10() {
        
        $q = Product::query();
        
       $products = Product::whereHas("phr.ph",function($q) use($param){
          
          $q->where('name', $param);
          
      });
      
    }
    
    public function proverkaBrand($catalog_id) {
        $catalog = Catalog::find($catalog_id);
        ///$products = $catalog->products->brand();
        $b = Brand::whereHas("products",function($q) use($catalog_id) {
            
            $q->where("catalog_id",$catalog_id);
            
            
        })->lists("id");
       // d($b);
        $brands = Brand::whereNotIn("id",$b)->get();
       // d($brands);
        $brands->each(function($brand) use($catalog_id) {
            
            $brand->delete_catalog($catalog_id);
            
        });
        
        
        //d($b);
       // $brand = Brand::find(10);
      //  d($brand);
      //  d($brand->products()->where("catalog_id",$catalog_id)->get());
        
        
        
        
        
    }
    
    public function proverkaType($catalog_id) {
        $catalog = Catalog::find($catalog_id);
        var_dump($catalog->name);
        $ph = PH::whereHas("phr.product",function($q) use($catalog_id) {
            
            $q->where("catalog_id",$catalog_id);
            
        })->where("name","type")->lists("id");
       /* $ph->each(function($h) {
            var_dump($h->name." ".$h->value);
            
        });*/
      //  d($ph);
        $phc = PH::whereNotIn("id",$ph)->where("name","type")->get();
        $phc->each(function($h) use($catalog_id){
            $h->delete_catalog($catalog_id);
        });
        
    }
    
    public function proverkaColor($catalog_id) {
               $catalog = Catalog::find($catalog_id);
        var_dump($catalog->name);
        $ph = PH::whereHas("phr.product", function($q) use($catalog_id) {

                    $q->where("catalog_id", $catalog_id);
                })->where("name", "color")->lists("id");
        /* $ph->each(function($h) {
          var_dump($h->name." ".$h->value);

          }); */
        //  d($ph);
        $phc = PH::whereNotIn("id", $ph)->where("name", "color")->get();
        $phc->each(function($h) use($catalog_id) {
            $h->delete_catalog($catalog_id);
        });
    }
    
    public function getSize($catalog_id) {
        $catalog = Catalog::find($catalog_id);
        $f = $catalog->getAccessFiltersArrayFormat();   
        if(isset($f["width"])) {
            $products = $catalog->products;
            foreach($products as $product) {
                $old = $product->migration_info->get_old_product();
                $attrs = $old->attrs;
               // d($attrs);
                if( isset($attrs["size"]) ) {
                    
                    $ph = PH::findPH("width",$attrs["size"]);
                 //   d($ph);
                    if(!is_null($ph)) {
                        $ph->add_catalog($catalog_id);
                    }
                    else {
                        $ph = PH::create("width",$attrs["size"]);
                        $ph->add_catalog($catalog_id);
                        
                    }
                    $product->savePH($ph->id);
                }
            }
            
        }

    }
    
    public function migratePh($catalog_id) {
        
        $catalog = Catalog::find($catalog_id);
        $f = $catalog->getAccessFiltersArrayFormat();
       // d($f);
        $products = $catalog->products()->lists("id")->toArray();

        
        $phids = \App\PH::whereHas("phr.product",function($q) use($products){
            $q->whereIn('id', $products);
            
        })->get();

        
        $phids->each(function($ph) use($f,$catalog) {

            if(isset($f[$ph->name])) {
                
                if(!in_array($ph->id, $f[$ph->name]) ) {

                    $ph->add_catalog($catalog->id);
                }
                
            }

            
            
        });
        
        $pbrand = \App\Brand::whereHas("products",function($q) use($products) {
            $q->whereIn('id', $products);
            
        })->get();

        
        $pbrand->each(function($brand) use($catalog_id) {
            
            $ac = $brand->getCatalogs();
            if(!in_array($catalog_id,$ac)) {
               $brand->add_catalog($catalog_id);
            }

        });
        


        
        
        
        
        
    }
    
    public function setProductHFormat($catalog_id){
        $catalog = Catalog::find($catalog_id);
         $products = $catalog->products;
         
         $products->each(function($p){
             
             $list =  explode("&!#",$p->haracteristic);

             foreach($list as &$l) {
                 $l = "<li>$l</li>";
             }
             //d($list);
             $p->haracteristic = "<ul>".implode("",$list)."</ul>";

             $p->save();
             
             
             
         });
         
        
    }
    
    public function setPHF($p) {
                     $list =  explode("&!#",$p->haracteristic);

             foreach($list as &$l) {
                 $l = "<li>$l</li>";
             }
             //d($list);
             $p->haracteristic = "<ul>".implode("",$list)."</ul>";

             $p->save();
        
    }
    
   /* public function set_catalog($catalog_id) {
        $redis = Redis::connection();
        $p = json_decode($redis->get("catalog_products:$catalog_id"));
        foreach ($p as $v) {

            $old = json_decode($redis->get("old:$v"));
            $catalog = Catalog::where("name",$old->old_cat->id_cat)->get();
            
            d($catalog);
            
            $this->update_redis_catalog();
          //  $p->
            //d($old);
        }
    }*/
    
    
    //Проверка товаров на правильность данных
    public function test_products($step = 0) {
        

        $redis = Redis::connection();
        $products = Product::query();
        $count = $products->count();
        $products = $products->get();
        

        
        $products->each(function($product) use($redis) {
            
            $old = json_decode($redis->get("old:".$product->id)) ;
            $catalog = Catalog::where("name",$old->old_cat->id_cat)->get();
            $cur_catalog = $product->catalog_id;
            
            if($product->catalog_id == 2) {
                d($old);
            }
            if( $catalog->count() > 0 ) {
                $need_catalog = $catalog[0]->id;

 
            }
            else {
                if( $old->old_cat->id_cat == "Холодильники и морозильники"){
                    $need_catalog = 14; 
                    
                }
                
            }
            
            if( isset($need_catalog) && ($cur_catalog !== $need_catalog) ) {
                
               // $product->catalog_id = $need_catalog;
               // $product->save();
                d($old);
                d("update product set catalog_id = $need_catalog where id = ".$product->id);
            }

            
        });
        
        return \Illuminate\Support\Facades\Response::json( $count );

        
        
    }
    
    
        //Проверка товаров на правильность данных
    public function test_products2($step = 0) {
        

        $redis = Redis::connection();
        $products = Product::query();
        $count = $products->count();
        $products = $products->get();
        

        
        $products->each(function($product) use($redis) {
            
            $old = json_decode($redis->get("old:".$product->id)) ;
          //  d($old->old_cat);
            
            
            if( $old->old_cat->id_podcat2 =="Холодильные камеры" ) {
                
                $product->savePH(101);
             // d($product->id); 
            }
                
                if( $old->old_cat->id_podcat2 =="Морозильные камеры" ) {
                    
                    $product->savePH(100);
                  //  d($product->id); 
                }
                    
            if( $old->old_cat->id_podcat1 =="Двухкамерные" ){
                
                $product->savePh(102);
              // d($product->id);  
            }    
                

//
//            
//            if($product->catalog_id == 2) {
//                d($old);
//            }
//            if( $catalog->count() > 0 ) {
//                $need_catalog = $catalog[0]->id;
//
// 
//            }
//            else {
//                if( $old->old_cat->id_cat == "Холодильники и морозильники"){
//                    $need_catalog = 14; 
//                    
//                }
//                
//            }
//            
//            if( isset($need_catalog) && ($cur_catalog !== $need_catalog) ) {
//                
//               // $product->catalog_id = $need_catalog;
//               // $product->save();
//                d($old);
//                d("update product set catalog_id = $need_catalog where id = ".$product->id);
//            }

            
        });
        
        return \Illuminate\Support\Facades\Response::json( $count );

        
        
    }
    
    public function product_operation() {
        return view("product_operation");
    }

    public function testduh($catalog_id) {
        $redis = Redis::connection();

        $p = json_decode($redis->get("catalog_products:$catalog_id"));
        
        foreach($p as $v) {
            $attr = json_decode($redis->get("product_ph:$v")) ;
          // d($attr);
           $an = array();
            foreach($attr as $k=>$vv){
                $an[] = $vv->name;
               // var_dump($vv->name."   ".$vv->value);
            }
            if(in_array("type", $an) ) continue;
            $old = json_decode($redis->get("old:$v")) ;
            $pod = $old->old_cat->id_podcat1;
            $ph = PH::find_by_redis("value",$pod);
          //  d($ph);
            if(!empty($ph)) {
                $ph = $ph[0];
                $product = Product::find($v);
                $product->savePH($ph->id);
                $fc = Filter_cache::where("catalog_id",$catalog_id)->where("type","h")->where("info_id",$ph->id)->get();
                if($fc->count() == 0) {
                    $fc = new Filter_cache();
                    $fc->catalog_id = $catalog_id;
                    $fc->type = "h";
                    $fc->info_id = $ph->id;
                    $fc->save();
                }
                else {
                    $fc = $fc[0];
                }
                $fc->add_product($v);
//d($fc);
                }
            //$ph_ob = PH::find($ph->id);
            //d($ph_ob);


            
           
            
            
            
        }

        
    }
    
    
    public function get_moroz() {
          $db = DB::connection('mig');
          $arr = array(31);
           $old_catalog =  $db->table('pamira_tovar')->whereIn("id_podcat1",$arr)->lists("id");
           $mg = \App\Migration_info::whereIn("old_id",$old_catalog)->where("type","product")->get();

           d($mg);
           
    }
    
    public function testduh2() {
        
    }
    
    public function redis() {
        $redis = Redis::connection();
        $products = Product::query()->skip(2999)->take(1000)->get();
        d($products->count());
       // $p2 = new Product();

       // var_dump($p);
      //  d($redis);
        
        Redis::pipeline(function($pipe) use($products)  {
            
            foreach($products as $p) {
                $ob = new \stdClass();
                $ob->old_ob = $p->migration_info->get_old_product();
                $ob->old_cat =$p->migration_info->get_old_catalogs();
               // d("old:".$p->id);
                $k = "old:".$p->id;
                $pipe->set($k, json_encode($ob));
                
                
            }
          //  for ($i = 0; $i < 1000; $i++) {
                
          //  }
        });


        //$product
        
    }
    
    public function redis2() {
        $redis = Redis::connection();
        $user = json_decode($redis->get("catalog_products:1"));
        d($user);
        
    }
    
    public function catalogs_to_redis() {
        $redis = Redis::connection();
        $catalogs = Catalog::all();
        Redis::pipeline(function($pipe) use($catalogs) {
            foreach ($catalogs as $cat) {
                $p = $cat->products()->lists("id");
                $p = json_encode($p);
                $k = "catalog_products:".$cat->id;
                $pipe->set($k, $p);
                
                
            }
        });
    }
    
    public function update_redis_catalog($catalog_id) {
        $catalog = Catalog::find($catalog_id);
        $redis = Redis::connection();
        $p = $cat->products()->lists("id");
        $p = json_encode($p);
        $k = "catalog_products:".$cat->id;
        $redis->set($k,$p);
        
        
    }
    
   public function product_to_redis() {
        $redis = Redis::connection();
        $products = Product::query()->skip(1999)->take(2000)->get();
        d($products->count());
        // $p2 = new Product();
        // var_dump($p);
        //  d($redis);

        Redis::pipeline(function($pipe) use($products) {

            foreach ($products as $p) {
                // d("old:".$p->id);
                $p->haracteristic = "";
                $k = "product:" . $p->id;
                $pipe->set($k, json_encode($p));
            }
            //  for ($i = 0; $i < 1000; $i++) {
            //  }
        });
    }
    
    public function ph_to_redis() {
        $redis = Redis::connection();
        $ph = PH::all();
       // d($products->count());
        // $p2 = new Product();
        // var_dump($p);
        //  d($redis);

        Redis::pipeline(function($pipe) use($ph) {

            foreach ($ph as $p) {
                // d("old:".$p->id);
 
                $k = "ph:" . $p->id;
                $pipe->set($k, json_encode($p));
            }
            //  for ($i = 0; $i < 1000; $i++) {
            //  }
        });
    }
    
        public function phr_to_redis() {
        $redis = Redis::connection();
        $ph = PHR::all();
       // d($products->count());
        // $p2 = new Product();
        // var_dump($p);
        //  d($redis);

        Redis::pipeline(function($pipe) use($ph) {

            foreach ($ph as $p) {
                // d("old:".$p->id);
 
                $k = "phr:" . $p->id;
                $pipe->set($k, json_encode($p));
            }
            //  for ($i = 0; $i < 1000; $i++) {
            //  }
        });
    }
    
       public function product_ph_to_redis() {
        $redis = Redis::connection();
        $products = Product::query()->skip(0)->take(2000)->get();
        d($products->count());
        // $p2 = new Product();
        // var_dump($p);
        //  d($redis);

        Redis::pipeline(function($pipe) use($products) {

            foreach ($products as $p) {
                // d("old:".$p->id);
                $k = "product_ph:" . $p->id;
                $pipe->set($k, json_encode($p->getPHs()));
            }
            //  for ($i = 0; $i < 1000; $i++) {
            //  }
        });
    }
    
    
    public function getCSVData() {
                $path = public_path() . '/uploads/product.csv';
 
        
                    if(!file_exists($path) || !is_readable($path))
                return FALSE;

            $header = NULL;
            $data = array();
            if (($handle = fopen($path, 'r')) !== FALSE)
            {
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE)
                {
                    if(!$header)
                        $header = $row;
                    else
                        $data[] = array_combine($header, $row);
                }
                fclose($handle);
            }
         //   d($data);
            $data2 = array();
            
            foreach($data as $k=>$v) {
                $elem = array();
                foreach($v as $s1=>$s2) {

                $elem[trim(str_replace("\n", "", $s1))] = trim(str_replace("\n", "", $s2));
               
                }
                $data2[] = $elem;

            }
            return $data2;
    }
    
    public function readCsv() {
        $data2 = $this->getCSVData();
        foreach ($data2 as $product) {
            $p = Product::getByName($product["Имя"]);
            $p->each(function($pr) use($product) {
                d($pr->getPHs()); 
            });
        }
    }

    public function csv() {

        $data2 = $this->getCSVData();
        //d($data2);

        $catalog = array();

        foreach ($data2 as $product) {
            //dd($product);

            // d($product);
            $p = Product::getByName($product["Имя"]);
            // d($p);
            if ($product["Каталог"] == "Посуда") {
                
            }
            //"Кухонные мойки"
            $catalog[] = $product["Каталог"];
            switch ($product["Каталог"]) {
                case "Посуда":
                    $p->each(function($pr) use($product) {
                        $this->saveCsvElem($pr, 15, $product);
                    });

                    break;
                case "Кухонные мойки":
                    $p->each(function($pr) use($product) {
                        $this->saveCsvElem($pr, 1, $product);
                    });

                    break;
                case "Варочные поверхности":
                    $p->each(function($pr) use($product) {
                        $this->saveCsvElem($pr, 6, $product);
                    });
                    break;
                case "Кухонные смесители":
                    $p->each(function($pr) use($product) {
                        $this->saveCsvElem($pr, 2, $product);
                    });
                    break;
                case "Franke":
                    $p->each(function($pr) use($product) {

                        $this->saveCsvElem($pr, 2, $product);
                    });
                    break;
            }

            // $p->save();
        }
    }

    public function saveCsvElem($pr, $catalog_id, $product) {
        $pr->catalog_id = $catalog_id;
        

        $type = $product["Тип"];
        $material = $product["Материал"];
        $color = $product["Цвет"];
       // d($catalog_id);
     //   d($type);
        if($type == "Газовая") $type = "Газовые";
        if($type == "С подключением фильтра") $type = "С подключением к фильтру";
        
        
        $pht = PH::findPH("type", $type);
        if(is_null($pht)){
            dd($type);
            
        }
        $phm = PH::findPH("material", $material);
        $phc = PH::findPH("color", $color);
       
//        if(is_null($pht)) dd($type."t");
//        if(is_null($phm)) dd($material."mat");
//        if(is_null($phc)) dd($color."color");

        $pr->savePH($pht->id);
        $pr->savePH($phm->id);
        if(!is_null($phc)) {
        $pr->savePH($phc->id);
        }
        $pr->save();
        
        d("Бренд ".$product["Бренд"]);
        $b = Brand::getByName($product["Бренд"]);
        if( is_null($b) ) d("null brand ".$product["Бренд"]);
        //d("name ". Brand::getByName($product["Бренд"]));
        $pr->brand_id = $b->id;
        $pr->save();
        $this->setPHF($pr);
        
        if($catalog_id == 15) {
//            d($type);
//            d($pht->id);
//            d($phm->id);
//            if(!is_null($phc)) {
//               d($phc->id); 
//            }
//            
//           d($pr->getPHs()); 
        }
        
        
    }
    
    public function tp() {
        
        $phr = PHR::where("haracteristic_id",104)->get();
        d($phr);
        
    }
    
public function test_catt() {
    
        $catalog = Catalog::find(13);
        $products = $catalog->products;

        $products->each(function($p) {
            
            $phs = $p->getPHs();
            
            foreach($phs as $k=>$ph) {
              d( $ph->name." ".$ph->value);   
            };

            d("---");
            
            
            
            //d($p->getPHs());
            
        });
    }
    
    //серия домино
    public function test_str() {
        $catalog = Catalog::find(6);
        $products = $catalog->products;
        
        $products->each(function($p) {
            $old = $p->migration_info->get_old_catalogs();
           // d($old->id_podcat1);
            if($old->id_podcat1 == "Серия Домино") {
               // print $old->id_podcat1;
                $p->savePH(73);
            }
          //  d($p->migration_info->get_old_catalogs());
            
        });
        
        return redirect('catalog/cache_all/6'); 
        

    }

}

class CPasswordGenerator{ 
    /** 
     * @desc минимальная длина генерируемого пароля 
     * @access private 
     */ 
    var $_minlen = 0; 
     
    /** 
     * @desc максимальная длина генерируемого пароля 
     * @access private 
     */     
    var $_maxlen = 0; 
     
    /** 
     * @desc символы, из которых должен состоять пароль 
     * @access private 
     */ 
    var $_alphabet = ""; 
     
    /** 
     * @desc текущий вариант пароля 
     * @access private 
     */ 
    var $_curPwd = ""; 
     
    /** 
     * @desc массив с числами, соответствующими буквам алфавита 
     * @access private 
     */ 
    var $_curPwdAr = array(); 

    /** 
     * @desc текущая длина пароля 
     * @access private 
     */ 
    var $_curPwdLen = 0; 
     
    /** 
     * @desc вариант последнего пароля при заданной длине и алфавите 
     * @access private 
     */ 
    var $_lastPwd = ""; 
     
    /** 
     * @desc 
     * @access private 
     */ 
    var $_index = 0; 
     
    /** 
     * @desc длина алфавита 
     * @access private 
     */ 
    var $_alphaLen = 0; 

    /** 
     * @desc длина алфавита минус один, чуток ускоряет генерацию 
     * @access private 
     */     
    var $_intAlphaLen = 0; 
     
    /** 
     * Конструктор 
     * @access public 
     * @param int минимальная длина пароля 
     * @param int максимальная длина  
     * @param string алфавит 
     * @return void 
     */ 
    function __construct($minLength, $maxLength, $alphabet) 
    { 
        $this->_minlen      = $minLength; 
        $this->_maxlen      = $maxLength; 
        $this->_alphabet    = $alphabet; 
        $this->_alphaLen    = strlen($this->_alphabet); 
        $this->_intAlphaLen = $this->_alphaLen - 1; 
         
        $this->_init($this->_minlen); 
    } 
     
    /** 
     * Производит инициализацию 
     * @access private 
     * @return bool true-успешно, false-неудача 
    */ 
    function _init($len) 
    { 
        if (!strlen($this->_alphabet))  
        { 
            trigger_error("Alphabet is empty"); 
            return false; 
        } 
        if(!($this->_minlen <= $len && $len <= $this->_maxlen)) 
        { 
            //trigger_error("Password length isn't correspond for its conditions"); 
            return false; 
        } 
         
        //генерируем последний вариант пароля 
        //и заполняем таблицу для бэктрекинга (backtracking) 
        $this->_curPwdLen = $len; 
        $this->_index      = 0; 
        $this->_curPwdAr  = array(); 
        $this->_curPwd    = ""; 
        $this->_lastPwd   = ""; 
        for($i = 0; $i < $len; $i++) 
        { 
            $this->_lastPwd       .=  $this->_alphabet[$this->_alphaLen - 1]; 
            $this->_curPwd        .= "a"; //может стоять любой символ 
            $this->_curPwdAr[$i]   = -1; 
        } 
        $this->_curPwdAr[$len] = $this->_alphaLen; 
        

         
        return true; 
    } 
     
    /** 
     * Генерирует пароль 
     * @access public 
     * @param &string переменная, куда будет записан пароль 
     * @return bool true-пароль успешно сгенерирован, false-все варианты исчерпаны 
    */ 
    function getNext(&$pwd) 
    { 
        if($this->_curPwd == $this->_lastPwd) 
        { 
            if (!$this->_init($this->_curPwdLen + 1)) return false; 
        } 
         
        while($this->_index >= 0) 
        { 
            while($this->_curPwdAr[$this->_index] < $this->_intAlphaLen) 
            { 
                $this->_curPwdAr[$this->_index] += 1; 
                $this->_curPwd[$this->_index]    = $this->_alphabet[$this->_curPwdAr[$this->_index]]; 

                $this->_index++; 
                 
                if($this->_index == $this->_curPwdLen) 
                { 
                    $pwd = $this->_curPwd; 
                    return true; 
                } 
            } 
            if($this->_index != $this->_curPwdLen) $this->_curPwdAr[$this->_index] = -1; 
            $this->_index--; 
        } 
    }
    

    
    

} 