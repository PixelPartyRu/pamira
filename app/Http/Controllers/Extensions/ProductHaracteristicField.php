<?php 

namespace App\Http\Controllers\Extensions;
use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\DataForm\Field\Field;
use Illuminate\Support\Facades\View;

class ProductHaracteristicField extends Field
{

    public $type = "product_haracteristic_field";
    public $description = "";
    public $clause = "where";

    public function getValue()
    {
        parent::getValue();
        foreach ($this->options as $value => $description) {
            if ($this->value == $value) {
                $this->description = $description;
            }
        }
    }
    
    public function setValue($val) {
        $this->value = $val;
    }

    public function build()
    {
        //dd($this);
        $output = "";

        unset($this->attributes['type'], $this->attributes['size']);
        if (parent::build() === false)
            return;

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
                $output = view("admin.product_haracteristic_field",array("data" => $this)); //Form::select($this->name, $this->options, $this->value, $this->attributes) . $this->extra_output;
                break;

            case "hidden":
                $output = Form::hidden($this->name, $this->value);
                break;

            default:
        }
        $this->output = $output;
    }

}
