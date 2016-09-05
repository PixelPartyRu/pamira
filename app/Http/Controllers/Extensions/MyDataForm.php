<?php

namespace App\Http\Controllers\Extensions;

use Zofe\Rapyd\DataForm\DataForm;
use Illuminate\Support\Facades\View;

class MyDataForm extends DataForm{

    public function AddText($text) {
        $text_ob = new \stdClass();
        $text_ob->type = "label";
        $text_ob->text = $text;
        $text_ob->status = "";
        $text_ob->orientation = "";
        $text_ob->messages = "";
        $text_ob->has_error = "";
        
        $this->fields[] = $text_ob;
    }
    protected function buildForm() {
        $this->prepareForm();
        $df = $this;


        return View::make("admin.dataform", compact('df'));
    }

    protected function buildFields() {
        $messages = (isset($this->validator)) ? $this->validator->messages() : false;

        foreach ($this->fields as $field) {
            $field->status = $this->status;
            $field->orientation = $this->orientation;
            if ($messages and $messages->has($field->name)) {
                $field->messages = $messages->get($field->name);
                $field->has_error = " has-error";
            }
            if($field->type !== "label")
            $field->build();
        }
    }

}


