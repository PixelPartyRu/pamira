<?php

Route::get("cfp",function($id = null) {
    
    $data["catalog"] = App\Catalog::all();
    return view("catalog_c",$data);
    
});



Route::get('/start_app', function()
{
    //Тут выполняются все миграции и забиваются в базу стартовые данные
    $exitCode = Artisan::call('migrate', []);
    $exitCode = Artisan::call('panel:install', []);
    $id = new App\Http\Controllers\InsertData();
    $id->catalog();
    $migrate = new App\Http\Controllers\MigrateController();
    $migrate->first_level();
    $migrate->migrate_brand();

    return redirect("start_app2");
    
   // $id->admin_link();
   // $id->brand();
   // $id->content();
    
  //  $id->random_product();
    

    //
});

Route::get('/start_app2', function()
{


    $migrate = new App\Http\Controllers\MigrateController();

    $migrate->set_brand_for_product();

    return redirect("start_app3");
    
   // $id->admin_link();
   // $id->brand();
   // $id->content();
    
  //  $id->random_product();
    

    //
});

Route::get('/start_app3', function()
{


    $migrate = new App\Http\Controllers\MigrateController();

    $migrate->set_catalog_for_product_first_level();
    $migrate->set_catalog_for_product_second_level();
    //return redirect("start_app2");
    
   // $id->admin_link();
   // $id->brand();
   // $id->content();
    
  //  $id->random_product();
    

    //
});

Route::get('/start_app4/{cur_count?}', function($cur_count = null)
{
//Рекурсия)
    print "1";
    var_dump($cur_count);
    $migrate = new App\Http\Controllers\MigrateController();

    if( is_null($cur_count)) $cur_count = 0;

    $last_count = $migrate->set_attr($cur_count);
    $cur_count+=200;
    var_dump($last_count);
    var_dump($cur_count);
    if($last_count != 0 ) 
    {
        return redirect("start_app4/".$cur_count);
    }
    else {
        return redirect("start_app5");
    }
    


});

Route::get('/start_app5', function(){ 
    
    $migrate = new App\Http\Controllers\MigrateController();
    $migrate->set_catalog_for_product_five_level();
    

    
});





        Route::get("imgresize", "ProductController@imgresize");

//Миграция базы
Route::group(['middleware' => array(), 'prefix' => 'migrate'], function() {
    
    
        Route::get("get_old_info/{id}", "MigrateController@get_old_info");
        Route::get("dealer", "MigrateController@dealer_migrate");
        Route::get("users", "MigrateController@users_migrate");
        Route::get("test", "MigrateController@test");
        Route::get("first_level", "MigrateController@first_level");
        Route::get("migrate_brand", "MigrateController@migrate_brand");
        Route::get("set_brand_for_product", "MigrateController@set_brand_for_product");
        Route::get("set_catalog_for_product_first_level", "MigrateController@set_catalog_for_product_first_level");
        Route::get("set_catalog_for_product_second_level", "MigrateController@set_catalog_for_product_second_level");
        Route::get("set_catalog_for_product_third_level", "MigrateController@set_catalog_for_product_third_level");
        
        Route::get("set_attr", "MigrateController@set_attr");
        Route::get("set_catalog_for_product_five_level", "MigrateController@set_catalog_for_product_five_level");
        
        Route::get("custom/{method}", "MigrateController@custom");
        

        
    });

