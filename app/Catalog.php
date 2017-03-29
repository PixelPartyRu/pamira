<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\Helper;
use App\PH;
use App\PHR;
use App\Cache_catalog;

class Catalog extends Model
{
  protected $table = 'catalog';
  public $cache_object;
  protected $fillable = array('name', 'level', 'alias','parent_id','order','title','description','keywords','h1','text','filter');

  public static $noFilterArray = [
        'type' => [
            5, 8, 10, 16
        ],
        'device' => [
            1, 2, 3, 6, 7, 9, 10, 12, 15, 13
        ],
        'width' => [
            7, 8, 11, 12, 13, 16
        ],
        'style' => [
            11, 12,
        ],
        'material' => [
            11, 12, 3, 2
        ],
  ];

  public static $filtersOrder = [
      "type" => 0,
      "material" => 1,
      "style" => 2,
      "width" => 3,
      "color" => 4,
      "device" => 5,
      "assortment" => 6
  ];

  public function __construct(array $attributes = array()) {

      parent::__construct($attributes);

  }

  public function products() {
      return $this->hasMany("App\Product","catalog_id","id");
  }
  //доступные фильры для каталога
  public function getAccessFilters() {
       // $filter_param1 = explode("|", $this->filter);
       $valid_filters = array_map('trim', explode('|', $this->filter));
       
        $filter_param = PH::where("catalogs","like","%<".$this->id.">%")->lists("name")->unique()->toArray();


        $haracteristic = \App\Product::getProductHaracteristic();
        $filters = array();

        foreach ($filter_param as $k => $v) {
            if($v == "") continue;
            $filter = new \stdClass();

            $filter->label = $haracteristic[$v];
            $filter->name = $v;


            $filter->values = $this->getAccessFiltersValues($v)->lists("value", "id");

            if($filter->name == "width")
            {
                $arr = $filter->values->toArray();
                asort($arr);
                $filter->values = collect($arr);
            }

            if($filter->name == "color" ) {
                $arr = $this->getAccessFiltersValues($filter->name)->get()->toArray();
                usort($arr, function($a, $b){
                    return strcasecmp($a['value'],$b['value']);
                });
                $filter->values = collect($arr);
            }
            if( $filter->name == "device" || $filter->name == "material") {
                $arr = $filter->values->toArray();
                asort($arr);
                $filter->values = collect($arr);
            }


            $filters[] = $filter;
        }
        foreach ($filters as $k =>$filter)
        {
            //if(isset(self::$noFilterArray[$filter->name]) && in_array($this->id, self::$noFilterArray[$filter->name]))
            if(!in_array($filter->name, $valid_filters) || (isset(self::$noFilterArray[$filter->name]) && in_array($this->id, self::$noFilterArray[$filter->name])))
            {
                unset($filters[$k]);
            }
        }
        usort($filters, function($a,$b) {
            if(isset(self::$filtersOrder[$a->name]) && isset(self::$filtersOrder[$b->name]))
            {
            return (self::$filtersOrder[$a->name] - self::$filtersOrder[$b->name]);
            }
            return 1;
        });
        return $filters;
    }

   public function getAccessFiltersWithCount() {
        $ah = $this->getAccessFilters();
        foreach ($ah as $h) {

            foreach ($h->values as $k=> $value) {

                if ($h->name !== "color") {
                  $param =  $k;
                } else {
                   $param =  $value["id"];
                }
                $products = $this->products()->whereHas("phr", function($q) use($param) {

                    $q->where('haracteristic_id', $param);
                });
               // $h->count = $products->count();
              //  $co = $this->getCacheObject();

              //  $h->cache_count = count($co->getProductsByInfoId($param));
            }
        }
        return $ah;
    }

    public function getCountProductByHid($hid) {
       $data["in_catalog"] = $this->products()->whereHas("phr", function($q) use($hid) {

            $q->where('haracteristic_id', $hid);
        })->count();


        $data["in_cache"] = count($this->getCacheObject()->getProductsByInfoId($hid));
        return $data;
    }

