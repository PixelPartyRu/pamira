<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use Illuminate\Html\HtmlBuilder;


class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function __construct() {


    }
    
    public function set_scripts($scripts) {
              $global_scripts = \Config::get('global_scripts.scripts');
        view()->share('global_scripts', $global_scripts);
        view()->share('scripts', $scripts); 
    }
    
    public function message_page($message) {
         return view("message",array("message" => $message));
    }

}
