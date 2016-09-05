<?php
//характеристика товара
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;


class PH extends Model
{
  protected $table = 'product_haracteristic';
  protected $fillable = ['name','value','color_code','catalogs'];
  public function phr() {
      return $this->hasMany("App\PHR","haracteristic_id","id");
  }
  static public function getPhByName($name) {
      
  }
  
  public function getValue() {
      return $this->value;
  }
  
  //Для каждого каталога определены свойства товара. Иногда они совпадают, например свойство
  // "цвет" есть у товаров из разных каталогов. 
  // Каталоги, к которым имеет отношение свойство хранятся в поле
  // catalogs в виде < каталог 1>|< каталог 2>
  // Определить имеет ли свойство отношение к каталог можно запросом типа like %<id-каталога>%
  public function add_catalog($catalog_id) {
      $catalogs = explode("|",$this->catalogs);

      $catalogs[] = "<$catalog_id>";
      $catalogs = array_unique($catalogs);
      
      
      $this->catalogs = implode("|",$catalogs);
      $this->save();
      
  }
      public function getCatalogs() {
        $catalogs = explode("|", $this->catalogs);
        
        foreach ($catalogs as $k => &$cat) {
            
            if ($cat == "") {
                unset($catalogs[$k]);
                continue;
            }
            $cat = (int) preg_replace('/[^0-9]/', '', $cat);
            
        }


        if((count($catalogs) == 1) && $catalogs[1] == 0) return array();
        return $catalogs;
    }
    public function catalogCount() {
        
        return count($this->getCatalogs());
    }
    
    public static function get_with_catalog_count($count) {
        
        $count_arr = array();
        $all = SELF::all();
        foreach($all as $ph){
            $c = $ph->catalogCount();
       
            
            if($c == $count ){
  
                $count_arr[] = $ph;
            }
            
        }
        return $count_arr;
        
    }

//Проверяет есть ли id каталога в списке достпуных каталог для характеристики
    public function hasAccessCatalog($catalog_id) {
        $catalogs = explode("|", $this->catalogs);
        if (in_array("<$catalog_id>", $catalogs)) {
            foreach ($catalogs as $k => $v) {
                if ($v == "<$catalog_id>") {
                    return true;
                }
            }
        }
        return false;
    }
//Удаляет каталог из списка достпных по id
    public function delete_catalog($catalog_id) {
        $catalogs = explode("|", $this->catalogs);
        if (in_array("<$catalog_id>", $catalogs)) {
            foreach ($catalogs as $k => $v) {
                if ($v == "<$catalog_id>") {
                    unset($catalogs[$k]);
                }
            }
        }

        $this->catalogs = implode("|", $catalogs);
        $this->save();
    }

    //Получение доступных каталогов для характеристики
  public static function getCatalogsByName($name) {
      $catalogs =  self::where("name",$name)->lists("catalogs");
      $new_arr = array();
      foreach($catalogs as $cat) {
          $cat = new \Illuminate\Database\Eloquent\Collection(explode("|",$cat));
          $cat->each(function($item) use(&$new_arr) {
              if( !empty($item) ){
                  $int = (int) preg_replace('/[^0-9]/', '', $item);
                  $new_arr[] = $int;

              }


            //$q->where('name', $name);
        });
        //  d($cat);
         // $new_arr = $new_arr->merge(explode("|",$cat));
          
      }

      return array_unique($new_arr);
      
      
      
  }
  
    //Получение доступных каталогов для характеристики по id характеристики
  public static function getCatalogsByHid($hid) {
      $catalog =  self::find($hid);
      $new_arr = array();

          $cat = new \Illuminate\Database\Eloquent\Collection(explode("|",$catalog->catalogs));
          $cat->each(function($item) use(&$new_arr) {
              if( !empty($item) ){
                  $int = (int) preg_replace('/[^0-9]/', '', $item);
                  $new_arr[] = $int;

              }


            //$q->where('name', $name);
        });
        //  d($cat);
         // $new_arr = $new_arr->merge(explode("|",$cat));
          
      

      return array_unique($new_arr);
      
      
      
  }
  //Поиск характеристики по имени и значению
  public static function findPH($name,$value) {
      $q = self::where("name",$name)->where("value",$value);
      if($q->count() > 0) return $q->get()->first();
      
      return null;
      
  }
  
//  public static function find_by_redis($param,$value) {
//      $redis = Redis::connection();
//      $find_elems = array();
//      $ph_keys = $redis->keys('ph:*');
//               foreach($ph_keys as $k) {
//             $elem = new \stdClass();
//             $elem->$param = $value;
//             $string=  json_encode($elem);
//             $string = str_replace("{","",$string);
//             $string = str_replace("}","",$string);
//             //var_dump($string);
//             
//             $ph = $redis->get($k);
//             if(strpos($ph,$string)) {
//                 $find_elems[] = json_decode($ph);
//             }
//            // d(  strpos($ph,$string) );
//             
//         }
//         return $find_elems;
//  }
}