    public function getAccessFiltersArrayTestFormat() {

        $filter_array = array();
        $filters = $this->getAccessFilters();

        foreach ($filters as $filter) {

            $val_array = array();
            foreach ($filter->values as $k => $v) {
                $elem = new \stdClass();

                if ($filter->name == "color") {
                    $val_array[] = $v["id"];
                } else {
                    $val_array[] = $k;
                }
                $elem->vals = $val_array;
                $elem->name = $filter->name;
                $filter_array[] = $elem;

            }

        }

        return $filter_array;
    }
   public function getAccessFiltersArrayFormat() {

       $filter_array = array();
       $filters = $this->getAccessFilters();

       foreach($filters as $filter) {

       $val_array = array();
           foreach($filter->values as $k => $v) {
               if($filter->name == "color") {
                   $val_array[] = $v["id"];
               }
               else {
                 $val_array[] = $k;
               }

           }
          $filter_array[$filter->name] = $val_array;
       }

       return $filter_array;

    }

    public function getAFTypesArray() {
        $filter_array = array();
        $filters = $this->getAccessFiltersArrayFormat();
        foreach($filters as $k=>$v) {
            $filter_array[] = $k;
        }
        return $filter_array;

    }

    public function hasFilter($name) {
       $filters = $this->getAccessFiltersArrayFormat();

       return isset($filters[$name])?$filters[$name]:null;
    }



    //Получить доутпные id характеристик для каталога
    public function getAccessHIds() {
        $filters = $this->getAccessFilters();
        $ids = array();

        foreach($filters as $filter) {
            foreach($filter->values as $k => $v) {

                if(!is_array($v)) {
               $ids[] = $k;
                }
                else {
                   $ids[] = $v["id"];
                }


            }

        }
        return $ids;
    }

  /*  public function getTypes() {
        return $this->getAccessFiltersValues($v);
    }*/
    public function getEmptyFiltersValues($filter_name) {
        $values = PH::where("name", $filter_name);

        return $values;
    }

    public function getAccessFiltersValues($filter_name) {
        $values = PH::where("name",$filter_name)
                ->where("catalogs","like","%<".$this->id.">%");
        return $values;

    }
    public function getAccessBrands() {
        return Brand::where("catalogs","like","%<".$this->id.">%")
                      ->orderBy("title","asc")
                      ->get();
    }
    private function getAccessBrandsIds() {
        return Brand::where("catalogs", "like", "%<" . $this->id . ">%")->lists("id")->toArray();
    }

    public function getRandCategoryImg($category_id,$brand_id) {

           $q =
                   PHR::getProductsByHaracteristicId( $category_id )
                   ->where("catalog_id",$this->id)->where("brand_id",$brand_id)->orderBy("id","asc");
           if($q->count() > 0) {
               $products = $q->get()->toArray();
               return $products[ 0 ]["img"];

           }

           return null;




    }



    //Возвращает фильтры, которые не доступны
    //Параметры - текущие значения филтра и запрос с фильтрацией
    public function getDisableFilters($data,$disabled_by) {

        unset($data['catalog']);
        unset($data['disabled_by']);
        if( $disabled_by === "brand") {
            return $this->disableFiltersByBrand($data);
        }
        else {
          // return $this->disableFiltersByHaracteristic($data);
        }


    }


    protected function disableFiltersByBrand($data) {
        if (!isset($data['brand']))
            return array();
        $access_filters = $this->getAccessFilters();


        $disable_filters = array();
        $cOb = $this->getCacheObject();

        //get_bp_by_array
        $brands_product = $cOb->get_bp_by_array($data['brand']);


        foreach ($access_filters as $filter) {

            foreach ($filter->values as $id => $value) {

                $has_product = count(array_intersect($brands_product, $cOb->getProductsByInfoId($id)));

                if ($has_product == 0) {
                    $disable_filters[$filter->name][] = $id;
                }
            }
        }

        //$disable_filters["brand"] = $disable_filters;
        // dd($disable_filters);
        return $disable_filters;
    }


