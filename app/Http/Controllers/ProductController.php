<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Extensions\MyCrudController;
use Illuminate\Support\Facades\Request;
use Zofe\Rapyd\Facades\Rapyd;
use App\Catalog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\PHR;
use App\PH;

use App\Http\Controllers\Extensions\MyDataEdit as DataEdit;
use App\Http\Controllers\Extensions\MyDataGrid as DataGrid;

use App\ParseXmlFile;




class ProductController extends MyCrudController {

    public function __construct(\Lang $lang) {
        parent::__construct($lang);

    }
    public function all($entity) {
        parent::all($entity);

        $rd = Request::all();

       // $s = Product::where("catalog_id",$rd);
        $source = Product::query();
        if(isset($rd['catalog'])){
            $source = $source->where("catalog_id", intval($rd['catalog']));
        }


        $this->filter = \DataFilter::source($source);
        $cat_arr = array(""=>"Каталог");

        $cat_info = \App\Catalog::all();
        foreach($cat_info as $cat){
            $cat_arr[$cat->id] = $cat->name;
        }

        $brand_arr = array(""=>"Бренд");
        $brand_info = \App\Brand::all();
        foreach($brand_info as $b){
            $brand_arr[$b->id] = $b->title;
        }




       $this->filter->add('catalog_id','Каталог','select')->options($cat_arr);
       $this->filter->add('brand_id','Бренд','select')->options($brand_arr);
       $this->filter->add('name', 'Имя', 'text');
       $this->filter->add('article', 'Артикул', 'text');
       // $this->filter->add('article', 'Артикул', 'text');
       $this->filter->add('moderated','Модерация','select')->options(
               array("1" => "Проверенные","0" => "На модерации")
               );
        $this->filter->submit('Перейти');
        $this->filter->reset('Сброс');
        $this->filter->build();
        $this->grid = DataGrid::source($this->filter);

          $this->grid->add('id', 'ID', true);
          $this->grid->add('name','Имя',"name");
          $this->grid->add('catalog_for_admin_list()','Каталог размещения',"catalog_id");
          $this->grid->add('show_cost()','Отображать цену',"viewcost");
          $this->grid->add('cost','Цена товара',"cost");
          // $this->grid->add('is_sales_leader()','Лидер продаж',"sales_leader");
          $this->grid->add('is_sales_leader()','Рекомендуем',"sales_leader");
          $this->grid->add('has_img()','Фото',"img");


          //$this->grid->add('alias','Латинское имя для формирования пути',"text");
          //$this->grid->add('article','Артикул',"text");

          $this->addStylesToGrid();

//        //this->grid->add('name', 'Categories', 'button');
//        //dd($this->grid);
//
//        $this->addStylesToGrid();
//        $this->grid->add("name", "Название каталога", "text");
//        $this->grid->add('parent', "Дочерний каталог")->actions("all", array("catalog"));
        Rapyd::js("public/js/product_grid.js");
        $this->grid->paginate(500);
        return $this->returnView();
    }




