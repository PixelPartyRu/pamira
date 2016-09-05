<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Banner;
class MainPageController extends Controller {
    
    public function __construct() {
        parent::__construct();
        
    }
    
    public function index() {
        //var_dump( \Illuminate\Support\Facades\Auth::guard("dealer")->user());
        $data['products'] = \App\Product::main_page_product();
        $data['is_main_page'] = true;
        $data['banners'] = Banner::where('main_page', 1)->take(3)->get();
        $data["share"] = \App\Shares::where("type","shares")->get();
        
        return view("mainpage",$data);
        
    }
    
}