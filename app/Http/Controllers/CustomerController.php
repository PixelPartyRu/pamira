<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Vsmoraes\Pdf\PdfFacade as PDF;
use Illuminate\Support\Facades\Mail;
use App\Orders;

class CustomerController extends Controller {

    public function __construct() {
        
    }
    //Проверка на заполнение личных данных
    public function getOrderUserData() {
        $customer = User::getLoginCustomer();
        $order = $customer->getCurentOrder();
        if(is_null($order->sns)) return 0;
        return 1;
    }
    public function logout() {
        User::logout();
    }
    public function createNewCustomer(Request $request) {
        //dd(\Illuminate\Support\Facades\Auth::guard("web")->user());
        $data['has_user'] = false;
        $validate_data = $request->all();

        
        $validator = Validator::make(
                    $validate_data, array(
                    'sns' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required|regex:/^[0-9- ]*$/'
                        ), array(
                    "sns.required" => "Введите ФИО",
                    "email.email" => "Введите корректный email",
                    "email.required" => "Введите email",
                    "phone.required" => "Введите корректный номер телефона",
                    "phone.regex" => "Введите корректный номер телефона"
                        )
        );
        $data['valid'] = !$validator->fails();

        $data['error'] = $validator->errors();

    if (!$validator->fails()) {
        
            $temporary_customer = User::getLoginCustomer();
            $order = $temporary_customer->getCurentOrder();
            $order->sns = $validate_data['sns'];
            
            
            //Предположим пользователь с таким email уже есть в базе. 
            //Не будем напрягать человека восстановлением пароля, а просто обновим данные
            if (!User::hasCustomerByEmail($validate_data['email'])) {
                $user = User::getCustomerByEmail($validate_data['email']);

                $order->user_id = $user->id;
                $temporary_customer->delete();
                
            }
            else {
            $user = $temporary_customer;

            }
            $order->save();
            $user->sns = $validate_data['sns'];
            $user->phone = $validate_data['phone'];
            $user->email = $validate_data['email'];
            $user->region_id = $validate_data['region_id'];
            $user->save();
            $user->type = "user";
            $user->password = bcrypt("111");
            $user->save();

            Auth::login($user, true);
        }


        return $data;
    }
    public function cart() {
        $dealer = \Illuminate\Support\Facades\Auth::guard("web")->user();
        $data['order'] = $dealer->getCurentOrder();
        if( is_null($data['order']) ) {
            return view("message",array("message" => "Заказ пуст"));
        }
        $scripts = array(
            "/js/modules/jquery.json-2.3.js",
            "/js/modules/min/helpers.min.js",
            "/js/min/dealer_cart.min.js"
        );
        $this->set_scripts($scripts);
        return view("cart.cart",$data);
    }
    
    public function save_order_pdf($order_id) {

        $order = \App\Orders::find($order_id);

        $file_name = "Заказ №" . $order->id . " от " . date("d.m.Y").".pdf";
        //$order->status = 1;
        //$order->save();

        $user = User::where('id', $order->user_id)->first();
    $html = view('pdf.order_pdf_user', ['order' => $order, 'user' => $user])->render();
        return PDF::load($html)->filename($file_name)->download();
    }
     public function manager_mail_send($order_id) {


        $order = \App\Orders::find($order_id);

        if (!is_null($order) && $order->products()->count() > 0) {


            $order->setComplitedStatus();
            //dd($order->customer);
           // dd($order->customer->region->name);
            $file_name = "Заказ для клиента №" . $order->id . " от " . date("d.m.Y") . ".pdf";
            $user = User::where('id', $order->user_id)->first();
            $html = view('pdf.order_pdf_user', ['order' => $order, 'user' => $user])->render();
            $path = public_path() . '/uploads/pdf/' . $file_name;
            PDF::load($html)
                    ->filename($path)
                    ->output();
            $data['path'] = $path;
            $data['to'] = $order->getEmailForCustomer();
            $data['order_id'] = $order->id;
            if( $_SERVER['HTTP_HOST'] !== "pamira") {
            Mail::send('emails.customer_letter', $data, function($message) use($data) {
                $message->subject('Новый createNewCustomer №'.$data['order_id']);
                $message->to($data['to'])->cc('heleonprime@ya.ru');

                $message->attach($data['path']);
            });
            }
              
              $user = User::getLoginCustomer();
              Auth::logout($user);
              
              

        }
    }
    public function test_mail($order_id) {
        $order = \App\Orders::find($order_id);

        if (!is_null($order) && $order->products()->count() > 0) {


            $order->setComplitedStatus();
            $file_name = "Заказ_№" . $order->id . "_от_" . date("d.m.Y") . ".pdf";
            $user = User::where('id', $order->user_id)->first();
            $html = view('pdf.order_pdf_user', ['order' => $order, 'user' => $user])->render();
            $path = public_path() . '/uploads/pdf/' . $file_name;
            PDF::load($html)
                    ->filename($path)
                    ->output();
            $data['path'] = $path;
            $data['to'] = $order->customer->email;
            $data['order_id'] = $order->id;
            Mail::send('emails.customer_letter', $data, function($message) use($data) {
                $message->subject('Новый заказ №'.$data['order_id']);
                $message->to($data['to']);

                $message->attach($data['path']);
            });
        }
    }

}
