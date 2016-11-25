<?php

namespace App\Http\Controllers;


use App\Dealer;


use Illuminate\Http\Request;
use App\Http\Controllers\Extensions\MyCrudController;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Support\Facades\Response;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Vsmoraes\Pdf\PdfFacade as PDF;
use App\Margin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Extensions\MyDataGrid as DataGrid;
use App\Http\Controllers\Extensions\MyDataEdit;
use App\Orders;


use Zofe\Rapyd\Facades\Rapyd;

class DealerController extends MyCrudController {

  //  use AuthenticatesAndRegistersUsers;
  /*  public function __construct() {
        parent::__construct();

    }*/
    private $manager_email = "website@pamira.ru";
    public function all($entity) {
        parent::all($entity);

        $this->filter = \DataFilter::source(\App\User::query()->where("type","dealer"));

        $this->grid = DataGrid::source($this->filter);
        $this->grid->add('id', 'ID', "text");
        $this->grid->add("name", "Имя", "text");
        $this->grid->add("email", "Email", "text");
        $this->addStylesToGrid();
$this->grid->paginate(1000);
        return $this->returnView();
    }

    public function edit($entity) {

        parent::edit($entity);
        $params = \Illuminate\Support\Facades\Request::all();
        $this->edit = MyDataEdit::source(new Dealer());
        if (isset($params["send_email"]) && $params["send_email"] == 1) {
            Mail::send('emails.dealer_letter', $params, function($message) use($params) {
                $message->subject("Смена пароля");
                $message->to($params["email"]);
            });
        }

        if (isset($params["update"]) && isset($params["password"]) && $params["password"] == "") {
            $this->edit->model->password = $params["old_password"];
            $this->edit->model->save();
        }
        elseif(isset($params["update"]) && isset($params["password"]) && $params["password"] != "") {
            $this->edit->model->password = md5($params["old_password"]);
            $this->edit->model->save();
        }

        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('email', 'Email', 'text')->rule('required|email');
        $typeField = $this->edit->add('type', '', 'hidden');
        $typeField->insertValue('dealer');
        $typeField->updateValue('dealer');


        $pass_filed =  $this->edit->add('password', 'Пароль', 'text');
        if( !isset($params["modify"]) && !isset($params["update"])) {
            $pass_filed->rule('required');
        }
        if( isset($params["modify"]) || isset($params["update"])) {
           $this->edit->add('old_password', '', 'hidden')->updateValue($this->edit->model->password);
           $this->edit->add('sourse_id', '', 'hidden')->updateValue($this->edit->model->id);

        }

        $this->edit->add("generate_password", "Сгенерировать пароль", self::$ext_apth . '\ButtonField');

        $this->edit->add('sns', 'Подразделение', 'text');

        $this->edit->add('send_email', 'отправить пароль на e-mail дилера', 'checkbox');
        $this->edit->add('region_id','Регион','select')->options(\App\Region::lists("name", "id")->all());
        $this->edit->add('sns', 'Подразделение', 'text')->rule('required');
        Rapyd::js("public/js/dealer.js");
        //bcrypt
        return $this->returnEditView();

    }

    private function getManagerEmail() {

        return $this->manager_email;

    }

    public function auth($pass) {

        $auth = Dealer::loginByPass($pass);

        return Response::json(   is_null($auth)?0:1  );

    }
    public function logout() {
       Auth::guard("dealer")->logout();
       return redirect("/");

    }

    public function marginList() {
        $dealer = Dealer::getLoginDealer();
        //var_dump($dealer);

        // Это на самом деле РОЗНИЦА
        $array_wholesale_margin = $dealer->getWholesaleMargins();
        // Теперь береберём массив, чтобы удалить/заменить служебные пометки
        for ($i=0; $i < count($array_wholesale_margin); $i++) {
            if(substr($array_wholesale_margin[$i]['name'], -3) == "rev"){
                $array_wholesale_margin[$i]['name'] = substr($array_wholesale_margin[$i]['name'], 0, -4);
            }
        }
        // Это на самом деле ОПТ
        $array_retail_margin = $dealer->getRetailMargins();
        // Теперь береберём массив, чтобы удалить/заменить служебные пометки
        for ($i=0; $i < count($array_retail_margin); $i++) {
            if(substr($array_retail_margin[$i]['name'], -3) == "rev"){
                $array_retail_margin[$i]['name'] = substr($array_retail_margin[$i]['name'], 0, -4);
            }
        }
        //dd($data['margins']);

        $data['wholesale_margin'] = $array_wholesale_margin;
        $data['retail_margin'] = $array_retail_margin;

        return view("dealer.for_admins",$data);

    }

