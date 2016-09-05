<?php 

/* Класс кеширования каталога */
namespace App;
use App\Filter_cache;
//Кешированная инфа для выборки в фильтре
class Cache_catalog{    
    public $brands;
    public $haracteristic;
    public $catalog_ob;
    public function __construct(Catalog $catalog_ob) {
        $this->catalog_ob = $catalog_ob;
        //$this->set_cache();
        
    }
    
    public function update_cache($info_id,$type) {
        $fc_ob = Filter_cache::where("catalog_id",$this->catalog_ob->id)
                ->where("type",$type)
                ->where("info_id",$info_id)
                ->get();
        if($fc_ob->count() == 0) {
            $fc = new Filter_cache();
            $fc->type = $type;
        } else {
            $fc = $fc_ob->first();
        }
        
        if ($fc->type == "h") {
            $info = PHR::getProductsByHaracteristicId($info_id)->where("catalog_id", $this->catalog_ob->id)->lists("id")->toArray();
            
        }
        if($fc->type == "b") {
            $brand = Brand::find($info_id);
            $info = $brand->products()->where("catalog_id",$this->catalog_ob->id)->lists("id")->toArray();
        }
       // d($info);
        $fc->data = implode("|", $info);
        $fc->save();
        
    }
    
    //Генерация инфы для кеширования
    public function set_cache_info() {
        
        //Генерация списка товаров, относящихся к бренду и каталогу
        $this->brands = array();
        $brands = $this->catalog_ob->getAccessBrands();
        foreach($brands as $brand) {
            
            $list = $brand->products()->where("catalog_id",$this->catalog_ob->id)->lists("id");
            $this->brands[$brand->id] = $list->toArray();
        }
        
        //Генерация списка товаров, относящихся к характеристике PH и каталогу
        $har = $this->catalog_ob->getAccessFilters();
        $hIds = $this->catalog_ob->getAccessHIds();
        $this->haracteristic = array();
        foreach($hIds as $h) {

                    
            $this->haracteristic[$h] = PHR::getProductsIdsByHaracteristicId($h,$this->catalog_ob->id);
        }
        
        
    }
    //Сохранения кеша в бд ( см модель Filter_cache)
    public function to_database() {
        foreach($this->haracteristic as $k=>$info) {
            $fc = new Filter_cache();
            $fc->catalog_id = $this->catalog_ob->id;
            $fc->type = "h";
            $fc->info_id = $k;
            $fc->data = implode("|",$info);
            $fc->save();
            
            
            
        }
        foreach ($this->brands as $k => $info) {
            $fc = new Filter_cache();
            $fc->catalog_id = $this->catalog_ob->id;
            $fc->type = "b";
            $fc->info_id = $k;
            $fc->data = implode("|", $info);
            $fc->save();
        }
    }
    
    
    
    //Вся инфа по каталогу из базы
    public function get_from_database() {
        $this->haracteristic = array();
        $this->brands = array();
        $info_h = Filter_cache::where("catalog_id",$this->catalog_ob->id)->where("type","h")->get();
      // d($info_h);
        foreach($info_h as $k => $h) {
          $this->haracteristic[$h->info_id] = explode("|",$h->data);
          unset($info_h[$k]);
          
        }
        $info_b = Filter_cache::where("catalog_id", $this->catalog_ob->id)->where("type", "b")->get();

        foreach ($info_b as $k => $ib) {
            
            $this->brands[$ib->info_id] = explode("|", $ib->data);
            unset($info_b[$k]);
        }
       // unset($this->catalog_ob);
    }
    
   // Выборка по характеристикам id ($h_array - id характеристик ph, $b_array - id брендов)
    public function get_from_database_by_hid($h_array,$b_array) {
        $this->haracteristic = array();
        $this->brands = array();
        $q = Filter_cache::where("catalog_id", $this->catalog_ob->id)->where("type", "h");
        if(!empty($h_array)) {
            $q = $q->whereIn("info_id",$h_array);
        }
        $info_h = $q->get();
        // d($info_h);
        foreach ($info_h as $k => $h) {
            $this->haracteristic[$h->info_id] = explode("|", $h->data);
            unset($info_h[$k]);
        }
        $q = Filter_cache::where("catalog_id", $this->catalog_ob->id)->where("type", "b");
        if(!empty($b_array)) {
            $q = $q->whereIn("info_id",$b_array);
        }
        $info_b = $q->get();

        foreach ($info_b as $k => $ib) {

            $this->brands[$ib->info_id] = explode("|", $ib->data);
            unset($info_b[$k]);
        }
        //unset($this->catalog_ob);
    }
    
    public function getProductsByInfoId($id) {

        $res = Filter_cache::where("catalog_id", $this->catalog_ob->id)
                ->where("type", "h")->where("info_id", $id);

        if( $res->count() == 0) return array();
        $ob = $res->get()->first();
        return explode("|", $ob->data);
        
    }
     public function getProductsByBrandId($id) {
        $res = Filter_cache::where("catalog_id", $this->catalog_ob->id)
                ->where("type", "b")->where("info_id", $id);
        if( $res->count() == 0) return array();
        $ob = $res->get()->first();
        return explode("|", $ob->data);
        
    }
    public function get_hp_by_array($h_array) {
        $q = Filter_cache::where("catalog_id", $this->catalog_ob->id)->where("type", "h");
        if (!empty($h_array)) {
            $q = $q->whereIn("info_id", $h_array);
        }
        $info_h = $q->get();
        // d($info_h);
        $ret_arr = array();
        foreach ($info_h as $k => $h) {
            $ret_arr  =  array_merge($ret_arr,explode("|", $h->data));
            unset($info_h[$k]);
        }
        return array_unique($ret_arr);
    }
        public function get_bp_by_array($b_array) {
            if(empty($b_array)) return array();
        $q = Filter_cache::where("catalog_id", $this->catalog_ob->id)->where("type", "b");
        if (!empty($b_array)) {
            $q = $q->whereIn("info_id", $b_array);
        }
        $info_h = $q->get();
        // d($info_h);
        $ret_arr = array();
        foreach ($info_h as $k => $h) {
            $ret_arr  =  array_merge($ret_arr,explode("|", $h->data));
            unset($info_h[$k]);
        }
        return array_unique($ret_arr);
    }
    
    public function get_merge_h_values($id_array) {
        $arr = array();
        foreach($id_array as $id) {
           $arr = array_merge($this->haracteristic[$id],$arr);
        }
        return array_unique($arr);
        
    }
    
}
