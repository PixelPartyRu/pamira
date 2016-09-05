<?php

namespace App\Providers;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
       // d($this->namespace);
        $router->group(['namespace' => $this->namespace], function ($router) {
            
         //   $r = Request::url();
         //   d($r);
          //  d(Request::method());
          //  d(Request::path());
            
            
            require app_path('Http/routes.php');
            require app_path('Http/routes2.php');

           
        });
        
    }
}