    public function edit($entity) {

        session_start();

        parent::edit($entity);




        $this->edit = DataEdit::source(new \App\Product());
        $params = Request::all();

        if (isset($params["do_delete"])) {
            $this->edit->model->deleted = 1;
            $this->edit->model->save();
            return redirect("/panel/Product/all");
        }
        if (isset($params["modify"])) {

            $old_product = array();
            $old_product["id"] = $this->edit->model->id;
            $old_product["brand_id"] = $this->edit->model->brand_id;
            $old_product["catalog_id"] = $this->edit->model->catalog_id;
            $old_product["phs"] = $this->edit->model->getPHs();



            $_SESSION["product_update"] = $old_product;
        }

        if (isset($params["update"])) {


            $this->edit->model->save();
           // $this->edit->model->product_admin_update($_SESSION["product_update"], $params["catalog_id"]);
            $old_product = array();
            $old_product["id"] = $this->edit->model->id;
            $old_product["brand_id"] = $this->edit->model->brand_id;
            $old_product["catalog_id"] = $this->edit->model->catalog_id;
            $old_product["phs"] = $this->edit->model->getPHs();



            $_SESSION["product_update"] = $old_product;
        }


        //var_dump($params);
        //   var_dump($this->edit->model->getPHs());
        if (isset($params['save'])) {

            $phs = array();
            $this->edit->model->save();

            foreach ($params as $k => $v) {
                if (strpos($k, "_ph") && $v != 0) {

                    $this->edit->model->savePH(intval($v));
                }
            }
        }



        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('alias', 'Имя для формирования пути', 'text')->rule('required');
        $this->edit->add('haracteristic', 'Характеристика', 'redactor');


        $catalog_field = $this->edit->add('catalog_id', 'Каталог', 'App\Http\Controllers\Extensions\MySelect')
                ->options(\App\Catalog::lists("name", "id")->all());

        $this->edit->add('img', 'Картинка 1', 'image')->move('uploads/product/img1')->preview(100, 100);
        $this->edit->add('img2', 'Картинка 2', 'image')->move('uploads/product/img2')->preview(100, 100);
        $this->edit->add('img3', 'Картинка 3', 'image')->move('uploads/product/img3')->preview(100, 100);
        $this->edit->add('img4', 'Картинка 4', 'image')->move('uploads/product/img4')->preview(100, 100);

        $this->edit->add('brand_id', 'Бренд', 'select')->options(\App\Brand::lists("title", "id")->all());

        $this->edit->add('article', 'Артикул', 'text')->rule('required');
        $this->edit->add('country', 'Страна', 'text')->rule('required');
        $this->edit->add('cost_trade', 'Цена', 'text')->rule('required');
        $this->edit->add('cost', 'Оптовая цена', 'text');
        $this->edit->checkbox('sales_leader', 'Рекомендуем');
        $this->edit->checkbox('sales', 'Распродажа');
        $this->edit->checkbox('in_main_page', 'На главной странице');
        $this->edit->checkbox('moderated', 'Модерация');
        $this->edit->checkbox('viewcost', 'Отображать цену');


        //Характеристики
        $haracteristic = Product::$PRODUCT_HARACTERISTIC;






        Rapyd::js("public/js/admin_catalog.js");
        Rapyd::js("public/js/admin_product_haracteristic.js");

        //материал тест

        $catalog = $this->edit->model->catalog;
        if (is_null($catalog) && isset($params["catalog_id"])) {
            $catalog = Catalog::find(intval($params["catalog_id"]));
            $catalog_field->setValue($catalog->id);
        }

        if ($entity == "Product" && is_null($this->edit->model->catalog) && !isset($params["catalog_id"])) {
          //  return $this->viewWithMessage("Не выбран каталог");
        }
        if (!is_null($catalog)) {
            $attrs = $catalog->getAccessFilters();

            foreach ($attrs as $attr) {




                if ($attr->name == "color") {
                    $op = $catalog->getAccessFiltersValues("color")->lists("value", "id")->toArray();
                } else {
                    $op = $attr->values->toArray();
                }
                $f = $this->edit->add($attr->name . "_ph", $attr->label, 'App\Http\Controllers\Extensions\ProductHaracteristicField');
                $f->options($op);

                $val = $this->edit->model->getPhByName($attr->name);

                if (!is_null($val)) {

                    $f->setValue($val->id);
                }
            }
        }

        if ($this->edit->model->moderated == 0 && is_null($catalog)) {
            $phs = $this->edit->model->getPHs();

            $catalogs_a = array();
            foreach ($phs as $ph) {
                if( !empty( $ph->getCatalogs() ) ){
                     $catalogs_a[] = $ph->getCatalogs();
                }



                $op = array( 0 => "",$ph->id => $ph->value );
                $f = $this->edit->add($ph->name . "_ph", $haracteristic[$ph->name], 'App\Http\Controllers\Extensions\ProductHaracteristicField');
                $f->options($op);
                $f->setValue($ph->id);
            };
        }

        return $this->customEditView("admin.product_edit");
    }




    public function getCatalogList($parent_id,\Illuminate\Http\Request $request) {

        return Response::json(Catalog::where("parent_id",$parent_id)->lists("name", "id")->all());

    }