    public function marginCreate(Request $request) {

        //dd($request->method());
        if($request->method() == "GET" ) {
         return $this->marginCreateForm($request);
        }
        if($request->method() == "POST" ) {
         return $this->marginSaveForm();
        }

        //get
        //pos=
    }
    public function margin_edit($margin_id,Request $request) {
        if ($request->method() == "GET") {
            return $this->marginEditForm( $margin_id );
        }
        if ($request->method() == "POST") {
            return $this->marginSaveEditForm( $margin_id );
        }
    }

    private function marginEditForm($margin_id) {
        $data['brands'] = \App\Brand::all();
        $data['margin'] = Margin::find($margin_id);

        if(substr($data['margin']->name, -3) == "rev"){
            $data['margin']->name = substr($data['margin']->name, 0, -4);
            $type = $data['margin']->type;
            ($type=="wholesale") ? $type="retail" : $type="wholesale";
            $data['margin']->current_type = $type;
        }
        else
            $data['margin']->current_type = $data['margin']->type;

        //var_dump( $data['margin']->brands );
        return view("dealer.edit_margin_form", $data);
    }
    private function marginSaveEditForm($margin_id) {

        $post = \Illuminate\Support\Facades\Request::all();
        $form_data = \App\Margin::formatPostMarginFormData( $post );
        $form_data['margin']['id'] = $margin_id;
        // $valid = \App\Margin::saveFormData($form_data['margin'],$form_data['brand_margin'],$form_data['brand_ids'], $form_data['default']);

        $margin_type = $form_data['margin'];
        if($form_data['mark_up_initially'] != $margin_type['type']){
            // $form_data['margin']->type = $form_data['mark_up_initially'];
            // $form_data['margin']->name = $form_data['margin']->name . ' (+)';
            $valid = \App\Margin::saveFormData($form_data['margin'],$form_data['brand_margin'],$form_data['brand_ids'], $form_data['default'], $form_data['mark_up_initially'], ' rev', true);
        }
        else{
            $valid = \App\Margin::saveFormData($form_data['margin'],$form_data['brand_margin'],$form_data['brand_ids'], $form_data['default'], $form_data['mark_up_initially'], '', false);
        }

        if($valid) {
          return redirect('/dealer/margin_list');
        }
        else {
            return redirect()->back()->withErrors(array("name" => false));
        }

    }

    private function marginCreateForm($request) {

        $data['brands'] = \App\Brand::all();
        $data['margin_type'] = $request->get('margin_type', false);
        return view("dealer.create_margin_form",$data);

    }

    private function marginSaveForm() {

        //Приводит данные, полученные из post, к удобному виду
        $post = \Illuminate\Support\Facades\Request::all();
        $form_data = \App\Margin::formatPostMarginFormData( $post );
        // $valid = \App\Margin::saveFormData($form_data['margin'],$form_data['brand_margin'],$form_data['brand_ids'], $form_data['default']);

        $margin_type = $form_data['margin'];
        if($form_data['mark_up_initially'] != $margin_type['type']){
            // $form_data['margin']->type = $form_data['mark_up_initially'];
            // $form_data['margin']->name = $form_data['margin']->name . ' (+)';
            $valid = \App\Margin::saveFormData($form_data['margin'],$form_data['brand_margin'],$form_data['brand_ids'], $form_data['default'], $form_data['mark_up_initially'], ' rev', true);
        }
        else{
            $valid = \App\Margin::saveFormData($form_data['margin'],$form_data['brand_margin'],$form_data['brand_ids'], $form_data['default'], $form_data['mark_up_initially'], '', false);
        }


        if ($valid) {
            return redirect('/dealer/margin_list');
        } else {
            return redirect()->back()->withErrors(array("name" => false)); //->withInput( $form_data['margin'] );
        }
    }

    public function orderList() {

    }

    public function order() {

    }



    public function cart_step(Request $request) {
        $ses = Session::all();
       // var_dump($ses);

        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();

        if(!is_null($order)) {
            $token = $request->get("step_token");

            if (!is_null($token) && ( $token === csrf_token())) {

                //dd(redirect()->back());
                //Уничтожаем текущий токен, чтобы при перезагрузке страницы не плюсовался шаг заказа
               // Session::forget('_token');

            }

            if($request->method() == "POST")
            {
                $post = $request->all();
                if(isset($post['step']))
                {
                    $order->setStep($post['step']);
                }
            }
            if($request->method() == "GET" && $request->get('step') == 1)
            {
                $order->setStep($request->get('step'));
            }
            switch ($order->order_step) {
                case 1:
                    return $this->cart();
                    break;
                case 2:
                    return $this->formalize_order_cart($request);
                    break;
                case 3:
                    return $this->formalize_order_completion($request);
                    break;
                default:
                    return $this->cart();
                    break;
                //case 4:return $this->caterer_mail_send();break;
            }
        }
        else {
            return view("message",array("message" => "Корзина пуста"));
        }
    }

