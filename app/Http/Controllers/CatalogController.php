<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Extensions\MyCrudController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

use \Illuminate\Support\Facades\Schema;
use App\Catalog;
use App\Product;

use App\Http\Controllers\Extensions\MyDataEdit;
use App\Http\Controllers\Extensions\MyDataGrid as DataGrid;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class CatalogController extends MyCrudController {



        public function all($entity){

        parent::all($entity);

        $params = Request::all();
        $parent_id = empty($params)||!isset($params['catalog'])?0:intval($params['catalog']);

        $this->filter = \DataFilter::source(\App\Catalog::query()->where("parent_id",$parent_id));

        $this->grid = DataGrid::source($this->filter);
        $this->grid->add('id','ID', true)->style("width:100px");
       // $this->grid->add('parent_id','parent_id');

        //this->grid->add('name', 'Categories', 'button');


        $this->addStylesToGrid();
        $this->grid->add("name","Название каталога","text");
        $this->grid->add('parent',"Товары")->actions("product", array("catalog"));
        $this->grid->paginate(1000);
        return $this->returnView();



    }
    public static function test_param( $param ){

        $products = Product::whereHas("phr.ph",function($q) use($param){

          $q->where('name', $param);

      });



      $pr = \App\PHR::whereHas("ph",function($q) use($param){

          $q->where('name', $param);

      });


    }

    public function getCount() {
        $start = microtime(true);
        $r = Request::all();

        $catalog = Catalog::find( intval($r['catalog_id']) );
           //     echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';
        return Response::json($catalog->getCountProductByHid( intval($r['hid'])) );



    }

      public function edit($entity) {

        parent::edit($entity);
        $this->edit = MyDataEdit::source(new Catalog());
        $params = Request::all();
        $parent_id = empty($params)||!isset($params['catalog'])?0:intval($params['catalog']);

        if($parent_id!==0) {
            $catalog_name = Catalog::find($parent_id)->name;
        }
        else {
            $catalog_name = "";
        }

       // $this->edit->label('Добавление подкаталога в каталог'.$catalog_name);
        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('alias', 'Для формирования пути', 'text');
        $this->edit->add('img', 'Картинка', 'image')->move('uploads/product/img')->preview(100, 100);
       // $this->edit->AddText("Выбор доступных фильтров для каталога");

        $haracteristic = Product::$PRODUCT_HARACTERISTIC;
       // $this->edit->add('filter', 'Categories', 'checkboxgroup')->options($haracteristic);

        $params = Request::all();
        if ( isset($params['insert']) || isset($params['update']) ) {

          //  $this->edit->model->filter = json_encode($params['filter']);
        }

       // $parent_id = empty($params)||!isset($params['catalog'])?0:intval($params['catalog']);

       // $this->edit->model->parent_id = $parent_id;

        $ah = $this->edit->model->getAccessFiltersWithCount();
        //d($ah);



            return \View::make("admin.catalog_edit", array('title' => $this->entity,
                        'edit' => $this->edit,
                        'catalog' => $this->edit->model->id,
                        'ah' => $ah,
                        'helper_message' => $this->helper_message));

    }
    public function product() {
        $lang = new \Illuminate\Support\Facades\Lang();
        $product = new ProductController($lang);
       return $product->all("Product");

    }

    public function catalog($catalog) {

        $data['catalog_ob'] = Catalog::where("alias",$catalog)->get()->first();
        $id = $data['catalog_ob']->id;
        $data['product_list'] = Product::where("catalog_id",$id)
                                    // ->orderBy("sales_leader","desc")
                                    ->orderBy("cost_trade","asc")
                                    ->where("deleted",0)
                                    ->where('moderated', 1)
                                    ->paginate(12);//get()
        $data['filters'] = $data['catalog_ob']->getAccessFilters();


        $data['filter_max_price'] = Product::where("catalog_id",$id)
                                    ->where("deleted",0)
                                    ->orderBy("cost_trade","asc")
                                    ->max('cost_trade');
        $data['filter_min_price'] = Product::where("catalog_id",$id)
                                    ->where("deleted",0)
                                    ->orderBy("cost_trade","asc")
                                    ->min('cost_trade');
        $data['filter_brand'] = $data['catalog_ob']->getAccessBrands();
        return view("catalog.catalog_page",$data);

    }

    public function catalog_test($catalog) {

        $data['catalog_ob'] = Catalog::where("alias", $catalog)->get()->first();
        $id = $data['catalog_ob']->id;
        $data['product_list'] = Product::where("catalog_id", $id)
                                    // ->where("deleted",0)
                                    ->orderBy("sales_leader", "desc")
                                    ->orderBy("name", "acs")
                                    ->take(12)
                                    ->get();
        $data['filters'] = $data['catalog_ob']->getAccessFilters();

        $data['filter_max_price'] = Product::where("catalog_id", $id)
                                    // ->where("deleted",0)
                                    ->max('cost_trade');
        $data['filter_min_price'] = Product::where("catalog_id", $id)
                                    // ->where("deleted",0)
                                    ->min('cost_trade');

        $data['filter_brand'] = $data['catalog_ob']->getAccessBrands();
        return view("catalog.catalog_page_test", $data);
    }

    //При смене чекбоксов возвращаю результат
    public function filter() {

        $data = Request::all();
        //$cur = $data["cur"];

        $catalog_id = intval($data['catalog']);
        $catalog = Catalog::find(intval($data['catalog']));
        $data = $catalog->prepareFilterFormat($data);

       // d($data);

        $filters_to_disable = true;
        $filtered_ids = $catalog->getCountProductByFilterValues($data, $filters_to_disable);
        $product_query = Product::whereIn("id", $filtered_ids)
                        ->where("cost_trade", ">=", $data['cost_trade']['min'])
                        ->where("cost_trade", "<=", $data['cost_trade']['max'])
                        ->where("catalog_id",$catalog_id)
                        ->where("deleted",0)
                        ->orderBy("cost_trade","asc");
        
        $products['disable_filters'] = $filters_to_disable;
        $products['count'] = $product_query->count();


        if(empty($data["filter"])) {
            $products['disable_filters'] = array();
        }

        //var_dump($data, $products, $count_result);
        return response()->json($products);
    }

    public function test_filter() {
        $data = Request::all();
        $catalog = Catalog::find(intval($data['catalog']));
        $data = $catalog->prepareFilterFormat($data);


    }

    //Товары по фильтру
    public function getFilterProduct() {
        $data = Request::all();
        $catalog = Catalog::find( intval($data['catalog']) );
        $data = $catalog->prepareFilterFormat($data);
       // var_dump($data);
        if(!empty($data["filter"])) {
            if( empty($data['sort']) || !isset($data['sort']) )
                $sort = "cost_trade";
            else
                $sort = $data['sort'];

            $products = Product::whereIn("id", $catalog->getCountProductByFilterValues($data))
                ->where("catalog_id",$catalog->id)
                ->where("cost_trade",">=",$data['cost_trade']['min'])
                ->where("cost_trade","<=",$data['cost_trade']['max'])
                ->where("deleted",0)
                // ->orderBy("sales_leader","desc")
                // ->orderBy("cost_trade","asc")
                ->orderBy($sort,"asc")
                ->get();
        }
        else {
           $products = Product::where("catalog_id",$catalog->id)
                ->where("cost_trade",">=",$data['cost_trade']['min'])
                ->where("cost_trade","<=",$data['cost_trade']['max'])
                ->where("deleted",0)
                // ->orderBy("sales_leader","desc")
                // ->orderBy($data['sort'],"asc")
                ->orderBy("cost_trade","asc")
                ->get();
        }


        $products_ret = array();
        foreach($products as $p) {
            $el["id"]             = $p->id;
            $el["alias"]          = $p->getPathAliasForCatalog();
            $el["img"]            = $p->img;
            $el["name"]           = $p->name;
            $el["sales_leader"]   = $p->sales_leader;
            $el["sticker_promo"]  = $p->sticker_promo;
            $el["sticker_action"] = $p->sticker_action;
            $el["cost_trade"]     = $p->getFormatCost();
            $el["viewcost"]       = $p->viewcost;
            $el["sklad_kol"]       = $p->sklad_kol;
            $el["sklad_kol_post"]  = $p->sklad_kol_post;
            $products_ret[] = $el;
        }

        return response()->json($products_ret);

    }

    public function frame() {
        $filter = Request::all();

        $catalog = Catalog::find(1);

        return $catalog->getCountProductByFilterValues($filter);

    }

    public function cache_catalog($catalog_id) {
        $catalog = Catalog::find($catalog_id);
        $ob = $catalog->getCacheObject();
        $ob->set_cache_info();
        $ob->to_database();

    }

    public function test_cache($catalog_id) {
        $r = Request::all();
        $r['filter'] = json_decode($r['filter']);
        $filter = array();
         foreach ($r['filter'] as $value) {

            $name = str_replace("[]", "", $value->name);
            $filter[$name][] = $value->value;
        }

        $b_ids = array();
        if(isset($filter["brand"])) {
            $b_ids = $filter["brand"];
        }
        unset($filter["brand"]);
        $h_ids = array();
        foreach($filter as $f) {
          $h_ids = array_merge($f,$h_ids);
        }
        //dd($h_ids);
        $catalog = Catalog::find($catalog_id);

        $ob = $catalog->getCacheObject();
        //$ob->set_cache_info();
        //$ob->to_database();
        $ob->get_from_database_by_hid($h_ids,$b_ids);

        $new_filter = array();
        foreach($filter as $k=>$v) {
            $new_filter[$k] = $ob->get_merge_h_values($v);
            unset($filter[$k]);
        }
    }



    public function cache_all($catalog_id){


        $r = DB::delete('delete from filter_cache where catalog_id = ?', array($catalog_id));
        $catalog = Catalog::find($catalog_id);
        if (!is_null($catalog)) {
            $ob = $catalog->getCacheObject();
            $ob->set_cache_info();
            $ob->to_database();

        }
    }




}