    public function add_product_haracteristic($product_haracteristic,$value,\Illuminate\Http\Request $request) {



        DB::table("product_haracteristic_".$product_haracteristic)->insert(array('name' => $value));
       return Response::json(array("id" => DB::table("product_haracteristic_".$product_haracteristic)->max('id')));


    }
    public function getProductPageById($product_id) {
        return $this->productPage( $product_id );

    }
    public function getProductPageByAlias($catalog,$product) {
        $product = Product::getByAlias($product);

        if(is_null($product) || $product->moderated == 0) return view("message",array("message" => "Товар не найден"));
        return $this->productPage( $product->id );
    }

    private function productPage($product_id) {


        $data = array();
        $product = Product::find($product_id);
        $data['product'] = $product;
        $data['product_analogs'] = [];
        if(!empty($product->analog))
        {
            $data['product_analogs'] = Product::where('analog', $product->analog)->where('id','!=',$product->id)->get();
        }
       // d($product);
       // d($data['product']->img4);
      //  $data[]
        if($product)
        {
            $data['product']->haracteristic = str_replace('&amp;', '', $data['product']->haracteristic);
        }
        $data['product_img'] = $product->getImageArr();


        // $gb_retail = $product->getFormatCost();
        $gb_retail = $product->getRoundCost();
        $gb_wholesale = $product->getCostWithMargin(true);

        // settype($gb_retail, "integer");
        // settype($gb_wholesale, "integer");

        $gb_retail = intval($gb_retail);
        $gb_wholesale = intval($gb_wholesale);

        $markup = ( $gb_retail / $gb_wholesale - 1 ) * 100;
        $gb_cost['markup'] = round($markup,2);

        return view("catalog.product_page",$data, $gb_cost);


    }

    public function search() {
        //Товар ищем по артикулу, наименованию, описанию
        $r = Request::all();


        $data['search_word'] = $r['search'];

        $data['products'] = Product::search($r['search']);
        //dd( $data['products'] );
        foreach($data['products'] as $p) {
            if( is_null($p->catalog) ){
                //d($p);
            }
            //d($p->catalog->alias);

        }
        //dd();
        return view("search_page",$data);
    }

    public function imgresize() {
        $r = Request::all();
        if(!isset($r['file'])) return;
        $file = $r['file'];
        $size = GetImageSize($file);
	if ($size[2]==1) $scr_img=ImageCreateFromGif($file);
	if ($size[2]==2) $scr_img=ImageCreateFromJpeg($file);
	if ($size[2]==3) $scr_img=ImageCreateFromPng($file);
	$scr_width = $size[0];
	$scr_height = $size[1];
	if($r==1) if($scr_width<300) $w=$scr_width; else $w=300;
	{
		if($scr_width<300) $w=$scr_width; else $w=300;
	}
	if($r==2) $w=80;
	if($r==3) $w=74;
	if($r==4) $w=200;
	if($r=='') $w=220;
	$dest = imagecreatetruecolor($w,$w);
	$white = imagecolorallocate($dest, 255, 255, 255);
	imagefill($dest, 0, 0, $white);
	if($scr_width>$scr_height)
	{
		$scr_width1=$w;
		$scr_height1=$scr_height/($scr_width/$w);
		$koord_y=($w-$scr_height1)/2;
        	imagecopyresampled($dest, $scr_img, 0, $koord_y, 0, 0, $scr_width1,$scr_height1, $scr_width, $scr_height);

	}
	if($scr_width<$scr_height)
	{
		$scr_height1=$w;
		$scr_width1=$scr_width/($scr_height/$w);
        	$koord_x=($w-$scr_width1)/2;
		imagecopyresampled($dest, $scr_img, $koord_x, 0, 0, 0, $scr_width1,$scr_height1, $scr_width, $scr_height);
	}
	if($scr_width==$scr_height)
	{
		imagecopyresampled($dest, $scr_img, 0, 0, 0, 0, $w,$w, $scr_width, $scr_height);
	}
	header("Content-type:image/png");
	ImagePng($dest);
	ImageDestroy($dest);
    }

    public function add_ph() {
        $r = Request::all();
        $ph = PH::create( array('name' => $r["type"],'value'=> $r["value"]) );
        $ph->add_catalog( intval($r["catalog_id"]) );
        $data["id"] = $ph->id;
        $catalog = Catalog::find(intval($r["catalog_id"]));
        $cc_ob = $catalog->getCacheObject();
        $cc_ob->update_cache($ph->id,"h");

        return Response::json( $data );

    }