    public function cart() {
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $data['order'] = $dealer->getCurentOrder();
        $this->setCartScripts();

        return view("cart.cart",$data);
    }

    //Страница оформления текущего заказа
    public function formalize_order_cart(Request $request) {
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $data['order'] = $dealer->getCurentOrder();
        $this->setCartScripts();
        return view("dealer.formalize_order_page",$data);
    }

    public function formalize_order_completion(Request $request) {
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();
        if ($request->method() == "POST") {
            $validate_data = $request->all();
            $validator = Validator::make(
                            $validate_data, array('name' => 'required')
            );
            if ($validator->fails()) {
                $order->setStep(2);
                $this->setCartScripts();
                return view("dealer.formalize_order_page",['order' => $order, 'errors' => $validator->errors()]);
            }
            $order->sns = $validate_data['name'];
            $order->setComplitedStatus();
            $order->save();
        }

        $data['order'] = $order;
        $this->set_scripts([
            //'/js/modules/jquery.sortable.min.js',
            '/js/modules/min/jquery.ui-drag-drop.min.js',
            '/js/min/formalize_order_completion.min.js'
        ]);

        return redirect("/dealer/completed_order/" . $order->id);

    }

    public function setCartScripts() {
        $scripts = array(
            "/js/modules/jquery.json-2.3.js",
            "/js/modules/min/helpers.min.js",
            // "/js/min/dealer_cart.min.js"
            // "/js/dealer_cart.js"
            "/js/dealer_cart.GeorgeBramus02.js"
        );
        $this->set_scripts($scripts);
    }

    public function test_pdf() {


        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
       $order = $dealer->getCurentOrder();

        $html = view('pdf.order_pdf',['order' => $order, 'dealer' => $dealer])->render();

    PDF::load($html)
        ->filename(public_path().'/uploads/pdf/example2.pdf')
        ->output();

    }

    //Управление оформленным заказом - pdf файлы и отправка поставщику
    public function option_completed_order($type, Request $request) {
        switch ($type) {
            case "save_client_pfd":
               return $this->save_client_order_pdf_current( $request );
               break;
            case "save_caterer_pfd":
                return $this->save_order_pdf_current( $request );
                break;
            case "caterer_mail_send":
                return $this->caterer_mail_send_current( $request );
                break;
        }

        // var_dump( $_POST );
       // $data = $request->all();
       // var_dump($data);

    }

    public function save_order(Request $request) {

        $request = $request->all();
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();
        $ids = $request['id_order'];
        //return $this->save_order($order,$ids);
        //$order->setProducrOrder($ids);
        $order->status = 1;
        $order->save();
        return 1;
    }

    public function save_order_pdf_by_id($id, Request $request) {
        $all = $request->all();

        $order = \App\Orders::find($id);
        $ids = $all['id_order'];
        return $this->save_order_pdf($order, $ids);
    }

    public function save_client_order_pdf_by_id($id, Request $request) {
        $all = $request->all();
        $order = \App\Orders::find($id);
        $ids = $all['id_order'];
        return $this->save_client_order_pdf($order, $ids);
    }

    public function caterer_mail_send_by_id($id, Request $request) {
        $all = $request->all();
        $order = \App\Orders::find($id);
        $ids = $all['id_order'];
        return $this->caterer_mail_send($order, $ids);
    }
    public function save_client_order_pdf_current(Request $request ) {
        $all = $request->all();
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();
        $ids = $all['id_order'];
        return $this->save_client_order_pdf($order,$ids);
    }
    public function save_order_pdf_current(Request $request ) {
        $all = $request->all();
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();
        $ids = $all['id_order'];
        return $this->save_order_pdf($order,$ids);
    }


