<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Yangqi\Htmldom\Htmldom;
use Intervention\Image\ImageManager;

class ParseXmlFile {

    public $file;
    public $name_arr;
    public $attr_arr;
    public $step = 50;

    public function __construct($file = null) {
        session_start();
        if (!is_null($file)) {
            $_SESSION["file_xml"] = $file;
            $_SESSION["update_count"] = 0;
            $_SESSION["new_count"] = 0;
        }

        $this->file = $_SESSION["file_xml"];
        if(isset($_SESSION["name_arr"]) && isset($_SESSION["attr_arr"])) {
            $this->name_arr = $_SESSION["name_arr"];
            $this->attr_arr = $_SESSION["attr_arr"];
        }
        else {
            $this->load_xml();
        }

    }

    public function clear_session() {
        foreach($_SESSION as $k=>$v) {
            unset($_SESSION[$k]);
        }

    }

    public function load_xml() {
        $html = new Htmldom($this->file);

        $elements = $html->find("Element");
        unset($html);
        $products_name = array();
        $products_attr = array();




        foreach ($elements as $k => $el) {

            $product = new \stdClass();
            $products_name[] = $el->innertext;
            $products_attr[] = $el->attr;
            unset($elements[$k]);
        }
        unset($elements);
        $this->name_arr = $products_name;
        $this->attr_arr = $products_attr;
        unset($products_name);
        unset($products_attr);
        $_SESSION["name_arr"] = $this->name_arr;
        $_SESSION["attr_arr"] = $this->attr_arr;
        




    }

    public function update($step) {
  
        $this->strip_data($step);
        $products = array();

  

        foreach ($this->name_arr as $k => $name) {
            $attr = $this->attr_arr[$k];
            $products[] = $this->xml_product_to_base($name, $attr);
        }
        return $products;
    }

    //Удаление из массиово эелементов, которые уже загруженны
    public function strip_data($step) {

        $offset = $step * $this->step;

        $this->name_arr = array_slice($this->name_arr, $offset, $this->step + 1);
        $this->attr_arr = array_slice($this->attr_arr, $offset, $this->step + 1);
    }
    
    public function get_new_count() {
        return $_SESSION["new_count"];
    }
    public function get_update_count() {
        return $_SESSION["update_count"];
    }

