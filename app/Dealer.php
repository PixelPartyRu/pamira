<?php

namespace App;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Orders;

class Dealer extends User {

    protected $table = "users";
    public $need_bcrypt_pass = false;


    public static function loginByPass($pass) {

        $user = null;
        self::logoutAll();
        $dealer = Dealer::where("type","dealer")->where('password', md5($pass))->first();
        if($dealer)
        {
            Auth::guard("dealer")->login($dealer, true);
        }
        return $dealer;
    }

    public static function getLoginDealer() {
//var_dump(Auth::guard("dealer")->user());

        return Auth::guard("dealer")->user();
    }

    public static function is_login() {
        //var_dump($_SESSION['dealer']);
        if (!is_null(Auth::guard("dealer")->user()))
            return true;
        return false;
    }

   /* public static function logout() {
        Auth::guard("dealer")->logout();
    }*/

    public function margin() {

        return $this->hasMany("App\Margin", "user_id", "id");
    }



    //Оптовые наценки дилера
    public function getWholesaleMargins() {
        $auth_dealer = self::getLoginDealer();
        return $auth_dealer->margin()->where("type", "wholesale")->get();
    }

    // Розничные наценки дилера
    public function getRetailMargins() {
        $auth_dealer = self::getLoginDealer();
        return $auth_dealer->margin()->where("type", "retail")->get();
    }





    //возвращает поседний завершенный казаз
    public static function getLastFormalizeOrder() {

        $status_change_date = $this->dealer_orders()->where("status", 1)->max("status_change_date");
        // return $this->dealer_orders()->
    }

    public function getCurentOrdrer() {
        //Поиск заказа, который не закрыт
        $order = $this->dealer_orders()->where("status", 0);

        if ($order->count() == 0) {
            $order = Orders::create(['user_id' => $this->id]);
        } else {
            $order = $order->get()->first();
        }
        return $order;
    }

    //история завершенных заказов

    public function getOrderHistory() {
        return $this->dealer_orders()->where("status", 1)->orderBy('id', 'desc')->get();
    }

    public static function getDealerMarginList() {
        $auth_dealer = self::getLoginDealer();
        return $auth_dealer->margin;
    }

    public function setDefaultMargin($type, $margin_id) {
        DB::table('margins')
            ->where("type", $type)
            ->where('user_id', $this->id)
            ->where("default", 1)
            ->update(array('default' => 0));

        DB::table('margins')
            ->where("type", $type)
            ->where("id", $margin_id)
            ->where('user_id', $this->id)
            ->update(array('default' => 1));
    }

    public function bcrypt_pass(){
        $this->password = bcrypt($this->password);
        $this->save();

    }

    public function is_bcrypt_pass() {

        return strpos($this->password,"2y$10$");
    }

    public static function boot() {
        parent::boot();

        static::created(function($user) {
            if ($user->type == "dealer" && !$user->is_md5_password() ) {
                $user->password = md5($user->password);
                $user->save();
            }
        });
        static::updated(function($user) {
            if ($user->type == "dealer" && !$user->is_md5_password() ) {

                $user->password = md5($user->password);
                $user->save();
            }
        });
    }

}
