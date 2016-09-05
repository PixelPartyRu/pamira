<?php

namespace App;
use App\User;
use App\Orders;
use App\Product;
use Illuminate\Database\Eloquent\Model;
//Все сравнение будет в контролллере Buy
class Compare extends Model{
     protected $table = 'compare';
     protected $fillable = array("user_id","type","compare_position");
     
     public function add_position($id){
         
         if($this->compare_position !== "") {
         $position = (array)json_decode($this->compare_position);
         }
         else {
            $position = array(); 
         }
         
         $position[] = $id;
         $position = array_unique($position);
         $position_arr = array();
         foreach($position as $p){
             $position_arr[] = $p;
         }
         var_dump($position_arr);
         $position = json_encode($position_arr);
         $this->compare_position = $position;
         $this->save();
         
         
         
     }
     public function remove_position($id) {
         print "<pre>";
        $position =  (array)json_decode($this->compare_position);

        
        if (in_array($id, $position)) {

            foreach ($position as $k => &$p) {
               
                if (intval($p) == intval($id)) {
                    $p = "";
                    
                     
                }
            }
            //var_dump($position);
            
        }
        
        print "</pre>";
        $position = array_unique($position);
        $position = json_encode($position);
        $this->compare_position = $position;
        $this->save();
    }

    public function get_positions() {
        $position_arr = array();
        if ($this->compare_position == "")
            return [];
        $positions = (array)json_decode($this->compare_position);
        if ($this->type == "order" || $this->type == "product") {
            foreach ($positions as $p) {
                if($order = Orders::find(intval($p)))
                {
                    $position_arr[] = $order;
                }
            }
        }
        return $position_arr;
    }

    public function count_position() {
         if($this->compare_position == "") return 0;
         $position = json_decode($this->compare_position);
         return count($position);
         
     }
     
     public static function getUsersPositions($type) {
         
         $user = User::getLoginCustomerOrUser();
         $q = self::where("user_id",$user->id)->where("type",$type);
         if($q->count() == 0){
             $c_info = self::create( array("user_id" => $user->id,"type" => $type) );
         }
         else {
             $c_info = $q->get()->first();
         }
         return $c_info;
     }
     
     public static function getPositionCount() {
         
         return self::getUsersPositions("product")->count_position();
         
     }
     
     
}
