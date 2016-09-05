<?php 

namespace App\Http\Controllers\Extensions;
use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\DataForm\Field\Field;
use Illuminate\Support\Facades\View;
use App\Http\Requests\Request;

class CatalogField extends Field
{

    public $type = "catalog_field";
    public $description = "";
    public $clause = "where";
    public $level;
    public $first_level = array();
    public $second_level = array();
    public $third_level = array();


    public function getValue()
    {
        parent::getValue();
        foreach ($this->options as $value => $description) {
            if ($this->value == $value) {
                $this->description = $description;
            }
        }
    }

    public function build()
    {
        

        $output = "";

        unset($this->attributes['type'], $this->attributes['size']);
        if (parent::build() === false)
            return;

        $this->first_level['options'] = \App\Catalog::where("parent_id",0)->lists("name", "id")->all();
        
        $this->second_level['options'] = array();
        $this->second_level['value'] = null; 
        $this->second_level['attr'] = array("class" => "hidden second_level_catalog");
        
        
        
        $this->third_level['options'] = array();
        $this->third_level['value'] = null;
        $this->third_level['attr'] = array("class" => "hidden third_level_catalog");
        
        if($this->status == "create") {
            
        }
        

        


        switch ($this->status) {
            case "disabled":
            case "show":
                if (!isset($this->value)) {
                    $output = $this->layout['null_label'];
                } else {
                    $output = $this->description;
                }
                $output = "<div class='help-block'>".$output."&nbsp;</div>";
                break;

            case "create":
            case "modify":
                $output = view("admin.catalog_field",array("data" => $this)); //Form::select($this->name, $this->options, $this->value, $this->attributes) . $this->extra_output;
                break;

            case "hidden":
                $output = Form::hidden($this->name, $this->value);
                break;

            default:
        }
        
        if ($this->status == "modify") {
//            $r = \Illuminate\Support\Facades\Request::all();
//
//            $product = \App\Product::find(intval($r['modify']));
//            $model = \App\Catalog::find($product->catalog_id);
//            $this->level = $model->level;
//            var_dump($this->value);
//            var_dump($this->level);
//            if ($this->level == 1) {
//                $this->first_level['value'] = $this->value;
//            }
//            if ($this->level >= 2) {
//                $this->second_level['options'] = \App\Catalog::where("level", 2)->lists("name", "id")->all();
//                $this->second_level['attr'] = array("class" => "form-control second_level_catalog");
//                
//            }
//            if ($this->level == 2) {
//                $this->second_level['value'] = $this->value;
//                $this->first_level['value'] = $model->parent_catalog()->id;
//            }
//            if ($this->level == 3) {
//                $this->third_level['options'] = \App\Catalog::where("level", 3)->lists("name", "id")->all();
//                $this->third_level['value'] = $this->value;
//                $this->second_level['value'] = $model->parent_catalog()->id;
//                $this->first_level['value'] = $model->parent_catalog()->parent_catalog()->id;
//                $this->third_level['attr'] = array("class" => "form-control third_level_catalog");
//            }
        }
        // var_dump( $this->options );
        $this->output = $output;
    }

}