    public function parse_xml(){

        if (Request::getMethod() == 'POST') {
            $file = Request::file("file");
            $destinationPath = 'uploads/xml'; // upload path
            $extension = $file->getClientOriginalExtension(); // getting image extension
            $fileName = rand(11111,99999).'.'.$extension; // renameing image
            $file->move($destinationPath, $fileName);
            //d($destinationPath);
            //d($fileName);

           // d($file);
            $path = $destinationPath."/".$fileName;

            $xml = new ParseXmlFile($path);
            $xml->load_xml();
            return redirect("/panel/Product/start_parse_script");

          //  d( $parts );

        }


        return view("admin.parse_xml");

    }

    public function start_parse_script() {
        $xml = new ParseXmlFile();


        $data["parts"] = $xml->get_parts();
        $data["count_product"] = $xml->getSize();
        return view("admin.parse_xml",$data);


    }

    public function update_by_xml() {
        $start = microtime(true);
        $info = array();
        $r = Request::all();
        $step = $r["step"];
        $info['step'] = $step;
        if($step == 0 ){
            //Все товары из прошлых сессий с нулевым каталого убираем
            DB::table('product')->where("catalog_id",10000)->update(['catalog_id' => 0]);
            DB::table('product_haracteristic')->truncate();
            DB::table('product_haracteristic_relation')->truncate();
            DB::table('brands')->update(['catalogs' => '']);
        }
        $xml = new ParseXmlFile();
        $info["products"] = $xml->update($step);
        $finish = microtime(true);

        $delta = $finish - $start;
        $info["sec"] = ceil($delta);
        $info["cur_count"] = $xml->getSize();
        return Response::json($info);



    }


    public function get_xml($xml_document) {


        foreach($products_name as $k=>$name){
            $attr = $products_attr[$k];
            $this->xml_product_to_base($name, $attr);

        }

    }

    public function getParseInfo() {
         $xml = new ParseXmlFile();
         $data["new"] = $xml->get_new_count();
         $data["update"]= $xml->get_update_count();
         $xml->clear_session();
         return Response::json($data);

    }

    public function catalog_cache() {
        $data = array();
        $data["catalog"] = Catalog::all();
        return view("admin.catalog_cache",$data);
    }

    public function null_catalog() {


        $data["products"] = Product::where("catalog_id",10000)->get();
        $data["catalog"] = Catalog::lists("name","id")->toArray();

        return view("admin.null_catalog",$data);

    }

    public function first_step_set_cat($id) {
        $product = Product::find($id);
       $catalog = Catalog::all();
        $ph_ids = $product->getPHsIDs();

        foreach ($catalog as $cat) {
            $res = DB::table("product_haracteristic")->where("catalogs", "like", "|<" . $cat->id . ">")->lists("id");
            $inter = array_intersect($res, $ph_ids);
            if(!empty($inter)) {
                $product->catalog_id = $cat->id;
                $product->moderated = 1;
                $product->save();
                return $cat->name;

            }


        }


        return null;
    }

    public function next_step_set_cat($id) {
                    $prod = Product::find($id);

            $barnd_catalogs  = $prod->brand->getCatalogs();
            $ph_catalogs = $prod->uniqPhCatalog();
            $result_catalog = !empty($barnd_catalogs)?array_intersect($barnd_catalogs, $ph_catalogs):$ph_catalogs;

            $res_arr = array();
            foreach($result_catalog as $res) {


                $catalog = Catalog::find($res);
                $prod_keys = $prod->getPHsNamesKeys();
                $catalog_kyes = $catalog->getAFTypesArray();


                foreach($prod_keys as $k=>$v) {
                    if(in_array($v,$catalog_kyes )) {
                        unset($prod_keys[$k]);
                    }
                }
                if(count($prod_keys) == 0) {
                    $res_arr[] = $res;
                }

                //





            }

            if(count($res_arr) == 1) {
                $prod->catalog_id = $res_arr[0];
                $prod->moderated = 1;
                $prod->save();
                $catalog = Catalog::find($res_arr[0]);
                return $catalog->name;
            }
            return null;

    }

