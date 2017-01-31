<?php

namespace App\Http\Controllers;

use App\Orders;
use App\Http\Controllers\Extensions\MyCrudController;
use App\Http\Controllers\Extensions\MyDataGrid as DataGrid;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use stdClass;


class OrdersController extends MyCrudController {

    public function all($entity) {
        parent::all($entity);

        return $this->viewData();
  
    }

    /*   public function edit($entity) {

      } */
    private function viewData() {

        
        $param = Request::all();
        //var_dump($param);



        $view_query = $this->getViewQuery();
        $this->filter = \DataFilter::source($view_query);
        
        $this->filter->add('customer_type', 'Тип заказчика', 'select')->options(
                array('dealer' => "Дилер", "user" => "Покупатель")
        );
        $this->filter->submit('search');
        $this->filter->reset('reset');
        
        $this->filter->build();
        
        $this->grid = DataGrid::source($this->filter);
        $this->removeErrorWhere();

        $this->grid->add('id', 'ID', true)->style("width:100px");
        $this->grid->add("id", "Номер заказа", "id");
        $this->grid->add("customer_name", "Дилер (если заказ от дилера)", "customer_name"); // *
        $this->grid->add("sns", "ФИО заказчика", "sns");
        $this->grid->add("customer_type", "Тип заказчика", "customer_type"); // *
        $this->grid->add("customer_phone", "Телефон заказчика", "customer_phone"); // *
        $this->grid->add("customer_email", "Email заказчика", "customer_email"); // *

        $this->grid->add("getProductsSumm()", "Сумма заказа,руб.", "order_summ");
        //$this->grid->add("order_summ", "Сумма заказа(sql),руб.", "order_summ");

        $this->grid->add("status_change_date", "Дата заказа", "status_change_date");
        $this->grid->add('parent',"Подробности заказа")->actions("order_product", array("order"));
        
        $this->grid->setRelation(array("customer"));
        $this->setGridDataQuery($view_query);
        $this->grid->build("",$this->getViewQuery()->get());
        
        $this->grid->paginate(1000);
        $view_data = array(
                    'grid' => $this->grid,
                    'filter' => $this->filter,
                    'title' => $this->entity,
                    'current_entity' => $this->entity,
                    'import_message' =>
                    (\Session::has('import_message')) ? \Session::get('import_message') : ''
            );
        //dd(view('panelViews::all',$view_data));
        return view('panelViews::all',$view_data);
        //return $this->returnView();
    }
    
    //Формирует запрос отображаемых данных на основе фильтров
    private function getViewQuery() {
        $q = \App\Orders::query()
                ->where("status",1)
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->select(
                    'orders.*',
                    'users.type as customer_type',
                    'users.name as customer_name',
                    'users.phone as customer_phone',
                    'users.email as customer_email',
                    DB::raw('(
                        select 
                            -- Какой алгоритм расчета? по идее должно быть coalesce(op.edit_cost, p.cost_trade) , но в карточке товара рассчитывает только по p.cost_trade. поэтому сделал так:
                            cast(sum(coalesce(p.cost_trade, op.edit_cost, p.cost_trade) * (100 - op.discount) * op.count_product) / 100 as decimal(12, 0))
                            from orders_product as op 
                                left join product p on p.id=op.product_id
                            where op.order_id=orders.id
                        ) as order_summ'
                    )
                    
                );

        $param = Request::all();
        if (isset($param["customer_type"]) && $param["customer_type"] !== "" ) {
            unset($param["search"]);
            
            $q = $q->where("users.type", "=", $param["customer_type"]);            
        }

        //dd($q->toSql());exit;
        return $q;
    }
    
    //Удаляем условия, которые могут привести к ошибкам
    private function removeErrorWhere() {
        $wheres = $this->grid->source->query->getQuery()->wheres;
        if(is_null($wheres)) return;


        
        foreach ($wheres as $k => $where) {

            if (isset($where["column"]) && $where["column"] == "customer_type") {
                unset($wheres[$k]);
            }
        }
        $this->grid->source->query->getQuery()->wheres = $wheres;
    }
    private function setGridDataQuery($query) {
        
        $this->grid->source->query = $query;
    }
    
    public function order_product() {
          $param = Request::all();
          if( !isset($param["order"]) ) return;
              
          //var_dump($param);
        $order = Orders::find( intval($param["order"]) );  
        $order->recalcTotalCost();
        $q = \App\Order_product::where("order_id",$param["order"]);
        $this->filter = \DataFilter::source($q);
        $this->grid = DataGrid::source($this->filter);
        //$this->grid->setRelation(array("product"));
        $this->grid->add("name", "Наименование", "text");
        $this->grid->add("count_product", "Количество", "text");
        $this->grid->add("cost_trade", "Цена", "text");

        if($order->getCustomerType() == "dealer") {
            
         $this->grid->add("discount", "Скидка", "text");   
        }
        $this->grid->add("summ", "Сумма", "text");
        if ($order->getCustomerType() == "dealer") {

            $this->grid->add("summWithDiscount", "Сумма со скидкой", "text");
        }

        $data_q = $q->get();
        //dd($data);
        $grid_data = new \Illuminate\Database\Eloquent\Collection();
        foreach($data_q as $position) {
            if($position->product_id == 0) continue;
            $new_position = new stdClass();
            $new_position->name = $position->product->name;
            $new_position->count_product = $position->count_product;
            $new_position->discount = $position->discount;
            $new_position->summ = $position->getPositionSumm();
            $new_position->summWithDiscount = $position->getCostWithDiscount();
            if($order->getCustomerType() == "dealer" && !is_null($position->edit_cost)) {
                
                $new_position->cost_trade = $position->edit_cost;
            }
            else {
                $new_position->cost_trade = $position->product->cost_trade;
            }
            $grid_data->push($new_position);
            
            
            
            
            
            
            
            
        }
        //dd($grid_data);
        
        
        $grid = $this->grid;

        $this->grid = $grid->build("",$grid_data);

        $view_data = array(
                    'order' => $order,
                    'grid' => $this->grid,
                    'filter' => $this->filter,
                    'title' => $this->entity,
                    'current_entity' => $this->entity,
                    'import_message' =>
                    (\Session::has('import_message')) ? \Session::get('import_message') : ''
            );
        //dd(view('panelViews::all',$view_data));
        return view('admin.order_page',$view_data);
        
        
        
    }
    
    public function nulled() {

        $r = Request::all();
        $order_id = $r["order_id"];
        $order = Orders::find(intval($order_id));
        $order->nulled();
    }

    public function executed() {
        $r = Request::all();
        $order_id = $r["order_id"];
        $order = Orders::find(intval($order_id));
        $order->executed();
    }

}
