<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
/**
 * Description of Migration_info
 *
 * @author Alina2
 */
class Migration_info extends Model {
    protected $table = 'migration_info';
  
    protected $fillable = ['old_id','new_id','type'];
    protected $guarded = array("id");
    
    public static function get_products_by_podcat1($id) {
        
                $db = DB::connection('mig');
        return $db->table('pamira_tovar')->where("id_podcat1",$id)->lists("id");
    }
    
    
    public static function get_old_products_by_hid($name,$id) {
        
        $params = array();
        $elem = new \stdClass();
        $elem->table = "pamira_color";
        $elem->key = "id_color_";
        $params["color"] = $elem;

        $elem = new \stdClass();
        $elem->table = "pamira_pribor";
        $elem->key = "id_pribor";
        $params["device"] = $elem;


        $elem = new \stdClass();
        $elem->table = "pamira_style";
        $elem->key = "id_style";
        $params["style"] = $elem;


        $elem = new \stdClass();
        $elem->table = "pamira_type";
        $elem->key = "id_type";
        $params["type"] = $elem;

        $elem = new \stdClass();
        $elem->table = "pamira_material";
        $elem->key = "id_material";
        $params["material"] = $elem;

        $elem = new \stdClass();
        $elem->table = "pamira_size";
        $elem->key = "id_size";
        $params["size"] = $elem;

        $elem = new \stdClass();
        $elem->table = "pamira_base";
        $elem->key = "id_base";
        $params["base"] = $elem;
        
                $db = DB::connection('mig');
        $key = $params[$name]->key;
        return $db->table('pamira_tovar')->where($key,$id)->lists("id");

    }
    
    
    public function product() {
        return $this->belongsTo("App\Product","new_id","id");
    }
    public function brand() {
        return $this->belongsTo("App\Brand", "new_id", "id");
    }
    
    public static function getBrandByOldId($id){
        return self::where("old_id",$id)->where("type","brand")->get()->first()->brand;
        
    }

    public static function getMigratedProduct() {
       return self::where("type","product")->get();
        
        
    }
    public static function getMigratedProductCount() {
        return self::where("type", "product")->count();
    }

    public function get_old_product() {
        $db = DB::connection('mig');
        return new OldProduct($this->old_id);
        
    }
    
    public function get_old_product_clean() {
       $db = DB::connection('mig'); 
      return $db->table('pamira_tovar')->where("id",$this->old_id)->get();
    }
    public function get_old_product_clean_ob() {
       $db = DB::connection('mig'); 
      return $db->table('pamira_tovar')->where("id",$this->old_id)->get()[0];
    }
    
    public function get_old_ph($param_name) {
        $old = $this->get_old_product();
        $attrs = $old->attrs;
        if(isset($attrs[$param_name])){
            return $attrs[$param_name];
        }
        return null;
    }
    public function get_old_ph_all() {
        $old = $this->get_old_product();
        return $old->attrs;
    }
    
    public function get_old_catalogs() {
        $db = DB::connection('mig'); 
        $old = $this->get_old_product_clean_ob();
       // d($old);
        $ob = new \stdClass();
        $ob->id_podcat1 =  $old->id_podcat1!=0?$db->table('pamira_podcatalog1')->where("id", $old->id_podcat1)->get()[0]->name_rus:"Нет";
        $ob->id_podcat2 =  $old->id_podcat2!=0?$db->table('pamira_podcatalog2')->where("id", $old->id_podcat2)->get()[0]->name_rus:"Нет";
        $ob->id_podcat3 =  $old->id_podcat3!=0?$db->table('pamira_podcatalog3')->where("id", $old->id_podcat3)->get()[0]->name_rus:"Нет";
        $ob->id_cat =  $old->id_cat!=0?$db->table('pamira_catalog')->where("id", $old->id_cat)->get()[0]->name:"Нет";
        $ob->ph = $this->get_old_ph_all();
        $old_f =  $this->get_old_product();
        $ob->ph["brand"] = $old_f->brand;
        $har = Product::getProductHaracteristic();
       // d($har);
        $new_ph = array();
        foreach($ob->ph as $k=>$v) {
            if(isset($har[$k])) {
            $new_ph[$har[$k]] = $v;
            }
            else {
                if ($k == "base") {
                    $new_ph["База"] = $v;
                }
                if ($k == "brand") {
                    $new_ph["Бренд"] = is_object($v)?$v->name:"Нет";
                }

                
            }
        };
        
        $ob->ph = $new_ph;

        
        
        
        
        
        return $ob;
        
    }
    //put your code here
}

class OldProduct {
    public $product;
    public $old_catalog;
    public $id_brand;
    public $brand;
    public $attrs;
    public function __construct($id) {
        $attrs = array();
        $db = DB::connection('mig');
        
        
       // d($db->table('pamira_tovar')->where("id",$id)->get());
        $pq = $db->table('pamira_tovar')->where("id",$id)->get();
       // dd($pq);
       /* if(!isset($pq[0])) {
            d($pq);
        }*/
        $this->product = isset($pq[0])?$pq[0]:null;
        $cq = $db->table('pamira_catalog')->where("id",$this->product->id_cat)->get();
        
      //  dd($this->product);
        if($this->product->id_brand != 0) {
        $this->brand = $db->table('pamira_brand')->where("id",$this->product->id_brand)->get()[0];
        }
        else {
           $this->brand = 0; 
        }
        

        $this->old_catalog = isset($cq[0])?$cq[0]:null;
        $this->id_brand = $this->product->id_brand;
        if ($this->product->id_color_ > 0) {
            $this->attrs['color'] = $db->table('pamira_color')->where("id", $this->product->id_color_)->get()[0]->name_color;
        }
        if ($this->product->id_pribor > 0) {
            $this->attrs['device'] = $db->table('pamira_pribor')->where("id", $this->product->id_pribor)->get()[0]->name;
        }

        if ($this->product->id_style > 0) {
            $this->attrs['style'] = $db->table('pamira_style')->where("id", $this->product->id_style)->get()[0]->name;
        }
        if ($this->product->id_type > 0) {
            $this->attrs['type'] = $db->table('pamira_type')->where("id", $this->product->id_type)->get()[0]->name;
        }
        if ($this->product->id_material > 0) {
            $this->attrs['material'] = $db->table('pamira_material')->where("id", $this->product->id_material)->get()[0]->name;
        }
        if ($this->product->id_size > 0) {
            $this->attrs['size'] = $db->table('pamira_size')->where("id", $this->product->id_size)->get()[0]->name;
        }
        
        if ($this->product->id_base > 0) {
            $this->attrs['base'] = $db->table('pamira_base')->where("id", $this->product->id_base)->get()[0]->name;
        }
        
        

    }
    
}
