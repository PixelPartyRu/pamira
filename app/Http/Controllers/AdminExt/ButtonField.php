<?php 

namespace App\Http\Controllers\AdminExt;
use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\DataForm\Field\Field;
use Illuminate\Support\Facades\View;

class ButtonField extends Field
{

    public $type = "button";
    public $description = "";
    public $clause = "where";
    public $button_text;
    

    public function __construct($name, $label, &$model = null, &$model_relations = null) {
        parent::__construct($name, "", $model, $model_relations);
        $this->button_text = $label;
    }
    public function getValue()
    {
        parent::getValue();

    }

    public function build()
    {        

        if (parent::build() === false) return;
        return $this->output = "<input class='btn btn-primary' type='button' value='".$this->button_text."' />";
        
    }

}
