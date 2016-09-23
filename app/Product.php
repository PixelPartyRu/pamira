<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Jobs\Helper;
use App\PH;
use App\PHR;
use App\Product;
use App\User;
// use App\Margin;

class Product extends Model
{
  protected $table = 'product';

    protected $fillable = ['*'];
    protected $guarded = array("id");
    //Кеширование характереристик товара при сохранении
    public $is_cached = 1;





    //параметры товара
    static $PRODUCT_HARACTERISTIC = array(
            "material" => "Материал",
            "wardrobe" => "Шкаф для монтажа (мм)",
            "color" => "Цвет",
            "device" => "Прибор",
            "size" => "Размер",
            "style" => "Стиль",
            "type" => "Тип",
            "width" => "Ширина (мм)",
            "panel_material" => "Материал панели управления"

        );

        public function __construct() {
        parent::__construct();



        }



    public static  function getProductHaracteristic() {
        return self::$PRODUCT_HARACTERISTIC;

    }
  public function getHaracteristicValue($haracteristic) {
      if(is_null($this->getPhByName($haracteristic))) return null;
      return $this->getPhByName($haracteristic)->value;

  }

  public static function getByName($name) {

      $p = self::where("name",$name);
      if($p->count() > 0) return $p->get();

      return null;

  }
  public static function main_page_product() {

     return self::where("moderated",1)->where('in_main_page',1)->orderBy("in_main_page","desc")->paginate(9)/*->orderBy("sales_leader","desc")/**/;
  }
    public function migration_info() {

        return $this->hasOne("App\Migration_info", "new_id", "id");
    }

    public function catalog() {

      return $this->belongsTo("App\Catalog","catalog_id","id");
  }
  public function brand() {
      return $this->hasOne("App\Brand","id","brand_id");

  }

public static function query() {
    return parent::query()->where("deleted","=",0);
}
  public function scopeFilter($query, $params) {
      unset($params['catalog']);
//            $qw = $this->phr()->whereHas("ph",function($q) use($name){
//
//          $q->where('name', $name);
//
//      });
//
      unset($params['brand']);
     // $p_ids = $query->lists("id");


        $h_array = array();

        foreach ($params as $param => $array) {

            if (is_array($array)) {
                $h_array = array_merge($h_array, $array);
            } else {

                $h_array[] = $array;
            }
        }
        //d($h_array);
        if( empty($h_array) ) {return $query;}
        $qw = $query->whereHas("phr.ph", function($q) use($h_array){
            $q->whereIn("haracteristic_id", $h_array);
        });


        return $qw;

    }

    public function getMargin( $type = 'wholesale' )
    {
        if(User::getLoginUserType() == "dealer")
        {
            $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
            $margin = \App\Margin::where('user_id', $dealer->id)->where('default', 1)->where('type', $type)->first();

            if(!empty($margin))
            {
                $brand = $brand_id = $this->brand;
                if(!empty($this->brand))
                {
                    $brand_id = $this->brand->id;
                    $marginBrand = \App\MarginBrand::where('brand_id', $brand_id)->where('margin_id', $margin->id)->first();
                    if(!empty($marginBrand))
                    {
                        return $marginBrand->margin;
                    }
                }
            }
        }
        return 0;
    }

    public function getCostWithMargin($opt = false) {
        $cost = ($opt) ? $this->cost : $this->cost_trade;

        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $margin = \App\Margin::where('user_id', $dealer->id)->where('default', 1)->where('type', $type)->first();

        if(!empty($margin)){
            $margin_name = substr($margin->name, -3);
        }
        else{
            $margin_name = "";
        }

        iff(!empty($margin_name) && $margin_name=="rev"){
            return ceil_dec($cost + ($cost / 100 * $this->getMargin(( $opt ? 'wholesale' : 'retail' ))));
        }
        else{
            return ceil_dec($cost + ($cost / 100 * $this->getMargin(( $opt ? 'retail' : 'wholesale' ))));

        }

    }

    public static function setCostFormat(&$data) {

        foreach($data as $k=>&$v) {

            $v->cost_trade = number_format(  round($v->getCostWithMargin() )  ,  0  ,  ','  ,  ' '  );

        }

    }
    public function getFormatCost() {
        return number_format(  round($this->getCostWithMargin() )  ,  0  ,  ','  ,  ' '  );
    }
    public function getRoundCost() {
        return round( $this->getCostWithMargin() );
    }

