<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Extensions\MyCrudController;
use App\Banner;
use App\Catalog;
use App\Jobs\Helper;
use Product;
use Illuminate\Support\Facades\Request;
use App\PHR;



class BannerController extends MyCrudController {
    
    public function __construct(\Lang $lang) {
        parent::__construct($lang);

        
        view()->share('path', Request::path());
    }
    public function all($entity){
        parent::all($entity); 
        $this->filter = \DataFilter::source(new \App\Banner());
        $this->filter->add('id', 'ID', 'text');
        $this->filter->add('title', 'Название', 'text');
        $this->grid = \DataGrid::source($this->filter);
        $this->grid->add('id','ID', true)->style("width:100px");
        $this->grid->add('title','Название');
        $this->addStylesToGrid();          
        $this->grid->paginate(1000);
        return $this->returnView();
    }
    
    public function  edit($entity){
        
        parent::edit($entity);
        $this->edit = \DataEdit::source(new \App\Banner());
        $this->edit->label('Редактирование баннера');
        $this->edit->add('title','Название', 'text')->rule('required');
        $this->edit->add('img', 'Баннер', 'image')->move('uploads/banners/')->preview(100,100);
        $this->edit->add('url','Ссылка', 'text')->rule('required');
        $this->edit->add('main_page', 'Отображать на главной', 'checkbox');
        //$this->edit->add('description', 'Описание', 'redactor');
        return $this->returnEditView();
    }

}