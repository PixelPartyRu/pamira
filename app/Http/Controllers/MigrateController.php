<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\Catalog;
use App\Migration_info;
use Illuminate\Support\Facades\Schema;
use App\Brand;
use App\PH;

class MigrateController extends Controller {

    public function __construct() {
        
    }
    //Каталоги с которыми все ясно
    private function get_first_level_catalog() {
        $db = DB::connection('mig');
        $old_catalog =  $db->table('pamira_catalog')->lists("name");

        $cur_catalog = Catalog::lists("name")->toArray();

         $all_arr = array_merge($old_catalog,$cur_catalog);
         
         $all_arr = array_unique($all_arr);
         
         $old_catalog = new \Illuminate\Support\Collection( $old_catalog );
         $cur_catalog = new \Illuminate\Support\Collection( $cur_catalog );

         $mix_col = new \Illuminate\Support\Collection( $all_arr );
         $has_all = new \Illuminate\Support\Collection();
         $has_only_new = new \Illuminate\Support\Collection();
         $has_only_old = new \Illuminate\Support\Collection();
         foreach($mix_col as $k=>$v) {
             if($old_catalog->contains($v) && $cur_catalog->contains($v) ) {
                 $has_all->push($v);
             }
             else if($old_catalog->contains($v)) {
                 $has_only_old->push($v);
             }
             else if($cur_catalog->contains($v)) {
                 $has_only_new->push($v);
             }
             

             
             
         }
        return $has_all;
 
    }
    
    public function test() {
         $db = DB::connection('mig');
        $old_catalog =  $db->table('pamira_catalog')->lists("name");

        $cur_catalog = Catalog::lists("name")->toArray();

         $all_arr = array_merge($old_catalog,$cur_catalog);
         
         $all_arr = array_unique($all_arr);
         
         $old_catalog = new \Illuminate\Support\Collection( $old_catalog );
         $cur_catalog = new \Illuminate\Support\Collection( $cur_catalog );

         $mix_col = new \Illuminate\Support\Collection( $all_arr );
         $has_all = new \Illuminate\Support\Collection();
         $has_only_new = new \Illuminate\Support\Collection();
         $has_only_old = new \Illuminate\Support\Collection();
         foreach($mix_col as $k=>$v) {
             if($old_catalog->contains($v) && $cur_catalog->contains($v) ) {
                 $has_all->push($v);
             }
             else if($old_catalog->contains($v)) {
                 $has_only_old->push($v);
             }
             else if($cur_catalog->contains($v)) {
                 $has_only_new->push($v);
             }
             

             
             
         }

    }
    
    //первый этап - перенос товаров без дополнительных атрибутов
    public function first_level() {
        $db = DB::connection('mig');
        $old_product =  $db->table('pamira_tovar')->get();

        foreach($old_product as $old) {
            $new_product = new Product();
            $new_product->name = $old->name_rus;
            $new_product->article = $old->artikul;
            $new_product->haracteristic = $old->haracteristic;
            
            $new_product->country = $old->country;
            
            $new_product->sales_leader = $old->lider_prodash;
            
            
            $new_product->img = $old->img;
            $new_product->cost = $old->cost;
            
            $new_product->cost_trade = $old->cost_opt;
            $new_product->viewcost = $old->view_cost;
            $new_product->viewcost_nonauth = $old->viewcost_nonauth;
            $new_product->in_main_page = $old->in_main_page;
            $new_product->code_1c = $old->kod_1c;
            
            $new_product->save();
            Migration_info::create( array("old_id"=>$old->id,"new_id" => $new_product->id,"type" => "product") );
            
            
            
            
        }
        
    }
    
    public function migrate_brand() {

        $db = DB::connection('mig');
        $old_brand = $db->table('pamira_brand')->get();
        foreach ($old_brand as $old) {
            $new_brand = new Brand();
            $new_brand->title = $old->name;
            $new_brand->alias = \App\Jobs\Helper::translit($old->name);
            $new_brand->img = $old->img_logo;
            $new_brand->description1 = $old->anons;
            $new_brand->description2 = $old->opisanie;
            $new_brand->forcart = $old->textforcart;
            $new_brand->keywords = $old->keywords;
            $new_brand->main_page =  $old->vis;
            
            $new_brand->save();
            Migration_info::create( array("old_id"=>$old->id,"new_id" => $new_brand->id,"type" => "brand") );
        }
    }
    
    public function set_brand_for_product() {
        $db = DB::connection('mig');
        $old_product =  $db->table('pamira_tovar')->get();
        $mps = Migration_info::getMigratedProduct();
        foreach($mps as $mp) {

            $old_product = $mp->get_old_product();
            if($old_product->id_brand == 0) {
                $mp->product->brand_id = 0;
            }
            else {
               $brand = Migration_info::getBrandByOldId($old_product->id_brand); 
               $mp->product->brand_id = $brand->id;
            }
            

            
            $mp->product->save();

        }
        
    }
    
    public function set_catalog_for_product_first_level() {
        
        $catalogs_list = $this->get_first_level_catalog();

        $mps = Migration_info::getMigratedProduct();
        foreach($mps as $mp) {
            $old_product = $mp->get_old_product();
            if( is_null($old_product->old_catalog) ){
               $mp->product->catalog_id = 0; 
            }
            else {
                
                $name_catalog = $old_product->old_catalog->name;
                if( !$catalogs_list->contains($name_catalog) ) continue; 
                $cq = Catalog::where("name",$name_catalog)->get();

                if( $cq->count() == 0) {

                    
                }
                else {
                $catalog_id = $cq->first()->id;
                $mp->product->catalog_id = $catalog_id; 
                }


            }
            $mp->product->save();

        }
        
    }
    