    public static function getByAlias($alias) {
        $q = self::where("alias",$alias);
        if($q->count() == 0) return null;
        return $q->get()->first();

    }

    public static function search($world) {

        //По наименованию
        $words = explode(" ", $world);
       // var_dump($words);
       // var_dump($words);
        $collection  = new Collection;

        $by_name = self::query();
        $by_haracteristic = self::query();
        $by_article = self::query();

        foreach($words as $w) {
            if( strlen($w) == 0 ) {
                continue;
            }
            $by_name = $by_name->where('name', 'like', "%$w%");
            $by_haracteristic = $by_haracteristic->where('haracteristic', 'like', "%$w%");
            $by_article = $by_article->where('article', 'like', "%$w%");
            //$collection = $collection->merge($by_name)->merge($by_haracteristic)->merge($by_article);
        }

        $by_name = $by_name->get();
        $by_haracteristic = $by_haracteristic->get();
        $by_article = $by_article->get();
        $collection = $collection->merge($by_name)->merge($by_haracteristic)->merge($by_article);
        $products = $collection->unique();
        return $products;

    }
    //Сгенерировать алиас для раздела бренды
    public function getPathAliasForBrand() {
        $category = $this->getPhByName("type");
        $category = is_null($category)?"_":Helper::translit($category->value);
        $catalog = $this->catalog->alias;
        $brand = $this->brand->alias;
        return "/brand/$brand/$catalog/$category/".$this->alias;

    }
    //Для каталога
    public function getPathAliasForCatalog() {
        if(!is_null($this->catalog)) {
        $catalog = $this->catalog->alias;
        }
        else {
          //  d($this->id);

           $catalog = "_";
        }
        return "/product_catalog/get/".$catalog."/".$this->alias;

    }

    public function getImageArr() {
        $product_img = array();
        $product_img[] = $this->img;
        $product_img[] = $this->img2;
        $product_img[] = $this->img3;
        $product_img[] = $this->img4;
        return $product_img;
    }


    //При сохранении товара из админки
    //Проверка новых достпуных характеристик для каталога
    // + кеширование

    public static function product_admin_update($old,$catalog_id = null) {


        //d($old);
        //Cache_catalog обновления кеша для фильтра
        $catalog_id = is_null($catalog_id)?intval($old["catalog_id"]):$catalog_id;
        $catalog = Catalog::find($catalog_id);
        $cc_ob = $catalog->getCacheObject();
        $new = Product::find( $old["id"] );
        $new->catalog_id = $catalog_id;
        $new_phs = $new->getPhs();
        foreach ($old["phs"] as $k => $ph) {


            $cc_ob->update_cache($ph->id, "h");
        }
        foreach ($new_phs as $k => $new_ph) {
          //  d($new_ph);
           // d($new_ph->hasAccessCatalog($catalog->id));

           if(!$new_ph->hasAccessCatalog($new->catalog_id)) {
                $new_ph->add_catalog($new->catalog_id);

            }
            $cc_ob->update_cache($new_ph->id, "h");
        }

       // if($new->brand_id !== $old["brand_id"] ) {


            if(!$new->brand->hasAccessCatalog($new->catalog_id)) {
                d($new->brand->title);
                d($new->brand->hasAccessCatalog($new->catalog_id));
                $new->brand->add_catalog($new->catalog_id);

            }
            $cc_ob->update_cache($new->brand_id,"b");
            $cc_ob->update_cache($old["brand_id"],"b");



      //  }

    }

    public function caching() {

    }

/*------------------------------------------------------*/
 /*
 *  РАБОТА С ХАРАКТЕРИСТИКАМИ ТОВАРА
 */

  public function phr() {
      return $this->hasMany("App\PHR","product_id","id");
  }

   /*
    *  Получение значения характеристики товара по её названию: $product->getPhByName('color')
    */

    public function getPhByName($name) {



      foreach($this->phr as $k=>$v) {
          if(is_null($v->ph))              continue;
          $catalogs = $v->ph->getCatalogs();
          if($v->ph->name == $name && in_array($this->catalog_id, $catalogs)){
              return $v->ph;
          }


      }


      return null;
  }
     /*
     *  Получение объекта связи товара и характеристики по имени PHR: $product->getPhrByName('color')
     */

    public function getPhrByName($name) {
        $qw = $this->phr()->whereHas("ph", function($q) use($name) {

            $q->where('name', $name);
        });
        return $qw->count() == 0 ? null : $qw->get()->first();
    }

