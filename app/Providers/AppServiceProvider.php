<?php

namespace App\Providers;

use App\YaMarket;
use Illuminate\Support\Facades\Config;
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
    public function boot()
    {
        $link = Request::path();
        $global_scripts = \Config::get('global_scripts.scripts');
        view()->share('global_scripts', $global_scripts);
        view()->share('is_main_page', "false");
        view()->share('cur_path', $link);
        view()->share('user_agent', $this->GetUserAgent());
        view()->share('is_any_sales_now', \App\Product::is_any_sales_now());
        
        $view = view();
        foreach(\App\Dictionary::getAll() as $item) {
            $view->share($item->name, $item->value);
        }

        $view = view();
        foreach (\App\Dictionary::getAll() as $item) {
            $view->share($item->name, $item->value);
        }

        if (Schema::hasTable('region') && Schema::hasTable('brands') && Schema::hasTable('catalog') && Schema::hasTable('users')) {
            view()->share('catalog', \App\Catalog::orderBy('order', 'asc')->get());
            view()->share('brands', \App\Brand::where("main_page", 1)->get());

            view()->share('brands_right', \App\Brand::where("main_page", 1)->get());
            view()->share('regions', \App\Region::all());

        }
        //var_dump(\Illuminate\Support\Facades\Auth::guard("dealer")->user());

    }

    public function GetUserAgent()
    {
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
        $this->app->bind(YaMarket::class, function($app) {
            $shop_name = Config::get('yandex-market.shop_name');
            $company_name = Config::get('yandex-market.company_name');
            $company_url = Config::get('yandex-market.url');
            $oauth_token = Config::get('yandex-market.token');
            $client_id = Config::get('yandex-market.client_id');

            return new \App\YaMarket($shop_name, $company_name, $company_url, $oauth_token, $client_id);
        });
    }
}
