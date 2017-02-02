<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Extensions\MyCrudController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\MyDataGrid as DataGrid;
use \Illuminate\Support\Facades\Schema;
use App\Dictionary;
use App\Http\Controllers\Extensions\MyDataEdit;
use Zofe\Rapyd\Facades\Rapyd;
use Illuminate\Support\Facades\Response;

class DictionaryController extends MyCrudController {

    public function all($entity) {
        parent::all($entity);

        $this->filter = \DataFilter::source(new Dictionary());

        $this->grid = \DataGrid::source($this->filter);


        $this->grid->add('id', 'ID', true)->style("width:100px");
        $this->addStylesToGrid();
        $this->grid->add("name", "Ключ", "text");
        $this->grid->add("value", "Значение", "text");

        $this->grid->paginate(1000);
        
        return $this->returnView();
    }

    public function edit($entity) {

        $r = Request::all();


        parent::edit($entity);


        $this->edit = MyDataEdit::source(new Dictionary());

        $this->edit->add('name', 'Ключ', 'text')->rule('required');
        $this->edit->add('value', 'Значение', 'textarea')->rule('required');


        return $this->returnEditView();
    }
}
