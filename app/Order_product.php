<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Order_product extends Model
{
  protected $table = 'orders_product';
  protected $fillable = ['order_id','product_id','count_product','discount'];
      protected $guarded = array("id");
  
  public function product() {
      return $this->belongsTo("App\Product","product_id","id");
  }
  
  public function order() {
      return $this->belongsTo("App\Orders","order_id","id");
  }
  
  //проверка на дублирование товара в заказе
  private function isset_in_order() {
      
     $q = self::where("order_id",$this->order_id)->where("product_id",$this->product_id);
     if( $q->count() > 0){
         return $q->get()->first();
     }
     else {
         return null;
     }
      
  }
  //Получение информции по id заказа и товара
  private static function getProductOrderInfo($order_id,$product_id) {
      
      return self::where("order_id",$order_id)->where("product_id",$product_id)->get()->first();
      
  }
  
  public static function addProductInfoInOrder($order_id,$product_id) {
        $q = self::where("order_id", $order_id)->where("product_id", $product_id);
        if ($q->count() > 0) {
            return self::updateExisting($order_id,$product_id);
        } else {
            return self::createNew($order_id,$product_id);
        }
  }
  
  private static function updateExisting($order_id,$product_id) {
      //return
   //   dd(Route::getFacadeRoot());
      $info = self::where("order_id", $order_id)->where("product_id",$product_id)->get()->first();
      $info->count_product++;
      $info->total_cost += $info->product->getCostWithMargin();
      $info->save();
      return $info;
      
  }
  private static function createNew($order_id,$product_id) {
      $info = Order_product::create( array("discount" => 0 ,"order_id" => $order_id,"product_id" => $product_id,"count_product" => 1));
      
      $product = Product::find($info->product_id);
      $info->total_cost = $product->getCostWithMargin();
      $info->save();
      return $info;
  }
  public function getCostWithDiscount() {
      return ceil_dec( $this->getPositionSummRecalc() - ($this->getPositionSummRecalc()/100*$this->discount) );
  }
  public function getOptCostWithDiscount() {
      return ceil_dec( $this->getOptPositionSummRecalc() - ($this->getOptPositionSummRecalc()/100*$this->discount) );
  }
  
  
  
  public function getPositionSumm() {
    $product = $this->product;
    return $product->getCostWithMargin() * $this->count_product;
    //return round($this->total_cost);  
  }
  public function getPositionSummRecalc() {
      if(is_null($this->product)) return 0;
       return round( $this->product->getCostWithMargin() * $this->count_product );
  }
  public function getOptPositionSummRecalc() {
      if(is_null($this->product)) return 0;
       return round(  $this->product->getCostWithMargin(true) * $this->count_product  );
  }
  public function getFormatCostWithDiscount() {
      return number_format( round($this->getCostWithDiscount())  ,  0  ,  ','  ,  ' '  );
  }
    public function getFormatPositionSumm() {
      return number_format( round($this->getPositionSummRecalc())  ,  0  ,  ','  ,  ' '  );
  }
  

  
}