    public function set_catalog_for_product_second_level() {
        $db = DB::connection('mig');
        $old_catalog = $db->table('pamira_tovar')->where("id_cat",14)->get();

        $ids = array();
        foreach($old_catalog as $old) {
            $ids[] = $old->id;
        }
        $list = $db->table('pamira_tovar')->where("id_cat",14)->lists("id");
        $mps = Migration_info::whereIn("old_id",$list)->get();

       foreach($mps as $mp) {

           $mp->product->catalog_id = 14;
           $mp->product->save();
       }

        
    }
    //id_podcat1
    ///54
    public function set_catalog_for_product_third_level() {
        
        $db = DB::connection('mig');

        
        $list = $db->table('pamira_tovar')->where("id_podcat1",54)->lists("id");
        $mps = Migration_info::whereIn("old_id",$list)->get();
        foreach($mps as $mp) {

           $mp->product->catalog_id = 3;
           $mp->product->save();
       }
        
    }
    
    //
    public function set_attr($start_count) {
        

        $mps = Migration_info::where("type","product")->skip($start_count)->take(200)->get();
        
        foreach($mps as $mp) {
            $old = $mp->get_old_product();

            foreach($old->attrs  as $k => $atr) {
                $q = PH::where("value",$atr)->where("name",$k)->get();

                if($q->count() == 0) {
                  $ph = PH::create(array('name' => $k,'value' => $atr));
                  $ph->add_catalog($mp->product->catalog_id);
                  
                }
                else {
                   $ph =  $q->first();
                }
                $mp->product->savePH($ph->id);
                //savePH
                
                
            }
        }
        return $mps->count();
        
    }
    
    public function set_catalog_for_product_five_level() {
        
       $db = DB::connection('mig');
       $list = $db->table('pamira_tovar')->where("id_podcat1",18)->lists("id"); 
       $mps = Migration_info::whereIn("old_id", $list)->get();
        foreach ($mps as $mp) {

            $mp->product->catalog_id = 15;
            $mp->product->save();
        }
    }
    
    public function users_migrate() {
        $db = DB::connection('mig');
        $list = $db->table('pamira_users')->get();

        foreach ($list as $user) {


            $sh = \App\Shares_participants::create(array(
                        'name' => $user->name,
                        'salon' => $user->salon,
                        'adress' => $user->address,
                        'region_id' => $user->categ
                            )
            );
        }
    }
    
    public function get_old_info($id) {
        
        $old = Migration_info::where("new_id",$id)->where("type","product")->get()->first();
        d($old->get_old_product_clean());
    }
    
    public function custom($method) {
        
        print $method;
        return $this->$method();
        
    }
    public function get_old_dif_new() {
                $db = DB::connection('mig');
        $old_catalog =  $db->table('pamira_catalog')->lists("id");
        $old_info_m = Migration_info::lists("old_id")->toArray();
        dd(array_diff($old_catalog,$old_info_m));
        
        
    }
    
    public function with_wb() {
        print "<pre>";
        $catalogs = PH::getCatalogsByName("wardrobe");
        foreach($catalogs as $catalog) {
            $cat = Catalog::find($catalog);
            foreach($cat->products as $product) {
                $val = $product->migration_info->get_old_ph("base");
                $val = $val == "Угловой шкаф"?"Угловой":$val;
                
                $ph = PH::findPH("wardrobe",$val);
                if(!is_null($ph)){
                    $product->savePH($ph->id);
                }
            }
            
        }
        print "</pre>";

        
        
    }
        public function with_50() {
        print "<pre>";
        $catalogs = PH::getCatalogsByHid(50);
        d($catalogs);
            $data1 = \App\Migration_info::get_old_products_by_hid("type",58);
            $data2 = \App\Migration_info::get_old_products_by_hid("type",60);
            $data = array_merge($data1,$data2);
            d($data);
            $products = Product::whereHas("migration_info", function($q) use($data) {

                    $q->whereIn('old_id', $data);
                })->get();
             $products->each(function($item){
                 $item->savePH(50);
             });



        print "</pre>";

        
        
    }
    
    public function doza() {
            $data = array();
            
            $data = array_merge($data,\App\Migration_info::get_old_products_by_hid("device",24));
            $data = array_merge($data, \App\Migration_info::get_old_products_by_hid("type",75));
            $data = array_merge($data,\App\Migration_info::get_old_products_by_hid("type",76));
            $data = array_merge($data,\App\Migration_info::get_products_by_podcat1(54));
            d(array_unique($data));

             $products = Product::whereHas("migration_info", function($q) use($data) {

                    $q->whereIn('old_id', $data);
                })->get();
        $products->each(function($item) {
            $item->catalog_id = 3;
            $item->save();
        });
    }
    
        public function dealer_migrate() {
        $db = DB::connection('mig');
        $list = $db->table('pamira_members')->get();

        foreach ($list as $user) {


           $dealer = new \App\Dealer();
           $dealer->type = "dealer";
           $dealer->name = $user->name_members;
           $dealer->adress = $user->name_division;
           
           $dealer->password = $user->pass;
           $dealer->region_id = $user->region_id;


               $dealer->email = $user->email;
           
           
           $dealer->save();
  
           
           
           
           
           
        }
    }

    //array_diff

}