//cache_all_catalogs
Route::group(array('middleware' => ['web']), function() {
    

    


    /* Route::group(['middleware' => array(), 'prefix' => 'panel/Shares'], function() {
        Route::get("save_point_info", "SharesController@save_point_info");
        
    });*/
    
    /***************************/



    /*******************/
    Route::group(['middleware' => array(), 'prefix' => 'catalog'], function() {


         Route::get("catalog_test/{catalog}", "CatalogController@catalog_test");
        Route::get("{catalog}", "CatalogController@catalog");

        Route::get("cache_catalog/{catalog_id}", "CatalogController@cache_catalog");
        Route::get("test_cache/{catalog_id}", "CatalogController@test_cache");
        Route::get("cache_all/{catalog_id}", "CatalogController@cache_all");
        Route::post("frame", "CatalogController@frame");
        
        
        Route::get("test_param/{param}", "CatalogController@test_param");
        
        Route::get("test_catalog/{catalog_id}", "CatalogController@test_catalog");
        
        






//
//                        $request = \App::make('request');
//                d($request->url());
//                d($request->decodedPath());
//                 d($request->segments());
//                   d($request->is());
//                   d("routr");
//                   d($request->route());
//                          // die();
//
//                   
//                   
//                   
//                   
//                 
//                 
//                
//                d($request->path());






        

        
    });

    /***************************/

    Route::get("/", "MainPageController@index");
    
    Route::get("search", "ProductController@search");
    

    Route::get("getCatalogList/{parent_id?}", "ProductController@getCatalogList");
    Route::get("addProductHaracteristic/{product_haracteristic?}/{value?}", "ProductController@add_product_haracteristic");

    
    

    

     
    
    Route::group(['middleware' => array(), 'prefix' => 'content'], function() {
        
        Route::get("translit_alias/{val}", "ContentController@translit_alias");
        Route::get("article/{rubric?}", "ContentController@news_page");
        Route::get("article_page/{alias}", "ContentController@article_page");
    });
    
    Route::group(['middleware' => array(), 'prefix' => 'news'], function() {

        Route::get("", "ContentController@news");
        Route::get("{alias}", "ContentController@article_page");
    });
    
    Route::group(['middleware' => array(), 'prefix' => 'help'], function() {

        Route::get("", "ContentController@help");
        Route::get("{alias}", "ContentController@article_page");
    });


    Route::group(['middleware' => array(), 'prefix' => 'product_catalog'], function() {

        Route::get("filter", "CatalogController@filter");
        Route::get("getFilterProduct", "CatalogController@getFilterProduct");
        Route::get("product/{product_id?}", "ProductController@getProductPageById");
        Route::get("get/{catalog}/{product}", "ProductController@getProductPageByAlias");
                Route::get("test_filter", "CatalogController@test_filter");
    });
    
    
    //Бренды
    Route::group(['middleware' => array(), 'prefix' => 'brand'], function() {
         
        Route::get("{brand_alias}", "BrandController@brand_page");
        Route::get("{brand_alias}/{catalog_alias}", "BrandController@brand_catalog_page");
        Route::get("{brand_alias}/{catalog_alias}/{category}", "BrandController@brand_catalog_category");
        Route::get("{brand_alias}/{catalog_alias}/{category}/{product_alias}", "BrandController@brand_catalog_product");         
    });


    Route::group(['middleware' => array(), 'prefix' => 'insert'], function() {

        Route::get("random_product", "InsertData@random_product");
        Route::get("random_product2", "InsertData@random_product2");
        Route::get("test", "InsertData@test");
        Route::get("content", "InsertData@content");
        Route::get("catalog", "InsertData@catalog");
        Route::get("admin_link", "InsertData@admin_link");
        
    });



//Route::get("create_dealer_test", "Auth\AuthController@create_dealer_test"); 



    Route::get("test_auth", "Buy@test_auth");
    Route::get("logout_all", "Buy@logout_all");

    Route::group(['middleware' => array(), 'prefix' => 'customer'], function() {
        //базовый контроллер для группы
        Route::get("test_mail/{order_id}", "CustomerController@test_mail");
        Route::get("sendFormData", "CustomerController@createNewCustomer");
        Route::group(['middleware' => array("auth:web")], function() {
            Route::get("cart", "CustomerController@cart");
            Route::get("getOrderUserData", "CustomerController@getOrderUserData");
            
            Route::get("save_order_pdf/{order_id}", "CustomerController@save_order_pdf");
            Route::get("manager_mail_send/{order_id}", "CustomerController@manager_mail_send");

        });
    });

    //Общие методы для юзера и дилера
    Route::group(['middleware' => array(), 'prefix' => 'buy'], function() {
        Route::get("{product_id}", "Buy@buy");
        Route::get("remove_position/{id}", "Buy@remove_position");
        Route::group(['middleware' => array(), 'prefix' => 'dealer'], function() {
            Route::get("save_dealer_cart_ajax", "Buy@save_dealer_cart_ajax");
            Route::get("remove_position/{id}", "Buy@remove_position");
        });
    });



    Route::group(['middleware' => array(), 'prefix' => 'dealer'], function() {
        Route::get("auth/{pass}", "DealerController@auth");
        Route::group(['middleware' => array("auth:dealer"), 'prefix' => ''], function() {
            Route::get("logout", "DealerController@logout");
            Route::get("margin_list", "DealerController@marginList");
            //Создание наценки
            Route::get("margin_create", "DealerController@marginCreate");
            Route::post("margin_create", "DealerController@marginCreate");
            //Редактирование наценки
            Route::get("margin_edit/{margin_id}", "DealerController@margin_edit");
            Route::post("margin_edit/{margin_id}", "DealerController@margin_edit");


            Route::get("prices", "DealerController@yandexMarketPrices");

            // Route::group(array('middleware' => array()), function() {
            //Роуты с определением шага заказа    
            Route::get("cart", "DealerController@cart_step"); //шаг 1
            Route::post("cart", "DealerController@cart_step");
            // Route::get("formalize_order_cart", "DealerController@formalize_order_cart"); //шаг 2
            // Route::get("formalize_order_completion", "DealerController@formalize_order_completion"); //шаг 3
            // Route::post("formalize_order_completion", "DealerController@formalize_order_completion");
            // });




            Route::any("option_completed_order/{type}", "DealerController@option_completed_order");
            Route::any("save_order", "DealerController@save_order");
            
            Route::any("save_order_pdf_by_id/{id}", "DealerController@save_order_pdf_by_id");
            Route::any("save_client_order_pdf_by_id/{id}", "DealerController@save_client_order_pdf_by_id");
            Route::any("caterer_mail_send_by_id/{id}", "DealerController@caterer_mail_send_by_id");
            


            

            Route::get("set_default_margin/{margin_id}", "DealerController@set_default_margin");
            Route::get("delete_margin/{margin_id}", "DealerController@delete_margin");

            Route::any("order_products_order", "DealerController@order_products_order");

            Route::get("order_history", "DealerController@order_history");
            Route::get("completed_order/{order_id}", "DealerController@completed_order");
            Route::get("remove_order/{order_id}", "DealerController@remove_order");
            Route::get("save_order_pdf/{order_id}", "DealerController@save_order_pdf");
            Route::get("restore_order_in_cart/{order_id}", "DealerController@restore_order_in_cart");
            
            Route::any("save_compare_pdf", "DealerController@save_compare_pdf");
        });
    });

   //Для админки
    Route::group(['middleware' => array("auth:panel"), 'prefix' => 'admin'], function() {
        
               Route::get("add_ph", "ProductController@add_ph");  
    
        
        
    });

    
    
    
});

Route::get('glide/{path}', function($path){
        $server = \League\Glide\ServerFactory::create([
            'source' => app('filesystem')->disk('public')->getDriver(),
            'cache' => storage_path('glide'),
        ]);
        return $server->getImageResponse($path, Input::query());
    })->where('path', '.+');
