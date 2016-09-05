<?php
/* 
 * Объект кеширования каталога 
 * 
 * 
 */

namespace App;
use Illuminate\Database\Eloquent\Model;

class Filter_cache extends Model {
    protected $table = 'filter_cache';
  
    protected $fillable = ['*'];
    protected $guarded = array("id");
    /*
     * 
     * Поля объекта - id каталога, data - список id доступных товаров объекта в формате id1|id2|id3
     * type = h|b - характеристика PH/Бренд Brand
     * info_id - id характеристики/бренда

          */
    protected $f_ob_fileds = array("catalog_id","data","type","info_id");
    
    //Добавления товара в список объекта
    public function add_product($product_id) {
        $products = explode("|",$this->data);
        $products[] = intval($product_id);
        $products = array_unique($products);
        $this->data = implode("|",$products);
        $this->save();
        
        
        
        
        
    }
    //Удаление
    public function delete_product($product_id) {
        $products = explode("|",$this->data);
        if (in_array($product_id, $products)) {
            foreach ($products as $k => $v) {
                if ($v == $product_id) {
                    unset($products[$k]);
                }
            }
        }

        $this->data = implode("|", $products);
        $this->save();
    }

    //put your code here
}
