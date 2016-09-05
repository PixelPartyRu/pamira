<?php 

namespace App\Http\Controllers\Extensions;
use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\DataForm\Field\Field;
use Illuminate\Support\Facades\View;
use App\Http\Requests\Request;

class LabelField extends Field
{

    public $type = "label";
    public $description = "";
    public $clause = "where";



    public function getValue()
    {
        parent::getValue();

    }

    public function build()
    {
        

        $output = "";

        return "1111111";
    }

}