<?php

namespace App\Http\Controllers\Extensions;

use Zofe\Rapyd\DataGrid\DataGrid;
use Illuminate\Support\Facades\View;
use Zofe\Rapyd\Persistence;
use Illuminate\Support\Facades\Config;
use Zofe\Rapyd\DataGrid\Row;
use Zofe\Rapyd\DataGrid\Cell;
use Illuminate\Support\Facades\Request;


class MyDataGrid extends DataGrid {

    private $relation = array();
    public $edutUrl = null;
    public $custom_build = false;

    public function __construct() {
        parent::__construct();
    }
    
    public function setRelation($rel_array) {
        $this->relation = $rel_array;

    }
    
    

    protected function getCellValue($column, $tablerow, $sanitize = true) {
        //blade

        if (strpos($column->name, '{{') !== false) {

            if (is_object($tablerow) && method_exists($tablerow, "getAttributes")) {
                $fields = $tablerow->getAttributes();
                $relations = $tablerow->getRelations();
                $array = array_merge($fields, $relations);

                $array['row'] = $tablerow;
            } else {
                $array = (array) $tablerow;
            }

            $value = $this->parser->compileString($column->name, $array);
            

            //eager loading smart syntax  relation.field
        }elseif (strpos($column->name,'()') && is_object($tablerow)) {
            $method = str_replace("()", "" , $column->name);
            $value = $tablerow->$method();
            
        } 
        elseif (preg_match('#^[a-z0-9_-]+(?:\.[a-z0-9_-]+)+$#i', $column->name, $matches) && is_object($tablerow)) {
            //switch to blade and god bless eloquent

            $expression = '{{$' . trim(str_replace('.', '->', $column->name)) . '}}';

            $fields = $tablerow->getAttributes();


            $relations = $tablerow->getRelations();


            $array = array_merge($fields, $relations);

            $value = $this->parser->compileString($expression, $array);

            //fieldname in a collection
        } elseif (is_object($tablerow)) {

            $value = @$tablerow->{$column->name};
            if ($sanitize) {
                $value = $this->sanitize($value);
            }
            //fieldname in an array
        } elseif (is_array($tablerow) && isset($tablerow[$column->name])) {

            $value = $tablerow[$column->name];

            //none found, cell will have the column name
        } else {
            $value = $column->name;
        }

        //decorators, should be moved in another method
        if ($column->link) {
            if (is_object($tablerow) && method_exists($tablerow, "getAttributes")) {
                $array = $tablerow->getAttributes();
                $array['row'] = $tablerow;
            } else {
                $array = (array) $tablerow;
            }
            $value = '<a href="' . $this->parser->compileString($column->link, $array) . '">' . $value . '</a>';
        }
        if (count($column->actions) > 0) {
            $key = ($column->key != '') ? $column->key : $this->key;
            $keyvalue = @$tablerow->{$key};
            $routeParamters = \Route::current()->parameters();
            $value = \View::make('admin.actions', array('uri' => $column->uri, 'id' => $keyvalue, 'actions' => $column->actions,
                        'current_entity' => $routeParamters['entity']));
        }


        return $value;
    }
    
    public function build_row($data) {

        $this->rows = array();
        foreach ($data as $tablerow) {
                         


                $row = new Row($tablerow);

                

                foreach ($this->columns as $column) {
      

                    $cell = new Cell($column->name);
                    $sanitize = (count($column->filters) || $column->cell_callable) ? false : true;
                    $value = $this->getCellValue($column, $tablerow, $sanitize);


                    $cell->value($value);
                    
                    $cell->parseFilters($column->filters);
                    
                    if ($column->cell_callable) {
                        $callable = $column->cell_callable;
                        $cell->value($callable($cell->value));
                    
                    }
                    $row->add($cell);
                    
                }

                if (count($this->row_callable)) {
                    foreach ($this->row_callable as $callable) {
                        $callable($row);
                    }
                }

                $this->rows[] = $row;
        }
    }

    public function build($view = '',$data = null) {
        
        //d("build");


 
           
        ($view == '') and $view = 'admin.datagrid';
        
        if(!is_null($data)) {
            $this->data = $data;
        }
        else {
            parent::build();
            Persistence::save();
        }



        $this->rows = array();

        if (empty($this->rows)) {




            foreach ($this->data as $tablerow) {

                
                foreach ($this->relation as $rel) {
                    $tablerow->$rel;
                }


                $row = new Row($tablerow);

                

                foreach ($this->columns as $column) {
      

                    $cell = new Cell($column->name);
                    $sanitize = (count($column->filters) || $column->cell_callable) ? false : true;
                    $value = $this->getCellValue($column, $tablerow, $sanitize);


                    $cell->value($value);
                    
                    $cell->parseFilters($column->filters);
                    
                    if ($column->cell_callable) {
                        $callable = $column->cell_callable;
                        $cell->value($callable($cell->value));
                    
                    }
                    $row->add($cell);
                    
                }

                if (count($this->row_callable)) {
                    foreach ($this->row_callable as $callable) {
                        $callable($row);
                    }
                }

                $this->rows[] = $row;
            }
        }

        //dd(count($this->rows));
        $routeParamters = \Route::current()->parameters();
        $param = Request::all();
        $data = array('dg' => $this, 'buttons' => $this->button_container, 'label' => $this->label,
            'current_entity' => $routeParamters['entity']);
        if (!empty($param) && isset($param['catalog'])) {
            $data['catalog'] = "?catalog=" . $param['catalog'];
        } else {
            $data['catalog'] = "";
        }



        return \View::make($view, $data);
    }
    


}