    public function disableFiltersByHaracteristic($q,$data,$cur) {
      //  d($data);
        $dbb = $this->disableFiltersByBrand($data);


        $cOb = $this->getCacheObject();

        //$product_brands_array = array_unique($q->lists("brand_id")->toArray());

        $product_id_array = array_unique($q->lists("id")->toArray());


        $brands = []; //$this->getAccessBrandsIds();
        $result_brands = $brands;// array_diff($brands, $product_brands_array);
        $presult = array();
        //для бренда
        foreach ($result_brands as $r) {
            $presult[] = $r;
        }

        

        $phf = $this->getAccessFiltersWithoutCheckValues($data);
        // $phf - все чекбоксы без выбранных
        
        
//        d("data");
//        d($data);
//        d("phf");
//        d($phf);

        $disable_filters = array();

        foreach ($phf as $filter_name => $filter) {
          //  d($filter);
            foreach ($filter as $id => $value) {
                $filtered_ids = $cOb->getProductsByInfoId($value);
                $has_product = count(array_intersect($product_id_array, $filtered_ids));

                if ($has_product == 0) {
                    $disable_filters[$filter_name][] = $value;
                }
            }
        }


       // d($disable_filters);


        $result["brand"] = $presult;

        $result = array_merge($result,$disable_filters);


        $new_result_arr = array();
        $new_result_arr = array_merge($dbb,$result);
        $k_array = array();


        //d($dbb);
       // d($result);

        foreach($new_result_arr as $k=>$v){

            $brand = isset($dbb[$k])?$dbb[$k]:array();
            $har = isset($result[$k])?$result[$k]:array();
            $new_result_arr[$k] = array_unique(array_merge($brand,$har));

        }
        //d($new_result_arr);

        //$in = array_intersect($new_result_arr, $data);
       //$cur_ph = PH::find(intval($cur));
       //if(isset($new_result_arr[$cur_ph->name])) unset($new_result_arr[$cur_ph->name]);
        return $new_result_arr;



    }
    //Удаляет из доступных фильтов уже выбранные значения
    private function getAccessFiltersWithoutCheckValues($values) {


        foreach($values as $k=>&$param) {

            foreach($param as &$value) {
                $value = intval($value);
            }

        }
     //   d($values);

       // d("getAccessFiltersWithoutCheckValues");
      //  d($values);

        $access_filters = $this->getAccessFiltersArrayFormat();
       // dd($access_filters);
        if(count($values) == 1){

        }
        foreach($values  as $k=>$v) {
            if( isset($access_filters[$k]) ){
                $access_filters[$k] = array_diff ($access_filters[$k], $v);

            }

        }
//        foreach($access_filters as $k=>$filter) {
//            if( isset($values[$k]) ) {
//
//
//               // d($values[$k]);
//               // d($access_filters[$k]);
//               // $access_filters[$k] = array_diff($access_filters[$k],$values[$k]);
//               // d($access_filters[$k]);
//
//
//            }
//
//        }
//       // d("end");
        return $access_filters;

    }

    public static function getByAlias($alias) {
        $q = self::where("alias",$alias);
        if( $q->count() == 0) return null;

        return $q->get()->first();

    }

    //Алиас для типа в базе не хранится
    public function getCategotyByAlias($alias) {

        $categories =  $this->getAccessFiltersValues("type")->get();
        foreach($categories as $k=>$v) {
            if(  Helper::translit($v->value) == $alias ) {
                return $v;

            }
        }
        return null;

    }

    //Изначально была рандомная картинка, теперь первая
    public function getRandomImgByBrand($brand_id) {

        $products = Product::where("catalog_id",$this->id)->where("brand_id",$brand_id)->orderBy("id","asc")->get()->first();
        return $products->img;
        //return Product::where("")

    }
    //Изначально была рандомная картинка, теперь первая
    public function getCatalogImg() {

        $products = Product::where("catalog_id", $this->id)->orderBy("id", "asc")->get()->first();
        return $products->img;
        //return Product::where("")
    }

    /* Чтобы в разделе Распродажи у каждого подраздела была картинка-обложка
     * первого товара среди прочих, которые входят в этот подраздел.
     * ----- code by George Bramus
     */
    public function getCatalogImgForSales() {
        $products = Product::where("catalog_id", $this->id)
                           ->where("sales", "1")
                           // ->orderBy("id", "asc")
                           ->orderBy("cost_trade", "asc")
                           ->get()
                           ->first();
        return $products->img;
        //return Product::where("")
    }