    public function caterer_mail_send_current(Request $request ) {
        $all = $request->all();
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();
        $order->setComplitedStatus();
        $ids = $all['id_order'];
        return $this->caterer_mail_send($order,$ids);
    }
    public function save_order_pdf($order,$ids) {
        $order->setProducrOrder($ids);
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $file_name = "Заказ для поставщика ".$dealer->name." №" . $order->id . " от " . date("d.m.Y").".pdf";
        $path = public_path() . '/uploads/pdf/' . $file_name.".pdf";
        $html = view('pdf.order_pdf',['order' => $order, 'dealer' => $dealer])->render();
        return PDF::load( $html )->filename($file_name)->download();
    }
    public function save_client_order_pdf($order,$ids) {
        $order->setProducrOrder($ids);
        $file_name = "Заказ для клиента ".$order->sns." №" . $order->id . " от " . date("d.m.Y").".pdf";
        $path = public_path() . '/uploads/pdf/' . $file_name.".pdf";
        $html = view('pdf.client_order_pdf',['order' => $order])->render();
        return PDF::load( $html )->filename($file_name)->download();
    }

    private function caterer_mail_send($order,$ids) {
        if(!is_null($order) && $order->products()->count() > 0 ) {
        $order->setProducrOrder($ids);
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();

        $file_name = "Заказ для поставщика №" . $order->id . " от " . date("d.m.Y") . '.pdf';
        $html = view('pdf.order_pdf', ['order' => $order, 'dealer' => $dealer])->render();
        $path = public_path() . '/uploads/pdf/' . $file_name;
        PDF::load($html)
                ->filename($path)
                ->output();
        $data['path'] = $path;
        $data['to'] = $order->getEmailForDealer();

        Mail::send('emails.customer_letter', $data, function($message) use($data,$order) {
            $message->subject("Заказ для поставщика №" . $order->id . " от " . date("d.m.Y"));
            $message->to($data['to'])->cc('heleonprime@ya.ru');
            $message->attach( $data['path'] );
        });

        //print view( "message", array("message" => "Заказ успешно отправлен поставщику") );
        //return redirect("/");
        }
    }

    public function set_default_margin($margin_id) {
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $margin = Margin::where('id', $margin_id )->where('user_id', $dealer->id)->first();
        if(!empty($margin))
        {
            $margin->setDefault();
        }

    }

    public function delete_margin($margin_id){
        $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $margin = Margin::where('id', $margin_id )->where('user_id', $dealer->id)->first();
        if(!empty($margin))
        {
            $margin->delete();
        }
    }




    public function order_history() {

        $dealer = Dealer::getLoginDealer();
        $data['orders'] = $dealer->getOrderHistory();


        return view("dealer.order_history",$data);

    }
    public function completed_order($order_id) {
        $data['order'] = \App\Orders::find($order_id);


        $this->set_scripts([
            //'/js/modules/jquery.sortable.min.js',
            '/js/modules/min/jquery.ui-drag-drop.min.js',
            '/js/min/formalize_order_completion.min.js'
            ]);
        return view("dealer.formalize_order_completion",$data);
    }

    public function remove_order($order_id) {

        $dealer = Dealer::getLoginDealer();
        $order = \App\Orders::where('id', $order_id)->where('user_id', $dealer->id)->first() ;

        if(!empty($order))
        {
            \App\Order_product::where('order_id', $order->id)->delete();
            $order->delete();
        }
    }

    //Отменя заказа и восстановления позиция в корзине

    public function restore_order_in_cart($order_id) {
        $dealer = Dealer::getLoginDealer();
        $order = $dealer->getCurentOrder();
        if(!is_null($order)) {
        $order->delete();
        }

        $new_order = Orders::create(array("user_id" => $dealer->id));

        $id = $new_order->id;
        $new_order->delete();

        $order_h = Orders::find($order_id);
        foreach($order_h->products as $k=>&$v) {
            $v->order_id = $id;
            $v->save();

        }


        $order_h->status = 0;
        $order_h->order_step = 1;
        $order_h->id = $id;

        $order_h->save();

        return Response::json($order_h);

    }

    public function save_compare_pdf() {
        $pi = \App\Compare::getUsersPositions("order");
        $data["orders"] = $pi->get_positions();
        $data["types"] = [];
        $orderIds = [];
        foreach ($data["orders"] as $order) {
            $data["types"] = array_merge($order->getProductTypes(), $data["types"]);
            $orderIds[] = $order->id;
        }
        $data["types"] = array_unique($data["types"]);
        $file_name = "Сравнение заказов №" . implode(', ', $orderIds) . ".pdf";
        $path = public_path() . '/uploads/pdf/' . $file_name.".pdf";
        $html = view('pdf.orders_compare',$data)->render();
        //return $html;
        return PDF::load( $html )->filename($file_name)->download();
    }

    public function order_products_order(Request $request){
        $post = $request->all();
        if(!empty ($post['ids']))
        {
            foreach($post['ids'] as $order => $id)
            {
                \App\Order_product::where('id', $id)->update(['ordering' => $order]);
            }
            return 1;
        }
        return 0;
    }
}
