<?php


Route::group(array('middleware' => ['web']), function() {
    
   Route::group(['middleware' => array('shares'), 'prefix' => 'shares'], function() {
       

        Route::get("", "SharesController@shares_page");
        Route::any("share/{id}", "SharesController@share_info_page");
        Route::any("share_registration", "SharesController@share_registration");
        Route::get("logout", "SharesController@logout");
        Route::get("isHuman/{code}", "SharesController@isHuman");
        Route::get("captcha", "SharesController@captcha");

    });
    
    Route::group(['middleware' => array("auth:web"), 'prefix' => 'compare_user'], function() {
        
        Route::get("add_to_compare/{pid}/{type}", "Buy@add_to_compare");
       
        
        
    });
    
    Route::group(['middleware' => array("auth:dealer"), 'prefix' => 'compare_dealer'], function() {

        Route::get("add_to_compare/{pid}/{type}", "Buy@add_to_compare");
        Route::get("order_compare_list", "Buy@order_compare_list");
        Route::get("remove_position_compare/{pid}/{type}", "Buy@remove_position_compare");
        Route::get("clear_compare_list", "Buy@clear_compare_list");
        
    });



    Route::get("test/{method}", "TestController@test");
   // Route::get("test/{method}/{param}", "TestController@test");
    
    $base_method = "test/{method}";
    for($i = 0; $i < 10;$i++) {
        $base_method.="/{param$i}";
        Route::get($base_method, "TestController@test");
        
    }
    
    Route::get("params/{method}", "CatalogsParametrs@method");
    
});

Route::get("sales", "ProductController@sales_page");
Route::get("sales/{catalog_alias}", "ProductController@sales_page_catalog");

?>