    public function getCacheObject() {
        return new Cache_catalog($this);
    }

    public function prepareFilterFormat($filter_get){
         $filter_get['filter'] = json_decode($filter_get['filter']);
        $filter = array();
        foreach ($filter_get['filter'] as $value) {
            $name = str_replace("[]", "", $value->name);
            $filter[$name][] = $value->value;
        }
        $filter_get['filter'] = $filter;
        return $filter_get;
    }
    
    /**
    * Возвращает идентификаторы найденых продуктов удовлетворяющих условиям в фильтре 
    * Если $filtersToDisable установлен в значение отличное от false, то в эту переменную устанавлвается массив фильтров которые необходимо выключить
    */ 
    public function getCountProductByFilterValues($filter_get, &$filtersToDisable = false) {
        $ob = $this->getCacheObject();
        
        if(isset($filter_get['filter'])) {
        $filter = $filter_get['filter'];
        }
        else {
           $filter =  $filter_get;
        }
        $filter_orig = $filter;

       // d($filter["brand"]);
        $brands = !empty($filter["brand"]) ? $ob->get_bp_by_array($filter["brand"]) : array();

        //Чекбоксы бренда
        $isEmptyBcb = empty($filter["brand"]);
        unset($filter["brand"]);

        //Чекбоксы характеристик
        $isEmptyHcb = empty($filter);

        $data = array();
        foreach ($filter as $k => $v) {
            $data[$k] = $ob->get_hp_by_array($v);
        }
        
        if(false !== $filtersToDisable) {
            //$phf = $this->getAccessFiltersWithoutCheckValues($filter);
            $phf = $this->getAccessFiltersWithoutCheckValues(array());
            $disable_filters = array();
            $all_brand_ids_without_checked = array_diff($this->getAccessBrandsIds(), !empty($filter_orig["brand"]) ?  $filter_orig["brand"] : array());
            $phf['brand'] = $all_brand_ids_without_checked;
            if(!empty($brands)) {
                $data['brand'] = $brands;
            }
            //var_dump($filter_orig, $data);
            
            // чекбокс выключается если НЕ входит хоть в один из установленных
            foreach($phf as $filter_type => $filter_values) {
                foreach($filter_values as $filter_value) {
                    if($filter_type == 'brand') {
                        $check_ids = $ob->get_bp_by_array(array($filter_value)); // все id для одного бренда
                    } else {
                        $check_ids = $ob->get_hp_by_array(array($filter_value)); // все id для одной характеристики
                    }
                    
                    $found = 0;
                    foreach($data as $check_filter_type => $group_ids) {
                        if($check_filter_type == $filter_type || count(array_intersect($check_ids, $group_ids)) > 0) {
                            $found++;
                        }
                    }
                    
                    if($found < count($data)) {
                        $disable_filters[$filter_type][] = $filter_value;
                    }
                }
            }
            unset($data['brand']);
            
            //var_dump($disable_filters);
            $filtersToDisable = $disable_filters;
        }

        $hp = !empty($data) ? array_shift($data) : array();

        foreach ($data as $k => $v) {
            $hp = array_intersect($v, $hp);
        }
        
        return $this->prepareCountResult($hp, $brands,$isEmptyBcb,$isEmptyHcb);
    }
    
    private function prepareCountResult($hp, $brands,$isEmptyBcb,$isEmptyHcb) {
        //Наборы чекбоксов для брендов и харакеристик не пустые
        if (!$isEmptyBcb && !$isEmptyHcb) {
            return array_intersect($hp, $brands);
        }

        if ($isEmptyBcb && !$isEmptyHcb) {
            return $hp;
        }
        if (!$isEmptyBcb && $isEmptyHcb) {

            return $brands;
        }
    }

    public static function boot() {
        parent::boot();

        static::created(function($catalog) {

            $catalog->alias = Helper::translit($catalog->name);
            $catalog->save();




        });

    }

}