    private function xml_product_to_base($name, $attr) {

       // if(!isset($_SESSION["update_count"])) $_SESSION["update_count"] = 0;
        //if(!isset($_SESSION["new_count"])) $_SESSION["new_count"] = 0;
        $pr = Product::where("code_1c", $attr["kod"])->get();
        $attf = $this->format_attr($attr);
        $is_new = 0;
        $product_attr = array();
        if ($pr->count() > 0) {

            $product = $pr->first()->id;
            $_SESSION["update_count"] = $_SESSION["update_count"]+1;
            $product_attr["catalog_id"] = $pr->first()->catalog_id;

            
        } else {

            //$product = \Illuminate\Support\Facades\DB::table("product")->max('id') + 1;
            
            $pr_new = new Product();
            $pr_new->catalog_id = 10000;
             
//Эта безумная цифра нужна, чтобы определять товары из текущей сессии
//для работы раздела "Товары без каталога"
            $pr_new->moderated = 0;
            $pr_new->code_1c = $attr["kod"];
            $pr_new->save();
            $product = $pr_new->id;
            
            $_SESSION["new_count"] = $_SESSION["new_count"]+1;
            $is_new = 1;
            $product_attr["catalog_id"] = 10000;
        }
        //При парсинге не кешируем характеристики

        
        $save_ph = $this->get_ph_for_save($attf, $product_attr["catalog_id"]);
        foreach ($save_ph as $sph) {

            $new_phr = PHR::firstOrNew(
                            array(
                                "product_id" => $product,
                                "name" => $sph->name
                            )
            );
            $new_phr->haracteristic_id = $sph->id;
            $new_phr->save();
        }

        $product_attr["name"] = $name;
        $product_attr["cost"] = $attr['cenaopt'];
        $product_attr["cost_trade"] = $attr['cenaroz'];

        $list = explode("!#", $attr['opisanie']);

        foreach ($list as &$l) {
            $l = "<li>$l</li>";
        }

        $product_attr["haracteristic"] = "<ul>" . implode("", $list) . "</ul>";
        $product_attr["viewcost"] = (!isset($pr->viewcost))?1:$pr->viewcost;
        $product_attr["viewcost_nonauth"] = (!isset($pr->viewcost_nonauth))?1:$pr->viewcost_nonauth;
        $product_attr["country"] = $attr['proizvoditel'];
        $product_attr["img"] = $attr['foto1'];
        $product_attr["img2"] = $attr['foto2'];
        $product_attr["img3"] = $attr['foto3'];
        $product_attr["img4"] = $attr['foto4'];
        $product_attr["analog"] = $attr['analog'];
        
        $photo = array();
        $photo[2] = $attr['foto2'];
        $photo[3] = $attr['foto3'];
        $photo[4] = $attr['foto4'];
        $manager = new ImageManager(array('driver' => 'imagick'));
        
        foreach($photo as $k=>$p) {
            if($p == "") continue;
            $path = public_path()."/uploads/product/img1/".$p;
            
            if(\Illuminate\Support\Facades\File::exists($path)) {
                                      
                $path2 = public_path()."/uploads/product/img$k/".$p;
                \Illuminate\Support\Facades\File::move($path,$path2);
                
            }
            
        }
        
        $product_attr["article"] = $attr['artikul'];
        $name = ucfirst(mb_strtolower(preg_replace("/[^a-zA-Z+-]/", "", $attr['roditel'])));
        $brand = Brand::getByName($name);
        if(is_null($brand)){
            $brand = new Brand( array( "title" => $name ));
            $brand->save();
        }
        if( $product_attr["catalog_id"] !== 10000)
        {
            $brand->add_catalog($product_attr["catalog_id"]);
        }
        $product_attr["brand_id"] = $brand->id;
        $product_attr["alias"] = Jobs\Helper::translit($product_attr["name"]);

        $this->product_update($product,$product_attr);
        return $product_attr["name"];

       // var_dump($_SESSION["update_count"]);
    }
    
    public function product_update($id,$product){
                \Illuminate\Support\Facades\DB::table("product")->where('id', $id)
                ->update($product);
    }

    private function format_attr($attr) {
        $phs = array();
        $main_ph = array(
            "material" => "material",
            "cvet" => "color",
            "pribor" => "device",
            "razmer" => "width",
            "shirina" => "width",
            "stil" => "style",
            "tip" => "type"
        );
        foreach ($attr as $k => $v) {
            if (isset($main_ph[$k])) {
                $phs[$main_ph[$k]] = $v;
            }
        };
        return $phs;
    }

    public function getSize() {
        return count($this->name_arr);
    }

    public function get_parts() {
        if(count($this->name_arr) < $this->step ) return 1;
        return intval(ceil(count($this->name_arr) / $this->step));
        
    }

    private function get_ph_for_save($attr, $catalog_id) {
        $ph_save_ids = array();

        foreach ($attr as $k => $v) {
            if($v !== '')
            {
                $ph = PH::where("name", $k)->where("value", 'like', "$v");
                if ($ph->count() == 0) {
                    $not_have_ph[$k][] = $v;
                    $not_have_ph[$k] = array_unique($not_have_ph[$k]);

                        $new_ph = new PH();
                        $new_ph->name = $k;
                        $new_ph->value = $v;
                        $new_ph->save();
                        if($catalog_id !== 10000) {
                            $new_ph->add_catalog($catalog_id);
                        }
                        $ph_save_ids[] = $new_ph;


                } else {
                    $ph_obj = $ph->first();
                    if($catalog_id !== 10000) {
                        $ph_obj->add_catalog($catalog_id);
                    }
                    $ph_save_ids[] = $ph_obj;

                }
            }
        }
        return $ph_save_ids;
    }

}
