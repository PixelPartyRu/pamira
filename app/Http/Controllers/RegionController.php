<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Extensions\MyCrudController;
use App\Http\Controllers\Extensions\MyDataEdit;
use App\Http\Controllers\Extensions\MyDataGrid as DataGrid;
use App\Region;


class RegionController extends MyCrudController {
    
    public function all($entity){
        parent::all($entity); 
        $this->filter = \DataFilter::source(Region::query());
        $this->grid = DataGrid::source($this->filter);
        $this->grid->add('id','ID', true)->style("width:100px");
        $this->grid->add("name","Название региона","text");
        $this->addStylesToGrid(); 
        $this->grid->paginate(1000);
        return $this->returnView();
          
    }
    
    public function edit($entity) {
        parent::edit($entity);
        $this->edit = MyDataEdit::source(new Region());
        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('email_for_dealer', 'Email для дилера', 'text')->rule('required');
        $this->edit->add('email_for_cutomer', 'Email для покупателя', 'text')->rule('required');
         return $this->returnEditView();
    }
    
}