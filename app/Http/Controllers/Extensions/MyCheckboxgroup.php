<?php  
namespace App\Http\Controllers\Extensions;
use Zofe\Rapyd\DataForm\Field\Checkboxgroup;

use Collective\Html\FormFacade as Form;

class MyCheckboxgroup extends Extensions
{
    public $checked_value = "checks";


    public function getValue()
    {
        parent::getValue();

        $this->values = explode($this->serialization_sep, $this->value);

        $description_arr = array();
        foreach ($this->options as $value => $description) {
            if (in_array($value, $this->values)) {
                $description_arr[] = $description;
            }
        }
        $this->description = implode($this->separator, $description_arr);
    }

    public function build()
    {
        $output = "";

        if (!isset($this->style)) {
            $this->style = "margin:0 2px 0 0; vertical-align: middle";
        }
        unset($this->attributes['id']);
        if (parent::build() === false) return;

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

                //dd($this->options, $this->values);
                foreach ($this->options as $val => $label) {

                    $this->checked = in_array($val, $this->values);

                    //echo ((int)$this->checked)."<br />";
                    $output .= sprintf($this->format, Form::checkbox($this->name.'[]', $val, $this->checked) . $label) . $this->separator;
                }
                $output .= $this->extra_output;

                break;

            case "hidden":
                $output = Form::hidden($this->name, $this->value);
                break;

            default:
        }
        $this->output = $output;
    }

}
