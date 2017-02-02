<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Catalog;
use App\Product;
use App\PH;
use App\Brand;
use App\Content;
use App\Region;

Class InsertData extends Controller {
    public function admin_link() {
        \Serverfireteam\Panel\Link::create(array("display" => "Список значений","url" => "Brand"));
        \Serverfireteam\Panel\Link::create(array("display" => "Бренды","url" => "Brand"));
        \Serverfireteam\Panel\Link::create(array("display" => "Каталог","url" => "Catalog"));
        \Serverfireteam\Panel\Link::create(array("display" => "Товары","url" => "Product"));
        \Serverfireteam\Panel\Link::create(array("display" => "Контент ( статьи и страницы )","url" => "Content"));
    }
    
    public function content() {
        Content::create(array(
            'name' => "Доставка",'img' => "",'text' => "",'type' => "page","alias" => "delivery"
        ));
        Content::create(array(
            'name' => "Установка", 'img' => "", 'text' => "", 'type' => "page", "alias" => "installation"
        ));
        Content::create(array(
            'name' => "Гарантия", 'img' => "", 'text' => "", 'type' => "page", "alias" => "warranty"
        ));
        Content::create(array(
            'name' => "О компании", 'img' => "", 'text' => "", 'type' => "page", "alias" => "about"
        ));
        Content::create(array(
            'name' => "Контакты", 'img' => "", 'text' => "", 'type' => "page", "alias" => "contacts"
        ));

    }
    public function region() {
        
        
    }
    
    public function set_catalog_filter($filter) {
        $ph_arr = array_flip( Product::$PRODUCT_HARACTERISTIC );
        foreach($filter as &$f){
            $f = $ph_arr[$f];
        }
        return implode("|",$filter);
        
    }

    public function add_ph($data,$catalog_id) {
        $ph_arr = array_flip( Product::$PRODUCT_HARACTERISTIC );
        foreach($data as $k=>$hars) {
            foreach($hars as $h) {
                  $ph = PH::firstOrCreate( array( "name" => $ph_arr[$k],"value" => $h) );
                  $ph->add_catalog($catalog_id);
                  
            }

        }
        
        
    }
    
    private function add_brand($catalog_id, $brand_names) {
        foreach ($brand_names as $brand_name) {
            $brand = Brand::getByName($brand_name);
            if (is_null($brand)) {
                $brand = Brand::create(array("title" => $brand_name, "main_page" => 0));
            }
            $brand->add_catalog($catalog_id);
           // var_dump($catalog_id);
        }
    }

    public function catalog() {

        //Создание каталога с доступными фильтрами

        $c = Catalog::create(["name" => "Кухонные мойки"]);
        $filter = array("Материал", "Тип", "Стиль", "Шкаф для монтажа (мм)", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        
        $data["Тип"] = array("Врезная", "Под столешницу", "Вровень со столешницей");
        $data["Материал"] = array("Нержавеющая сталь", "Гранит", "Кварц", "Керамика", "Стекло");
        $data['Стиль'] = array("Классический", "Модерн");
        $data['Шкаф для монтажа (мм)'] = array("300", "400", "450", "500", "600", "800", "900", "Угловой");
        $data['Ширина (мм)'] = array("");
        $data['Цвет'] = array("Алюминий", "Антик", "Антрацит",
            "Арктик", "Базальт", "Бежевый",
            "Белый", "Бронза", "Ваниль", "Графит",
            "Жасмин", "Классик", "Кремовый", "Латунь",
            "Малахит", "Медь", "Миндаль", "Нержавеющая сталь",
            "Овес", "Оникс", "Песок", "Пирит", "Сахара", "Серебристый",
            "Серый", "Состаренное серебро", "Черный", "Шоколад");
        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("Franke","Smeg","Granula","Zorg");
        $this->add_brand($c->id,$brand_arr);
        






        $c = Catalog::create(array("name" => "Кухонные смесители"));
        $filter = array("Тип", "Стиль", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("С выдвижным шлангом", "Без выдвижного шланга", "С подключением к фильтру");

        $data["Стиль"] = array("Классический", "Модерн");
        $data["Цвет"] = array("Алюминий", "Антик", "Антрацит"
            , "Арктик", "Базальт", "Бежевый", "Белый",
            "Бронза", "Ваниль", "Графит", "Жасмин", "Золото",
            "Классик", "Кремовый", "Латунь", "Малахит", "Медь",
            "Миндаль", "Нержавеющая сталь", "Никель", "Овес",
            "Оникс", "Песок", "Пирит", "Сахара", "Серебристый", "Серый",
            "Титан", "Хром", "Черный", "Шоколад");
        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("Franke","Smeg","Granula","Zorg");
        $this->add_brand($c->id,$brand_arr);



        $c = Catalog::create(array("name" => "Дозаторы мыла"));
        $filter = array("Стиль", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();

        $data["Стиль"] = array("Классический", "Модерн");
        $data["Цвет"] = array("Бронза", "Нержавеющая сталь", "Хром");
        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("Franke","Smeg","Zorg");
        $this->add_brand($c->id,$brand_arr);
        
        
        

        $c = Catalog::create(array("name" => "Измельчители и сортеры"));
        $filter = array("Тип");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Измельчители пищевых отходов", "Системы сортировки отходов");
        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("Franke","InSinkErstor");
        $this->add_brand($c->id,$brand_arr);

        $c = Catalog::create(array("name" => "Духовые шкафы"));
        $filter = array("Материал", "Тип", "Стиль", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();

        $data["Тип"] = array("Электрический", "Газовый", "С приготовлением на пару", "С режимом микроволн");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Материал"] = array("Латунь", "Медь", "Нержавеющая сталь", "Стекло", "Эмаль");
        $data["Цвет"] = array("Антрацит", "Бежевый", "Белый", "Бордовый", "Бронза", "Ваниль", "Голубой",
            "Графит", "Жасмин", "Коричневый", "Кремовый",
            "Латунь", "Медь", "Нержавеющая сталь", "Овес", "Песочный", "Розовый", "Сахара", "Серебристый", "Серый", "Слоновая кость", "Черный");
        $data["Ширина (мм)"] = array("");
        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("AEG","Asko","Electrolux","Franke","Fulgor","Gorenje",
            "Gorenje+" ,"Kuppersbusch","Midea","Smeg","Whirlpool","Zanussi");
        $this->add_brand($c->id,$brand_arr);



        $c = Catalog::create(array("name" => "Варочные поверхности"));
        $filter = array("Материал", "Тип", "Стиль", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Газовые", "Электрические", "Индукционные", "Серия домино");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Материал"] = array("Нержавеющая сталь", "Стекло", "Стеклокерамика", "Эмаль");
        $data["Цвет"] = array("Антрацит", "Бежевый", "Белый"
            , "Бронза", "Ваниль", "Графит", "Жасмин", "Коричневый", "Кремовый", "Латунь", "Медь",
            "Нержавеющая сталь", "Овес",
            "Песочный", "Сахара", "Серебристый", "Серый", "Слоновая кость", "Черный");
        $data["Ширина (мм)"] = array("");

        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("AEG","Asko","Electrolux","Franke",
            "Fulgor","Gorenje","Gorenje+","Kuppersbusch","Midea","Smeg","Whirlpool","Zanussi");
        $this->add_brand($c->id,$brand_arr);


        $c = Catalog::create(array("name" => "Микроволновые печи"));
        $filter = array("Материал", "Тип", "Стиль", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();

        $data["Тип"] = array("Встраиваемая", "Отдельностоящая");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Материал"] = array("Латунь", "Нержавеющая сталь", "Стекло", "Эмаль");
        $data["Цвет"] = array("Антрацит", "Белый", "Бронза",
            "Ваниль", "Графит", "Кремовый", "Латунь", "Нержавеющая сталь", "Серебристый",
            "Серый", "Слоновая кость", "Черный");
        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("AEG","Asko","Electrolux","Franke","Fulgor","Gorenje",
            "Gorenje+","Kuppersbusch","Smeg","Whirlpool","Zanussi");
        $this->add_brand($c->id,$brand_arr);


        $c = Catalog::create(array("name" => "Компактные приборы"));
        $filter = array("Материал", "Тип", "Стиль", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();


        $data["Тип"] = array("Духовые шкафы",
            "Духовые шкафы с приготовлением на пару",
            "Духовые шкафы с режимом микроволн",
            "Кофемашины", "Пароварки",
            "Подогреватели посуды", "Телевизоры");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Материал"] = array("Нержавеющая сталь", "Стекло", "Эмаль");
        $data["Цвет"] = array("Антрацит", "Белый", "Кремовый", "Латунь",
            "Нержавеющая сталь", "Серебристый", "Серый", "Черный");



        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("AEG","Asko" ,"Electrolux","Fulgor",
            "Gorenje", "Gorenje+","Kuppersbusch","Smeg","Zanussi");
        $this->add_brand($c->id,$brand_arr);


        $c = Catalog::create(array("name" => "Вытяжки"));
        $filter = array("Материал", "Тип", "Стиль", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Настенные", "Островные", "Угловые", "Встраиваемые");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Материал"] = array("Дерево", "Медь", "Нержавеющая сталь", "Пластик", "Стекло", "Эмаль");
        $data["Цвет"] = array("Антрацит", "Бежевый", "Белый", "Ваниль", "Венецианская штукатурка", "Вишня"
            , "Голубой", "Графит");
        $data["Ширина (мм)"] = array("");



        $this->add_ph($data, $c->id);
        $data = array();
        $brand_arr = array("Asko","Elica","Faber","Franke","Fulgor","Gorenje",
            "Gorenje+","Kuppersbusch", "Smeg");
        $this->add_brand($c->id,$brand_arr);


        $c = Catalog::create(array("name" => "Посудомоечные машины"));
        $filter = array("Материал", "Тип", "Стиль", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Встраиваемая", "Отдельностоящая");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Материал панели управления"] = array("Нержавеющая сталь", "Пластик");
        $data["Цвет"] = array("");
        $data["Ширина (мм)"] = array("450", "600", "900");
        $this->add_ph($data, $c->id);
        $data = array();



        $c = Catalog::create(array("name" => "Стиральные машины"));
        $filter = array("Тип", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Встраиваемая", "Отдельностоящая");
        $data["Цвет"] = array("");
        $data["Ширина (мм)"] = array("");

        $this->add_ph($data, $c->id);
        $data = array();



        $c = Catalog::create(array("name" => "Сушильные машины"));
        $filter = array("Тип", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Встраиваемая", "Отдельностоящая");
        $data["Цвет"] = array("");
        $data["Ширина (мм)"] = array("");



        $this->add_ph($data, $c->id);
        $data = array();



        $c = Catalog::create(array("name" => "Сушильные шкафы"));
        $filter = array("Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Цвет"] = array();
        $data["Ширина (мм)"] = array();



        $this->add_ph($data, $c->id);
        $data = array();


        $c = Catalog::create(array("name" => "Холодильное оборудование"));
        $filter = array("Материал", "Тип", "Стиль", "Прибор", "Ширина (мм)", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Встраиваемые", "Отдельностоящие");
        $data["Прибор"] = array("Винные шкафы", "Морозильные камеры", "Холодильные камеры", "Холодильники двухкамерные");
        $data["Стиль"] = array("Классический", "Модерн");
        $data["Цвет"] = array("");
        $data["Материал"] = array("");
        $data["Ширина (мм)"] = array("");



        $this->add_ph($data, $c->id);
        $data = array();



        $c = Catalog::create(array("name" => "Посуда для приготовления"));
        $filter = array("Материал", "Тип", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("Сковороды", "Кастрюли", "Сотейники", "Крышки", "Наборы посуды");
        $data["Цвет"] = array("");
        $data["Материал"] = array("");
        $data["Цвет"] = array("");



        $this->add_ph($data, $c->id);
        $data = array();



        $c = Catalog::create(array("name" => "Мелкая бытовая техника"));
        $filter = array("Материал", "Тип", "Цвет");
        $c->filter = $this->set_catalog_filter($filter);
        $c->save();
        $data["Тип"] = array("");
        $data["Цвет"] = array("");
        $data["Материал"] = array("");



        $this->add_ph($data, $c->id);
        $data = array();

    }


    
public function brand() {
    
           for($i = 0; $i < 10;$i++) {
                $brand = new Brand();
                $brand->title = "Бренд $i";
                $brand->img = "faber.png";

                $brand->save();
            }
}
    public function random_product() {
        

           /* for($i = 0; $i < 10;$i++) {
                $brand = new Brand();
                $brand->title = "Бренд $i";
                $brand->img = "faber.png";

                $brand->save();
            }
*/
        
        $brand = \App\Brand::lists("id")->all();
        $catalog = \App\Catalog::lists("id")->all();

        
        for ($i = 0; $i < 100; $i++) {
            $product = new Product;
            $product->name = str_random(20);
            $product->in_main_page = random_int(0, 1);
            $product->cost_trade = random_int(30, 40000);
            $product->moderated = 1;
            $product->save();
        }



        $products = Product::all();

        foreach($products as $product) {
            
            $product->brand_id = $brand[random_int(0, count($brand)-1)];

            $product->alias = $product->name;
            $product->country = "Russia";
            $product->article = "11111111";
                        $product->img = "imgresize_1.png";
            $rand = random_int(1, count($catalog)-1);

            $product->catalog_id = $rand; 
            $catalog_elem = Catalog::find($product->catalog_id);

           $filters = $catalog_elem->getAccessFilters();
          
            foreach ($filters as $f) {
                if (empty($f->values)) continue;
                if (!is_array($f->values)) {
                    $values = $f->values->toArray();
                    if (!empty($values)) {
                        $rand = array_rand($values);
                       // var_dump($values);
                        //var_dump($rand);
                        if (!is_null($rand)) $product->savePH(intval($rand));
                    }
                }
            }






                //}
            
           
            
            $product->cost_trade = random_int(30,40000);

            //dd($product);
            $product->save();
  
            
        
                
        
    }
    
    
    


}


    public function random_product2() {
        


        
        $brand_id = 1;
        $catalog_id = 1;

        
       /* for ($i = 0; $i < 100; $i++) {
            $product = new Product;
            $product->name = str_random(20);
            $product->in_main_page = random_int(0, 1);
            $product->cost_trade = random_int(30, 40000);
            //$product->save();
        }*/



        $products = Product::all();

        foreach($products as $product) {
            
            $product->brand_id = $brand_id;

            $product->alias = $product->name;
            $product->country = "Russia";
            $product->article = "11111111";
            $product->img = "imgresize_1.png";


            $product->catalog_id = $catalog_id; 
            $catalog_elem = Catalog::find($product->catalog_id);
            $filters = $catalog_elem->getAccessFiltersValues("type")->get()->toArray();

                         $rand = array_rand($filters);
                         d($rand);
                         d($filters[$rand]);

                        if (!is_null($filters[$rand])) $product->savePH($filters[$rand]["id"]);
                        
                      //  d($product->getPhByName($rand));
                        
                        


            
           $product->cost_trade = random_int(30,40000);

            //dd($product);
            $product->save();
            d($product->getPhByName("type")->value);
  
            
        
                
        
    }
    
    
    


}

}
