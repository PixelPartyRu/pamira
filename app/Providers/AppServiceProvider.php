<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use hisorange\BrowserDetect\Facade\Parser;
use Illuminate\Support\Facades\Request;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $link = Request::path();
        $global_scripts = \Config::get('global_scripts.scripts');
        view()->share('global_scripts', $global_scripts);
        view()->share('is_main_page', "false");
        view()->share('cur_path', $link);
        view()->share('user_agent', $this->GetUserAgent());
        if (Schema::hasTable('region') && Schema::hasTable('brands') && Schema::hasTable('catalog') && Schema::hasTable('users')) {
            view()->share('catalog', \App\Catalog::all());
            view()->share('brands', \App\Brand::where("main_page",1)->get());
            
            view()->share('brands_right', \App\Brand::where("main_page",1)->get());
                  view()->share('regions', \App\Region::all());

        }
        //var_dump(\Illuminate\Support\Facades\Auth::guard("dealer")->user());

    }
    
    public function GetUserAgent() {
$result = Parser::detect();




return strtolower($result["browserFamily"]);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
