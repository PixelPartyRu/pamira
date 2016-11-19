<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CustomerController;
use App\User;
use App\Compare;

class Buy extends Controller {

    public function __construct() {

    }

    //Добавление товара в корзину
    public function buy($product_id) {


        //Если дилер

        if (!is_null(\App\Dealer::getLoginDealer())) {


            $data = $this->add_product_in_cart(\App\Dealer::getLoginDealer(), $product_id);
            $data['is_login'] = 1;
            return \Illuminate\Support\Facades\Response::json($data);
        }
        //если с дилером не прокатило, то дальше работаем только с обычным покупателем

        $con = new CustomerController();

        //Если уже есть сессия с обычным покупатем
        if (!is_null(\App\User::getLoginCustomer())) {
            $data = $this->add_product_in_cart(\App\User::getLoginCustomer(), $product_id);
            $data['is_login'] = 1;

            return \Illuminate\Support\Facades\Response::json($data);
        }

        //Если нет вообще никого залогиненого, то стратует новая сессия для обычного покупателя

        if (is_null(\App\User::getLoginCustomer()) && is_null(\App\Dealer::getLoginDealer())) {
            \App\User::createAndLoginEmptyCustomer();

            $data = $this->add_product_in_cart(\App\User::getLoginCustomer(), $product_id);
            $data['is_login'] = 1;
            return \Illuminate\Support\Facades\Response::json($data);
        }
    }

    public function test_auth() {

        d(Auth::guard("dealer")->user());

        d(Auth::guard("web")->user());
    }

    public function logout_all() {
        Auth::guard("dealer")->logout();
        Auth::guard("web")->logout();
    }

    public function add_product_in_cart($user, $product_id) {

        $order = $user->getCurentOrder();
        if (is_null($order))
            $order = $user->createNewOrder();
        $info['success'] = 1;
        $info['order_step'] = $order->order_step;
        if ($order->order_step == 1) {
            $order->addProduct($product_id);
            $info['count'] = $order->getProductsCount();
            $info['summ'] = $order->getFormatProductsSumm();
        } else {
            $info['error_message'] = "Заказ находится на стадии оформления";
            $info['success'] = 0;
        }


        return $info;
    }

    public function save_dealer_cart_ajax() {

        $data = \Illuminate\Support\Facades\Request::all();

        // $data['cart'] = json_decode($data['cart']);
        // $cart = \App\Orders::formatAjaxCartData($data['cart']);
        $data['cart'] = json_decode($data['cart']);
        $cart = \App\Orders::formatAjaxCartData($data['cart']);
        \App\Orders::updateCart($cart);

        echo '<script>console.log("Я передал AJAX");</script>';
    }

    public function remove_position($id) {
        //dd(!( is_null(Auth::guard("dealer")->user()) && is_null(Auth::guard("web")->user()) ));
        if (!( is_null(Auth::guard("dealer")->user()) && is_null(Auth::guard("web")->user()) )) {
            $position = \App\Order_product::find($id);
            $position->delete();
        }
    }

    public function add_to_compare($pid,$type) {
        $user = User::getLoginCustomerOrUser();
        $compare_user_info = Compare::getUsersPositions($type);
        $compare_user_info->add_position($pid);
        return \Illuminate\Support\Facades\Response::json( $compare_user_info->count_position() );


    }

    public function remove_position_compare($pid,$type) {
        $user = User::getLoginCustomerOrUser();
        $compare_user_info = Compare::getUsersPositions($type);
        $compare_user_info->remove_position($pid);

    }

    public function order_compare_list() {
        $pi = Compare::getUsersPositions("order");
        $data["orders"] = $pi->get_positions();
        if(count($data["orders"]) == 0)
        {
            return redirect('/dealer/order_history');
        }
        $data["types"] = array();
        foreach ($data["orders"] as $order) {
            $data["types"] = array_merge($order->getProductTypes(), $data["types"]);
        }
        $data["types"] = array_unique($data["types"]);
        return view("dealer.order_compare_list", $data);
    }

    public function clear_compare_list() {
        $user = User::getLoginCustomerOrUser();
        if(Compare::where('user_id', $user->id)->delete())
        {
            return 1;
        }
        return 0;
    }

}