     /*
     *  Привязка характеристи к товаро по id PH
     */
  public function savePH($ph_id,$cached = 1) {


      $ph = PH::find($ph_id);
        $phr = $this->getPhrByName($ph->name);
        if (!is_null($phr)) {
            $phr->delete();
        }

        $new_phr = PHR::create([
                    "product_id" => $this->id,
                    "haracteristic_id" => intval($ph_id),
            "name" => $ph->name,

        ]);


        $new_phr->save();
        if($cached == 1){

          $new_phr->caching();
        }

    }



    public function saveParsePh($ph){


        $new_phr = PHR::firstOrCreate(
                array(
                    "product_id" => $this->id,

                    "name" => $ph->name
                )
        );
        \Illuminate\Support\Facades\DB::table("product_haracteristic_relation")->where('id', $new_phr->id)
            ->update(array('haracteristic_id' => $ph->id));


        //"haracteristic_id" => intval($ph->id),
    }


    /*
     *  Возвращает массив всех характеристик товара( объектов PH).
     */
    public function getPHs() {

        $phs = array();
        foreach($this->phr as $phr) {
            //d($phr);
            if(!is_null($phr->ph)) {
           $phs[$phr->ph->id] = $phr->ph;
            }
        }
        return $phs;



    }
    public function getPHsIDs() {
              $phs = array();
        foreach ($this->phr as $phr) {
            //d($phr);
            if (!is_null($phr->ph)) {
                $phs[] = $phr->ph->id;
            }
        }
        return $phs;
    }
        public function getPHsNames() {

        $phs = array();
        foreach($this->phr as $phr) {
            //d($phr);
            if(!is_null($phr->ph)) {
           $phs[$phr->ph->name] = $phr->ph->value;
            }
        }
        return $phs;



    }
            public function getPHsNamesKeys() {

        $phs = array();
        foreach($this->phr as $phr) {
            //d($phr);
            if(!is_null($phr->ph)) {
           $phs[] = $phr->ph->name;
            }
        }
        return $phs;



    }

    public function delete_event() {
        $phrs = $this->phr;
        foreach($this->phr as $phr) {
            $phr->delete();
        }
    }

    public function update_event() {
        $c_ob = $this->catalog->getCacheObject();
        $c_ob->update_cache($this->brand_id,"b");
    }

 /*******************************************************************/

public function uniqPhCatalog() {
    $ph = $this->getPHs();
    $cat_array = array();
    foreach($ph as $p) {
        if(!empty($p->getCatalogs())) {
        $cat_array[] = $p->getCatalogs();
        }

    }

    $inter = array_shift($cat_array);
    foreach($cat_array as $ca) {
       $inter = array_intersect($ca, $inter);
    }

    return $inter;

}

public static function get_sales_catalogs() {
    $products = self::query()->where("sales", 1)->get();
        $catalogs = array();
        foreach ($products as $p) {
            $catalogs[] = $p->catalog_id;
        }
        $catalogs = array_unique($catalogs);
        foreach ($catalogs as $k => $v) {

            if ($v != 0) {
                $catalogs[$k] = Catalog::find($v);
            } else {
                unset($catalogs[$k]);
            }
        }
        return $catalogs;
    }

public static function get_product_width_sales($catalog_id) {
    return self::query()->where("sales",1)->where("catalog_id",$catalog_id)->get();
}

public function getPHWithNumberCat($num) {
        $ph = $this->getPHs();
        $cat_array = array();
        foreach ($ph as $p) {
            if ( count(($p->getCatalogs())) == $num ) {
                $cat_array[] = $p;
            }
        }
        return $cat_array;
    }


    public function is_sales_leader() {
        return $this->sales_leader==1?"Да":"Нет";
    }
        public function show_cost() {
        return $this->viewcost==1?"Да":"Нет";
    }
            public function has_img() {
        return $this->img !== "" ? "Да" : "Нет";
    }

    public function catalog_for_admin_list() {

        $type = $this->getPhByName("type");
        $str = !is_null($this->catalog) ? $this->catalog->name : "";
        $str.= $str!==""?" / ":"";
        $str.=!is_null($type) ? $type->value : "";

        return $str;
    }

    public static function boot() {
        parent::boot();

        static::created(function($product) {

            $product->alias = Helper::translit($product->name);
            $product->save();
        });
        static::updated(function($product) {



        });


        static::deleting(function($product) {

            $product->delete_event();
        });
    }

}
