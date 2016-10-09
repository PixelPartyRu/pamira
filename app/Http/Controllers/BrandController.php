<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Extensions\MyCrudController;
use App\Brand;
use App\Catalog;
use App\Jobs\Helper;
use Product;
use Illuminate\Support\Facades\Request;
use App\PHR;



class BrandController extends MyCrudController {

    public function __construct(\Lang $lang) {
        parent::__construct($lang);


        view()->share('path', Request::path());
    }
    public function all($entity){
        parent::all($entity);
        $this->filter = \DataFilter::source(new \App\Brand());
        $this->filter->add('id', 'ID', 'text');
        $this->filter->add('title', 'Title', 'text');
        $this->grid = \DataGrid::source($this->filter);
        $this->grid->add('id','ID', true)->style("width:100px");
        $this->grid->add('title','Title');
        $this->addStylesToGrid();
        $this->grid->paginate(1000);
        return $this->returnView();
    }

    public function  edit($entity){

        parent::edit($entity);
        $this->edit = \DataEdit::source(new \App\Brand());
        $this->edit->label('Edit Pages');
        $this->edit->add('title','Title', 'text')->rule('required');
        $this->edit->add('alias','Имя для формирования пути(латинское имя)', 'text')->rule('required');
        $this->edit->add('img', 'Картинка бренда', 'image')->move('uploads/brands/')->preview(100,100);
        $this->edit->add('main_page', 'Отображать на главной', 'checkbox');
        $this->edit->add('order','Порядок', 'text')->rule('required:numeric');
        $this->edit->add('description1', 'Слоган', 'redactor');
        $this->edit->add('description2', 'Seo', 'redactor');
        return $this->returnEditView();
    }


    public function brand_page($brand_alias) {

        $brand = Brand::getByAlias($brand_alias);
        if(is_null($brand)) {
        return view("message",array("message" => "Страница не найдена"));

        }
        //dd($brand->getCategories());
        $data['brand'] = $brand;
        $data['catalogs'] = $brand->getCategories();
        return view("brand.brand_page",$data);



    }

    public function brand_catalog_page($brand_alias,$catalog_alias) {

        $data = array();
       // var_dump($brand_alias);
       // var_dump($catalog_alias);
        $catalog = Catalog::getByAlias($catalog_alias);
        $categories =  $catalog->getAccessFiltersValues("type")->get();
        if($categories->count() == 0) {
            return $this->brand_products_by_catalog($brand_alias,$catalog_alias);
        }
     //  dd( $categories_p->get() );
        $data['brand'] = Brand::getByAlias($brand_alias);
        $data['cur_catalog'] = $catalog;

        foreach ($categories as $k => $cat) {
            $count = \App\Product::where("brand_id", $data["brand"]->id)
                    ->where("catalog_id", $data['cur_catalog']->id)
                    ->where("deleted",0)
                    ->filter(array("type" => $cat->id))
                    ->count();
            if ($count == 0) {
                unset($categories[$k]);
            }
        }
        if($categories->count() == 0) {
            return $this->brand_products_by_catalog($brand_alias,$catalog_alias);
        }
        $data["categories"] = $categories;
        $data["images"] = array();
        foreach($categories as $i=>$cat) {
            $img = $catalog->getRandCategoryImg($cat->id,$data['brand']->id);
            $data["images"][$i] = !is_null($img)?"/uploads/product/img1/".$img:"/img/no_img.jpg";

        }


        return view("brand.brand_catalog_page",$data);
    }
    public function brand_products_by_catalog($brand_alias,$catalog_alias) {

        $data = array();
        $data["brand"] = Brand::getByAlias($brand_alias);
        $data['cur_catalog'] = Catalog::getByAlias($catalog_alias);
        $data['products'] = \App\Product::where("brand_id", $data["brand"]->id)
                        ->where("catalog_id", $data['cur_catalog']->id)
                        ->where("deleted",0)
                        // ->orderBy("name","asc")
                        ->orderBy("cost_trade","asc")
                        ->paginate(12);//get()
        $data['brand_alias'] = true;

        return view("brand.brand_catalog_category", $data);
    }
    public function brand_catalog_category($brand_alias,$catalog_alias,$category) {

        $data = array();
        $data["brand"] = Brand::getByAlias($brand_alias);
        $data['cur_catalog'] = Catalog::getByAlias($catalog_alias);
        $data["category"] = $data['cur_catalog']->getCategotyByAlias($category);
        $data["category_alias"] =  $category;
        $data['products'] = \App\Product::where("brand_id",$data["brand"]->id)
                ->where("catalog_id",$data['cur_catalog']->id)
                ->where("deleted",0)
                ->filter(array("type"=>$data["category"]->id))
                // ->orderBy("name","asc")->paginate(12);//->get()
                ->orderBy("cost_trade","asc")
                ->paginate(12);//->get()
        $data['brand_alias'] = true;
        return view("brand.brand_catalog_category",$data);

    }

    public function brand_catalog_product($brand_alias,$catalog_alias,$category,$product_alias) {

        $data["brand"] = Brand::getByAlias($brand_alias);
        $data['cur_catalog'] = Catalog::getByAlias($catalog_alias);

        if($category !== "_") {

        $data["category"] = $data['cur_catalog']->getCategotyByAlias($category);
        $data["category_alias"] =  $category;
        }
        $product = \App\Product::getByAlias($product_alias)
                            ->where("deleted",0);
        if (is_null($product) || $product->moderated == 0) return view("message", array("message" => "Товар не найден"));
        $data['product'] = $product;
        $data['product_img'] = $product->getImageArr();

        return view("brand.product_page", $data);
    }




}