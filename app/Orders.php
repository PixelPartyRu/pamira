<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order_product;
use App\User;
class Orders extends Model
{
  protected $table = 'orders';
  protected $fillable = array('user_id');
  
  

  //позиции в корзине дилерв
  public function products() {
      return $this->hasMany("App\Order_product","order_id","id")->orderBy('ordering');
  }
  public function customer() {
        return $this->belongsTo("App\User", "user_id", "id");
  }
  
  public function getEmailForDealer() {
      return $this->customer->region->email_for_dealer;
  }
  public function getEmailForCustomer() {
       return $this->customer->region->email_for_cutomer;
  }
  public function getCustomerType() {
      return $this->customer->type;
  }
  
  

    public function cart() {
      
  }
  
  //Добавление товара в заказ
  public static function add_position_in_cart($product_id) {
      //Ищем заказ последний, который ещё не был закрыт, он и будет текущим
     //$current_order 
      
      
  }
  public function addProduct($product_id) {
      //var_dump($this->id);
      $order_product = Order_product::addProductInfoInOrder($this->id,$product_id);
     
// $this->products->create($order_product);

      
  }
  
  public function getProductList() {
      
  }
  
  //считает количество позиций
  public function getProductsCount() {
      return $this->products->count();
  }
  //количество товаров
  public function getProductsCountAll() {
      return $this->products()->sum("count_product");
  }
  
  public function recalcTotalCost() {
      
      foreach($this->products as $k=>$product_info) {
          if(is_null($product_info->product))continue;

          if( $product_info->product_id == 0 ) continue;
         
          
          if(is_null($product_info->edit_cost) && !is_null($product_info->product)) {
          $cost = $product_info->product->cost_trade;
          }
          else {
             $cost =  $product_info->edit_cost;
          }
          if(is_null($product_info->product)){
              $cost = 0;
          }


          $product_info->total_cost   = $cost * $product_info->count_product;

          $product_info->save();
      }

      
  }
  public function getProductsSumm() {
      $this->recalcTotalCost();
      $summ = 0;
      foreach($this->products as $k=>$product_info) {
          $summ += $product_info->getPositionSummRecalc();
      }
      return $summ;
      
  }
      public function getFormatProductsSumm() {
        return number_format(round($this->getProductsSumm()), 0, ',', ' ');
    }

    public function getSumWithDiscount() {
            $this->recalcTotalCost();
      $summ = 0;
      foreach($this->products as $product_info) {
          if(is_null($this->products)) continue;
          $summ += $product_info->getPositionSummRecalc() - ($product_info->getPositionSummRecalc()/100*$product_info->discount);
      }
      return $summ;
    }
    public function getOptSumWithDiscount() {
      $summ = 0;
      foreach($this->products as $product_info) {
          if(is_null($this->products)) continue;
          $summ += $product_info->getOptPositionSummRecalc() - ($product_info->getOptPositionSummRecalc()/100*$product_info->discount);
      }
      return $summ;
    }
  
  
    public function getFormatSumWithDiscount() {
return number_format(  round($this->getSumWithDiscount())  ,  0  ,  ','  ,  ' '  );

      
  }
  
    public static function updateCart($data) {

        $dealer = User::getLoginCustomerOrUser();
        $order = $dealer->getCurentOrder();

        foreach($order->products as $position) {
            if($position->product_id == 0) continue;
            if(isset($data[$position->id]))
            {
                $position->count_product = $data[$position->id]["count"];
                // $position->cost = $data[$position->id]["cost"];
                $position->discount = $data[$position->id]["discount"];
                $position->edit_cost = $data[$position->id]["cost_trade"];

                $position->save();
            }
        }
    }
  
  public static function formatAjaxCartData( $cart ) {
      
      $new_cart_data = array();
      foreach($cart as &$cart_elem){
          $cart_elem = (array)$cart_elem;
          unset($cart_elem['withDCell']);
          unset($cart_elem['withoutDCell']);
          $id = $cart_elem['id'];
          unset($cart_elem["id"]);
          $new_cart_data[$id] = array();
          
          $new_cart_data[$id] = $cart_elem;
      }
      return $new_cart_data;
      
      
  }
  //$order_ids - массив с id позиций в нужном порядке
  public function setProducrOrder($order_ids) {
        $elements = $this->products;
        $sort = array();
        foreach ($order_ids as $k => $v) {
            $sort[$v] = null;
        }

        foreach ($elements as $element) {

            $sort[$element->id] = $element;
        }
        //dd_not_die($sort);
        $this->products = $sort;
    }
    
    public function setNextStep() {
        $this->order_step++;
        $this->save();
    }
    
    public function setStep($step = 1) {
        $this->order_step = $step;
        $this->save();
    }
    
    public function setComplitedStatus() {
       $this->status = 1;
       $this->status_change_date = date("Y-m-d H:i:s");
       unset($this->products);
       $this->save();

    }
    public function getFormatComplitedDate() {
        $date = new \DateTime($this->status_change_date);
        
        return  date("d-m-Y H:i",$date->getTimestamp());
    }
    
    //Для админа
    
    //Анулировать заказ
    public function nulled() {
        $this->admin_status = 2;
        $this->save();
    }
        //Анулировать заказ
    public function  executed() {
        $this->admin_status = 1;
        $this->save();
    }
    
    public function getAdminStatus() {
        if($this->admin_status == 1) return "Выполнен";
        if($this->admin_status == 2) return "Аннулирован";
        return "На рассмотрении";
    }
    
    public function getProductTypes() {
        
        $types = array();
        $product = $this->products;
        $product_arr = array();
        $types_arr = array();
        foreach($product as $p) {
            if($type = $p->product->getPhByName("type"))
            {
                $types_arr[] = $type;
            }
            
        }
        
        return $types_arr;
        //getPhrByName($name)
    }
    
    public function getProductsByType($type) {

        $product = $this->products;
        $product_arr = array();
        foreach ($product as $p) {
            $cur_type = $p->product->getPhByName("type");
            if (!is_null($cur_type) && $type == $cur_type->value) {
                $product_arr[] = $p;
            }
        }
        return $product_arr;
    }

    public static function boot() {
        parent::boot();

        static::created(function($order) {
            if($order->customer->type == "user"){
                $order->sns = $order->customer->sns;
                $order->save();
            }

        });
    }

}