    public function setCatalogsForPh($id) {
        $id = Request::get("pid");
        $product = Product::find($id);
        $phs = $product->getPHs();
        foreach($phs as $ph){
            $ph->add_catalog($product->catalog_id);
        }
    }


public function set_cat() {
        $id = Request::get("id");
        $product = Product::find($id);


        $res = $this->first_step_set_cat($id);
        if (!is_null($res)) {
            $this->setCatalogsForPh($id);
            return $res;
        }





        // d($product);
        // d($product->name);

        setlocale(LC_ALL, 'ru_RU.UTF-8');
        $name = trim(preg_replace('/[^А-Яа-я ]/ui', '', $product->name));
        $name = $this->get2words($name);

        $pr = Product::where("catalog_id", "!=", 0)->where("catalog_id", "!=", 10000)->where("name", "like", "%$name%")->get();

        $cat_pr_array = array();
        $cat_count_array = array();

        $pr->each(function($el) use(&$cat_pr_array, &$cat_count_array) {
            //if($el->catalog_id == 0) var_dump("0 catalof");
            if (isset($cat_count_array[$el->catalog_id]))
                $cat_count_array[$el->catalog_id] = $cat_count_array[$el->catalog_id] + 1;
            else {
                $cat_count_array[$el->catalog_id] = 1;
            }


            $cat_pr_array[] = $el->catalog_id;
        });

        $max_count_catalog = !empty($cat_count_array) ? $this->get_max_array($cat_count_array) : 0;




        $barnd_catalogs = $product->brand->getCatalogs();
        $ph_catalogs = $product->uniqPhCatalog();

        $no_empty_arr = array();
        if (!empty($barnd_catalogs))
            $no_empty_arr[] = $barnd_catalogs;
        if (!empty($ph_catalogs))
            $no_empty_arr[] = $ph_catalogs;
        if (!empty($cat_pr_array))
            $no_empty_arr[] = $cat_pr_array;
        $result_catalog = !empty($no_empty_arr) ? array_shift($no_empty_arr) : array();
        if (!empty($result_catalog)) {
            foreach ($no_empty_arr as $arr) {
                $result_catalog = array_intersect($result_catalog, $arr);
            }
        }




        if (count($result_catalog) == 1) {
            $catalog = array_shift($result_catalog);
            $product->catalog_id = $catalog;
            $product->moderated = 1;
            $product->save();
            $this->setCatalogsForPh($id);
            return Response::json(Catalog::find($catalog)->name);
        } else {
            if ($max_count_catalog == 0)
                return Response::json("<span style='color:red;'>Неопределен</span>");
            $product->catalog_id = $max_count_catalog;
            $product->moderated = 1;
            $product->save();
            $this->setCatalogsForPh($id);


            //var_dump($max_count_catalog);
            return Response::json(Catalog::find($max_count_catalog)->name);
        }
    }



    public function get2words($word){
            $new_word = "";
            $word_arr = explode(" ",$word);

            if(count($word_arr) > 1) {
                $new_word = $word_arr[0]." ".$word_arr[1];
            }
            else {
               $new_word =  $word_arr[0];
            }
            return $new_word;

        }
        public function get_max_array($arr) {
            $save_arr = $arr;
        $max = array_shift($save_arr);
        $index_max = 0;
        foreach ($arr as $k => $a) {
            if ($a > $max) {
                $index_max = $k;
                $max = $a;
            }
            if ($a == $max) {
                $index_max = $k;
                $max = $a;
            }
        }
        return $index_max;
    }

    public function set_new_cat() {
          $cid = Request::get("cid");
          $pid = Request::get("pid");
          $product = Product::find($pid);
          $product->catalog_id = $cid;
          $product->moderated = 1;
          $product->save();
          $this->setCatalogsForPh($pid);

    }

    public function sales_page() {


        $data["catalogs"] = Product::get_sales_catalogs();


        return view("sales",$data);
    }

    public function sales_page_catalog($catalog_alias) {
        $catalog = Catalog::getByAlias($catalog_alias);
        $data["products"] = Product::get_product_width_sales($catalog->id);
        $data["catalog_products"] = $catalog;
        return view("sales",$data);


    }

}



